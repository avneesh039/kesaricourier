// backend/src/controllers/tariffController.ts
import { Request, Response, NextFunction } from 'express';
import { z } from 'zod';
import prisma from '../config/database';
import { calculateWeight } from '../utils/volumetric';

export async function getTariffs(req: Request, res: Response, next: NextFunction) {
  try {
    const tariffs = await prisma.tariff.findMany({
      where: { tenant_id: req.tenantId! },
      include: {
        zone: true,
        client: { select: { id: true, name: true, client_code: true } },
        slabs: { orderBy: { weight_from: 'asc' } },
      },
      orderBy: { created_at: 'desc' },
    });
    res.json({ success: true, data: tariffs });
  } catch (error) {
    next(error);
  }
}

export async function calculateRate(req: Request, res: Response, next: NextFunction) {
  try {
    const calcSchema = z.object({
      sendBy: z.enum(['Air', 'Surface']),
      mode: z.enum(['Dox', 'Non-Dox']),
      destinationId: z.number(),
      deadWeight: z.number().min(0.01),
      lengthCm: z.number().optional(),
      widthCm: z.number().optional(),
      heightCm: z.number().optional(),
      clientId: z.number().optional(),
    });

    const body = calcSchema.parse(req.body);

    // 1. Compute billable weight comparing dead vs volumetric
    const weightCalc = calculateWeight(
      body.deadWeight,
      body.sendBy,
      body.lengthCm,
      body.widthCm,
      body.heightCm
    );

    const billableWeight = weightCalc.billableWeight;

    // 2. Fetch destination to find its operational zone
    const destination = await prisma.destination.findUnique({
      where: { id: body.destinationId },
      include: {
        state: {
          include: { zone: true },
        },
      },
    });

    if (!destination || !destination.state) {
      return res.status(404).json({ success: false, error: { message: 'Destination not found.' } });
    }

    const zoneId = destination.state.zone_id;

    // 3. Look for client-specific tariff first, then fallback to agency general tariff
    let tariff = null;
    if (body.clientId) {
      tariff = await prisma.tariff.findFirst({
        where: {
          tenant_id: req.tenantId!,
          client_id: body.clientId,
          send_by: body.sendBy,
          mode: body.mode,
          zone_id: zoneId,
        },
        include: { slabs: { orderBy: { weight_from: 'asc' } } },
      });
    }

    if (!tariff) {
      tariff = await prisma.tariff.findFirst({
        where: {
          tenant_id: req.tenantId!,
          client_id: null,
          send_by: body.sendBy,
          mode: body.mode,
          zone_id: zoneId,
        },
        include: { slabs: { orderBy: { weight_from: 'asc' } } },
      });
    }

    let subtotal = 0;
    let appliedSlab = null;

    if (tariff && tariff.slabs.length > 0) {
      // Find matching slab
      const match = tariff.slabs.find(
        (s) => billableWeight >= Number(s.weight_from) && billableWeight <= Number(s.weight_to)
      );

      if (match) {
        subtotal = Number(match.rate);
        appliedSlab = `${match.weight_from}kg - ${match.weight_to}kg @ ₹${match.rate}`;
      } else {
        // Exceeds highest slab: take highest slab rate + incremental steps
        const highestSlab = tariff.slabs[tariff.slabs.length - 1];
        const baseRate = Number(highestSlab.rate);
        const excessWeight = billableWeight - Number(highestSlab.weight_to);

        const step = Number(tariff.additional_weight_step) || 0.5;
        const stepRate = Number(tariff.additional_weight_rate) || 30.0;
        const stepsCount = Math.ceil(excessWeight / step);
        const additionalCharge = stepsCount * stepRate;

        subtotal = baseRate + additionalCharge;
        appliedSlab = `Base ₹${baseRate} + ${stepsCount}x ₹${stepRate} incremental`;
      }
    } else {
      // Standard fallback default rate when no specific matrix is configured
      const baseRate = body.sendBy === 'Air' ? 50 : 35;
      const stepRate = body.sendBy === 'Air' ? 40 : 25;
      const additionalKg = Math.max(0, Math.ceil(billableWeight - 0.5));
      subtotal = baseRate + additionalKg * stepRate;
      appliedSlab = `Standard default rate ₹${subtotal}`;
    }

    // 4. Client fuel surcharge %
    let fuelPercent = 0;
    if (body.clientId) {
      const client = await prisma.client.findUnique({
        where: { id: body.clientId },
        select: { fuel_surcharge_percent: true },
      });
      fuelPercent = Number(client?.fuel_surcharge_percent || 0);
    }

    const fuelAmount = Math.round(subtotal * (fuelPercent / 100) * 100) / 100;
    const docketCharge = 0;
    const totalAmount = Math.round((subtotal + fuelAmount + docketCharge) * 100) / 100;

    res.json({
      success: true,
      data: {
        destination: {
          id: destination.id,
          code: destination.code,
          name: destination.name,
          state: destination.state.name,
          zone: destination.state.zone.name,
        },
        weights: weightCalc,
        rateCalculation: {
          subtotal,
          appliedSlab,
          fuelPercent,
          fuelAmount,
          docketCharge,
          totalAmount,
        },
      },
    });
  } catch (error) {
    next(error);
  }
}

// backend/src/controllers/publicController.ts
import { Request, Response, NextFunction } from 'express';
import { z } from 'zod';
import prisma from '../config/database';
import { calculateWeight } from '../utils/volumetric';

export async function trackShipment(req: Request, res: Response, next: NextFunction) {
  try {
    const awb = String(req.params.awb);

    const consignment = await prisma.consignment.findUnique({
      where: { consignment_no: awb.trim() },
      include: {
        destination: { include: { state: true } },
        branch: { select: { branch_name: true, city: true, phone: true } },
        tenant: { select: { name: true, trade_name: true, phone: true } },
        runsheet_items: {
          include: {
            runsheet: {
              include: { delivery_person: { select: { name: true } } },
            },
          },
        },
      },
    });

    if (!consignment) {
      return res.status(404).json({
        success: false,
        error: { code: 'AWB_NOT_FOUND', message: `No consignment found for tracking number ${awb}.` },
      });
    }

    // Determine current progression milestone index (0 to 4)
    let currentStep = 0; // Booked
    if (consignment.status === 'in_manifest') currentStep = 1;
    if (consignment.status === 'in_transit') currentStep = 2;
    if (consignment.status === 'out_for_delivery') currentStep = 3;
    if (consignment.status === 'delivered') currentStep = 4;

    const latestRunsheetItem = consignment.runsheet_items[consignment.runsheet_items.length - 1];

    const milestones = [
      {
        step: 0,
        title: 'Booking Confirmed',
        description: `Shipment booked at ${consignment.branch.branch_name}, ${consignment.branch.city}`,
        timestamp: consignment.booking_date,
        completed: true,
      },
      {
        step: 1,
        title: 'Manifested & Bagged',
        description: `Consignment consolidated into line-haul dispatch manifest`,
        timestamp: currentStep >= 1 ? consignment.created_at : null,
        completed: currentStep >= 1,
      },
      {
        step: 2,
        title: 'In Transit',
        description: `En route to destination hub: ${consignment.destination.name} (${consignment.destination.code})`,
        timestamp: currentStep >= 2 ? consignment.created_at : null,
        completed: currentStep >= 2,
      },
      {
        step: 3,
        title: 'Out for Delivery',
        description:
          currentStep >= 3
            ? `Assigned to delivery executive${
                latestRunsheetItem?.runsheet?.delivery_person?.name
                  ? ` (${latestRunsheetItem.runsheet.delivery_person.name})`
                  : ''
              }`
            : 'Pending arrival at final delivery branch',
        timestamp: currentStep >= 3 ? latestRunsheetItem?.runsheet?.delivery_date : null,
        completed: currentStep >= 3,
      },
      {
        step: 4,
        title: 'Delivered',
        description:
          consignment.status === 'delivered'
            ? `Successfully delivered to ${latestRunsheetItem?.recipient_name || consignment.recipient_name} (${latestRunsheetItem?.recipient_relation || 'Consignee'})`
            : latestRunsheetItem?.status === 'attempted'
            ? `Delivery attempted: ${latestRunsheetItem.failure_reason || 'Consignee unavailable'}`
            : 'Scheduled for doorstep delivery',
        timestamp: latestRunsheetItem?.delivered_at || null,
        completed: consignment.status === 'delivered',
      },
    ];

    res.json({
      success: true,
      data: {
        consignmentNo: consignment.consignment_no,
        status: consignment.status,
        service: `${consignment.send_by} ${consignment.mode}`,
        origin: `${consignment.branch.city}`,
        destination: `${consignment.destination.name} (${consignment.destination.pincode || ''})`,
        recipientName: consignment.recipient_name,
        pieces: consignment.pieces,
        billableWeight: Number(consignment.billable_weight),
        currentStep,
        milestones,
      },
    });
  } catch (error) {
    next(error);
  }
}

export async function estimateTariff(req: Request, res: Response, next: NextFunction) {
  try {
    const schema = z.object({
      destinationId: z.number(),
      deadWeight: z.number().min(0.01),
      sendBy: z.enum(['Air', 'Surface']).default('Air'),
      lengthCm: z.number().optional(),
      widthCm: z.number().optional(),
      heightCm: z.number().optional(),
    });

    const body = schema.parse(req.body);

    const weights = calculateWeight(
      body.deadWeight,
      body.sendBy,
      body.lengthCm,
      body.widthCm,
      body.heightCm
    );

    const dest = await prisma.destination.findUnique({
      where: { id: body.destinationId },
      include: { state: { include: { zone: true } } },
    });

    if (!dest) {
      return res.status(404).json({ success: false, error: { message: 'Destination not found.' } });
    }

    const baseRate = body.sendBy === 'Air' ? 60 : 40;
    const stepRate = body.sendBy === 'Air' ? 40 : 25;
    const additionalKg = Math.max(0, Math.ceil(weights.billableWeight - 0.5));
    const estimatedSubtotal = baseRate + additionalKg * stepRate;
    const gstAmount = Math.round(estimatedSubtotal * 0.18 * 100) / 100;
    const estimatedTotal = Math.round(estimatedSubtotal + gstAmount);

    res.json({
      success: true,
      data: {
        destination: dest.name,
        state: dest.state.name,
        weights,
        estimatedSubtotal,
        gstAmount,
        estimatedTotal,
      },
    });
  } catch (error) {
    next(error);
  }
}

export async function getPublicDestinations(req: Request, res: Response, next: NextFunction) {
  try {
    const { search } = req.query;
    const where: any = { is_active: true };

    if (search && typeof search === 'string') {
      where.OR = [
        { name: { contains: search } },
        { code: { contains: search } },
        { pincode: { contains: search } },
      ];
    }

    const destinations = await prisma.destination.findMany({
      where,
      take: 20,
      include: { state: true },
      orderBy: { name: 'asc' },
    });

    res.json({ success: true, data: destinations });
  } catch (error) {
    next(error);
  }
}

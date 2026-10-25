// backend/src/controllers/operationController.ts
import { Request, Response, NextFunction } from 'express';
import { z } from 'zod';
import prisma from '../config/database';
import { generateBarcodeSvg } from '../utils/barcode';

export async function getSingleThermalLabel(req: Request, res: Response, next: NextFunction) {
  try {
    const awb = String(req.params.awb);
    const booking = await prisma.consignment.findFirst({
      where: { consignment_no: awb, tenant_id: req.tenantId! },
      include: {
        destination: { include: { state: true } },
        client: true,
        branch: true,
        tenant: true,
      },
    });

    if (!booking) {
      return res.status(404).json({ success: false, error: { message: 'Consignment not found.' } });
    }

    const barcodeSvg = generateBarcodeSvg(booking.consignment_no, 55, 1.8, true);
    const trackingUrl = `${process.env.FRONTEND_URL || 'http://localhost:5173'}/track/${booking.consignment_no}`;

    res.json({
      success: true,
      data: {
        booking,
        barcodeSvg,
        trackingUrl,
      },
    });
  } catch (error) {
    next(error);
  }
}

export async function getBatchThermalLabels(req: Request, res: Response, next: NextFunction) {
  try {
    const { awbs } = req.body;
    if (!Array.isArray(awbs) || awbs.length === 0) {
      return res.status(400).json({ success: false, error: { message: 'Array of AWBs required.' } });
    }

    const bookings = await prisma.consignment.findMany({
      where: {
        consignment_no: { in: awbs },
        tenant_id: req.tenantId!,
      },
      include: {
        destination: { include: { state: true } },
        client: true,
        branch: true,
        tenant: true,
      },
    });

    const labels = bookings.map((b) => ({
      booking: b,
      barcodeSvg: generateBarcodeSvg(b.consignment_no, 55, 1.8, true),
      trackingUrl: `${process.env.FRONTEND_URL || 'http://localhost:5173'}/track/${b.consignment_no}`,
    }));

    res.json({ success: true, data: labels });
  } catch (error) {
    next(error);
  }
}

export async function createManifest(req: Request, res: Response, next: NextFunction) {
  try {
    const schema = z.object({
      destinationHubId: z.number(),
      driverName: z.string().optional(),
      vehicleNo: z.string().optional(),
      consignmentIds: z.array(z.number()).min(1),
      branchId: z.number().optional(),
    });

    const body = schema.parse(req.body);
    const branchId = body.branchId || req.branchId || 1;

    // Generate manifest number
    const manifestNo = `MAN-${Date.now().toString().slice(-8)}`;

    // Calculate total weight
    const consignments = await prisma.consignment.findMany({
      where: { id: { in: body.consignmentIds }, tenant_id: req.tenantId! },
    });

    const totalWeight = consignments.reduce((sum, c) => sum + Number(c.billable_weight), 0);

    const manifest = await prisma.manifest.create({
      data: {
        tenant_id: req.tenantId!,
        branch_id: branchId,
        manifest_no: manifestNo,
        destination_hub_id: body.destinationHubId,
        driver_name: body.driverName || null,
        vehicle_no: body.vehicleNo || null,
        total_consignments: consignments.length,
        total_weight: totalWeight,
        status: 'dispatched',
        items: {
          create: body.consignmentIds.map((cid) => ({
            consignment_id: cid,
          })),
        },
      },
      include: {
        destination_hub: true,
        items: {
          include: { consignment: true },
        },
      },
    });

    // Update consignments to in_transit
    await prisma.consignment.updateMany({
      where: { id: { in: body.consignmentIds } },
      data: { status: 'in_transit' },
    });

    res.status(201).json({
      success: true,
      message: `Manifest ${manifestNo} created with ${consignments.length} consignments.`,
      data: manifest,
    });
  } catch (error) {
    next(error);
  }
}

export async function getManifests(req: Request, res: Response, next: NextFunction) {
  try {
    const manifests = await prisma.manifest.findMany({
      where: { tenant_id: req.tenantId! },
      orderBy: { created_at: 'desc' },
      include: {
        destination_hub: true,
        branch: { select: { branch_name: true, branch_code: true } },
      },
    });
    res.json({ success: true, data: manifests });
  } catch (error) {
    next(error);
  }
}

export async function createRunsheet(req: Request, res: Response, next: NextFunction) {
  try {
    const schema = z.object({
      deliveryPersonId: z.number(),
      deliveryDate: z.string().optional(),
      consignmentIds: z.array(z.number()).min(1),
      branchId: z.number().optional(),
    });

    const body = schema.parse(req.body);
    const branchId = body.branchId || req.branchId || 1;
    const runsheetNo = `DRS-${Date.now().toString().slice(-8)}`;

    const runsheet = await prisma.runsheet.create({
      data: {
        tenant_id: req.tenantId!,
        branch_id: branchId,
        runsheet_no: runsheetNo,
        delivery_person_id: body.deliveryPersonId,
        delivery_date: body.deliveryDate ? new Date(body.deliveryDate) : new Date(),
        status: 'assigned',
        items: {
          create: body.consignmentIds.map((cid) => ({
            consignment_id: cid,
            status: 'pending',
          })),
        },
      },
      include: {
        delivery_person: { select: { id: true, name: true, phone: true } },
        items: { include: { consignment: true } },
      },
    });

    // Update consignments status to out_for_delivery
    await prisma.consignment.updateMany({
      where: { id: { in: body.consignmentIds } },
      data: { status: 'out_for_delivery' },
    });

    res.status(201).json({
      success: true,
      message: `Delivery Runsheet ${runsheetNo} generated.`,
      data: runsheet,
    });
  } catch (error) {
    next(error);
  }
}

export async function getRunsheets(req: Request, res: Response, next: NextFunction) {
  try {
    const runsheets = await prisma.runsheet.findMany({
      where: { tenant_id: req.tenantId! },
      orderBy: { created_at: 'desc' },
      include: {
        delivery_person: { select: { id: true, name: true, phone: true } },
        branch: { select: { branch_name: true, branch_code: true } },
        _count: { select: { items: true } },
      },
    });
    res.json({ success: true, data: runsheets });
  } catch (error) {
    next(error);
  }
}

export async function inscanConsignments(req: Request, res: Response, next: NextFunction) {
  try {
    const schema = z.object({
      courierPartner: z.string().default('Self / Local'),
      receivedFromPin: z.string(),
      items: z.array(
        z.object({
          docketNo: z.string().min(1),
          pieces: z.number().default(1),
          weight: z.number().default(0.5),
          destinationPin: z.string().default('400001'),
        })
      ).min(1),
    });

    const body = schema.parse(req.body);
    const tenantId = req.tenantId!;

    for (const item of body.items) {
      const existing = await prisma.consignment.findFirst({
        where: { consignment_no: item.docketNo, tenant_id: tenantId },
      });

      if (existing) {
        await prisma.consignment.update({
          where: { id: existing.id },
          data: { status: 'in_transit' },
        });
      }
    }

    res.json({
      success: true,
      message: `Inscan completed for ${body.items.length} dockets.`,
      data: { count: body.items.length },
    });
  } catch (error) {
    next(error);
  }
}

export async function recordSinglePod(req: Request, res: Response, next: NextFunction) {
  try {
    const schema = z.object({
      docketNo: z.string().min(1),
      deliveryDateTime: z.string().optional(),
      receiverName: z.string().min(1),
      relation: z.string().default('Self'),
      status: z.enum(['delivered', 'ndr']),
      ndrReason: z.string().optional(),
      remarks: z.string().optional(),
    });

    const body = schema.parse(req.body);
    const tenantId = req.tenantId!;

    const consignment = await prisma.consignment.findFirst({
      where: { consignment_no: body.docketNo, tenant_id: tenantId },
    });

    if (!consignment) {
      return res.status(404).json({ success: false, error: { message: `Docket ${body.docketNo} not found.` } });
    }

    const newStatus = body.status === 'delivered' ? 'delivered' : 'attempted';

    await prisma.consignment.update({
      where: { id: consignment.id },
      data: { status: newStatus },
    });

    res.json({
      success: true,
      message: `POD recorded successfully for Docket #${body.docketNo}`,
      data: { consignmentNo: body.docketNo, status: newStatus },
    });
  } catch (error) {
    next(error);
  }
}

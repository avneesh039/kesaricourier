// backend/src/controllers/bookingController.ts
import { Request, Response, NextFunction } from 'express';
import { z } from 'zod';
import prisma from '../config/database';
import { generateAwbNumber } from '../utils/awbGenerator';
import { calculateWeight } from '../utils/volumetric';
import { generateBarcodeSvg } from '../utils/barcode';

const bookingSchema = z.object({
  consignmentNo: z.string().optional(),
  clientId: z.number(),
  senderName: z.string().min(2),
  senderPhone: z.string().min(10),
  recipientName: z.string().min(2),
  recipientPhone: z.string().min(10),
  recipientAddress: z.string().min(5),
  destinationId: z.number().default(1),
  destinationPincode: z.string().optional(),
  fromPincode: z.string().optional(),
  productDescription: z.string().optional(),
  riskType: z.string().optional().default('Owner Risk'),
  bookingDate: z.string().optional(),
  mode: z.enum(['Dox', 'Non-Dox']),
  sendBy: z.string().default('Air'),
  pieces: z.number().int().min(1).default(1),
  deadWeight: z.number().min(0.01),
  lengthCm: z.number().optional(),
  widthCm: z.number().optional(),
  heightCm: z.number().optional(),
  subtotal: z.number().min(0),
  fuelSurchargeAmount: z.number().default(0),
  docketCharge: z.number().default(0),
  otherCharges: z.number().default(0),
  discountAmount: z.number().default(0),
  insuredValue: z.number().default(0),
  totalAmount: z.number().min(0),
  branchId: z.number().optional(),
});

export async function createBooking(req: Request, res: Response, next: NextFunction) {
  try {
    const tenantId = req.tenantId!;
    const body = bookingSchema.parse(req.body);

    // Determine branch
    let branchId = body.branchId || req.branchId;
    if (!branchId) {
      const defaultBranch = await prisma.branch.findFirst({
        where: { tenant_id: tenantId, status: 'active' },
      });
      branchId = defaultBranch?.id || 1;
    }

    // Determine or generate 14-digit AWB
    let awb = body.consignmentNo?.trim();
    if (!awb) {
      const tenant = await prisma.tenant.findUnique({
        where: { id: tenantId },
        select: { agency_code: true },
      });
      const branch = await prisma.branch.findUnique({
        where: { id: branchId },
        select: { branch_code: true },
      });
      awb = await generateAwbNumber(tenant?.agency_code || '26', branch?.branch_code || '01');
    }

    // Volumetric weight check
    const weightCalc = calculateWeight(
      body.deadWeight,
      body.sendBy,
      body.lengthCm,
      body.widthCm,
      body.heightCm
    );

    let destId = body.destinationId;
    if (body.destinationPincode) {
      const match = await prisma.destination.findFirst({
        where: { pincode: body.destinationPincode },
      });
      if (match) destId = match.id;
    }

    const booking = await prisma.consignment.create({
      data: {
        tenant_id: tenantId,
        branch_id: branchId,
        booking_date: body.bookingDate ? new Date(body.bookingDate) : new Date(),
        consignment_no: awb,
        client_id: body.clientId,
        sender_name: body.senderName,
        sender_phone: body.senderPhone,
        recipient_name: body.recipientName,
        recipient_phone: body.recipientPhone,
        recipient_address: body.recipientAddress,
        destination_id: destId,
        mode: body.mode,
        send_by: body.sendBy,
        pieces: body.pieces,
        dead_weight: body.deadWeight,
        length_cm: body.lengthCm,
        width_cm: body.widthCm,
        height_cm: body.heightCm,
        volumetric_weight: weightCalc.volumetricWeight,
        billable_weight: weightCalc.billableWeight,
        subtotal: body.subtotal,
        fuel_surcharge_amount: body.fuelSurchargeAmount,
        docket_charge: body.docketCharge,
        other_charges: body.otherCharges,
        discount_amount: body.discountAmount,
        insured_value: body.insuredValue,
        total_amount: body.totalAmount,
        status: 'booked',
        created_by_user_id: req.user?.userId || null,
        operator_name: req.user?.name || null,
      },
      include: {
        destination: {
          include: { state: true },
        },
        client: true,
        branch: true,
      },
    });

    res.status(201).json({
      success: true,
      message: `Consignment ${awb} booked successfully.`,
      data: booking,
    });
  } catch (error) {
    next(error);
  }
}

export async function getBookings(req: Request, res: Response, next: NextFunction) {
  try {
    const tenantId = req.tenantId!;
    const {
      search,
      status,
      clientId,
      branchId,
      startDate,
      endDate,
      page = '1',
      limit = '25',
    } = req.query;

    const pageNum = parseInt(page as string, 10);
    const limitNum = parseInt(limit as string, 10);
    const skip = (pageNum - 1) * limitNum;

    const where: any = { tenant_id: tenantId };

    if (search && typeof search === 'string') {
      where.OR = [
        { consignment_no: { contains: search } },
        { recipient_name: { contains: search } },
        { recipient_phone: { contains: search } },
      ];
    }

    if (status && typeof status === 'string' && status !== 'all') {
      where.status = status;
    }

    if (clientId) {
      where.client_id = parseInt(clientId as string, 10);
    }

    if (branchId) {
      where.branch_id = parseInt(branchId as string, 10);
    } else if (req.branchId && req.user?.role !== 'agency_admin' && req.user?.role !== 'superadmin' && req.user?.role !== 'customer') {
      where.branch_id = req.branchId;
    }

    if (startDate && endDate) {
      where.booking_date = {
        gte: new Date(startDate as string),
        lte: new Date(endDate as string),
      };
    }

    const [total, bookings] = await Promise.all([
      prisma.consignment.count({ where }),
      prisma.consignment.findMany({
        where,
        skip,
        take: limitNum,
        orderBy: { created_at: 'desc' },
        include: {
          destination: {
            include: { state: true },
          },
          client: {
            select: { id: true, name: true, client_code: true },
          },
          branch: {
            select: { id: true, branch_code: true, branch_name: true },
          },
        },
      }),
    ]);

    res.json({
      success: true,
      data: bookings,
      meta: {
        page: pageNum,
        limit: limitNum,
        total,
        totalPages: Math.ceil(total / limitNum),
      },
    });
  } catch (error) {
    next(error);
  }
}

export async function getBookingByAwb(req: Request, res: Response, next: NextFunction) {
  try {
    const awb = String(req.params.awb);
    const booking = await prisma.consignment.findFirst({
      where: {
        consignment_no: awb,
        tenant_id: req.tenantId!,
      },
      include: {
        destination: {
          include: { state: { include: { zone: true } } },
        },
        client: true,
        branch: true,
        manifest_items: {
          include: { manifest: true },
        },
        runsheet_items: {
          include: {
            runsheet: {
              include: { delivery_person: { select: { id: true, name: true, phone: true } } },
            },
          },
        },
      },
    });

    if (!booking) {
      return res.status(404).json({ success: false, error: { message: 'Consignment not found.' } });
    }

    const barcodeSvg = generateBarcodeSvg(booking.consignment_no, 60, 2.0, true);

    res.json({
      success: true,
      data: {
        ...booking,
        barcodeSvg,
      },
    });
  } catch (error) {
    next(error);
  }
}

export async function updateBookingStatus(req: Request, res: Response, next: NextFunction) {
  try {
    const awb = String(req.params.awb);
    const { status } = req.body;

    const booking = await prisma.consignment.findFirst({
      where: { consignment_no: awb, tenant_id: req.tenantId! },
    });

    if (!booking) {
      return res.status(404).json({ success: false, error: { message: 'Consignment not found.' } });
    }

    const updated = await prisma.consignment.update({
      where: { id: booking.id },
      data: { status },
    });

    res.json({
      success: true,
      message: `Status updated to ${status}.`,
      data: updated,
    });
  } catch (error) {
    next(error);
  }
}

// backend/src/controllers/clientController.ts
import { Request, Response, NextFunction } from 'express';
import { z } from 'zod';
import prisma from '../config/database';

export async function getClients(req: Request, res: Response, next: NextFunction) {
  try {
    const { search } = req.query;
    const where: any = { tenant_id: req.tenantId! };

    if (search && typeof search === 'string') {
      where.OR = [
        { client_code: { contains: search } },
        { name: { contains: search } },
        { phone: { contains: search } },
        { gstin: { contains: search } },
      ];
    }

    const clients = await prisma.client.findMany({
      where,
      orderBy: { name: 'asc' },
      include: {
        _count: {
          select: {
            consignments: true,
            invoices: true,
          },
        },
      },
    });

    res.json({ success: true, data: clients });
  } catch (error) {
    next(error);
  }
}

export async function createClient(req: Request, res: Response, next: NextFunction) {
  try {
    const clientSchema = z.object({
      clientCode: z.string().min(1),
      name: z.string().min(2),
      contactPerson: z.string().optional(),
      phone: z.string().optional(),
      email: z.string().email().optional().or(z.literal('')),
      address: z.string().optional(),
      billingAddress: z.string().optional(),
      city: z.string().optional(),
      state: z.string().optional(),
      pincode: z.string().optional(),
      gstin: z.string().optional(),
      panNo: z.string().optional(),
      fuelSurchargePercent: z.number().default(10.0),
    });

    const body = clientSchema.parse(req.body);

    const client = await prisma.client.create({
      data: {
        tenant_id: req.tenantId!,
        client_code: body.clientCode,
        name: body.name,
        contact_person: body.contactPerson,
        phone: body.phone,
        email: body.email || null,
        address: body.address,
        billing_address: body.billingAddress,
        city: body.city,
        state: body.state,
        pincode: body.pincode,
        gstin: body.gstin,
        pan_no: body.panNo,
        fuel_surcharge_percent: body.fuelSurchargePercent,
        status: 'active',
      },
    });

    res.status(201).json({ success: true, data: client });
  } catch (error) {
    next(error);
  }
}

export async function getClientDetail(req: Request, res: Response, next: NextFunction) {
  try {
    const id = parseInt(String(req.params.id), 10);
    const client = await prisma.client.findFirst({
      where: { id, tenant_id: req.tenantId! },
      include: {
        consignments: {
          take: 10,
          orderBy: { booking_date: 'desc' },
          include: { destination: true },
        },
        invoices: {
          take: 5,
          orderBy: { invoice_date: 'desc' },
        },
      },
    });

    if (!client) {
      return res.status(404).json({ success: false, error: { message: 'Client not found.' } });
    }

    res.json({ success: true, data: client });
  } catch (error) {
    next(error);
  }
}

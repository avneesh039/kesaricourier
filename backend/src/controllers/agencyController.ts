// backend/src/controllers/agencyController.ts
import { Request, Response, NextFunction } from 'express';
import bcrypt from 'bcryptjs';
import { z } from 'zod';
import prisma from '../config/database';

export async function getAgencyDashboard(req: Request, res: Response, next: NextFunction) {
  try {
    const tenantId = req.tenantId!;

    const todayStart = new Date();
    todayStart.setHours(0, 0, 0, 0);

    const [
      totalBookings,
      todayBookings,
      deliveredCount,
      inTransitCount,
      outForDeliveryCount,
      branchesCount,
      clientsCount,
      unbilledCount,
      agency,
    ] = await Promise.all([
      prisma.consignment.count({ where: { tenant_id: tenantId } }),
      prisma.consignment.count({
        where: { tenant_id: tenantId, booking_date: { gte: todayStart } },
      }),
      prisma.consignment.count({ where: { tenant_id: tenantId, status: 'delivered' } }),
      prisma.consignment.count({ where: { tenant_id: tenantId, status: 'in_transit' } }),
      prisma.consignment.count({ where: { tenant_id: tenantId, status: 'out_for_delivery' } }),
      prisma.branch.count({ where: { tenant_id: tenantId, status: 'active' } }),
      prisma.client.count({ where: { tenant_id: tenantId, status: 'active' } }),
      prisma.consignment.count({ where: { tenant_id: tenantId, is_billed: false } }),
      prisma.tenant.findUnique({
        where: { id: tenantId },
        select: {
          name: true,
          trade_name: true,
          agency_code: true,
          status: true,
          validity_end: true,
          grace_period_days: true,
          plan_type: true,
          brand_color: true,
        },
      }),
    ]);

    // Financial totals
    const bookings = await prisma.consignment.findMany({
      where: { tenant_id: tenantId },
      select: { total_amount: true },
    });
    const totalFreightRevenue = bookings.reduce((sum, b) => sum + Number(b.total_amount), 0);

    // Recent 5 consignments
    const recentBookings = await prisma.consignment.findMany({
      where: { tenant_id: tenantId },
      take: 5,
      orderBy: { created_at: 'desc' },
      include: {
        destination: true,
        client: true,
      },
    });

    res.json({
      success: true,
      data: {
        agency,
        kpis: {
          totalBookings,
          todayBookings,
          deliveredCount,
          inTransitCount,
          outForDeliveryCount,
          branchesCount,
          clientsCount,
          unbilledCount,
          totalFreightRevenue,
        },
        recentBookings,
      },
    });
  } catch (error) {
    next(error);
  }
}

export async function getProfile(req: Request, res: Response, next: NextFunction) {
  try {
    const tenant = await prisma.tenant.findUnique({
      where: { id: req.tenantId! },
      include: {
        bank_accounts: true,
      },
    });
    res.json({ success: true, data: tenant });
  } catch (error) {
    next(error);
  }
}

export async function updateProfile(req: Request, res: Response, next: NextFunction) {
  try {
    const schema = z.object({
      tradeName: z.string().optional(),
      contactPerson: z.string().min(2).optional(),
      phone: z.string().min(10).optional(),
      address: z.string().optional(),
      brandColor: z.string().optional(),
    });

    const body = schema.parse(req.body);
    const updated = await prisma.tenant.update({
      where: { id: req.tenantId! },
      data: {
        trade_name: body.tradeName,
        contact_person: body.contactPerson,
        phone: body.phone,
        address: body.address,
        brand_color: body.brandColor,
      },
    });

    res.json({ success: true, message: 'Agency profile updated.', data: updated });
  } catch (error) {
    next(error);
  }
}

export async function getBranches(req: Request, res: Response, next: NextFunction) {
  try {
    const branches = await prisma.branch.findMany({
      where: { tenant_id: req.tenantId! },
      include: {
        manager: {
          select: { id: true, name: true, email: true, phone: true },
        },
        _count: {
          select: { users: true, consignments: true },
        },
      },
      orderBy: { created_at: 'asc' },
    });
    res.json({ success: true, data: branches });
  } catch (error) {
    next(error);
  }
}

export async function createBranch(req: Request, res: Response, next: NextFunction) {
  try {
    const branchSchema = z.object({
      branchCode: z.string().min(2).max(10).toUpperCase(),
      branchName: z.string().min(2),
      address: z.string().min(5),
      city: z.string().min(2),
      state: z.string().min(2),
      pincode: z.string().min(6).max(6),
      phone: z.string().min(10),
      managerId: z.number().optional(),
    });

    const body = branchSchema.parse(req.body);

    // Check branch quota against plan
    const branchCount = await prisma.branch.count({ where: { tenant_id: req.tenantId! } });
    const tenant = await prisma.tenant.findUnique({ where: { id: req.tenantId! } });

    const branch = await prisma.branch.create({
      data: {
        tenant_id: req.tenantId!,
        branch_code: body.branchCode,
        branch_name: body.branchName,
        address: body.address,
        city: body.city,
        state: body.state,
        pincode: body.pincode,
        phone: body.phone,
        manager_id: body.managerId || null,
        status: 'active',
      },
    });

    res.status(201).json({ success: true, message: 'Branch created.', data: branch });
  } catch (error) {
    next(error);
  }
}

export async function getStaff(req: Request, res: Response, next: NextFunction) {
  try {
    const staff = await prisma.user.findMany({
      where: { tenant_id: req.tenantId! },
      include: {
        assigned_branch: {
          select: { id: true, branch_name: true, branch_code: true },
        },
      },
      orderBy: { created_at: 'desc' },
    });
    res.json({ success: true, data: staff });
  } catch (error) {
    next(error);
  }
}

export async function createStaff(req: Request, res: Response, next: NextFunction) {
  try {
    const staffSchema = z.object({
      name: z.string().min(2),
      email: z.string().email(),
      phone: z.string().min(10).optional(),
      password: z.string().min(6),
      role: z.enum([
        'agency_admin',
        'branch_manager',
        'operations_staff',
        'delivery_personnel',
        'accounts_staff',
      ]),
      branchId: z.number().optional(),
    });

    const body = staffSchema.parse(req.body);
    const hash = await bcrypt.hash(body.password, 10);

    const user = await prisma.user.create({
      data: {
        tenant_id: req.tenantId!,
        branch_id: body.branchId || null,
        name: body.name,
        email: body.email.toLowerCase(),
        phone: body.phone,
        password_hash: hash,
        role: body.role,
        status: 'active',
      },
    });

    res.status(201).json({
      success: true,
      message: 'Staff account created successfully.',
      data: { id: user.id, name: user.name, email: user.email, role: user.role },
    });
  } catch (error) {
    next(error);
  }
}

export async function getAgencyTickets(req: Request, res: Response, next: NextFunction) {
  try {
    const tickets = await prisma.ticket.findMany({
      where: { tenant_id: req.tenantId! },
      orderBy: { created_at: 'desc' },
    });
    res.json({ success: true, data: tickets });
  } catch (error) {
    next(error);
  }
}

export async function createAgencyTicket(req: Request, res: Response, next: NextFunction) {
  try {
    const ticketSchema = z.object({
      category: z.enum(['destination_request', 'billing', 'technical', 'general']),
      subject: z.string().min(3),
      description: z.string().min(5),
      destination_name: z.string().optional(),
      state_id: z.number().optional(),
      pincode: z.string().optional(),
      priority: z.enum(['low', 'normal', 'high', 'urgent']).default('normal'),
    });

    const body = ticketSchema.parse(req.body);
    const count = await prisma.ticket.count({ where: { tenant_id: req.tenantId! } });
    const ticketNumber = `TKT-${req.tenantId}-${Date.now().toString().slice(-4)}${(count + 1).toString().padStart(3, '0')}`;

    const ticket = await prisma.ticket.create({
      data: {
        tenant_id: req.tenantId!,
        ticket_number: ticketNumber,
        category: body.category,
        subject: body.subject,
        description: body.description,
        destination_name: body.destination_name || null,
        state_id: body.state_id || null,
        pincode: body.pincode || null,
        priority: body.priority,
        status: 'open',
      },
    });

    // Record audit log
    await prisma.auditLog.create({
      data: {
        tenant_id: req.tenantId!,
        user_id: req.user?.userId,
        action: `CREATE_SUPPORT_TICKET_${ticket.ticket_number}`,
        details_json: JSON.stringify({ category: body.category, subject: body.subject }),
        ip_address: req.ip,
      },
    });

    res.status(201).json({
      success: true,
      message: 'Support ticket submitted successfully.',
      data: ticket,
    });
  } catch (error) {
    next(error);
  }
}


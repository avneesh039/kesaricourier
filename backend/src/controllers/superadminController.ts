// backend/src/controllers/superadminController.ts
import { Request, Response, NextFunction } from 'express';
import { z } from 'zod';
import prisma from '../config/database';

export async function getDashboard(req: Request, res: Response, next: NextFunction) {
  try {
    const totalAgencies = await prisma.tenant.count();
    const activeAgencies = await prisma.tenant.count({ where: { status: 'active' } });
    const pendingAgencies = await prisma.tenant.count({ where: { status: 'pending_kyc' } });
    const suspendedAgencies = await prisma.tenant.count({ where: { status: 'suspended' } });

    const totalBookings = await prisma.consignment.count();
    const deliveredBookings = await prisma.consignment.count({ where: { status: 'delivered' } });
    const inTransitBookings = await prisma.consignment.count({ where: { status: 'in_transit' } });

    // Expiring within 30 days
    const today = new Date();
    const thirtyDaysLater = new Date();
    thirtyDaysLater.setDate(today.getDate() + 30);

    const expiringAgencies = await prisma.tenant.count({
      where: {
        status: 'active',
        validity_end: {
          gte: today,
          lte: thirtyDaysLater,
        },
      },
    });

    const expiredAgencies = await prisma.tenant.count({
      where: {
        validity_end: {
          lt: today,
        },
      },
    });

    // Recent agencies
    const recentAgencies = await prisma.tenant.findMany({
      take: 5,
      orderBy: { created_at: 'desc' },
      select: {
        id: true,
        name: true,
        slug: true,
        agency_code: true,
        status: true,
        city: true,
        state: true,
        created_at: true,
      },
    });

    // Platform revenue calculation from active subscriptions
    const activeTenants = await prisma.tenant.findMany({
      where: { status: 'active' },
      select: { plan_price: true },
    });
    const totalRevenue = activeTenants.reduce((acc, t) => acc + Number(t.plan_price || 0), 0);

    res.json({
      success: true,
      data: {
        kpis: {
          totalAgencies,
          activeAgencies,
          pendingAgencies,
          suspendedAgencies,
          expiringAgencies,
          expiredAgencies,
          totalBookings,
          deliveredBookings,
          inTransitBookings,
          totalRevenue,
        },
        recentAgencies,
      },
    });
  } catch (error) {
    next(error);
  }
}

export async function getAgencies(req: Request, res: Response, next: NextFunction) {
  try {
    const { status, search, page = '1', limit = '20' } = req.query;
    const pageNum = parseInt(page as string, 10);
    const limitNum = parseInt(limit as string, 10);
    const skip = (pageNum - 1) * limitNum;

    const where: any = {};
    if (status && typeof status === 'string' && status !== 'all') {
      where.status = status;
    }
    if (search && typeof search === 'string') {
      where.OR = [
        { name: { contains: search } },
        { agency_code: { contains: search } },
        { email: { contains: search } },
        { city: { contains: search } },
      ];
    }

    const [total, agencies] = await Promise.all([
      prisma.tenant.count({ where }),
      prisma.tenant.findMany({
        where,
        skip,
        take: limitNum,
        orderBy: { created_at: 'desc' },
        include: {
          _count: {
            select: {
              branches: true,
              users: true,
              consignments: true,
            },
          },
        },
      }),
    ]);

    res.json({
      success: true,
      data: agencies,
      meta: { page: pageNum, limit: limitNum, total, totalPages: Math.ceil(total / limitNum) },
    });
  } catch (error) {
    next(error);
  }
}

export async function getAgencyDetail(req: Request, res: Response, next: NextFunction) {
  try {
    const id = parseInt(String(req.params.id), 10);
    const agency = await prisma.tenant.findUnique({
      where: { id },
      include: {
        branches: true,
        users: {
          select: {
            id: true,
            name: true,
            email: true,
            role: true,
            status: true,
            last_login_at: true,
          },
        },
        subscriptions: true,
        bank_accounts: true,
        _count: {
          select: {
            consignments: true,
            invoices: true,
          },
        },
      },
    });

    if (!agency) {
      return res.status(404).json({ success: false, error: { message: 'Agency not found' } });
    }

    res.json({ success: true, data: agency });
  } catch (error) {
    next(error);
  }
}

export async function updateAgencyStatus(req: Request, res: Response, next: NextFunction) {
  try {
    const id = parseInt(String(req.params.id), 10);
    const { status, agencyCode, rejectionReason } = req.body;

    const agency = await prisma.tenant.findUnique({ where: { id } });
    if (!agency) {
      return res.status(404).json({ success: false, error: { message: 'Agency not found' } });
    }

    const updateData: any = { status };
    if (rejectionReason) {
      updateData.rejection_reason = rejectionReason;
    }

    // When approving agency, assign code and set validity
    if (status === 'active') {
      if (agencyCode) {
        updateData.agency_code = agencyCode;
      } else if (!agency.agency_code) {
        // Find highest existing code or assign random unique 2-digit code
        const lastCode = await prisma.tenant.findFirst({
          where: { agency_code: { not: null } },
          orderBy: { id: 'desc' },
          select: { agency_code: true },
        });
        const nextNum = lastCode?.agency_code ? parseInt(lastCode.agency_code, 10) + 1 : 27;
        updateData.agency_code = String(nextNum);
      }

      if (!agency.validity_start) {
        const start = new Date();
        const end = new Date();
        end.setFullYear(end.getFullYear() + 1);
        updateData.validity_start = start;
        updateData.validity_end = end;
      }
    }

    const updated = await prisma.tenant.update({
      where: { id },
      data: updateData,
    });

    res.json({
      success: true,
      message: `Agency status updated to ${status}.`,
      data: updated,
    });
  } catch (error) {
    next(error);
  }
}

export async function getPlans(req: Request, res: Response, next: NextFunction) {
  try {
    const plans = await prisma.subscriptionPlan.findMany({
      orderBy: { price: 'asc' },
    });
    res.json({ success: true, data: plans });
  } catch (error) {
    next(error);
  }
}

export async function createPlan(req: Request, res: Response, next: NextFunction) {
  try {
    const planSchema = z.object({
      name: z.string().min(2),
      description: z.string().optional(),
      price: z.number().min(0),
      billing_cycle: z.enum(['monthly', 'quarterly', 'half_yearly', 'yearly']),
      duration_days: z.number().default(365),
      max_branches: z.number().default(3),
      max_users: z.number().default(10),
      booking_quota: z.number().default(5000),
      features: z.record(z.boolean()).optional(),
    });

    const body = planSchema.parse(req.body);
    const plan = await prisma.subscriptionPlan.create({
      data: {
        name: body.name,
        description: body.description,
        price: body.price,
        billing_cycle: body.billing_cycle,
        duration_days: body.duration_days,
        max_branches: body.max_branches,
        max_users: body.max_users,
        booking_quota: body.booking_quota,
        features_json: body.features ? JSON.stringify(body.features) : null,
      },
    });

    res.status(201).json({ success: true, data: plan });
  } catch (error) {
    next(error);
  }
}

export async function getDestinations(req: Request, res: Response, next: NextFunction) {
  try {
    const destinations = await prisma.destination.findMany({
      include: {
        state: {
          include: {
            zone: true,
          },
        },
      },
      orderBy: { name: 'asc' },
    });

    res.json({ success: true, data: destinations });
  } catch (error) {
    next(error);
  }
}

export async function createDestination(req: Request, res: Response, next: NextFunction) {
  try {
    const destSchema = z.object({
      code: z.string().min(2).max(10).toUpperCase(),
      name: z.string().min(2).toUpperCase(),
      stateId: z.number(),
      pincode: z.string().min(6).max(6).optional(),
      isHub: z.boolean().default(false),
    });

    const body = destSchema.parse(req.body);
    const dest = await prisma.destination.create({
      data: {
        code: body.code,
        name: body.name,
        state_id: body.stateId,
        pincode: body.pincode,
        is_hub: body.isHub,
      },
    });

    res.status(201).json({ success: true, data: dest });
  } catch (error) {
    next(error);
  }
}

export async function getTickets(req: Request, res: Response, next: NextFunction) {
  try {
    const { status, category } = req.query;
    const where: any = {};
    if (status && status !== 'all') {
      where.status = String(status);
    }
    if (category && category !== 'all') {
      where.category = String(category);
    }

    const tickets = await prisma.ticket.findMany({
      where,
      include: {
        tenant: {
          select: { id: true, name: true, agency_code: true, phone: true, email: true },
        },
      },
      orderBy: { created_at: 'desc' },
    });

    res.json({ success: true, data: tickets });
  } catch (error) {
    next(error);
  }
}

export async function updateTicket(req: Request, res: Response, next: NextFunction) {
  try {
    const id = parseInt(String(req.params.id), 10);
    const { status, adminNotes } = req.body;

    const data: any = {};
    if (status) {
      data.status = status;
      if (status === 'resolved' || status === 'closed') {
        data.resolved_at = new Date();
      }
    }
    if (adminNotes !== undefined) {
      data.admin_notes = adminNotes;
    }

    const updated = await prisma.ticket.update({
      where: { id },
      data,
      include: {
        tenant: {
          select: { id: true, name: true, agency_code: true },
        },
      },
    });

    // Record audit log
    await prisma.auditLog.create({
      data: {
        tenant_id: updated.tenant_id,
        user_id: req.user?.userId,
        action: `SUPERADMIN_UPDATE_TICKET_${updated.ticket_number}`,
        details_json: JSON.stringify({ status, adminNotes }),
        ip_address: req.ip,
      },
    });

    res.json({ success: true, message: 'Ticket updated successfully', data: updated });
  } catch (error) {
    next(error);
  }
}

export async function getAuditLogs(req: Request, res: Response, next: NextFunction) {
  try {
    const { page = '1', limit = '50', search } = req.query;
    const pageNum = parseInt(page as string, 10);
    const limitNum = parseInt(limit as string, 10);
    const skip = (pageNum - 1) * limitNum;

    const where: any = {};
    if (search && typeof search === 'string') {
      where.OR = [
        { action: { contains: search } },
        { details_json: { contains: search } },
      ];
    }

    const [total, logs] = await Promise.all([
      prisma.auditLog.count({ where }),
      prisma.auditLog.findMany({
        where,
        skip,
        take: limitNum,
        orderBy: { created_at: 'desc' },
        include: {
          tenant: { select: { id: true, name: true, agency_code: true } },
          user: { select: { id: true, name: true, email: true, role: true } },
        },
      }),
    ]);

    res.json({
      success: true,
      data: logs,
      meta: { page: pageNum, limit: limitNum, total, totalPages: Math.ceil(total / limitNum) },
    });
  } catch (error) {
    next(error);
  }
}


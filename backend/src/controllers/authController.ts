// backend/src/controllers/authController.ts
import { Request, Response, NextFunction } from 'express';
import bcrypt from 'bcryptjs';
import crypto from 'crypto';
import { z } from 'zod';
import prisma from '../config/database';
import { signAccessToken, signRefreshToken, verifyRefreshToken } from '../config/jwt';

const loginSchema = z.object({
  email: z.string().email(),
  password: z.string().min(6),
  tenantSlug: z.string().optional(),
});

const registerAgencySchema = z.object({
  name: z.string().min(3),
  tradeName: z.string().optional(),
  contactPerson: z.string().min(2),
  email: z.string().email(),
  phone: z.string().min(10),
  city: z.string().min(2),
  state: z.string().min(2),
  pincode: z.string().min(6),
  gstin: z.string().optional(),
  panNo: z.string().optional(),
  planType: z.string().default('Yearly'),
});

export async function login(req: Request, res: Response, next: NextFunction) {
  try {
    const { email, password, tenantSlug } = loginSchema.parse(req.body);

    const user = await prisma.user.findUnique({
      where: { email: email.toLowerCase().trim() },
      include: {
        tenant: true,
        assigned_branch: true,
      },
    });

    if (!user) {
      return res.status(401).json({
        success: false,
        error: { code: 'INVALID_CREDENTIALS', message: 'Invalid email or password.' },
      });
    }

    const isMatch = await bcrypt.compare(password, user.password_hash);
    if (!isMatch) {
      return res.status(401).json({
        success: false,
        error: { code: 'INVALID_CREDENTIALS', message: 'Invalid email or password.' },
      });
    }

    if (user.status !== 'active') {
      return res.status(403).json({
        success: false,
        error: { code: 'USER_DEACTIVATED', message: 'Your account is currently deactivated.' },
      });
    }

    // Tenant check if agency user
    if (user.tenant_id && user.tenant) {
      if (tenantSlug && user.tenant.slug !== tenantSlug && user.role !== 'superadmin') {
        return res.status(403).json({
          success: false,
          error: { code: 'WRONG_TENANT', message: 'You do not have access to this agency portal.' },
        });
      }

      if (user.tenant.status === 'suspended') {
        return res.status(403).json({
          success: false,
          error: { code: 'TENANT_SUSPENDED', message: 'Agency portal is suspended. Contact administration.' },
        });
      }

      if (user.tenant.status === 'pending_kyc') {
        return res.status(403).json({
          success: false,
          error: { code: 'PENDING_APPROVAL', message: 'Agency registration is currently under review.' },
        });
      }

      // Check subscription lock
      if (user.tenant.validity_end) {
        const today = new Date();
        const lockDate = new Date(user.tenant.validity_end);
        lockDate.setDate(lockDate.getDate() + user.tenant.grace_period_days);

        if (today > lockDate) {
          return res.status(403).json({
            success: false,
            error: {
              code: 'SUBSCRIPTION_LOCKED',
              message: `Agency subscription expired on ${user.tenant.validity_end.toISOString().split('T')[0]} and grace period ended.`,
            },
          });
        }
      }
    }

    // Update last login
    await prisma.user.update({
      where: { id: user.id },
      data: { last_login_at: new Date(), failed_logins: 0 },
    });

    // Generate tokens
    const tokenPayload = {
      userId: user.id,
      tenantId: user.tenant_id,
      branchId: user.branch_id,
      email: user.email,
      role: user.role,
      name: user.name,
    };

    const accessToken = signAccessToken(tokenPayload);
    const refreshToken = signRefreshToken({ userId: user.id });

    // Store refresh token hash in DB
    const tokenHash = crypto.createHash('sha256').update(refreshToken).digest('hex');
    const expiresAt = new Date();
    expiresAt.setDate(expiresAt.getDate() + 7);

    await prisma.refreshToken.create({
      data: {
        user_id: user.id,
        token_hash: tokenHash,
        expires_at: expiresAt,
      },
    });

    // Set HTTP-only cookie
    res.cookie('refreshToken', refreshToken, {
      httpOnly: true,
      secure: process.env.NODE_ENV === 'production',
      sameSite: 'lax',
      maxAge: 7 * 24 * 60 * 60 * 1000,
    });

    res.json({
      success: true,
      message: 'Login successful',
      data: {
        accessToken,
        user: {
          id: user.id,
          name: user.name,
          email: user.email,
          role: user.role,
          tenantId: user.tenant_id,
          branchId: user.branch_id,
          branchName: user.assigned_branch?.branch_name || null,
          tenantName: user.tenant?.name || null,
          tradeName: user.tenant?.trade_name || null,
          agencyCode: user.tenant?.agency_code || null,
          brandColor: user.tenant?.brand_color || '#1a73e8',
        },
      },
    });
  } catch (error) {
    next(error);
  }
}

export async function refreshToken(req: Request, res: Response, next: NextFunction) {
  try {
    const token = req.cookies.refreshToken;
    if (!token) {
      return res.status(401).json({
        success: false,
        error: { code: 'NO_REFRESH_TOKEN', message: 'No refresh token provided.' },
      });
    }

    const decoded = verifyRefreshToken(token);
    if (!decoded) {
      return res.status(401).json({
        success: false,
        error: { code: 'INVALID_REFRESH_TOKEN', message: 'Refresh token expired or invalid.' },
      });
    }

    const tokenHash = crypto.createHash('sha256').update(token).digest('hex');
    const existing = await prisma.refreshToken.findFirst({
      where: {
        user_id: decoded.userId,
        token_hash: tokenHash,
        is_revoked: false,
        expires_at: { gt: new Date() },
      },
      include: {
        user: {
          include: {
            tenant: true,
            assigned_branch: true,
          },
        },
      },
    });

    if (!existing || !existing.user || existing.user.status !== 'active') {
      return res.status(401).json({
        success: false,
        error: { code: 'TOKEN_REVOKED', message: 'Refresh token is revoked or user is inactive.' },
      });
    }

    // Revoke old token
    await prisma.refreshToken.update({
      where: { id: existing.id },
      data: { is_revoked: true },
    });

    // Create new refresh token
    const newRefreshToken = signRefreshToken({ userId: existing.user.id });
    const newTokenHash = crypto.createHash('sha256').update(newRefreshToken).digest('hex');
    const newExpiresAt = new Date();
    newExpiresAt.setDate(newExpiresAt.getDate() + 7);

    await prisma.refreshToken.create({
      data: {
        user_id: existing.user.id,
        token_hash: newTokenHash,
        expires_at: newExpiresAt,
      },
    });

    const accessToken = signAccessToken({
      userId: existing.user.id,
      tenantId: existing.user.tenant_id,
      branchId: existing.user.branch_id,
      email: existing.user.email,
      role: existing.user.role,
      name: existing.user.name,
    });

    res.cookie('refreshToken', newRefreshToken, {
      httpOnly: true,
      secure: process.env.NODE_ENV === 'production',
      sameSite: 'lax',
      maxAge: 7 * 24 * 60 * 60 * 1000,
    });

    res.json({
      success: true,
      data: { accessToken },
    });
  } catch (error) {
    next(error);
  }
}

export async function logout(req: Request, res: Response, next: NextFunction) {
  try {
    const token = req.cookies.refreshToken;
    if (token) {
      const tokenHash = crypto.createHash('sha256').update(token).digest('hex');
      await prisma.refreshToken.updateMany({
        where: { token_hash: tokenHash },
        data: { is_revoked: true },
      });
    }

    res.clearCookie('refreshToken');
    res.json({ success: true, message: 'Logged out successfully.' });
  } catch (error) {
    next(error);
  }
}

export async function getMe(req: Request, res: Response, next: NextFunction) {
  try {
    if (!req.user) {
      return res.status(401).json({ success: false, error: { message: 'Unauthorized' } });
    }

    const user = await prisma.user.findUnique({
      where: { id: req.user.userId },
      include: {
        tenant: true,
        assigned_branch: true,
      },
    });

    if (!user) {
      return res.status(404).json({ success: false, error: { message: 'User not found' } });
    }

    res.json({
      success: true,
      data: {
        id: user.id,
        name: user.name,
        email: user.email,
        phone: user.phone,
        role: user.role,
        status: user.status,
        lastLoginAt: user.last_login_at,
        tenant: user.tenant
          ? {
              id: user.tenant.id,
              name: user.tenant.name,
              tradeName: user.tenant.trade_name,
              slug: user.tenant.slug,
              agencyCode: user.tenant.agency_code,
              status: user.tenant.status,
              brandColor: user.tenant.brand_color,
              validityEnd: user.tenant.validity_end,
            }
          : null,
        branch: user.assigned_branch
          ? {
              id: user.assigned_branch.id,
              code: user.assigned_branch.branch_code,
              name: user.assigned_branch.branch_name,
            }
          : null,
      },
    });
  } catch (error) {
    next(error);
  }
}

export async function registerAgency(req: Request, res: Response, next: NextFunction) {
  try {
    const body = registerAgencySchema.parse(req.body);

    const slug = body.name
      .toLowerCase()
      .replace(/[^a-z0-9]/g, '-')
      .replace(/-+/g, '-')
      .substring(0, 30);

    const existingSlug = await prisma.tenant.findUnique({ where: { slug } });
    const finalSlug = existingSlug ? `${slug}-${Date.now().toString().slice(-4)}` : slug;

    const tenant = await prisma.tenant.create({
      data: {
        uuid: crypto.randomUUID(),
        slug: finalSlug,
        name: body.name,
        trade_name: body.tradeName || body.name,
        contact_person: body.contactPerson,
        phone: body.phone,
        email: body.email.toLowerCase(),
        city: body.city,
        state: body.state,
        pincode: body.pincode,
        gstin: body.gstin || null,
        pan_no: body.panNo || null,
        status: 'pending_kyc',
        plan_type: body.planType,
      },
    });

    // Create temporary admin user
    const tempPassword = `Kesari@${Math.floor(1000 + Math.random() * 9000)}`;
    const hash = await bcrypt.hash(tempPassword, 10);

    await prisma.user.create({
      data: {
        tenant_id: tenant.id,
        name: body.contactPerson,
        email: body.email.toLowerCase(),
        phone: body.phone,
        password_hash: hash,
        role: 'agency_admin',
        status: 'active',
      },
    });

    res.status(201).json({
      success: true,
      message: 'Agency registration submitted successfully. Awaiting Super Admin review and code assignment.',
      data: {
        tenantId: tenant.id,
        slug: tenant.slug,
        initialCredentials: {
          email: body.email,
          tempPassword,
        },
      },
    });
  } catch (error) {
    next(error);
  }
}

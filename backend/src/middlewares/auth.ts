// backend/src/middlewares/auth.ts
import { Request, Response, NextFunction } from 'express';
import { verifyAccessToken, TokenPayload } from '../config/jwt';
import prisma from '../config/database';

declare global {
  namespace Express {
    interface Request {
      user?: TokenPayload;
      tenantId?: number | null;
      branchId?: number | null;
    }
  }
}

export async function authenticate(req: Request, res: Response, next: NextFunction) {
  const authHeader = req.headers.authorization;

  if (!authHeader || !authHeader.startsWith('Bearer ')) {
    return res.status(401).json({
      success: false,
      error: { code: 'UNAUTHORIZED', message: 'Authentication required. No token provided.' },
    });
  }

  const token = authHeader.split(' ')[1];
  const payload = verifyAccessToken(token);

  if (!payload) {
    return res.status(401).json({
      success: false,
      error: { code: 'TOKEN_EXPIRED', message: 'Access token is invalid or has expired.' },
    });
  }

  // Verify user still exists and is active
  const user = await prisma.user.findUnique({
    where: { id: payload.userId },
    include: { tenant: true },
  });

  if (!user || user.status !== 'active') {
    return res.status(401).json({
      success: false,
      error: { code: 'USER_INACTIVE', message: 'User account is deactivated or locked.' },
    });
  }

  // If agency user, verify agency tenant status & subscription grace period
  if (user.tenant_id && user.tenant) {
    if (user.tenant.status === 'suspended') {
      return res.status(403).json({
        success: false,
        error: { code: 'TENANT_SUSPENDED', message: 'Agency portal is currently suspended. Please contact support.' },
      });
    }

    if (user.tenant.validity_end) {
      const today = new Date();
      const graceEnd = new Date(user.tenant.validity_end);
      graceEnd.setDate(graceEnd.getDate() + user.tenant.grace_period_days);

      if (today > graceEnd) {
        return res.status(403).json({
          success: false,
          error: {
            code: 'SUBSCRIPTION_EXPIRED',
            message: `Agency subscription expired on ${user.tenant.validity_end.toISOString().split('T')[0]} and grace period ended. Please renew to continue.`,
          },
        });
      }
    }
  }

  req.user = payload;
  req.tenantId = user.tenant_id;
  req.branchId = user.branch_id;

  next();
}

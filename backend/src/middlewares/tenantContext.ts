// backend/src/middlewares/tenantContext.ts
import { Request, Response, NextFunction } from 'express';

export function enforceTenantContext(req: Request, res: Response, next: NextFunction) {
  if (!req.user) {
    return res.status(401).json({
      success: false,
      error: { code: 'UNAUTHORIZED', message: 'Authentication required' },
    });
  }

  // Superadmin can optionally filter by x-tenant-id or view all
  if (req.user.role === 'superadmin') {
    const overrideTenant = req.headers['x-tenant-id'];
    if (overrideTenant && typeof overrideTenant === 'string') {
      req.tenantId = parseInt(overrideTenant, 10);
    } else {
      // Default to primary agency tenant so operations/booking create works seamlessly for superadmin
      req.tenantId = 1;
    }
    return next();
  }

  // Regular users are strictly tied to their tenant
  if (!req.user.tenantId) {
    return res.status(403).json({
      success: false,
      error: { code: 'NO_TENANT_ASSIGNED', message: 'User is not assigned to any agency tenant.' },
    });
  }

  req.tenantId = req.user.tenantId;
  next();
}

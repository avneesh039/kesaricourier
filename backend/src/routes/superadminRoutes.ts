// backend/src/routes/superadminRoutes.ts
import { Router } from 'express';
import {
  getDashboard,
  getAgencies,
  getAgencyDetail,
  updateAgencyStatus,
  getPlans,
  createPlan,
  getDestinations,
  createDestination,
  getTickets,
  updateTicket,
  getAuditLogs,
} from '../controllers/superadminController';
import { authenticate } from '../middlewares/auth';
import { authorize } from '../middlewares/rbac';

const router = Router();

// GET /destinations can be accessed by any authenticated user for booking counters and tariffs
router.get('/destinations', authenticate, getDestinations);

// Protect all other Super Admin governance endpoints
router.use(authenticate, authorize('superadmin'));

router.get('/dashboard', getDashboard);
router.get('/agencies', getAgencies);
router.get('/agencies/:id', getAgencyDetail);
router.patch('/agencies/:id/status', updateAgencyStatus);

router.get('/plans', getPlans);
router.post('/plans', createPlan);

router.post('/destinations', createDestination);

router.get('/tickets', getTickets);
router.patch('/tickets/:id', updateTicket);
router.get('/audit-logs', getAuditLogs);

export default router;


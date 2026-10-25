// backend/src/routes/agencyRoutes.ts
import { Router } from 'express';
import {
  getAgencyDashboard,
  getProfile,
  updateProfile,
  getBranches,
  createBranch,
  getStaff,
  createStaff,
  getAgencyTickets,
  createAgencyTicket,
} from '../controllers/agencyController';
import { authenticate } from '../middlewares/auth';
import { authorize } from '../middlewares/rbac';
import { enforceTenantContext } from '../middlewares/tenantContext';

const router = Router();

router.use(authenticate, enforceTenantContext);

router.get('/dashboard', authorize('agency_admin', 'branch_manager'), getAgencyDashboard);
router.get('/profile', authorize('agency_admin'), getProfile);
router.put('/profile', authorize('agency_admin'), updateProfile);

router.get('/branches', authorize('agency_admin', 'branch_manager'), getBranches);
router.post('/branches', authorize('agency_admin'), createBranch);

router.get('/staff', authorize('agency_admin', 'branch_manager'), getStaff);
router.post('/staff', authorize('agency_admin'), createStaff);

router.get('/tickets', authorize('agency_admin', 'branch_manager', 'operations_staff'), getAgencyTickets);
router.post('/tickets', authorize('agency_admin', 'branch_manager', 'operations_staff'), createAgencyTicket);

export default router;


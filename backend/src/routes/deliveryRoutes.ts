// backend/src/routes/deliveryRoutes.ts
import { Router } from 'express';
import {
  getMyRunsheets,
  submitPod,
  recordFailureAttempt,
} from '../controllers/deliveryController';
import { authenticate } from '../middlewares/auth';
import { authorize } from '../middlewares/rbac';

const router = Router();

router.use(authenticate, authorize('delivery_personnel', 'operations_staff', 'agency_admin', 'superadmin'));

router.get('/my-runsheets', getMyRunsheets);
router.post('/pod', submitPod);
router.post('/attempt-failed', recordFailureAttempt);

export default router;

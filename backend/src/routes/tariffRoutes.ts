// backend/src/routes/tariffRoutes.ts
import { Router } from 'express';
import { getTariffs, calculateRate } from '../controllers/tariffController';
import { authenticate } from '../middlewares/auth';
import { enforceTenantContext } from '../middlewares/tenantContext';

const router = Router();

router.use(authenticate, enforceTenantContext);

router.get('/', getTariffs);
router.post('/calculate', calculateRate);

export default router;

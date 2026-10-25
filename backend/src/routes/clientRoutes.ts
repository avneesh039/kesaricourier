// backend/src/routes/clientRoutes.ts
import { Router } from 'express';
import {
  getClients,
  createClient,
  getClientDetail,
} from '../controllers/clientController';
import { authenticate } from '../middlewares/auth';
import { enforceTenantContext } from '../middlewares/tenantContext';

const router = Router();

router.use(authenticate, enforceTenantContext);

router.get('/', getClients);
router.post('/', createClient);
router.get('/:id', getClientDetail);

export default router;

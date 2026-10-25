// backend/src/routes/operationRoutes.ts
import { Router } from 'express';
import {
  getSingleThermalLabel,
  getBatchThermalLabels,
  createManifest,
  getManifests,
  createRunsheet,
  getRunsheets,
  inscanConsignments,
  recordSinglePod,
} from '../controllers/operationController';
import { authenticate } from '../middlewares/auth';
import { enforceTenantContext } from '../middlewares/tenantContext';

const router = Router();

router.use(authenticate, enforceTenantContext);

router.get('/print/single/:awb', getSingleThermalLabel);
router.post('/print/batch', getBatchThermalLabels);

router.post('/manifests', createManifest);
router.get('/manifests', getManifests);

router.post('/runsheets', createRunsheet);
router.get('/runsheets', getRunsheets);

router.post('/inscan', inscanConsignments);
router.post('/pod-single', recordSinglePod);

export default router;

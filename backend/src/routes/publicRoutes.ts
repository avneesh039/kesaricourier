// backend/src/routes/publicRoutes.ts
import { Router } from 'express';
import {
  trackShipment,
  estimateTariff,
  getPublicDestinations,
} from '../controllers/publicController';

const router = Router();

router.get('/track/:awb', trackShipment);
router.post('/tariff-estimate', estimateTariff);
router.get('/destinations', getPublicDestinations);

export default router;

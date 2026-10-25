// backend/src/routes/bookingRoutes.ts
import { Router } from 'express';
import {
  createBooking,
  getBookings,
  getBookingByAwb,
  updateBookingStatus,
} from '../controllers/bookingController';
import { authenticate } from '../middlewares/auth';
import { enforceTenantContext } from '../middlewares/tenantContext';

const router = Router();

router.use(authenticate, enforceTenantContext);

router.post('/', createBooking);
router.get('/', getBookings);
router.get('/:awb', getBookingByAwb);
router.patch('/:awb/status', updateBookingStatus);

export default router;

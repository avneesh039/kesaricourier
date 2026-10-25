// backend/src/routes/index.ts
import { Router } from 'express';
import authRoutes from './authRoutes';
import superadminRoutes from './superadminRoutes';
import agencyRoutes from './agencyRoutes';
import clientRoutes from './clientRoutes';
import tariffRoutes from './tariffRoutes';
import bookingRoutes from './bookingRoutes';
import operationRoutes from './operationRoutes';
import deliveryRoutes from './deliveryRoutes';
import invoiceRoutes from './invoiceRoutes';
import publicRoutes from './publicRoutes';

const router = Router();

router.use('/auth', authRoutes);
router.use('/superadmin', superadminRoutes);
router.use('/agency', agencyRoutes);
router.use('/clients', clientRoutes);
router.use('/tariffs', tariffRoutes);
router.use('/bookings', bookingRoutes);
router.use('/operations', operationRoutes);
router.use('/delivery', deliveryRoutes);
router.use('/invoices', invoiceRoutes);
router.use('/public', publicRoutes);

export default router;

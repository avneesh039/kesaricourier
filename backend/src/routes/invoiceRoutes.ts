// backend/src/routes/invoiceRoutes.ts
import { Router } from 'express';
import {
  getUnbilledConsignments,
  generateInvoice,
  getInvoices,
  getInvoiceDetail,
  recordPayment,
  exportGstr1,
  updateInvoiceLineItem,
} from '../controllers/invoiceController';
import { authenticate } from '../middlewares/auth';
import { enforceTenantContext } from '../middlewares/tenantContext';

const router = Router();

router.use(authenticate, enforceTenantContext);

router.get('/unbilled-consignments', getUnbilledConsignments);
router.post('/generate', generateInvoice);
router.get('/', getInvoices);
router.get('/gstr1-export', exportGstr1);
router.get('/:id', getInvoiceDetail);
router.patch('/:id/items/:itemId', updateInvoiceLineItem);
router.post('/payments', recordPayment);

export default router;

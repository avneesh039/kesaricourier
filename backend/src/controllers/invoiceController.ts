// backend/src/controllers/invoiceController.ts
import { Request, Response, NextFunction } from 'express';
import { z } from 'zod';
import prisma from '../config/database';
import { calculateGst } from '../utils/gstCalc';

export async function getUnbilledConsignments(req: Request, res: Response, next: NextFunction) {
  try {
    const { clientId, startDate, endDate } = req.query;

    if (!clientId) {
      return res.status(400).json({ success: false, error: { message: 'Client ID is required.' } });
    }

    const where: any = {
      tenant_id: req.tenantId!,
      client_id: parseInt(clientId as string, 10),
      is_billed: false,
    };

    if (startDate && endDate) {
      where.booking_date = {
        gte: new Date(startDate as string),
        lte: new Date(endDate as string),
      };
    }

    const consignments = await prisma.consignment.findMany({
      where,
      orderBy: { booking_date: 'asc' },
      include: {
        destination: true,
      },
    });

    res.json({ success: true, data: consignments });
  } catch (error) {
    next(error);
  }
}

export async function generateInvoice(req: Request, res: Response, next: NextFunction) {
  try {
    const schema = z.object({
      clientId: z.number(),
      consignmentIds: z.array(z.number()).min(1),
      invoiceDate: z.string().optional(),
      dueDate: z.string().optional(),
      docketCharges: z.number().default(0),
      bankAccountId: z.number().optional(),
    });

    const body = schema.parse(req.body);
    const tenantId = req.tenantId!;

    // 1. Fetch Tenant & Client
    const [tenant, client] = await Promise.all([
      prisma.tenant.findUnique({
        where: { id: tenantId },
        include: { bank_accounts: true },
      }),
      prisma.client.findUnique({
        where: { id: body.clientId },
      }),
    ]);

    if (!tenant || !client) {
      return res.status(404).json({ success: false, error: { message: 'Agency or Client not found.' } });
    }

    // 2. Fetch consignments
    const consignments = await prisma.consignment.findMany({
      where: {
        id: { in: body.consignmentIds },
        tenant_id: tenantId,
        is_billed: false,
      },
      include: { destination: true },
    });

    if (consignments.length === 0) {
      return res.status(400).json({
        success: false,
        error: { message: 'No valid unbilled consignments selected.' },
      });
    }

    // 3. Compute Subtotal
    const subtotal = consignments.reduce((sum, c) => sum + Number(c.subtotal), 0);
    const fuelPercent = Number(client.fuel_surcharge_percent || 0);

    // 4. Compute GST SAC 996812 Tax Breakdown
    const gstResult = calculateGst(
      subtotal,
      fuelPercent,
      body.docketCharges,
      tenant.gstin || '',
      client.gstin || '',
      tenant.state || 'Chhattisgarh',
      client.state || 'Chhattisgarh'
    );

    // Generate Invoice Number: INV-{AgencyCode}-{YearMonth}-{Sequence}
    const yearMonth = new Date().toISOString().slice(2, 7).replace('-', '');
    const count = await prisma.invoice.count({ where: { tenant_id: tenantId } });
    const invoiceNo = `INV-${tenant.agency_code || '26'}-${yearMonth}-${String(count + 1).padStart(4, '0')}`;

    const invDate = body.invoiceDate ? new Date(body.invoiceDate) : new Date();
    const dueDate = body.dueDate ? new Date(body.dueDate) : new Date(Date.now() + 15 * 86400000);

    // Determine Period From / To
    const sortedDates = consignments.map((c) => new Date(c.booking_date).getTime()).sort();
    const periodFrom = new Date(sortedDates[0]);
    const periodTo = new Date(sortedDates[sortedDates.length - 1]);

    const bankId = body.bankAccountId || tenant.bank_accounts[0]?.id || null;

    // 5. Execute Atomic Prisma Transaction
    const invoice = await prisma.$transaction(async (tx) => {
      const created = await tx.invoice.create({
        data: {
          tenant_id: tenantId,
          invoice_no: invoiceNo,
          invoice_date: invDate,
          due_date: dueDate,
          client_id: body.clientId,
          bank_account_id: bankId,
          period_from: periodFrom,
          period_to: periodTo,
          consignments_count: consignments.length,
          subtotal_amount: gstResult.subtotal,
          fuel_surcharge_percent: gstResult.fuelPercent,
          fuel_surcharge_amount: gstResult.fuelAmount,
          docket_charge_amount: gstResult.docketCharges,
          taxable_amount: gstResult.taxableAmount,
          cgst_rate: gstResult.cgstRate,
          cgst_amount: gstResult.cgstAmount,
          sgst_rate: gstResult.sgstRate,
          sgst_amount: gstResult.sgstAmount,
          igst_rate: gstResult.igstRate,
          igst_amount: gstResult.igstAmount,
          total_gst_amount: gstResult.totalGst,
          round_off: gstResult.roundOff,
          grand_total: gstResult.grandTotal,
          payment_status: 'pending',
          created_by_user_id: req.user?.userId || null,
          items: {
            create: consignments.map((c) => ({
              consignment_id: c.id,
              consignment_no: c.consignment_no,
              booking_date: c.booking_date,
              destination_name: c.destination.name,
              dest_code: c.destination.code,
              mode: c.mode,
              pieces: c.pieces,
              billable_weight: c.billable_weight,
              amount: c.subtotal,
            })),
          },
        },
        include: {
          client: true,
          items: true,
        },
      });

      // Mark consignments as billed
      await tx.consignment.updateMany({
        where: { id: { in: body.consignmentIds } },
        data: {
          is_billed: true,
          invoice_id: created.id,
        },
      });

      return created;
    });

    res.status(201).json({
      success: true,
      message: `Tax Invoice ${invoiceNo} generated for ${client.name}.`,
      data: {
        ...invoice,
        amountInWords: gstResult.amountInWords,
      },
    });
  } catch (error) {
    next(error);
  }
}

export async function getInvoices(req: Request, res: Response, next: NextFunction) {
  try {
    const tenantId = req.tenantId!;
    const { status, clientId } = req.query;

    const where: any = { tenant_id: tenantId };
    if (status && typeof status === 'string' && status !== 'all') {
      where.payment_status = status;
    }
    if (clientId) {
      where.client_id = parseInt(clientId as string, 10);
    }

    const invoices = await prisma.invoice.findMany({
      where,
      orderBy: { invoice_date: 'desc' },
      include: {
        client: { select: { id: true, name: true, client_code: true, gstin: true } },
      },
    });

    res.json({ success: true, data: invoices });
  } catch (error) {
    next(error);
  }
}

export async function getInvoiceDetail(req: Request, res: Response, next: NextFunction) {
  try {
    const id = parseInt(String(req.params.id), 10);
    const invoice = await prisma.invoice.findFirst({
      where: { id, tenant_id: req.tenantId! },
      include: {
        client: true,
        tenant: true,
        bank_account: true,
        items: { orderBy: { booking_date: 'asc' } },
        payments: { orderBy: { payment_date: 'desc' } },
      },
    });

    if (!invoice) {
      return res.status(404).json({ success: false, error: { message: 'Invoice not found.' } });
    }

    const { amountInWords } = calculateGst(
      Number(invoice.subtotal_amount),
      Number(invoice.fuel_surcharge_percent),
      Number(invoice.docket_charge_amount),
      invoice.tenant?.gstin || '',
      invoice.client.gstin || '',
      invoice.tenant?.state || 'Chhattisgarh',
      invoice.client.state || 'Chhattisgarh'
    );

    res.json({
      success: true,
      data: {
        ...invoice,
        amountInWords,
      },
    });
  } catch (error) {
    next(error);
  }
}

export async function recordPayment(req: Request, res: Response, next: NextFunction) {
  try {
    const schema = z.object({
      invoiceId: z.number(),
      amount: z.number().min(1),
      paymentMode: z.enum(['Cash', 'Cheque', 'NEFT/RTGS', 'UPI']),
      referenceNo: z.string().optional(),
      notes: z.string().optional(),
      paymentDate: z.string().optional(),
    });

    const body = schema.parse(req.body);
    const tenantId = req.tenantId!;

    const invoice = await prisma.invoice.findFirst({
      where: { id: body.invoiceId, tenant_id: tenantId },
    });

    if (!invoice) {
      return res.status(404).json({ success: false, error: { message: 'Invoice not found.' } });
    }

    const receiptNo = `RCP-${Date.now().toString().slice(-8)}`;
    const payDate = body.paymentDate ? new Date(body.paymentDate) : new Date();

    const payment = await prisma.payment.create({
      data: {
        tenant_id: tenantId,
        invoice_id: invoice.id,
        amount: body.amount,
        payment_date: payDate,
        payment_mode: body.paymentMode,
        reference_no: body.referenceNo || null,
        receipt_no: receiptNo,
        notes: body.notes || null,
        created_by_user_id: req.user?.userId || null,
      },
    });

    // Update invoice paid amount & status
    const newPaidAmount = Number(invoice.paid_amount) + body.amount;
    const isFullyPaid = newPaidAmount >= Number(invoice.grand_total);
    const newStatus = isFullyPaid ? 'paid' : 'partially_paid';

    await prisma.invoice.update({
      where: { id: invoice.id },
      data: {
        paid_amount: newPaidAmount,
        payment_status: newStatus,
        payment_mode: body.paymentMode,
      },
    });

    res.status(201).json({
      success: true,
      message: `Payment of ₹${body.amount} recorded successfully.`,
      data: payment,
    });
  } catch (error) {
    next(error);
  }
}

export async function exportGstr1(req: Request, res: Response, next: NextFunction) {
  try {
    const tenantId = req.tenantId!;
    const invoices = await prisma.invoice.findMany({
      where: { tenant_id: tenantId },
      include: { client: true },
      orderBy: { invoice_date: 'asc' },
    });

    let csv =
      'GSTIN/UIN of Recipient,Receiver Name,Invoice Number,Invoice date,Invoice Value,Place Of Supply,Reverse Charge,Applicable % of Tax Rate,Invoice Type,E-Commerce GSTIN,Rate,Taxable Value,Cess Amount\n';

    for (const inv of invoices) {
      const gstin = inv.client.gstin || 'URP';
      const name = `"${inv.client.name.replace(/"/g, '""')}"`;
      const invNo = inv.invoice_no;
      const invDate = new Date(inv.invoice_date).toLocaleDateString('en-GB'); // DD/MM/YYYY
      const invVal = Number(inv.grand_total).toFixed(2);
      const pos = inv.client.state ? `${inv.client.state}` : '22-Chhattisgarh';
      const rate = Number(inv.igst_rate) > 0 ? '18%' : '18%'; // total GST
      const taxable = Number(inv.taxable_amount).toFixed(2);

      csv += `${gstin},${name},${invNo},${invDate},${invVal},${pos},N,,Regular,,${rate},${taxable},0.00\n`;
    }

    res.header('Content-Type', 'text/csv');
    res.attachment(`GSTR1_Export_${new Date().toISOString().slice(0, 10)}.csv`);
    res.send(csv);
  } catch (error) {
    next(error);
  }
}

export async function updateInvoiceLineItem(req: Request, res: Response, next: NextFunction) {
  try {
    const invoiceId = parseInt(String(req.params.id), 10);
    const itemId = parseInt(String(req.params.itemId), 10);

    const schema = z.object({
      weight: z.number().optional(),
      amount: z.number().optional(),
      destinationCode: z.string().optional(),
    });

    const body = schema.parse(req.body);
    const tenantId = req.tenantId!;

    const invoice = await prisma.invoice.findFirst({
      where: { id: invoiceId, tenant_id: tenantId },
      include: { items: true, client: true, tenant: true },
    });

    if (!invoice) {
      return res.status(404).json({ success: false, error: { message: 'Invoice not found.' } });
    }

    const item = invoice.items.find((i) => i.id === itemId);
    if (!item) {
      return res.status(404).json({ success: false, error: { message: 'Invoice item not found.' } });
    }

    const updateData: any = {};
    if (body.weight !== undefined) updateData.weight = body.weight;
    if (body.amount !== undefined) updateData.amount = body.amount;
    if (body.destinationCode !== undefined) updateData.destination_code = body.destinationCode;

    await prisma.invoiceItem.update({
      where: { id: itemId },
      data: updateData,
    });

    // Recompute total subtotal from items
    const allItems = await prisma.invoiceItem.findMany({
      where: { invoice_id: invoiceId },
    });
    const newSubtotal = allItems.reduce((sum, it) => sum + Number(it.amount), 0);

    const gst = calculateGst(
      newSubtotal,
      Number(invoice.fuel_surcharge_percent),
      Number(invoice.docket_charge_amount),
      invoice.tenant?.gstin || '',
      invoice.client.gstin || '',
      invoice.tenant?.state || 'Chhattisgarh',
      invoice.client.state || 'Chhattisgarh'
    );

    const updatedInvoice = await prisma.invoice.update({
      where: { id: invoiceId },
      data: {
        subtotal_amount: newSubtotal,
        fuel_surcharge_amount: gst.fuelAmount,
        cgst_amount: gst.cgstAmount,
        sgst_amount: gst.sgstAmount,
        igst_amount: gst.igstAmount,
        taxable_amount: gst.taxableAmount,
        total_gst_amount: gst.totalGst,
        grand_total: gst.grandTotal,
        round_off: gst.roundOff,
      },
      include: { items: true },
    });

    res.json({
      success: true,
      message: 'Invoice line item updated and tax recalculated.',
      data: updatedInvoice,
    });
  } catch (error) {
    next(error);
  }
}

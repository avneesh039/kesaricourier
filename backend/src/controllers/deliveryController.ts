// backend/src/controllers/deliveryController.ts
import { Request, Response, NextFunction } from 'express';
import { z } from 'zod';
import prisma from '../config/database';

export async function getMyRunsheets(req: Request, res: Response, next: NextFunction) {
  try {
    const userId = req.user!.userId;
    const runsheets = await prisma.runsheet.findMany({
      where: { delivery_person_id: userId },
      orderBy: { created_at: 'desc' },
      include: {
        branch: { select: { branch_name: true, phone: true } },
        items: {
          include: {
            consignment: {
              include: { destination: true },
            },
          },
        },
      },
    });

    res.json({ success: true, data: runsheets });
  } catch (error) {
    next(error);
  }
}

export async function submitPod(req: Request, res: Response, next: NextFunction) {
  try {
    const schema = z.object({
      runsheetItemId: z.number(),
      recipientName: z.string().min(2),
      recipientRelation: z.string().default('Self'),
      signatureUrl: z.string().optional(),
      photoUrl: z.string().optional(),
    });

    const body = schema.parse(req.body);

    const item = await prisma.runsheetItem.findUnique({
      where: { id: body.runsheetItemId },
      include: { runsheet: true },
    });

    if (!item) {
      return res.status(404).json({ success: false, error: { message: 'Runsheet item not found.' } });
    }

    // Verify authorized delivery person or admin
    if (
      req.user?.role !== 'superadmin' &&
      req.user?.role !== 'agency_admin' &&
      item.runsheet.delivery_person_id !== req.user?.userId
    ) {
      return res.status(403).json({ success: false, error: { message: 'Unauthorized delivery personnel.' } });
    }

    const deliveredAt = new Date();

    // Update Runsheet Item
    const updatedItem = await prisma.runsheetItem.update({
      where: { id: body.runsheetItemId },
      data: {
        status: 'delivered',
        recipient_name: body.recipientName,
        recipient_relation: body.recipientRelation,
        signature_url: body.signatureUrl || null,
        photo_url: body.photoUrl || null,
        delivered_at: deliveredAt,
      },
    });

    // Update Consignment status
    await prisma.consignment.update({
      where: { id: item.consignment_id },
      data: { status: 'delivered' },
    });

    res.json({
      success: true,
      message: 'Proof of Delivery (POD) successfully submitted.',
      data: updatedItem,
    });
  } catch (error) {
    next(error);
  }
}

export async function recordFailureAttempt(req: Request, res: Response, next: NextFunction) {
  try {
    const schema = z.object({
      runsheetItemId: z.number(),
      failureReason: z.string().min(3),
    });

    const body = schema.parse(req.body);

    const item = await prisma.runsheetItem.findUnique({
      where: { id: body.runsheetItemId },
    });

    if (!item) {
      return res.status(404).json({ success: false, error: { message: 'Runsheet item not found.' } });
    }

    const updated = await prisma.runsheetItem.update({
      where: { id: body.runsheetItemId },
      data: {
        status: 'attempted',
        attempt_count: { increment: 1 },
        failure_reason: body.failureReason,
      },
    });

    res.json({
      success: true,
      message: 'Delivery attempt recorded.',
      data: updated,
    });
  } catch (error) {
    next(error);
  }
}

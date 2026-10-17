<?php
// src/Views/invoices/print.php - Printable A4 GST Tax Invoice Report
// Standard Indian GST Compliance for Courier & Cargo Logistics (SAC 996812)
$agencyName = $invoice['tenant_trade_name'] ?: $invoice['tenant_name'] ?: 'KESARI EXPRESS';
$agencyAddress = $invoice['tenant_address'] ?: 'Pandri Main Road, Raipur, Chhattisgarh';
$agencyGstin = $invoice['tenant_gstin'] ?: '22AAACK1234F1Z5';
$agencyPan = $invoice['tenant_pan'] ?: substr($agencyGstin, 2, 10);
$agencyState = $invoice['tenant_state'] ?: 'Chhattisgarh';
$agencyStateCode = substr($agencyGstin, 0, 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tax Invoice - <?= htmlspecialchars($invoice['invoice_no']) ?></title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif;
            background-color: #f1f3f4;
            color: #111;
            font-size: 11px;
            line-height: 1.3;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .no-print-toolbar {
            max-width: 800px;
            margin: 20px auto 12px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.12);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 500;
            border-radius: 4px;
            text-decoration: none;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: #1a73e8;
            color: #ffffff;
        }
        .btn-secondary {
            background: #e8eaed;
            color: #3c4043;
        }

        /* Standard A4 Printable Invoice Sheet */
        .invoice-sheet {
            width: 210mm;
            min-height: 297mm;
            margin: 0 auto 30px;
            background: #ffffff;
            border: 1px solid #d1d5db;
            padding: 14mm 16mm;
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
            position: relative;
        }

        .invoice-border-box {
            border: 1.5px solid #000;
        }

        .table-grid {
            width: 100%;
            border-collapse: collapse;
        }

        .table-grid th, .table-grid td {
            border: 1px solid #000;
            padding: 5px 7px;
        }

        .table-grid th {
            background: #f1f3f4;
            font-weight: 700;
            text-align: left;
            font-size: 10.5px;
        }

        @media print {
            body {
                background: none;
                margin: 0;
                padding: 0;
            }

            .no-print-toolbar {
                display: none !important;
            }

            .invoice-sheet {
                width: 100% !important;
                margin: 0 !important;
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
            }

            @page {
                size: A4 portrait;
                margin: 10mm;
            }
        }
    </style>
</head>
<body>

    <!-- On-Screen Action Bar -->
    <div class="no-print-toolbar">
        <div>
            <span style="font-size:15px; font-weight:700;">🧾 GST Tax Invoice Report</span>
            <div style="font-size:12px; color:#5f6368; margin-top:2px;">
                Invoice #<strong><?= htmlspecialchars($invoice['invoice_no']) ?></strong> • Client: <strong><?= htmlspecialchars($invoice['client_name']) ?></strong>
            </div>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="/invoices/<?= $invoice['id'] ?>" class="btn btn-secondary">← Back to Dossier</a>
            <button onclick="window.print()" class="btn btn-primary">🖨️ Print Tax Invoice (A4)</button>
        </div>
    </div>

    <!-- Printable Invoice Sheet -->
    <div class="invoice-sheet">
        <div class="invoice-border-box">

            <!-- Title & Copy Header -->
            <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1.5px solid #000; padding:6px 12px; background:#f8f9fa;">
                <div style="font-size:9px; font-weight:700; text-transform:uppercase; letter-spacing:1px;">
                    GST REGISTRATION: <strong><?= htmlspecialchars($agencyGstin) ?></strong>
                </div>
                <div style="font-size:16px; font-weight:900; letter-spacing:1px; text-transform:uppercase;">
                    TAX INVOICE
                </div>
                <div style="font-size:9px; font-weight:700; text-transform:uppercase; color:#444;">
                    Original for Recipient
                </div>
            </div>

            <!-- Agency Header & Invoice Meta Split -->
            <div style="display:grid; grid-template-columns: 1.5fr 1fr; border-bottom:1.5px solid #000;">
                <!-- Agency Details -->
                <div style="padding:10px 14px; border-right:1px solid #000;">
                    <div style="font-size:17px; font-weight:900; text-transform:uppercase; color:#000;">
                        <?= htmlspecialchars($agencyName) ?>
                    </div>
                    <div style="font-size:10px; color:#333; margin-top:3px; line-height:1.35;">
                        <?= htmlspecialchars($agencyAddress) ?><br>
                        Phone: <strong><?= htmlspecialchars($invoice['tenant_phone'] ?: '+91 98271 23456') ?></strong> | Email: <?= htmlspecialchars($invoice['tenant_email'] ?: 'billing@kesari.in') ?>
                    </div>
                    <div style="margin-top:6px; font-size:10px;">
                        <strong>PAN:</strong> <?= htmlspecialchars($agencyPan) ?> | 
                        <strong>State:</strong> <?= htmlspecialchars($agencyState) ?> (Code: <?= htmlspecialchars($agencyStateCode) ?>) |
                        <strong>Branch:</strong> #<?= htmlspecialchars($invoice['agency_code']) ?>
                    </div>
                </div>

                <!-- Invoice Details -->
                <div style="padding:10px 14px; font-size:10px; display:flex; flex-direction:column; justify-content:center; gap:4px;">
                    <div><strong>Invoice No:</strong> <span style="font-family:monospace; font-size:13px; font-weight:800;"><?= htmlspecialchars($invoice['invoice_no']) ?></span></div>
                    <div><strong>Invoice Date:</strong> <?= date('d-M-Y', strtotime($invoice['invoice_date'])) ?></div>
                    <div><strong>Billing Period:</strong> <?= date('d/m/y', strtotime($invoice['period_from'])) ?> to <?= date('d/m/y', strtotime($invoice['period_to'])) ?></div>
                    <div><strong>Payment Due Date:</strong> <?= date('d-M-Y', strtotime($invoice['due_date'])) ?></div>
                    <div><strong>Reverse Charge:</strong> No</div>
                </div>
            </div>

            <!-- Billed To (Client Details) -->
            <div style="padding:10px 14px; border-bottom:1.5px solid #000; background:#fafafa;">
                <div style="font-size:9px; font-weight:800; text-transform:uppercase; color:#555; margin-bottom:2px;">
                    Details of Receiver (Billed To):
                </div>
                <div style="display:grid; grid-template-columns: 1.5fr 1fr; gap:16px;">
                    <div>
                        <div style="font-size:13px; font-weight:800; text-transform:uppercase;">
                            <?= htmlspecialchars($invoice['client_name']) ?>
                        </div>
                        <div style="font-size:10px; line-height:1.35; margin-top:2px;">
                            <?= htmlspecialchars($invoice['client_address']) ?>, <?= htmlspecialchars($invoice['client_city']) ?>, <?= htmlspecialchars($invoice['client_state']) ?> - <?= htmlspecialchars($invoice['client_pincode']) ?>
                        </div>
                        <div style="margin-top:2px; font-size:10px;">
                            Contact: <?= htmlspecialchars($invoice['client_phone']) ?> | <?= htmlspecialchars($invoice['client_email']) ?>
                        </div>
                    </div>
                    <div style="font-size:10px; display:flex; flex-direction:column; justify-content:center; gap:3px;">
                        <div><strong>Client Code:</strong> #<?= htmlspecialchars($invoice['client_code']) ?></div>
                        <div><strong>Client GSTIN:</strong> <code style="font-size:11px;"><?= htmlspecialchars($invoice['client_gstin'] ?: 'Unregistered') ?></code></div>
                        <div><strong>State & Code:</strong> <?= htmlspecialchars($invoice['client_state']) ?> (<?= substr($invoice['client_gstin'] ?: '22', 0, 2) ?>)</div>
                        <div><strong>Place of Supply:</strong> <?= htmlspecialchars($invoice['client_state']) ?></div>
                    </div>
                </div>
            </div>

            <!-- Consignment Line Items Table -->
            <table class="table-grid" style="border:none;">
                <thead>
                    <tr>
                        <th style="width:30px; text-align:center;">#</th>
                        <th>Booking Date</th>
                        <th>AWB / Docket No</th>
                        <th>Destination</th>
                        <th>Mode</th>
                        <th style="text-align:center;">Pcs</th>
                        <th style="text-align:right;">Weight (kg)</th>
                        <th style="text-align:right;">Freight (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $sn = 1;
                    $totalPcs = 0;
                    $totalWt = 0.0;
                    foreach ($items as $item): 
                        $totalPcs += $item['pieces'];
                        $totalWt += (float)$item['billable_weight'];
                    ?>
                        <tr>
                            <td style="text-align:center;"><?= $sn++ ?></td>
                            <td><?= date('d-m-Y', strtotime($item['booking_date'])) ?></td>
                            <td style="font-family:monospace; font-weight:700;">
                                <?= htmlspecialchars($item['consignment_no']) ?>
                            </td>
                            <td><?= htmlspecialchars($item['destination_name']) ?> (<?= htmlspecialchars($item['dest_code']) ?>)</td>
                            <td><?= htmlspecialchars($item['mode']) ?></td>
                            <td style="text-align:center;"><?= $item['pieces'] ?></td>
                            <td style="text-align:right;"><?= number_format($item['billable_weight'], 2) ?></td>
                            <td style="text-align:right; font-weight:600;"><?= number_format($item['amount'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="background:#f8f9fa; font-weight:700;">
                        <td colspan="5" style="text-align:right;">Total Volume & Freight:</td>
                        <td style="text-align:center;"><?= $totalPcs ?> Pcs</td>
                        <td style="text-align:right;"><?= number_format($totalWt, 2) ?> kg</td>
                        <td style="text-align:right; font-size:12px;">₹<?= number_format($invoice['subtotal_amount'], 2) ?></td>
                    </tr>
                </tfoot>
            </table>

            <!-- GST Calculation & Bank Details Grid -->
            <div style="display:grid; grid-template-columns: 1.3fr 1fr; border-top:1.5px solid #000;">
                <!-- Banking Details & Amount in Words -->
                <div style="padding:10px 14px; border-right:1px solid #000; display:flex; flex-direction:column; justify-content:space-between;">
                    <div>
                        <div style="font-size:10px; font-weight:800; text-transform:uppercase; margin-bottom:4px;">
                            Bank Remittance Details (For NEFT / RTGS):
                        </div>
                        <div style="font-size:10px; line-height:1.4;">
                            Bank Name: <strong><?= htmlspecialchars($invoice['bank_name'] ?: 'HDFC Bank Ltd') ?></strong><br>
                            Account No: <strong style="font-family:monospace; font-size:11px;"><?= htmlspecialchars($invoice['account_no'] ?: '50200012345678') ?></strong><br>
                            IFSC Code: <strong style="font-family:monospace;"><?= htmlspecialchars($invoice['ifsc_code'] ?: 'HDFC0001234') ?></strong><br>
                            Branch: <?= htmlspecialchars($invoice['branch_name'] ?: 'Pandri Branch, Raipur') ?><br>
                            Beneficiary: <strong><?= htmlspecialchars($invoice['account_holder_name'] ?: $agencyName) ?></strong>
                        </div>
                    </div>

                    <div style="margin-top:12px; padding-top:8px; border-top:1px dashed #ccc;">
                        <div style="font-size:9px; font-weight:700; color:#444; text-transform:uppercase;">Amount in Words:</div>
                        <div style="font-size:11px; font-weight:800; color:#111; margin-top:2px;">
                            <?= htmlspecialchars($amountInWords) ?>
                        </div>
                    </div>
                </div>

                <!-- Financial Ledger Breakdown -->
                <div>
                    <table style="width:100%; border-collapse:collapse; font-size:10.5px;">
                        <tr>
                            <td style="padding:4px 8px; border-bottom:1px solid #ccc;">Freight Subtotal:</td>
                            <td style="padding:4px 8px; border-bottom:1px solid #ccc; text-align:right;">₹<?= number_format($invoice['subtotal_amount'], 2) ?></td>
                        </tr>
                        <?php if ($invoice['fuel_surcharge_amount'] > 0): ?>
                            <tr>
                                <td style="padding:4px 8px; border-bottom:1px solid #ccc;">Fuel Surcharge (<?= $invoice['fuel_surcharge_percent'] ?>%):</td>
                                <td style="padding:4px 8px; border-bottom:1px solid #ccc; text-align:right;">+ ₹<?= number_format($invoice['fuel_surcharge_amount'], 2) ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($invoice['docket_charge_amount'] > 0): ?>
                            <tr>
                                <td style="padding:4px 8px; border-bottom:1px solid #ccc;">Docket Charges:</td>
                                <td style="padding:4px 8px; border-bottom:1px solid #ccc; text-align:right;">+ ₹<?= number_format($invoice['docket_charge_amount'], 2) ?></td>
                            </tr>
                        <?php endif; ?>
                        <tr style="font-weight:700; background:#f8f9fa;">
                            <td style="padding:4px 8px; border-bottom:1px solid #000;">Taxable Value (SAC 996812):</td>
                            <td style="padding:4px 8px; border-bottom:1px solid #000; text-align:right;">₹<?= number_format($invoice['taxable_amount'], 2) ?></td>
                        </tr>

                        <!-- GST Split -->
                        <?php if ($invoice['cgst_amount'] > 0 || $invoice['sgst_amount'] > 0): ?>
                            <tr>
                                <td style="padding:4px 8px; border-bottom:1px solid #ccc;">CGST (<?= $invoice['cgst_rate'] ?>%):</td>
                                <td style="padding:4px 8px; border-bottom:1px solid #ccc; text-align:right;">₹<?= number_format($invoice['cgst_amount'], 2) ?></td>
                            </tr>
                            <tr>
                                <td style="padding:4px 8px; border-bottom:1px solid #ccc;">SGST (<?= $invoice['sgst_rate'] ?>%):</td>
                                <td style="padding:4px 8px; border-bottom:1px solid #ccc; text-align:right;">₹<?= number_format($invoice['sgst_amount'], 2) ?></td>
                            </tr>
                        <?php endif; ?>

                        <?php if ($invoice['igst_amount'] > 0): ?>
                            <tr>
                                <td style="padding:4px 8px; border-bottom:1px solid #ccc;">IGST (<?= $invoice['igst_rate'] ?>%):</td>
                                <td style="padding:4px 8px; border-bottom:1px solid #ccc; text-align:right;">₹<?= number_format($invoice['igst_amount'], 2) ?></td>
                            </tr>
                        <?php endif; ?>

                        <?php if ($invoice['round_off'] != 0): ?>
                            <tr>
                                <td style="padding:3px 8px; border-bottom:1px solid #ccc; font-size:9.5px; color:#555;">Round Off:</td>
                                <td style="padding:3px 8px; border-bottom:1px solid #ccc; text-align:right; font-size:9.5px;"><?= $invoice['round_off'] > 0 ? '+' : '' ?><?= number_format($invoice['round_off'], 2) ?></td>
                            </tr>
                        <?php endif; ?>

                        <tr style="font-size:13px; font-weight:900; background:#e8f0fe;">
                            <td style="padding:6px 8px; border-top:1.5px solid #000;">Total Amount (₹):</td>
                            <td style="padding:6px 8px; border-top:1.5px solid #000; text-align:right; color:#1a73e8;">₹<?= number_format($invoice['grand_total'], 2) ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Footer: QR Verification, Terms & Authorized Signatory -->
            <div style="display:grid; grid-template-columns: 100px 1.5fr 1fr; border-top:1.5px solid #000; align-items:center; padding:10px 14px;">
                <div style="text-align:center;">
                    <?= $qrSvg ?>
                    <div style="font-size:7px; font-weight:700; margin-top:2px;">SCAN TO VERIFY</div>
                </div>

                <div style="font-size:8px; color:#444; padding:0 14px; line-height:1.35;">
                    <strong>Terms & Conditions:</strong><br>
                    1. Payment is strictly due within <?= date('d-M-Y', strtotime($invoice['due_date'])) ?>.<br>
                    2. Interest @ 18% p.a. will be levied on overdue invoices.<br>
                    3. Cheques to be drawn in favour of '<strong><?= htmlspecialchars($agencyName) ?></strong>'.<br>
                    4. Subject to local jurisdiction only.
                </div>

                <div style="text-align:center; padding-top:20px;">
                    <div style="font-size:10px; font-weight:800;">For <?= htmlspecialchars($agencyName) ?></div>
                    <div style="height:36px;"></div>
                    <div style="border-top:1px solid #000; font-size:9px; font-weight:700; display:inline-block; padding:2px 14px;">
                        Authorized Signatory
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>

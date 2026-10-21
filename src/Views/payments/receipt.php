<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Receipt #<?= htmlspecialchars($payment['receipt_no']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f0f0f0;
            color: #333;
        }
        .receipt-card {
            max-width: 650px;
            margin: 20px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-top: 5px solid #0D7378;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .logo-title h2 {
            margin: 0;
            color: #0D7378;
            font-size: 22px;
        }
        .logo-title p {
            margin: 3px 0 0;
            font-size: 12px;
            color: #666;
        }
        .receipt-badge {
            text-align: right;
        }
        .badge {
            background: #e6f4ea;
            color: #137333;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
            display: inline-block;
        }
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            font-size: 13px;
            margin-bottom: 20px;
        }
        .amount-box {
            background: #f8fafc;
            border: 2px dashed #0D7378;
            padding: 15px 20px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
        }
        .amount-box .val {
            font-size: 28px;
            font-weight: bold;
            color: #0D7378;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin-bottom: 25px;
        }
        .table th, .table td {
            padding: 8px 12px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }
        .table th {
            background: #f8f9fa;
        }
        .footer {
            border-top: 1px solid #eee;
            padding-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            font-size: 11px;
            color: #777;
        }
        .sign {
            text-align: center;
            border-top: 1px solid #333;
            width: 160px;
            padding-top: 4px;
        }
        @media print {
            body { background: #fff; padding: 0; }
            .receipt-card { box-shadow: none; border: 1px solid #ccc; margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div style="text-align:center; margin-bottom:10px;" class="no-print">
    <button onclick="window.print()" style="padding:8px 20px; background:#0D7378; color:#fff; border:none; border-radius:4px; font-weight:bold; cursor:pointer;">
        🖨️ Print Receipt Voucher
    </button>
</div>

<div class="receipt-card">
    <div class="header">
        <div class="logo-title">
            <h2>KESARI COURIER & LOGISTICS</h2>
            <p><?= htmlspecialchars($tenant['name'] ?? 'Branch Operations') ?> (Branch #<?= htmlspecialchars($tenant['agency_code'] ?? '26') ?>)</p>
            <p><?= htmlspecialchars($tenant['address'] ?? '') ?> • Phone: <?= htmlspecialchars($tenant['phone'] ?? '') ?></p>
        </div>
        <div class="receipt-badge">
            <span class="badge">OFFICIAL PAYMENT RECEIPT</span>
            <div style="font-size:12px; color:#555; margin-top:6px;">
                Receipt: <strong><?= htmlspecialchars($payment['receipt_no']) ?></strong><br>
                Date: <?= date('d-M-Y', strtotime($payment['payment_date'])) ?>
            </div>
        </div>
    </div>

    <div class="amount-box">
        <div style="font-size:12px; color:#666; margin-bottom:4px;">TOTAL AMOUNT RECEIVED</div>
        <div class="val">₹ <?= number_format($payment['amount'], 2) ?></div>
        <div style="font-size:11px; color:#888; margin-top:4px;">Via <?= htmlspecialchars($payment['payment_mode']) ?> (Ref: <?= htmlspecialchars($payment['reference_no'] ?: 'Direct Clearance') ?>)</div>
    </div>

    <div class="details-grid">
        <div>
            <strong>Received From (Client):</strong><br>
            <?= htmlspecialchars($payment['client_name']) ?> (Code: #<?= htmlspecialchars($payment['client_code']) ?>)<br>
            <?= nl2br(htmlspecialchars($payment['client_address'] ?? '')) ?><br>
            GSTIN: <?= htmlspecialchars($payment['client_gstin'] ?: 'Unregistered') ?>
        </div>
        <div>
            <strong>Applied Against Invoice:</strong><br>
            Invoice No: <strong>#<?= htmlspecialchars($payment['invoice_no']) ?></strong><br>
            Invoice Date: <?= date('d-M-Y', strtotime($payment['invoice_date'])) ?><br>
            Invoice Grand Total: ₹<?= number_format($payment['grand_total'], 2) ?><br>
            Total Paid To Date: ₹<?= number_format($payment['total_invoice_paid'], 2) ?>
        </div>
    </div>

    <?php if (!empty($payment['notes'])): ?>
        <div style="background:#f8f9fa; padding:10px 14px; border-radius:4px; font-size:12px; margin-bottom:20px;">
            <strong>Remarks / Notes:</strong> <?= htmlspecialchars($payment['notes']) ?>
        </div>
    <?php endif; ?>

    <div class="footer">
        <div>
            This is a computer-generated official receipt voucher.<br>
            Generated on <?= date('d-M-Y H:i:s') ?>
        </div>
        <div class="sign">
            Authorized Cashier / Stamp
        </div>
    </div>
</div>

</body>
</html>

<?php
// src/Views/print/label-batch.php - 4x6" Batch Thermal Label Template
// Renders consecutive 4x6" labels with CSS page-breaks for thermal roll printers
$labelCount = count($labels);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Batch Print (<?= $labelCount ?> Labels) - 4x6" Thermal</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f1f3f4;
            color: #000;
            line-height: 1.2;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .preview-toolbar {
            max-width: 600px;
            margin: 20px auto 16px;
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
            font-size: 14px;
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
        .btn-primary:hover {
            background: #1557b0;
        }

        .btn-secondary {
            background: #e8eaed;
            color: #3c4043;
        }

        /* 4x6 Thermal Label Container */
        .label-container {
            width: 4in;
            height: 6in;
            margin: 0 auto 30px;
            background: #ffffff;
            border: 2px solid #000000;
            padding: 0.12in;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            page-break-inside: avoid;
            overflow: hidden;
        }

        .header-section {
            border-bottom: 2px solid #000;
            padding-bottom: 4px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .agency-brand {
            font-size: 15px;
            font-weight: 900;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .agency-meta {
            font-size: 9px;
            color: #333;
            margin-top: 2px;
        }

        .mode-badges {
            display: flex;
            gap: 4px;
            align-items: center;
        }

        .badge {
            border: 2px solid #000;
            font-size: 11px;
            font-weight: 900;
            padding: 2px 6px;
            border-radius: 2px;
            text-transform: uppercase;
        }

        .badge-dark {
            background: #000;
            color: #fff;
        }

        .routing-section {
            border-bottom: 2px solid #000;
            padding: 6px 0;
            display: grid;
            grid-template-columns: 1.6fr 1.2fr;
            gap: 8px;
            align-items: center;
        }

        .dest-city-title {
            font-size: 8px;
            text-transform: uppercase;
            font-weight: 700;
            color: #333;
        }

        .dest-city-code {
            font-size: 22px;
            font-weight: 900;
            line-height: 1.1;
            letter-spacing: 0.5px;
        }

        .dest-city-name {
            font-size: 11px;
            font-weight: 700;
        }

        .pincode-card {
            border: 2px solid #000;
            border-radius: 4px;
            text-align: center;
            padding: 4px;
            background: #fff;
        }

        .pincode-label {
            font-size: 8px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .pincode-val {
            font-size: 20px;
            font-weight: 900;
            letter-spacing: 2px;
            font-family: monospace, monospace;
        }

        .barcode-section {
            border-bottom: 2px solid #000;
            padding: 6px 0;
            text-align: center;
        }

        .address-section {
            border-bottom: 2px solid #000;
            padding: 6px 0;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            font-size: 10px;
        }

        .address-box {
            line-height: 1.25;
        }

        .address-box.consignee {
            border-left: 1.5px dashed #000;
            padding-left: 8px;
        }

        .box-title {
            font-size: 8px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #000;
            display: inline-block;
            margin-bottom: 3px;
        }

        .party-name {
            font-weight: 800;
            font-size: 11px;
        }

        .package-section {
            border-bottom: 2px solid #000;
            padding: 4px 0;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 4px;
            text-align: center;
        }

        .spec-item {
            border-right: 1px solid #ccc;
            padding: 2px 4px;
        }
        .spec-item:last-child {
            border-right: none;
        }

        .spec-title {
            font-size: 7.5px;
            text-transform: uppercase;
            font-weight: 700;
            color: #444;
        }

        .spec-val {
            font-size: 12px;
            font-weight: 900;
            margin-top: 1px;
        }

        .footer-section {
            display: grid;
            grid-template-columns: 1.1in 1fr;
            gap: 8px;
            align-items: center;
            padding-top: 4px;
        }

        .qr-box {
            text-align: center;
        }

        .footer-text {
            font-size: 8px;
            line-height: 1.3;
        }

        .footer-disclaimer {
            font-size: 7px;
            color: #444;
            margin-top: 4px;
            line-height: 1.2;
        }

        @media print {
            body {
                background: none;
                margin: 0;
                padding: 0;
            }

            .preview-toolbar {
                display: none !important;
            }

            .label-container {
                width: 4in !important;
                height: 6in !important;
                margin: 0 !important;
                border: 2px solid #000 !important;
                box-shadow: none !important;
                page-break-after: always;
                break-after: page;
            }

            @page {
                size: 4in 6in;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <!-- On-Screen Preview Toolbar -->
    <div class="preview-toolbar">
        <div>
            <span style="font-size:15px; font-weight:700;">🏷️ Batch Thermal Print (<?= $labelCount ?> Labels)</span>
            <div style="font-size:12px; color:#5f6368; margin-top:2px;">
                Ready for 4x6" roll thermal printing. Continuous feed with auto page-breaks.
            </div>
        </div>
        <div style="display:flex; gap:10px;">
            <a href="/print-labels" class="btn btn-secondary">← Back to Print Hub</a>
            <button onclick="window.print()" class="btn btn-primary">🖨️ Print All (<?= $labelCount ?> Labels)</button>
        </div>
    </div>

    <!-- Render Each 4x6 Thermal Label -->
    <?php foreach ($labels as $item): 
        $c = $item['consignment'];
        $barcodeSvg = $item['barcodeSvg'];
        $qrSvg = $item['qrSvg'];
        $agencyName = $c['tenant_trade_name'] ?: $c['tenant_name'] ?: 'KESARI EXPRESS';
        $agencyCode = $c['agency_code'] ?: 'KSR-01';
        $agencyPhone = $c['tenant_phone'] ?: '+91 98271 23456';
        $destPincode = $c['dest_pincode'] ?: '000000';
    ?>
    <div class="label-container">
        
        <!-- Header -->
        <div class="header-section">
            <div>
                <div class="agency-brand"><?= htmlspecialchars($agencyName) ?></div>
                <div class="agency-meta">
                    Branch: <strong><?= htmlspecialchars($agencyCode) ?></strong> | Helpline: <?= htmlspecialchars($agencyPhone) ?> | Date: <?= date('d-M-Y', strtotime($c['booking_date'])) ?>
                </div>
            </div>
            <div class="mode-badges">
                <span class="badge badge-dark"><?= strtoupper(htmlspecialchars($c['send_by'])) ?></span>
                <span class="badge"><?= strtoupper(htmlspecialchars($c['mode'])) ?></span>
            </div>
        </div>

        <!-- Routing -->
        <div class="routing-section">
            <div>
                <div class="dest-city-title">Routing Destination:</div>
                <div class="dest-city-code"><?= htmlspecialchars($c['dest_code']) ?></div>
                <div class="dest-city-name"><?= htmlspecialchars($c['destination_name']) ?>, <?= htmlspecialchars($c['state_name']) ?></div>
                <div style="font-size:9px; font-weight:600; margin-top:2px; color:#333;">Zone: <?= htmlspecialchars($c['zone_name'] ?: 'Standard') ?></div>
            </div>
            <div>
                <div class="pincode-card">
                    <div class="pincode-label">Delivery Pincode</div>
                    <div class="pincode-val"><?= htmlspecialchars($destPincode) ?></div>
                </div>
            </div>
        </div>

        <!-- Barcode -->
        <div class="barcode-section">
            <?= $barcodeSvg ?>
        </div>

        <!-- Addresses -->
        <div class="address-section">
            <div class="address-box">
                <span class="box-title">SENDER / SHIPPER</span>
                <div class="party-name"><?= htmlspecialchars($c['client_name']) ?></div>
                <div>Code: #<?= htmlspecialchars($c['client_code']) ?></div>
                <div>Ph: <?= htmlspecialchars($c['client_phone'] ?: $c['sender_phone'] ?: 'N/A') ?></div>
                <div>GSTIN: <?= htmlspecialchars($c['client_gstin'] ?: 'Unregistered') ?></div>
                <div style="margin-top:2px; font-size:9px; color:#333;">Origin: RAIPUR (CG)</div>
            </div>

            <div class="address-box consignee">
                <span class="box-title">DELIVER TO / CONSIGNEE</span>
                <div class="party-name"><?= htmlspecialchars($c['recipient_name']) ?></div>
                <div>Ph: <strong><?= htmlspecialchars($c['recipient_phone']) ?></strong></div>
                <div style="margin-top:2px; line-height:1.2; word-break:break-word;">
                    <?= htmlspecialchars($c['recipient_address']) ?>
                </div>
                <div style="font-weight:700; margin-top:2px;">
                    PIN: <?= htmlspecialchars($destPincode) ?>
                </div>
            </div>
        </div>

        <!-- Package Specs -->
        <div class="package-section">
            <div class="spec-item">
                <div class="spec-title">Pieces</div>
                <div class="spec-val"><?= $c['pieces'] ?></div>
            </div>
            <div class="spec-item">
                <div class="spec-title">Dead Wt</div>
                <div class="spec-val"><?= number_format($c['dead_weight'], 2) ?> kg</div>
            </div>
            <div class="spec-item">
                <div class="spec-title">Dimensions</div>
                <div class="spec-val" style="font-size:10px; font-weight:700;">
                    <?= ($c['length_cm'] && $c['width_cm'] && $c['height_cm']) ? "{$c['length_cm']}x{$c['width_cm']}x{$c['height_cm']}" : "Standard" ?>
                </div>
            </div>
            <div class="spec-item">
                <div class="spec-title">Billable Wt</div>
                <div class="spec-val" style="border:1px solid #000; border-radius:2px; background:#f0f0f0;">
                    <?= number_format($c['billable_weight'], 2) ?> kg
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-section">
            <div class="qr-box">
                <?= $qrSvg ?>
                <div style="font-size:7px; font-weight:700; margin-top:2px;">SCAN TO TRACK</div>
            </div>
            <div class="footer-text">
                <div><strong>Routing:</strong> RAI &rarr; <?= htmlspecialchars($c['dest_code']) ?></div>
                <div><strong>Booking Ref:</strong> <?= htmlspecialchars($c['consignment_no']) ?></div>
                <?php if ($c['insured_value'] > 0): ?>
                    <div><strong>Declared Value:</strong> ₹<?= number_format($c['insured_value'], 2) ?></div>
                <?php endif; ?>
                <div class="footer-disclaimer">
                    Carrier liability subject to contractual conditions printed on docket receipt. Scan QR code to view transit milestones.
                </div>
            </div>
        </div>

    </div>
    <?php endforeach; ?>

    <?php if ($autoprint): ?>
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                window.print();
            });
        </script>
    <?php endif; ?>

</body>
</html>

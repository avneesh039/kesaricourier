<?php
// src/Views/invoices/show.php - Invoice Dossier & Payment Manager
$pageTitle = 'Invoice #' . $invoice['invoice_no'];
$activeNav = 'invoices';

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

ob_start();
?>

<div class="page-header">
    <div>
        <a href="/invoices" style="font-size:13px; color:var(--text-secondary); text-decoration:none;">← Back to Invoices</a>
        <div style="display:flex; align-items:center; gap:12px; margin-top:6px;">
            <h1 class="page-title" style="margin:0; font-family:monospace;"><?= htmlspecialchars($invoice['invoice_no']) ?></h1>
            <?php if ($invoice['payment_status'] === 'paid'): ?>
                <span class="g-chip chip-success" style="font-size:12px;">Paid in Full</span>
            <?php elseif ($invoice['payment_status'] === 'partially_paid'): ?>
                <span class="g-chip chip-warning" style="font-size:12px;">Partially Paid</span>
            <?php elseif ($invoice['payment_status'] === 'cancelled'): ?>
                <span class="g-chip chip-danger" style="font-size:12px;">Cancelled</span>
            <?php else: ?>
                <span class="g-chip" style="font-size:12px; background:#fce8e6; color:#c5221f;">Payment Due</span>
            <?php endif; ?>
        </div>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="/invoices/<?= $invoice['id'] ?>/print" target="_blank" class="btn btn-primary">
            🖨️ Print GST Tax Invoice (A4)
        </a>
    </div>
</div>

<?php if ($flashSuccess): ?>
    <div style="background:#e6f4ea; border-left:4px solid var(--google-green); color:#137333; padding:12px 18px; border-radius:4px; margin-bottom:20px; font-size:13px;">
        <?= htmlspecialchars($flashSuccess) ?>
    </div>
<?php endif; ?>

<?php if ($flashError): ?>
    <div style="background:#fce8e6; border-left:4px solid var(--google-red); color:#c5221f; padding:12px 18px; border-radius:4px; margin-bottom:20px; font-size:13px;">
        <?= htmlspecialchars($flashError) ?>
    </div>
<?php endif; ?>

<div style="display:grid; grid-template-columns: 2fr 1fr; gap:24px; align-items:start;">
    
    <!-- Left Column: Parties & Itemized Dockets -->
    <div>
        <!-- Parties Card -->
        <div class="g-card" style="margin-bottom:20px;">
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; font-size:13px;">
                <div style="border-right:1px solid #f1f3f4; padding-right:16px;">
                    <span style="font-weight:700; color:var(--google-blue); display:block; margin-bottom:6px;">ISSUED BY (COURIER AGENCY)</span>
                    <div style="font-weight:600; font-size:15px;"><?= htmlspecialchars($invoice['tenant_trade_name'] ?: $invoice['tenant_name']) ?></div>
                    <div style="color:var(--text-secondary); margin-top:4px;">GSTIN: <code><?= htmlspecialchars($invoice['tenant_gstin'] ?: 'Unregistered') ?></code></div>
                    <div style="color:var(--text-secondary); margin-top:2px;">Branch Code: #<?= htmlspecialchars($invoice['agency_code']) ?></div>
                    <div style="color:var(--text-secondary); margin-top:2px;">Phone: <?= htmlspecialchars($invoice['tenant_phone']) ?></div>
                    <div style="color:var(--text-secondary); margin-top:2px;"><?= htmlspecialchars($invoice['tenant_address']) ?></div>
                </div>

                <div>
                    <span style="font-weight:700; color:var(--google-green); display:block; margin-bottom:6px;">BILLED TO (CORPORATE CLIENT)</span>
                    <div style="font-weight:600; font-size:15px;"><?= htmlspecialchars($invoice['client_name']) ?></div>
                    <div style="color:var(--text-secondary); margin-top:4px;">Client Code: #<?= htmlspecialchars($invoice['client_code']) ?></div>
                    <div style="color:var(--text-secondary); margin-top:2px;">GSTIN: <code><?= htmlspecialchars($invoice['client_gstin'] ?: 'Unregistered') ?></code></div>
                    <div style="color:var(--text-secondary); margin-top:2px;">Phone: <?= htmlspecialchars($invoice['client_phone']) ?></div>
                    <div style="color:var(--text-secondary); margin-top:2px;">
                        <?= htmlspecialchars($invoice['client_address']) ?>, <?= htmlspecialchars($invoice['client_city']) ?>, <?= htmlspecialchars($invoice['client_state']) ?> - <?= htmlspecialchars($invoice['client_pincode']) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Line Items Table -->
        <div class="g-card" style="padding:0; overflow:hidden;">
            <div style="padding:14px 20px; border-bottom:1px solid #dadce0; background:#f8f9fa; font-weight:700; font-size:14px;">
                Billed Consignment Dockets (<?= count($items) ?> Shipments)
            </div>
            <table class="g-table">
                <thead>
                    <tr>
                        <th>Booking Date</th>
                        <th>AWB #</th>
                        <th>Destination</th>
                        <th>Mode</th>
                        <th>Pieces</th>
                        <th>Billable Wt</th>
                        <th style="text-align:right;">Freight (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= date('d-M-Y', strtotime($item['booking_date'])) ?></td>
                            <td>
                                <a href="/bookings/<?= $item['consignment_id'] ?>" style="font-family:monospace; font-weight:700; color:var(--google-blue); text-decoration:none;">
                                    <?= htmlspecialchars($item['consignment_no']) ?>
                                </a>
                            </td>
                            <td><?= htmlspecialchars($item['destination_name']) ?> (<?= htmlspecialchars($item['dest_code']) ?>)</td>
                            <td><?= htmlspecialchars($item['mode']) ?></td>
                            <td><?= $item['pieces'] ?></td>
                            <td><?= number_format($item['billable_weight'], 2) ?> kg</td>
                            <td style="text-align:right; font-weight:600;">
                                ₹<?= number_format($item['amount'], 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right Column: GST Ledger & Settlement -->
    <div>
        <!-- Tax Ledger Breakdown -->
        <div class="g-card" style="margin-bottom:20px;">
            <h3 style="font-size:15px; margin-bottom:14px;">Tax Invoice Calculation</h3>

            <div style="font-size:13px; display:flex; flex-direction:column; gap:10px;">
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--text-secondary);">Invoice Date:</span>
                    <strong><?= date('d-M-Y', strtotime($invoice['invoice_date'])) ?></strong>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--text-secondary);">Payment Due:</span>
                    <span><?= date('d-M-Y', strtotime($invoice['due_date'])) ?></span>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--text-secondary);">SAC Code:</span>
                    <span>996812 (Courier Services)</span>
                </div>
                <div style="display:flex; justify-content:space-between; border-top:1px solid #f1f3f4; padding-top:8px;">
                    <span style="color:var(--text-secondary);">Freight Subtotal:</span>
                    <span>₹<?= number_format($invoice['subtotal_amount'], 2) ?></span>
                </div>
                <?php if ($invoice['fuel_surcharge_amount'] > 0): ?>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-secondary);">Fuel Surcharge (<?= $invoice['fuel_surcharge_percent'] ?>%):</span>
                        <span>+ ₹<?= number_format($invoice['fuel_surcharge_amount'], 2) ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($invoice['docket_charge_amount'] > 0): ?>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-secondary);">Docket Fees:</span>
                        <span>+ ₹<?= number_format($invoice['docket_charge_amount'], 2) ?></span>
                    </div>
                <?php endif; ?>
                <div style="display:flex; justify-content:space-between; font-weight:600; border-top:1px solid #f1f3f4; padding-top:8px;">
                    <span>Taxable Value:</span>
                    <span>₹<?= number_format($invoice['taxable_amount'], 2) ?></span>
                </div>

                <!-- GST Split -->
                <?php if ($invoice['cgst_amount'] > 0 || $invoice['sgst_amount'] > 0): ?>
                    <div style="display:flex; justify-content:space-between; color:var(--google-blue);">
                        <span>CGST (<?= $invoice['cgst_rate'] ?>%):</span>
                        <span>+ ₹<?= number_format($invoice['cgst_amount'], 2) ?></span>
                    </div>
                    <div style="display:flex; justify-content:space-between; color:var(--google-blue);">
                        <span>SGST (<?= $invoice['sgst_rate'] ?>%):</span>
                        <span>+ ₹<?= number_format($invoice['sgst_amount'], 2) ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($invoice['igst_amount'] > 0): ?>
                    <div style="display:flex; justify-content:space-between; color:var(--google-blue);">
                        <span>IGST (<?= $invoice['igst_rate'] ?>%):</span>
                        <span>+ ₹<?= number_format($invoice['igst_amount'], 2) ?></span>
                    </div>
                <?php endif; ?>

                <?php if ($invoice['round_off'] != 0): ?>
                    <div style="display:flex; justify-content:space-between; font-size:11px; color:var(--text-secondary);">
                        <span>Round Off:</span>
                        <span><?= $invoice['round_off'] > 0 ? '+ ' : '' ?>₹<?= number_format($invoice['round_off'], 2) ?></span>
                    </div>
                <?php endif; ?>

                <div style="display:flex; justify-content:space-between; border-top:2px solid var(--border-color); padding-top:10px; font-size:16px; font-weight:700;">
                    <span>Grand Total:</span>
                    <span style="color:var(--google-blue);">₹<?= number_format($invoice['grand_total'], 2) ?></span>
                </div>
                <div style="font-size:11px; color:var(--text-secondary); margin-top:2px; font-style:italic;">
                    <?= htmlspecialchars($amountInWords) ?>
                </div>
            </div>
        </div>

        <!-- Payment Settlement & Update Form -->
        <div class="g-card">
            <h3 style="font-size:15px; margin-bottom:14px;">Payment Settlement</h3>

            <form method="POST" action="/invoices/<?= $invoice['id'] ?>/payment">
                <div class="form-group">
                    <label class="form-label">Payment Status</label>
                    <select name="payment_status" class="form-control" id="paymentStatusSelect">
                        <option value="pending" <?= $invoice['payment_status'] === 'pending' ? 'selected' : '' ?>>Pending Payment</option>
                        <option value="paid" <?= $invoice['payment_status'] === 'paid' ? 'selected' : '' ?>>Paid (Settled)</option>
                        <option value="partially_paid" <?= $invoice['payment_status'] === 'partially_paid' ? 'selected' : '' ?>>Partially Paid</option>
                        <option value="cancelled" <?= $invoice['payment_status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Amount Paid (₹)</label>
                    <input type="number" step="0.01" name="paid_amount" class="form-control" value="<?= $invoice['paid_amount'] > 0 ? $invoice['paid_amount'] : $invoice['grand_total'] ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Payment Mode</label>
                    <select name="payment_mode" class="form-control">
                        <option value="NEFT/RTGS" <?= $invoice['payment_mode'] === 'NEFT/RTGS' ? 'selected' : '' ?>>NEFT / RTGS</option>
                        <option value="Cheque" <?= $invoice['payment_mode'] === 'Cheque' ? 'selected' : '' ?>>Cheque</option>
                        <option value="UPI" <?= $invoice['payment_mode'] === 'UPI' ? 'selected' : '' ?>>UPI / QR</option>
                        <option value="Cash" <?= $invoice['payment_mode'] === 'Cash' ? 'selected' : '' ?>>Cash</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Transaction / Reference Notes</label>
                    <textarea name="payment_notes" class="form-control" rows="2" placeholder="e.g. UTR / Cheque # or Bank Transfer reference"><?= htmlspecialchars($invoice['payment_notes'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; margin-top:8px;">
                    Update Payment Status
                </button>
            </form>
        </div>
    </div>

</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/src/Views/layout.php';
?>

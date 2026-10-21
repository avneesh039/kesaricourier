<?php
// src/Views/payments/index.php - Payment Receipts and Client Ledger View
$pageTitle = 'Payment Receipts & Client Ledger';
$activeNav = 'payments';

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

ob_start();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">💰 Payment Receipts & Client Ledger</h1>
        <p style="color:var(--text-secondary); font-size:13px; margin-top:4px;">
            Record client remittances against GST Tax Invoices. Track paid, partial, and outstanding balances.
        </p>
    </div>
    <div style="display:flex; gap:10px;">
        <button type="button" class="btn btn-primary" onclick="openPaymentModal()"><?= Icon::svg('plus') ?> Record Payment</button>
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

<!-- Filter & Search Bar -->
<div class="g-card" style="margin-bottom:20px; padding:16px 20px;">
    <form method="GET" action="/payments" style="display:flex; gap:16px; align-items:center; flex-wrap:wrap;">
        <div style="flex:1; min-width:200px;">
            <label class="form-label" style="font-size:12px;">Filter by Client</label>
            <select name="client_id" class="form-control" onchange="this.form.submit()">
                <option value="">All Corporate Clients</option>
                <?php foreach ($clients as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= ($clientId == $c['id']) ? 'selected' : '' ?>>
                        #<?= htmlspecialchars($c['client_code']) ?> - <?= htmlspecialchars($c['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="width:180px;">
            <label class="form-label" style="font-size:12px;">Payment Status</label>
            <select name="status" class="form-control" onchange="this.form.submit()">
                <option value="all" <?= ($statusFilter === 'all') ? 'selected' : '' ?>>All Invoices</option>
                <option value="pending" <?= ($statusFilter === 'pending') ? 'selected' : '' ?>>Pending / Unpaid</option>
                <option value="paid" <?= ($statusFilter === 'paid') ? 'selected' : '' ?>>Fully Paid</option>
            </select>
        </div>

        <div style="align-self:flex-end;">
            <a href="/payments" class="btn btn-outlined" style="height:38px;">Reset Filter</a>
        </div>
    </form>
</div>

<!-- Tabs: Invoices Ledger vs Recent Receipts -->
<div class="g-card" style="padding:0; overflow:hidden;">
    <div style="padding:16px 20px; border-bottom:1px solid #dadce0; display:flex; justify-content:space-between; align-items:center; background:#f8f9fa;">
        <div style="font-weight:700; font-size:14px;">
            Invoices & Outstanding Balances (<?= count($invoices) ?>)
        </div>
        <div style="font-size:12px; color:var(--text-secondary);">
            Branch #<?= htmlspecialchars(TenantContext::getTenant()['agency_code'] ?? '26') ?>
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table class="g-table">
            <thead>
                <tr>
                    <th>Invoice No</th>
                    <th>Date</th>
                    <th>Client (Code)</th>
                    <th style="text-align:right;">Grand Total</th>
                    <th style="text-align:right;">Paid Amount</th>
                    <th style="text-align:right;">Balance Due</th>
                    <th>Status</th>
                    <th style="text-align:right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($invoices)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center; padding:32px; color:var(--text-secondary);">
                            No invoices matching your selection.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($invoices as $inv): ?>
                        <tr>
                            <td>
                                <a href="/invoices/<?= $inv['id'] ?>" style="font-weight:700; font-family:monospace; color:#1a73e8; text-decoration:none;">
                                    <?= htmlspecialchars($inv['invoice_no']) ?>
                                </a>
                            </td>
                            <td><?= date('d-M-Y', strtotime($inv['invoice_date'])) ?></td>
                            <td>
                                <div><strong><?= htmlspecialchars($inv['client_name']) ?></strong></div>
                                <div style="font-size:11px; color:var(--text-secondary);">Code: #<?= htmlspecialchars($inv['client_code']) ?></div>
                            </td>
                            <td style="text-align:right; font-weight:600;">
                                ₹<?= number_format($inv['grand_total'], 2) ?>
                            </td>
                            <td style="text-align:right; color:#137333; font-weight:600;">
                                ₹<?= number_format($inv['paid_amount'] ?? 0, 2) ?>
                            </td>
                            <td style="text-align:right; font-weight:700; color:<?= $inv['balance_due'] > 1 ? '#c5221f' : '#137333' ?>;">
                                ₹<?= number_format(max(0, $inv['balance_due']), 2) ?>
                            </td>
                            <td>
                                <?php if ($inv['payment_status'] === 'paid'): ?>
                                    <span class="g-chip chip-success" style="font-size:11px;">Paid</span>
                                <?php elseif ($inv['payment_status'] === 'partially_paid'): ?>
                                    <span class="g-chip chip-info" style="font-size:11px;">Partial</span>
                                <?php else: ?>
                                    <span class="g-chip chip-danger" style="font-size:11px;">Pending</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:right;">
                                <?php if ($inv['payment_status'] !== 'paid'): ?>
                                    <button type="button" class="btn btn-outlined" style="padding:4px 10px; font-size:11px;" onclick="openPaymentForInvoice(<?= $inv['id'] ?>, '<?= htmlspecialchars($inv['invoice_no']) ?>', <?= max(0, $inv['balance_due']) ?>, '<?= htmlspecialchars($inv['client_name']) ?>')">
                                        + Pay
                                    </button>
                                <?php else: ?>
                                    <span style="font-size:12px; color:#137333;">✓ Cleared</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Recent Payment Receipts Section -->
<?php if (!empty($recentPayments)): ?>
    <div class="g-card" style="margin-top:24px; padding:0; overflow:hidden;">
        <div style="padding:16px 20px; border-bottom:1px solid #dadce0; background:#f8f9fa;">
            <div style="font-weight:700; font-size:14px;">
                Recent Payment Receipts (Vouchers)
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="g-table">
                <thead>
                    <tr>
                        <th>Receipt No</th>
                        <th>Payment Date</th>
                        <th>Invoice No</th>
                        <th>Client</th>
                        <th>Mode</th>
                        <th>Reference / Cheque</th>
                        <th style="text-align:right;">Amount Received</th>
                        <th style="text-align:right;">Print Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentPayments as $rp): ?>
                        <tr>
                            <td><strong style="font-family:monospace;"><?= htmlspecialchars($rp['receipt_no']) ?></strong></td>
                            <td><?= date('d-M-Y', strtotime($rp['payment_date'])) ?></td>
                            <td><?= htmlspecialchars($rp['invoice_no']) ?></td>
                            <td><?= htmlspecialchars($rp['client_name']) ?></td>
                            <td><span class="g-chip" style="font-size:11px;"><?= htmlspecialchars($rp['payment_mode']) ?></span></td>
                            <td><?= htmlspecialchars($rp['reference_no'] ?: '—') ?></td>
                            <td style="text-align:right; font-weight:700; color:#137333;">₹<?= number_format($rp['amount'], 2) ?></td>
                            <td style="text-align:right;">
                                <a href="/payments/<?= $rp['id'] ?>/receipt" target="_blank" class="btn btn-outlined" style="padding:4px 10px; font-size:11px;">
                                    <?= Icon::svg('print', '', 14) ?> Receipt
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<!-- Record Payment Modal Dialog -->
<div id="paymentModal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.5); z-index:99999; justify-content:center; align-items:center;">
    <div style="background:#ffffff; width:90%; max-width:480px; border-radius:12px; padding:24px; box-shadow:0 10px 30px rgba(0,0,0,0.3);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h3 style="font-size:16px; margin:0;" id="modalTitle">Record Invoice Payment</h3>
            <button type="button" onclick="closePaymentModal()" style="background:none; border:none; font-size:20px; cursor:pointer;">✕</button>
        </div>

        <form method="POST" action="/payments">
            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label">Select Invoice *</label>
                <select name="invoice_id" id="modalInvoiceSelect" class="form-control" required onchange="onModalInvoiceChange(this)">
                    <option value="">-- Choose Invoice --</option>
                    <?php foreach ($invoices as $inv): ?>
                        <?php if ($inv['payment_status'] !== 'paid'): ?>
                            <option value="<?= $inv['id'] ?>" data-balance="<?= max(0, $inv['balance_due']) ?>" data-client="<?= htmlspecialchars($inv['client_name']) ?>">
                                #<?= htmlspecialchars($inv['invoice_no']) ?> - <?= htmlspecialchars($inv['client_name']) ?> (Due: ₹<?= number_format(max(0, $inv['balance_due']), 2) ?>)
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label">Payment Amount (₹) *</label>
                <input type="number" step="0.01" name="amount" id="modalAmount" class="form-control" placeholder="0.00" required>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-bottom:14px;">
                <div class="form-group">
                    <label class="form-label">Payment Mode *</label>
                    <select name="payment_mode" class="form-control" required>
                        <option value="Cheque">Cheque</option>
                        <option value="NEFT/RTGS">NEFT / RTGS</option>
                        <option value="UPI">UPI / QR Code</option>
                        <option value="Cash">Cash</option>
                        <option value="NetBanking">Net Banking</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Payment Date *</label>
                    <input type="date" name="payment_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
            </div>

            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label">Reference No / Cheque No / UTR</label>
                <input type="text" name="reference_no" class="form-control" placeholder="e.g. CHQ-928192 or UTR12345678">
            </div>

            <div class="form-group" style="margin-bottom:20px;">
                <label class="form-label">Notes (Optional)</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Received by / bank branch remarks"></textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:10px;">
                <button type="button" class="btn btn-outlined" onclick="closePaymentModal()">Cancel</button>
                <button type="submit" class="btn btn-primary" style="padding:8px 20px;">Confirm & Save Receipt</button>
            </div>
        </form>
    </div>
</div>

<script>
function openPaymentModal() {
    document.getElementById('paymentModal').style.display = 'flex';
}

function closePaymentModal() {
    document.getElementById('paymentModal').style.display = 'none';
}

function onModalInvoiceChange(sel) {
    const opt = sel.options[sel.selectedIndex];
    const bal = opt.getAttribute('data-balance');
    if (bal) {
        document.getElementById('modalAmount').value = parseFloat(bal).toFixed(2);
    }
}

function openPaymentForInvoice(id, invNo, balance, clientName) {
    const sel = document.getElementById('modalInvoiceSelect');
    sel.value = id;
    document.getElementById('modalAmount').value = parseFloat(balance).toFixed(2);
    openPaymentModal();
}
</script>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/src/Views/layout.php';
?>

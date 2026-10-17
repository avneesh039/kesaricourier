<?php
// src/Views/invoices/index.php - B2B GST Invoices List & Billing Overview
$pageTitle = 'GST Invoices & Billing';
$activeNav = 'invoices';

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

ob_start();
?>

<div class="page-header">
    <div>
        <h1 class="page-title" style="display:flex; align-items:center; gap:10px;">
            <span style="color:var(--brand-primary); display:inline-flex;"><?= Icon::svg('invoices', '', 26) ?></span>
            GST Invoices & Billing
        </h1>
        <p style="color:var(--text-secondary); font-size:13px; margin-top:4px;">
            Manage B2B monthly billing, GST tax compliance (SAC 996812), payment statuses, and invoice print reports.
        </p>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="/bank-accounts" class="btn btn-outlined"><?= Icon::svg('bank') ?> Bank Accounts</a>
        <a href="/invoices/new" class="btn btn-primary"><?= Icon::svg('plus') ?> Generate Invoice</a>
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

<!-- Financial KPI Cards -->
<div class="metrics-grid">
    <div class="g-card" style="padding:16px;">
        <span style="font-size:12px; color:var(--text-secondary); text-transform:uppercase; font-weight:600;">Total Billed</span>
        <div style="font-size:24px; font-weight:700; color:#202124; margin-top:4px;">
            ₹<?= number_format($stats['total_billed'] ?? 0, 2) ?>
        </div>
        <div style="font-size:11px; color:var(--text-secondary); margin-top:4px;">
            Across <?= $stats['total_invoices'] ?? 0 ?> issued invoices
        </div>
    </div>

    <div class="g-card" style="padding:16px;">
        <span style="font-size:12px; color:var(--text-secondary); text-transform:uppercase; font-weight:600;">Total Collected</span>
        <div style="font-size:24px; font-weight:700; color:var(--google-green); margin-top:4px;">
            ₹<?= number_format($stats['total_paid'] ?? 0, 2) ?>
        </div>
        <div style="font-size:11px; color:var(--text-secondary); margin-top:4px;">
            Remitted to registered bank accounts
        </div>
    </div>

    <div class="g-card" style="padding:16px;">
        <span style="font-size:12px; color:var(--text-secondary); text-transform:uppercase; font-weight:600;">Outstanding Due</span>
        <div style="font-size:24px; font-weight:700; color:var(--google-red); margin-top:4px;">
            ₹<?= number_format($stats['total_due'] ?? 0, 2) ?>
        </div>
        <div style="font-size:11px; color:var(--text-secondary); margin-top:4px;">
            Pending client settlement
        </div>
    </div>

    <div class="g-card" style="padding:16px;">
        <span style="font-size:12px; color:var(--text-secondary); text-transform:uppercase; font-weight:600;">Settlement Rate</span>
        <div style="font-size:24px; font-weight:700; color:var(--google-blue); margin-top:4px;">
            <?php 
                $billed = (float)($stats['total_billed'] ?? 0);
                $paid = (float)($stats['total_paid'] ?? 0);
                echo $billed > 0 ? round(($paid / $billed) * 100, 1) . '%' : '100%';
            ?>
        </div>
        <div style="font-size:11px; color:var(--text-secondary); margin-top:4px;">
            Collection efficiency
        </div>
    </div>
</div>

<!-- Filters Bar -->
<div class="g-card" style="margin-bottom:20px; padding:16px;">
    <form method="GET" action="/invoices" style="display:grid; grid-template-columns: 2fr 1.5fr 1fr 1fr 1fr auto; gap:12px; align-items:end;">
        <div>
            <label class="form-label">Search Invoice</label>
            <input type="text" name="q" class="form-control" placeholder="Invoice # or client name" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
        </div>
        <div>
            <label class="form-label">Client</label>
            <select name="client_id" class="form-control">
                <option value="">All Corporate Clients</option>
                <?php foreach ($clients as $cl): ?>
                    <option value="<?= $cl['id'] ?>" <?= ($_GET['client_id'] ?? '') == $cl['id'] ? 'selected' : '' ?>>
                        #<?= htmlspecialchars($cl['client_code']) ?> - <?= htmlspecialchars($cl['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="">All Statuses</option>
                <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending Payment</option>
                <option value="paid" <?= ($_GET['status'] ?? '') === 'paid' ? 'selected' : '' ?>>Paid (Settled)</option>
                <option value="partially_paid" <?= ($_GET['status'] ?? '') === 'partially_paid' ? 'selected' : '' ?>>Partially Paid</option>
                <option value="cancelled" <?= ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
        </div>
        <div>
            <label class="form-label">From Date</label>
            <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($_GET['date_from'] ?? '') ?>">
        </div>
        <div>
            <label class="form-label">To Date</label>
            <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($_GET['date_to'] ?? '') ?>">
        </div>
        <div style="display:flex; gap:8px;">
            <button type="submit" class="btn btn-secondary">Filter</button>
            <?php if (!empty($_GET['q']) || !empty($_GET['client_id']) || !empty($_GET['status']) || !empty($_GET['date_from'])): ?>
                <a href="/invoices" class="btn btn-outlined">Reset</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Invoices Table -->
<div class="g-card" style="padding:0; overflow:hidden;">
    <table class="g-table">
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Date</th>
                <th>Corporate Client</th>
                <th>Billing Period</th>
                <th>Dockets</th>
                <th>Taxable Value</th>
                <th>Grand Total</th>
                <th>Status</th>
                <th style="text-align:right;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($invoices)): ?>
                <tr>
                    <td colspan="9" style="text-align:center; padding:36px; color:var(--text-secondary);">
                        No GST invoices found. Click <strong>+ Generate Invoice</strong> to bill unbilled consignments.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($invoices as $inv): ?>
                    <tr>
                        <td>
                            <div style="display:flex; align-items:center; gap:6px;">
                                <a href="/invoices/<?= $inv['id'] ?>" class="awb-badge" style="text-decoration:none; font-weight:700; color:var(--brand-primary);">
                                    <?= htmlspecialchars($inv['invoice_no']) ?>
                                </a>
                                <button type="button" class="copyable" data-copy="<?= htmlspecialchars($inv['invoice_no']) ?>" title="Copy Invoice No" style="background:none; border:none; cursor:pointer; color:var(--text-tertiary); display:inline-flex; align-items:center;">
                                    <?= Icon::svg('copy', '', 14) ?>
                                </button>
                            </div>
                        </td>
                        <td><?= date('d-M-Y', strtotime($inv['invoice_date'])) ?></td>
                        <td>
                            <div style="font-weight:600;"><?= htmlspecialchars($inv['client_name']) ?></div>
                            <div style="font-size:11px; color:var(--text-secondary);">
                                #<?= htmlspecialchars($inv['client_code']) ?> • GSTIN: <?= htmlspecialchars($inv['client_gstin'] ?: 'Unregistered') ?>
                            </div>
                        </td>
                        <td style="font-size:12px; color:var(--text-secondary);">
                            <?= date('d/m/y', strtotime($inv['period_from'])) ?> &rarr; <?= date('d/m/y', strtotime($inv['period_to'])) ?>
                        </td>
                        <td>
                            <strong><?= $inv['consignments_count'] ?></strong> dockets
                        </td>
                        <td>₹<?= number_format($inv['taxable_amount'], 2) ?></td>
                        <td>
                            <strong style="font-size:14px; color:#202124;">₹<?= number_format($inv['grand_total'], 2) ?></strong>
                        </td>
                        <td>
                            <?php if ($inv['payment_status'] === 'paid'): ?>
                                <span class="g-chip chip-success" style="font-size:11px;">Paid</span>
                            <?php elseif ($inv['payment_status'] === 'partially_paid'): ?>
                                <span class="g-chip chip-warning" style="font-size:11px;">Partially Paid</span>
                            <?php elseif ($inv['payment_status'] === 'cancelled'): ?>
                                <span class="g-chip chip-danger" style="font-size:11px;">Cancelled</span>
                            <?php else: ?>
                                <span class="g-chip" style="font-size:11px; background:#fce8e6; color:#c5221f;">Due</span>
                            <?php endif; ?>
                        </td>
                        <td style="text-align:right;">
                            <div style="display:flex; gap:6px; justify-content:flex-end;">
                                <a href="/invoices/<?= $inv['id'] ?>/print" target="_blank" class="btn btn-outlined" style="padding:4px 8px; font-size:11px;" title="Print A4 GST Tax Invoice">
                                    🖨️ Print
                                </a>
                                <a href="/invoices/<?= $inv['id'] ?>" class="btn btn-secondary" style="padding:4px 10px; font-size:11px;">
                                    View
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/src/Views/layout.php';
?>

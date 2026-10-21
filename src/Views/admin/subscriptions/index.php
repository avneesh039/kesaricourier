<?php
// src/Views/admin/subscriptions/index.php - Super Admin Subscription & Platform Fee Command Center
$pageTitle = 'Subscription Plans & Payment Control';
$activeNav = 'subscriptions';

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

ob_start();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">💳 Agency Subscriptions & Payment Control</h1>
        <p style="color:var(--text-secondary); font-size:13px; margin-top:4px;">
            Manage franchise platform license plans (Monthly, Quarterly, Yearly), track received payments vs due balance, and enforce grace period locks.
        </p>
    </div>
    <div style="display:flex; gap:10px;">
        <button type="button" class="btn btn-primary" onclick="openNewPlanModal()">+ Assign New Plan / Renewal</button>
    </div>
</div>

<?php if ($flashSuccess): ?>
    <div style="background:#e6f4ea; border-left:4px solid var(--google-green); color:#137333; padding:12px 18px; border-radius:4px; margin-bottom:20px; font-size:13px;">
        <?= htmlspecialchars($flashSuccess) ?>
    </div>
<?php endif; ?>

<?php if ($flashError): ?>
    <div style="background:#fce8e6; border-left:4px solid var(--google-red); color:#c5221f; padding:12px 18px; border-radius:4px; margin-bottom:20px; font-size:13px;">
        ⚠️ <?= htmlspecialchars($flashError) ?>
    </div>
<?php endif; ?>

<!-- Financial KPI Cards -->
<div class="metrics-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom:20px;">
    <div class="metric-card">
        <div class="metric-label">Total Platform Revenue Billed</div>
        <div class="metric-value">₹<?= number_format($stats['total_billed'], 2) ?></div>
        <div class="metric-sub" style="color:var(--text-secondary);">Subscription contracts issued</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Total Payments Received</div>
        <div class="metric-value" style="color:var(--google-green);">₹<?= number_format($stats['total_received'], 2) ?></div>
        <div class="metric-sub" style="color:var(--google-green);">Collected platform fees</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Outstanding Due Amount</div>
        <div class="metric-value" style="color:<?= $stats['total_due'] > 0 ? 'var(--google-red)' : 'var(--google-green)' ?>;">
            ₹<?= number_format($stats['total_due'], 2) ?>
        </div>
        <div class="metric-sub" style="color:var(--text-secondary);">Pending from agency franchises</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Active Agency Portals</div>
        <div class="metric-value" style="color:var(--brand-primary);"><?= $stats['active_plans'] ?></div>
        <div class="metric-sub" style="color:var(--text-secondary);">Licensed operational hubs</div>
    </div>
</div>

<!-- Subscriptions Table -->
<div class="g-card" style="padding:0; overflow:hidden;">
    <div style="padding:16px 20px; border-bottom:1px solid #dadce0; display:flex; justify-content:space-between; align-items:center;">
        <h3 style="font-size:15px; margin:0;">Franchise Subscription Master Ledger</h3>
        <span class="g-chip chip-info"><?= count($subscriptions) ?> active subscription records</span>
    </div>

    <div class="table-responsive">
        <table class="g-table">
            <thead>
                <tr>
                    <th>Agency & Code</th>
                    <th>Plan & Cycle</th>
                    <th>Validity Period</th>
                    <th>Contract Fee</th>
                    <th>Received</th>
                    <th>Due Amount</th>
                    <th>Payment Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($subscriptions)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center; padding:32px; color:var(--text-secondary);">
                            No subscription records found.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($subscriptions as $s): ?>
                    <?php
                        $daysLeft = null;
                        if (!empty($s['end_date'])) {
                            $end = new DateTime($s['end_date']);
                            $now = new DateTime();
                            $diff = $now->diff($end);
                            $daysLeft = $diff->invert ? -$diff->days : $diff->days;
                        }
                    ?>
                    <tr>
                        <td>
                            <div style="font-weight:600; font-size:14px;"><?= htmlspecialchars($s['agency_name']) ?></div>
                            <div style="font-size:11px; color:var(--text-secondary);">
                                Branch #<?= htmlspecialchars($s['agency_code'] ?? 'N/A') ?> • 
                                <span class="g-chip <?= $s['tenant_status'] === 'active' ? 'chip-success' : 'chip-danger' ?>" style="font-size:10px; padding:1px 6px;">
                                    <?= ucfirst($s['tenant_status']) ?>
                                </span>
                            </div>
                        </td>
                        <td>
                            <div><strong><?= htmlspecialchars($s['plan_name']) ?></strong></div>
                            <span class="g-chip chip-info" style="font-size:11px; text-transform:uppercase;"><?= htmlspecialchars($s['billing_cycle']) ?></span>
                        </td>
                        <td>
                            <div style="font-size:12px;"><?= htmlspecialchars($s['start_date']) ?> to <strong><?= htmlspecialchars($s['end_date']) ?></strong></div>
                            <?php if ($daysLeft !== null): ?>
                                <div style="font-size:11px; color:<?= $daysLeft <= 7 ? 'var(--google-red)' : ($daysLeft <= 30 ? 'var(--google-yellow)' : 'var(--google-green)') ?>;">
                                    <?= $daysLeft >= 0 ? "({$daysLeft} days remaining)" : "⚠️ EXPIRED (" . abs($daysLeft) . " days ago)" ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><strong>₹<?= number_format($s['amount'], 2) ?></strong></td>
                        <td style="color:var(--google-green); font-weight:600;">₹<?= number_format($s['paid_amount'], 2) ?></td>
                        <td style="color:<?= $s['due_amount'] > 0 ? 'var(--google-red)' : 'var(--text-secondary)' ?>; font-weight:600;">
                            ₹<?= number_format($s['due_amount'], 2) ?>
                        </td>
                        <td>
                            <?php if ($s['payment_status'] === 'paid'): ?>
                                <span class="g-chip chip-success">Paid in Full</span>
                            <?php elseif ($s['payment_status'] === 'partial'): ?>
                                <span class="g-chip chip-warning">Partially Paid</span>
                            <?php else: ?>
                                <span class="g-chip chip-danger">Payment Due</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <?php if ($s['due_amount'] > 0): ?>
                                    <button type="button" class="btn btn-primary" style="padding:4px 8px; font-size:11px;" onclick="openPaymentModal(<?= $s['id'] ?>, '<?= htmlspecialchars(addslashes($s['agency_name'])) ?>', <?= $s['due_amount'] ?>)">
                                        + Record Payment
                                    </button>
                                <?php endif; ?>
                                <a href="/admin/agencies/<?= $s['tenant_id'] ?>/edit" class="btn btn-outlined" style="padding:4px 8px; font-size:11px;">Edit Plan</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal 1: Record Subscription Payment -->
<div id="paymentModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div class="g-card" style="width:480px; max-width:90%; background:#fff; border-radius:12px; padding:24px; box-shadow:0 10px 25px rgba(0,0,0,0.2);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h3 style="font-size:16px; margin:0;" id="modalAgencyName">Record Platform Fee Payment</h3>
            <button type="button" onclick="closeModal('paymentModal')" style="background:none; border:none; font-size:20px; cursor:pointer;">&times;</button>
        </div>

        <form method="POST" action="/admin/subscriptions/payment">
            <input type="hidden" name="subscription_id" id="modalSubId" value="">

            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label">Payment Amount (₹) *</label>
                <input type="number" step="100" name="payment_amount" id="modalAmount" class="form-control" required>
            </div>

            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label">Payment Mode *</label>
                <select name="payment_mode" class="form-control" required>
                    <option value="Bank Transfer (NEFT/RTGS)">Bank Transfer (NEFT/RTGS)</option>
                    <option value="UPI / QR Payment">UPI / QR Payment</option>
                    <option value="Cheque">Cheque</option>
                    <option value="Cash Deposit">Cash Deposit</option>
                </select>
            </div>

            <div class="form-group" style="margin-bottom:14px;">
                <label class="form-label">Transaction / UTR Reference No.</label>
                <input type="text" name="reference_no" class="form-control" placeholder="e.g. ICIC20261021001">
            </div>

            <div class="form-group" style="margin-bottom:20px;">
                <label class="form-label">Notes</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Payment remarks"></textarea>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center;">
                <button type="button" class="btn btn-outlined" onclick="closeModal('paymentModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" style="padding:10px 24px;">Confirm & Log Remittance</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal 2: Assign New Plan -->
<div id="newPlanModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; align-items:center; justify-content:center;">
    <div class="g-card" style="width:520px; max-width:90%; background:#fff; border-radius:12px; padding:24px; box-shadow:0 10px 25px rgba(0,0,0,0.2);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h3 style="font-size:16px; margin:0;">Assign Subscription Plan / Renewal</h3>
            <button type="button" onclick="closeModal('newPlanModal')" style="background:none; border:none; font-size:20px; cursor:pointer;">&times;</button>
        </div>

        <form method="POST" action="/admin/subscriptions/new-plan">
            <div class="form-group" style="margin-bottom:12px;">
                <label class="form-label">Select Agency / Franchise *</label>
                <select name="tenant_id" class="form-control" required>
                    <option value="">-- Choose Agency --</option>
                    <?php foreach ($agencies as $ag): ?>
                        <option value="<?= $ag['id'] ?>">
                            <?= htmlspecialchars($ag['name']) ?> (#<?= htmlspecialchars($ag['agency_code'] ?? 'N/A') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="margin-bottom:12px;">
                <label class="form-label">Plan Name *</label>
                <input type="text" name="plan_name" class="form-control" value="Annual Platform License" required>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-bottom:12px;">
                <div class="form-group">
                    <label class="form-label">Cycle *</label>
                    <select name="billing_cycle" class="form-control" required>
                        <option value="monthly">Monthly (30d)</option>
                        <option value="quarterly">Quarterly (90d)</option>
                        <option value="half-yearly">Half-Yearly (180d)</option>
                        <option value="yearly" selected>Yearly (365d)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Total Fee (₹) *</label>
                    <input type="number" step="100" name="amount" class="form-control" value="12000" required>
                </div>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-bottom:12px;">
                <div class="form-group">
                    <label class="form-label">Start Date *</label>
                    <input type="date" name="start_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Expiry Date *</label>
                    <input type="date" name="end_date" class="form-control" value="<?= date('Y-m-d', strtotime('+365 days')) ?>" required>
                </div>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-bottom:20px;">
                <div class="form-group">
                    <label class="form-label">Paid Upfront (₹)</label>
                    <input type="number" step="100" name="paid_amount" class="form-control" value="12000">
                </div>
                <div class="form-group">
                    <label class="form-label">Grace Period (Days)</label>
                    <input type="number" name="grace_period_days" class="form-control" value="7">
                </div>
            </div>

            <div style="display:flex; justify-content:space-between; align-items:center;">
                <button type="button" class="btn btn-outlined" onclick="closeModal('newPlanModal')">Cancel</button>
                <button type="submit" class="btn btn-primary" style="padding:10px 24px;">Activate Subscription</button>
            </div>
        </form>
    </div>
</div>

<script>
function openPaymentModal(subId, agencyName, dueAmount) {
    document.getElementById('modalSubId').value = subId;
    document.getElementById('modalAgencyName').innerText = 'Record Payment: ' + agencyName;
    document.getElementById('modalAmount').value = dueAmount;
    document.getElementById('paymentModal').style.display = 'flex';
}

function openNewPlanModal() {
    document.getElementById('newPlanModal').style.display = 'flex';
}

function closeModal(id) {
    document.getElementById(id).style.display = 'none';
}
</script>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/src/Views/layout.php';
?>

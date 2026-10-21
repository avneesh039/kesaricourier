<?php
// src/Views/admin/agencies/edit.php - Comprehensive Agency Control Panel & Subscription Management
$pageTitle = 'Configure Agency & Subscription | ' . $tenant['name'];
$activeNav = 'agencies';

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

ob_start();
?>

<div class="page-header">
    <div>
        <a href="/admin/agencies" style="font-size:13px; color:var(--text-secondary); text-decoration:none;">← Back to Agency Directory</a>
        <h1 class="page-title" style="margin-top:6px;">🏢 Agency Controls: <?= htmlspecialchars($tenant['name']) ?></h1>
        <p style="color:var(--text-secondary); font-size:13px; margin-top:4px;">
            Branch #<?= htmlspecialchars($tenant['agency_code'] ?? 'Pending') ?> • Subdomain: <code><?= htmlspecialchars($tenant['slug']) ?>.localhost</code>
        </p>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="/?tenant=<?= htmlspecialchars($tenant['slug']) ?>" target="_blank" class="btn btn-outlined">Open Branch Portal ↗</a>
        <form method="POST" action="/admin/agencies/<?= $tenant['id'] ?>/toggle-status" style="display:inline;">
            <button type="submit" class="btn <?= $tenant['status'] === 'active' ? 'btn-outlined' : 'btn-primary' ?>" onclick="return confirm('Change portal access status for <?= htmlspecialchars($tenant['name']) ?>?')">
                <?= $tenant['status'] === 'active' ? '🔒 Suspend Agency Portal' : '✓ Activate Agency Portal' ?>
            </button>
        </form>
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

<div style="display:grid; grid-template-columns: 2fr 1fr; gap:24px;">
    <!-- Left Column: Subscription & Governance Settings -->
    <div>
        <div class="g-card" style="margin-bottom:20px;">
            <h3 style="font-size:15px; margin-bottom:16px; color:var(--brand-primary); display:flex; align-items:center; gap:8px;">
                <?= Icon::svg('receipt-tax') ?> Subscription Plan & Validity Control
            </h3>

            <form method="POST" action="/admin/agencies/<?= $tenant['id'] ?>/edit">
                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Subscription Plan *</label>
                        <select name="plan_type" class="form-control" required>
                            <option value="Monthly" <?= ($tenant['plan_type'] ?? '') === 'Monthly' ? 'selected' : '' ?>>Monthly Plan</option>
                            <option value="Quarterly" <?= ($tenant['plan_type'] ?? '') === 'Quarterly' ? 'selected' : '' ?>>Quarterly Plan</option>
                            <option value="Half-Yearly" <?= ($tenant['plan_type'] ?? '') === 'Half-Yearly' ? 'selected' : '' ?>>Half-Yearly Plan</option>
                            <option value="Yearly" <?= ($tenant['plan_type'] ?? '') === 'Yearly' ? 'selected' : '' ?>>Yearly Plan</option>
                            <option value="Custom" <?= ($tenant['plan_type'] ?? '') === 'Custom' ? 'selected' : '' ?>>Custom / Lifetime Plan</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Plan Fee (₹) *</label>
                        <input type="number" step="100" name="plan_price" class="form-control" value="<?= htmlspecialchars($tenant['plan_price'] ?? 12000.0) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Subscription Start Date *</label>
                        <input type="date" name="validity_start" class="form-control" value="<?= htmlspecialchars($tenant['validity_start'] ?? date('Y-m-d')) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Subscription Expiry Date *</label>
                        <input type="date" name="validity_end" class="form-control" value="<?= htmlspecialchars($tenant['validity_end'] ?? date('Y-m-d', strtotime('+365 days'))) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Grace Period (Days) *</label>
                        <input type="number" name="grace_period_days" class="form-control" value="<?= htmlspecialchars($tenant['grace_period_days'] ?? 7) ?>" required min="0" max="60">
                        <small style="color:var(--text-secondary); font-size:11px;">Agency is locked after expiry + grace days.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Portal Status Switch *</label>
                        <select name="status" class="form-control" required>
                            <option value="active" <?= ($tenant['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active (Full Access)</option>
                            <option value="suspended" <?= ($tenant['status'] ?? '') === 'suspended' ? 'selected' : '' ?>>Suspended (Lock Access)</option>
                            <option value="pending_kyc" <?= ($tenant['status'] ?? '') === 'pending_kyc' ? 'selected' : '' ?>>Pending KYC</option>
                            <option value="rejected" <?= ($tenant['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                        </select>
                    </div>

                    <div class="form-group" style="grid-column:span 2;">
                        <label class="form-label">Custom Brand Accent Color</label>
                        <div style="display:flex; align-items:center; gap:12px;">
                            <input type="color" name="brand_color" class="form-control" value="<?= htmlspecialchars($tenant['brand_color'] ?? '#1a73e8') ?>" style="width:60px; height:38px; padding:2px;">
                            <span style="font-size:12px; color:var(--text-secondary);">Applied across agency portal topbars and print labels</span>
                        </div>
                    </div>
                </div>

                <div style="margin-top:20px; display:flex; justify-content:space-between; align-items:center;">
                    <a href="/admin/agencies" class="btn btn-outlined">Cancel</a>
                    <button type="submit" class="btn btn-primary" style="padding:10px 24px;">Save Subscription Settings</button>
                </div>
            </form>
        </div>

        <!-- Subscription Payment Records -->
        <div class="g-card" style="padding:0; overflow:hidden;">
            <div style="padding:16px 20px; border-bottom:1px solid #dadce0; display:flex; justify-content:space-between; align-items:center;">
                <h3 style="font-size:15px; margin:0;">Platform Subscription Ledger</h3>
                <span class="g-chip chip-info"><?= count($subscriptions ?? []) ?> payments on record</span>
            </div>

            <div class="table-responsive">
                <table class="g-table">
                    <thead>
                        <tr>
                            <th>Plan</th>
                            <th>Cycle</th>
                            <th>Period</th>
                            <th>Fee</th>
                            <th>Paid</th>
                            <th>Due</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($subscriptions)): ?>
                            <tr>
                                <td colspan="7" style="text-align:center; padding:20px; color:var(--text-secondary); font-size:13px;">
                                    No subscription billing records logged yet.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($subscriptions as $s): ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($s['plan_name']) ?></strong></td>
                                    <td><?= ucfirst($s['billing_cycle']) ?></td>
                                    <td style="font-size:12px;">
                                        <?= htmlspecialchars($s['start_date']) ?> to <?= htmlspecialchars($s['end_date']) ?>
                                    </td>
                                    <td>₹<?= number_format($s['amount'], 2) ?></td>
                                    <td style="color:var(--google-green);">₹<?= number_format($s['paid_amount'], 2) ?></td>
                                    <td style="color:<?= $s['due_amount'] > 0 ? 'var(--google-red)' : 'var(--text-secondary)' ?>;">
                                        ₹<?= number_format($s['due_amount'], 2) ?>
                                    </td>
                                    <td>
                                        <?php if ($s['payment_status'] === 'paid'): ?>
                                            <span class="g-chip chip-success" style="font-size:11px;">Paid</span>
                                        <?php elseif ($s['payment_status'] === 'partial'): ?>
                                            <span class="g-chip chip-warning" style="font-size:11px;">Partial</span>
                                        <?php else: ?>
                                            <span class="g-chip chip-danger" style="font-size:11px;">Due</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Security, Credentials & Fast Actions -->
    <div>
        <!-- Reset Password Widget -->
        <div class="g-card" style="margin-bottom:20px;">
            <h3 style="font-size:15px; margin-bottom:14px; color:var(--google-red); display:flex; align-items:center; gap:8px;">
                <?= Icon::svg('lock') ?> Reset Agency Admin Password
            </h3>
            <p style="font-size:12px; color:var(--text-secondary); margin-bottom:14px;">
                Agency Login: <code><?= htmlspecialchars($tenant['email']) ?></code>
            </p>

            <form method="POST" action="/admin/agencies/<?= $tenant['id'] ?>/reset-password">
                <div class="form-group" style="margin-bottom:14px;">
                    <label class="form-label">New Password *</label>
                    <input type="text" name="new_password" class="form-control" placeholder="Enter new password" required minlength="6" value="kesari@<?= random_int(1000, 9999) ?>" style="font-family:monospace; font-weight:700;">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%; padding:10px; font-size:13px; background:#d93025; border-color:#d93025;" onclick="return confirm('Reset password for <?= htmlspecialchars($tenant['name']) ?>?')">
                    Reset & Overwrite Password
                </button>
            </form>
        </div>

        <!-- Quick Summary Box -->
        <div class="g-card">
            <h3 style="font-size:14px; margin-bottom:12px;">Branch Summary</h3>
            <div style="font-size:12px; display:flex; flex-direction:column; gap:10px; color:var(--text-secondary);">
                <div>
                    <strong>Owner / Contact:</strong><br>
                    <?= htmlspecialchars($tenant['contact_person']) ?> (<?= htmlspecialchars($tenant['phone']) ?>)
                </div>
                <div>
                    <strong>Address:</strong><br>
                    <?= htmlspecialchars($tenant['address'] ?? 'N/A') ?>, <?= htmlspecialchars($tenant['city'] ?? '') ?>, <?= htmlspecialchars($tenant['state'] ?? '') ?> - <?= htmlspecialchars($tenant['pincode'] ?? '') ?>
                </div>
                <div>
                    <strong>PAN / GSTIN:</strong><br>
                    PAN: <code><?= htmlspecialchars($tenant['pan_no'] ?? 'N/A') ?></code><br>
                    GSTIN: <code><?= htmlspecialchars($tenant['gstin'] ?? 'Unregistered') ?></code>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/src/Views/layout.php';
?>

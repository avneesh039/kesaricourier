<?php
// src/Views/admin/agencies/index.php - Super Admin Agency Directory & Subscriptions
$pageTitle = 'Agency Tenants & Compliance';
$activeNav = 'agencies';
$flash = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_success']);

ob_start();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Agency Portals & Multi-Tenancy</h1>
        <p style="color:var(--text-secondary); font-size:13px; margin-top:4px;">Manage white-labeled agency subscriptions, validity periods, and account statuses.</p>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="/admin/kyc" class="btn btn-outlined">
            🛡️ KYC Review Queue <?= $counts['pending_kyc'] > 0 ? "({$counts['pending_kyc']})" : '' ?>
        </a>
        <a href="/admin/agencies/new" class="btn btn-primary">+ Create Agency / Franchise</a>
    </div>
</div>

<?php if ($flash): ?>
    <div style="background:#e6f4ea; border-left:4px solid var(--google-green); color:#137333; padding:14px 18px; border-radius:4px; margin-bottom:20px; font-size:14px;">
        <?= htmlspecialchars($flash) ?>
    </div>
<?php endif; ?>

<!-- Filter Tabs -->
<div style="display:flex; gap:8px; margin-bottom:20px; border-bottom:1px solid var(--border-color); padding-bottom:10px;">
    <a href="/admin/agencies?filter=all" class="btn <?= ($filter ?? 'all') === 'all' ? 'btn-primary' : 'btn-outlined' ?>" style="border-radius:20px; padding:4px 14px; font-size:12px;">
        All (<?= $counts['all'] ?>)
    </a>
    <a href="/admin/agencies?filter=active" class="btn <?= ($filter ?? '') === 'active' ? 'btn-primary' : 'btn-outlined' ?>" style="border-radius:20px; padding:4px 14px; font-size:12px;">
        Active (<?= $counts['active'] ?>)
    </a>
    <a href="/admin/agencies?filter=pending_kyc" class="btn <?= ($filter ?? '') === 'pending_kyc' ? 'btn-primary' : 'btn-outlined' ?>" style="border-radius:20px; padding:4px 14px; font-size:12px;">
        Pending KYC (<?= $counts['pending_kyc'] ?>)
    </a>
    <a href="/admin/agencies?filter=expiring_soon" class="btn <?= ($filter ?? '') === 'expiring_soon' ? 'btn-primary' : 'btn-outlined' ?>" style="border-radius:20px; padding:4px 14px; font-size:12px;">
        Expiring (30d) (<?= $counts['expiring_soon'] ?>)
    </a>
    <a href="/admin/agencies?filter=suspended" class="btn <?= ($filter ?? '') === 'suspended' ? 'btn-primary' : 'btn-outlined' ?>" style="border-radius:20px; padding:4px 14px; font-size:12px;">
        Suspended (<?= $counts['suspended'] ?>)
    </a>
</div>

<div class="g-card">
    <div class="table-responsive">
        <table class="g-table">
            <thead>
                <tr>
                    <th>Branch Code</th>
                    <th>Agency / Legal Name</th>
                    <th>Subdomain</th>
                    <th>Contact Info</th>
                    <th>PAN / GSTIN</th>
                    <th>Validity Period</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tenants)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center; padding:32px; color:var(--text-secondary);">
                            No agency tenants match this filter.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($tenants as $t): ?>
                    <?php
                        $daysLeft = null;
                        if (!empty($t['validity_end'])) {
                            $end = new DateTime($t['validity_end']);
                            $daysLeft = (new DateTime())->diff($end)->days;
                        }
                    ?>
                    <tr>
                        <td><strong>#<?= htmlspecialchars($t['agency_code'] ?? 'PENDING') ?></strong></td>
                        <td>
                            <div style="font-weight:500; font-size:14px;"><?= htmlspecialchars($t['name']) ?></div>
                            <div style="font-size:11px; color:var(--text-secondary);"><?= htmlspecialchars($t['trade_name'] ?? '') ?></div>
                        </td>
                        <td>
                            <code><?= htmlspecialchars($t['slug']) ?>.localhost</code>
                        </td>
                        <td>
                            <div><?= htmlspecialchars($t['contact_person']) ?></div>
                            <div style="font-size:11px; color:var(--text-secondary);"><?= htmlspecialchars($t['phone']) ?> • <?= htmlspecialchars($t['city'] ?? '') ?></div>
                        </td>
                        <td>
                            <div>PAN: <code><?= htmlspecialchars($t['pan_no'] ?? 'N/A') ?></code></div>
                            <div style="font-size:11px; color:var(--text-secondary);">GSTIN: <?= htmlspecialchars($t['gstin'] ?? 'Unregistered') ?></div>
                        </td>
                        <td>
                            <?php if (!empty($t['validity_end'])): ?>
                                <div><?= htmlspecialchars($t['validity_end']) ?></div>
                                <div style="font-size:11px; color:<?= $daysLeft <= 30 ? 'var(--google-red)' : 'var(--text-secondary)' ?>;">
                                    (<?= $daysLeft ?> days left)
                                </div>
                            <?php else: ?>
                                <span style="color:var(--text-tertiary);">Not active yet</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($t['status'] === 'active'): ?>
                                <span class="g-chip chip-success">Active</span>
                            <?php elseif ($t['status'] === 'pending_kyc'): ?>
                                <span class="g-chip chip-warning">Pending Review</span>
                            <?php elseif ($t['status'] === 'rejected'): ?>
                                <span class="g-chip chip-danger">Rejected</span>
                            <?php else: ?>
                                <span class="g-chip chip-danger"><?= ucfirst($t['status']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display:flex; gap:6px; flex-wrap:wrap;">
                                <?php if ($t['status'] === 'pending_kyc'): ?>
                                    <a href="/admin/kyc/<?= $t['id'] ?>" class="btn btn-primary" style="padding:4px 8px; font-size:11px;">Review KYC</a>
                                <?php endif; ?>
                                <a href="/admin/agencies/<?= $t['id'] ?>/edit" class="btn btn-outlined" style="padding:4px 8px; font-size:11px;">Edit / Plan</a>
                                <form method="POST" action="/admin/agencies/<?= $t['id'] ?>/toggle-status" style="display:inline;">
                                    <button type="submit" class="btn btn-outlined" style="padding:4px 8px; font-size:11px;" onclick="return confirm('Change status for <?= htmlspecialchars($t['name']) ?>?')">
                                        <?= $t['status'] === 'active' ? '🔒 Suspend' : '✓ Activate' ?>
                                    </button>
                                </form>
                                <a href="/?tenant=<?= htmlspecialchars($t['slug']) ?>" target="_blank" class="btn btn-outlined" style="padding:4px 8px; font-size:11px;">Portal ↗</a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/src/Views/layout.php';
?>

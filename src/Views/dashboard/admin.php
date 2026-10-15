<?php
// src/Views/dashboard/admin.php - Super Admin Control Center View

$db = Database::getInstance();

// Metrics
$totalAgencies = (int)$db->fetchOne("SELECT COUNT(*) as c FROM tenants")['c'] ?? 0;
$activeAgencies = (int)$db->fetchOne("SELECT COUNT(*) as c FROM tenants WHERE status = 'active'")['c'] ?? 0;
$pendingKyc = (int)$db->fetchOne("SELECT COUNT(*) as c FROM tenants WHERE status = 'pending_kyc'")['c'] ?? 0;

$tenants = $db->fetchAll("SELECT * FROM tenants ORDER BY created_at DESC LIMIT 10");

$pageTitle = 'Super Admin Dashboard';
$activeNav = 'dashboard';

ob_start();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Super Admin Control Center</h1>
        <p style="color:var(--text-secondary); font-size:13px; margin-top:4px;">Manage multi-tenant courier agencies, KYC verification, and central destination governance.</p>
    </div>
    <div style="display:flex; gap:12px;">
        <a href="/admin/destinations" class="btn btn-outlined">📍 Master Destinations</a>
        <a href="/admin/agencies/new" class="btn btn-primary">+ Register Agency</a>
    </div>
</div>

<!-- Metrics Overview Cards -->
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-label">Active Agency Portals</div>
        <div class="metric-value"><?= $activeAgencies ?></div>
        <div class="metric-sub" style="color:var(--google-green);">
            <span>●</span> <?= $totalAgencies ?> Total Registered
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-label">Pending KYC Verifications</div>
        <div class="metric-value" style="color:var(--google-yellow);"><?= $pendingKyc ?></div>
        <div class="metric-sub" style="color:var(--text-secondary);">
            Review PAN, Aadhaar & GSTIN
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-label">Expiring Subscriptions (30d)</div>
        <div class="metric-value" style="color:var(--google-blue);">1</div>
        <div class="metric-sub" style="color:var(--text-secondary);">
            Automated alerts active
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-label">Open Destination Tickets</div>
        <div class="metric-value">0</div>
        <div class="metric-sub" style="color:var(--google-green);">
            ✓ All routes updated
        </div>
    </div>
</div>

<!-- Agency Tenants Table -->
<div class="g-card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
        <h2 style="font-size:16px;">Agency Tenants & Subscriptions</h2>
        <span class="g-chip chip-info"><?= count($tenants) ?> agencies</span>
    </div>

    <div class="table-responsive">
        <table class="g-table">
            <thead>
                <tr>
                    <th>Agency Code</th>
                    <th>Agency / Franchise Name</th>
                    <th>Subdomain / Host</th>
                    <th>City / State</th>
                    <th>Contact Person</th>
                    <th>Validity Expiry</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tenants as $t): ?>
                    <?php
                        $daysLeft = null;
                        if (!empty($t['validity_end'])) {
                            $end = new DateTime($t['validity_end']);
                            $daysLeft = (new DateTime())->diff($end)->days;
                        }
                    ?>
                    <tr>
                        <td><strong>#<?= htmlspecialchars($t['agency_code']) ?></strong></td>
                        <td>
                            <div style="font-weight:500;"><?= htmlspecialchars($t['name']) ?></div>
                            <div style="font-size:11px; color:var(--text-secondary);"><?= htmlspecialchars($t['trade_name'] ?? '') ?></div>
                        </td>
                        <td>
                            <code><?= htmlspecialchars($t['slug']) ?>.localhost</code>
                        </td>
                        <td><?= htmlspecialchars($t['city'] ?? '') ?>, <?= htmlspecialchars($t['state'] ?? '') ?></td>
                        <td>
                            <div><?= htmlspecialchars($t['contact_person']) ?></div>
                            <div style="font-size:11px; color:var(--text-secondary);"><?= htmlspecialchars($t['phone']) ?></div>
                        </td>
                        <td>
                            <div><?= htmlspecialchars($t['validity_end'] ?? 'N/A') ?></div>
                            <?php if ($daysLeft !== null): ?>
                                <div style="font-size:11px; color:<?= $daysLeft <= 30 ? 'var(--google-red)' : 'var(--text-secondary)' ?>;">
                                    (<?= $daysLeft ?> days left)
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($t['status'] === 'active'): ?>
                                <span class="g-chip chip-success">Active</span>
                            <?php elseif ($t['status'] === 'pending_kyc'): ?>
                                <span class="g-chip chip-warning">Pending KYC</span>
                            <?php else: ?>
                                <span class="g-chip chip-danger"><?= ucfirst($t['status']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="/?tenant=<?= htmlspecialchars($t['slug']) ?>" class="btn btn-outlined" style="padding:4px 10px; font-size:12px;">Open Portal ↗</a>
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

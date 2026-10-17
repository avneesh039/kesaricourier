<?php
// src/Views/dashboard/admin.php - Super Admin Control Center View
// Multi-Tenant Platform Governance Dashboard (UI-02)

$pageTitle = 'Super Admin Control Center';
$activeNav = 'dashboard';

ob_start();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">🛡️ Super Admin Control Center</h1>
        <p style="color:var(--text-secondary); font-size:13px; margin-top:4px;">
            Platform-wide governance for courier agency tenants, KYC approvals, destination master tables, and subscription lifecycles.
        </p>
    </div>
    <div style="display:flex; gap:12px;">
        <a href="/admin/destinations" class="btn btn-outlined">📍 Master Destinations</a>
        <a href="/admin/tickets" class="btn btn-outlined">🎫 Tickets (<?= $openTickets ?? 0 ?>)</a>
        <a href="/admin/agencies/new" class="btn btn-primary">+ Register Agency</a>
    </div>
</div>

<!-- Metrics Overview Cards -->
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-label">Active Agency Portals</div>
        <div class="metric-value"><?= $activeTenants ?? 0 ?></div>
        <div class="metric-sub" style="color:var(--google-green);">
            <span>●</span> <?= $totalTenants ?? 0 ?> Total Tenants Registered
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-label">Pending KYC Verifications</div>
        <div class="metric-value" style="color:<?= ($pendingKyc ?? 0) > 0 ? 'var(--google-yellow)' : 'var(--google-green)' ?>;">
            <?= $pendingKyc ?? 0 ?>
        </div>
        <div class="metric-sub" style="color:var(--text-secondary);">
            Review PAN, Aadhaar & GSTIN
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-label">Expiring Subscriptions (30d)</div>
        <div class="metric-value" style="color:<?= ($expiringSoon ?? 0) > 0 ? 'var(--google-red)' : 'var(--google-green)' ?>;">
            <?= $expiringSoon ?? 0 ?>
        </div>
        <div class="metric-sub" style="color:var(--text-secondary);">
            Automated renewal alerts active
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-label">Platform Gross Volume</div>
        <div class="metric-value" style="color:var(--google-blue);">
            ₹<?= number_format($systemBookings['volume'] ?? 0, 0) ?>
        </div>
        <div class="metric-sub" style="color:var(--text-secondary);">
            <?= $systemBookings['count'] ?? 0 ?> consignments booked platform-wide
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns: 1.5fr 1fr; gap:24px; margin-top:20px; align-items:start;">

    <!-- Left: Pending KYC Verification Pipeline -->
    <div class="g-card" style="padding:0; overflow:hidden;">
        <div style="padding:16px 20px; border-bottom:1px solid #dadce0; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h2 style="font-size:16px; margin:0;">Pending KYC Verification Queue</h2>
                <p style="font-size:12px; color:var(--text-secondary); margin:2px 0 0 0;">New agencies awaiting document verification and branch code activation</p>
            </div>
            <a href="/admin/kyc" class="btn btn-outlined" style="padding:4px 12px; font-size:12px;">Full KYC Queue →</a>
        </div>

        <div class="table-responsive">
            <table class="g-table">
                <thead>
                    <tr>
                        <th>Agency Name</th>
                        <th>City / State</th>
                        <th>Contact</th>
                        <th>Applied On</th>
                        <th style="text-align:right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pendingAgencies)): ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding:32px; color:var(--text-secondary);">
                                ✓ All KYC applications have been reviewed. Queue is clear!
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($pendingAgencies as $pa): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($pa['name']) ?></strong>
                                    <div style="font-size:11px; color:var(--text-secondary);"><?= htmlspecialchars($pa['trade_name'] ?? '') ?></div>
                                </td>
                                <td><?= htmlspecialchars($pa['city']) ?>, <?= htmlspecialchars($pa['state']) ?></td>
                                <td>
                                    <div><?= htmlspecialchars($pa['contact_person']) ?></div>
                                    <div style="font-size:11px; color:var(--text-secondary);"><?= htmlspecialchars($pa['phone']) ?></div>
                                </td>
                                <td><?= date('d-M-Y', strtotime($pa['created_at'])) ?></td>
                                <td style="text-align:right;">
                                    <a href="/admin/agencies/<?= $pa['id'] ?>/kyc" class="btn btn-primary" style="padding:4px 12px; font-size:11px;">
                                        Review Docs
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right: Recent Tickets & Route Governance -->
    <div class="g-card" style="padding:0; overflow:hidden;">
        <div style="padding:16px 20px; border-bottom:1px solid #dadce0; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h2 style="font-size:16px; margin:0;">Destination & Support Tickets</h2>
                <p style="font-size:12px; color:var(--text-secondary); margin:2px 0 0 0;">Requests from branch managers</p>
            </div>
            <a href="/admin/tickets" class="btn btn-outlined" style="padding:4px 12px; font-size:12px;">All Tickets →</a>
        </div>

        <?php if (empty($recentTickets)): ?>
            <div style="padding:32px; text-align:center; color:var(--text-secondary); font-size:13px;">
                No open support tickets.
            </div>
        <?php else: ?>
            <div style="display:flex; flex-direction:column;">
                <?php foreach ($recentTickets as $tk): ?>
                    <div style="padding:12px 18px; border-bottom:1px solid #f1f3f4; display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <div style="font-weight:600; font-size:13px;"><?= htmlspecialchars($tk['subject']) ?></div>
                            <div style="font-size:11px; color:var(--text-secondary); margin-top:2px;">
                                Agency: <strong><?= htmlspecialchars($tk['tenant_name']) ?></strong> (#<?= htmlspecialchars($tk['agency_code']) ?>)
                            </div>
                        </div>
                        <div style="text-align:right;">
                            <?php if ($tk['status'] === 'open'): ?>
                                <span class="g-chip chip-warning" style="font-size:10px;">Open</span>
                            <?php else: ?>
                                <span class="g-chip chip-success" style="font-size:10px;">Resolved</span>
                            <?php endif; ?>
                            <div style="margin-top:4px;">
                                <a href="/admin/tickets/<?= $tk['id'] ?>" style="font-size:11px; color:var(--google-blue); text-decoration:none;">Review →</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/src/Views/layout.php';
?>

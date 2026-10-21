<?php
// src/Views/dashboard/admin.php - Super Admin Control Center View
// Multi-Tenant Platform Governance Dashboard (UI-02)

$pageTitle = 'Super Admin Control Center';
$activeNav = 'dashboard';

ob_start();
?>

<div class="page-header">
    <div>
        <h1 class="page-title" style="display:flex; align-items:center; gap:10px;">
            <span style="color:var(--brand-primary); display:inline-flex;"><?= Icon::svg('kyc', '', 26) ?></span>
            Super Admin Control Center
        </h1>
        <p style="color:var(--text-secondary); font-size:13px; margin-top:4px;">
            Platform-wide governance for courier agency tenants, KYC approvals, destination master tables, and subscription lifecycles.
        </p>
    </div>
    <div style="display:flex; gap:10px; align-items:center;">
        <button type="button" class="btn btn-outlined" onclick="toggleAdminGuidance()" id="guidanceToggleBtn" style="font-size:13px;">
            <?= Icon::svg('sparkles', '', 16) ?> <span id="guidanceBtnLabel">Hide Guidance</span>
        </button>
        <a href="/admin/subscriptions" class="btn btn-outlined"><?= Icon::svg('receipt-tax') ?> Subscriptions</a>
        <a href="/admin/courier-apis" class="btn btn-outlined"><?= Icon::svg('truck') ?> Courier APIs</a>
        <a href="/admin/destinations" class="btn btn-outlined"><?= Icon::svg('destinations') ?> Destinations</a>
        <a href="/admin/agencies/new" class="btn btn-primary"><?= Icon::svg('plus') ?> Create Agency</a>
    </div>
</div>

<!-- Super Admin Operational Guidance & Basic Functionality Intro -->
<div class="guidance-box" id="adminGuidanceBox">
    <div class="guidance-header">
        <div class="guidance-header-title">
            <span style="color:var(--brand-primary); display:inline-flex;"><?= Icon::svg('sparkles', '', 20) ?></span>
            <span>Super Admin Operations & Platform Governance Guide</span>
        </div>
        <span class="g-chip chip-info" style="font-size:11px;">
            4-Step Core Workflow
        </span>
    </div>

    <div class="guidance-steps-grid">
        <!-- Step 1 -->
        <div class="guidance-step-card">
            <div>
                <div class="step-heading">
                    <span class="step-num-badge">1</span>
                    <span>KYC & Branch Onboarding</span>
                </div>
                <div class="step-desc">
                    Review incoming franchise applications. Inspect Aadhaar, PAN card, and GSTIN documents. One-click approve to issue branch agency codes (e.g., #26 Raipur) and set login passwords.
                </div>
            </div>
            <div class="step-meta">
                <span>Governance Module</span>
                <a href="/admin/kyc" style="color:var(--brand-primary); text-decoration:none; font-weight:600;">Open KYC Queue →</a>
            </div>
        </div>

        <!-- Step 2 -->
        <div class="guidance-step-card">
            <div>
                <div class="step-heading">
                    <span class="step-num-badge">2</span>
                    <span>Destination & Hub Master</span>
                </div>
                <div class="step-desc">
                    Manage Central & Western India hub matrix. Add destination cities, route codes (NGP, BPL, JLR), destination pincodes, and active transit corridors for automatic counter rate calculations.
                </div>
            </div>
            <div class="step-meta">
                <span>Routing Engine</span>
                <a href="/admin/destinations" style="color:var(--brand-primary); text-decoration:none; font-weight:600;">Manage Hubs →</a>
            </div>
        </div>

        <!-- Step 3 -->
        <div class="guidance-step-card">
            <div>
                <div class="step-heading">
                    <span class="step-num-badge">3</span>
                    <span>Branch Support & Tickets</span>
                </div>
                <div class="step-desc">
                    Resolve operational escalation requests submitted by counter operators regarding missing pincodes, line-haul delays, and special corporate rate agreements.
                </div>
            </div>
            <div class="step-meta">
                <span>Support Desk</span>
                <a href="/admin/tickets" style="color:var(--brand-primary); text-decoration:none; font-weight:600;">View Tickets (<?= $openTickets ?? 0 ?>) →</a>
            </div>
        </div>

        <!-- Step 4 -->
        <div class="guidance-step-card">
            <div>
                <div class="step-heading">
                    <span class="step-num-badge">4</span>
                    <span>Platform Billing & GST Audit</span>
                </div>
                <div class="step-desc">
                    Monitor consolidated gross booking volumes across all agency franchises. Ensure SAC 996812 18% GST tax adherence and maintain tenant subscription lifecycles.
                </div>
            </div>
            <div class="step-meta">
                <span>Fiscal Audit</span>
                <a href="/bookings" style="color:var(--brand-primary); text-decoration:none; font-weight:600;">Live Feed →</a>
            </div>
        </div>
    </div>
</div>

<script>
function toggleAdminGuidance() {
    var box = document.getElementById('adminGuidanceBox');
    var label = document.getElementById('guidanceBtnLabel');
    if (!box) return;
    if (box.style.display === 'none') {
        box.style.display = 'block';
        label.innerText = 'Hide Guidance';
        localStorage.setItem('kesari_admin_guidance', 'visible');
    } else {
        box.style.display = 'none';
        label.innerText = 'How to Use Guide';
        localStorage.setItem('kesari_admin_guidance', 'hidden');
    }
}

// Restore saved state
(function() {
    var state = localStorage.getItem('kesari_admin_guidance');
    if (state === 'hidden') {
        var box = document.getElementById('adminGuidanceBox');
        var label = document.getElementById('guidanceBtnLabel');
        if (box && label) {
            box.style.display = 'none';
            label.innerText = 'How to Use Guide';
        }
    }
})();
</script>

<!-- Metrics Overview Cards -->
<div class="metrics-grid" style="display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:18px; width:100%; margin-bottom:26px;">
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
            <a href="/admin/kyc" class="btn btn-outlined" style="padding:4px 12px; font-size:12px; display:inline-flex; align-items:center; gap:6px;">
                Full KYC Queue <?= Icon::svg('arrow-right', '', 14) ?>
            </a>
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
                                <div style="display:inline-flex; align-items:center; gap:8px;">
                                    <?= Icon::svg('check', '', 18, 'var(--google-green)') ?>
                                    All KYC applications have been reviewed. Queue is clear!
                                </div>
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
            <a href="/admin/tickets" class="btn btn-outlined" style="padding:4px 12px; font-size:12px; display:inline-flex; align-items:center; gap:6px;">
                All Tickets <?= Icon::svg('arrow-right', '', 14) ?>
            </a>
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
                                <a href="/admin/tickets/<?= $tk['id'] ?>" style="font-size:11px; color:var(--google-blue); text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                                    Review <?= Icon::svg('chevron-right', '', 12) ?>
                                </a>
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

<?php
// src/Views/dashboard/agency.php - Agency Operational Command Center View
// Google Material Design 3 real-time dashboard

$tenant = TenantContext::getTenant();
$pageTitle = ($tenant['name'] ?? 'Agency Portal') . ' - Operations Command Center';
$activeNav = 'dashboard';

ob_start();
?>

<div class="page-header">
    <div>
        <div style="display:flex; align-items:center; gap:10px;">
            <h1 class="page-title"><?= htmlspecialchars($tenant['name'] ?? 'Agency Operations') ?></h1>
            <span class="g-chip chip-info">Branch #<?= htmlspecialchars($tenant['agency_code'] ?? '26') ?></span>
            <span class="g-chip chip-success">Tenant Active</span>
        </div>
        <p style="color:var(--text-secondary); font-size:13px; margin-top:4px;">
            <?= htmlspecialchars($tenant['address'] ?? 'Agency Hub') ?> • Phone: <?= htmlspecialchars($tenant['phone'] ?? '') ?>
        </p>
    <div style="display:flex; gap:10px; align-items:center;">
        <button type="button" class="btn btn-outlined" onclick="toggleAgencyGuidance()" id="agencyGuidanceToggleBtn" style="font-size:13px;">
            <?= Icon::svg('sparkles', '', 16) ?> <span id="agencyGuidanceBtnLabel">Hide Guide</span>
        </button>
        <a href="/print-labels" class="btn btn-outlined"><?= Icon::svg('print') ?> Print Thermal Labels</a>
        <a href="/invoices/new" class="btn btn-outlined"><?= Icon::svg('invoices') ?> Issue Invoice</a>
        <a href="/bookings/new" class="btn btn-primary"><?= Icon::svg('plus') ?> New Booking (Enter)</a>
    </div>
</div>

<!-- Agency Counter Operator Guidance & Daily Workflow Intro -->
<div class="guidance-box" id="agencyGuidanceBox">
    <div class="guidance-header">
        <div class="guidance-header-title">
            <span style="color:var(--brand-primary); display:inline-flex;"><?= Icon::svg('sparkles', '', 20) ?></span>
            <span>Branch Counter Operator Quick Guide & Daily Workflow</span>
        </div>
        <div style="display:flex; gap:8px; align-items:center;">
            <span class="g-chip chip-info" style="font-size:11px;">
                Branch #<?= htmlspecialchars($tenant['agency_code'] ?? '26') ?> Operator Desk
            </span>
        </div>
    </div>

    <div class="guidance-steps-grid">
        <!-- Step 1 -->
        <div class="guidance-step-card">
            <div>
                <div class="step-heading">
                    <span class="step-num-badge">1</span>
                    <span>Book Consignment</span>
                </div>
                <div class="step-desc">
                    Weigh parcel and enter weight. Select destination city, speed mode (Air/Surface), and package type (Dox/Non-Dox). Tariffs and 18% GST (SAC 996812) calculate automatically.
                </div>
            </div>
            <div class="step-meta">
                <span>Shortcut: <span class="kbd-shortcut">Ctrl + B</span></span>
                <a href="/bookings/new" style="color:var(--brand-primary); text-decoration:none; font-weight:600;">+ Book Docket →</a>
            </div>
        </div>

        <!-- Step 2 -->
        <div class="guidance-step-card">
            <div>
                <div class="step-heading">
                    <span class="step-num-badge">2</span>
                    <span>Print 4x6" Thermal Label</span>
                </div>
                <div class="step-desc">
                    Generate standard 4x6" thermal adhesive barcode labels. Supports Code 128 barcode & 2D QR code for TSC, Zebra, TVS, and Rollo thermal printers. Affix label to parcel.
                </div>
            </div>
            <div class="step-meta">
                <span>Shortcut: <span class="kbd-shortcut">Ctrl + P</span></span>
                <a href="/print-labels" style="color:var(--brand-primary); text-decoration:none; font-weight:600;">Print Queue →</a>
            </div>
        </div>

        <!-- Step 3 -->
        <div class="guidance-step-card">
            <div>
                <div class="step-heading">
                    <span class="step-num-badge">3</span>
                    <span>Line-Haul Handover</span>
                </div>
                <div class="step-desc">
                    Bag booked consignments by destination route. Hand over to scheduled line-haul vehicle at evening cutoff. Consignment statuses transition to in-transit automatically.
                </div>
            </div>
            <div class="step-meta">
                <span>Dispatch Feed</span>
                <a href="/bookings" style="color:var(--brand-primary); text-decoration:none; font-weight:600;">Check Feed →</a>
            </div>
        </div>

        <!-- Step 4 -->
        <div class="guidance-step-card">
            <div>
                <div class="step-heading">
                    <span class="step-num-badge">4</span>
                    <span>Monthly B2B Tax Invoicing</span>
                </div>
                <div class="step-desc">
                    Filter corporate client bookings for the billing cycle. Generate consolidated GST tax invoices with SAC 996812 tax split and agency RTGS bank remittance details.
                </div>
            </div>
            <div class="step-meta">
                <span>Corporate Invoicing</span>
                <a href="/invoices/new" style="color:var(--brand-primary); text-decoration:none; font-weight:600;">Issue Invoice →</a>
            </div>
        </div>
    </div>

    <!-- Quick Keyboard Shortcuts Bar -->
    <div style="margin-top:14px; padding-top:12px; border-top:1px solid #e2e8f0; display:flex; align-items:center; gap:16px; font-size:12px; color:var(--text-secondary); flex-wrap:wrap;">
        <strong style="color:var(--text-primary); font-size:12px;">⚡ Pro Keyboard Shortcuts:</strong>
        <span><span class="kbd-shortcut">Ctrl + B</span> New Booking</span>
        <span><span class="kbd-shortcut">Ctrl + P</span> Print Thermal Labels</span>
        <span><span class="kbd-shortcut">Ctrl + I</span> Create Invoice</span>
        <span><span class="kbd-shortcut">Esc</span> Close Modals & Windows</span>
    </div>
</div>

<script>
function toggleAgencyGuidance() {
    var box = document.getElementById('agencyGuidanceBox');
    var label = document.getElementById('agencyGuidanceBtnLabel');
    if (!box) return;
    if (box.style.display === 'none') {
        box.style.display = 'block';
        label.innerText = 'Hide Guide';
        localStorage.setItem('kesari_agency_guidance', 'visible');
    } else {
        box.style.display = 'none';
        label.innerText = 'How to Use Guide';
        localStorage.setItem('kesari_agency_guidance', 'hidden');
    }
}

// Restore saved state
(function() {
    var state = localStorage.getItem('kesari_agency_guidance');
    if (state === 'hidden') {
        var box = document.getElementById('agencyGuidanceBox');
        var label = document.getElementById('agencyGuidanceBtnLabel');
        if (box && label) {
            box.style.display = 'none';
            label.innerText = 'How to Use Guide';
        }
    }

    // Global keyboard shortcuts for branch counter
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 'b') {
            e.preventDefault();
            window.location.href = '/bookings/new';
        } else if (e.ctrlKey && e.key === 'p') {
            e.preventDefault();
            window.location.href = '/print-labels';
        } else if (e.ctrlKey && e.key === 'i') {
            e.preventDefault();
            window.location.href = '/invoices/new';
        }
    });
})();
</script>

<!-- Agency Real-Time KPI Cards (UI-02) -->
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-label">Today's Bookings</div>
        <div class="metric-value" style="color:var(--brand-primary);"><?= $todayStats['count'] ?? 0 ?></div>
        <div class="metric-sub" style="color:var(--google-green);">
            <span>₹</span> <?= number_format($todayStats['revenue'] ?? 0, 2) ?> booked today
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-label">Active Line-Haul Shipments</div>
        <div class="metric-value"><?= $activeParcels ?? 0 ?></div>
        <div class="metric-sub" style="color:var(--text-secondary);">
            Parcels in-transit / bagged
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-label">Unbilled Consignments Pool</div>
        <div class="metric-value" style="color:var(--google-blue);"><?= $unbilledStats['count'] ?? 0 ?></div>
        <div class="metric-sub" style="color:var(--text-secondary);">
            Est. ₹<?= number_format($unbilledStats['estimated_revenue'] ?? 0, 2) ?> unbilled freight
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-label">Pending Invoices Due</div>
        <div class="metric-value" style="color:<?= ($invoiceDueStats['count'] ?? 0) > 0 ? 'var(--google-red)' : 'var(--google-green)' ?>;">
            ₹<?= number_format($invoiceDueStats['due_amount'] ?? 0, 2) ?>
        </div>
        <div class="metric-sub" style="color:var(--text-secondary);">
            <?= $invoiceDueStats['count'] ?? 0 ?> invoices awaiting payment
        </div>
    </div>
</div>

<div style="display:grid; grid-template-columns: 2fr 1fr; gap:24px; margin-top:20px; align-items:start;">

    <!-- Left: Recent Consignments Stream -->
    <div class="g-card" style="padding:0; overflow:hidden;">
        <div style="padding:16px 20px; border-bottom:1px solid #dadce0; display:flex; justify-content:space-between; align-items:center;">
            <div>
                <h2 style="font-size:16px; margin:0;">Recent Consignment Bookings</h2>
                <p style="font-size:12px; color:var(--text-secondary); margin:2px 0 0 0;">Live docket feed created by counter operators</p>
            </div>
            <a href="/bookings" class="btn btn-outlined" style="padding:4px 12px; font-size:12px;">All Consignments →</a>
        </div>

        <div class="table-responsive">
            <table class="g-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>AWB #</th>
                        <th>Destination</th>
                        <th>Mode</th>
                        <th>Weight</th>
                        <th>B2B Client</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th style="text-align:right;">Print</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentBookings)): ?>
                        <tr>
                            <td colspan="9" style="text-align:center; padding:32px; color:var(--text-secondary);">
                                No consignments booked yet. Click <strong>+ New Booking</strong> to book parcels.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentBookings as $b): ?>
                            <tr>
                                <td><?= date('d-M', strtotime($b['booking_date'])) ?></td>
                                <td>
                                    <div style="display:flex; align-items:center; gap:4px;">
                                        <a href="/bookings/<?= $b['id'] ?>" class="awb-badge" style="text-decoration:none;">
                                            <?= htmlspecialchars($b['consignment_no']) ?>
                                        </a>
                                        <button type="button" class="copyable" data-copy="<?= htmlspecialchars($b['consignment_no']) ?>" title="Copy AWB" style="background:none; border:none; cursor:pointer; color:var(--text-tertiary); display:inline-flex; align-items:center;">
                                            <?= Icon::svg('copy', '', 14) ?>
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($b['dest_code']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($b['send_by']) ?></td>
                                <td><?= number_format($b['billable_weight'], 1) ?> kg</td>
                                <td>
                                    <span style="font-size:12px;"><?= htmlspecialchars($b['client_name']) ?></span>
                                </td>
                                <td><strong>₹<?= number_format($b['total_amount'], 2) ?></strong></td>
                                <td>
                                    <span class="g-chip <?= $b['status'] === 'delivered' ? 'chip-success' : 'chip-info' ?>" style="font-size:11px;">
                                        <?= ucfirst(str_replace('_', ' ', $b['status'])) ?>
                                    </span>
                                </td>
                                <td style="text-align:right;">
                                    <a href="/print/single?awb=<?= urlencode($b['consignment_no']) ?>" target="_blank" class="btn btn-outlined" style="padding:4px 8px; font-size:11px; display:inline-flex; align-items:center; gap:4px;" title="Print 4x6 Label">
                                        <?= Icon::svg('print', '', 13) ?> 4x6
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right: Quick Actions & Invoices Summary -->
    <div>
        <!-- Quick Action Speed Dial -->
        <div class="g-card" style="margin-bottom:20px;">
            <h3 style="font-size:15px; margin-bottom:12px;">Operational Quick Actions</h3>
            <div style="display:flex; flex-direction:column; gap:8px;">
                <a href="/bookings/new" class="btn btn-primary" style="justify-content:flex-start; padding:10px 14px; gap:10px;">
                    <?= Icon::svg('book', '', 18) ?> Rapid Consignment Booking (Enter)
                </a>
                <a href="/print-labels" class="btn btn-secondary" style="justify-content:flex-start; padding:10px 14px; gap:10px;">
                    <?= Icon::svg('print', '', 18) ?> Thermal Print Center (Single / Batch)
                </a>
                <a href="/invoices/new" class="btn btn-secondary" style="justify-content:flex-start; padding:10px 14px; gap:10px;">
                    <?= Icon::svg('invoices', '', 18) ?> Generate Monthly GST Tax Invoice
                </a>
                <a href="/payments" class="btn btn-secondary" style="justify-content:flex-start; padding:10px 14px; gap:10px;">
                    <?= Icon::svg('payments', '', 18) ?> Client Payment Receipts & Ledger
                </a>
                <a href="/operators" class="btn btn-secondary" style="justify-content:flex-start; padding:10px 14px; gap:10px;">
                    <?= Icon::svg('operators', '', 18) ?> Branch Desk Operators (Staff Desks)
                </a>
                <a href="/bank-accounts" class="btn btn-secondary" style="justify-content:flex-start; padding:10px 14px; gap:10px;">
                    <?= Icon::svg('bank', '', 18) ?> Settlement Bank Account Settings
                </a>
                <a href="/tickets/new" class="btn btn-secondary" style="justify-content:flex-start; padding:10px 14px; gap:10px;">
                    <?= Icon::svg('tickets', '', 18) ?> Request Destination / Pincode Addition
                </a>
            </div>
        </div>

        <!-- Recent Invoices Widget -->
        <div class="g-card" style="padding:0; overflow:hidden;">
            <div style="padding:14px 16px; border-bottom:1px solid #dadce0; display:flex; justify-content:space-between; align-items:center;">
                <span style="font-weight:700; font-size:14px;">Recent Tax Invoices</span>
                <a href="/invoices" style="font-size:12px; color:var(--google-blue); text-decoration:none;">View All →</a>
            </div>

            <?php if (empty($recentInvoices)): ?>
                <div style="padding:24px; text-align:center; color:var(--text-secondary); font-size:13px;">
                    No invoices generated yet.
                </div>
            <?php else: ?>
                <div style="display:flex; flex-direction:column;">
                    <?php foreach ($recentInvoices as $inv): ?>
                        <div style="padding:12px 16px; border-bottom:1px solid #f1f3f4; display:flex; justify-content:space-between; align-items:center;">
                            <div>
                                <a href="/invoices/<?= $inv['id'] ?>" style="font-family:monospace; font-weight:700; color:var(--google-blue); text-decoration:none; font-size:13px;">
                                    <?= htmlspecialchars($inv['invoice_no']) ?>
                                </a>
                                <div style="font-size:11px; color:var(--text-secondary); margin-top:2px;">
                                    <?= htmlspecialchars($inv['client_name']) ?>
                                </div>
                            </div>
                            <div style="text-align:right;">
                                <div style="font-weight:700; font-size:13px;">₹<?= number_format($inv['grand_total'], 2) ?></div>
                                <?php if ($inv['payment_status'] === 'paid'): ?>
                                    <span class="g-chip chip-success" style="font-size:10px; padding:1px 6px;">Paid</span>
                                <?php else: ?>
                                    <span class="g-chip" style="font-size:10px; padding:1px 6px; background:#fce8e6; color:#c5221f;">Due</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/src/Views/layout.php';
?>

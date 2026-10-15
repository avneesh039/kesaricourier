<?php
// src/Views/dashboard/agency.php - White-Labeled Agency Portal Dashboard View

$tenant = TenantContext::getTenant();
$pageTitle = ($tenant['name'] ?? 'Agency Portal') . ' - Operations';
$activeNav = 'dashboard';

ob_start();
?>

<div class="page-header">
    <div>
        <div style="display:flex; align-items:center; gap:10px;">
            <h1 class="page-title"><?= htmlspecialchars($tenant['name'] ?? 'Agency Operations') ?></h1>
            <span class="g-chip chip-info">Branch Code: #<?= htmlspecialchars($tenant['agency_code'] ?? '26') ?></span>
            <span class="g-chip chip-success">Tenant Active</span>
        </div>
        <p style="color:var(--text-secondary); font-size:13px; margin-top:4px;">
            <?= htmlspecialchars($tenant['address'] ?? 'Agency Hub') ?> • Contact: <?= htmlspecialchars($tenant['phone'] ?? '') ?>
        </p>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="/print-labels" class="btn btn-outlined">🏷️ Print Thermal Labels</a>
        <a href="/bookings/new" class="btn btn-primary">+ New Booking (AWB)</a>
    </div>
</div>

<!-- Agency Metrics -->
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-label">Today's Bookings</div>
        <div class="metric-value" style="color:var(--brand-primary);">14</div>
        <div class="metric-sub" style="color:var(--google-green);">
            <span>↑</span> 18.5 kg total weight
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-label">In-Transit Shipments</div>
        <div class="metric-value">42</div>
        <div class="metric-sub" style="color:var(--text-secondary);">
            Air & Surface dispatched
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-label">Delivered (e-POD)</div>
        <div class="metric-value" style="color:var(--google-green);">128</div>
        <div class="metric-sub" style="color:var(--google-green);">
            ✓ 98.4% on-time delivery
        </div>
    </div>

    <div class="metric-card">
        <div class="metric-label">Unbilled B2B Ledger</div>
        <div class="metric-value">₹ 42,850</div>
        <div class="metric-sub" style="color:var(--text-secondary);">
            Ready for month-end GST bill
        </div>
    </div>
</div>

<!-- Recent Consignment Bookings (Modeled from live legacy system) -->
<div class="g-card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
        <div>
            <h2 style="font-size:16px;">Recent Consignment Bookings</h2>
            <p style="font-size:12px; color:var(--text-secondary); margin-top:2px;">Real-time feed of AWB dockets created by branch operators</p>
        </div>
        <a href="/bookings" class="btn btn-outlined" style="padding:4px 12px; font-size:12px;">View All Bookings →</a>
    </div>

    <div class="table-responsive">
        <table class="g-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>AWB / Consignment No</th>
                    <th>Destination</th>
                    <th>Mode</th>
                    <th>Sent By</th>
                    <th>Weight</th>
                    <th>B2B Client Name</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= date('d-m-Y') ?></td>
                    <td><strong>25011200064307</strong></td>
                    <td>SURAT</td>
                    <td><span class="g-chip chip-info" style="font-size:11px;">Dox</span></td>
                    <td>Air</td>
                    <td>0.1 kg</td>
                    <td>VANDANA ISPAT PVT LTD-72</td>
                    <td><strong>₹ 40.00</strong></td>
                    <td><span class="g-chip chip-success">Booked</span></td>
                    <td>
                        <a href="#" class="btn btn-outlined" style="padding:2px 8px; font-size:11px;">Print 4x6"</a>
                    </td>
                </tr>
                <tr>
                    <td><?= date('d-m-Y') ?></td>
                    <td><strong>25011200064296</strong></td>
                    <td>MUMBAI</td>
                    <td><span class="g-chip chip-info" style="font-size:11px;">Dox</span></td>
                    <td>Air</td>
                    <td>0.1 kg</td>
                    <td>VANDANA ISPAT PVT LTD-72</td>
                    <td><strong>₹ 40.00</strong></td>
                    <td><span class="g-chip chip-success">Booked</span></td>
                    <td>
                        <a href="#" class="btn btn-outlined" style="padding:2px 8px; font-size:11px;">Print 4x6"</a>
                    </td>
                </tr>
                <tr>
                    <td><?= date('d-m-Y') ?></td>
                    <td><strong>25011100060705</strong></td>
                    <td>GANDHINAGAR</td>
                    <td><span class="g-chip chip-info" style="font-size:11px;">Dox</span></td>
                    <td>Surface</td>
                    <td>7.0 kg</td>
                    <td>CASH SALE SHREE MARUTI-46</td>
                    <td><strong>₹ 1,229.00</strong></td>
                    <td><span class="g-chip chip-info">In-Transit</span></td>
                    <td>
                        <a href="#" class="btn btn-outlined" style="padding:2px 8px; font-size:11px;">Print 4x6"</a>
                    </td>
                </tr>
                <tr>
                    <td><?= date('d-m-Y', strtotime('-1 day')) ?></td>
                    <td><strong>25011100060702</strong></td>
                    <td>PUNE</td>
                    <td><span class="g-chip chip-info" style="font-size:11px;">Dox</span></td>
                    <td>Surface</td>
                    <td>0.5 kg</td>
                    <td>CASH SALE SHREE MARUTI-37</td>
                    <td><strong>₹ 130.00</strong></td>
                    <td><span class="g-chip chip-success">Delivered</span></td>
                    <td>
                        <a href="#" class="btn btn-outlined" style="padding:2px 8px; font-size:11px;">Print 4x6"</a>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/src/Views/layout.php';
?>

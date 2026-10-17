<?php
// src/Views/bookings/index.php - Consignment Records Management Table
$pageTitle = 'Consignments & AWB Directory';
$activeNav = 'bookings';
$flash = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_success']);

ob_start();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Consignments & AWB Records</h1>
        <p style="color:var(--text-secondary); font-size:13px; margin-top:4px;">
            Full operational log of parcel & document shipments booked at this agency.
        </p>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="/print-labels" class="btn btn-outlined">🏷️ Print Thermal Labels</a>
        <a href="/bookings/new" class="btn btn-primary">+ New Booking (AWB)</a>
    </div>
</div>

<?php if ($flash): ?>
    <div style="background:#e6f4ea; border-left:4px solid var(--google-green); color:#137333; padding:12px 18px; border-radius:4px; margin-bottom:20px; font-size:13px;">
        <?= htmlspecialchars($flash) ?>
    </div>
<?php endif; ?>

<!-- Filter & Search Bar -->
<div class="g-card" style="padding:16px; margin-bottom:20px;">
    <form method="GET" action="/bookings" style="display:grid; grid-template-columns: 2fr 1fr 1fr 1fr auto auto; gap:12px; align-items:center;">
        <div>
            <input type="text" name="q" class="form-control" placeholder="Search AWB, recipient, or phone..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
        </div>

        <div>
            <select name="client_id" class="form-control">
                <option value="">All Clients</option>
                <?php foreach ($clients as $cl): ?>
                    <option value="<?= $cl['id'] ?>" <?= ($_GET['client_id'] ?? '') == $cl['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cl['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($_GET['date_from'] ?? '') ?>" title="From Date">
        </div>

        <div>
            <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($_GET['date_to'] ?? '') ?>" title="To Date">
        </div>

        <div>
            <select name="status" class="form-control">
                <option value="">All Statuses</option>
                <option value="booked" <?= ($_GET['status'] ?? '') === 'booked' ? 'selected' : '' ?>>Booked</option>
                <option value="in_transit" <?= ($_GET['status'] ?? '') === 'in_transit' ? 'selected' : '' ?>>In-Transit</option>
                <option value="delivered" <?= ($_GET['status'] ?? '') === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                <option value="cancelled" <?= ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Filter</button>
    </form>
</div>

<div class="g-card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <h2 style="font-size:16px;">Shipment Feed</h2>
        <span class="g-chip chip-info"><?= count($consignments) ?> shipments</span>
    </div>

    <div class="table-responsive">
        <table class="g-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>AWB / Consignment No</th>
                    <th>Destination</th>
                    <th>Mode</th>
                    <th>Weight</th>
                    <th>B2B Client Name</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($consignments)): ?>
                    <tr>
                        <td colspan="9" style="text-align:center; padding:32px; color:var(--text-secondary);">
                            No consignments found matching your search.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($consignments as $c): ?>
                    <tr>
                        <td><?= date('d-m-Y', strtotime($c['booking_date'])) ?></td>
                        <td>
                            <a href="/bookings/<?= $c['id'] ?>" style="font-weight:bold; font-family:monospace; font-size:14px;">
                                <?= htmlspecialchars($c['consignment_no']) ?>
                            </a>
                        </td>
                        <td>
                            <strong><?= htmlspecialchars($c['destination_name']) ?></strong> (<?= htmlspecialchars($c['dest_code']) ?>)
                            <div style="font-size:11px; color:var(--text-secondary);"><?= htmlspecialchars($c['state_name']) ?></div>
                        </td>
                        <td>
                            <span class="g-chip <?= $c['send_by'] === 'Air' ? 'chip-info' : 'chip-warning' ?>" style="font-size:11px;">
                                <?= htmlspecialchars($c['send_by']) ?>
                            </span>
                            <span style="font-size:11px; color:var(--text-secondary);"><?= htmlspecialchars($c['mode']) ?></span>
                        </td>
                        <td>
                            <strong><?= number_format($c['billable_weight'], 2) ?> kg</strong>
                        </td>
                        <td>
                            <div style="font-weight:500; font-size:13px;"><?= htmlspecialchars($c['client_name']) ?></div>
                            <div style="font-size:11px; color:var(--text-secondary);">To: <?= htmlspecialchars($c['recipient_name']) ?></div>
                        </td>
                        <td>
                            <strong style="font-size:14px;">₹<?= number_format($c['total_amount'], 2) ?></strong>
                        </td>
                        <td>
                            <?php if ($c['status'] === 'delivered'): ?>
                                <span class="g-chip chip-success">Delivered</span>
                            <?php elseif ($c['status'] === 'in_transit'): ?>
                                <span class="g-chip chip-info">In-Transit</span>
                            <?php elseif ($c['status'] === 'booked'): ?>
                                <span class="g-chip chip-warning">Booked</span>
                            <?php else: ?>
                                <span class="g-chip chip-danger"><?= ucfirst($c['status']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <a href="/print/single?awb=<?= urlencode($c['consignment_no']) ?>" target="_blank" class="btn btn-outlined" style="padding:3px 8px; font-size:11px;" title="Print 4x6 Thermal Label">
                                    🏷️ Label
                                </a>
                                <a href="/bookings/<?= $c['id'] ?>" class="btn btn-outlined" style="padding:3px 8px; font-size:11px;">
                                    View
                                </a>
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

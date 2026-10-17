<?php
// src/Views/bookings/show.php - Consignment Detail & Tracking View
$pageTitle = 'Consignment #' . $c['consignment_no'];
$activeNav = 'bookings';
$flash = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_success']);

ob_start();
?>

<div class="page-header">
    <div>
        <a href="/bookings" style="font-size:13px; color:var(--text-secondary); text-decoration:none;">← Back to Consignments</a>
        <h1 class="page-title copyable" data-copy="<?= htmlspecialchars($c['consignment_no']) ?>" title="Click to copy AWB" style="margin-top:6px; font-family:var(--font-mono); cursor:pointer;">
            AWB #<?= htmlspecialchars($c['consignment_no']) ?> <span style="font-size:14px; opacity:0.6; font-family:sans-serif;">📋</span>
        </h1>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="/print/single?awb=<?= urlencode($c['consignment_no']) ?>" target="_blank" class="btn btn-primary">
            🏷️ Print 4x6" Thermal Label
        </a>
    </div>
</div>

<?php if ($flash): ?>
    <div style="background:#e6f4ea; border-left:4px solid var(--google-green); color:#137333; padding:12px 18px; border-radius:4px; margin-bottom:20px; font-size:13px;">
        <?= htmlspecialchars($flash) ?>
    </div>
<?php endif; ?>

<div style="display:grid; grid-template-columns: 2fr 1fr; gap:24px;">
    <!-- Left Column: Shipment Dossier -->
    <div>
        <!-- Route & Delivery Milestone Card -->
        <div class="g-card">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                <h2 style="font-size:16px;">Logistics Route & Status</h2>
                <span class="g-chip <?= $c['status'] === 'delivered' ? 'chip-success' : 'chip-info' ?>" style="font-size:13px; padding:4px 14px;">
                    Current Status: <?= ucfirst(str_replace('_', ' ', $c['status'])) ?>
                </span>
            </div>

            <div style="display:grid; grid-template-columns: 1fr auto 1fr; gap:16px; align-items:center; background:#f8f9fa; border-radius:12px; padding:20px; margin-bottom:20px;">
                <div>
                    <div style="font-size:11px; color:var(--text-secondary); text-transform:uppercase;">Origin</div>
                    <div style="font-size:18px; font-weight:600; margin-top:2px;">RAIPUR (RAI)</div>
                    <div style="font-size:12px; color:var(--text-secondary);">Chhattisgarh</div>
                </div>

                <div style="font-size:24px; color:var(--google-blue);">➔</div>

                <div>
                    <div style="font-size:11px; color:var(--text-secondary); text-transform:uppercase;">Destination</div>
                    <div style="font-size:18px; font-weight:600; margin-top:2px;"><?= htmlspecialchars($c['destination_name']) ?> (<?= htmlspecialchars($c['dest_code']) ?>)</div>
                    <div style="font-size:12px; color:var(--text-secondary);"><?= htmlspecialchars($c['state_name']) ?> • Pincode: <?= htmlspecialchars($c['dest_pincode'] ?: 'N/A') ?></div>
                </div>
            </div>

            <!-- Update Status Form -->
            <form method="POST" action="/bookings/<?= $c['id'] ?>/status" style="display:flex; gap:12px; align-items:center; background:#f1f3f4; padding:12px 16px; border-radius:8px;">
                <span style="font-size:13px; font-weight:500;">Update Status:</span>
                <select name="status" class="form-control" style="width:200px;">
                    <option value="booked" <?= $c['status'] === 'booked' ? 'selected' : '' ?>>Booked (At Counter)</option>
                    <option value="manifested" <?= $c['status'] === 'manifested' ? 'selected' : '' ?>>Manifested / Bagged</option>
                    <option value="in_transit" <?= $c['status'] === 'in_transit' ? 'selected' : '' ?>>In-Transit (Line-Haul)</option>
                    <option value="out_for_delivery" <?= $c['status'] === 'out_for_delivery' ? 'selected' : '' ?>>Out for Delivery</option>
                    <option value="delivered" <?= $c['status'] === 'delivered' ? 'selected' : '' ?>>Delivered (POD Received)</option>
                    <option value="cancelled" <?= $c['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                </select>
                <button type="submit" class="btn btn-primary" style="padding:8px 16px;">Update</button>
            </form>
        </div>

        <!-- Sender and Recipient Details -->
        <div class="g-card">
            <h2 style="font-size:16px; margin-bottom:16px;">Addresses & Parties</h2>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px; font-size:13px;">
                <div style="border-right:1px solid #f1f3f4; padding-right:16px;">
                    <span style="font-weight:600; color:var(--google-blue); display:block; margin-bottom:8px;">Sender (B2B Client)</span>
                    <div style="font-weight:500; font-size:14px;"><?= htmlspecialchars($c['client_name']) ?></div>
                    <div style="color:var(--text-secondary); margin-top:4px;">Client Code: #<?= htmlspecialchars($c['client_code']) ?></div>
                    <div style="color:var(--text-secondary); margin-top:2px;">Phone: <?= htmlspecialchars($c['client_phone'] ?: $c['sender_phone'] ?: 'N/A') ?></div>
                    <div style="color:var(--text-secondary); margin-top:2px;">GSTIN: <code><?= htmlspecialchars($c['client_gstin'] ?: 'Unregistered') ?></code></div>
                </div>

                <div>
                    <span style="font-weight:600; color:var(--google-green); display:block; margin-bottom:8px;">Consignee (Recipient)</span>
                    <div style="font-weight:500; font-size:14px;"><?= htmlspecialchars($c['recipient_name']) ?></div>
                    <div style="color:var(--text-secondary); margin-top:4px;">Phone: <?= htmlspecialchars($c['recipient_phone']) ?></div>
                    <div style="color:var(--text-secondary); margin-top:6px; line-height:1.4;">
                        Address: <?= htmlspecialchars($c['recipient_address']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Package Dimensions & Fare Ledger -->
    <div>
        <div class="g-card">
            <h3 style="font-size:15px; margin-bottom:14px;">Package Specifications</h3>
            
            <div style="font-size:13px; display:flex; flex-direction:column; gap:10px;">
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--text-secondary);">Mode:</span>
                    <strong><?= htmlspecialchars($c['mode']) ?> (<?= htmlspecialchars($c['send_by']) ?>)</strong>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--text-secondary);">Pieces:</span>
                    <strong><?= $c['pieces'] ?> Pcs</strong>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--text-secondary);">Dead Weight:</span>
                    <span><?= number_format($c['dead_weight'], 2) ?> kg</span>
                </div>
                <?php if ($c['volumetric_weight']): ?>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-secondary);">Dimensions:</span>
                        <span><?= $c['length_cm'] ?>×<?= $c['width_cm'] ?>×<?= $c['height_cm'] ?> cm</span>
                    </div>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-secondary);">Volumetric:</span>
                        <span><?= number_format($c['volumetric_weight'], 2) ?> kg</span>
                    </div>
                <?php endif; ?>
                <div style="display:flex; justify-content:space-between; border-top:1px solid #f1f3f4; padding-top:8px;">
                    <span style="color:var(--text-secondary);">Billable Weight:</span>
                    <strong style="color:var(--google-blue); font-size:15px;"><?= number_format($c['billable_weight'], 2) ?> kg</strong>
                </div>
            </div>
        </div>

        <div class="g-card">
            <h3 style="font-size:15px; margin-bottom:14px;">Charges & Billing</h3>

            <div style="font-size:13px; display:flex; flex-direction:column; gap:10px;">
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--text-secondary);">Freight Subtotal:</span>
                    <span>₹<?= number_format($c['subtotal'], 2) ?></span>
                </div>
                <?php if ($c['discount_amount'] > 0): ?>
                    <div style="display:flex; justify-content:space-between; color:var(--google-green);">
                        <span>Discount:</span>
                        <span>- ₹<?= number_format($c['discount_amount'], 2) ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($c['other_charges'] > 0): ?>
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-secondary);">Other Surcharges:</span>
                        <span>+ ₹<?= number_format($c['other_charges'], 2) ?></span>
                    </div>
                <?php endif; ?>
                <div style="display:flex; justify-content:space-between; border-top:2px solid var(--border-color); padding-top:10px; font-size:16px; font-weight:700;">
                    <span>Total Amount:</span>
                    <span style="color:var(--google-blue);">₹<?= number_format($c['total_amount'], 2) ?></span>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:11px; color:var(--text-secondary); margin-top:4px;">
                    <span>B2B Invoicing Status:</span>
                    <span><?= $c['is_billed'] ? 'Billed on Invoice' : 'Unbilled (In Current Cycle)' ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/src/Views/layout.php';
?>

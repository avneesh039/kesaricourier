<?php
// src/Views/bookings/create.php - High-Speed Google Workspace Style Consignment Booking Form
$pageTitle = 'Consignment Entry (AWB Booking)';
$activeNav = 'book';
$error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error']);

ob_start();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Consignment Booking Entry</h1>
        <p style="color:var(--text-secondary); font-size:13px; margin-top:4px;">
            Rapid keyboard-first parcel & document booking with automatic rate calculation.
        </p>
    </div>
    <a href="/bookings" class="btn btn-outlined">📋 View All Bookings</a>
</div>

<?php if ($error): ?>
    <div style="background:#fce8e6; color:#c5221f; padding:12px; border-radius:8px; font-size:13px; margin-bottom:20px;">
        ⚠️ <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<form method="POST" action="/bookings/new" id="bookingForm">
    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:24px;">
        <!-- Left: Booking Form Inputs -->
        <div>
            <!-- Section 1: Docket & Routing Info -->
            <div class="g-card">
                <div style="font-weight:500; font-size:15px; margin-bottom:16px; color:var(--brand-primary); display:flex; align-items:center; gap:8px;">
                    <?= Icon::svg('tag') ?> Consignment Docket & Routing
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Booking Date</label>
                        <input type="date" name="booking_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">AWB / Consignment No. *</label>
                        <input type="text" name="consignment_no" id="consignment_no" class="form-control" value="<?= htmlspecialchars($nextAwb) ?>" required style="font-family:monospace; font-weight:bold; font-size:16px; letter-spacing:1px;">
                    </div>

                    <!-- Client Selection -->
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">B2B Corporate Client / Sender *</label>
                        <select name="client_id" id="client_id" class="form-control" required onchange="onClientSelected(this)">
                            <option value="">-- Select Client or Walk-in Customer --</option>
                            <?php foreach ($clients as $cl): ?>
                                <option value="<?= $cl['id'] ?>" data-name="<?= htmlspecialchars($cl['name']) ?>" data-phone="<?= htmlspecialchars($cl['phone']) ?>">
                                    [#<?= $cl['client_code'] ?>] <?= htmlspecialchars($cl['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Sender Phone</label>
                        <input type="tel" name="sender_phone" id="sender_phone" class="form-control" placeholder="10-digit mobile">
                    </div>

                    <!-- Destination Selection -->
                    <div class="form-group" style="grid-column: span 3;">
                        <label class="form-label">Destination City / Hub *</label>
                        <select name="destination_id" id="destination_id" class="form-control" required onchange="calculateRate()">
                            <option value="">-- Select Central Master Destination --</option>
                            <?php foreach ($destinations as $d): ?>
                                <option value="<?= $d['id'] ?>">
                                    <?= htmlspecialchars($d['name']) ?> (<?= htmlspecialchars($d['code']) ?>) - <?= htmlspecialchars($d['state_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 2: Consignee / Recipient Info -->
            <div class="g-card">
                <div style="font-weight:500; font-size:15px; margin-bottom:16px; color:var(--brand-primary); display:flex; align-items:center; gap:8px;">
                    <?= Icon::svg('destinations') ?> Consignee (Recipient Details)
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Recipient Name / Company *</label>
                        <input type="text" name="recipient_name" id="recipient_name" class="form-control" placeholder="Recipient Name" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Recipient Mobile / Phone *</label>
                        <input type="tel" name="recipient_phone" id="recipient_phone" class="form-control" placeholder="10-digit phone" required>
                    </div>

                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Delivery Street Address *</label>
                        <textarea name="recipient_address" id="recipient_address" class="form-control" rows="2" placeholder="Complete destination address, landmark, area" required></textarea>
                    </div>
                </div>
            </div>

            <!-- Section 3: Package Mode & Volumetric Dimensions -->
            <div class="g-card">
                <div style="font-weight:500; font-size:15px; margin-bottom:16px; color:var(--brand-primary); display:flex; align-items:center; gap:8px;">
                    <?= Icon::svg('truck') ?> Weight & Package Specifications
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label class="form-label">Mode (Package Type) *</label>
                        <select name="mode" id="mode" class="form-control" required onchange="calculateRate()">
                            <option value="Dox" selected>Dox (Documents)</option>
                            <option value="Non-Dox">Non-Dox (Parcel / Goods)</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Send By (Transport) *</label>
                        <select name="send_by" id="send_by" class="form-control" required onchange="calculateRate()">
                            <option value="Air" selected>Air Express</option>
                            <option value="Surface">Surface Cargo</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">No. of Pieces *</label>
                        <input type="number" name="pieces" id="pieces" class="form-control" value="1" min="1" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Dead Weight (KG) *</label>
                        <input type="number" step="0.01" name="dead_weight" id="dead_weight" class="form-control" value="0.5" required oninput="onWeightChange()">
                    </div>

                    <div class="form-group" style="grid-column:span 2;">
                        <label class="form-label">Dimensions (L × W × H in cm)</label>
                        <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:8px;">
                            <input type="number" step="1" name="length_cm" id="dim_l" class="form-control" placeholder="L cm" oninput="onWeightChange()">
                            <input type="number" step="1" name="width_cm" id="dim_w" class="form-control" placeholder="W cm" oninput="onWeightChange()">
                            <input type="number" step="1" name="height_cm" id="dim_h" class="form-control" placeholder="H cm" oninput="onWeightChange()">
                        </div>
                    </div>
                </div>

                <!-- Live Volumetric vs Dead Weight Comparison Pill -->
                <div id="weightSummaryBadge" style="background:#f1f3f4; border-radius:8px; padding:10px 14px; font-size:13px; display:flex; justify-content:space-between; align-items:center; margin-top:8px;">
                    <div>Dead Weight: <strong id="lblDead">0.50 kg</strong> | Volumetric: <strong id="lblVol">—</strong></div>
                    <div>Billable Weight: <span class="g-chip chip-info" id="lblBillable">0.50 kg</span></div>
                </div>
            </div>
        </div>

        <!-- Right: Live Billing Summary Sidebar -->
        <div>
            <div class="g-card" style="position:sticky; top:80px;">
                <h3 style="font-size:16px; margin-bottom:16px; border-bottom:1px solid #f1f3f4; padding-bottom:10px; display:flex; align-items:center; gap:8px;">
                    <?= Icon::svg('invoices') ?> Fare & Charges Breakdown
                </h3>

                <div style="font-size:13px; display:flex; flex-direction:column; gap:12px;">
                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-secondary);">Tariff Zone:</span>
                        <strong id="lblZone">Central Zone</strong>
                    </div>

                    <div style="display:flex; justify-content:space-between;">
                        <span style="color:var(--text-secondary);">Freight Subtotal:</span>
                        <span style="font-size:16px; font-weight:600;">₹ <span id="lblSubtotal">50.00</span></span>
                    </div>
                    <input type="hidden" name="subtotal" id="input_subtotal" value="50.00">

                    <div style="font-size:11px; color:var(--text-secondary); background:#f8f9fa; padding:6px 10px; border-radius:4px;" id="lblTariffNote">
                        Standard rate applied
                    </div>

                    <!-- Discount -->
                    <div style="border-top:1px solid #f1f3f4; padding-top:12px;">
                        <label class="form-label">Discount</label>
                        <div style="display:grid; grid-template-columns: 80px 1fr; gap:8px;">
                            <select name="discount_type" id="discount_type" class="form-control" onchange="calculateFinalTotal()">
                                <option value="fixed">₹ (Fixed)</option>
                                <option value="percent">% (Pct)</option>
                            </select>
                            <input type="number" step="0.5" name="discount_value" id="discount_value" class="form-control" value="0" oninput="calculateFinalTotal()">
                        </div>
                    </div>

                    <!-- Surcharges & Insured -->
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px;">
                        <div>
                            <label class="form-label">Insured Value (₹)</label>
                            <input type="number" step="100" name="insured_value" class="form-control" value="0">
                        </div>
                        <div>
                            <label class="form-label">Other Charges (₹)</label>
                            <input type="number" step="5" name="other_charges" id="other_charges" class="form-control" value="0" oninput="calculateFinalTotal()">
                        </div>
                    </div>

                    <!-- Grand Total Banner -->
                    <div style="background:var(--google-blue-light); border-radius:12px; padding:16px; margin-top:8px; text-align:center;">
                        <div style="font-size:12px; color:var(--google-blue); font-weight:600; text-transform:uppercase;">Grand Billable Amount</div>
                        <div style="font-size:32px; font-weight:700; color:var(--google-blue); margin-top:4px;">
                            ₹ <span id="lblTotal">50.00</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width:100%; padding:14px; border-radius:24px; font-size:15px; margin-top:12px; display:inline-flex; align-items:center; justify-content:center; gap:8px;">
                        <?= Icon::svg('check', '', 18) ?> Confirm Booking & Print Label <?= Icon::svg('arrow-right', '', 16) ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
function onClientSelected(sel) {
    var opt = sel.options[sel.selectedIndex];
    if (opt && opt.dataset.phone) {
        document.getElementById('sender_phone').value = opt.dataset.phone;
    }
    calculateRate();
}

function onWeightChange() {
    var dead = parseFloat(document.getElementById('dead_weight').value) || 0.1;
    var l = parseFloat(document.getElementById('dim_l').value) || 0;
    var w = parseFloat(document.getElementById('dim_w').value) || 0;
    var h = parseFloat(document.getElementById('dim_h').value) || 0;

    var vol = (l > 0 && w > 0 && h > 0) ? (l * w * h) / 5000 : 0;
    var billable = Math.max(dead, vol);

    document.getElementById('lblDead').innerText = dead.toFixed(2) + ' kg';
    document.getElementById('lblVol').innerText = vol > 0 ? vol.toFixed(2) + ' kg' : '—';
    document.getElementById('lblBillable').innerText = billable.toFixed(2) + ' kg';

    calculateRate();
}

function calculateRate() {
    var clientId = document.getElementById('client_id').value;
    var destId = document.getElementById('destination_id').value;
    var mode = document.getElementById('mode').value;
    var sendBy = document.getElementById('send_by').value;

    var dead = parseFloat(document.getElementById('dead_weight').value) || 0.1;
    var l = parseFloat(document.getElementById('dim_l').value) || 0;
    var w = parseFloat(document.getElementById('dim_w').value) || 0;
    var h = parseFloat(document.getElementById('dim_h').value) || 0;
    var vol = (l > 0 && w > 0 && h > 0) ? (l * w * h) / 5000 : 0;
    var billable = Math.max(dead, vol);

    if (!destId) return;

    var url = '/api/tariffs/calculate?client_id=' + encodeURIComponent(clientId) +
              '&destination_id=' + encodeURIComponent(destId) +
              '&mode=' + encodeURIComponent(mode) +
              '&send_by=' + encodeURIComponent(sendBy) +
              '&weight=' + encodeURIComponent(billable);

    fetch(url)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('lblSubtotal').innerText = parseFloat(data.subtotal).toFixed(2);
                document.getElementById('input_subtotal').value = data.subtotal;
                document.getElementById('lblZone').innerText = data.zone_name;
                document.getElementById('lblTariffNote').innerText = data.breakdown;
                calculateFinalTotal();
            }
        });
}

function calculateFinalTotal() {
    var sub = parseFloat(document.getElementById('input_subtotal').value) || 0;
    var dType = document.getElementById('discount_type').value;
    var dVal = parseFloat(document.getElementById('discount_value').value) || 0;
    var other = parseFloat(document.getElementById('other_charges').value) || 0;

    var discount = (dType === 'percent') ? (sub * dVal) / 100 : Math.min(sub, dVal);
    var grand = Math.max(0, (sub - discount) + other);

    document.getElementById('lblTotal').innerText = grand.toFixed(2);
}

// Auto calculate on initial load
calculateRate();
</script>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/src/Views/layout.php';
?>

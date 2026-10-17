<?php
// src/Views/invoices/create.php - Generate B2B GST Tax Invoice Wizard
$pageTitle = 'Generate GST Invoice';
$activeNav = 'invoices';

$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error']);

ob_start();
?>

<div class="page-header">
    <div>
        <a href="/invoices" style="font-size:13px; color:var(--text-secondary); text-decoration:none;">← Back to Invoices</a>
        <h1 class="page-title" style="margin-top:6px;">🧾 Generate B2B GST Tax Invoice</h1>
        <p style="color:var(--text-secondary); font-size:13px; margin-top:4px;">
            Select a corporate client to aggregate unbilled consignments, apply contractual fuel surcharge, and generate GST invoice.
        </p>
    </div>
</div>

<?php if ($flashError): ?>
    <div style="background:#fce8e6; border-left:4px solid var(--google-red); color:#c5221f; padding:12px 18px; border-radius:4px; margin-bottom:20px; font-size:13px;">
        <?= htmlspecialchars($flashError) ?>
    </div>
<?php endif; ?>

<!-- Step 1: Filter Unbilled Consignments -->
<div class="g-card" style="margin-bottom:24px; padding:20px;">
    <h2 style="font-size:15px; margin-bottom:14px;">1. Select Client & Billing Cycle</h2>
    <form method="GET" action="/invoices/new" style="display:grid; grid-template-columns: 2fr 1fr 1fr auto; gap:14px; align-items:end;">
        <div>
            <label class="form-label">Corporate Client *</label>
            <select name="client_id" class="form-control" onchange="this.form.submit()" required>
                <option value="">-- Choose Corporate Client --</option>
                <?php foreach ($clients as $cl): ?>
                    <option value="<?= $cl['id'] ?>" <?= ($clientId == $cl['id']) ? 'selected' : '' ?>>
                        #<?= htmlspecialchars($cl['client_code']) ?> - <?= htmlspecialchars($cl['name']) ?> (GSTIN: <?= htmlspecialchars($cl['gstin'] ?: 'Unregistered') ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="form-label">Period From</label>
            <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($dateFrom) ?>">
        </div>
        <div>
            <label class="form-label">Period To</label>
            <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($dateTo) ?>">
        </div>
        <div>
            <button type="submit" class="btn btn-secondary" style="height:38px;">Fetch Unbilled</button>
        </div>
    </form>
</div>

<?php if ($selectedClient): ?>

    <form method="POST" action="/invoices" id="generateInvoiceForm">
        <input type="hidden" name="client_id" value="<?= $selectedClient['id'] ?>">
        <input type="hidden" name="period_from" value="<?= htmlspecialchars($dateFrom) ?>">
        <input type="hidden" name="period_to" value="<?= htmlspecialchars($dateTo) ?>">

        <div style="display:grid; grid-template-columns: 2fr 1fr; gap:24px; align-items:start;">
            
            <!-- Left Column: Unbilled Consignments Selection -->
            <div>
                <div class="g-card" style="padding:0; overflow:hidden;">
                    <div style="padding:16px 20px; border-bottom:1px solid #dadce0; display:flex; justify-content:space-between; align-items:center; background:#f8f9fa;">
                        <div>
                            <span style="font-weight:700; font-size:14px;">
                                Ready Unbilled Consignments (<?= count($unbilledConsignments) ?> found)
                            </span>
                            <div style="font-size:12px; color:var(--text-secondary); margin-top:2px;">
                                Client: <strong><?= htmlspecialchars($selectedClient['name']) ?></strong> (Code #<?= htmlspecialchars($selectedClient['client_code']) ?>)
                            </div>
                        </div>
                        <div style="font-size:13px;">
                            <label style="display:flex; align-items:center; gap:6px; cursor:pointer;">
                                <input type="checkbox" id="selectAllConsignments" checked>
                                <strong>Select All</strong>
                            </label>
                        </div>
                    </div>

                    <?php if (empty($unbilledConsignments)): ?>
                        <div style="text-align:center; padding:40px; color:var(--text-secondary);">
                            ✓ No unbilled consignments found for this client in the selected date range.
                        </div>
                    <?php else: ?>
                        <div style="max-height:450px; overflow-y:auto;">
                            <table class="g-table">
                                <thead>
                                    <tr>
                                        <th style="width:30px;"></th>
                                        <th>Date</th>
                                        <th>AWB #</th>
                                        <th>Destination</th>
                                        <th>Mode</th>
                                        <th>Billable Wt</th>
                                        <th style="text-align:right;">Freight (₹)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($unbilledConsignments as $c): ?>
                                        <tr>
                                            <td>
                                                <input 
                                                    type="checkbox" 
                                                    name="consignment_ids[]" 
                                                    value="<?= $c['id'] ?>" 
                                                    data-amount="<?= $c['total_amount'] ?>" 
                                                    class="consignment-cb" 
                                                    checked
                                                >
                                            </td>
                                            <td><?= date('d-M-Y', strtotime($c['booking_date'])) ?></td>
                                            <td>
                                                <span style="font-family:monospace; font-weight:700;">
                                                    <?= htmlspecialchars($c['consignment_no']) ?>
                                                </span>
                                            </td>
                                            <td><?= htmlspecialchars($c['destination_name']) ?> (<?= htmlspecialchars($c['dest_code']) ?>)</td>
                                            <td><?= htmlspecialchars($c['send_by']) ?> / <?= htmlspecialchars($c['mode']) ?></td>
                                            <td><?= number_format($c['billable_weight'], 2) ?> kg</td>
                                            <td style="text-align:right; font-weight:600;">
                                                ₹<?= number_format($c['total_amount'], 2) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Column: Invoice Parameters & Live Calculation -->
            <div>
                <div class="g-card">
                    <h2 style="font-size:16px; margin-bottom:16px;">Invoice Parameters</h2>

                    <div class="form-group">
                        <label class="form-label">Invoice Date *</label>
                        <input type="date" name="invoice_date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Payment Due Date *</label>
                        <input type="date" name="due_date" class="form-control" value="<?= date('Y-m-d', strtotime('+15 days')) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Settlement Bank Account *</label>
                        <select name="bank_account_id" class="form-control" required>
                            <?php foreach ($bankAccounts as $acc): ?>
                                <option value="<?= $acc['id'] ?>" <?= $acc['is_primary'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($acc['bank_name']) ?> (A/C: <?= htmlspecialchars($acc['account_no']) ?>) <?= $acc['is_primary'] ? '★' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                        <div class="form-group">
                            <label class="form-label">Fuel Surcharge (%)</label>
                            <input type="number" step="0.5" min="0" max="100" name="fuel_surcharge_percent" id="fuelPercentInput" class="form-control" value="10.0">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Docket Charges (₹)</label>
                            <input type="number" step="1" min="0" name="docket_charge_amount" id="docketChargeInput" class="form-control" value="50.0">
                        </div>
                    </div>

                    <!-- Live Calculation Summary Card -->
                    <div style="background:#f8f9fa; border-radius:8px; padding:16px; margin-top:16px; font-size:13px; display:flex; flex-direction:column; gap:8px;">
                        <div style="display:flex; justify-content:space-between;">
                            <span style="color:var(--text-secondary);">Selected Shipments:</span>
                            <strong id="calcShipmentCount">0</strong>
                        </div>
                        <div style="display:flex; justify-content:space-between;">
                            <span style="color:var(--text-secondary);">Freight Subtotal:</span>
                            <span id="calcFreightSubtotal">₹0.00</span>
                        </div>
                        <div style="display:flex; justify-content:space-between;">
                            <span style="color:var(--text-secondary);">Fuel Surcharge:</span>
                            <span id="calcFuelAmount">₹0.00</span>
                        </div>
                        <div style="display:flex; justify-content:space-between;">
                            <span style="color:var(--text-secondary);">Docket Fees:</span>
                            <span id="calcDocketAmount">₹0.00</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; border-top:1px solid #dadce0; padding-top:6px; font-weight:600;">
                            <span>Taxable Value (SAC 996812):</span>
                            <span id="calcTaxableAmount">₹0.00</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; color:var(--google-blue);">
                            <span>Estimated GST (18%):</span>
                            <span id="calcGstAmount">₹0.00</span>
                        </div>
                        <div style="display:flex; justify-content:space-between; border-top:2px solid var(--border-color); padding-top:8px; font-size:16px; font-weight:700;">
                            <span>Grand Total:</span>
                            <span style="color:var(--google-blue);" id="calcGrandTotal">₹0.00</span>
                        </div>
                    </div>

                    <button 
                        type="submit" 
                        id="submitInvoiceBtn" 
                        class="btn btn-primary" 
                        style="width:100%; justify-content:center; margin-top:20px; padding:12px;"
                        <?= empty($unbilledConsignments) ? 'disabled' : '' ?>
                    >
                        🧾 Issue Tax Invoice
                    </button>
                </div>
            </div>

        </div>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const selectAll = document.getElementById('selectAllConsignments');
            const checkboxes = document.querySelectorAll('.consignment-cb');
            const fuelInput = document.getElementById('fuelPercentInput');
            const docketInput = document.getElementById('docketChargeInput');

            const countEl = document.getElementById('calcShipmentCount');
            const freightEl = document.getElementById('calcFreightSubtotal');
            const fuelEl = document.getElementById('calcFuelAmount');
            const docketEl = document.getElementById('calcDocketAmount');
            const taxableEl = document.getElementById('calcTaxableAmount');
            const gstEl = document.getElementById('calcGstAmount');
            const grandEl = document.getElementById('calcGrandTotal');
            const submitBtn = document.getElementById('submitInvoiceBtn');

            function recalculate() {
                let subtotal = 0;
                let count = 0;

                checkboxes.forEach(cb => {
                    if (cb.checked) {
                        subtotal += parseFloat(cb.dataset.amount || 0);
                        count++;
                    }
                });

                const fuelPercent = parseFloat(fuelInput.value || 0);
                const docketCharge = parseFloat(docketInput.value || 0);

                const fuelAmount = subtotal * (fuelPercent / 100);
                const taxable = subtotal + fuelAmount + docketCharge;
                const gst = taxable * 0.18;
                const grandTotal = Math.round(taxable + gst);

                countEl.textContent = count;
                freightEl.textContent = '₹' + subtotal.toFixed(2);
                fuelEl.textContent = '₹' + fuelAmount.toFixed(2);
                docketEl.textContent = '₹' + docketCharge.toFixed(2);
                taxableEl.textContent = '₹' + taxable.toFixed(2);
                gstEl.textContent = '₹' + gst.toFixed(2);
                grandEl.textContent = '₹' + grandTotal.toFixed(2);

                if (submitBtn) {
                    submitBtn.disabled = (count === 0);
                }
            }

            if (selectAll) {
                selectAll.addEventListener('change', () => {
                    checkboxes.forEach(cb => cb.checked = selectAll.checked);
                    recalculate();
                });
            }

            checkboxes.forEach(cb => {
                cb.addEventListener('change', () => {
                    recalculate();
                    if (!cb.checked && selectAll) selectAll.checked = false;
                });
            });

            if (fuelInput) fuelInput.addEventListener('input', recalculate);
            if (docketInput) docketInput.addEventListener('input', recalculate);

            recalculate();
        });
    </script>

<?php endif; ?>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/src/Views/layout.php';
?>

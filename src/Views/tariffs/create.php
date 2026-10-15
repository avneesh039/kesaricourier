<?php
// src/Views/tariffs/create.php - Create New Rate Matrix Tariff
$pageTitle = 'Add Rate Matrix';
$activeNav = 'tariffs';

ob_start();
?>

<div class="page-header">
    <div>
        <a href="/tariffs" style="font-size:13px; color:var(--text-secondary); text-decoration:none;">← Back to Rate Matrix</a>
        <h1 class="page-title" style="margin-top:6px;">Define New Tariff / Pricing Matrix</h1>
    </div>
</div>

<div class="g-card" style="max-width:800px;">
    <form method="POST" action="/tariffs/new">
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:18px; margin-bottom:24px;">
            <div class="form-group" style="grid-column: span 2;">
                <label class="form-label">Client Scope</label>
                <select name="client_id" class="form-control">
                    <option value="">★ Default Branch Tariff (Applies to all clients without custom rates)</option>
                    <?php foreach ($clients as $c): ?>
                        <option value="<?= $c['id'] ?>">Contract Client: <?= htmlspecialchars($c['name']) ?> (#<?= htmlspecialchars($c['client_code']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Send By (Transport Mode) *</label>
                <select name="send_by" class="form-control" required>
                    <option value="Air">Air Express</option>
                    <option value="Surface">Surface Cargo</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Package Mode *</label>
                <select name="mode" class="form-control" required>
                    <option value="Dox">Dox (Documents)</option>
                    <option value="Non-Dox">Non-Dox (Parcel / Goods)</option>
                </select>
            </div>

            <div class="form-group" style="grid-column: span 2;">
                <label class="form-label">Destination Operational Zone *</label>
                <select name="zone_id" class="form-control" required>
                    <?php foreach ($zones as $z): ?>
                        <option value="<?= $z['id'] ?>"><?= htmlspecialchars($z['name']) ?> (<?= htmlspecialchars($z['code']) ?> - <?= htmlspecialchars($z['description'] ?? '') ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Weight Slabs Grid -->
        <div style="background:#f8f9fa; border:1px solid #dadce0; border-radius:8px; padding:20px; margin-bottom:24px;">
            <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px;">
                <h3 style="font-size:15px;">Weight Slabs & Base Rates</h3>
                <button type="button" class="btn btn-outlined" onclick="addSlabRow()" style="padding:4px 12px; font-size:12px;">+ Add Weight Slab Row</button>
            </div>

            <div id="slabsContainer">
                <div class="slab-row" style="display:grid; grid-template-columns: 1fr 1fr 1fr 40px; gap:12px; margin-bottom:10px;">
                    <div>
                        <label class="form-label">Weight From (kg)</label>
                        <input type="number" step="0.05" name="weight_from[]" class="form-control" value="0.0" required>
                    </div>
                    <div>
                        <label class="form-label">Weight To (kg)</label>
                        <input type="number" step="0.05" name="weight_to[]" class="form-control" value="0.5" required>
                    </div>
                    <div>
                        <label class="form-label">Rate (₹)</label>
                        <input type="number" step="0.5" name="slab_rate[]" class="form-control" value="50.0" required>
                    </div>
                    <div style="display:flex; align-items:flex-end;">
                        <button type="button" class="btn btn-outlined" onclick="removeRow(this)" style="color:red; padding:8px 10px;">✕</button>
                    </div>
                </div>

                <div class="slab-row" style="display:grid; grid-template-columns: 1fr 1fr 1fr 40px; gap:12px; margin-bottom:10px;">
                    <div>
                        <input type="number" step="0.05" name="weight_from[]" class="form-control" value="0.5" required>
                    </div>
                    <div>
                        <input type="number" step="0.05" name="weight_to[]" class="form-control" value="1.0" required>
                    </div>
                    <div>
                        <input type="number" step="0.5" name="slab_rate[]" class="form-control" value="80.0" required>
                    </div>
                    <div>
                        <button type="button" class="btn btn-outlined" onclick="removeRow(this)" style="color:red; padding:8px 10px;">✕</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Incremental Weight Surcharge -->
        <div style="background:#f8f9fa; border:1px solid #dadce0; border-radius:8px; padding:20px; margin-bottom:24px;">
            <h3 style="font-size:15px; margin-bottom:12px;">Excess Weight Surcharge (Beyond Highest Slab)</h3>
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                <div class="form-group">
                    <label class="form-label">Additional Weight Step (kg)</label>
                    <input type="number" step="0.1" name="additional_weight_step" class="form-control" value="0.5" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Additional Step Rate (₹)</label>
                    <input type="number" step="0.5" name="additional_weight_rate" class="form-control" value="30.0" required>
                </div>
            </div>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center;">
            <a href="/tariffs" class="btn btn-outlined">Cancel</a>
            <button type="submit" class="btn btn-primary" style="padding:10px 24px;">Save Rate Matrix</button>
        </div>
    </form>
</div>

<script>
function addSlabRow() {
    var container = document.getElementById('slabsContainer');
    var div = document.createElement('div');
    div.className = 'slab-row';
    div.style = 'display:grid; grid-template-columns: 1fr 1fr 1fr 40px; gap:12px; margin-bottom:10px;';
    div.innerHTML = `
        <div><input type="number" step="0.05" name="weight_from[]" class="form-control" placeholder="From kg" required></div>
        <div><input type="number" step="0.05" name="weight_to[]" class="form-control" placeholder="To kg" required></div>
        <div><input type="number" step="0.5" name="slab_rate[]" class="form-control" placeholder="Rate ₹" required></div>
        <div><button type="button" class="btn btn-outlined" onclick="removeRow(this)" style="color:red; padding:8px 10px;">✕</button></div>
    `;
    container.appendChild(div);
}

function removeRow(btn) {
    var row = btn.closest('.slab-row');
    row.remove();
}
</script>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/src/Views/layout.php';
?>

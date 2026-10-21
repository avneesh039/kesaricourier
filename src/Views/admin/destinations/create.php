<?php
// src/Views/admin/destinations/create.php - Add New Master Destination
$pageTitle = 'Add Master Destination';
$activeNav = 'destinations';
$error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error']);

ob_start();
?>

<div class="page-header">
    <div>
        <a href="/admin/destinations" style="font-size:13px; color:var(--text-secondary); text-decoration:none;">← Back to Master Destinations</a>
        <h1 class="page-title" style="margin-top:6px;">Add New Master Delivery Destination</h1>
    </div>
</div>

<div class="g-card" style="max-width:640px;">
    <?php if ($error): ?>
        <div style="background:#fce8e6; color:#c5221f; padding:12px; border-radius:8px; font-size:13px; margin-bottom:20px;">
            ⚠️ <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/admin/destinations/new">
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
            <div class="form-group" style="grid-column:span 2;">
                <label class="form-label">City / Destination Name *</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. JABALPUR" required style="text-transform:uppercase;">
            </div>

            <div class="form-group">
                <label class="form-label">Destination Code (3-4 Letters) *</label>
                <input type="text" name="code" class="form-control" placeholder="e.g. JLR" maxlength="6" required style="text-transform:uppercase;">
            </div>

            <div class="form-group">
                <label class="form-label">Pincode</label>
                <input type="text" name="pincode" class="form-control" placeholder="e.g. 482001" maxlength="6" pattern="[0-9]{6}">
                <small style="color:var(--text-secondary); font-size:11px;">6-digit pincode. System automatically blocks duplicate entries.</small>
            </div>

            <div class="form-group" style="grid-column:span 2;">
                <label class="form-label">State & Zone *</label>
                <select name="state_id" class="form-control" required>
                    <option value="">Select State</option>
                    <?php foreach ($states as $s): ?>
                        <option value="<?= $s['id'] ?>">
                            <?= htmlspecialchars($s['name']) ?> (<?= htmlspecialchars($s['code']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group" style="grid-column:span 2;">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="is_hub" value="1">
                    <span style="font-size:13px; font-weight:500;">Classify as Primary Airport / Transshipment Hub</span>
                </label>
            </div>
        </div>

        <div style="margin-top:24px; display:flex; justify-content:space-between; align-items:center;">
            <a href="/admin/destinations" class="btn btn-outlined">Cancel</a>
            <button type="submit" class="btn btn-primary" style="padding:10px 24px;">Create Master Destination</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/src/Views/layout.php';
?>

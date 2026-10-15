<?php
// src/Views/clients/create.php - Add New B2B Client
$pageTitle = 'Add B2B Client';
$activeNav = 'clients';
$error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error']);

ob_start();
?>

<div class="page-header">
    <div>
        <a href="/clients" style="font-size:13px; color:var(--text-secondary); text-decoration:none;">← Back to Clients</a>
        <h1 class="page-title" style="margin-top:6px;">Add New Corporate B2B Client</h1>
    </div>
</div>

<div class="g-card" style="max-width:680px;">
    <?php if ($error): ?>
        <div style="background:#fce8e6; color:#c5221f; padding:12px; border-radius:8px; font-size:13px; margin-bottom:20px;">
            ⚠️ <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/clients/new">
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:18px;">
            <div class="form-group" style="grid-column: span 2;">
                <label class="form-label">Client / Company Name *</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. RELIANCE RETAIL LIMITED" required style="text-transform:uppercase;">
            </div>

            <div class="form-group">
                <label class="form-label">Contact Person Name</label>
                <input type="text" name="contact_person" class="form-control" placeholder="Account manager / contact">
            </div>

            <div class="form-group">
                <label class="form-label">Phone Number *</label>
                <input type="tel" name="phone" class="form-control" placeholder="10-digit mobile" required>
            </div>

            <div class="form-group">
                <label class="form-label">Email Address (for Invoices)</label>
                <input type="email" name="email" class="form-control" placeholder="billing@client.com">
            </div>

            <div class="form-group">
                <label class="form-label">GSTIN (15 Digits)</label>
                <input type="text" name="gstin" class="form-control" placeholder="e.g. 22AABCR2668G1ZL" maxlength="15" style="text-transform:uppercase;">
            </div>

            <div class="form-group" style="grid-column: span 2;">
                <label class="form-label">Billing Address</label>
                <textarea name="address" class="form-control" rows="2" placeholder="Full office address for GST invoices"></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">City *</label>
                <input type="text" name="city" class="form-control" placeholder="City" required>
            </div>

            <div class="form-group">
                <label class="form-label">State *</label>
                <select name="state" class="form-control" required>
                    <option value="">Select State</option>
                    <?php foreach ($states as $s): ?>
                        <option value="<?= htmlspecialchars($s['name']) ?>"><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Pincode</label>
                <input type="text" name="pincode" class="form-control" placeholder="6-digit pincode" maxlength="6">
            </div>

            <div class="form-group">
                <label class="form-label">PAN Number</label>
                <input type="text" name="pan_no" class="form-control" placeholder="10-character PAN" maxlength="10" style="text-transform:uppercase;">
            </div>
        </div>

        <div style="margin-top:24px; display:flex; justify-content:space-between; align-items:center;">
            <a href="/clients" class="btn btn-outlined">Cancel</a>
            <button type="submit" class="btn btn-primary" style="padding:10px 24px;">Create Corporate Client</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/src/Views/layout.php';
?>

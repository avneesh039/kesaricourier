<?php
// src/Views/admin/agencies/edit.php - Edit Agency Validity & Subscription Controls
$pageTitle = 'Configure Agency Subscription | ' . $tenant['name'];
$activeNav = 'agencies';

ob_start();
?>

<div class="page-header">
    <div>
        <a href="/admin/agencies" style="font-size:13px; color:var(--text-secondary); text-decoration:none;">← Back to Agency Directory</a>
        <h1 class="page-title" style="margin-top:6px;">Agency Configuration: <?= htmlspecialchars($tenant['name']) ?></h1>
    </div>
</div>

<div class="g-card" style="max-width:680px;">
    <form method="POST" action="/admin/agencies/<?= $tenant['id'] ?>/edit">
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
            <div class="form-group">
                <label class="form-label">Agency Code</label>
                <input type="text" class="form-control" value="#<?= htmlspecialchars($tenant['agency_code'] ?? 'Pending') ?>" disabled style="background:#f1f3f4;">
            </div>

            <div class="form-group">
                <label class="form-label">Subdomain Slug</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($tenant['slug']) ?>.localhost" disabled style="background:#f1f3f4;">
            </div>

            <div class="form-group">
                <label class="form-label">Subscription Start Date</label>
                <input type="date" name="validity_start" class="form-control" value="<?= htmlspecialchars($tenant['validity_start'] ?? date('Y-m-d')) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Subscription Expiry Date *</label>
                <input type="date" name="validity_end" class="form-control" value="<?= htmlspecialchars($tenant['validity_end'] ?? date('Y-m-d', strtotime('+365 days'))) ?>" required>
                <small style="color:var(--text-secondary); font-size:11px;">Agency portal shows warnings 30 days prior to this date.</small>
            </div>

            <div class="form-group">
                <label class="form-label">Portal Status Switch *</label>
                <select name="status" class="form-control" required>
                    <option value="active" <?= ($tenant['status'] ?? '') === 'active' ? 'selected' : '' ?>>Active (Full Access)</option>
                    <option value="suspended" <?= ($tenant['status'] ?? '') === 'suspended' ? 'selected' : '' ?>>Suspended (Lock Access)</option>
                    <option value="pending_kyc" <?= ($tenant['status'] ?? '') === 'pending_kyc' ? 'selected' : '' ?>>Pending KYC</option>
                    <option value="rejected" <?= ($tenant['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Custom Brand Accent Color</label>
                <input type="color" name="brand_color" class="form-control" value="<?= htmlspecialchars($tenant['brand_color'] ?? '#1a73e8') ?>" style="height:42px; padding:4px;">
            </div>
        </div>

        <div style="margin-top:24px; display:flex; justify-content:space-between; align-items:center;">
            <a href="/admin/agencies" class="btn btn-outlined">Cancel</a>
            <button type="submit" class="btn btn-primary" style="padding:10px 24px;">Save Subscription Settings</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/src/Views/layout.php';
?>

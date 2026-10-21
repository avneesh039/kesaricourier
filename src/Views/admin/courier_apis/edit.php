<?php
// src/Views/admin/courier_apis/edit.php - Edit Courier API Integration
$pageTitle = 'Configure Courier API | ' . $api['courier_name'];
$activeNav = 'courier_apis';

ob_start();
?>

<div class="page-header">
    <div>
        <a href="/admin/courier-apis" style="font-size:13px; color:var(--text-secondary); text-decoration:none;">← Back to Courier Gateway Directory</a>
        <h1 class="page-title" style="margin-top:6px;">⚡ Configure API: <?= htmlspecialchars($api['courier_name']) ?></h1>
        <p style="color:var(--text-secondary); font-size:13px; margin-top:4px;">
            Carrier Code: <code><?= htmlspecialchars($api['courier_code']) ?></code> • Status: <strong><?= strtoupper($api['status']) ?></strong>
        </p>
    </div>
    <div style="display:flex; gap:10px;">
        <form method="POST" action="/admin/courier-apis/<?= $api['id'] ?>/test" style="display:inline;">
            <button type="submit" class="btn btn-outlined">⚡ Send Test Ping</button>
        </form>
    </div>
</div>

<div class="g-card" style="max-width:720px;">
    <form method="POST" action="/admin/courier-apis/<?= $api['id'] ?>/edit">
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
            <div class="form-group" style="grid-column:span 2;">
                <label class="form-label">Courier Partner Name *</label>
                <input type="text" name="courier_name" class="form-control" value="<?= htmlspecialchars($api['courier_name']) ?>" required>
            </div>

            <div class="form-group">
                <label class="form-label">Unique Carrier Code</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($api['courier_code']) ?>" disabled style="background:#f1f3f4;">
            </div>

            <div class="form-group">
                <label class="form-label">Client / Merchant Account ID</label>
                <input type="text" name="client_id" class="form-control" value="<?= htmlspecialchars($api['client_id'] ?? '') ?>">
            </div>

            <div class="form-group" style="grid-column:span 2;">
                <label class="form-label">API Base Endpoint URL *</label>
                <input type="url" name="api_endpoint" class="form-control" value="<?= htmlspecialchars($api['api_endpoint']) ?>" required>
            </div>

            <div class="form-group" style="grid-column:span 2;">
                <label class="form-label">Public Tracking Redirect URL Template</label>
                <input type="text" name="tracking_url_template" class="form-control" value="<?= htmlspecialchars($api['tracking_url_template'] ?? '') ?>">
                <small style="color:var(--text-secondary); font-size:11px;">Use <code>{awb}</code> as the placeholder for the consignment tracking number.</small>
            </div>

            <div class="form-group">
                <label class="form-label">API Key / Bearer Token</label>
                <input type="text" name="api_key" class="form-control" value="<?= htmlspecialchars($api['api_key'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label class="form-label">API Secret / Password</label>
                <input type="password" name="api_secret" class="form-control" value="<?= htmlspecialchars($api['api_secret'] ?? '') ?>">
            </div>

            <div class="form-group" style="grid-column:span 2;">
                <label class="form-label">Internal Operational Notes</label>
                <textarea name="notes" class="form-control" rows="2"><?= htmlspecialchars($api['notes'] ?? '') ?></textarea>
            </div>

            <div class="form-group" style="grid-column:span 2;">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" <?= !empty($api['is_active']) ? 'checked' : '' ?>>
                    <span style="font-size:13px; font-weight:500;">Enable & Activate this Courier API Gateway</span>
                </label>
            </div>
        </div>

        <div style="margin-top:24px; display:flex; justify-content:space-between; align-items:center;">
            <a href="/admin/courier-apis" class="btn btn-outlined">Cancel</a>
            <button type="submit" class="btn btn-primary" style="padding:10px 24px;">Save Configuration</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/src/Views/layout.php';
?>

<?php
// src/Views/admin/courier_apis/create.php - Add New Courier API Integration
$pageTitle = 'Add Courier API Partner';
$activeNav = 'courier_apis';

$error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error']);

ob_start();
?>

<div class="page-header">
    <div>
        <a href="/admin/courier-apis" style="font-size:13px; color:var(--text-secondary); text-decoration:none;">← Back to Courier Gateway Directory</a>
        <h1 class="page-title" style="margin-top:6px;">⚡ Connect New Courier API Partner</h1>
        <p style="color:var(--text-secondary); font-size:13px; margin-top:4px;">
            Configure API endpoint, authentication headers, and tracking templates for automatic carrier sync.
        </p>
    </div>
</div>

<?php if ($error): ?>
    <div style="background:#fce8e6; border-left:4px solid var(--google-red); color:#c5221f; padding:12px 18px; border-radius:4px; margin-bottom:20px; font-size:13px;">
        ⚠️ <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="g-card" style="max-width:720px;">
    <form method="POST" action="/admin/courier-apis/new">
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
            <div class="form-group" style="grid-column:span 2;">
                <label class="form-label">Courier Partner Name *</label>
                <input type="text" name="courier_name" class="form-control" placeholder="e.g. DTDC Express Tracking API" required>
            </div>

            <div class="form-group">
                <label class="form-label">Unique Carrier Code / Slug *</label>
                <input type="text" name="courier_code" class="form-control" placeholder="e.g. dtdc" required pattern="[a-z0-9\-]+">
            </div>

            <div class="form-group">
                <label class="form-label">Client / Merchant Account ID</label>
                <input type="text" name="client_id" class="form-control" placeholder="e.g. DTDC_CLIENT_102">
            </div>

            <div class="form-group" style="grid-column:span 2;">
                <label class="form-label">API Base Endpoint URL *</label>
                <input type="url" name="api_endpoint" class="form-control" placeholder="https://api.courier.com/v1/track" required>
            </div>

            <div class="form-group" style="grid-column:span 2;">
                <label class="form-label">Public Tracking Redirect URL Template</label>
                <input type="text" name="tracking_url_template" class="form-control" placeholder="https://carrier.com/track/{awb}">
                <small style="color:var(--text-secondary); font-size:11px;">Use <code>{awb}</code> as the placeholder for the consignment tracking number.</small>
            </div>

            <div class="form-group">
                <label class="form-label">API Key / Bearer Token</label>
                <input type="text" name="api_key" class="form-control" placeholder="Enter API Key / Token">
            </div>

            <div class="form-group">
                <label class="form-label">API Secret / Password</label>
                <input type="password" name="api_secret" class="form-control" placeholder="Enter API Secret">
            </div>

            <div class="form-group" style="grid-column:span 2;">
                <label class="form-label">Internal Operational Notes</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="SLA details, point of contact, or special packaging notes"></textarea>
            </div>

            <div class="form-group" style="grid-column:span 2;">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer;">
                    <input type="checkbox" name="is_active" value="1" checked>
                    <span style="font-size:13px; font-weight:500;">Enable & Activate this Courier API Gateway</span>
                </label>
            </div>
        </div>

        <div style="margin-top:24px; display:flex; justify-content:space-between; align-items:center;">
            <a href="/admin/courier-apis" class="btn btn-outlined">Cancel</a>
            <button type="submit" class="btn btn-primary" style="padding:10px 24px;">Save & Register Courier API</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/src/Views/layout.php';
?>

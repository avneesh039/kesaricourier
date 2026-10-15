<?php
// src/Views/tickets/agency-create.php - Agency Ticket Creation Form
$pageTitle = 'Request New Destination / Support';
$activeNav = 'tickets';
$error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error']);

ob_start();
?>

<div class="page-header">
    <div>
        <a href="/tickets" style="font-size:13px; color:var(--text-secondary); text-decoration:none;">← Back to Support Tickets</a>
        <h1 class="page-title" style="margin-top:6px;">Submit Destination Request / Support Ticket</h1>
    </div>
</div>

<div style="display:grid; grid-template-columns: 2fr 1fr; gap:24px;">
    <!-- Main Ticket Form -->
    <div class="g-card">
        <?php if ($error): ?>
            <div style="background:#fce8e6; color:#c5221f; padding:12px; border-radius:8px; font-size:13px; margin-bottom:20px;">
                ⚠️ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/tickets/new">
            <div class="form-group">
                <label class="form-label">Ticket Purpose *</label>
                <select name="category" class="form-control" id="ticketCategory" onchange="toggleCategoryFields(this.value)">
                    <option value="destination_request" selected>📍 Request New Delivery City / Destination</option>
                    <option value="billing">💳 Billing & Invoice Query</option>
                    <option value="technical">⚙️ Technical / Portal Support</option>
                    <option value="general">💬 General Operational Query</option>
                </select>
            </div>

            <!-- Destination Specific Fields -->
            <div id="destFields" style="background:#f8f9fa; border:1px solid #e8eaed; border-radius:8px; padding:18px; margin-bottom:18px;">
                <div style="font-weight:500; font-size:14px; margin-bottom:12px; color:var(--google-blue);">
                    📍 Proposed Delivery Location
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">City / Destination Name *</label>
                        <input type="text" name="destination_name" class="form-control" placeholder="e.g. KANKER or KAWARDHA" style="text-transform:uppercase;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">State *</label>
                        <select name="state_id" class="form-control">
                            <option value="">Select State</option>
                            <?php foreach ($states as $s): ?>
                                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Pincode</label>
                        <input type="text" name="pincode" class="form-control" placeholder="6-digit pincode" maxlength="6" pattern="[0-9]{6}">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Subject / Title</label>
                <input type="text" name="subject" class="form-control" placeholder="Brief summary of request">
            </div>

            <div class="form-group">
                <label class="form-label">Details / Commercial Justification *</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Mention expected client booking volume, urgency, or specific delivery address details..." required></textarea>
            </div>

            <div class="form-group">
                <label class="form-label">Priority</label>
                <select name="priority" class="form-control">
                    <option value="normal" selected>Normal</option>
                    <option value="high">High (Commercial Booking Waiting)</option>
                    <option value="urgent">Urgent</option>
                </select>
            </div>

            <div style="margin-top:24px; display:flex; justify-content:space-between; align-items:center;">
                <a href="/tickets" class="btn btn-outlined">Cancel</a>
                <button type="submit" class="btn btn-primary" style="padding:10px 24px;">Submit Ticket to Administration ➔</button>
            </div>
        </form>
    </div>

    <!-- Omnichannel Quick Connect Panel -->
    <div>
        <div class="g-card">
            <h2 style="font-size:15px; margin-bottom:12px;">Immediate Assistance</h2>
            <p style="font-size:12px; color:var(--text-secondary); line-height:1.5; margin-bottom:18px;">
                Need urgent destination activation for an active dispatch counter customer? Contact the Super Admin directly:
            </p>

            <a href="https://wa.me/919893000000?text=Hello+Admin%2C+I+urgently+need+a+new+destination+added+for+Branch+Code+<?= urlencode(TenantContext::getTenant()['agency_code'] ?? '26') ?>." target="_blank" class="btn" style="width:100%; margin-bottom:12px; background:#25D366; color:white; justify-content:center;">
                💬 WhatsApp Super Admin
            </a>

            <a href="mailto:admin@kesari.com?subject=Urgent+Destination+Request+From+Branch+<?= urlencode(TenantContext::getTenant()['agency_code'] ?? '26') ?>" class="btn btn-outlined" style="width:100%; justify-content:center;">
                ✉️ Email Admin Desk
            </a>
        </div>
    </div>
</div>

<script>
function toggleCategoryFields(val) {
    var destFields = document.getElementById('destFields');
    if (val === 'destination_request') {
        destFields.style.display = 'block';
    } else {
        destFields.style.display = 'none';
    }
}
</script>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/src/Views/layout.php';
?>

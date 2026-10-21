<?php
// src/Views/operators/create.php - Add New Agency Desk Operator
$pageTitle = 'Add New Desk Operator';
$activeNav = 'operators';

$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error']);

ob_start();
?>

<div class="page-header">
    <div>
        <a href="/operators" style="font-size:13px; color:var(--text-secondary); text-decoration:none;">← Back to Operators List</a>
        <h1 class="page-title" style="margin-top:6px;">Add New Counter Desk Operator</h1>
        <p style="color:var(--text-secondary); font-size:13px; margin-top:4px;">
            Create login credentials for booking counter staff. Operators will be able to book consignments and print labels.
        </p>
    </div>
</div>

<div class="g-card" style="max-width:620px;">
    <?php if ($flashError): ?>
        <div style="background:#fce8e6; border-left:4px solid var(--google-red); color:#c5221f; padding:12px 18px; border-radius:4px; margin-bottom:20px; font-size:13px;">
            ⚠️ <?= htmlspecialchars($flashError) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/operators/new">
        <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
            <div class="form-group" style="grid-column:span 2;">
                <label class="form-label">Operator Full Name *</label>
                <input type="text" name="name" class="form-control" placeholder="e.g. Ramesh Kumar" required>
            </div>

            <div class="form-group">
                <label class="form-label">Contact / Mobile No. *</label>
                <input type="tel" name="phone" class="form-control" placeholder="10-digit mobile" required>
            </div>

            <div class="form-group">
                <label class="form-label">Role Access</label>
                <select name="role" class="form-control">
                    <option value="agency_operator" selected>Counter Desk Operator</option>
                    <option value="agency_admin">Branch Co-Admin</option>
                </select>
            </div>

            <div class="form-group" style="grid-column:span 2;">
                <label class="form-label">Login Email (Username) *</label>
                <input type="email" name="email" class="form-control" placeholder="e.g. ramesh.raipur@kesari.com" required>
                <div style="font-size:11px; color:var(--text-secondary); margin-top:4px;">
                    This email is used to log in at <code>http://localhost:8000/login?tenant=<?= htmlspecialchars(TenantContext::getTenant()['slug'] ?? 'raipur') ?></code>
                </div>
            </div>

            <div class="form-group" style="grid-column:span 2;">
                <label class="form-label">Initial Password *</label>
                <input type="password" name="password" class="form-control" placeholder="Enter secure password" required minlength="6">
            </div>
        </div>

        <div style="margin-top:24px; display:flex; justify-content:space-between; align-items:center;">
            <a href="/operators" class="btn btn-outlined">Cancel</a>
            <button type="submit" class="btn btn-primary" style="padding:10px 24px;">Create Desk Operator</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/src/Views/layout.php';
?>

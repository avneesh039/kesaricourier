<?php
// src/Views/auth/change-password.php - Agency User Change Password
$pageTitle = 'Change Password';
$activeNav = 'changepwd';

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

ob_start();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">🔐 Change Account Password</h1>
        <p style="color:var(--text-secondary); font-size:13px; margin-top:4px;">
            Update your account password. Use a strong combination of letters, numbers, and symbols.
        </p>
    </div>
</div>

<div class="g-card" style="max-width:520px;">
    <?php if ($flashSuccess): ?>
        <div style="background:#e6f4ea; border-left:4px solid var(--google-green); color:#137333; padding:12px 18px; border-radius:4px; margin-bottom:20px; font-size:13px;">
            <?= htmlspecialchars($flashSuccess) ?>
        </div>
    <?php endif; ?>

    <?php if ($flashError): ?>
        <div style="background:#fce8e6; border-left:4px solid var(--google-red); color:#c5221f; padding:12px 18px; border-radius:4px; margin-bottom:20px; font-size:13px;">
            ⚠️ <?= htmlspecialchars($flashError) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/change-password">
        <div class="form-group" style="margin-bottom:16px;">
            <label class="form-label">Current Password *</label>
            <input type="password" name="current_password" class="form-control" placeholder="Enter current password" required>
        </div>

        <div class="form-group" style="margin-bottom:16px;">
            <label class="form-label">New Password *</label>
            <input type="password" name="new_password" class="form-control" placeholder="Minimum 6 characters" required minlength="6">
        </div>

        <div class="form-group" style="margin-bottom:24px;">
            <label class="form-label">Confirm New Password *</label>
            <input type="password" name="confirm_password" class="form-control" placeholder="Re-enter new password" required minlength="6">
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center;">
            <a href="/" class="btn btn-outlined">Cancel</a>
            <button type="submit" class="btn btn-primary" style="padding:10px 24px;">Update Password</button>
        </div>
    </form>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/src/Views/layout.php';
?>

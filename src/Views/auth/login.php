<?php
// src/Views/auth/login.php - Google Material Design 3 Login Screen

$tenant = TenantContext::getTenant();
$isAgency = ($tenant !== null);
$brandName = $isAgency ? $tenant['name'] : 'Kesari Express Control Plane';
$brandColor = $isAgency && !empty($tenant['brand_color']) ? $tenant['brand_color'] : '#1a73e8';
$csrfToken = Auth::generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in | <?= htmlspecialchars($brandName) ?></title>
    <link rel="stylesheet" href="/assets/css/google-theme.css">
    <style>
        body {
            background-color: #f0f4f9;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .login-card {
            background: #ffffff;
            border-radius: 28px;
            padding: 40px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            border: 1px solid #e1e3e1;
        }
        .login-logo {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: <?= htmlspecialchars($brandColor) ?>;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        .demo-box {
            background: #f8f9fa;
            border: 1px dashed #dadce0;
            border-radius: 8px;
            padding: 12px;
            margin-top: 24px;
            font-size: 12px;
        }
        .demo-btn {
            background: #ffffff;
            border: 1px solid #dadce0;
            border-radius: 4px;
            padding: 4px 8px;
            font-size: 11px;
            cursor: pointer;
            margin-top: 6px;
            margin-right: 6px;
        }
        .demo-btn:hover {
            background: #e8f0fe;
            border-color: #1a73e8;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-logo">
        K
    </div>

    <h1 style="font-size: 24px; font-weight: 400; margin-bottom: 8px; color:#1f1f1f;">Sign in</h1>
    <p style="font-size: 14px; color: #444746; margin-bottom: 28px;">
        to continue to <strong><?= htmlspecialchars($brandName) ?></strong>
    </p>

    <?php if (!empty($error)): ?>
        <div style="background:#fce8e6; color:#c5221f; padding:12px; border-radius:8px; font-size:13px; margin-bottom:20px;">
            ⚠️ <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/login<?= isset($_GET['tenant']) ? '?tenant=' . htmlspecialchars($_GET['tenant']) : '' ?>">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" id="email" name="email" class="form-control" placeholder="name@company.com" required autofocus>
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:24px;">
            <a href="#" style="font-size:13px; color:<?= htmlspecialchars($brandColor) ?>;">Forgot password?</a>
            <button type="submit" class="btn btn-primary" style="background-color:<?= htmlspecialchars($brandColor) ?>; padding:10px 24px; border-radius:20px;">Next</button>
        </div>
    </form>

    <div class="demo-box">
        <strong>⚡ Quick Demo Credentials:</strong>
        <div>
            <button type="button" class="demo-btn" onclick="fillCreds('admin@kesari.com', 'admin123')">Super Admin</button>
            <button type="button" class="demo-btn" onclick="fillCreds('shreemaruti@raipur.com', 'india3101')">Raipur Branch Agency</button>
        </div>
    </div>
</div>

<script>
function fillCreds(email, pass) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = pass;
}
</script>

</body>
</html>

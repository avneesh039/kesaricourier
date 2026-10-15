<?php
// src/Views/onboard/success.php - Onboarding Submission Successful Screen
$tenantId = $_SESSION['onboard_tenant_id'] ?? null;
$db = Database::getInstance();
$tenant = $tenantId ? $db->fetchOne("SELECT * FROM tenants WHERE id = ?", [$tenantId]) : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application Submitted | Kesari Logistics</title>
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
        .success-card {
            background: #ffffff;
            border-radius: 28px;
            padding: 48px;
            max-width: 520px;
            width: 100%;
            text-align: center;
            box-shadow: var(--shadow-2);
        }
    </style>
</head>
<body>

<div class="success-card">
    <div style="width:64px; height:64px; border-radius:50%; background:var(--google-green-light); color:var(--google-green); display:inline-flex; align-items:center; justify-content:center; font-size:32px; margin-bottom:20px;">
        ✓
    </div>

    <h1 style="font-size: 24px; font-weight: 500; margin-bottom: 8px;">Application Submitted!</h1>
    <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 24px;">
        Your agency registration and KYC documents for <strong><?= htmlspecialchars($tenant['name'] ?? 'Your Agency') ?></strong> have been received by Kesari Logistics Super Administration.
    </p>

    <div style="background:#f8f9fa; border:1px solid #e8eaed; border-radius:12px; padding:18px; text-align:left; margin-bottom:28px; font-size:13px;">
        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
            <span style="color:var(--text-secondary);">Application Ref:</span>
            <strong>#APP-<?= htmlspecialchars($tenant['id'] ?? '00') ?></strong>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:8px;">
            <span style="color:var(--text-secondary);">Current Status:</span>
            <span class="g-chip chip-warning">Under Admin Review</span>
        </div>
        <div style="display:flex; justify-content:space-between;">
            <span style="color:var(--text-secondary);">Registered Email:</span>
            <span><?= htmlspecialchars($tenant['email'] ?? '') ?></span>
        </div>
    </div>

    <p style="font-size:13px; color:var(--text-secondary); line-height:1.6; margin-bottom:28px;">
        Our team reviews documents within <strong>24 business hours</strong>. Upon approval, your agency code and login credentials will be dispatched to your email.
    </p>

    <a href="/login" class="btn btn-primary" style="padding:10px 24px; border-radius:20px;">Return to Portal Sign in</a>
</div>

</body>
</html>

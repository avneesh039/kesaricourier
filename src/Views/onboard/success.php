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
            padding: 24px;
        }
        .success-card {
            background: #ffffff;
            border-radius: 28px;
            padding: 48px 40px;
            max-width: 540px;
            width: 100%;
            text-align: center;
            box-shadow: var(--shadow-2);
            border: 1px solid var(--border-light);
        }
    </style>
</head>
<body>

<div class="success-card">
    <div style="width:68px; height:68px; border-radius:50%; background:var(--google-green-light); color:var(--google-green); display:inline-flex; align-items:center; justify-content:center; margin-bottom:20px;">
        <?= Icon::svg('check', '', 36) ?>
    </div>

    <h1 style="font-size: 24px; font-weight: 600; margin-bottom: 8px;">Franchise Application Received!</h1>
    <p style="color: var(--text-secondary); font-size: 14px; margin-bottom: 24px; line-height:1.5;">
        Your agency registration and regulatory KYC documents for <strong><?= htmlspecialchars($tenant['name'] ?? 'Your Agency') ?></strong> are queued for Super Administration review.
    </p>

    <div style="background:#f8f9fa; border:1px solid #e8eaed; border-radius:14px; padding:20px; text-align:left; margin-bottom:28px; font-size:13px;">
        <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
            <span style="color:var(--text-secondary);">Application Ref:</span>
            <strong style="font-family:var(--font-mono);">#APP-<?= htmlspecialchars($tenant['id'] ?? '00') ?></strong>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
            <span style="color:var(--text-secondary);">Current Status:</span>
            <span class="g-chip chip-warning">Under Admin KYC Review</span>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:10px;">
            <span style="color:var(--text-secondary);">Registered Email:</span>
            <span><?= htmlspecialchars($tenant['email'] ?? '') ?></span>
        </div>
        <div style="display:flex; justify-content:space-between;">
            <span style="color:var(--text-secondary);">Hub Location:</span>
            <span><?= htmlspecialchars(($tenant['city'] ?? '') . ', ' . ($tenant['state'] ?? '')) ?></span>
        </div>
    </div>

    <p style="font-size:13px; color:var(--text-secondary); line-height:1.6; margin-bottom:32px;">
        Our compliance team verifies PAN & Aadhaar details within <strong>24 business hours</strong>. Once approved, your assigned <strong>Agency Branch Code</strong> and platform credentials will be dispatched to your registered email.
    </p>

    <div style="display:flex; gap:12px; justify-content:center;">
        <a href="/" class="btn btn-outlined" style="padding:10px 22px; border-radius:20px;">Return to Home</a>
        <a href="/login" class="btn btn-primary" style="padding:10px 24px; border-radius:20px;">Portal Sign In</a>
    </div>
</div>

</body>
</html>

<?php
// src/Views/onboard/verify-otp.php - Email OTP Verification (Step 1.5)
$email = $_SESSION['onboard_pending_email'] ?? '';
$devOtp = OtpService::getDevOtp();
$csrfToken = Auth::generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email OTP | Kesari Logistics</title>
    <link rel="stylesheet" href="/assets/css/google-theme.css">
    <style>
        body {
            background-color: #f0f4f9;
            padding: 40px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .otp-card {
            background: #ffffff;
            border-radius: 24px;
            padding: 40px;
            max-width: 440px;
            width: 100%;
            box-shadow: var(--shadow-2);
            text-align: center;
        }
        .otp-input {
            width: 100%;
            letter-spacing: 12px;
            font-size: 28px;
            text-align: center;
            font-family: monospace;
            padding: 12px;
            border-radius: 8px;
            border: 2px solid var(--border-color);
            margin: 20px 0;
            outline: none;
        }
        .otp-input:focus {
            border-color: var(--google-blue);
            box-shadow: 0 0 0 3px var(--google-blue-light);
        }
    </style>
</head>
<body>

<div class="otp-card">
    <div style="width:52px; height:52px; border-radius:50%; background:var(--google-blue-light); display:inline-flex; align-items:center; justify-content:center; color:var(--google-blue); font-size:24px; margin-bottom:16px;">
        ✉️
    </div>

    <h1 style="font-size: 22px; font-weight: 500;">Verify your email</h1>
    <p style="color: var(--text-secondary); font-size: 13px; margin-top: 8px;">
        We've sent a 6-digit verification code to<br>
        <strong><?= htmlspecialchars($email) ?></strong>
    </p>

    <?php if ($devOtp): ?>
        <div style="background:#e6f4ea; color:#137333; padding:10px; border-radius:8px; font-size:12px; margin:16px 0;">
            ⚡ <strong>Development OTP Code:</strong> <code style="font-size:16px; font-weight:bold;"><?= htmlspecialchars($devOtp) ?></code>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div style="background:#fce8e6; color:#c5221f; padding:10px; border-radius:8px; font-size:13px; margin:12px 0;">
            ⚠️ <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="/onboard/verify-otp">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
        <input type="text" name="otp" class="otp-input" maxlength="6" pattern="[0-9]{6}" placeholder="000000" autofocus required>

        <button type="submit" class="btn btn-primary" style="width:100%; padding:12px; border-radius:24px; font-size:15px;">Verify & Continue to KYC ➔</button>
    </form>

    <div style="margin-top:20px; font-size:12px; color:var(--text-secondary);">
        Didn't receive code? <a href="/onboard/resend-otp" style="font-weight:500;">Resend OTP</a> • <a href="/onboard/register">Change Email</a>
    </div>
</div>

</body>
</html>

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
            border-radius: 28px;
            padding: 44px 40px;
            max-width: 460px;
            width: 100%;
            box-shadow: var(--shadow-2);
            text-align: center;
            border: 1px solid var(--border-light);
        }
        .otp-input {
            width: 100%;
            letter-spacing: 12px;
            font-size: 28px;
            text-align: center;
            font-family: var(--font-mono);
            font-weight: 700;
            padding: 14px;
            border-radius: var(--radius-sm);
            border: 2px solid var(--border-color);
            margin: 24px 0 20px 0;
            outline: none;
            transition: var(--transition);
        }
        .otp-input:focus {
            border-color: var(--brand-primary);
            box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.18);
        }
    </style>
</head>
<body>

<div class="otp-card">
    <div style="width:56px; height:56px; border-radius:50%; background:var(--brand-primary-light); color:var(--brand-primary); display:inline-flex; align-items:center; justify-content:center; margin-bottom:18px;">
        <?= Icon::svg('reports', '', 26) ?>
    </div>

    <h1 style="font-size: 22px; font-weight: 600;">Verify Your Email</h1>
    <p style="color: var(--text-secondary); font-size: 13.5px; margin-top: 8px; line-height:1.5;">
        We've dispatched a 6-digit verification code to<br>
        <strong style="color:var(--text-primary);"><?= htmlspecialchars($email) ?></strong>
    </p>

    <?php if ($devOtp): ?>
        <div style="background:#e6f4ea; border:1px solid #ceead6; color:#137333; padding:12px; border-radius:10px; font-size:12px; margin:20px 0; text-align:center;">
            <div style="font-weight:600; margin-bottom:4px; display:flex; align-items:center; justify-content:center; gap:6px;">
                <?= Icon::svg('sparkles', '', 14) ?> Development OTP Code
            </div>
            <code style="font-size:18px; font-weight:700; letter-spacing:4px; font-family:var(--font-mono);"><?= htmlspecialchars($devOtp) ?></code>
            <div style="margin-top:6px;">
                <button type="button" onclick="document.getElementById('otp').value='<?= htmlspecialchars($devOtp) ?>'" class="btn btn-outlined" style="padding:2px 10px; font-size:11px; background:#ffffff;">
                    Auto-fill Code
                </button>
            </div>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div style="background:#fce8e6; border-left:4px solid var(--google-red); color:#c5221f; padding:10px 14px; border-radius:6px; font-size:13px; margin:14px 0; text-align:left; display:flex; align-items:center; gap:8px;">
            <?= Icon::svg('alert-triangle', '', 16) ?>
            <span><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <form method="POST" action="/onboard/verify-otp">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">
        <input type="text" name="otp" id="otp" class="otp-input" maxlength="6" pattern="[0-9]{6}" placeholder="000000" autofocus required>

        <button type="submit" class="btn btn-primary" style="width:100%; padding:13px; border-radius:24px; font-size:15px; display:inline-flex; align-items:center; justify-content:center; gap:8px;">
            Verify & Proceed to KYC <?= Icon::svg('arrow-right', '', 16) ?>
        </button>
    </form>

    <div style="margin-top:24px; font-size:12.5px; color:var(--text-secondary);">
        Didn't receive the code? 
        <a href="/onboard/resend-otp" style="font-weight:600; color:var(--brand-primary);">Resend OTP</a> 
        • 
        <a href="/onboard/register" style="color:var(--text-secondary);">Change Email</a>
    </div>
</div>

</body>
</html>

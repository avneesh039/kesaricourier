<?php
// src/Views/onboard/kyc.php - KYC Document Submission (Step 2)

$tenantId = $_SESSION['onboard_tenant_id'] ?? null;
if (!$tenantId) {
    header('Location: /onboard/register');
    exit;
}

$db = Database::getInstance();
$tenant = $db->fetchOne("SELECT * FROM tenants WHERE id = ?", [$tenantId]);
$csrfToken = Auth::generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KYC Document Verification | Kesari Logistics</title>
    <link rel="stylesheet" href="/assets/css/google-theme.css">
    <style>
        body {
            background-color: #f0f4f9;
            padding: 40px 20px 80px 20px;
            min-height: 100vh;
        }

        .onboard-container {
            max-width: 780px;
            margin: 0 auto;
        }

        .stepper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 40px;
            margin-bottom: 36px;
            position: relative;
        }

        .step-item {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .step-circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #ffffff;
            border: 2px solid #dadce0;
            color: #5f6368;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            transition: var(--transition);
        }

        .step-item.active .step-circle {
            background: var(--google-blue);
            border-color: var(--google-blue);
            color: #ffffff;
            box-shadow: 0 0 0 4px rgba(26, 115, 232, 0.2);
        }

        .step-item.completed .step-circle {
            background: var(--google-green);
            border-color: var(--google-green);
            color: #ffffff;
        }

        .step-label {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .step-item.active .step-label {
            color: var(--google-blue);
            font-weight: 600;
        }

        .doc-upload-box {
            background: #fafafa;
            border: 2px dashed #dadce0;
            border-radius: 12px;
            padding: 18px 20px;
            transition: var(--transition);
        }

        .doc-upload-box:hover {
            border-color: var(--brand-primary);
            background: #f4f8ff;
        }
    </style>
</head>
<body>

<div class="onboard-container">
    <div style="text-align: center; margin-bottom: 28px;">
        <div style="width:48px; height:48px; border-radius:12px; background:var(--google-blue); color:white; display:inline-flex; align-items:center; justify-content:center; margin-bottom:12px;">
            <?= Icon::svg('kyc', '', 26) ?>
        </div>
        <h1 style="font-size: 24px; font-weight: 600;">KYC & Regulatory Compliance</h1>
        <p style="color: var(--text-secondary); font-size: 13.5px; margin-top: 4px;">
            Submitting verification documents for <strong><?= htmlspecialchars($tenant['name'] ?? 'Your Agency') ?></strong>
        </p>
    </div>

    <!-- Stepper Navigation -->
    <div class="stepper">
        <div class="step-item completed">
            <div class="step-circle"><?= Icon::svg('check', '', 16) ?></div>
            <div class="step-label">Basic Profile & OTP</div>
        </div>
        <div style="width:30px; height:2px; background:#dadce0;"></div>
        <div class="step-item active">
            <div class="step-circle">2</div>
            <div class="step-label">KYC Document Upload</div>
        </div>
        <div style="width:30px; height:2px; background:#dadce0;"></div>
        <div class="step-item">
            <div class="step-circle">3</div>
            <div class="step-label">Branch Activation</div>
        </div>
    </div>

    <!-- KYC Form Card -->
    <div class="g-card" style="border-radius: 20px; padding: 36px;">
        <?php if (!empty($error)): ?>
            <div style="background:#fce8e6; border-left:4px solid var(--google-red); color:#c5221f; padding:12px 16px; border-radius:6px; font-size:13px; margin-bottom:24px; display:flex; align-items:center; gap:8px;">
                <?= Icon::svg('alert-triangle', '', 16) ?>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" action="/onboard/kyc" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:22px;">
                <!-- PAN Card -->
                <div class="doc-upload-box">
                    <div style="font-weight:600; font-size:14px; margin-bottom:12px; display:flex; align-items:center; gap:6px; color:var(--text-primary);">
                        <span style="color:var(--brand-primary);"><?= Icon::svg('reports', '', 16) ?></span>
                        PAN Card Details *
                    </div>
                    <div class="form-group" style="margin-bottom:12px;">
                        <label class="form-label">PAN Number</label>
                        <input type="text" name="pan_no" class="form-control" placeholder="ABCDE1234F" maxlength="10" required style="text-transform:uppercase; font-family:var(--font-mono);">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Upload PAN Document (PDF, JPG, PNG)</label>
                        <input type="file" name="pan_doc" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required style="padding:6px 10px;">
                    </div>
                </div>

                <!-- Aadhaar Card -->
                <div class="doc-upload-box">
                    <div style="font-weight:600; font-size:14px; margin-bottom:12px; display:flex; align-items:center; gap:6px; color:var(--text-primary);">
                        <span style="color:var(--google-green);"><?= Icon::svg('kyc', '', 16) ?></span>
                        Aadhaar Card Details *
                    </div>
                    <div class="form-group" style="margin-bottom:12px;">
                        <label class="form-label">12-Digit Aadhaar Number</label>
                        <input type="text" name="aadhaar_no" class="form-control" placeholder="1234 5678 9012" maxlength="12" pattern="[0-9]{12}" required style="font-family:var(--font-mono);">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Upload Aadhaar Document (PDF, JPG, PNG)</label>
                        <input type="file" name="aadhaar_doc" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required style="padding:6px 10px;">
                    </div>
                </div>

                <!-- GSTIN & Address -->
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">GSTIN (Optional if annual turnover &lt; ₹20 Lakhs)</label>
                    <input type="text" name="gstin" class="form-control" placeholder="e.g. 22ABCDE1234F1Z5" style="text-transform:uppercase; font-family:var(--font-mono);">
                    <small style="color:var(--text-tertiary); font-size:11px;">Enables B2B GST tax credit invoicing under SAC 996812</small>
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Registered Hub / Office Street Address *</label>
                    <textarea name="address" class="form-control" rows="2" placeholder="Full shop/office address, road, landmark, area" required></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Postal Pincode *</label>
                    <input type="text" name="pincode" class="form-control" placeholder="6-digit pincode" pattern="[0-9]{6}" maxlength="6" required style="font-family:var(--font-mono);">
                </div>

                <div class="form-group">
                    <label class="form-label">City / State</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars(($tenant['city'] ?? '') . ', ' . ($tenant['state'] ?? '')) ?>" disabled style="background:#f1f3f4;">
                </div>
            </div>

            <div style="margin-top:32px; display:flex; justify-content:space-between; align-items:center;">
                <a href="/onboard/register" style="font-size:13px; color:var(--text-secondary); text-decoration:none;">← Back to Profile</a>
                <button type="submit" class="btn btn-primary" style="padding:13px 32px; border-radius:24px; font-size:14.5px; display:inline-flex; align-items:center; gap:8px;">
                    <?= Icon::svg('check', '', 18) ?> Submit Application for Verification
                </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>

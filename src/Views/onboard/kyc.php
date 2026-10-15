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
            padding: 40px 20px;
        }
        .onboard-container {
            max-width: 680px;
            margin: 0 auto;
        }
        .stepper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 30px;
            position: relative;
        }
        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
        }
        .step-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #ffffff;
            border: 2px solid #dadce0;
            color: #5f6368;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 500;
            font-size: 14px;
        }
        .step-item.active .step-circle {
            background: var(--google-blue);
            border-color: var(--google-blue);
            color: #ffffff;
        }
        .step-item.completed .step-circle {
            background: var(--google-green);
            border-color: var(--google-green);
            color: #ffffff;
        }
        .step-label {
            font-size: 12px;
            font-weight: 500;
            color: var(--text-secondary);
        }
        .step-item.active .step-label {
            color: var(--google-blue);
            font-weight: 600;
        }
        .file-dropzone {
            border: 2px dashed #dadce0;
            border-radius: 8px;
            padding: 16px;
            text-align: center;
            background: #fafafa;
            cursor: pointer;
            transition: var(--transition);
        }
        .file-dropzone:hover {
            border-color: var(--google-blue);
            background: #f0f7ff;
        }
    </style>
</head>
<body>

<div class="onboard-container">
    <div style="text-align: center; margin-bottom: 28px;">
        <h1 style="font-size: 24px; font-weight: 500;">KYC & Regulatory Compliance</h1>
        <p style="color: var(--text-secondary); font-size: 13px; margin-top: 4px;">
            Submitting documents for <strong><?= htmlspecialchars($tenant['name'] ?? 'Your Agency') ?></strong>
        </p>
    </div>

    <!-- Stepper Navigation -->
    <div class="stepper">
        <div class="step-item completed">
            <div class="step-circle">✓</div>
            <div class="step-label">Basic Details & OTP</div>
        </div>
        <div class="step-item active">
            <div class="step-circle">2</div>
            <div class="step-label">KYC Documents</div>
        </div>
        <div class="step-item">
            <div class="step-circle">3</div>
            <div class="step-label">Admin Approval</div>
        </div>
    </div>

    <!-- KYC Form Card -->
    <div class="g-card" style="border-radius: 20px; padding: 36px;">
        <?php if (!empty($error)): ?>
            <div style="background:#fce8e6; color:#c5221f; padding:12px 16px; border-radius:8px; font-size:13px; margin-bottom:20px;">
                ⚠️ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/onboard/kyc" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:20px;">
                <!-- PAN Card -->
                <div class="form-group">
                    <label class="form-label">PAN Card Number *</label>
                    <input type="text" name="pan_no" class="form-control" placeholder="e.g. ABCDE1234F" maxlength="10" required style="text-transform:uppercase;">
                </div>

                <div class="form-group">
                    <label class="form-label">PAN Card Document (PDF/JPG/PNG) *</label>
                    <input type="file" name="pan_doc" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                </div>

                <!-- Aadhaar Card -->
                <div class="form-group">
                    <label class="form-label">Aadhaar Card Number *</label>
                    <input type="text" name="aadhaar_no" class="form-control" placeholder="12-digit Aadhaar" maxlength="12" pattern="[0-9]{12}" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Aadhaar Document (PDF/JPG/PNG) *</label>
                    <input type="file" name="aadhaar_doc" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                </div>

                <!-- GSTIN & Address -->
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">GSTIN (Optional if turnover < ₹20 Lakhs)</label>
                    <input type="text" name="gstin" class="form-control" placeholder="e.g. 22ABCDE1234F1Z5" style="text-transform:uppercase;">
                </div>

                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Registered Agency Office Address *</label>
                    <textarea name="address" class="form-control" rows="2" placeholder="Full shop/office address, road, area" required></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Pincode *</label>
                    <input type="text" name="pincode" class="form-control" placeholder="6-digit pincode" pattern="[0-9]{6}" maxlength="6" required>
                </div>
            </div>

            <div style="margin-top:28px; display:flex; justify-content:flex-end;">
                <button type="submit" class="btn btn-primary" style="padding:12px 28px; border-radius:24px; font-size:14px;">
                    Submit for Official Verification ➔
                </button>
            </div>
        </form>
    </div>
</div>

</body>
</html>

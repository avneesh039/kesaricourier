<?php
// src/Views/onboard/register.php - Agency Self-Service Registration (Step 1)
$csrfToken = Auth::generateCsrfToken();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agency Registration & Franchise Onboarding | Kesari Logistics</title>
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
            z-index: 2;
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
    </style>
</head>
<body>

<div class="onboard-container">
    <div style="text-align: center; margin-bottom: 28px;">
        <div style="width:48px; height:48px; border-radius:12px; background:var(--google-blue); display:inline-flex; align-items:center; justify-content:center; color:white; font-size:24px; font-weight:700; margin-bottom:12px;">
            K
        </div>
        <h1 style="font-size: 26px; font-weight: 500;">Partner Agency Onboarding</h1>
        <p style="color: var(--text-secondary); font-size: 14px; margin-top: 4px;">Join the Kesari Express Logistics network with your independent branded portal.</p>
    </div>

    <!-- Stepper Navigation -->
    <div class="stepper">
        <div class="step-item active">
            <div class="step-circle">1</div>
            <div class="step-label">Basic Details & OTP</div>
        </div>
        <div class="step-item">
            <div class="step-circle">2</div>
            <div class="step-label">KYC Documents</div>
        </div>
        <div class="step-item">
            <div class="step-circle">3</div>
            <div class="step-label">Admin Approval</div>
        </div>
    </div>

    <!-- Registration Card -->
    <div class="g-card" style="border-radius: 20px; padding: 36px;">
        <?php if (!empty($error)): ?>
            <div style="background:#fce8e6; color:#c5221f; padding:12px 16px; border-radius:8px; font-size:13px; margin-bottom:20px;">
                ⚠️ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="/onboard/register">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                <div class="form-group" style="grid-column: span 2;">
                    <label class="form-label">Agency / Hub Legal Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Kesari Express Bilaspur Hub" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Franchise Brand / Trade Name</label>
                    <input type="text" name="trade_name" class="form-control" placeholder="e.g. Shree Maruti Courier Agency" value="<?= htmlspecialchars($_POST['trade_name'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Proprietor / Contact Person *</label>
                    <input type="text" name="contact_person" class="form-control" placeholder="Full legal name" required value="<?= htmlspecialchars($_POST['contact_person'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Official Email Address *</label>
                    <input type="email" name="email" class="form-control" placeholder="contact@agency.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    <small style="color:var(--text-tertiary); font-size:11px;">Verification OTP will be sent here</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Mobile Number *</label>
                    <input type="tel" name="phone" class="form-control" placeholder="10-digit mobile" pattern="[0-9]{10}" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">City *</label>
                    <input type="text" name="city" class="form-control" placeholder="Operating city" required value="<?= htmlspecialchars($_POST['city'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">State *</label>
                    <select name="state" class="form-control" required>
                        <option value="">Select State</option>
                        <option value="Chhattisgarh">Chhattisgarh</option>
                        <option value="Maharashtra">Maharashtra</option>
                        <option value="Madhya Pradesh">Madhya Pradesh</option>
                        <option value="Delhi">Delhi</option>
                        <option value="Gujarat">Gujarat</option>
                        <option value="Uttar Pradesh">Uttar Pradesh</option>
                        <option value="Karnataka">Karnataka</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>

            <div style="margin-top:24px; display:flex; justify-content:space-between; align-items:center;">
                <a href="/login" style="font-size:13px;">Already have an account? Sign in</a>
                <button type="submit" class="btn btn-primary" style="padding:10px 24px; border-radius:24px;">Send Verification OTP ➔</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>

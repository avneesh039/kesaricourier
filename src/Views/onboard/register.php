<?php
// src/Views/onboard/register.php - Franchise Partner Self-Service Registration (Step 1)
$csrfToken = Auth::generateCsrfToken();

$selectedPlan = htmlspecialchars($_GET['plan'] ?? $_POST['selected_plan'] ?? 'Yearly');
$planDetails = [
    'Monthly' => ['name' => 'Starter Branch Plan', 'price' => '&#8377;1,499/mo', 'badge' => 'Monthly Billing'],
    'Quarterly' => ['name' => 'Growth Hub Plan', 'price' => '&#8377;3,999/quarter', 'badge' => 'Quarterly Billing'],
    'Yearly' => ['name' => 'Enterprise Franchise Plan', 'price' => '&#8377;11,999/yr', 'badge' => 'Best Value • Save 35%'],
    'Custom' => ['name' => 'Regional Master License', 'price' => '&#8377;24,999/2-yr', 'badge' => '2-Year License'],
];
$currPlan = $planDetails[$selectedPlan] ?? $planDetails['Yearly'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Franchise Partner Registration | Kesari Logistics Network</title>
    <link rel="stylesheet" href="/assets/css/google-theme.css">
    <style>
        body {
            background-color: #f0f4f9;
            padding: 40px 20px 80px 20px;
            min-height: 100vh;
        }

        .onboard-container {
            max-width: 1040px;
            margin: 0 auto;
        }

        .onboard-top-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 32px;
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

        /* 2-Column Onboard Grid */
        .onboard-grid {
            display: grid;
            grid-template-columns: 1fr 1.35fr;
            gap: 28px;
            align-items: start;
        }

        .benefits-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8fafd 100%);
            border-radius: 20px;
            padding: 32px;
            border: 1px solid var(--border-light);
            box-shadow: var(--shadow-card);
        }

        .benefit-item {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 20px;
        }

        .benefit-icon {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: var(--brand-primary-light);
            color: var(--brand-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        @media (max-width: 860px) {
            .onboard-grid { grid-template-columns: 1fr; }
            .stepper { gap: 16px; }
            .step-label { display: none; }
        }
    </style>
</head>
<body>

<div class="onboard-container">
    <!-- Top Nav with Back to Home -->
    <div class="onboard-top-nav">
        <a href="/" style="display:flex; align-items:center; gap:8px; text-decoration:none; color:var(--text-primary);">
            <div style="width:34px; height:34px; border-radius:8px; background:var(--google-blue); color:white; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:16px;">
                K
            </div>
            <strong style="font-size:16px;">Kesari Express</strong>
        </a>

        <a href="/" style="font-size:13px; color:var(--text-secondary); text-decoration:none; display:inline-flex; align-items:center; gap:6px;">
            <span style="transform:rotate(180deg); display:inline-flex;"><?= Icon::svg('chevron-right', '', 14) ?></span>
            Return to Homepage
        </a>
    </div>

    <!-- Stepper Navigation -->
    <div class="stepper">
        <div class="step-item active">
            <div class="step-circle">1</div>
            <div class="step-label">Agency Profile & OTP</div>
        </div>
        <div style="width:30px; height:2px; background:#dadce0;"></div>
        <div class="step-item">
            <div class="step-circle">2</div>
            <div class="step-label">KYC Document Upload</div>
        </div>
        <div style="width:30px; height:2px; background:#dadce0;"></div>
        <div class="step-item">
            <div class="step-circle">3</div>
            <div class="step-label">Branch Activation</div>
        </div>
    </div>

    <!-- 2-Column Onboard Layout -->
    <div class="onboard-grid">
        <!-- Left Column: Franchise Perks & Trust -->
        <div class="benefits-card">
            <!-- Indian Branch Partner Visual -->
            <div style="position:relative; border-radius:14px; overflow:hidden; margin-bottom:22px; box-shadow:0 6px 20px rgba(15,23,42,0.1); height:190px; border:1px solid var(--border-light);">
                <img src="/assets/images/agency_manager.jpg" alt="Indian Agency Franchise Partner at booking counter" style="width:100%; height:100%; object-fit:cover; display:block;">
                <div style="position:absolute; bottom:10px; left:10px; right:10px; background:rgba(255,255,255,0.94); backdrop-filter:blur(8px); padding:8px 12px; border-radius:8px; font-size:12px; font-weight:600; color:var(--text-primary); display:flex; justify-content:space-between; align-items:center; border:1px solid rgba(255,255,255,0.9);">
                    <span style="display:flex; align-items:center; gap:6px;">
                        <span style="color:var(--google-green); font-size:14px;">●</span> Verified Branch Network
                    </span>
                    <strong style="color:var(--brand-primary);">450+ Active Hubs</strong>
                </div>
            </div>

            <div style="display:inline-flex; align-items:center; gap:6px; color:var(--brand-primary); font-size:12px; font-weight:600; text-transform:uppercase; margin-bottom:8px;">
                <?= Icon::svg('sparkles', '', 14) ?> Franchise Opportunities
            </div>
            <h2 style="font-size:22px; font-weight:600; line-height:1.25; margin-bottom:12px;">
                Launch Your Logistics Hub with Kesari Express
            </h2>
            <p style="font-size:13px; color:var(--text-secondary); line-height:1.5; margin-bottom:24px;">
                Join an expanding regional logistics SaaS network. Get your dedicated counter portal, automated barcode printing, and direct B2B corporate billing.
            </p>

            <div class="benefit-item">
                <div class="benefit-icon"><?= Icon::svg('agencies', '', 18) ?></div>
                <div>
                    <strong style="font-size:14px; display:block;">Independent Branded Hub</strong>
                    <span style="font-size:12px; color:var(--text-secondary);">Your dedicated agency branch code, multi-staff logins, and custom branded thermal label dockets.</span>
                </div>
            </div>

            <div class="benefit-item">
                <div class="benefit-icon"><?= Icon::svg('print', '', 18) ?></div>
                <div>
                    <strong style="font-size:14px; display:block;">4x6" Thermal Print Engine</strong>
                    <span style="font-size:12px; color:var(--text-secondary);">Integrated Code 128 barcode & QR code generator compatible with TSC, TVS, Zebra, and Rollo.</span>
                </div>
            </div>

            <div class="benefit-item">
                <div class="benefit-icon"><?= Icon::svg('invoices', '', 18) ?></div>
                <div>
                    <strong style="font-size:14px; display:block;">GST SAC 996812 Invoicing</strong>
                    <span style="font-size:12px; color:var(--text-secondary);">Automated monthly invoice bundling for B2B corporate clients with registered bank accounts.</span>
                </div>
            </div>

            <div class="benefit-item" style="margin-bottom:0;">
                <div class="benefit-icon"><?= Icon::svg('destinations', '', 18) ?></div>
                <div>
                    <strong style="font-size:14px; display:block;">Central Master Destinations</strong>
                    <span style="font-size:12px; color:var(--text-secondary);">Pre-configured network hubs across Central and Western India with live multi-tier rate matrices.</span>
                </div>
            </div>
        </div>

        <!-- Right Column: Registration Form -->
        <div class="g-card" style="border-radius: 20px; padding: 36px;">
            <div style="margin-bottom:24px;">
                <h2 style="font-size:20px; font-weight:600;">Agency Application Form</h2>
                <p style="color:var(--text-secondary); font-size:13px; margin-top:4px;">
                    Fill your operating details. A 6-digit OTP will be sent to verify your email.
                </p>
            </div>

            <?php if (!empty($error)): ?>
                <div style="background:#fce8e6; border-left:4px solid var(--google-red); color:#c5221f; padding:12px 16px; border-radius:6px; font-size:13px; margin-bottom:20px; display:flex; align-items:center; gap:8px;">
                    <?= Icon::svg('alert-triangle', '', 16) ?>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            
            <!-- Plan Selection Banner -->
            <div style="background:#e6f4ea; border:1px solid #b7e1cd; padding:12px 16px; border-radius:12px; margin-bottom:20px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="color:#137333; display:inline-flex;"><?= Icon::svg('check-circle', '', 20) ?></span>
                    <div>
                        <strong style="color:#137333; font-size:14px;"><?= $currPlan['name'] ?> (<?= $currPlan['price'] ?>)</strong>
                        <div style="font-size:11.5px; color:#5f6368;">Includes 7-day grace period on renewal & immediate branch counter access</div>
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="background:#137333; color:#fff; font-size:11px; font-weight:700; padding:4px 10px; border-radius:12px;"><?= $currPlan['badge'] ?></span>
                    <a href="/#franchise-plans" style="font-size:12px; color:#0D7378; text-decoration:underline;">Change</a>
                </div>
            </div>
            <form method="POST" action="/onboard/register">
                <input type="hidden" name="selected_plan" value="<?= $selectedPlan ?>">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken) ?>">

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-group" style="grid-column: span 2;">
                        <label class="form-label">Agency / Hub Legal Entity Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Kesari Express Bilaspur Hub" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Franchise Trade / Shop Name</label>
                        <input type="text" name="trade_name" class="form-control" placeholder="e.g. Maruti Logistics Agency" value="<?= htmlspecialchars($_POST['trade_name'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Proprietor / Contact Person *</label>
                        <input type="text" name="contact_person" class="form-control" placeholder="Full legal name" required value="<?= htmlspecialchars($_POST['contact_person'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Official Email Address *</label>
                        <input type="email" name="email" class="form-control" placeholder="contact@agency.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                        <small style="color:var(--text-tertiary); font-size:11px;">Verification OTP code sent here</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mobile Number *</label>
                        <input type="tel" name="phone" class="form-control" placeholder="10-digit mobile number" pattern="[0-9]{10}" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Operating City *</label>
                        <input type="text" name="city" class="form-control" placeholder="e.g. Bilaspur" required value="<?= htmlspecialchars($_POST['city'] ?? '') ?>">
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

                <div style="margin-top:28px; display:flex; justify-content:space-between; align-items:center;">
                    <a href="/login" style="font-size:13px; color:var(--brand-primary);">Already registered? Sign in</a>
                    <button type="submit" class="btn btn-primary" style="padding:12px 28px; border-radius:24px; font-size:14px; display:inline-flex; align-items:center; gap:8px;">
                        Send Verification OTP <?= Icon::svg('arrow-right', '', 16) ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>

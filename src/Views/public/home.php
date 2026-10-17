<?php
// src/Views/public/home.php - Public Customer-Facing Website & Tracking Portal
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kesari Express | Express Courier & Logistics Network</title>
    <link rel="stylesheet" href="/assets/css/google-theme.css">
    <style>
        /* Public Website Specific Styles */
        .public-navbar {
            position: sticky;
            top: 0;
            z-index: 1000;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-bottom: 1px solid var(--border-light);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 40px;
            height: 70px;
        }

        .brand-logo-wrap {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--text-primary);
        }

        .brand-badge {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--google-blue);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
            box-shadow: 0 4px 10px rgba(26, 115, 232, 0.3);
        }

        .brand-text-title {
            font-family: var(--font-heading);
            font-weight: 700;
            font-size: 18px;
            line-height: 1.1;
            letter-spacing: -0.3px;
        }

        .brand-text-sub {
            font-size: 11px;
            color: var(--text-tertiary);
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .public-nav-links {
            display: flex;
            align-items: center;
            gap: 28px;
        }

        .public-nav-links a {
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 14px;
            text-decoration: none;
            transition: var(--transition);
        }

        .public-nav-links a:hover {
            color: var(--brand-primary);
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(180deg, #edf2fc 0%, #f8fafd 100%);
            padding: 70px 24px 60px 24px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .hero-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 16px;
            border-radius: var(--radius-pill);
            background: #ffffff;
            border: 1px solid var(--border-light);
            font-size: 13px;
            font-weight: 500;
            color: var(--brand-primary);
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
            margin-bottom: 24px;
        }

        .hero-title {
            font-size: 46px;
            font-weight: 700;
            line-height: 1.15;
            letter-spacing: -1px;
            color: #1f1f1f;
            max-width: 820px;
            margin: 0 auto 16px auto;
        }

        .hero-sub {
            font-size: 17px;
            color: var(--text-secondary);
            max-width: 680px;
            margin: 0 auto 36px auto;
            line-height: 1.5;
        }

        /* Tracker Card in Hero */
        .tracker-hero-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08), 0 1px 3px rgba(0, 0, 0, 0.04);
            border: 1px solid var(--border-light);
            max-width: 720px;
            margin: 0 auto;
            padding: 10px 10px 10px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
        }

        .tracker-input {
            flex: 1;
            border: none;
            outline: none;
            font-family: var(--font-mono);
            font-size: 16px;
            font-weight: 500;
            color: var(--text-primary);
            letter-spacing: 0.5px;
        }

        .tracker-input::placeholder {
            font-family: var(--font-body);
            font-size: 14px;
            color: var(--text-tertiary);
            letter-spacing: normal;
        }

        .sample-awb-tags {
            margin-top: 14px;
            font-size: 13px;
            color: var(--text-secondary);
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            flex-wrap: wrap;
        }

        .sample-awb-chip {
            font-family: var(--font-mono);
            font-size: 12px;
            padding: 3px 8px;
            border-radius: 6px;
            background: #ffffff;
            border: 1px solid var(--border-color);
            color: var(--brand-primary);
            cursor: pointer;
            text-decoration: none;
            transition: var(--transition);
        }

        .sample-awb-chip:hover {
            background: var(--brand-primary-light);
            border-color: var(--brand-primary);
        }

        /* Tracking Timeline Result */
        .tracking-result-box {
            max-width: 900px;
            margin: 40px auto 0 auto;
            text-align: left;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: var(--shadow-2);
            border: 1px solid var(--border-light);
            padding: 32px;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .timeline-steps {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            position: relative;
            margin: 32px 0 20px 0;
            gap: 12px;
        }

        .timeline-steps::before {
            content: '';
            position: absolute;
            top: 16px;
            left: 20px;
            right: 20px;
            height: 3px;
            background: #e8eaed;
            z-index: 1;
        }

        .step-node {
            position: relative;
            z-index: 2;
            text-align: center;
        }

        .step-circle {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #ffffff;
            border: 3px solid #dadce0;
            margin: 0 auto 8px auto;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #747775;
            transition: var(--transition);
        }

        .step-node.completed .step-circle {
            background: var(--google-green);
            border-color: var(--google-green);
            color: #ffffff;
        }

        .step-node.current .step-circle {
            background: var(--google-blue);
            border-color: var(--google-blue-container);
            color: #ffffff;
            box-shadow: 0 0 0 4px rgba(26, 115, 232, 0.25);
        }

        .step-title {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-primary);
            line-height: 1.3;
        }

        .step-desc {
            font-size: 11px;
            color: var(--text-tertiary);
            margin-top: 2px;
        }

        /* Section Layouts */
        .content-section {
            padding: 80px 24px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .section-header {
            text-align: center;
            margin-bottom: 48px;
        }

        .section-header h2 {
            font-size: 32px;
            font-weight: 600;
            letter-spacing: -0.5px;
            margin-bottom: 12px;
        }

        .section-header p {
            font-size: 16px;
            color: var(--text-secondary);
            max-width: 600px;
            margin: 0 auto;
        }

        /* Services Grid */
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 24px;
        }

        .service-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 28px;
            border: 1px solid var(--border-light);
            box-shadow: var(--shadow-card);
            transition: var(--transition);
        }

        .service-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-card-hover);
            border-color: var(--brand-primary-container);
        }

        .service-icon-wrap {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: var(--brand-primary-light);
            color: var(--brand-primary);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
        }

        /* Calculator Card */
        .calculator-card {
            background: #ffffff;
            border-radius: 20px;
            box-shadow: var(--shadow-2);
            border: 1px solid var(--border-light);
            padding: 36px;
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 40px;
            align-items: center;
        }

        /* Hubs Chips */
        .hubs-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .hub-chip {
            background: #ffffff;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-pill);
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.03);
            transition: var(--transition);
        }

        .hub-chip:hover {
            border-color: var(--brand-primary);
            color: var(--brand-primary);
            transform: translateY(-1px);
        }

        /* CTA Banner */
        .cta-banner {
            background: linear-gradient(135deg, #1a73e8 0%, #1557b0 100%);
            color: #ffffff;
            border-radius: 24px;
            padding: 50px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 30px;
            box-shadow: 0 12px 30px rgba(26, 115, 232, 0.3);
        }

        /* Public Footer */
        .public-footer {
            background: #ffffff;
            border-top: 1px solid var(--border-light);
            padding: 60px 40px 30px 40px;
        }

        @media (max-width: 860px) {
            .public-navbar { padding: 0 20px; }
            .public-nav-links { display: none; }
            .hero-title { font-size: 32px; }
            .timeline-steps { grid-template-columns: 1fr; gap: 20px; }
            .timeline-steps::before { display: none; }
            .calculator-card { grid-template-columns: 1fr; }
            .cta-banner { flex-direction: column; text-align: center; }
        }
    </style>
</head>
<body>

<!-- Public Top Navigation Bar -->
<header class="public-navbar">
    <a href="/" class="brand-logo-wrap">
        <div class="brand-badge">K</div>
        <div>
            <div class="brand-text-title">Kesari Express</div>
            <div class="brand-text-sub">Logistics SaaS Network</div>
        </div>
    </a>

    <nav class="public-nav-links">
        <a href="#track">Track Shipment</a>
        <a href="#calculator">Rate Calculator</a>
        <a href="#services">Services</a>
        <a href="#network">Network Hubs</a>
        <a href="/onboard" style="color:var(--google-blue); font-weight:600;">Franchise Partner</a>
    </nav>

    <div style="display:flex; align-items:center; gap:10px;">
        <a href="/login?tenant=raipur" class="btn btn-outlined" style="padding:7px 16px; font-size:13px;">
            <?= Icon::svg('agencies', '', 16) ?> Branch Login
        </a>
        <a href="/login" class="btn btn-primary" style="padding:7px 18px; font-size:13px;">
            <?= Icon::svg('user', '', 16) ?> Portal Sign In
        </a>
    </div>
</header>

<!-- Hero Section with Live AWB Tracker -->
<section class="hero-section" id="track">
    <div class="hero-pill">
        <span style="display:inline-flex;"><?= Icon::svg('sparkles', '', 16) ?></span>
        Central India's Premier Express Logistics Network
    </div>

    <h1 class="hero-title">
        Fast, Reliable & Barcode-Tracked Express Courier Network
    </h1>

    <p class="hero-sub">
        Real-time docket tracking, automated thermal label generation, multi-tier slab tariffs, and GST SAC 996812 compliant B2B invoicing.
    </p>

    <!-- Instant Tracking Form -->
    <form method="GET" action="/" class="tracker-hero-card">
        <div style="display:inline-flex; color:var(--brand-primary);"><?= Icon::svg('search', '', 20) ?></div>
        <input 
            type="text" 
            name="awb" 
            class="tracker-input" 
            placeholder="Enter your 14-digit AWB or Consignment Number..." 
            value="<?= htmlspecialchars($_GET['awb'] ?? '') ?>"
            required
        >
        <button type="submit" class="btn btn-primary" style="padding:10px 24px; border-radius:14px; font-size:14px;">
            Track Docket <?= Icon::svg('arrow-right', '', 16) ?>
        </button>
    </form>

    <div class="sample-awb-tags">
        <span>⚡ Try sample dockets:</span>
        <a href="/?awb=25011100060705" class="sample-awb-chip">25011100060705 (In-Transit)</a>
        <a href="/?awb=25011200064307" class="sample-awb-chip">25011200064307 (Booked)</a>
        <a href="/?awb=25011200064296" class="sample-awb-chip">25011200064296</a>
    </div>

    <!-- Live Tracking Result Display -->
    <?php if (isset($_GET['awb']) && empty($trackingResult)): ?>
        <div class="tracking-result-box" style="text-align:center; padding:40px; max-width:600px;">
            <div style="width:48px; height:48px; border-radius:50%; background:var(--google-red-light); color:var(--google-red); display:inline-flex; align-items:center; justify-content:center; margin-bottom:14px;">
                <?= Icon::svg('alert-triangle', '', 24) ?>
            </div>
            <h3 style="font-size:18px; margin-bottom:6px;">Consignment Not Found</h3>
            <p style="font-size:14px; color:var(--text-secondary);">
                We couldn't locate docket <strong><?= htmlspecialchars($_GET['awb']) ?></strong>. Please double-check your AWB number and try again.
            </p>
        </div>
    <?php elseif (!empty($trackingResult)): ?>
        <div class="tracking-result-box">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:16px; border-bottom:1px solid var(--border-light); padding-bottom:20px;">
                <div>
                    <div style="font-size:12px; color:var(--text-secondary); text-transform:uppercase; font-weight:600;">Consignment Dossier</div>
                    <div style="display:flex; align-items:center; gap:10px; margin-top:4px;">
                        <h2 style="font-family:var(--font-mono); font-size:24px; font-weight:700;">
                            <?= htmlspecialchars($trackingResult['consignment_no']) ?>
                        </h2>
                        <span class="g-chip chip-info" style="font-size:12px;">
                            <?= htmlspecialchars($trackingResult['status_label']) ?>
                        </span>
                    </div>
                    <div style="font-size:13px; color:var(--text-secondary); margin-top:4px;">
                        Booked on <?= htmlspecialchars($trackingResult['booking_date']) ?> via <?= htmlspecialchars($trackingResult['mode']) ?>
                    </div>
                </div>

                <div style="display:flex; gap:12px; align-items:center;">
                    <a href="/print/single?awb=<?= urlencode($trackingResult['consignment_no']) ?>" target="_blank" class="btn btn-outlined" style="padding:6px 14px; font-size:12px;">
                        <?= Icon::svg('print', '', 14) ?> 4x6" Label
                    </a>
                </div>
            </div>

            <!-- Route Origin to Destination Card -->
            <div style="display:grid; grid-template-columns: 1fr auto 1fr; gap:16px; align-items:center; background:var(--surface-hover); border-radius:14px; padding:18px 24px; margin-top:24px;">
                <div>
                    <div style="font-size:11px; color:var(--text-tertiary); text-transform:uppercase; font-weight:600;">Origin Hub</div>
                    <div style="font-size:16px; font-weight:600; margin-top:2px;"><?= htmlspecialchars($trackingResult['origin']) ?></div>
                    <div style="font-size:12px; color:var(--text-secondary);"><?= htmlspecialchars($trackingResult['origin_agency']) ?></div>
                </div>

                <div style="color:var(--brand-primary); display:flex; align-items:center; justify-content:center;">
                    <?= Icon::svg('arrow-right', '', 24) ?>
                </div>

                <div style="text-align:right;">
                    <div style="font-size:11px; color:var(--text-tertiary); text-transform:uppercase; font-weight:600;">Destination Hub</div>
                    <div style="font-size:16px; font-weight:600; margin-top:2px;"><?= htmlspecialchars($trackingResult['destination']) ?></div>
                    <div style="font-size:12px; color:var(--text-secondary);"><?= htmlspecialchars($trackingResult['dest_state']) ?> • PIN: <?= htmlspecialchars($trackingResult['dest_pincode']) ?></div>
                </div>
            </div>

            <!-- Stepper Progress Timeline -->
            <div class="timeline-steps">
                <?php foreach ($trackingResult['milestones'] as $m): ?>
                    <div class="step-node <?= $m['completed'] ? 'completed' : '' ?> <?= $m['current'] ? 'current' : '' ?>">
                        <div class="step-circle">
                            <?= $m['completed'] ? Icon::svg('check', '', 14) : '•' ?>
                        </div>
                        <div class="step-title"><?= htmlspecialchars($m['title']) ?></div>
                        <div class="step-desc"><?= htmlspecialchars($m['date']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Meta details row -->
            <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:16px; background:#fafafa; border-radius:10px; padding:14px 18px; font-size:12px; border:1px solid var(--border-light); margin-top:20px;">
                <div>
                    <span style="color:var(--text-tertiary);">Recipient:</span>
                    <strong style="display:block; margin-top:2px;"><?= htmlspecialchars($trackingResult['recipient_masked']) ?></strong>
                </div>
                <div>
                    <span style="color:var(--text-tertiary);">Pieces:</span>
                    <strong style="display:block; margin-top:2px;"><?= $trackingResult['pieces'] ?> Pcs</strong>
                </div>
                <div>
                    <span style="color:var(--text-tertiary);">Billable Weight:</span>
                    <strong style="display:block; margin-top:2px;"><?= htmlspecialchars($trackingResult['weight']) ?></strong>
                </div>
                <div>
                    <span style="color:var(--text-tertiary);">Transport Mode:</span>
                    <strong style="display:block; margin-top:2px;"><?= htmlspecialchars($trackingResult['mode']) ?></strong>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>

<!-- Interactive Live Rate Calculator Section -->
<section class="content-section" id="calculator">
    <div class="section-header">
        <h2>Instant Tariff & Rate Calculator</h2>
        <p>Calculate transparent courier charges across destinations with dynamic weight slabs and Indian GST SAC 996812 compliance.</p>
    </div>

    <div class="calculator-card">
        <!-- Form Column -->
        <div>
            <div class="form-group">
                <label class="form-label">Destination Hub *</label>
                <select id="calc_destination" class="form-control" onchange="runCalculator()">
                    <option value="">-- Select Destination City --</option>
                    <?php foreach ($destinations as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= $d['code'] === 'NGP' ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['name']) ?> (<?= htmlspecialchars($d['code']) ?>) - <?= htmlspecialchars($d['state_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                <div class="form-group">
                    <label class="form-label">Package Type *</label>
                    <select id="calc_mode" class="form-control" onchange="runCalculator()">
                        <option value="Dox" selected>Documents (Dox)</option>
                        <option value="Non-Dox">Goods / Parcels (Non-Dox)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Speed Mode *</label>
                    <select id="calc_send_by" class="form-control" onchange="runCalculator()">
                        <option value="Air" selected>Air Express</option>
                        <option value="Surface">Surface Cargo</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Dead Weight (KG) *</label>
                <input type="number" step="0.1" min="0.1" value="0.5" id="calc_weight" class="form-control" oninput="runCalculator()">
            </div>
        </div>

        <!-- Result Column -->
        <div style="background:var(--surface-bg); border-radius:16px; padding:28px; border:1px solid var(--border-light);">
            <div style="font-size:12px; color:var(--text-tertiary); text-transform:uppercase; font-weight:600;">Estimated Rate</div>
            <div style="font-size:38px; font-weight:700; color:var(--brand-primary); margin:8px 0;" id="calc_lbl_total">
                ₹ 47.20
            </div>

            <div style="font-size:13px; display:flex; flex-direction:column; gap:8px; margin-top:16px; border-top:1px solid var(--border-light); padding-top:14px;">
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--text-secondary);">Base Freight:</span>
                    <strong id="calc_lbl_subtotal">₹ 40.00</strong>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--text-secondary);">GST (18% SAC 996812):</span>
                    <strong id="calc_lbl_gst">₹ 7.20</strong>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span style="color:var(--text-secondary);">Routing Zone:</span>
                    <strong id="calc_lbl_zone">Central Zone</strong>
                </div>
            </div>

            <a href="/bookings/new" class="btn btn-primary" style="width:100%; margin-top:20px; padding:10px;">
                Book Consignment at This Rate <?= Icon::svg('arrow-right', '', 14) ?>
            </a>
        </div>
    </div>
</section>

<!-- Core Services Grid -->
<section class="content-section" id="services" style="padding-top:0;">
    <div class="section-header">
        <h2>Commercial Logistics Services</h2>
        <p>Enterprise logistics solutions built for regional courier agencies, retailers, and corporate clients.</p>
    </div>

    <div class="services-grid">
        <div class="service-card">
            <div class="service-icon-wrap">
                <?= Icon::svg('truck', '', 24) ?>
            </div>
            <h3 style="font-size:18px; margin-bottom:8px;">Express Air & Line-Haul</h3>
            <p style="font-size:13px; color:var(--text-secondary); line-height:1.5;">
                Guaranteed next-day connectivity between major central commercial hubs with high-priority freight dispatch.
            </p>
        </div>

        <div class="service-card">
            <div class="service-icon-wrap">
                <?= Icon::svg('print', '', 24) ?>
            </div>
            <h3 style="font-size:18px; margin-bottom:8px;">4x6" Thermal Label Engine</h3>
            <p style="font-size:13px; color:var(--text-secondary); line-height:1.5;">
                High-density Code 128 barcodes and vector QR codes for instant barcode scanner parsing at counter check-in.
            </p>
        </div>

        <div class="service-card">
            <div class="service-icon-wrap">
                <?= Icon::svg('invoices', '', 24) ?>
            </div>
            <h3 style="font-size:18px; margin-bottom:8px;">B2B Corporate Invoicing</h3>
            <p style="font-size:13px; color:var(--text-secondary); line-height:1.5;">
                Automated monthly GST tax invoice generation under SAC code 996812 with multi-bank remittance management.
            </p>
        </div>

        <div class="service-card">
            <div class="service-icon-wrap">
                <?= Icon::svg('agencies', '', 24) ?>
            </div>
            <h3 style="font-size:18px; margin-bottom:8px;">Multi-Tenant Agency SaaS</h3>
            <p style="font-size:13px; color:var(--text-secondary); line-height:1.5;">
                Independent white-label portals for agency franchise operators with real-time bookings feed and ticket resolution.
            </p>
        </div>
    </div>
</section>

<!-- Network Hubs Showcase -->
<section class="content-section" id="network" style="background:#f8fafd; border-radius:24px; padding:60px 40px; margin-bottom:60px;">
    <div class="section-header">
        <h2>Our Growing Network Hubs</h2>
        <p>Serving central commercial hubs and major destination routes across states.</p>
    </div>

    <div class="hubs-grid">
        <?php foreach ($destinations as $d): ?>
            <div class="hub-chip">
                <span style="color:var(--brand-primary);"><?= Icon::svg('destinations', '', 14) ?></span>
                <strong><?= htmlspecialchars($d['name']) ?></strong> (<?= htmlspecialchars($d['code']) ?>) • <?= htmlspecialchars($d['state_name']) ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- Franchise Agency Partner CTA Banner -->
<section class="content-section" style="padding-top:0;">
    <div class="cta-banner">
        <div>
            <h2 style="font-size:28px; font-weight:700; color:#ffffff; margin-bottom:8px;">
                Become a Kesari Courier Franchise Partner
            </h2>
            <p style="font-size:15px; opacity:0.9; max-width:600px;">
                Instant self-service onboarding with Aadhaar & PAN KYC verification. Launch your agency branch portal in minutes.
            </p>
        </div>
        <a href="/onboard" class="btn" style="background:#ffffff; color:var(--google-blue); padding:12px 28px; font-weight:600; font-size:15px; box-shadow:0 4px 12px rgba(0,0,0,0.15);">
            Apply for Agency Franchise <?= Icon::svg('arrow-right', '', 16) ?>
        </a>
    </div>
</section>

<!-- Public Footer -->
<footer class="public-footer">
    <div style="max-width:1200px; margin:0 auto; display:grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap:40px; margin-bottom:40px;">
        <div>
            <div class="brand-logo-wrap" style="margin-bottom:14px;">
                <div class="brand-badge" style="width:34px; height:34px; font-size:16px;">K</div>
                <div class="brand-text-title">Kesari Express</div>
            </div>
            <p style="font-size:13px; color:var(--text-secondary); line-height:1.6; max-width:320px;">
                Central India's digital logistics platform for commercial parcel bookings, thermal labels, and automated GST billing compliance.
            </p>
        </div>

        <div>
            <h4 style="font-size:13px; text-transform:uppercase; color:var(--text-tertiary); margin-bottom:14px;">Services</h4>
            <div style="display:flex; flex-direction:column; gap:8px; font-size:13px;">
                <a href="#track">Live AWB Tracking</a>
                <a href="#calculator">Tariff Estimator</a>
                <a href="#services">Air & Surface Express</a>
                <a href="#network">Destination Hubs</a>
            </div>
        </div>

        <div>
            <h4 style="font-size:13px; text-transform:uppercase; color:var(--text-tertiary); margin-bottom:14px;">Agency Partners</h4>
            <div style="display:flex; flex-direction:column; gap:8px; font-size:13px;">
                <a href="/onboard">Franchise Onboarding</a>
                <a href="/login?tenant=raipur">Raipur Branch Portal</a>
                <a href="/login">Super Admin Control</a>
            </div>
        </div>

        <div>
            <h4 style="font-size:13px; text-transform:uppercase; color:var(--text-tertiary); margin-bottom:14px;">Compliance</h4>
            <div style="font-size:13px; color:var(--text-secondary); line-height:1.5;">
                <div>GST SAC: <strong>996812</strong></div>
                <div>Courier & Cargo Services</div>
                <div style="margin-top:6px; color:var(--google-green); font-weight:500;">✓ 100% Tax Compliant</div>
            </div>
        </div>
    </div>

    <div style="max-width:1200px; margin:0 auto; border-top:1px solid var(--border-light); padding-top:20px; display:flex; justify-content:space-between; align-items:center; font-size:12px; color:var(--text-tertiary);">
        <div>&copy; <?= date('Y') ?> Kesari Express Logistics Network. All rights reserved.</div>
        <div>Commercial Multi-Tenant Platform</div>
    </div>
</footer>

<script>
function runCalculator() {
    var destId = document.getElementById('calc_destination').value;
    var mode = document.getElementById('calc_mode').value;
    var sendBy = document.getElementById('calc_send_by').value;
    var weight = parseFloat(document.getElementById('calc_weight').value) || 0.5;

    if (!destId) return;

    var url = '/api/calculator?destination_id=' + encodeURIComponent(destId) +
              '&mode=' + encodeURIComponent(mode) +
              '&send_by=' + encodeURIComponent(sendBy) +
              '&weight=' + encodeURIComponent(weight);

    fetch(url)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('calc_lbl_total').innerText = '₹ ' + data.total;
                document.getElementById('calc_lbl_subtotal').innerText = '₹ ' + data.subtotal;
                document.getElementById('calc_lbl_gst').innerText = '₹ ' + data.gst;
                document.getElementById('calc_lbl_zone').innerText = data.zone;
            }
        });
}

// Initial calculation run
runCalculator();
</script>

</body>
</html>

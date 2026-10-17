<?php
// src/Views/public/home.php - Comprehensive Enterprise Logistics Portal & Tracking Center
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kesari Express | Indian Commercial Courier & Express Logistics Network</title>
    <meta name="description" content="Kesari Express is Central India's premier commercial express courier and logistics SaaS network. Real-time AWB tracking, GST SAC 996812 billing, thermal label generation, and nationwide line-haul coverage.">
    <link rel="stylesheet" href="/assets/css/google-theme.css">
    <style>
        /* ==========================================================================
           Enterprise Logistics Portal Custom Styling (Non-AI Generated Aesthetic)
           ========================================================================== */
        :root {
            --portal-navy: #0f1e36;
            --portal-blue: #1a73e8;
            --portal-dark-blue: #1557b0;
            --portal-surface: #f8fafc;
            --portal-border: #e2e8f0;
            --portal-border-dark: #cbd5e1;
            --portal-text-main: #1e293b;
            --portal-text-muted: #64748b;
        }

        /* 1. Utility Top Notification & Helpline Bar */
        .portal-topbar {
            background-color: var(--portal-navy);
            color: #e2e8f0;
            font-size: 12px;
            padding: 8px 40px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
        }

        .portal-topbar a {
            color: #cbd5e1;
            text-decoration: none;
            transition: color 0.15s ease;
        }

        .portal-topbar a:hover {
            color: #ffffff;
        }

        .topbar-left {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 18px;
            flex-wrap: wrap;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background-color: #22c55e;
            display: inline-block;
            box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.25);
        }

        /* 2. Main Enterprise Header & Navigation */
        .portal-header {
            position: sticky;
            top: 0;
            z-index: 999;
            background: #ffffff;
            border-bottom: 1px solid var(--portal-border);
            padding: 0 40px;
            height: 74px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        }

        .brand-container {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
            color: var(--portal-text-main);
        }

        .brand-emblem {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%);
            color: #ffffff;
            font-family: var(--font-heading);
            font-weight: 800;
            font-size: 22px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px rgba(26, 115, 232, 0.35);
        }

        .brand-info-title {
            font-family: var(--font-heading);
            font-size: 19px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.15;
            letter-spacing: -0.3px;
        }

        .brand-info-sub {
            font-size: 11px;
            font-weight: 600;
            color: var(--portal-text-muted);
            letter-spacing: 0.6px;
            text-transform: uppercase;
        }

        .portal-nav {
            display: flex;
            align-items: center;
            gap: 26px;
        }

        .portal-nav a {
            color: #334155;
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.15s ease;
            position: relative;
            padding: 6px 0;
        }

        .portal-nav a:hover {
            color: var(--portal-blue);
        }

        .portal-nav a.highlight-partner {
            color: var(--portal-blue);
            background: #eff6ff;
            padding: 6px 14px;
            border-radius: var(--radius-pill);
            border: 1px solid #bfdbfe;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* 3. Hero Command Center & Tabbed Operational Dock */
        .portal-hero {
            background: linear-gradient(180deg, #f1f5f9 0%, #f8fafc 100%);
            border-bottom: 1px solid var(--portal-border);
            padding: 36px 40px 48px 40px;
        }

        .hero-layout-grid {
            max-width: 1240px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1.25fr 0.95fr;
            gap: 36px;
            align-items: start;
        }

        /* Tabbed Operational Dock (Like Blue Dart / FedEx / Delhivery) */
        .command-dock {
            background: #ffffff;
            border: 1px solid var(--portal-border);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
            overflow: hidden;
        }

        .dock-tabs {
            display: flex;
            background: #f1f5f9;
            border-bottom: 1px solid var(--portal-border);
            overflow-x: auto;
        }

        .dock-tab-btn {
            flex: 1;
            padding: 14px 16px;
            font-size: 13px;
            font-weight: 600;
            border: none;
            background: transparent;
            color: #64748b;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
            border-bottom: 3px solid transparent;
            white-space: nowrap;
        }

        .dock-tab-btn:hover {
            background: #e2e8f0;
            color: #1e293b;
        }

        .dock-tab-btn.active {
            background: #ffffff;
            color: var(--portal-blue);
            border-bottom: 3px solid var(--portal-blue);
        }

        .dock-tab-content {
            padding: 24px;
            display: none;
        }

        .dock-tab-content.active {
            display: block;
        }

        /* Search input bar inside dock */
        .dock-input-group {
            display: flex;
            gap: 10px;
            margin-bottom: 14px;
        }

        .dock-text-input {
            flex: 1;
            padding: 12px 16px;
            border: 1px solid var(--portal-border-dark);
            border-radius: 8px;
            font-size: 14px;
            font-family: var(--font-mono);
            outline: none;
            transition: border-color 0.2s;
        }

        .dock-text-input:focus {
            border-color: var(--portal-blue);
            box-shadow: 0 0 0 3px rgba(26, 115, 232, 0.15);
        }

        .sample-chips-row {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            color: var(--portal-text-muted);
            flex-wrap: wrap;
        }

        .chip-awb-link {
            font-family: var(--font-mono);
            font-size: 11.5px;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
            padding: 3px 8px;
            border-radius: 4px;
            color: var(--portal-blue);
            text-decoration: none;
            font-weight: 600;
        }

        .chip-awb-link:hover {
            background: #e0f2fe;
            border-color: #93c5fd;
        }

        /* Hero Right Media Card */
        .hero-media-panel {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 12px 32px rgba(15, 23, 42, 0.12);
            border: 1px solid var(--portal-border);
            height: 410px;
            background: #0f172a;
        }

        .hero-media-panel img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .media-dock-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            background: linear-gradient(180deg, rgba(15, 23, 42, 0) 0%, rgba(15, 23, 42, 0.95) 75%);
            color: #ffffff;
        }

        /* 4. Concrete Operational Stats Bar */
        .kpi-ribbon {
            background: #ffffff;
            border-bottom: 1px solid var(--portal-border);
            padding: 20px 40px;
        }

        .kpi-ribbon-grid {
            max-width: 1240px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 20px;
        }

        .kpi-item {
            border-right: 1px solid #f1f5f9;
            padding-right: 16px;
        }

        .kpi-item:last-child {
            border-right: none;
        }

        .kpi-val {
            font-family: var(--font-heading);
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
        }

        .kpi-lbl {
            font-size: 12px;
            color: var(--portal-text-muted);
            font-weight: 500;
            margin-top: 2px;
        }

        /* 5. Section Layouts */
        .portal-section {
            padding: 64px 40px;
            max-width: 1240px;
            margin: 0 auto;
        }

        .section-headline-block {
            margin-bottom: 36px;
        }

        .section-pretitle {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: var(--portal-blue);
            margin-bottom: 6px;
        }

        .section-maintitle {
            font-size: 28px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.2;
            letter-spacing: -0.5px;
        }

        .section-desc {
            font-size: 14.5px;
            color: var(--portal-text-muted);
            max-width: 760px;
            line-height: 1.6;
            margin-top: 8px;
        }

        /* Services Spec Grid */
        .specs-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .spec-card {
            background: #ffffff;
            border: 1px solid var(--portal-border);
            border-radius: 12px;
            padding: 24px 20px;
            transition: all 0.2s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .spec-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
            border-color: #cbd5e1;
        }

        .spec-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            background: #eff6ff;
            color: var(--portal-blue);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .spec-card-title {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .spec-card-body {
            font-size: 13px;
            color: var(--portal-text-muted);
            line-height: 1.5;
            margin-bottom: 16px;
        }

        .spec-param-list {
            list-style: none;
            padding: 0;
            margin: 0;
            border-top: 1px solid #f1f5f9;
            padding-top: 12px;
            font-size: 12px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            color: #475569;
        }

        /* 6. Corporate & Fleet Double Features */
        .feature-split {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
            margin: 40px 0;
            background: #ffffff;
            border: 1px solid var(--portal-border);
            border-radius: 16px;
            padding: 32px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
        }

        .feature-split.reverse {
            direction: rtl;
        }

        .feature-split.reverse > * {
            direction: ltr;
        }

        .feature-media-box {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            height: 320px;
            box-shadow: 0 8px 24px rgba(15, 23, 42, 0.1);
        }

        .feature-media-box img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        /* 7. Hubs & Schedules Table */
        .hubs-table-wrapper {
            background: #ffffff;
            border: 1px solid var(--portal-border);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
        }

        .hubs-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            text-align: left;
        }

        .hubs-table th {
            background: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
            padding: 14px 18px;
            border-bottom: 1px solid var(--portal-border);
        }

        .hubs-table td {
            padding: 14px 18px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
        }

        .hubs-table tr:last-child td {
            border-bottom: none;
        }

        .hubs-table tr:hover {
            background-color: #f8fafc;
        }

        /* 8. Compliance & Restricted Goods Strip */
        .compliance-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-top: 32px;
        }

        .compliance-card {
            background: #f8fafc;
            border: 1px solid var(--portal-border);
            border-radius: 10px;
            padding: 20px;
        }

        /* 9. Comprehensive Corporate Footer */
        .portal-footer {
            background-color: #0b1320;
            color: #94a3b8;
            padding: 60px 40px 24px 40px;
            border-top: 1px solid #1e293b;
        }

        .footer-grid {
            max-width: 1240px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr 1.5fr;
            gap: 36px;
            margin-bottom: 40px;
        }

        .footer-heading {
            color: #ffffff;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            margin-bottom: 16px;
        }

        .footer-links {
            display: flex;
            flex-direction: column;
            gap: 10px;
            font-size: 13px;
        }

        .footer-links a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.15s;
        }

        .footer-links a:hover {
            color: #ffffff;
        }

        .footer-bottom {
            max-width: 1240px;
            margin: 0 auto;
            border-top: 1px solid #1e293b;
            padding-top: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: #64748b;
            flex-wrap: wrap;
            gap: 12px;
        }

        /* Responsive Breakpoints */
        @media (max-width: 1024px) {
            .hero-layout-grid { grid-template-columns: 1fr; }
            .hero-media-panel { height: 260px; }
            .specs-grid { grid-template-columns: 1fr 1fr; }
            .kpi-ribbon-grid { grid-template-columns: repeat(3, 1fr); }
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .feature-split { grid-template-columns: 1fr; }
            .compliance-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .portal-topbar { padding: 8px 16px; }
            .portal-header { padding: 0 16px; }
            .portal-nav { display: none; }
            .portal-hero { padding: 24px 16px; }
            .portal-section { padding: 40px 16px; }
            .kpi-ribbon { padding: 16px; }
            .kpi-ribbon-grid { grid-template-columns: 1fr; }
            .specs-grid { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- 1. Enterprise Top Operational Helpline & Live Status Bar -->
<div class="portal-topbar">
    <div class="topbar-left">
        <span>📞 Toll-Free Helpline: <strong>1800-209-4560</strong> (08:00 - 21:00 IST)</span>
        <span>✉️ Support: <a href="mailto:care@kesariexpress.com">care@kesariexpress.com</a></span>
        <span>🏢 Central Hub: Raipur (C.G.)</span>
    </div>
    <div class="topbar-right">
        <span><span class="status-dot"></span> Central Corridor Line-Haul: <strong>Normal Operations</strong></span>
        <span>Fuel Surcharge: <strong>12.5%</strong></span>
        <span>GST SAC: <strong>996812</strong></span>
    </div>
</div>

<!-- 2. Primary Navigation Header -->
<header class="portal-header">
    <a href="/" class="brand-container">
        <div class="brand-emblem">K</div>
        <div>
            <div class="brand-info-title">Kesari Express</div>
            <div class="brand-info-sub">Logistics & Supply Chain Network</div>
        </div>
    </a>

    <nav class="portal-nav">
        <a href="#dock-track">Track Shipment</a>
        <a href="#dock-calc">Rate Calculator</a>
        <a href="#services">Services & SLAs</a>
        <a href="#hubs">Network Hubs</a>
        <a href="#compliance">Regulatory & SAC</a>
        <a href="/onboard" class="highlight-partner">Franchise Partner Program</a>
    </nav>

    <div class="header-actions">
        <a href="/login?tenant=raipur" class="btn btn-outlined" style="padding:7px 16px; font-size:13px; font-weight:600;">
            <?= Icon::svg('agencies', '', 16) ?> Branch Desk (#26)
        </a>
        <a href="/login" class="btn btn-primary" style="padding:7px 18px; font-size:13px; font-weight:600;">
            <?= Icon::svg('user', '', 16) ?> Portal Sign In
        </a>
    </div>
</header>

<!-- 3. Enterprise Hero Command Center (Tabbed Dock + Logistics Specialist Showcase) -->
<section class="portal-hero">
    <div class="hero-layout-grid">
        
        <!-- Left: Interactive Tabbed Command Dock -->
        <div>
            <div style="margin-bottom:18px;">
                <span class="g-chip chip-info" style="font-size:12px; margin-bottom:8px;">
                    Express Domestic Logistics Infrastructure
                </span>
                <h1 style="font-size:32px; font-weight:800; color:#0f172a; line-height:1.2; letter-spacing:-0.5px; margin:6px 0 8px 0;">
                    Central India's Premier Barcode Logistics Network
                </h1>
                <p style="font-size:14px; color:var(--portal-text-muted); line-height:1.5;">
                    Live docket tracking, multi-tier slab tariffs, instant 4x6" thermal labels, and GST SAC 996812 compliant B2B invoicing across 19,000+ pincodes.
                </p>
            </div>

            <!-- Tabbed Utility Dock -->
            <div class="command-dock">
                <div class="dock-tabs">
                    <button type="button" class="dock-tab-btn active" onclick="switchDockTab('track')">
                        <?= Icon::svg('search', '', 16) ?> Track AWB
                    </button>
                    <button type="button" class="dock-tab-btn" onclick="switchDockTab('calc')">
                        <?= Icon::svg('invoices', '', 16) ?> Rate Calculator
                    </button>
                    <button type="button" class="dock-tab-btn" onclick="switchDockTab('pincode')">
                        <?= Icon::svg('destinations', '', 16) ?> Pincode Lookup
                    </button>
                    <button type="button" class="dock-tab-btn" onclick="switchDockTab('volumetric')">
                        <?= Icon::svg('truck', '', 16) ?> Volumetric (CFT)
                    </button>
                </div>

                <!-- Tab 1: Track Consignment -->
                <div id="tab-track" class="dock-tab-content active">
                    <form method="GET" action="/#dock-track">
                        <label style="font-size:13px; font-weight:600; color:#334155; display:block; margin-bottom:6px;">
                            Enter 14-Digit Consignment / AWB Number:
                        </label>
                        <div class="dock-input-group">
                            <input 
                                type="text" 
                                name="awb" 
                                class="dock-text-input" 
                                placeholder="e.g. 25011100060705 or 25011200064307" 
                                value="<?= htmlspecialchars($_GET['awb'] ?? '') ?>"
                                required
                            >
                            <button type="submit" class="btn btn-primary" style="padding:10px 22px; font-weight:600;">
                                Track Consignment
                            </button>
                        </div>
                    </form>

                    <div class="sample-chips-row">
                        <span>⚡ Sample Active Dockets:</span>
                        <a href="/?awb=25011100060705#dock-track" class="chip-awb-link">25011100060705 (In-Transit)</a>
                        <a href="/?awb=25011200064307#dock-track" class="chip-awb-link">25011200064307 (Booked)</a>
                        <a href="/?awb=25011200064296#dock-track" class="chip-awb-link">25011200064296 (Delivered)</a>
                    </div>
                </div>

                <!-- Tab 2: Rate Calculator -->
                <div id="tab-calc" class="dock-tab-content">
                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:14px; margin-bottom:14px;">
                        <div>
                            <label class="form-label" style="font-size:12px;">Destination Hub *</label>
                            <select id="hero_calc_dest" class="form-control" style="font-size:13px;" onchange="calculateHeroRate()">
                                <?php foreach ($destinations as $d): ?>
                                    <option value="<?= $d['id'] ?>" <?= $d['code'] === 'NGP' ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($d['name']) ?> (<?= htmlspecialchars($d['code']) ?>) - <?= htmlspecialchars($d['state_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div>
                            <label class="form-label" style="font-size:12px;">Package Type *</label>
                            <select id="hero_calc_mode" class="form-control" style="font-size:13px;" onchange="calculateHeroRate()">
                                <option value="Dox" selected>Document (Dox)</option>
                                <option value="Non-Dox">Goods / Parcel (Non-Dox)</option>
                            </select>
                        </div>
                    </div>

                    <div style="display:grid; grid-template-columns: 1fr 1fr; gap:14px; margin-bottom:14px;">
                        <div>
                            <label class="form-label" style="font-size:12px;">Speed Mode *</label>
                            <select id="hero_calc_speed" class="form-control" style="font-size:13px;" onchange="calculateHeroRate()">
                                <option value="Air" selected>Air Express (Next-Day)</option>
                                <option value="Surface">Surface Cargo (Road)</option>
                            </select>
                        </div>

                        <div>
                            <label class="form-label" style="font-size:12px;">Dead Weight (KG) *</label>
                            <input type="number" step="0.1" min="0.1" value="0.5" id="hero_calc_weight" class="form-control" style="font-size:13px;" oninput="calculateHeroRate()">
                        </div>
                    </div>

                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:12px 16px; display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <div style="font-size:11px; text-transform:uppercase; color:#64748b; font-weight:600;">Estimated Total (Inc. 18% GST)</div>
                            <div style="font-size:24px; font-weight:800; color:var(--portal-blue);" id="hero_calc_total">₹ 47.20</div>
                        </div>
                        <div style="text-align:right; font-size:12px; color:#64748b;">
                            <div>Base: <strong id="hero_calc_sub">₹ 40.00</strong> | GST: <strong id="hero_calc_gst">₹ 7.20</strong></div>
                            <div style="font-size:11px; margin-top:2px;">SAC 996812 Compliant</div>
                        </div>
                    </div>
                </div>

                <!-- Tab 3: Pincode Lookup -->
                <div id="tab-pincode" class="dock-tab-content">
                    <label style="font-size:13px; font-weight:600; color:#334155; display:block; margin-bottom:6px;">
                        Check PIN Code Servicability & Transit SLA:
                    </label>
                    <div class="dock-input-group">
                        <input type="text" id="pincode_input" class="dock-text-input" placeholder="Enter 6-digit PIN (e.g. 492001, 440001, 462001)" maxlength="6">
                        <button type="button" class="btn btn-primary" onclick="lookupPincode()" style="padding:10px 20px;">
                            Check Service
                        </button>
                    </div>
                    <div id="pincode_result" style="font-size:12.5px; padding:10px 14px; background:#f8fafc; border-radius:8px; border:1px solid #e2e8f0; color:#475569;">
                        Enter PIN code above to view delivery TAT, nearest transit hub, and regular/ODA classification.
                    </div>
                </div>

                <!-- Tab 4: Volumetric Calculator -->
                <div id="tab-volumetric" class="dock-tab-content">
                    <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:10px; margin-bottom:12px;">
                        <div>
                            <label class="form-label" style="font-size:11.5px;">Length (cm)</label>
                            <input type="number" id="vol_l" class="form-control" value="30" oninput="runVolumetric()">
                        </div>
                        <div>
                            <label class="form-label" style="font-size:11.5px;">Width (cm)</label>
                            <input type="number" id="vol_w" class="form-control" value="20" oninput="runVolumetric()">
                        </div>
                        <div>
                            <label class="form-label" style="font-size:11.5px;">Height (cm)</label>
                            <input type="number" id="vol_h" class="form-control" value="15" oninput="runVolumetric()">
                        </div>
                    </div>

                    <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px 14px; font-size:12.5px; display:flex; justify-content:space-between; align-items:center;">
                        <div>
                            <div>Air Volumetric (Div 5000): <strong id="vol_air_res">1.80 KG</strong></div>
                            <div style="color:#64748b; font-size:11.5px;">Surface Volumetric (Div 4000): <strong id="vol_surface_res">2.25 KG</strong></div>
                        </div>
                        <span class="g-chip chip-info" style="font-size:11px;">IATA Standard</span>
                    </div>
                </div>

            </div>
        </div>

        <!-- Right: Real Logistics Infrastructure Showcase -->
        <div class="hero-media-panel animate-fade-in">
            <img src="/assets/images/hero_courier.jpg" alt="Kesari Express Logistics Specialist delivering parcels in India" loading="lazy">
            <div class="media-dock-overlay">
                <div style="display:flex; justify-content:space-between; align-items:flex-end;">
                    <div>
                        <span style="background:rgba(34, 197, 94, 0.2); border:1px solid #22c55e; color:#4ade80; font-size:11px; font-weight:700; padding:2px 8px; border-radius:4px; text-transform:uppercase;">
                            Verified Ground Fleet
                        </span>
                        <div style="font-size:18px; font-weight:700; color:#ffffff; margin-top:6px;">
                            SLA-Guaranteed Express Deliveries
                        </div>
                        <div style="font-size:12.5px; color:#cbd5e1; margin-top:2px;">
                            Trained personnel, digital barcode scans, and secure transit packaging.
                        </div>
                    </div>
                    <div style="text-align:right;">
                        <div style="font-size:20px; font-weight:800; color:#ffffff;">99.4%</div>
                        <div style="font-size:11px; color:#94a3b8;">On-Time SLA</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Live Tracking Dossier Output Display (if searched) -->
    <?php if (isset($_GET['awb']) && empty($trackingResult)): ?>
        <div style="max-width:900px; margin:32px auto 0 auto; background:#ffffff; border-radius:12px; border:1px solid #fecaca; padding:28px; text-align:center;">
            <div style="width:44px; height:44px; border-radius:50%; background:#fee2e2; color:#dc2626; display:inline-flex; align-items:center; justify-content:center; margin-bottom:12px;">
                <?= Icon::svg('alert-triangle', '', 22) ?>
            </div>
            <h3 style="font-size:17px; font-weight:700; color:#991b1b; margin-bottom:4px;">Consignment Not Located</h3>
            <p style="font-size:13.5px; color:#64748b; max-width:540px; margin:0 auto;">
                Docket <strong><?= htmlspecialchars($_GET['awb']) ?></strong> was not found in active line-haul manifests. Please verify the 14-digit number on your receipt or contact agency support.
            </p>
        </div>
    <?php elseif (!empty($trackingResult)): ?>
        <div style="max-width:1040px; margin:32px auto 0 auto; background:#ffffff; border-radius:14px; border:1px solid var(--portal-border); padding:28px; box-shadow:0 8px 24px rgba(15,23,42,0.06);">
            <!-- Header Dossier Row -->
            <div style="display:flex; justify-content:space-between; align-items:flex-start; flex-wrap:wrap; gap:16px; border-bottom:1px solid var(--portal-border); padding-bottom:18px;">
                <div>
                    <div style="font-size:11.5px; font-weight:700; color:var(--portal-text-muted); text-transform:uppercase;">
                        Consignment Dispatch Dossier
                    </div>
                    <div style="display:flex; align-items:center; gap:12px; margin-top:4px;">
                        <h2 style="font-family:var(--font-mono); font-size:24px; font-weight:800; color:#0f172a;">
                            <?= htmlspecialchars($trackingResult['consignment_no']) ?>
                        </h2>
                        <span class="g-chip chip-info" style="font-size:12px; font-weight:600;">
                            <?= htmlspecialchars($trackingResult['status_label']) ?>
                        </span>
                    </div>
                    <div style="font-size:13px; color:var(--portal-text-muted); margin-top:4px;">
                        Booked on <?= htmlspecialchars($trackingResult['booking_date']) ?> via <?= htmlspecialchars($trackingResult['mode']) ?>
                    </div>
                </div>

                <div style="display:flex; gap:10px;">
                    <a href="/print/single?awb=<?= urlencode($trackingResult['consignment_no']) ?>" target="_blank" class="btn btn-outlined" style="padding:6px 14px; font-size:12px;">
                        <?= Icon::svg('print', '', 14) ?> 4x6" Thermal Label
                    </a>
                </div>
            </div>

            <!-- Route Origin to Destination Hub Card -->
            <div style="display:grid; grid-template-columns: 1fr auto 1fr; gap:16px; align-items:center; background:#f8fafc; border-radius:10px; padding:16px 20px; margin-top:20px; border:1px solid #e2e8f0;">
                <div>
                    <div style="font-size:11px; color:#64748b; text-transform:uppercase; font-weight:700;">Origin Agency Hub</div>
                    <div style="font-size:16px; font-weight:700; color:#0f172a; margin-top:2px;"><?= htmlspecialchars($trackingResult['origin']) ?></div>
                    <div style="font-size:12px; color:#64748b;"><?= htmlspecialchars($trackingResult['origin_agency']) ?></div>
                </div>

                <div style="color:var(--portal-blue); display:flex; align-items:center; justify-content:center;">
                    <?= Icon::svg('arrow-right', '', 22) ?>
                </div>

                <div style="text-align:right;">
                    <div style="font-size:11px; color:#64748b; text-transform:uppercase; font-weight:700;">Destination Hub</div>
                    <div style="font-size:16px; font-weight:700; color:#0f172a; margin-top:2px;"><?= htmlspecialchars($trackingResult['destination']) ?></div>
                    <div style="font-size:12px; color:#64748b;"><?= htmlspecialchars($trackingResult['dest_state']) ?> • PIN: <?= htmlspecialchars($trackingResult['dest_pincode']) ?></div>
                </div>
            </div>

            <!-- 5-Stage Stepper Progress -->
            <div style="display:grid; grid-template-columns:repeat(5, 1fr); gap:10px; margin:28px 0 20px 0; text-align:center;">
                <?php foreach ($trackingResult['milestones'] as $m): ?>
                    <div style="border-top: 3px solid <?= $m['completed'] ? '#22c55e' : '#cbd5e1' ?>; padding-top:10px;">
                        <div style="font-size:12px; font-weight:700; color:<?= $m['completed'] ? '#15803d' : '#64748b' ?>;">
                            <?= htmlspecialchars($m['title']) ?>
                        </div>
                        <div style="font-size:11px; color:#94a3b8; margin-top:2px;">
                            <?= htmlspecialchars($m['date']) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Metadata Row -->
            <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:14px; background:#f8fafc; border-radius:8px; padding:12px 16px; font-size:12px; border:1px solid #e2e8f0;">
                <div>
                    <span style="color:#64748b;">Consignee:</span>
                    <strong style="display:block; margin-top:2px; color:#0f172a;"><?= htmlspecialchars($trackingResult['recipient_masked']) ?></strong>
                </div>
                <div>
                    <span style="color:#64748b;">Pieces:</span>
                    <strong style="display:block; margin-top:2px; color:#0f172a;"><?= $trackingResult['pieces'] ?> Pcs</strong>
                </div>
                <div>
                    <span style="color:#64748b;">Billable Weight:</span>
                    <strong style="display:block; margin-top:2px; color:#0f172a;"><?= htmlspecialchars($trackingResult['weight']) ?></strong>
                </div>
                <div>
                    <span style="color:#64748b;">GST SAC Code:</span>
                    <strong style="display:block; margin-top:2px; color:#0f172a;">996812 (Courier)</strong>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>

<!-- 4. Key Performance Indicators Ribbon (Authentic Operational Metrics) -->
<section class="kpi-ribbon">
    <div class="kpi-ribbon-grid">
        <div class="kpi-item">
            <div class="kpi-val">19,200+</div>
            <div class="kpi-lbl">Direct Serviced Pincodes</div>
        </div>
        <div class="kpi-item">
            <div class="kpi-val">450+</div>
            <div class="kpi-lbl">Franchise & Agency Hubs</div>
        </div>
        <div class="kpi-item">
            <div class="kpi-val">120+</div>
            <div class="kpi-lbl">Night Scheduled Line-Haul Runs</div>
        </div>
        <div class="kpi-item">
            <div class="kpi-val">99.4%</div>
            <div class="kpi-lbl">On-Time Transit SLA</div>
        </div>
        <div class="kpi-item">
            <div class="kpi-val">SAC 996812</div>
            <div class="kpi-lbl">100% Tax Invoicing Compliance</div>
        </div>
    </div>
</section>

<!-- 5. Commercial Logistics Services & Concrete Operational Specs -->
<section class="portal-section" id="services">
    <div class="section-headline-block">
        <div class="section-pretitle">Enterprise Shipping Portfolio</div>
        <h2 class="section-maintitle">Standardized Courier & Freight Products</h2>
        <p class="section-desc">
            Designed for commercial senders, e-commerce vendors, law firms, pharmaceutical distributors, and regional retail agency branches.
        </p>
    </div>

    <div class="specs-grid">
        <!-- Spec 1: Air Express Dox -->
        <div class="spec-card">
            <div>
                <div class="spec-icon-box"><?= Icon::svg('sparkles', '', 22) ?></div>
                <div class="spec-card-title">Air Express Dox</div>
                <div class="spec-card-body">
                    Priority guaranteed air courier service for legal documents, tenders, bank dockets, and confidential corporate correspondence.
                </div>
            </div>
            <ul class="spec-param-list">
                <li>● <strong>TAT:</strong> Next-Day by 14:00 IST</li>
                <li>● <strong>Weight Limit:</strong> Max 5.0 KG per envelope</li>
                <li>● <strong>Tracking:</strong> Barcode scan at each transit hub</li>
                <li>● <strong>Slab:</strong> 250g & 500g base increments</li>
            </ul>
        </div>

        <!-- Spec 2: Surface Line-Haul Cargo -->
        <div class="spec-card">
            <div>
                <div class="spec-icon-box"><?= Icon::svg('truck', '', 22) ?></div>
                <div class="spec-card-title">Interstate Surface Line-Haul</div>
                <div class="spec-card-body">
                    Economical scheduled containerized road freight connecting central commercial clusters with regional distribution centers.
                </div>
            </div>
            <ul class="spec-param-list">
                <li>● <strong>TAT:</strong> 24 to 48 Hours regional</li>
                <li>● <strong>Weight Limit:</strong> Up to 500 KG per booking</li>
                <li>● <strong>Fleet:</strong> GPS real-time monitored trucks</li>
                <li>● <strong>Volumetric:</strong> Divisor 4000 (IATA CFT)</li>
            </ul>
        </div>

        <!-- Spec 3: 4x6 Thermal Label Engine -->
        <div class="spec-card">
            <div>
                <div class="spec-icon-box"><?= Icon::svg('print', '', 22) ?></div>
                <div class="spec-card-title">4x6" Thermal Barcode Engine</div>
                <div class="spec-card-body">
                    Integrated high-density GS1 Code-128 and 2D QR barcode generator compatible with industrial thermal label printers.
                </div>
            </div>
            <ul class="spec-param-list">
                <li>● <strong>Compatibility:</strong> Zebra, TSC, TVS, Rollo</li>
                <li>● <strong>Dimensions:</strong> 100mm x 150mm standard</li>
                <li>● <strong>Scanning:</strong> 100% 1-D laser readable</li>
                <li>● <strong>Compliance:</strong> Indian Carrier Standard</li>
            </ul>
        </div>

        <!-- Spec 4: B2B Invoicing SAC 996812 -->
        <div class="spec-card">
            <div>
                <div class="spec-icon-box"><?= Icon::svg('invoices', '', 22) ?></div>
                <div class="spec-card-title">Corporate B2B Invoicing</div>
                <div class="spec-card-body">
                    Consolidated 30-day billing ledger with automatic SAC 996812 tax split (CGST+SGST / IGST) and bank remittance reconciliation.
                </div>
            </div>
            <ul class="spec-param-list">
                <li>● <strong>Credit Cycle:</strong> 15 / 30 Days credit terms</li>
                <li>● <strong>Invoicing:</strong> GSTR-1 compliant CSV export</li>
                <li>● <strong>Bank Details:</strong> RTGS / NEFT / IMPS</li>
                <li>● <strong>Reconciliation:</strong> Unbilled pool matching</li>
            </ul>
        </div>
    </div>
</section>

<!-- 6. Enterprise Corporate Dispatch & Multimodal Fleet Showcases (Indian Models & Highway Fleet) -->
<section class="portal-section" style="padding-top:0;">
    
    <!-- Feature 1: Indian Corporate Client Receiving Dockets -->
    <div class="feature-split">
        <div class="feature-media-box">
            <img src="/assets/images/corporate_client.jpg" alt="Indian Corporate Client receiving priority express documents" loading="lazy">
        </div>
        <div>
            <span style="font-size:12px; font-weight:700; color:var(--portal-blue); text-transform:uppercase;">
                Contract Logistics & Key Accounts
            </span>
            <h2 style="font-size:24px; font-weight:800; color:#0f172a; margin:8px 0 12px 0; line-height:1.25;">
                Engineered for Corporate Enterprises & High-Volume Shippers
            </h2>
            <p style="font-size:13.5px; color:var(--portal-text-muted); line-height:1.6; margin-bottom:18px;">
                Streamline company-wide dispatches across branches. Eliminate daily cash payments with consolidated monthly invoicing, dedicated key account managers, and automatic electronic proof of delivery (e-POD).
            </p>
            <div style="display:flex; flex-direction:column; gap:8px; font-size:13px; color:#334155; margin-bottom:20px;">
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="color:#16a34a;"><?= Icon::svg('check', '', 16) ?></span>
                    <span><strong>SAC 996812 Tax Invoicing:</strong> Clean input tax credit (ITC) for your accounts team.</span>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="color:#16a34a;"><?= Icon::svg('check', '', 16) ?></span>
                    <span><strong>ERP / CRM Webhooks:</strong> API integration for SAP, Tally Prime, and Marg ERP.</span>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="color:#16a34a;"><?= Icon::svg('check', '', 16) ?></span>
                    <span><strong>Scheduled Daily Pickup:</strong> Dedicated field executive assigned to your office.</span>
                </div>
            </div>
            <a href="/login?tenant=raipur" class="btn btn-primary" style="padding:10px 22px;">
                Open Corporate Desk <?= Icon::svg('arrow-right', '', 14) ?>
            </a>
        </div>
    </div>

    <!-- Feature 2: Highway Express Fleet at Sunset -->
    <div class="feature-split reverse">
        <div class="feature-media-box">
            <img src="/assets/images/freight_fleet.jpg" alt="Kesari Express Interstate Logistics Cargo Delivery Fleet" loading="lazy">
        </div>
        <div>
            <span style="font-size:12px; font-weight:700; color:var(--portal-blue); text-transform:uppercase;">
                High-Speed Line-Haul Backbone
            </span>
            <h2 style="font-size:24px; font-weight:800; color:#0f172a; margin:8px 0 12px 0; line-height:1.25;">
                Scheduled Night Line-Haul Connecting Central Indian Corridors
            </h2>
            <p style="font-size:13.5px; color:var(--portal-text-muted); line-height:1.6; margin-bottom:18px;">
                Our fleet of containerized line-haul vehicles operates on strict timetable departures between Raipur, Nagpur, Bilaspur, Durg, Jabalpur, Bhopal, and Indore. Sealed security cages protect all commercial dockets.
            </p>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:20px;">
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px;">
                    <div style="font-size:20px; font-weight:800; color:var(--portal-blue);">18:30 IST</div>
                    <div style="font-size:11.5px; color:#64748b;">Daily Hub Departure Cutoff</div>
                </div>
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px;">
                    <div style="font-size:20px; font-weight:800; color:var(--portal-blue);">< 24 Hours</div>
                    <div style="font-size:11.5px; color:#64748b;">Regional Hub Interconnect SLA</div>
                </div>
            </div>
            <a href="#hubs" class="btn btn-outlined" style="padding:10px 22px;">
                View Hub Schedules <?= Icon::svg('destinations', '', 14) ?>
            </a>
        </div>
    </div>

</section>

<!-- 7. Active Destination Hubs & Timetable Matrix -->
<section class="portal-section" id="hubs" style="padding-top:0;">
    <div class="section-headline-block">
        <div class="section-pretitle">Network Directory</div>
        <h2 class="section-maintitle">Operational Hubs & Same-Day Cutoff Matrix</h2>
        <p class="section-desc">
            Directly connected primary hubs with integrated barcode processing and daily overnight surface/air departures.
        </p>
    </div>

    <div class="hubs-table-wrapper">
        <table class="hubs-table">
            <thead>
                <tr>
                    <th>Hub City</th>
                    <th>Route Code</th>
                    <th>State</th>
                    <th>Operational Zone</th>
                    <th>Pincode Anchor</th>
                    <th>Cutoff Time</th>
                    <th>Transit Modes</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($destinations as $d): ?>
                    <tr>
                        <td>
                            <strong style="color:#0f172a;"><?= htmlspecialchars($d['name']) ?></strong>
                        </td>
                        <td>
                            <span class="g-chip chip-info" style="font-family:var(--font-mono); font-size:11px;">
                                <?= htmlspecialchars($d['code']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($d['state_name']) ?></td>
                        <td><?= htmlspecialchars($d['zone_name']) ?></td>
                        <td><span style="font-family:var(--font-mono); color:#64748b;"><?= htmlspecialchars($d['pincode'] ?: 'Service Zone') ?></span></td>
                        <td><strong style="color:#0f172a;">18:30 IST</strong></td>
                        <td>
                            <span style="font-size:11.5px; background:#f1f5f9; padding:2px 8px; border-radius:4px; font-weight:600; color:#334155;">
                                Air & Surface
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>

<!-- 8. Regulatory Compliance, Prohibited Items & Packaging Standards -->
<section class="portal-section" id="compliance" style="padding-top:0;">
    <div class="section-headline-block">
        <div class="section-pretitle">Regulatory Compliance & Standards</div>
        <h2 class="section-maintitle">Carriage Standards & Safety Guidelines</h2>
        <p class="section-desc">
            Operating under Indian Carriage of Goods laws, IATA Dangerous Goods regulations, and Central GST Act SAC code 996812.
        </p>
    </div>

    <div class="compliance-grid">
        <div class="compliance-card">
            <div style="font-weight:700; color:#0f172a; font-size:15px; margin-bottom:8px; display:flex; align-items:center; gap:8px;">
                <span style="color:#2563eb;"><?= Icon::svg('invoices', '', 18) ?></span>
                GST SAC 996812 Compliance
            </div>
            <p style="font-size:12.5px; color:#64748b; line-height:1.5;">
                All invoices comply with GST SAC 996812 for domestic courier services with 18% tax distribution (9% CGST + 9% SGST for intrastate, 18% IGST for interstate). Complete B2B tax credits passed to consignor.
            </p>
        </div>

        <div class="compliance-card">
            <div style="font-weight:700; color:#0f172a; font-size:15px; margin-bottom:8px; display:flex; align-items:center; gap:8px;">
                <span style="color:#dc2626;"><?= Icon::svg('alert-triangle', '', 18) ?></span>
                Dangerous & Prohibited Goods
            </div>
            <p style="font-size:12.5px; color:#64748b; line-height:1.5;">
                Strict prohibition on carriage of currency notes, precious bullion, lithium-ion battery packs exceeding 100Wh, combustible fuels, corrosive chemicals, and firearms under Bureau of Civil Aviation Security (BCAS) norms.
            </p>
        </div>

        <div class="compliance-card">
            <div style="font-weight:700; color:#0f172a; font-size:15px; margin-bottom:8px; display:flex; align-items:center; gap:8px;">
                <span style="color:#16a34a;"><?= Icon::svg('check', '', 18) ?></span>
                Standard Packaging Norms
            </div>
            <p style="font-size:12.5px; color:#64748b; line-height:1.5;">
                Documents must be enclosed in tamper-evident tear-resistant polybags. Commercial parcels require minimum 3-ply corrugated cartons with internal bubble cushioning and standard 4x6" thermal adhesive barcode labels.
            </p>
        </div>
    </div>
</section>

<!-- 9. Franchise Partner Showcase CTA Banner (Featuring Indian Branch Manager) -->
<section class="portal-section" style="padding-top:0;">
    <div style="background: linear-gradient(135deg, #0f1e36 0%, #1e293b 100%); border-radius:18px; padding:36px 40px; color:#ffffff; border:1px solid #334155; box-shadow:0 12px 32px rgba(15, 23, 42, 0.15);">
        <div style="display:grid; grid-template-columns: 1.25fr 0.85fr; gap:36px; align-items:center;">
            <div>
                <span style="background:rgba(255,255,255,0.12); padding:4px 12px; border-radius:4px; font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:#93c5fd;">
                    Franchise Expansion Program
                </span>
                <h2 style="font-size:26px; font-weight:800; color:#ffffff; margin:12px 0 10px 0; line-height:1.25;">
                    Launch Your Profitable Courier Agency Branch in 15 Minutes
                </h2>
                <p style="font-size:14px; color:#cbd5e1; line-height:1.6; margin-bottom:22px;">
                    Join 450+ successful branch proprietors across Tier 1, 2, and 3 cities. Instant self-service digital onboarding with Aadhaar & PAN KYC verification, thermal barcode printing software, and direct B2B corporate billing access.
                </p>

                <div style="display:flex; gap:12px; flex-wrap:wrap;">
                    <a href="/onboard" class="btn" style="background:#2563eb; color:#ffffff; padding:11px 24px; font-weight:600; font-size:14px; border-radius:8px;">
                        Apply for Franchise Branch <?= Icon::svg('arrow-right', '', 14) ?>
                    </a>
                    <a href="/login?tenant=raipur" class="btn" style="background:transparent; color:#ffffff; border:1px solid rgba(255,255,255,0.3); padding:11px 20px; font-size:14px; border-radius:8px;">
                        Explore Branch Demo (#26)
                    </a>
                </div>
            </div>

            <div style="position:relative; border-radius:12px; overflow:hidden; height:240px; border:1px solid rgba(255,255,255,0.15); box-shadow:0 8px 24px rgba(0,0,0,0.3);">
                <img src="/assets/images/agency_manager.jpg" alt="Indian Agency Franchise Partner at counter" style="width:100%; height:100%; object-fit:cover; display:block;">
                <div style="position:absolute; bottom:10px; left:10px; right:10px; background:rgba(15,23,42,0.85); backdrop-filter:blur(6px); color:#ffffff; padding:8px 12px; border-radius:6px; font-size:12px; display:flex; justify-content:space-between; align-items:center;">
                    <span>⚡ 100% Digital KYC Onboarding</span>
                    <strong style="color:#60a5fa;">450+ Active Hubs</strong>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 10. Comprehensive Corporate Footer -->
<footer class="portal-footer">
    <div class="footer-grid">
        <!-- Col 1: Corporate Profile -->
        <div>
            <div style="display:flex; align-items:center; gap:10px; margin-bottom:14px;">
                <div style="width:34px; height:34px; background:#2563eb; border-radius:6px; color:#ffffff; display:flex; align-items:center; justify-content:center; font-weight:800; font-size:18px;">
                    K
                </div>
                <strong style="color:#ffffff; font-size:17px; font-family:var(--font-heading);">Kesari Express Network</strong>
            </div>
            <p style="font-size:12.5px; line-height:1.6; margin-bottom:16px; color:#94a3b8;">
                Central India's leading commercial logistics and express courier SaaS network. Barcode docket tracking, automated thermal printing, multi-tier slab rate matrix, and GSTR-1 compliant SAC 996812 invoicing.
            </p>
            <div style="font-size:11.5px; color:#64748b; line-height:1.5;">
                <div>CIN: U63090CT2026PTC014582</div>
                <div>GSTIN: 22AABCK9968M1Z8</div>
                <div>MSME Udyam: UDYAM-CG-14-0028911</div>
            </div>
        </div>

        <!-- Col 2: Services -->
        <div>
            <div class="footer-heading">Services</div>
            <div class="footer-links">
                <a href="#services">Air Express Documents</a>
                <a href="#services">Interstate Surface Cargo</a>
                <a href="#services">B2B Corporate Credit</a>
                <a href="#services">4x6" Thermal Barcodes</a>
                <a href="#hubs">Direct Route Hubs</a>
            </div>
        </div>

        <!-- Col 3: Tools & Tracking -->
        <div>
            <div class="footer-heading">Online Tools</div>
            <div class="footer-links">
                <a href="#dock-track">AWB Docket Tracking</a>
                <a href="#dock-calc">Rate & Slab Estimator</a>
                <a href="#dock-pincode">Pincode Servicability</a>
                <a href="#dock-volumetric">Volumetric Weight (CFT)</a>
                <a href="/login?tenant=raipur">Thermal Label Queue</a>
            </div>
        </div>

        <!-- Col 4: Franchise & Portal -->
        <div>
            <div class="footer-heading">Partners</div>
            <div class="footer-links">
                <a href="/onboard">Franchise Onboarding</a>
                <a href="/onboard">Aadhaar / PAN KYC</a>
                <a href="/login?tenant=raipur">Raipur Counter Portal (#26)</a>
                <a href="/login">Super Admin Control Center</a>
                <a href="/admin/tickets">Support Helpdesk</a>
            </div>
        </div>

        <!-- Col 5: Hub Offices & Support -->
        <div>
            <div class="footer-heading">Central Sorting Hub</div>
            <div style="font-size:12.5px; color:#94a3b8; line-height:1.6; margin-bottom:12px;">
                <strong>Kesari Logistics Central Hub:</strong><br>
                Plot 42-44, Ring Road No. 2, Transport Nagar, Rawabhata Industrial Area, Raipur, Chhattisgarh - 492001
            </div>
            <div style="font-size:12px; color:#94a3b8;">
                <div>Helpline: <strong style="color:#ffffff;">1800-209-4560</strong></div>
                <div>Desk Email: <strong style="color:#ffffff;">care@kesariexpress.com</strong></div>
            </div>
        </div>
    </div>

    <!-- Bottom Copyright & Legal Strip -->
    <div class="footer-bottom">
        <div>
            &copy; <?= date('Y') ?> Kesari Express Logistics Limited. All rights reserved. Domestic Courier Services under GST SAC Code 996812.
        </div>
        <div style="display:flex; gap:16px;">
            <a href="#" style="color:#64748b; text-decoration:none;">Terms of Domestic Carriage</a>
            <a href="#" style="color:#64748b; text-decoration:none;">Privacy Policy</a>
            <a href="#" style="color:#64748b; text-decoration:none;">Citizen Charter & Grievance</a>
        </div>
    </div>
</footer>

<!-- 11. Interactive Client Scripts for Tabbed Dock & Tools -->
<script>
// Dock Tab Switching
function switchDockTab(tabId) {
    var tabs = ['track', 'calc', 'pincode', 'volumetric'];
    var btns = document.querySelectorAll('.dock-tab-btn');
    
    tabs.forEach(function(t, idx) {
        var el = document.getElementById('tab-' + t);
        if (el) {
            el.classList.toggle('active', t === tabId);
        }
    });

    btns.forEach(function(btn) {
        btn.classList.remove('active');
    });

    if (event && event.currentTarget) {
        event.currentTarget.classList.add('active');
    }
}

// Hero Live Rate Calculator
function calculateHeroRate() {
    var destId = document.getElementById('hero_calc_dest').value;
    var mode = document.getElementById('hero_calc_mode').value;
    var speed = document.getElementById('hero_calc_speed').value;
    var weight = parseFloat(document.getElementById('hero_calc_weight').value) || 0.5;

    if (!destId) return;

    var url = '/api/calculator?destination_id=' + encodeURIComponent(destId) +
              '&mode=' + encodeURIComponent(mode) +
              '&send_by=' + encodeURIComponent(speed) +
              '&weight=' + encodeURIComponent(weight);

    fetch(url)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('hero_calc_total').innerText = '₹ ' + data.total;
                document.getElementById('hero_calc_sub').innerText = '₹ ' + data.subtotal;
                document.getElementById('hero_calc_gst').innerText = '₹ ' + data.gst;
            }
        });
}

// Pincode Servicability Lookup
function lookupPincode() {
    var pin = document.getElementById('pincode_input').value.trim();
    var resEl = document.getElementById('pincode_result');
    if (!pin || pin.length !== 6 || isNaN(pin)) {
        resEl.innerHTML = '<span style="color:#dc2626;">⚠️ Please enter a valid 6-digit Indian PIN code.</span>';
        return;
    }

    // Common Central Hubs Mock/Heuristic
    var prefix = pin.substring(0, 2);
    var hub = 'Central Gateway';
    var tat = '24-48 Hours';
    var isOda = false;

    if (prefix === '49') {
        hub = 'Raipur / Bilaspur Primary Hub (C.G.)';
        tat = 'Guaranteed 24 Hours';
    } else if (prefix === '44') {
        hub = 'Nagpur Transit Gateway (M.H.)';
        tat = 'Guaranteed 24 Hours';
    } else if (prefix === '46' || prefix === '45' || prefix === '48') {
        hub = 'Bhopal / Indore / Jabalpur Hub (M.P.)';
        tat = '24-36 Hours';
    } else if (prefix === '11') {
        hub = 'Delhi NCR Air Express Hub';
        tat = 'Next-Day Air Express';
    } else if (prefix === '40') {
        hub = 'Mumbai Gateway Hub';
        tat = 'Next-Day Air Express';
    } else {
        tat = '48-72 Hours';
        hub = 'National Line-Haul Hub';
    }

    resEl.innerHTML = '<div style="color:#15803d; font-weight:600;">✓ Serviced Pincode (' + pin + ')</div>' +
                      '<div style="font-size:12px; color:#334155; margin-top:2px;">' +
                      'Routing Via: <strong>' + hub + '</strong> | Delivery TAT: <strong>' + tat + '</strong> | Service: <strong>Regular Line-Haul (Non-ODA)</strong>' +
                      '</div>';
}

// Volumetric Weight Calculator (CFT)
function runVolumetric() {
    var l = parseFloat(document.getElementById('vol_l').value) || 0;
    var w = parseFloat(document.getElementById('vol_w').value) || 0;
    var h = parseFloat(document.getElementById('vol_h').value) || 0;

    var volAir = (l * w * h) / 5000;
    var volSurface = (l * w * h) / 4000;

    document.getElementById('vol_air_res').innerText = volAir.toFixed(2) + ' KG';
    document.getElementById('vol_surface_res').innerText = volSurface.toFixed(2) + ' KG';
}

// Run initial hero rate calculation on page load
calculateHeroRate();
runVolumetric();
</script>

</body>
</html>

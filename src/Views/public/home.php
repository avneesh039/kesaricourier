<?php
// src/Views/public/home.php - Comprehensive Enterprise Logistics Portal & Tracking Center
// Features isolated transparent cutout models, multi-modal routing, corridor schedules, and regulatory guidelines
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

        /* 1. Utility Top Notification Bar */
        .portal-topbar {
            background-color: var(--portal-navy);
            color: #e2e8f0;
            font-size: 12px;
            padding: 8px 32px;
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
            gap: 22px;
            flex-wrap: wrap;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .topbar-chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            white-space: nowrap;
        }

        .status-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background-color: #22c55e;
            display: inline-block;
            box-shadow: 0 0 0 2px rgba(34, 197, 94, 0.25);
        }

        /* 2. Main Enterprise Header & Navigation - Guaranteed Single Line */
        .portal-header {
            position: sticky;
            top: 0;
            z-index: 999;
            background: #ffffff;
            border-bottom: 1px solid var(--portal-border);
            padding: 0 32px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);
            white-space: nowrap;
        }

        .brand-container {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--portal-text-main);
            flex-shrink: 0;
            white-space: nowrap;
        }

        .brand-emblem {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #1a73e8 0%, #0d47a1 100%);
            color: #ffffff;
            font-family: var(--font-heading);
            font-weight: 800;
            font-size: 20px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 3px 10px rgba(26, 115, 232, 0.35);
        }

        .brand-info-title {
            font-family: var(--font-heading);
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
            line-height: 1.15;
            letter-spacing: -0.3px;
            white-space: nowrap;
        }

        .brand-info-sub {
            font-size: 10.5px;
            font-weight: 600;
            color: var(--portal-text-muted);
            letter-spacing: 0.5px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .portal-nav {
            display: flex;
            align-items: center;
            gap: 20px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .portal-nav a {
            color: #334155;
            font-size: 13.5px;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.15s ease;
            position: relative;
            padding: 6px 2px;
            white-space: nowrap;
            display: inline-flex;
            align-items: center;
            gap: 6px;
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

        .portal-nav a.highlight-partner:hover {
            background: #dbeafe;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
            white-space: nowrap;
        }

        /* 3. Hero Command Center & Tabbed Operational Dock */
        .portal-hero {
            background: linear-gradient(180deg, #f8fafc 0%, #edf2f7 100%);
            border-bottom: 1px solid var(--portal-border);
            padding: 40px 40px 52px 40px;
            position: relative;
            overflow: hidden;
        }

        .hero-layout-grid {
            max-width: 1240px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1.22fr 0.98fr;
            gap: 36px;
            align-items: center;
        }

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

        /* Transparent Cutout Hero Stage */
        .hero-cutout-stage {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 480px;
        }

        .stage-halo-circle {
            position: absolute;
            width: 380px;
            height: 380px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(26, 115, 232, 0.18) 0%, rgba(26, 115, 232, 0.04) 70%, transparent 100%);
            z-index: 1;
        }

        .stage-orbit-ring {
            position: absolute;
            width: 440px;
            height: 440px;
            border-radius: 50%;
            border: 2px dashed rgba(26, 115, 232, 0.25);
            z-index: 1;
        }

        .hero-courier-cutout-img {
            position: relative;
            z-index: 2;
            max-height: 470px;
            width: auto;
            object-fit: contain;
            filter: drop-shadow(0 24px 32px rgba(15, 23, 42, 0.16));
            transition: transform 0.4s ease;
        }

        .hero-courier-cutout-img:hover {
            transform: translateY(-6px) scale(1.02);
        }

        /* 4. Concrete Operational Stats Bar */
        .kpi-ribbon {
            background: #ffffff;
            border-bottom: 1px solid var(--portal-border);
            padding: 22px 40px;
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

        /* Transparent Cutout Split Showcase */
        .cutout-split-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
            margin: 48px 0;
            background: #ffffff;
            border: 1px solid var(--portal-border);
            border-radius: 18px;
            padding: 36px 40px;
            box-shadow: 0 4px 16px rgba(15, 23, 42, 0.04);
            position: relative;
            overflow: hidden;
        }

        .cutout-split-row.reverse {
            direction: rtl;
        }

        .cutout-split-row.reverse > * {
            direction: ltr;
        }

        .cutout-split-media {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            min-height: 340px;
        }

        .cutout-split-media img {
            max-height: 340px;
            width: auto;
            object-fit: contain;
            position: relative;
            z-index: 2;
            filter: drop-shadow(0 18px 28px rgba(15, 23, 42, 0.14));
            transition: transform 0.3s ease;
        }

        .cutout-split-media:hover img {
            transform: scale(1.03);
        }

        .split-halo {
            position: absolute;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(26, 115, 232, 0.12) 0%, transparent 70%);
            z-index: 1;
        }

        /* Hubs & Timetable Matrix */
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

        /* Tariff Table */
        .tariff-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            background: #ffffff;
            border: 1px solid var(--portal-border);
            border-radius: 12px;
            overflow: hidden;
        }

        .tariff-table th {
            background: #f1f5f9;
            color: #334155;
            padding: 12px 16px;
            font-weight: 700;
            border-bottom: 1px solid var(--portal-border);
        }

        .tariff-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #f1f5f9;
            color: #1e293b;
        }

        /* Corporate Footer */
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

        @media (max-width: 1024px) {
            .hero-layout-grid { grid-template-columns: 1fr; }
            .hero-cutout-stage { min-height: 380px; }
            .specs-grid { grid-template-columns: 1fr 1fr; }
            .kpi-ribbon-grid { grid-template-columns: repeat(3, 1fr); }
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .cutout-split-row { grid-template-columns: 1fr; padding: 24px; }
            .journey-grid { grid-template-columns: 1fr 1fr; }
            .case-studies-grid { grid-template-columns: 1fr; }
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
            .journey-grid { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<!-- 1. Utility Top Notification & Helpline Bar with Custom Premium Icons -->
<div class="portal-topbar">
    <div class="topbar-left">
        <span class="topbar-chip">
            <?= Icon::svg('phone', '', 13, '#93c5fd') ?>
            <span>Toll-Free Helpline: <strong>1800-209-4560</strong> (08:00 - 21:00 IST)</span>
        </span>
        <span class="topbar-chip">
            <?= Icon::svg('mail', '', 13, '#93c5fd') ?>
            <span>Support: <a href="mailto:care@kesariexpress.com">care@kesariexpress.com</a></span>
        </span>
        <span class="topbar-chip">
            <?= Icon::svg('hub', '', 13, '#93c5fd') ?>
            <span>Central Master Hub: <strong>Raipur (C.G.)</strong></span>
        </span>
    </div>
    <div class="topbar-right">
        <span class="topbar-chip">
            <span class="status-dot"></span>
            <?= Icon::svg('radar', '', 13, '#4ade80') ?>
            <span>Central Corridor Line-Haul: <strong>Normal Operations</strong></span>
        </span>
        <span class="topbar-chip">
            <?= Icon::svg('fuel', '', 13, '#93c5fd') ?>
            <span>Fuel Surcharge: <strong>12.5%</strong></span>
        </span>
        <span class="topbar-chip">
            <?= Icon::svg('receipt-tax', '', 13, '#93c5fd') ?>
            <span>GST SAC: <strong>996812</strong></span>
        </span>
    </div>
</div>

<!-- 2. Primary Navigation Header - Single Line Layout -->
<header class="portal-header">
    <a href="/" class="brand-container">
        <div class="brand-emblem">K</div>
        <div>
            <div class="brand-info-title">Kesari Express</div>
            <div class="brand-info-sub">Logistics & Supply Chain Network</div>
        </div>
    </a>

    <nav class="portal-nav">
        <a href="#dock-track">Track AWB</a>
        <a href="#dock-calc">Rate Calculator</a>
        <a href="#corridors">Express Corridors</a>
        <a href="#journey">How It Works</a>
        <a href="#services">Services</a>
        <a href="#tariffs">Tariff Matrix</a>
        <a href="#regulatory">E-Way Bill & SAC</a>
        <a href="/onboard" class="highlight-partner"><?= Icon::svg('sparkles', '', 13) ?> Franchise Program</a>
    </nav>

    <div class="header-actions">
        <a href="/login?tenant=raipur" class="btn btn-outlined" style="padding:7px 15px; font-size:13px; font-weight:600;">
            <?= Icon::svg('agencies', '', 15) ?> Branch Desk (#26)
        </a>
        <a href="/login" class="btn btn-primary" style="padding:7px 16px; font-size:13px; font-weight:600;">
            <?= Icon::svg('user', '', 15) ?> Portal Sign In
        </a>
    </div>
</header>

<!-- 3. Hero Command Center (Tabbed Dock + Transparent Cutout Courier Model) -->
<section class="portal-hero">
    <div class="hero-layout-grid">
        
        <!-- Left: Interactive Command Dock -->
        <div>
            <div style="margin-bottom:18px;">
                <span class="g-chip chip-info" style="font-size:12px; margin-bottom:8px;">
                    Express Logistics & Supply Chain SaaS
                </span>
                <h1 style="font-size:34px; font-weight:800; color:#0f172a; line-height:1.2; letter-spacing:-0.5px; margin:6px 0 8px 0;">
                    Central India's Premier Barcode Logistics Network
                </h1>
                <p style="font-size:14.5px; color:var(--portal-text-muted); line-height:1.5;">
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
                        <span>⚡ Sample Dockets:</span>
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

        <!-- Right: Transparent Cutout Indian Courier Model (No Background Box!) -->
        <div class="hero-cutout-stage">
            <div class="stage-halo-circle"></div>
            <div class="stage-orbit-ring"></div>
            
            <!-- Seamless Cutout Image without Background -->
            <img 
                src="/assets/images/courier_cutout.png" 
                alt="Indian Courier Delivery Specialist holding express package with barcode label" 
                class="hero-courier-cutout-img"
            >

            <!-- Floating Operational Callouts directly over page -->
            <div class="cutout-tag" style="top:24px; left:10px;">
                <span style="color:#16a34a; font-size:14px;">●</span>
                <span>120+ Daily Scheduled Runs</span>
            </div>

            <div class="cutout-tag" style="bottom:36px; right:10px;">
                <span style="color:var(--portal-blue); font-size:14px;">★</span>
                <span>99.4% On-Time Delivery SLA</span>
            </div>

            <div class="cutout-tag" style="top:45%; right:-10px;">
                <span style="color:#f59e0b; font-size:14px;">⚡</span>
                <span>GS1 Code-128 Barcode Scans</span>
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

<!-- 4. Key Performance Indicators Ribbon -->
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

<!-- 5. Interactive Express Corridors & Transit Schedules Matrix -->
<section class="portal-section" id="corridors">
    <div class="section-headline-block">
        <div class="section-pretitle">High-Speed Line-Haul Corridors</div>
        <h2 class="section-maintitle">Central Indian Overnight Transit Arteries</h2>
        <p class="section-desc">
            Direct high-frequency scheduled line-haul runs connecting key economic hubs with guaranteed next-day delivery windows.
        </p>
    </div>

    <div class="corridor-grid">
        <!-- Corridor 1 -->
        <div class="corridor-card">
            <div class="corridor-route-bar">
                <span>Raipur ⇄ Bilaspur ⇄ Korba</span>
                <span class="corridor-tag-pill">4 Hours</span>
            </div>
            <div style="font-size:12.5px; color:#64748b; line-height:1.5; margin-bottom:12px;">
                Express industrial corridor serving mining, power plants, steel fabricators, and commercial distributors.
            </div>
            <div style="font-size:11.5px; color:#0f172a; display:flex; justify-content:space-between; border-top:1px dashed #e2e8f0; padding-top:8px;">
                <span>Daily Runs: <strong>4 Scheduled</strong></span>
                <span>Cutoff: <strong>19:30 IST</strong></span>
            </div>
        </div>

        <!-- Corridor 2 -->
        <div class="corridor-card">
            <div class="corridor-route-bar">
                <span>Raipur ⇄ Durg ⇄ Nagpur</span>
                <span class="corridor-tag-pill">6 Hours</span>
            </div>
            <div style="font-size:12.5px; color:#64748b; line-height:1.5; margin-bottom:12px;">
                Gateway to Maharashtra & Western India. High-volume overnight line-haul container trucks with GPS locks.
            </div>
            <div style="font-size:11.5px; color:#0f172a; display:flex; justify-content:space-between; border-top:1px dashed #e2e8f0; padding-top:8px;">
                <span>Daily Runs: <strong>6 Scheduled</strong></span>
                <span>Cutoff: <strong>19:00 IST</strong></span>
            </div>
        </div>

        <!-- Corridor 3 -->
        <div class="corridor-card">
            <div class="corridor-route-bar">
                <span>Nagpur ⇄ Jabalpur ⇄ Bhopal</span>
                <span class="corridor-tag-pill">9 Hours</span>
            </div>
            <div style="font-size:12.5px; color:#64748b; line-height:1.5; margin-bottom:12px;">
                Connecting Madhya Pradesh commercial capitals with Central transshipment sorting hubs.
            </div>
            <div style="font-size:11.5px; color:#0f172a; display:flex; justify-content:space-between; border-top:1px dashed #e2e8f0; padding-top:8px;">
                <span>Daily Runs: <strong>3 Scheduled</strong></span>
                <span>Cutoff: <strong>18:30 IST</strong></span>
            </div>
        </div>

        <!-- Corridor 4 -->
        <div class="corridor-card">
            <div class="corridor-route-bar">
                <span>Central Hubs ⇄ Delhi / Mumbai</span>
                <span class="corridor-tag-pill">Next-Day Air</span>
            </div>
            <div style="font-size:12.5px; color:#64748b; line-height:1.5; margin-bottom:12px;">
                Daily priority air express charters for critical legal dockets, banking tenders, and medical consignments.
            </div>
            <div style="font-size:11.5px; color:#0f172a; display:flex; justify-content:space-between; border-top:1px dashed #e2e8f0; padding-top:8px;">
                <span>Air Dispatch: <strong>Twice Daily</strong></span>
                <span>Cutoff: <strong>18:00 IST</strong></span>
            </div>
        </div>
    </div>
</section>

<!-- 6. The 4-Step Operational Journey of Every Consignment -->
<section class="portal-section" id="journey" style="padding-top:0;">
    <div class="section-headline-block">
        <div class="section-pretitle">The Kesari Assurance</div>
        <h2 class="section-maintitle">How Your Shipment Moves From Counter to Doorstep</h2>
        <p class="section-desc">
            A fully digitized 4-stage chain of custody ensuring zero loss, end-to-end visibility, and barcode verification at every touchpoint.
        </p>
    </div>

    <div class="journey-grid">
        <!-- Step 1 -->
        <div class="journey-step-card">
            <div class="journey-step-num">1</div>
            <h3 style="font-size:16px; font-weight:700; color:#0f172a; margin-bottom:8px;">
                Counter Check-in & Weighing
            </h3>
            <p style="font-size:13px; color:#64748b; line-height:1.5;">
                Parcel dead weight is verified on certified digital scales. Volumetric dimensions are logged and a 4x6" thermal barcode label is generated instantly.
            </p>
        </div>

        <!-- Step 2 -->
        <div class="journey-step-card">
            <div class="journey-step-num">2</div>
            <h3 style="font-size:16px; font-weight:700; color:#0f172a; margin-bottom:8px;">
                Manifesting & Security Bagging
            </h3>
            <p style="font-size:13px; color:#64748b; line-height:1.5;">
                Dockets are grouped by destination route code (e.g. NGP, BPL). Consignments are packed into sealed tamper-evident polybags with numbered security locks.
            </p>
        </div>

        <!-- Step 3 -->
        <div class="journey-step-card">
            <div class="journey-step-num">3</div>
            <h3 style="font-size:16px; font-weight:700; color:#0f172a; margin-bottom:8px;">
                Overnight Line-Haul Transfer
            </h3>
            <p style="font-size:13px; color:#64748b; line-height:1.5;">
                Scheduled line-haul vehicles depart strictly at cutoff. GPS trackers transmit real-time location telemetry to our central monitoring control room.
            </p>
        </div>

        <!-- Step 4 -->
        <div class="journey-step-card">
            <div class="journey-step-num">4</div>
            <h3 style="font-size:16px; font-weight:700; color:#0f172a; margin-bottom:8px;">
                Last-Mile OTP & Digital POD
            </h3>
            <p style="font-size:13px; color:#64748b; line-height:1.5;">
                Assigned delivery executive delivers parcel at receiver's doorstep. Delivery confirmation is secured via secure OTP or recipient signature with instant SMS update.
            </p>
        </div>
    </div>
</section>

<!-- 7. Corporate Client Solutions (Featuring Cutout Businesswoman) -->
<section class="portal-section" style="padding-top:0;">
    <div class="cutout-split-row">
        <!-- Text Column -->
        <div>
            <span style="font-size:12px; font-weight:700; color:var(--portal-blue); text-transform:uppercase; letter-spacing:0.8px;">
                Corporate Key Accounts
            </span>
            <h2 style="font-size:26px; font-weight:800; color:#0f172a; margin:8px 0 12px 0; line-height:1.25;">
                Complete Dispatch Automation for Corporate Enterprises
            </h2>
            <p style="font-size:14px; color:var(--portal-text-muted); line-height:1.6; margin-bottom:20px;">
                Stop paying counter cash for daily mail. Benefit from pre-printed dockets, dedicated field pickup executives, 30-day corporate credit terms, and consolidated GSTR-1 compliant tax invoices.
            </p>

            <div style="display:flex; flex-direction:column; gap:10px; font-size:13.5px; color:#334155; margin-bottom:24px;">
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="color:#16a34a;"><?= Icon::svg('check', '', 16) ?></span>
                    <span><strong>SAC 996812 Input Tax Credit:</strong> Full 18% GST credit pass-through for corporate taxes.</span>
                </div>
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="color:#16a34a;"><?= Icon::svg('check', '', 16) ?></span>
                    <span><strong>ERP / CRM Webhook APIs:</strong> Auto-push dockets from Tally, Marg ERP, and SAP.</span>
                </div>
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="color:#16a34a;"><?= Icon::svg('check', '', 16) ?></span>
                    <span><strong>Reverse Logistics & POD Archival:</strong> Digital signed POD access on the portal for 365 days.</span>
                </div>
            </div>

            <a href="/login?tenant=raipur" class="btn btn-primary" style="padding:10px 24px; font-weight:600;">
                Access Corporate Client Desk <?= Icon::svg('arrow-right', '', 14) ?>
            </a>
        </div>

        <!-- Transparent Cutout Column (Customer Cutout with no background box!) -->
        <div class="cutout-split-media">
            <div class="split-halo"></div>
            <img 
                src="/assets/images/customer_cutout.png" 
                alt="Indian Businesswoman client receiving priority courier package"
            >
        </div>
    </div>
</section>

<!-- 8. Commercial Fleet & Highway Line-Haul (Featuring Cutout Delivery Van) -->
<section class="portal-section" style="padding-top:0;">
    <div class="cutout-split-row reverse">
        <!-- Text Column -->
        <div>
            <span style="font-size:12px; font-weight:700; color:var(--portal-blue); text-transform:uppercase; letter-spacing:0.8px;">
                Containerized Interstate Fleet
            </span>
            <h2 style="font-size:26px; font-weight:800; color:#0f172a; margin:8px 0 12px 0; line-height:1.25;">
                GPS-Monitored Cargo Vans & Container Line-Haul
            </h2>
            <p style="font-size:14px; color:var(--portal-text-muted); line-height:1.6; margin-bottom:20px;">
                Our fleet of branded express delivery vans and line-haul container trucks operate with sealed biometric cargo bay locks, real-time speed monitoring, and weatherproof transit compartments.
            </p>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:22px;">
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px;">
                    <div style="font-size:22px; font-weight:800; color:var(--portal-blue);">18:30 IST</div>
                    <div style="font-size:12px; color:#64748b; margin-top:2px;">Night Departure Cutoff</div>
                </div>
                <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px; padding:14px;">
                    <div style="font-size:22px; font-weight:800; color:var(--portal-blue);">< 24 Hours</div>
                    <div style="font-size:12px; color:#64748b; margin-top:2px;">Regional Delivery SLA</div>
                </div>
            </div>

            <a href="#corridors" class="btn btn-outlined" style="padding:10px 24px; font-weight:600;">
                Inspect Route Schedules <?= Icon::svg('truck', '', 14) ?>
            </a>
        </div>

        <!-- Transparent Cutout Delivery Van (No background box!) -->
        <div class="cutout-split-media">
            <div class="split-halo" style="width:300px; height:300px;"></div>
            <img 
                src="/assets/images/van_cutout.png" 
                alt="Kesari Express branded delivery van cutout"
            >
        </div>
    </div>
</section>

<!-- 9. Standard Courier Specifications Grid -->
<section class="portal-section" id="services" style="padding-top:0;">
    <div class="section-headline-block">
        <div class="section-pretitle">Logistics Specifications</div>
        <h2 class="section-maintitle">Standardized Packaging & Service Specs</h2>
        <p class="section-desc">
            Complete operational specifications for commercial senders, e-commerce retailers, and regional retail agency branches.
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

<!-- 10. Transparent Slab Rate Card & Zone Matrix (SAC 996812) -->
<section class="portal-section" id="tariffs" style="padding-top:0;">
    <div class="section-headline-block">
        <div class="section-pretitle">Transparent Tariff Matrix</div>
        <h2 class="section-maintitle">Standard Commercial Rate Slab Matrix</h2>
        <p class="section-desc">
            Clear, transparent shipping slabs with no hidden surcharges. All rates subject to 18% Indian GST under SAC 996812.
        </p>
    </div>

    <table class="tariff-table">
        <thead>
            <tr>
                <th>Zone / Route Type</th>
                <th>Coverage Hubs</th>
                <th>Docs (Up to 250g)</th>
                <th>Docs (Up to 500g)</th>
                <th>Parcel Base (1st KG)</th>
                <th>Addl. KG (Parcel)</th>
                <th>Standard TAT</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>Intra-State Hubs</strong></td>
                <td>Within Chhattisgarh (Raipur, BSP, Durg, Korba)</td>
                <td>₹ 30.00</td>
                <td>₹ 40.00</td>
                <td>₹ 60.00</td>
                <td>₹ 25.00</td>
                <td>24 Hours</td>
            </tr>
            <tr>
                <td><strong>Regional Central Zone</strong></td>
                <td>C.G., M.P. & Vidarbha (Nagpur, BPL, IND, JLR)</td>
                <td>₹ 40.00</td>
                <td>₹ 60.00</td>
                <td>₹ 80.00</td>
                <td>₹ 35.00</td>
                <td>24 - 36 Hours</td>
            </tr>
            <tr>
                <td><strong>Metro Air Express</strong></td>
                <td>Delhi NCR, Mumbai, Bengaluru, Hyderabad</td>
                <td>₹ 70.00</td>
                <td>₹ 100.00</td>
                <td>₹ 150.00</td>
                <td>₹ 65.00</td>
                <td>Next-Day Air</td>
            </tr>
            <tr>
                <td><strong>Rest of India (Surface)</strong></td>
                <td>All other serviceable PIN codes nationwide</td>
                <td>₹ 50.00</td>
                <td>₹ 80.00</td>
                <td>₹ 110.00</td>
                <td>₹ 45.00</td>
                <td>48 - 72 Hours</td>
            </tr>
        </tbody>
    </table>
</section>

<!-- 11. E-Way Bill & Indian Regulatory Guidelines -->
<section class="portal-section" id="regulatory" style="padding-top:0;">
    <div class="section-headline-block">
        <div class="section-pretitle">Compliance & Statutory Norms</div>
        <h2 class="section-maintitle">E-Way Bill & Transport Regulatory Rules</h2>
        <p class="section-desc">
            Important statutory guidelines mandated by the Central Board of Indirect Taxes and Customs (CBIC) for domestic freight.
        </p>
    </div>

    <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:20px;">
        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:20px;">
            <div style="font-weight:700; color:#0f172a; font-size:15px; margin-bottom:8px; display:flex; align-items:center; gap:8px;">
                <span style="color:#2563eb;"><?= Icon::svg('invoices', '', 18) ?></span>
                E-Way Bill (Consignments > ₹50,000)
            </div>
            <p style="font-size:12.5px; color:#64748b; line-height:1.5;">
                Under Rule 138 of CGST Rules, every consignment of commercial goods exceeding ₹50,000 invoice value requires a valid E-Way Bill (Part A & Part B). The counter operator logs the E-Way Bill number directly onto the docket.
            </p>
        </div>

        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:20px;">
            <div style="font-weight:700; color:#0f172a; font-size:15px; margin-bottom:8px; display:flex; align-items:center; gap:8px;">
                <span style="color:#dc2626;"><?= Icon::svg('alert-triangle', '', 18) ?></span>
                Dangerous Goods (BCAS & IATA)
            </div>
            <p style="font-size:12.5px; color:#64748b; line-height:1.5;">
                Strict prohibition on carriage of uncertified lithium-ion batteries, liquid chemicals, flammable gases, firearms, and cash currency. Shippers face immediate docket forfeiture and legal reporting for undeclared hazardous materials.
            </p>
        </div>

        <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:20px;">
            <div style="font-weight:700; color:#0f172a; font-size:15px; margin-bottom:8px; display:flex; align-items:center; gap:8px;">
                <span style="color:#16a34a;"><?= Icon::svg('check', '', 18) ?></span>
                SAC 996812 Input Tax Credit
            </div>
            <p style="font-size:12.5px; color:#64748b; line-height:1.5;">
                All invoices generated by Kesari Express include your enterprise GSTIN. Monthly summaries are uploaded automatically into GSTR-1, ensuring zero friction in claiming 100% input tax credit (ITC) on all freight expenses.
            </p>
        </div>
    </div>
</section>

<!-- 12. Regional Case Studies / Customer Stories -->
<section class="portal-section" style="padding-top:0;">
    <div class="section-headline-block">
        <div class="section-pretitle">Enterprise Client Stories</div>
        <h2 class="section-maintitle">Trusted by Businesses Across Central India</h2>
        <p class="section-desc">
            See how regional distributors, industrial suppliers, and e-commerce companies streamline their daily dispatches with Kesari Express.
        </p>
    </div>

    <div class="case-studies-grid">
        <div class="case-study-card">
            <div>
                <span class="g-chip chip-info" style="font-size:11px; margin-bottom:12px;">Pharmaceutical Distributor</span>
                <h4 style="font-size:16px; font-weight:700; color:#0f172a; margin-bottom:8px;">
                    Overnight Supply to 64 Medical Retailers
                </h4>
                <p style="font-size:13px; color:#64748b; line-height:1.5;">
                    "We dispatch temperature-sensitive critical medicines from Raipur to chemists in Bilaspur, Korba, and Jagdalpur. Kesari's 18:30 line-haul cutoffs ensure next-morning delivery with zero transit breakage."
                </p>
            </div>
            <div style="margin-top:16px; border-top:1px solid #f1f5f9; padding-top:10px; font-size:12px; color:#334155;">
                <strong>— S. K. Agrawal</strong>, Vardhman Pharma Logistics, Raipur
            </div>
        </div>

        <div class="case-study-card">
            <div>
                <span class="g-chip chip-info" style="font-size:11px; margin-bottom:12px;">Steel & Spares Fabricator</span>
                <h4 style="font-size:16px; font-weight:700; color:#0f172a; margin-bottom:8px;">
                    Instant 4x6" Labels Saved 2 Hours Daily
                </h4>
                <p style="font-size:13px; color:#64748b; line-height:1.5;">
                    "Our dispatch team used to manually fill paper bilty copies. With Kesari's bulk docket booking and barcode thermal labels, our heavy machinery spares dispatch turnaround dropped from 3 hours to 20 minutes."
                </p>
            </div>
            <div style="margin-top:16px; border-top:1px solid #f1f5f9; padding-top:10px; font-size:12px; color:#334155;">
                <strong>— Rakesh Verma</strong>, Bhilai Precision Engineering, Durg
            </div>
        </div>

        <div class="case-study-card">
            <div>
                <span class="g-chip chip-info" style="font-size:11px; margin-bottom:12px;">Textile Distributor</span>
                <h4 style="font-size:16px; font-weight:700; color:#0f172a; margin-bottom:8px;">
                    Monthly SAC 996812 Invoicing Streamlined ITC
                </h4>
                <p style="font-size:13px; color:#64748b; line-height:1.5;">
                    "Having a dedicated key account manager and clean GSTR-1 compliant monthly invoices under SAC 996812 eliminated all GST reconciliation mismatches for our chartered accountant."
                </p>
            </div>
            <div style="margin-top:16px; border-top:1px solid #f1f5f9; padding-top:10px; font-size:12px; color:#334155;">
                <strong>— Manish Dewangan</strong>, Chhattisgarh Silk Handloom Traders
            </div>
        </div>
    </div>
</section>

<!-- 13. Franchise Partner Program (Featuring Cutout Branch Manager) -->
<section class="portal-section" style="padding-top:0;">
    <div class="cutout-split-row" style="background: linear-gradient(135deg, #0f1e36 0%, #1e293b 100%); color:#ffffff; border-color:#334155;">
        <!-- Text Column -->
        <div>
            <span style="background:rgba(255,255,255,0.12); padding:4px 12px; border-radius:4px; font-size:11.5px; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; color:#93c5fd;">
                Franchise Expansion Program
            </span>
            <h2 style="font-size:28px; font-weight:800; color:#ffffff; margin:12px 0 10px 0; line-height:1.25;">
                Start Your Profitable Courier Franchise in 15 Minutes
            </h2>
            <p style="font-size:14px; color:#cbd5e1; line-height:1.6; margin-bottom:22px;">
                Join 450+ successful branch proprietors across India. Instant self-service digital onboarding with Aadhaar & PAN KYC verification, thermal barcode printing software, and direct B2B corporate billing access.
            </p>

            <div style="display:flex; gap:12px; flex-wrap:wrap;">
                <a href="/onboard" class="btn" style="background:#2563eb; color:#ffffff; padding:12px 26px; font-weight:600; font-size:14px; border-radius:8px;">
                    Apply for Franchise Branch <?= Icon::svg('arrow-right', '', 14) ?>
                </a>
                <a href="/login?tenant=raipur" class="btn" style="background:transparent; color:#ffffff; border:1px solid rgba(255,255,255,0.3); padding:12px 22px; font-size:14px; border-radius:8px;">
                    Explore Branch Demo (#26)
                </a>
            </div>
        </div>

        <!-- Transparent Cutout Branch Manager (No background box!) -->
        <div class="cutout-split-media">
            <div class="split-halo" style="background:radial-gradient(circle, rgba(37, 99, 235, 0.25) 0%, transparent 70%);"></div>
            <img 
                src="/assets/images/manager_cutout.png" 
                alt="Indian Agency Franchise Partner at counter holding barcode scanner"
            >
        </div>
    </div>
</section>

<!-- 14. Interactive Frequently Asked Questions (FAQ) Accordion -->
<section class="portal-section" style="padding-top:0;">
    <div class="section-headline-block" style="text-align:center;">
        <div class="section-pretitle">Common Questions</div>
        <h2 class="section-maintitle">Frequently Asked Questions</h2>
        <p class="section-desc" style="margin:8px auto 0 auto;">
            Everything you need to know about booking dockets, tracking shipments, volumetric calculations, and corporate billing.
        </p>
    </div>

    <div class="faq-list">
        <div class="faq-item active">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>How is billable weight determined for large lightweight cartons?</span>
                <span class="faq-icon"><?= Icon::svg('chevron-right', '', 16) ?></span>
            </div>
            <div class="faq-answer">
                Under IATA carrier rules, billable weight is the higher of actual dead weight vs. volumetric weight. Volumetric weight is calculated using the formula: $(L \times W \times H \text{ in cm}) / 5000$ for Air Express and $/ 4000$ for Surface Cargo. Use our built-in Volumetric (CFT) calculator tab at the top of the page.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>What are the daily cutoff timings for same-day dispatch from Raipur?</span>
                <span class="faq-icon"><?= Icon::svg('chevron-right', '', 16) ?></span>
            </div>
            <div class="faq-answer">
                Consignments booked before 18:30 IST at our Raipur Master Hub are sorted and loaded into the overnight line-haul container trucks for next-morning delivery across Bilaspur, Durg-Bhilai, Rajnandgaon, and Nagpur. Dockets booked after 18:30 IST are dispatched on the next scheduled run.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>How can our business open a monthly credit account?</span>
                <span class="faq-icon"><?= Icon::svg('chevron-right', '', 16) ?></span>
            </div>
            <div class="faq-answer">
                Companies dispatching 25 or more dockets monthly are eligible for our B2B Corporate Credit program. We offer 15-day and 30-day billing terms with consolidated monthly tax invoices under SAC 996812 and assigned field pickup staff. Apply via our Corporate Desk link or call 1800-209-4560.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-question" onclick="toggleFaq(this)">
                <span>Can thermal labels be printed on regular office desktop printers?</span>
                <span class="faq-icon"><?= Icon::svg('chevron-right', '', 16) ?></span>
            </div>
            <div class="faq-answer">
                Yes! While our system generates standard 4x6" thermal labels optimized for industrial thermal printers (TSC, Zebra, TVS, Rollo), you can also print labels on standard A4 paper (2 dockets per sheet or 4 dockets per sheet) from any regular laser or inkjet printer.
            </div>
        </div>
    </div>
</section>

<!-- 15. Comprehensive Corporate Footer -->
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
                <a href="#corridors">Express Corridors</a>
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

<!-- 16. Interactive Client Scripts -->
<script>
function switchDockTab(tabId) {
    var tabs = ['track', 'calc', 'pincode', 'volumetric'];
    var btns = document.querySelectorAll('.dock-tab-btn');
    
    tabs.forEach(function(t) {
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

function lookupPincode() {
    var pin = document.getElementById('pincode_input').value.trim();
    var resEl = document.getElementById('pincode_result');
    if (!pin || pin.length !== 6 || isNaN(pin)) {
        resEl.innerHTML = '<span style="color:#dc2626;">⚠️ Please enter a valid 6-digit Indian PIN code.</span>';
        return;
    }

    var prefix = pin.substring(0, 2);
    var hub = 'Central Gateway';
    var tat = '24-48 Hours';

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

function runVolumetric() {
    var l = parseFloat(document.getElementById('vol_l').value) || 0;
    var w = parseFloat(document.getElementById('vol_w').value) || 0;
    var h = parseFloat(document.getElementById('vol_h').value) || 0;

    var volAir = (l * w * h) / 5000;
    var volSurface = (l * w * h) / 4000;

    document.getElementById('vol_air_res').innerText = volAir.toFixed(2) + ' KG';
    document.getElementById('vol_surface_res').innerText = volSurface.toFixed(2) + ' KG';
}

function toggleFaq(btn) {
    var item = btn.parentElement;
    item.classList.toggle('active');
}

// Initial calculation run
calculateHeroRate();
runVolumetric();
</script>

</body>
</html>

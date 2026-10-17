<?php
// src/Views/layout.php - Google Material Design 3 Master Layout Shell

$isSuperAdmin = TenantContext::isSuperAdmin();
$tenant = TenantContext::getTenant();
$brandColor = TenantContext::getBrandColor();
$brandName = TenantContext::getTenantName();
$currentUser = Auth::user();
$daysUntilExpiry = TenantContext::getDaysUntilExpiry();
$isExpiring = TenantContext::isExpiringSoon(30);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? $brandName) ?> | Kesari Logistics SaaS</title>
    <link rel="stylesheet" href="/assets/css/google-theme.css">
    <style>
        :root {
            --brand-primary: <?= htmlspecialchars($brandColor) ?>;
        }
    </style>
</head>
<body>
<div class="app-layout">
    <!-- Collapsible Modern Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div style="width:32px; height:32px; border-radius:8px; background:var(--brand-primary); display:flex; align-items:center; justify-content:center; color:white; font-weight:700; font-size:16px;">
                K
            </div>
            <div class="sidebar-title" title="<?= htmlspecialchars($brandName) ?>">
                <?= htmlspecialchars($brandName) ?>
            </div>
        </div>

        <nav class="sidebar-nav">
            <?php if ($isSuperAdmin): ?>
                <div class="nav-section-title">Control Plane</div>
                <a href="/" class="nav-item <?= ($activeNav ?? '') === 'dashboard' ? 'active' : '' ?>">
                    <span class="nav-icon">📊</span> Dashboard
                </a>
                <a href="/admin/agencies" class="nav-item <?= ($activeNav ?? '') === 'agencies' ? 'active' : '' ?>">
                    <span class="nav-icon">🏢</span> Agency Tenants
                </a>
                <a href="/admin/kyc" class="nav-item <?= ($activeNav ?? '') === 'kyc' ? 'active' : '' ?>">
                    <span class="nav-icon">🛡️</span> KYC Verification
                </a>
                <a href="/admin/destinations" class="nav-item <?= ($activeNav ?? '') === 'destinations' ? 'active' : '' ?>">
                    <span class="nav-icon">📍</span> Master Destinations
                </a>
                <a href="/admin/tickets" class="nav-item <?= ($activeNav ?? '') === 'tickets' ? 'active' : '' ?>">
                    <span class="nav-icon">🎫</span> Support & City Requests
                </a>
                <a href="/admin/invoices" class="nav-item <?= ($activeNav ?? '') === 'invoices' ? 'active' : '' ?>">
                    <span class="nav-icon">📑</span> Consolidated Reports
                </a>
            <?php else: ?>
                <div class="nav-section-title">Operations</div>
                <a href="/" class="nav-item <?= ($activeNav ?? '') === 'dashboard' ? 'active' : '' ?>">
                    <span class="nav-icon">📊</span> Overview
                </a>
                <a href="/bookings/new" class="nav-item <?= ($activeNav ?? '') === 'book' ? 'active' : '' ?>">
                    <span class="nav-icon">📦</span> Consignment Entry
                </a>
                <a href="/bookings" class="nav-item <?= ($activeNav ?? '') === 'bookings' ? 'active' : '' ?>">
                    <span class="nav-icon">📋</span> Consignment List
                </a>
                <a href="/print-labels" class="nav-item <?= ($activeNav ?? '') === 'print' ? 'active' : '' ?>">
                    <span class="nav-icon">🏷️</span> Thermal Print Hub
                </a>

                <div class="nav-section-title">Billing & Compliance</div>
                <a href="/invoices" class="nav-item <?= ($activeNav ?? '') === 'invoices' ? 'active' : '' ?>">
                    <span class="nav-icon">🧾</span> GST Tax Invoices
                </a>
                <a href="/bank-accounts" class="nav-item <?= ($activeNav ?? '') === 'bank' ? 'active' : '' ?>">
                    <span class="nav-icon">🏦</span> Bank Accounts
                </a>

                <div class="nav-section-title">Masters & Tariff</div>
                <a href="/clients" class="nav-item <?= ($activeNav ?? '') === 'clients' ? 'active' : '' ?>">
                    <span class="nav-icon">👥</span> B2B Corporate Clients
                </a>
                <a href="/tariffs" class="nav-item <?= ($activeNav ?? '') === 'tariffs' ? 'active' : '' ?>">
                    <span class="nav-icon">⚡</span> Rate Matrix (Tariff)
                </a>
                <a href="/tickets" class="nav-item <?= ($activeNav ?? '') === 'tickets' ? 'active' : '' ?>">
                    <span class="nav-icon">💬</span> Destination Request / Help
                </a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <div style="font-size:12px; color:var(--text-tertiary);">
                <?= $isSuperAdmin ? 'Super Admin Mode' : 'Agency Code: <strong>' . htmlspecialchars($tenant['agency_code'] ?? 'N/A') . '</strong>' ?>
            </div>
            <a href="/logout" title="Sign out" style="color:var(--text-secondary); font-size:16px;">⏻</a>
        </div>
    </aside>

    <!-- Main Content Shell -->
    <div class="main-wrapper">
        <!-- Top App Bar -->
        <header class="topbar">
            <div class="search-box">
                <span>🔍</span>
                <input type="text" class="search-input" placeholder="Search consignment (AWB), client, destination...">
            </div>

            <div class="topbar-actions">
                <!-- Environment / Tenant switcher helper for development -->
                <?php if ($isSuperAdmin): ?>
                    <a href="/?tenant=raipur" class="btn btn-outlined" style="padding:4px 10px; font-size:12px;">Switch to Raipur Agency View</a>
                <?php else: ?>
                    <a href="/" class="btn btn-outlined" style="padding:4px 10px; font-size:12px;">Switch to Admin View</a>
                <?php endif; ?>

                <div class="user-badge">
                    <div class="user-avatar">
                        <?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div style="font-size:13px; line-height:1.2;">
                        <div style="font-weight:500;"><?= htmlspecialchars($currentUser['name'] ?? 'User') ?></div>
                        <div style="font-size:11px; color:var(--text-secondary);"><?= htmlspecialchars($currentUser['role'] ?? 'staff') ?></div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Container -->
        <main class="page-container">
            <!-- 30-Day Expiry Notification Banner -->
            <?php if (!$isSuperAdmin && $isExpiring): ?>
                <div class="expiry-banner <?= $daysUntilExpiry <= 7 ? 'critical' : '' ?>">
                    <div>
                        ⚠️ <strong>Subscription Notice:</strong> Your agency platform account validity expires in 
                        <strong><?= $daysUntilExpiry ?> days</strong> (<?= htmlspecialchars($tenant['validity_end'] ?? '') ?>).
                    </div>
                    <a href="/tickets" class="btn btn-primary" style="padding:4px 12px; font-size:12px;">Request Extension / Renewal</a>
                </div>
            <?php endif; ?>

            <!-- View Specific Body Content -->
            <?= $content ?? '' ?>
        </main>
    </div>
</div>
</body>
</html>

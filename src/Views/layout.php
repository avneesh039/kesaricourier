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
    <!-- Collapsible Modern Sidebar Drawer -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <div class="sidebar-logo-icon">
                <?= strtoupper(substr($brandName, 0, 1)) ?>
            </div>
            <div class="sidebar-title" title="<?= htmlspecialchars($brandName) ?>">
                <?= htmlspecialchars($brandName) ?>
            </div>
        </div>

        <nav class="sidebar-nav">
            <?php if ($isSuperAdmin): ?>
                <div class="nav-section-title">Control Plane</div>
                <a href="/" class="nav-item <?= ($activeNav ?? '') === 'dashboard' ? 'active' : '' ?>">
                    <span class="nav-icon"><?= Icon::svg('dashboard') ?></span> Dashboard
                </a>
                <a href="/admin/agencies" class="nav-item <?= ($activeNav ?? '') === 'agencies' ? 'active' : '' ?>">
                    <span class="nav-icon"><?= Icon::svg('agencies') ?></span> Agency Franchises
                </a>
                <a href="/admin/subscriptions" class="nav-item <?= ($activeNav ?? '') === 'subscriptions' ? 'active' : '' ?>">
                    <span class="nav-icon"><?= Icon::svg('receipt-tax') ?></span> Plans & Subscriptions
                </a>
                <a href="/admin/courier-apis" class="nav-item <?= ($activeNav ?? '') === 'courier_apis' ? 'active' : '' ?>">
                    <span class="nav-icon"><?= Icon::svg('truck') ?></span> Courier APIs Gateway
                </a>
                <a href="/admin/kyc" class="nav-item <?= ($activeNav ?? '') === 'kyc' ? 'active' : '' ?>">
                    <span class="nav-icon"><?= Icon::svg('kyc') ?></span> KYC Verification
                </a>
                <a href="/admin/destinations" class="nav-item <?= ($activeNav ?? '') === 'destinations' ? 'active' : '' ?>">
                    <span class="nav-icon"><?= Icon::svg('destinations') ?></span> Master Destinations
                </a>
                <a href="/admin/tickets" class="nav-item <?= ($activeNav ?? '') === 'tickets' ? 'active' : '' ?>">
                    <span class="nav-icon"><?= Icon::svg('tickets') ?></span> Support & City Requests
                </a>
                <a href="/admin/invoices" class="nav-item <?= ($activeNav ?? '') === 'invoices' ? 'active' : '' ?>">
                    <span class="nav-icon"><?= Icon::svg('reports') ?></span> Consolidated Reports
                </a>
            <?php else: ?>
                <div class="nav-section-title">Operations</div>
                <a href="/" class="nav-item <?= ($activeNav ?? '') === 'dashboard' ? 'active' : '' ?>">
                    <span class="nav-icon"><?= Icon::svg('dashboard') ?></span> Overview
                </a>
                <a href="/bookings/new" class="nav-item <?= ($activeNav ?? '') === 'book' ? 'active' : '' ?>">
                    <span class="nav-icon"><?= Icon::svg('book') ?></span> Consignment Entry
                </a>
                <a href="/bookings" class="nav-item <?= ($activeNav ?? '') === 'bookings' ? 'active' : '' ?>">
                    <span class="nav-icon"><?= Icon::svg('bookings') ?></span> Consignment List
                </a>
                <a href="/print-labels" class="nav-item <?= ($activeNav ?? '') === 'print' ? 'active' : '' ?>">
                    <span class="nav-icon"><?= Icon::svg('print') ?></span> Thermal Print Hub
                </a>

                <div class="nav-section-title">Billing & Compliance</div>
                <a href="/invoices" class="nav-item <?= ($activeNav ?? '') === 'invoices' ? 'active' : '' ?>">
                    <span class="nav-icon"><?= Icon::svg('invoices') ?></span> GST Tax Invoices
                </a>
                <a href="/payments" class="nav-item <?= ($activeNav ?? '') === 'payments' ? 'active' : '' ?>">
                    <span class="nav-icon"><?= Icon::svg('payments') ?></span> Payment Receipts & Ledger
                </a>
                <a href="/bank-accounts" class="nav-item <?= ($activeNav ?? '') === 'bank' ? 'active' : '' ?>">
                    <span class="nav-icon"><?= Icon::svg('bank') ?></span> Bank Accounts
                </a>

                <div class="nav-section-title">Masters & Staff</div>
                <a href="/clients" class="nav-item <?= ($activeNav ?? '') === 'clients' ? 'active' : '' ?>">
                    <span class="nav-icon"><?= Icon::svg('clients') ?></span> B2B Corporate Clients
                </a>
                <a href="/operators" class="nav-item <?= ($activeNav ?? '') === 'operators' ? 'active' : '' ?>">
                    <span class="nav-icon"><?= Icon::svg('operators') ?></span> Branch Operators
                </a>
                <a href="/tariffs" class="nav-item <?= ($activeNav ?? '') === 'tariffs' ? 'active' : '' ?>">
                    <span class="nav-icon"><?= Icon::svg('tariffs') ?></span> Rate Matrix (Tariff)
                </a>
                <a href="/tickets" class="nav-item <?= ($activeNav ?? '') === 'tickets' ? 'active' : '' ?>">
                    <span class="nav-icon"><?= Icon::svg('tickets') ?></span> Destination Request / Help
                </a>
                <a href="/change-password" class="nav-item <?= ($activeNav ?? '') === 'changepwd' ? 'active' : '' ?>">
                    <span class="nav-icon"><?= Icon::svg('lock') ?></span> Change Password
                </a>
            <?php endif; ?>
        </nav>

        <div class="sidebar-footer">
            <div style="font-size:12px; color:var(--text-tertiary);">
                <?= $isSuperAdmin ? 'Super Admin Mode' : 'Agency: <strong>#' . htmlspecialchars($tenant['agency_code'] ?? 'N/A') . '</strong>' ?>
            </div>
            <div style="display:flex; align-items:center; gap:6px;">
                <a href="/change-password" title="Change Password" style="color:var(--text-secondary); display:inline-flex; align-items:center; padding:6px; border-radius:6px; transition:var(--transition);"><?= Icon::svg('lock', '', 18) ?></a>
                <a href="/logout" title="Sign out" style="color:var(--text-secondary); display:inline-flex; align-items:center; padding:6px; border-radius:6px; transition:var(--transition);"><?= Icon::svg('logout', '', 18) ?></a>
            </div>
        </div>
    </aside>

    <!-- Main Content Shell -->
    <div class="main-wrapper">
        <!-- Sticky Glass Topbar -->
        <header class="topbar">
            <div class="topbar-left">
                <button type="button" class="mobile-menu-btn" id="sidebar-toggle" aria-label="Toggle navigation"><?= Icon::svg('menu', '', 20) ?></button>
                <div class="search-box">
                    <span><?= Icon::svg('search', '', 18) ?></span>
                    <input type="text" class="search-input" placeholder="Search consignment (AWB), client, destination...">
                    <span class="search-shortcut">Ctrl K</span>
                </div>
            </div>

            <div class="topbar-actions">
                <!-- Environment / Tenant switcher helper for development & Super Admin -->
                <?php if (Auth::isSuperAdmin()): ?>
                    <?php if ($tenant): ?>
                        <a href="/" class="btn btn-outlined" style="padding:6px 14px; font-size:12px;">Switch to Admin View</a>
                    <?php else: ?>
                        <a href="/?tenant=raipur" class="btn btn-outlined" style="padding:6px 14px; font-size:12px;">Switch to Raipur Agency View</a>
                    <?php endif; ?>
                <?php endif; ?>

                <div class="user-badge">
                    <div class="user-avatar">
                        <?= strtoupper(substr($currentUser['name'] ?? 'U', 0, 1)) ?>
                    </div>
                    <div style="font-size:13px; line-height:1.2;">
                        <div style="font-weight:600;"><?= htmlspecialchars($currentUser['name'] ?? 'User') ?></div>
                        <div style="font-size:11px; color:var(--text-secondary);"><?= htmlspecialchars($currentUser['role'] ?? 'staff') ?></div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Container -->
        <main class="page-container">
            <!-- 30-Day Expiry Notification Banner -->
            <?php if (!$isSuperAdmin && $isExpiring): ?>
                <div class="expiry-banner <?= $daysUntilExpiry <= 7 ? 'critical' : '' ?>" style="background:#fef7e0; border-left:4px solid var(--google-yellow); padding:14px 20px; border-radius:8px; margin-bottom:24px; display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        ⚠️ <strong>Subscription Notice:</strong> Your agency platform account validity expires in 
                        <strong><?= $daysUntilExpiry ?> days</strong> (<?= htmlspecialchars($tenant['validity_end'] ?? '') ?>).
                    </div>
                    <a href="/tickets" class="btn btn-primary" style="padding:6px 14px; font-size:12px;">Request Extension / Renewal</a>
                </div>
            <?php endif; ?>

            <!-- View Specific Body Content -->
            <?= $content ?? '' ?>
        </main>
    </div>
</div>

<!-- Global Application Scripts -->
<script src="/assets/js/app.js"></script>
</body>
</html>

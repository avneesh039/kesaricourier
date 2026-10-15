<?php
// src/TenantContext.php - Multi-Tenant Resolution and Brand Context

require_once __DIR__ . '/Database.php';

class TenantContext {
    private static ?array $activeTenant = null;
    private static bool $isSuperAdmin = false;
    private static bool $resolved = false;

    public static function resolve(?string $host = null, ?string $overrideSlug = null): ?array {
        if (self::$resolved) {
            return self::$activeTenant;
        }

        $db = Database::getInstance();
        $host = $host ?: ($_SERVER['HTTP_HOST'] ?? 'localhost');
        // Strip port if present
        $cleanHost = explode(':', $host)[0];

        // 1. Direct query parameter override (vital for local development & testing)
        $slug = $overrideSlug ?: ($_GET['tenant'] ?? null);

        if ($slug) {
            $tenant = $db->fetchOne("SELECT * FROM tenants WHERE slug = ? OR agency_code = ?", [$slug, $slug]);
            if ($tenant) {
                self::$activeTenant = $tenant;
                self::$isSuperAdmin = false;
                self::$resolved = true;
                return self::$activeTenant;
            }
        }

        // 2. Super Admin host check
        if (defined('SUPER_ADMIN_HOST') && $cleanHost === SUPER_ADMIN_HOST) {
            self::$isSuperAdmin = true;
            self::$activeTenant = null;
            self::$resolved = true;
            return null;
        }

        // 3. Domain / Subdomain matching via tenant_domains
        $matchedDomain = $db->fetchOne("
            SELECT t.* FROM tenants t 
            JOIN tenant_domains td ON td.tenant_id = t.id 
            WHERE td.domain = ?
        ", [$cleanHost]);

        if ($matchedDomain) {
            self::$activeTenant = $matchedDomain;
            self::$isSuperAdmin = false;
            self::$resolved = true;
            return self::$activeTenant;
        }

        // 4. Subdomain extraction fallback (e.g. raipur.kesari.com or raipur.localhost)
        $parts = explode('.', $cleanHost);
        if (count($parts) >= 2) {
            $subdomain = $parts[0];
            if ($subdomain === 'admin') {
                self::$isSuperAdmin = true;
                self::$activeTenant = null;
                self::$resolved = true;
                return null;
            }

            $tenantBySubdomain = $db->fetchOne("SELECT * FROM tenants WHERE slug = ?", [$subdomain]);
            if ($tenantBySubdomain) {
                self::$activeTenant = $tenantBySubdomain;
                self::$isSuperAdmin = false;
                self::$resolved = true;
                return self::$activeTenant;
            }
        }

        // Default: If no agency matched, treat as Super Admin / Global portal
        self::$isSuperAdmin = true;
        self::$activeTenant = null;
        self::$resolved = true;
        return null;
    }

    public static function getTenant(): ?array {
        if (!self::$resolved) {
            self::resolve();
        }
        return self::$activeTenant;
    }

    public static function getTenantId(): ?int {
        $tenant = self::getTenant();
        return $tenant ? (int)$tenant['id'] : null;
    }

    public static function isSuperAdmin(): bool {
        if (!self::$resolved) {
            self::resolve();
        }
        return self::$isSuperAdmin;
    }

    public static function getTenantName(): string {
        $tenant = self::getTenant();
        return $tenant ? $tenant['name'] : 'Kesari Logistics Network';
    }

    public static function getBrandColor(): string {
        $tenant = self::getTenant();
        return $tenant && !empty($tenant['brand_color']) ? $tenant['brand_color'] : '#1a73e8';
    }

    public static function getBrandLogo(): string {
        $tenant = self::getTenant();
        return $tenant && !empty($tenant['logo_url']) ? $tenant['logo_url'] : '/assets/images/google-logo.svg';
    }

    public static function getDaysUntilExpiry(): ?int {
        $tenant = self::getTenant();
        if (!$tenant || empty($tenant['validity_end'])) {
            return null;
        }
        $endDate = new DateTime($tenant['validity_end']);
        $now = new DateTime();
        $diff = $now->diff($endDate);
        return $diff->invert ? -$diff->days : $diff->days;
    }

    public static function isExpiringSoon(int $daysThreshold = 30): bool {
        $days = self::getDaysUntilExpiry();
        return $days !== null && $days >= 0 && $days <= $daysThreshold;
    }

    public static function isExpired(): bool {
        $days = self::getDaysUntilExpiry();
        return $days !== null && $days < 0;
    }
}

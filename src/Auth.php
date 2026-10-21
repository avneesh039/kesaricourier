<?php
// src/Auth.php - Authentication and Authorization Service

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/TenantContext.php';

class Auth {
    public static function login(string $email, string $password, ?int $tenantId = null): array {
        $db = Database::getInstance();
        $user = $db->fetchOne("SELECT * FROM users WHERE email = ?", [trim($email)]);

        if (!$user) {
            return ['success' => false, 'error' => 'Invalid email or password.'];
        }

        if (!password_verify($password, $user['password_hash'])) {
            return ['success' => false, 'error' => 'Invalid email or password.'];
        }

        if ($user['status'] !== 'active') {
            return ['success' => false, 'error' => 'Your account is deactivated. Please contact support.'];
        }

        // If trying to log in at an agency portal, verify user belongs to this tenant
        if ($tenantId !== null) {
            if ($user['role'] !== 'superadmin' && (int)$user['tenant_id'] !== $tenantId) {
                return ['success' => false, 'error' => 'You do not have access to this agency portal.'];
            }
        }

        // Check tenant status if agency user
        if ($user['tenant_id'] !== null) {
            $tenant = $db->fetchOne("SELECT * FROM tenants WHERE id = ?", [$user['tenant_id']]);
            if ($tenant) {
                if ($tenant['status'] === 'suspended') {
                    return ['success' => false, 'error' => 'Agency portal is currently suspended. Please contact platform administration.'];
                }
                if ($tenant['status'] === 'pending_kyc') {
                    return ['success' => false, 'error' => 'Agency account is awaiting KYC approval.'];
                }
            }
        }

        // Record last login
        $db->execute("UPDATE users SET last_login_at = CURRENT_TIMESTAMP WHERE id = ?", [$user['id']]);

        // Set session
        $_SESSION['user'] = [
            'id' => (int)$user['id'],
            'tenant_id' => $user['tenant_id'] ? (int)$user['tenant_id'] : null,
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ];

        return ['success' => true, 'user' => $_SESSION['user']];
    }

    public static function logout(): void {
        unset($_SESSION['user']);
        session_regenerate_id(true);
    }

    public static function check(): bool {
        return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
    }

    public static function user(): ?array {
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?int {
        return $_SESSION['user']['id'] ?? null;
    }

    public static function role(): ?string {
        return $_SESSION['user']['role'] ?? null;
    }

    public static function isSuperAdmin(): bool {
        return self::role() === 'superadmin';
    }

    public static function isAgencyAdmin(): bool {
        return self::role() === 'agency_admin' || self::role() === 'superadmin';
    }

    public static function requireLogin(): void {
        if (!self::check()) {
            $redirect = urlencode($_SERVER['REQUEST_URI'] ?? '/');
            $tenant = TenantContext::getTenant();
            $tenantParam = ($tenant && !empty($tenant['slug'])) ? '&tenant=' . urlencode($tenant['slug']) : '';
            header("Location: /login?redirect={$redirect}{$tenantParam}");
            exit;
        }
    }

    public static function requireRole(array|string $allowedRoles): void {
        self::requireLogin();
        $roles = (array)$allowedRoles;
        if (!in_array(self::role(), $roles, true) && !self::isSuperAdmin()) {
            http_response_code(403);
            die("<h1>403 Forbidden</h1><p>You do not have permission to view this resource.</p>");
        }
    }

    public static function generateCsrfToken(): string {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public static function validateCsrfToken(?string $token): bool {
        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }
}

<?php
// src/Controllers/AdminAgencyController.php - Super Admin Agency Governance & KYC Verification Controller

require_once dirname(__DIR__) . '/Database.php';
require_once dirname(__DIR__) . '/Auth.php';

class AdminAgencyController {
    public function __construct() {
        Auth::requireRole('superadmin');
    }

    public function index(): void {
        $db = Database::getInstance();
        $filter = $_GET['filter'] ?? 'all';
        $search = trim($_GET['q'] ?? '');

        $sql = "SELECT * FROM tenants WHERE 1=1";
        $params = [];

        if ($filter === 'active') {
            $sql .= " AND status = 'active'";
        } elseif ($filter === 'pending_kyc') {
            $sql .= " AND status = 'pending_kyc'";
        } elseif ($filter === 'suspended') {
            $sql .= " AND status = 'suspended'";
        } elseif ($filter === 'expiring_soon') {
            $sql .= " AND status = 'active' AND validity_end <= date('now', '+30 days') AND validity_end >= date('now')";
        }

        if (!empty($search)) {
            $sql .= " AND (name LIKE ? OR agency_code LIKE ? OR city LIKE ? OR contact_person LIKE ?)";
            $term = "%{$search}%";
            $params = array_merge($params, [$term, $term, $term, $term]);
        }

        $sql .= " ORDER BY id DESC";
        $tenants = $db->fetchAll($sql, $params);

        // Counts for tabs
        $counts = [
            'all' => (int)$db->fetchOne("SELECT COUNT(*) as c FROM tenants")['c'],
            'active' => (int)$db->fetchOne("SELECT COUNT(*) as c FROM tenants WHERE status = 'active'")['c'],
            'pending_kyc' => (int)$db->fetchOne("SELECT COUNT(*) as c FROM tenants WHERE status = 'pending_kyc'")['c'],
            'expiring_soon' => (int)$db->fetchOne("SELECT COUNT(*) as c FROM tenants WHERE status = 'active' AND validity_end <= date('now', '+30 days') AND validity_end >= date('now')")['c'],
            'suspended' => (int)$db->fetchOne("SELECT COUNT(*) as c FROM tenants WHERE status = 'suspended'")['c'],
        ];

        require ROOT_PATH . '/src/Views/admin/agencies/index.php';
    }

    public function kycQueue(): void {
        $_GET['filter'] = 'pending_kyc';
        $this->index();
    }

    public function reviewKyc(int $id): void {
        $db = Database::getInstance();
        $tenant = $db->fetchOne("SELECT * FROM tenants WHERE id = ?", [$id]);

        if (!$tenant) {
            header('Location: /admin/agencies');
            exit;
        }

        require ROOT_PATH . '/src/Views/admin/agencies/kyc-review.php';
    }

    public function approveKyc(int $id): void {
        $db = Database::getInstance();
        $tenant = $db->fetchOne("SELECT * FROM tenants WHERE id = ?", [$id]);

        if (!$tenant) {
            die("Agency not found.");
        }

        // Auto-generate next branch code if not already assigned
        $agencyCode = $tenant['agency_code'];
        if (empty($agencyCode)) {
            $maxCode = (int)$db->fetchOne("SELECT MAX(CAST(agency_code AS INTEGER)) as max_code FROM tenants")['max_code'] ?? 26;
            $agencyCode = (string)($maxCode + 1);
        }

        $validityStart = date('Y-m-d');
        $validityEnd = date('Y-m-d', strtotime('+365 days'));
        $tempPassword = 'kesari@' . random_int(1000, 9999);

        // 1. Update Tenant
        $db->execute("
            UPDATE tenants SET 
                agency_code = ?, 
                status = 'active', 
                validity_start = ?, 
                validity_end = ? 
            WHERE id = ?
        ", [$agencyCode, $validityStart, $validityEnd, $id]);

        // 2. Add Domain mapping
        $existingDomain = $db->fetchOne("SELECT * FROM tenant_domains WHERE tenant_id = ?", [$id]);
        if (!$existingDomain) {
            $db->execute("INSERT INTO tenant_domains (tenant_id, domain, is_primary) VALUES (?, ?, 1)", [
                $id,
                $tenant['slug'] . '.localhost'
            ]);
        }

        // 3. Create Agency Administrator User
        $existingUser = $db->fetchOne("SELECT * FROM users WHERE email = ?", [$tenant['email']]);
        if (!$existingUser) {
            $db->execute("
                INSERT INTO users (tenant_id, name, email, password_hash, role, status) 
                VALUES (?, ?, ?, ?, 'agency_admin', 'active')
            ", [
                $id,
                $tenant['contact_person'],
                $tenant['email'],
                password_hash($tempPassword, PASSWORD_BCRYPT)
            ]);
        }

        // 4. Record Audit Log
        $db->execute("
            INSERT INTO audit_logs (tenant_id, user_id, action, details_json, ip_address) 
            VALUES (?, ?, 'approve_kyc', ?, ?)
        ", [
            $id,
            Auth::id(),
            json_encode([
                'agency_code' => $agencyCode,
                'validity_end' => $validityEnd,
                'temp_password' => $tempPassword
            ]),
            $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ]);

        $_SESSION['flash_success'] = "✓ Agency approved! Assigned Branch Code: #{$agencyCode}. Generated Credentials: {$tenant['email']} / {$tempPassword}";
        header('Location: /admin/agencies');
        exit;
    }

    public function rejectKyc(int $id): void {
        $db = Database::getInstance();
        $reason = trim($_POST['reason'] ?? 'Documents did not meet compliance verification standards.');

        $db->execute("UPDATE tenants SET status = 'rejected', rejection_reason = ? WHERE id = ?", [$reason, $id]);

        $db->execute("
            INSERT INTO audit_logs (tenant_id, user_id, action, details_json, ip_address) 
            VALUES (?, ?, 'reject_kyc', ?, ?)
        ", [
            $id,
            Auth::id(),
            json_encode(['reason' => $reason]),
            $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ]);

        $_SESSION['flash_success'] = "Agency application marked as rejected.";
        header('Location: /admin/agencies');
        exit;
    }

    public function edit(int $id): void {
        $db = Database::getInstance();
        $tenant = $db->fetchOne("SELECT * FROM tenants WHERE id = ?", [$id]);

        if (!$tenant) {
            header('Location: /admin/agencies');
            exit;
        }

        require ROOT_PATH . '/src/Views/admin/agencies/edit.php';
    }

    public function update(int $id): void {
        $db = Database::getInstance();
        $validityStart = $_POST['validity_start'] ?? null;
        $validityEnd = $_POST['validity_end'] ?? null;
        $status = $_POST['status'] ?? 'active';
        $brandColor = $_POST['brand_color'] ?? '#1a73e8';

        $db->execute("
            UPDATE tenants SET 
                validity_start = ?, 
                validity_end = ?, 
                status = ?, 
                brand_color = ? 
            WHERE id = ?
        ", [$validityStart, $validityEnd, $status, $brandColor, $id]);

        $_SESSION['flash_success'] = "Agency configuration & validity period updated successfully.";
        header('Location: /admin/agencies');
        exit;
    }
}

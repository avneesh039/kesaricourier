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

    public function create(): void {
        $db = Database::getInstance();
        $maxCode = (int)$db->fetchOne("SELECT MAX(CAST(agency_code AS INTEGER)) as max_code FROM tenants")['max_code'] ?? 26;
        $nextBranchCode = (string)($maxCode + 1);
        require ROOT_PATH . '/src/Views/admin/agencies/create.php';
    }

    public function store(): void {
        $db = Database::getInstance();
        $name = trim($_POST['name'] ?? '');
        $tradeName = trim($_POST['trade_name'] ?? '');
        $agencyCode = trim($_POST['agency_code'] ?? '');
        $contactPerson = trim($_POST['contact_person'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $address = trim($_POST['address'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $state = trim($_POST['state'] ?? '');
        $pincode = trim($_POST['pincode'] ?? '');
        $slug = strtolower(trim($_POST['slug'] ?? ''));
        $panNo = strtoupper(trim($_POST['pan_no'] ?? ''));
        $gstin = strtoupper(trim($_POST['gstin'] ?? ''));
        $password = trim($_POST['password'] ?? '');
        $status = $_POST['status'] ?? 'active';
        $planType = $_POST['plan_type'] ?? 'Yearly';
        $planPrice = (float)($_POST['plan_price'] ?? 12000.0);
        $validityStart = $_POST['validity_start'] ?? date('Y-m-d');
        $validityEnd = $_POST['validity_end'] ?? date('Y-m-d', strtotime('+365 days'));
        $graceDays = (int)($_POST['grace_period_days'] ?? 7);
        $paymentStatus = $_POST['payment_status'] ?? 'paid';
        $paidAmount = (float)($_POST['paid_amount'] ?? $planPrice);

        if (empty($name) || empty($contactPerson) || empty($phone) || empty($email) || empty($slug) || empty($password)) {
            $_SESSION['flash_error'] = 'All mandatory fields marked with * must be filled.';
            header('Location: /admin/agencies/new');
            exit;
        }

        // Check duplicate email
        $dupEmail = $db->fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
        if ($dupEmail) {
            $_SESSION['flash_error'] = "User with email '{$email}' already exists.";
            header('Location: /admin/agencies/new');
            exit;
        }

        // Check duplicate slug
        $dupSlug = $db->fetchOne("SELECT id FROM tenants WHERE slug = ?", [$slug]);
        if ($dupSlug) {
            $_SESSION['flash_error'] = "Subdomain slug '{$slug}' is already in use. Please pick another.";
            header('Location: /admin/agencies/new');
            exit;
        }

        // Check duplicate agency_code
        if (!empty($agencyCode)) {
            $dupCode = $db->fetchOne("SELECT id FROM tenants WHERE agency_code = ?", [$agencyCode]);
            if ($dupCode) {
                $_SESSION['flash_error'] = "Branch Code '{$agencyCode}' is already assigned.";
                header('Location: /admin/agencies/new');
                exit;
            }
        }

        $uuid = 'tenant-' . bin2hex(random_bytes(8));

        // 1. Insert Tenant
        $db->execute("
            INSERT INTO tenants (
                uuid, slug, agency_code, name, trade_name, contact_person, phone, email, 
                address, city, state, pincode, pan_no, gstin, status, 
                plan_type, plan_price, grace_period_days, validity_start, validity_end
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [
            $uuid, $slug, $agencyCode ?: null, $name, $tradeName, $contactPerson, $phone, $email,
            $address, $city, $state, $pincode, $panNo, $gstin, $status,
            $planType, $planPrice, $graceDays, $validityStart, $validityEnd
        ]);

        $tenantId = (int)$db->getPdo()->lastInsertId();

        // 2. Insert Domain
        $db->execute("INSERT INTO tenant_domains (tenant_id, domain, is_primary) VALUES (?, ?, 1)", [
            $tenantId, $slug . '.localhost'
        ]);

        // 3. Insert Agency Admin User
        $db->execute("
            INSERT INTO users (tenant_id, name, email, password_hash, role, contact_no, status) 
            VALUES (?, ?, ?, ?, 'agency_admin', ?, 'active')
        ", [
            $tenantId, $contactPerson, $email, password_hash($password, PASSWORD_BCRYPT), $phone
        ]);

        // 4. Create Initial Subscription Record
        $dueAmount = max(0, $planPrice - $paidAmount);
        $db->execute("
            INSERT INTO tenant_subscriptions (
                tenant_id, plan_name, billing_cycle, amount, paid_amount, due_amount,
                payment_status, payment_mode, reference_no, start_date, end_date, grace_period_days, notes
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [
            $tenantId, $planType . ' Subscription', strtolower($planType), $planPrice, $paidAmount, $dueAmount,
            $paymentStatus, 'Direct Provisioning', 'PROV-' . date('Ymd') . '-' . $tenantId, $validityStart, $validityEnd, $graceDays,
            'Initial platform subscription provisioned manually by Super Admin'
        ]);

        // 5. Audit Log
        $db->execute("
            INSERT INTO audit_logs (tenant_id, user_id, action, details_json, ip_address) 
            VALUES (?, ?, 'manual_create_agency', ?, ?)
        ", [
            $tenantId, Auth::id(), json_encode(['agency_code' => $agencyCode, 'plan' => $planType, 'price' => $planPrice]),
            $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ]);

        $_SESSION['flash_success'] = "✓ Agency '{$name}' (#{$agencyCode}) successfully provisioned! Login: {$email} / {$password}";
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

        $subscriptions = $db->fetchAll("SELECT * FROM tenant_subscriptions WHERE tenant_id = ? ORDER BY id DESC", [$id]);
        $agencyAdmin = $db->fetchOne("SELECT * FROM users WHERE tenant_id = ? AND role = 'agency_admin'", [$id]);

        require ROOT_PATH . '/src/Views/admin/agencies/edit.php';
    }

    public function update(int $id): void {
        $db = Database::getInstance();
        $validityStart = $_POST['validity_start'] ?? null;
        $validityEnd = $_POST['validity_end'] ?? null;
        $status = $_POST['status'] ?? 'active';
        $brandColor = $_POST['brand_color'] ?? '#1a73e8';
        $planType = $_POST['plan_type'] ?? 'Yearly';
        $planPrice = (float)($_POST['plan_price'] ?? 12000.0);
        $graceDays = (int)($_POST['grace_period_days'] ?? 7);

        $db->execute("
            UPDATE tenants SET 
                validity_start = ?, 
                validity_end = ?, 
                status = ?, 
                brand_color = ?,
                plan_type = ?,
                plan_price = ?,
                grace_period_days = ?
            WHERE id = ?
        ", [$validityStart, $validityEnd, $status, $brandColor, $planType, $planPrice, $graceDays, $id]);

        $_SESSION['flash_success'] = "Agency configuration & validity period updated successfully.";
        header('Location: /admin/agencies');
        exit;
    }

    public function resetPassword(int $id): void {
        $db = Database::getInstance();
        $tenant = $db->fetchOne("SELECT * FROM tenants WHERE id = ?", [$id]);
        if (!$tenant) {
            header('Location: /admin/agencies');
            exit;
        }

        $newPassword = trim($_POST['new_password'] ?? '');
        if (strlen($newPassword) < 6) {
            $_SESSION['flash_error'] = 'Password must be at least 6 characters.';
            header("Location: /admin/agencies/{$id}/edit");
            exit;
        }

        $hash = password_hash($newPassword, PASSWORD_BCRYPT);
        $db->execute("UPDATE users SET password_hash = ? WHERE tenant_id = ? AND role = 'agency_admin'", [$hash, $id]);

        $db->execute("
            INSERT INTO audit_logs (tenant_id, user_id, action, details_json, ip_address) 
            VALUES (?, ?, 'admin_reset_password', ?, ?)
        ", [$id, Auth::id(), json_encode(['admin_email' => $tenant['email']]), $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1']);

        $_SESSION['flash_success'] = "✓ Password for agency '{$tenant['name']}' reset successfully to '{$newPassword}'.";
        header("Location: /admin/agencies/{$id}/edit");
        exit;
    }

    public function toggleStatus(int $id): void {
        $db = Database::getInstance();
        $tenant = $db->fetchOne("SELECT * FROM tenants WHERE id = ?", [$id]);
        if ($tenant) {
            $newStatus = ($tenant['status'] === 'active') ? 'suspended' : 'active';
            $db->execute("UPDATE tenants SET status = ? WHERE id = ?", [$newStatus, $id]);
            $label = ($newStatus === 'active') ? 'Activated' : 'Suspended';
            $_SESSION['flash_success'] = "Agency '{$tenant['name']}' has been {$label}.";
        }
        header('Location: /admin/agencies');
        exit;
    }
}

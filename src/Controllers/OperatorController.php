<?php
// src/Controllers/OperatorController.php - Agency Operators (Staff Desk) Master Controller

class OperatorController {
    private $db;
    private $tenantId;

    public function __construct() {
        Auth::requireRole(['agency_admin', 'superadmin']);
        $this->db = Database::getInstance();
        $this->tenantId = TenantContext::getTenantId();

        if (!$this->tenantId && !Auth::isSuperAdmin()) {
            header('Location: /login');
            exit;
        }
    }

    public function index() {
        $operators = $this->db->fetchAll("
            SELECT u.*, 
                   COUNT(c.id) as bookings_count,
                   MAX(c.booking_date) as last_booking_date
            FROM users u
            LEFT JOIN consignments c ON c.created_by_user_id = u.id AND c.tenant_id = u.tenant_id
            WHERE u.tenant_id = ? AND u.role IN ('agency_operator', 'agency_admin')
            GROUP BY u.id
            ORDER BY u.role DESC, u.created_at ASC
        ", [$this->tenantId]);

        require ROOT_PATH . '/src/Views/operators/index.php';
    }

    public function create() {
        require ROOT_PATH . '/src/Views/operators/create.php';
    }

    public function store() {
        $name = trim($_POST['name'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $phone = trim($_POST['phone'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $role = trim($_POST['role'] ?? 'agency_operator');

        if (empty($name) || empty($email) || empty($password)) {
            $_SESSION['flash_error'] = 'Name, Email and Password are mandatory.';
            header('Location: /operators/new');
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['flash_error'] = 'Please enter a valid email address.';
            header('Location: /operators/new');
            exit;
        }

        // Check if email already registered globally or in this tenant
        $existing = $this->db->fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
        if ($existing) {
            $_SESSION['flash_error'] = 'This email address is already registered in the system.';
            header('Location: /operators/new');
            exit;
        }

        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        $this->db->execute("
            INSERT INTO users (tenant_id, name, email, contact_no, password_hash, role, status)
            VALUES (?, ?, ?, ?, ?, ?, 'active')
        ", [
            $this->tenantId,
            $name,
            $email,
            $phone,
            $passwordHash,
            $role === 'agency_admin' ? 'agency_admin' : 'agency_operator'
        ]);

        $_SESSION['flash_success'] = "Operator '{$name}' created successfully with login credentials.";
        header('Location: /operators');
        exit;
    }

    public function toggleStatus($id) {
        $user = $this->db->fetchOne("SELECT * FROM users WHERE id = ? AND tenant_id = ?", [$id, $this->tenantId]);
        if (!$user) {
            header('Location: /operators');
            exit;
        }

        // Prevent blocking self
        $current = Auth::user();
        if ($current && (int)$current['id'] === (int)$id) {
            $_SESSION['flash_error'] = 'You cannot disable your own active account.';
            header('Location: /operators');
            exit;
        }

        $newStatus = ($user['status'] === 'active') ? 'blocked' : 'active';
        $this->db->execute("UPDATE users SET status = ? WHERE id = ?", [$newStatus, $id]);

        $_SESSION['flash_success'] = "Operator '{$user['name']}' status updated to " . strtoupper($newStatus);
        header('Location: /operators');
        exit;
    }

    public function delete($id) {
        $user = $this->db->fetchOne("SELECT * FROM users WHERE id = ? AND tenant_id = ?", [$id, $this->tenantId]);
        if (!$user) {
            header('Location: /operators');
            exit;
        }

        $current = Auth::user();
        if ($current && (int)$current['id'] === (int)$id) {
            $_SESSION['flash_error'] = 'You cannot delete your own active account.';
            header('Location: /operators');
            exit;
        }

        $hasBookings = $this->db->fetchOne("SELECT id FROM consignments WHERE created_by_user_id = ? LIMIT 1", [$id]);
        if ($hasBookings) {
            // Cannot hard delete because of foreign audit trace, set to blocked
            $this->db->execute("UPDATE users SET status = 'blocked' WHERE id = ?", [$id]);
            $_SESSION['flash_success'] = "Operator has booking history, so account was deactivated instead of deleted.";
        } else {
            $this->db->execute("DELETE FROM users WHERE id = ?", [$id]);
            $_SESSION['flash_success'] = "Operator deleted successfully.";
        }

        header('Location: /operators');
        exit;
    }
}

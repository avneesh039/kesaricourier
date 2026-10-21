<?php
// src/Controllers/AdminSubscriptionController.php - Super Admin Subscription & Platform Fee Ledger
require_once dirname(__DIR__) . '/Database.php';
require_once dirname(__DIR__) . '/Auth.php';

class AdminSubscriptionController {
    public function __construct() {
        Auth::requireRole('superadmin');
    }

    public function index(): void {
        $db = Database::getInstance();

        $subscriptions = $db->fetchAll("
            SELECT s.*, t.name as agency_name, t.agency_code, t.slug as agency_slug, t.status as tenant_status,
                   t.validity_end as tenant_validity_end, t.grace_period_days as tenant_grace
            FROM tenant_subscriptions s
            JOIN tenants t ON t.id = s.tenant_id
            ORDER BY s.id DESC
        ");

        $agencies = $db->fetchAll("SELECT id, name, agency_code, plan_type, plan_price, validity_end FROM tenants ORDER BY name ASC");

        $stats = [
            'total_billed' => (float)$db->fetchOne("SELECT SUM(amount) as s FROM tenant_subscriptions")['s'] ?? 0,
            'total_received' => (float)$db->fetchOne("SELECT SUM(paid_amount) as s FROM tenant_subscriptions")['s'] ?? 0,
            'total_due' => (float)$db->fetchOne("SELECT SUM(due_amount) as s FROM tenant_subscriptions")['s'] ?? 0,
            'active_plans' => (int)$db->fetchOne("SELECT COUNT(*) as c FROM tenants WHERE status = 'active'")['c'] ?? 0,
        ];

        require ROOT_PATH . '/src/Views/admin/subscriptions/index.php';
    }

    public function recordPayment(): void {
        $db = Database::getInstance();

        $subId = (int)($_POST['subscription_id'] ?? 0);
        $amount = (float)($_POST['payment_amount'] ?? 0);
        $mode = trim($_POST['payment_mode'] ?? 'Bank Transfer (NEFT)');
        $refNo = trim($_POST['reference_no'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        if ($subId === 0 || $amount <= 0) {
            $_SESSION['flash_error'] = 'Invalid subscription or payment amount.';
            header('Location: /admin/subscriptions');
            exit;
        }

        $sub = $db->fetchOne("SELECT * FROM tenant_subscriptions WHERE id = ?", [$subId]);
        if (!$sub) {
            $_SESSION['flash_error'] = 'Subscription record not found.';
            header('Location: /admin/subscriptions');
            exit;
        }

        $newPaid = $sub['paid_amount'] + $amount;
        $newDue = max(0, $sub['amount'] - $newPaid);
        $newStatus = ($newDue <= 0) ? 'paid' : 'partial';

        $db->execute("
            UPDATE tenant_subscriptions SET 
                paid_amount = ?,
                due_amount = ?,
                payment_status = ?,
                payment_mode = ?,
                reference_no = ?,
                notes = ?
            WHERE id = ?
        ", [$newPaid, $newDue, $newStatus, $mode, $refNo, $notes, $subId]);

        // If paid, ensure agency status is active
        if ($newStatus === 'paid') {
            $db->execute("UPDATE tenants SET status = 'active' WHERE id = ? AND status = 'suspended'", [$sub['tenant_id']]);
        }

        // Log audit
        $db->execute("
            INSERT INTO audit_logs (tenant_id, user_id, action, details_json, ip_address) 
            VALUES (?, ?, 'record_subscription_payment', ?, ?)
        ", [$sub['tenant_id'], Auth::id(), json_encode(['amount' => $amount, 'mode' => $mode, 'ref' => $refNo]), $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1']);

        $_SESSION['flash_success'] = "✓ Remittance of ₹" . number_format($amount, 2) . " logged successfully. Remaining due: ₹" . number_format($newDue, 2);
        header('Location: /admin/subscriptions');
        exit;
    }

    public function createPlan(): void {
        $db = Database::getInstance();

        $tenantId = (int)($_POST['tenant_id'] ?? 0);
        $planName = trim($_POST['plan_name'] ?? 'Annual Plan');
        $billingCycle = trim($_POST['billing_cycle'] ?? 'yearly');
        $amount = (float)($_POST['amount'] ?? 12000.0);
        $paidAmount = (float)($_POST['paid_amount'] ?? 0.0);
        $startDate = $_POST['start_date'] ?? date('Y-m-d');
        $endDate = $_POST['end_date'] ?? date('Y-m-d', strtotime('+365 days'));
        $graceDays = (int)($_POST['grace_period_days'] ?? 7);
        $mode = trim($_POST['payment_mode'] ?? 'Bank Transfer');
        $refNo = trim($_POST['reference_no'] ?? '');

        if ($tenantId === 0 || $amount <= 0) {
            $_SESSION['flash_error'] = 'Please select a valid agency and amount.';
            header('Location: /admin/subscriptions');
            exit;
        }

        $due = max(0, $amount - $paidAmount);
        $status = ($due <= 0) ? 'paid' : (($paidAmount > 0) ? 'partial' : 'pending');

        $db->execute("
            INSERT INTO tenant_subscriptions (
                tenant_id, plan_name, billing_cycle, amount, paid_amount, due_amount,
                payment_status, payment_mode, reference_no, start_date, end_date, grace_period_days, notes
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [
            $tenantId, $planName, $billingCycle, $amount, $paidAmount, $due,
            $status, $mode, $refNo, $startDate, $endDate, $graceDays, 'Created from Subscription Control Panel'
        ]);

        // Update tenant validity end
        $db->execute("
            UPDATE tenants SET 
                validity_start = ?,
                validity_end = ?,
                plan_type = ?,
                plan_price = ?,
                grace_period_days = ?,
                status = 'active'
            WHERE id = ?
        ", [$startDate, $endDate, ucfirst($billingCycle), $amount, $graceDays, $tenantId]);

        $_SESSION['flash_success'] = "✓ New subscription invoice & validity period assigned to agency.";
        header('Location: /admin/subscriptions');
        exit;
    }
}

<?php
// src/Controllers/PaymentController.php - Payment Receipts and Client Ledger Controller

class PaymentController {
    private $db;
    private $tenantId;

    public function __construct() {
        Auth::requireLogin();
        $this->db = Database::getInstance();
        $this->tenantId = TenantContext::getTenantId();

        if (!$this->tenantId && !Auth::isSuperAdmin()) {
            header('Location: /login');
            exit;
        }
    }

    public function index() {
        $statusFilter = $_GET['status'] ?? 'all';
        $clientId = $_GET['client_id'] ?? null;

        $params = [$this->tenantId];
        $whereSql = "WHERE i.tenant_id = ?";

        if (!empty($clientId)) {
            $whereSql .= " AND i.client_id = ?";
            $params[] = (int)$clientId;
        }

        if ($statusFilter === 'pending') {
            $whereSql .= " AND i.payment_status IN ('pending', 'partially_paid')";
        } elseif ($statusFilter === 'paid') {
            $whereSql .= " AND i.payment_status = 'paid'";
        }

        $invoices = $this->db->fetchAll("
            SELECT i.*, 
                   cl.name as client_name,
                   cl.client_code,
                   cl.phone as client_phone,
                   (i.grand_total - IFNULL(i.paid_amount, 0)) as balance_due
            FROM invoices i
            JOIN clients cl ON cl.id = i.client_id
            {$whereSql}
            ORDER BY i.invoice_date DESC, i.id DESC
        ", $params);

        // Recent payments list
        $recentPayments = $this->db->fetchAll("
            SELECT p.*, 
                   i.invoice_no,
                   cl.name as client_name,
                   cl.client_code
            FROM payments p
            JOIN invoices i ON i.id = p.invoice_id
            JOIN clients cl ON cl.id = i.client_id
            WHERE p.tenant_id = ?
            ORDER BY p.payment_date DESC, p.id DESC
            LIMIT 20
        ", [$this->tenantId]);

        // Clients list for dropdown
        $clients = $this->db->fetchAll("SELECT id, name, client_code FROM clients WHERE tenant_id = ? ORDER BY name ASC", [$this->tenantId]);

        require ROOT_PATH . '/src/Views/payments/index.php';
    }

    public function store() {
        $invoiceId = (int)($_POST['invoice_id'] ?? 0);
        $amount = (float)($_POST['amount'] ?? 0);
        $paymentDate = trim($_POST['payment_date'] ?? date('Y-m-d'));
        $paymentMode = trim($_POST['payment_mode'] ?? 'Cheque');
        $referenceNo = trim($_POST['reference_no'] ?? '');
        $notes = trim($_POST['notes'] ?? '');

        if ($invoiceId <= 0 || $amount <= 0) {
            $_SESSION['flash_error'] = 'Please enter a valid invoice and positive payment amount.';
            header('Location: /payments');
            exit;
        }

        $invoice = $this->db->fetchOne("SELECT * FROM invoices WHERE id = ? AND tenant_id = ?", [$invoiceId, $this->tenantId]);
        if (!$invoice) {
            $_SESSION['flash_error'] = 'Invoice not found.';
            header('Location: /payments');
            exit;
        }

        $currentPaid = (float)($invoice['paid_amount'] ?? 0);
        $grandTotal = (float)$invoice['grand_total'];
        $newPaid = round($currentPaid + $amount, 2);

        $newStatus = 'partially_paid';
        if ($newPaid >= ($grandTotal - 1.0)) { // within Rs 1 roundoff
            $newStatus = 'paid';
        }

        $tenant = TenantContext::getTenant();
        $agencyCode = $tenant['agency_code'] ?? '26';
        $receiptNo = 'REC-' . $agencyCode . '-' . str_pad($invoiceId, 5, '0', STR_PAD_LEFT) . '-' . substr(time(), -4);

        $currentUser = Auth::user();
        $userId = $currentUser ? $currentUser['id'] : null;

        $this->db->execute("
            INSERT INTO payments (tenant_id, invoice_id, amount, payment_date, payment_mode, reference_no, receipt_no, notes, created_by_user_id)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [
            $this->tenantId,
            $invoiceId,
            $amount,
            $paymentDate,
            $paymentMode,
            $referenceNo,
            $receiptNo,
            $notes,
            $userId
        ]);

        $this->db->execute("
            UPDATE invoices 
            SET paid_amount = ?, payment_status = ?, payment_mode = ?, payment_notes = ?
            WHERE id = ?
        ", [
            $newPaid,
            $newStatus,
            $paymentMode,
            $notes,
            $invoiceId
        ]);

        $_SESSION['flash_success'] = "Payment of ₹" . number_format($amount, 2) . " recorded successfully against Invoice #{$invoice['invoice_no']} (Receipt: {$receiptNo})";
        header('Location: /payments');
        exit;
    }

    public function receipt($id) {
        $payment = $this->db->fetchOne("
            SELECT p.*, 
                   i.invoice_no,
                   i.invoice_date,
                   i.grand_total,
                   i.paid_amount as total_invoice_paid,
                   cl.name as client_name,
                   cl.client_code,
                   cl.address as client_address,
                   cl.gstin as client_gstin,
                   cl.phone as client_phone
            FROM payments p
            JOIN invoices i ON i.id = p.invoice_id
            JOIN clients cl ON cl.id = i.client_id
            WHERE p.id = ? AND p.tenant_id = ?
        ", [$id, $this->tenantId]);

        if (!$payment) {
            echo "Payment receipt not found.";
            exit;
        }

        $tenant = TenantContext::getTenant();
        require ROOT_PATH . '/src/Views/payments/receipt.php';
    }
}

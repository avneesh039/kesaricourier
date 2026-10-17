<?php
// src/Controllers/InvoiceController.php - B2B GST Billing & Invoicing Engine
// Aggregates unbilled consignments, computes fuel & GST taxes, and issues compliant invoices.

require_once ROOT_PATH . '/src/Services/GstService.php';
require_once ROOT_PATH . '/src/Services/QrCodeService.php';

use App\Services\GstService;
use App\Services\QrCodeService;

class InvoiceController
{
    /**
     * Invoices List & Financial Dashboard
     */
    public function index(): void
    {
        Auth::requireLogin();
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();

        $clientFilter = !empty($_GET['client_id']) ? (int)$_GET['client_id'] : null;
        $statusFilter = trim($_GET['status'] ?? '');
        $dateFrom = $_GET['date_from'] ?? null;
        $dateTo = $_GET['date_to'] ?? null;
        $search = trim($_GET['q'] ?? '');

        $where = ["i.tenant_id = ?"];
        $params = [$tenantId];

        if ($clientFilter) {
            $where[] = "i.client_id = ?";
            $params[] = $clientFilter;
        }

        if ($statusFilter !== '') {
            $where[] = "i.payment_status = ?";
            $params[] = $statusFilter;
        }

        if ($dateFrom) {
            $where[] = "i.invoice_date >= ?";
            $params[] = $dateFrom;
        }

        if ($dateTo) {
            $where[] = "i.invoice_date <= ?";
            $params[] = $dateTo;
        }

        if ($search !== '') {
            $where[] = "(i.invoice_no LIKE ? OR cl.name LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        $whereSql = implode(' AND ', $where);

        $sql = "
            SELECT i.*, cl.name AS client_name, cl.client_code, cl.gstin AS client_gstin
            FROM invoices i
            JOIN clients cl ON i.client_id = cl.id
            WHERE {$whereSql}
            ORDER BY i.id DESC
        ";
        $invoices = $db->fetchAll($sql, $params);

        // Compute Financial Summary Cards
        $stats = $db->fetchOne("
            SELECT 
                COUNT(*) as total_invoices,
                COALESCE(SUM(grand_total), 0) as total_billed,
                COALESCE(SUM(paid_amount), 0) as total_paid,
                COALESCE(SUM(CASE WHEN payment_status != 'cancelled' THEN (grand_total - paid_amount) ELSE 0 END), 0) as total_due
            FROM invoices
            WHERE tenant_id = ?
        ", [$tenantId]);

        $clients = $db->fetchAll("SELECT id, client_code, name FROM clients WHERE tenant_id = ? ORDER BY name ASC", [$tenantId]);

        require ROOT_PATH . '/src/Views/invoices/index.php';
    }

    /**
     * Invoice Creation & Unbilled Consignments Aggregator
     */
    public function create(): void
    {
        Auth::requireLogin();
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();

        $clientId = !empty($_GET['client_id']) ? (int)$_GET['client_id'] : null;
        $dateFrom = $_GET['date_from'] ?? date('Y-m-01');
        $dateTo = $_GET['date_to'] ?? date('Y-m-d');

        $clients = $db->fetchAll("SELECT id, client_code, name, gstin FROM clients WHERE tenant_id = ? ORDER BY name ASC", [$tenantId]);
        $bankAccounts = $db->fetchAll("SELECT id, bank_name, account_no, ifsc_code, is_primary FROM agency_bank_accounts WHERE tenant_id = ? ORDER BY is_primary DESC, id DESC", [$tenantId]);

        $unbilledConsignments = [];
        $selectedClient = null;

        if ($clientId) {
            $selectedClient = $db->fetchOne("SELECT * FROM clients WHERE id = ? AND tenant_id = ?", [$clientId, $tenantId]);
            if ($selectedClient) {
                $where = "c.tenant_id = ? AND c.client_id = ? AND c.is_billed = 0";
                $params = [$tenantId, $clientId];

                if (!empty($_GET['date_from'])) {
                    $where .= " AND c.booking_date >= ?";
                    $params[] = $_GET['date_from'];
                }
                if (!empty($_GET['date_to'])) {
                    $where .= " AND c.booking_date <= ?";
                    $params[] = $_GET['date_to'];
                }

                $sql = "
                    SELECT c.*, d.name AS destination_name, d.code AS dest_code, d.pincode AS dest_pincode
                    FROM consignments c
                    JOIN destinations d ON c.destination_id = d.id
                    WHERE {$where}
                    ORDER BY c.booking_date ASC, c.id ASC
                ";
                $unbilledConsignments = $db->fetchAll($sql, $params);
            }
        }

        require ROOT_PATH . '/src/Views/invoices/create.php';
    }

    /**
     * Store Generated Invoice
     */
    public function store(): void
    {
        Auth::requireLogin();
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();

        $clientId = (int)($_POST['client_id'] ?? 0);
        $bankAccountId = !empty($_POST['bank_account_id']) ? (int)$_POST['bank_account_id'] : null;
        $invoiceDate = $_POST['invoice_date'] ?? date('Y-m-d');
        $dueDate = $_POST['due_date'] ?? date('Y-m-d', strtotime('+15 days'));
        $periodFrom = $_POST['period_from'] ?? date('Y-m-01');
        $periodTo = $_POST['period_to'] ?? date('Y-m-d');
        $fuelPercent = (float)($_POST['fuel_surcharge_percent'] ?? 10.0);
        $docketCharges = (float)($_POST['docket_charge_amount'] ?? 50.0);
        $selectedAwbs = $_POST['consignment_ids'] ?? [];

        if ($clientId <= 0 || empty($selectedAwbs)) {
            $_SESSION['flash_error'] = "Please select a client and at least one unbilled consignment to bill.";
            header("Location: /invoices/new?client_id={$clientId}");
            exit;
        }

        $client = $db->fetchOne("SELECT * FROM clients WHERE id = ? AND tenant_id = ?", [$clientId, $tenantId]);
        $tenant = $db->fetchOne("SELECT * FROM tenants WHERE id = ?", [$tenantId]);

        if (!$client || !$tenant) {
            $_SESSION['flash_error'] = "Invalid tenant or client.";
            header('Location: /invoices');
            exit;
        }

        // Fetch selected consignments securely
        $placeholders = implode(',', array_fill(0, count($selectedAwbs), '?'));
        $params = array_merge([$tenantId, $clientId], array_map('intval', $selectedAwbs));

        $sql = "
            SELECT c.*, d.name AS destination_name, d.code AS dest_code
            FROM consignments c
            JOIN destinations d ON c.destination_id = d.id
            WHERE c.tenant_id = ? AND c.client_id = ? AND c.is_billed = 0 AND c.id IN ({$placeholders})
        ";
        $consignments = $db->fetchAll($sql, $params);

        if (empty($consignments)) {
            $_SESSION['flash_error'] = "None of the selected consignments are eligible for billing.";
            header("Location: /invoices/new?client_id={$clientId}");
            exit;
        }

        // Calculate Subtotal from consignments
        $subtotal = 0.0;
        foreach ($consignments as $c) {
            $subtotal += (float)$c['total_amount'];
        }

        // Run GST Calculation Engine
        $agencyGstin = $tenant['gstin'] ?: '22AAACK1234F1Z5';
        $clientGstin = $client['gstin'] ?: '';
        $agencyState = $tenant['state'] ?: 'Chhattisgarh';
        $clientState = $client['state'] ?: 'Chhattisgarh';

        $gst = GstService::calculate(
            $subtotal,
            $fuelPercent,
            $docketCharges,
            $agencyGstin,
            $clientGstin,
            $agencyState,
            $clientState
        );

        // Generate sequential invoice number: e.g. KSR/26/2026/001
        $count = (int)$db->fetchOne("SELECT COUNT(*) as c FROM invoices WHERE tenant_id = ?", [$tenantId])['c'] + 1;
        $agencyCode = $tenant['agency_code'] ?: '01';
        $year = date('y', strtotime($invoiceDate));
        $invoiceNo = sprintf("INV-%s-%s-%04d", $agencyCode, $year, $count);

        // Atomic Database Transaction
        $pdo = $db->getPdo();
        $pdo->beginTransaction();

        try {
            // 1. Insert Invoice
            $stmt = $pdo->prepare("
                INSERT INTO invoices (
                    tenant_id, invoice_no, invoice_date, due_date, client_id, bank_account_id,
                    period_from, period_to, consignments_count, subtotal_amount,
                    fuel_surcharge_percent, fuel_surcharge_amount, docket_charge_amount,
                    taxable_amount, cgst_rate, cgst_amount, sgst_rate, sgst_amount,
                    igst_rate, igst_amount, total_gst_amount, round_off, grand_total,
                    payment_status, created_by_user_id
                ) VALUES (
                    ?, ?, ?, ?, ?, ?,
                    ?, ?, ?, ?,
                    ?, ?, ?,
                    ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?,
                    'pending', ?
                )
            ");

            $stmt->execute([
                $tenantId, $invoiceNo, $invoiceDate, $dueDate, $clientId, $bankAccountId,
                $periodFrom, $periodTo, count($consignments), $gst['subtotal'],
                $gst['fuel_percent'], $gst['fuel_amount'], $gst['docket_charges'],
                $gst['taxable_amount'], $gst['cgst_rate'], $gst['cgst_amount'], $gst['sgst_rate'], $gst['sgst_amount'],
                $gst['igst_rate'], $gst['igst_amount'], $gst['total_gst'], $gst['round_off'], $gst['grand_total'],
                Auth::id()
            ]);

            $invoiceId = (int)$pdo->lastInsertId();

            // 2. Insert Invoice Items and Mark Consignments Billed
            $itemStmt = $pdo->prepare("
                INSERT INTO invoice_items (
                    invoice_id, consignment_id, consignment_no, booking_date,
                    destination_name, dest_code, mode, pieces, billable_weight, amount
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $updateConsignmentStmt = $pdo->prepare("
                UPDATE consignments SET is_billed = 1, invoice_id = ? WHERE id = ? AND tenant_id = ?
            ");

            foreach ($consignments as $c) {
                $itemStmt->execute([
                    $invoiceId, $c['id'], $c['consignment_no'], $c['booking_date'],
                    $c['destination_name'], $c['dest_code'], $c['mode'], $c['pieces'],
                    $c['billable_weight'], $c['total_amount']
                ]);

                $updateConsignmentStmt->execute([$invoiceId, $c['id'], $tenantId]);
            }

            $pdo->commit();

            $_SESSION['flash_success'] = "✓ Tax Invoice #{$invoiceNo} generated successfully for ₹" . number_format($gst['grand_total'], 2);
            header("Location: /invoices/{$invoiceId}");
            exit;

        } catch (\Exception $e) {
            $pdo->rollBack();
            $_SESSION['flash_error'] = "Error generating invoice: " . $e->getMessage();
            header("Location: /invoices/new?client_id={$clientId}");
            exit;
        }
    }

    /**
     * Invoice Dossier & Payment Manager
     */
    public function show(int $id): void
    {
        Auth::requireLogin();
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();

        $invoice = $db->fetchOne("
            SELECT i.*, 
                   cl.name AS client_name, cl.client_code, cl.phone AS client_phone, cl.email AS client_email,
                   cl.address AS client_address, cl.city AS client_city, cl.state AS client_state, cl.pincode AS client_pincode,
                   cl.gstin AS client_gstin, cl.pan_no AS client_pan,
                   ba.bank_name, ba.account_no, ba.account_holder_name, ba.ifsc_code, ba.branch_name,
                   t.name AS tenant_name, t.trade_name AS tenant_trade_name, t.agency_code, t.gstin AS tenant_gstin,
                   t.phone AS tenant_phone, t.email AS tenant_email, t.address AS tenant_address
            FROM invoices i
            JOIN clients cl ON i.client_id = cl.id
            LEFT JOIN agency_bank_accounts ba ON i.bank_account_id = ba.id
            JOIN tenants t ON i.tenant_id = t.id
            WHERE i.id = ? AND i.tenant_id = ?
        ", [$id, $tenantId]);

        if (!$invoice) {
            $_SESSION['flash_error'] = "Invoice not found.";
            header('Location: /invoices');
            exit;
        }

        $items = $db->fetchAll("
            SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY booking_date ASC, id ASC
        ", [$id]);

        $amountInWords = GstService::numberToWords((int)$invoice['grand_total']) . ' Only';

        require ROOT_PATH . '/src/Views/invoices/show.php';
    }

    /**
     * Professional Printable GST Tax Invoice (A4 Layout)
     */
    public function print(int $id): void
    {
        Auth::requireLogin();
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();

        $invoice = $db->fetchOne("
            SELECT i.*, 
                   cl.name AS client_name, cl.client_code, cl.phone AS client_phone, cl.email AS client_email,
                   cl.address AS client_address, cl.city AS client_city, cl.state AS client_state, cl.pincode AS client_pincode,
                   cl.gstin AS client_gstin, cl.pan_no AS client_pan,
                   ba.bank_name, ba.account_no, ba.account_holder_name, ba.ifsc_code, ba.branch_name,
                   t.name AS tenant_name, t.trade_name AS tenant_trade_name, t.agency_code, t.gstin AS tenant_gstin,
                   t.pan_no AS tenant_pan, t.phone AS tenant_phone, t.email AS tenant_email, t.address AS tenant_address,
                   t.city AS tenant_city, t.state AS tenant_state, t.pincode AS tenant_pincode, t.logo_url AS tenant_logo
            FROM invoices i
            JOIN clients cl ON i.client_id = cl.id
            LEFT JOIN agency_bank_accounts ba ON i.bank_account_id = ba.id
            JOIN tenants t ON i.tenant_id = t.id
            WHERE i.id = ? AND i.tenant_id = ?
        ", [$id, $tenantId]);

        if (!$invoice) {
            http_response_code(404);
            echo "Invoice not found.";
            exit;
        }

        $items = $db->fetchAll("
            SELECT * FROM invoice_items WHERE invoice_id = ? ORDER BY booking_date ASC, id ASC
        ", [$id]);

        $amountInWords = GstService::numberToWords((int)$invoice['grand_total']) . ' Only';

        // Generate Verification QR code for invoice
        $host = $_SERVER['HTTP_HOST'] ?? 'kesari.in';
        $verifyUrl = "https://{$host}/invoices/verify?no=" . urlencode($invoice['invoice_no']) . "&amt=" . $invoice['grand_total'];
        $qrSvg = QrCodeService::generateSvg($verifyUrl, 100, 2);

        require ROOT_PATH . '/src/Views/invoices/print.php';
    }

    /**
     * Update Invoice Payment Status
     */
    public function updatePayment(int $id): void
    {
        Auth::requireLogin();
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();

        $status = trim($_POST['payment_status'] ?? 'pending');
        $paidAmount = (float)($_POST['paid_amount'] ?? 0.0);
        $mode = trim($_POST['payment_mode'] ?? '');
        $notes = trim($_POST['payment_notes'] ?? '');

        $invoice = $db->fetchOne("SELECT * FROM invoices WHERE id = ? AND tenant_id = ?", [$id, $tenantId]);
        if (!$invoice) {
            $_SESSION['flash_error'] = "Invoice not found.";
            header('Location: /invoices');
            exit;
        }

        if ($status === 'paid' && $paidAmount <= 0) {
            $paidAmount = (float)$invoice['grand_total'];
        }

        $db->execute("
            UPDATE invoices 
            SET payment_status = ?, paid_amount = ?, payment_mode = ?, payment_notes = ?
            WHERE id = ? AND tenant_id = ?
        ", [$status, $paidAmount, $mode, $notes, $id, $tenantId]);

        $_SESSION['flash_success'] = "✓ Payment details for Invoice #{$invoice['invoice_no']} updated successfully.";
        header("Location: /invoices/{$id}");
        exit;
    }
}

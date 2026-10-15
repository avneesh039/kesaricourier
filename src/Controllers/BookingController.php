<?php
// src/Controllers/BookingController.php - Consignment Booking & Operational Tracking Controller

require_once dirname(__DIR__) . '/Database.php';
require_once dirname(__DIR__) . '/Auth.php';
require_once dirname(__DIR__) . '/TenantContext.php';

class BookingController {
    public function __construct() {
        Auth::requireLogin();
    }

    public function create(): void {
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();

        $clients = $db->fetchAll("SELECT id, client_code, name, phone, address FROM clients WHERE tenant_id = ? AND status = 'active' ORDER BY name ASC", [$tenantId]);
        $destinations = $db->fetchAll("
            SELECT d.id, d.code, d.name, s.name as state_name 
            FROM destinations d 
            JOIN states s ON s.id = d.state_id 
            WHERE d.is_active = 1 
            ORDER BY d.name ASC
        ");

        // Generate next sequential AWB / Consignment Number
        $yearPrefix = date('y');
        $randomSeq = (string)random_int(100000000000, 999999999999);
        $nextAwb = $yearPrefix . substr($randomSeq, 0, 12);

        require ROOT_PATH . '/src/Views/bookings/create.php';
    }

    public function store(): void {
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();

        $bookingDate = $_POST['booking_date'] ?? date('Y-m-d');
        $awb = trim($_POST['consignment_no'] ?? '');
        $clientId = (int)($_POST['client_id'] ?? 0);
        $destId = (int)($_POST['destination_id'] ?? 0);
        $mode = $_POST['mode'] ?? 'Dox';
        $sendBy = $_POST['send_by'] ?? 'Air';
        $pieces = max(1, (int)($_POST['pieces'] ?? 1));

        $deadWeight = (float)($_POST['dead_weight'] ?? 0.1);
        $length = !empty($_POST['length_cm']) ? (float)$_POST['length_cm'] : null;
        $width = !empty($_POST['width_cm']) ? (float)$_POST['width_cm'] : null;
        $height = !empty($_POST['height_cm']) ? (float)$_POST['height_cm'] : null;

        $volumetricWeight = null;
        if ($length && $width && $height) {
            $volumetricWeight = round(($length * $width * $height) / 5000, 2);
        }

        $billableWeight = max($deadWeight, (float)($volumetricWeight ?: $deadWeight));

        $senderName = trim($_POST['sender_name'] ?? '');
        $senderPhone = trim($_POST['sender_phone'] ?? '');
        $recipientName = trim($_POST['recipient_name'] ?? '');
        $recipientPhone = trim($_POST['recipient_phone'] ?? '');
        $recipientAddress = trim($_POST['recipient_address'] ?? '');

        $subtotal = (float)($_POST['subtotal'] ?? 0);
        $discountType = $_POST['discount_type'] ?? 'fixed';
        $discountVal = (float)($_POST['discount_value'] ?? 0);

        $discountAmount = 0.0;
        if ($discountType === 'percent') {
            $discountAmount = ($subtotal * $discountVal) / 100;
        } else {
            $discountAmount = min($subtotal, $discountVal);
        }

        $insuredVal = (float)($_POST['insured_value'] ?? 0);
        $otherCharges = (float)($_POST['other_charges'] ?? 0);

        $totalAmount = max(0, ($subtotal - $discountAmount) + $otherCharges);

        if (empty($awb) || $clientId === 0 || $destId === 0 || empty($recipientName)) {
            $_SESSION['flash_error'] = 'Consignment AWB, Client, Destination, and Recipient Name are mandatory.';
            header('Location: /bookings/new');
            exit;
        }

        // Check unique AWB
        $exists = $db->fetchOne("SELECT id FROM consignments WHERE consignment_no = ?", [$awb]);
        if ($exists) {
            $_SESSION['flash_error'] = "Consignment Number {$awb} already exists.";
            header('Location: /bookings/new');
            exit;
        }

        $db->execute("
            INSERT INTO consignments (
                tenant_id, booking_date, consignment_no, client_id, 
                sender_name, sender_phone, recipient_name, recipient_phone, recipient_address, 
                destination_id, mode, send_by, pieces, dead_weight, 
                length_cm, width_cm, height_cm, volumetric_weight, billable_weight, 
                subtotal, discount_type, discount_amount, total_amount, 
                insured_value, other_charges, status, is_billed, created_by_user_id
            ) VALUES (
                ?, ?, ?, ?, 
                ?, ?, ?, ?, ?, 
                ?, ?, ?, ?, ?, 
                ?, ?, ?, ?, ?, 
                ?, ?, ?, ?, 
                ?, ?, 'booked', 0, ?
            )
        ", [
            $tenantId, $bookingDate, $awb, $clientId,
            $senderName, $senderPhone, $recipientName, $recipientPhone, $recipientAddress,
            $destId, $mode, $sendBy, $pieces, $deadWeight,
            $length, $width, $height, $volumetricWeight, $billableWeight,
            $subtotal, $discountType, $discountAmount, $totalAmount,
            $insuredVal, $otherCharges, Auth::id()
        ]);

        $consignmentId = (int)$db->lastInsertId();
        $_SESSION['flash_success'] = "✓ Consignment #{$awb} booked successfully! Ready for thermal label printing.";
        header("Location: /print-labels?awb={$awb}");
        exit;
    }

    public function index(): void {
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();

        $search = trim($_GET['q'] ?? '');
        $statusFilter = trim($_GET['status'] ?? '');
        $clientFilter = !empty($_GET['client_id']) ? (int)$_GET['client_id'] : null;
        $dateFrom = $_GET['date_from'] ?? null;
        $dateTo = $_GET['date_to'] ?? null;

        $sql = "
            SELECT c.*, cl.name as client_name, cl.client_code, d.name as destination_name, d.code as dest_code, s.name as state_name
            FROM consignments c
            JOIN clients cl ON cl.id = c.client_id
            JOIN destinations d ON d.id = c.destination_id
            JOIN states s ON s.id = d.state_id
            WHERE c.tenant_id = ?
        ";
        $params = [$tenantId];

        if (!empty($search)) {
            $sql .= " AND (c.consignment_no LIKE ? OR c.recipient_name LIKE ? OR c.recipient_phone LIKE ?)";
            $term = "%{$search}%";
            $params = array_merge($params, [$term, $term, $term]);
        }

        if (!empty($statusFilter)) {
            $sql .= " AND c.status = ?";
            $params[] = $statusFilter;
        }

        if ($clientFilter) {
            $sql .= " AND c.client_id = ?";
            $params[] = $clientFilter;
        }

        if ($dateFrom) {
            $sql .= " AND c.booking_date >= ?";
            $params[] = $dateFrom;
        }

        if ($dateTo) {
            $sql .= " AND c.booking_date <= ?";
            $params[] = $dateTo;
        }

        $sql .= " ORDER BY c.id DESC LIMIT 50";
        $consignments = $db->fetchAll($sql, $params);
        $clients = $db->fetchAll("SELECT id, client_code, name FROM clients WHERE tenant_id = ? ORDER BY name ASC", [$tenantId]);

        require ROOT_PATH . '/src/Views/bookings/index.php';
    }

    public function show(int $id): void {
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();

        $c = $db->fetchOne("
            SELECT c.*, cl.name as client_name, cl.client_code, cl.phone as client_phone, cl.gstin as client_gstin,
                   d.name as destination_name, d.code as dest_code, d.pincode as dest_pincode,
                   s.name as state_name, z.name as zone_name
            FROM consignments c
            JOIN clients cl ON cl.id = c.client_id
            JOIN destinations d ON d.id = c.destination_id
            JOIN states s ON s.id = d.state_id
            JOIN operational_zones z ON z.id = s.zone_id
            WHERE c.id = ? AND c.tenant_id = ?
        ", [$id, $tenantId]);

        if (!$c) {
            header('Location: /bookings');
            exit;
        }

        require ROOT_PATH . '/src/Views/bookings/show.php';
    }

    public function updateStatus(int $id): void {
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();
        $status = $_POST['status'] ?? 'in_transit';

        $db->execute("UPDATE consignments SET status = ? WHERE id = ? AND tenant_id = ?", [$status, $id, $tenantId]);

        $_SESSION['flash_success'] = "Consignment status updated to " . ucfirst($status);
        header("Location: /bookings/{$id}");
        exit;
    }
}

<?php
// src/Controllers/PrintController.php - Thermal Label Printing Controller
// Handles 4x6" commercial-grade thermal label generation, barcodes, QR codes, and batch printing

require_once ROOT_PATH . '/src/Services/BarcodeService.php';
require_once ROOT_PATH . '/src/Services/QrCodeService.php';

use App\Services\BarcodeService;
use App\Services\QrCodeService;

class PrintController
{
    /**
     * Helper to load rich consignment details with tenant, client, and destination data.
     */
    private static function getConsignmentQuery(): string
    {
        return "
            SELECT 
                c.*,
                cl.name AS client_name,
                cl.contact_person AS client_contact,
                cl.client_code,
                cl.phone AS client_phone,
                cl.gstin AS client_gstin,
                cl.address AS client_address,
                cl.city AS client_city,
                cl.pincode AS client_pincode,
                d.name AS destination_name,
                d.code AS dest_code,
                d.pincode AS dest_pincode,
                s.name AS state_name,
                z.name AS zone_name,
                z.code AS zone_code,
                t.name AS tenant_name,
                t.trade_name AS tenant_trade_name,
                t.agency_code,
                t.phone AS tenant_phone,
                t.email AS tenant_email,
                t.address AS tenant_address,
                t.city AS tenant_city,
                t.logo_url AS tenant_logo
            FROM consignments c
            LEFT JOIN clients cl ON c.client_id = cl.id
            LEFT JOIN destinations d ON c.destination_id = d.id
            LEFT JOIN states s ON d.state_id = s.id
            LEFT JOIN operational_zones z ON s.zone_id = z.id
            LEFT JOIN tenants t ON c.tenant_id = t.id
        ";
    }

    /**
     * Thermal Print Hub Dashboard
     */
    public function index(): void
    {
        Auth::requireLogin();
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();

        $search = trim($_GET['q'] ?? '');
        $clientFilter = !empty($_GET['client_id']) ? (int)$_GET['client_id'] : null;
        $dateFrom = $_GET['date_from'] ?? null;
        $dateTo = $_GET['date_to'] ?? null;
        $targetAwb = trim($_GET['awb'] ?? '');

        $where = ["c.tenant_id = ?"];
        $params = [$tenantId];

        if ($targetAwb !== '') {
            $where[] = "c.consignment_no = ?";
            $params[] = $targetAwb;
        }

        if ($search !== '') {
            $where[] = "(c.consignment_no LIKE ? OR c.recipient_name LIKE ? OR c.recipient_phone LIKE ?)";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
            $params[] = "%{$search}%";
        }

        if ($clientFilter !== null) {
            $where[] = "c.client_id = ?";
            $params[] = $clientFilter;
        }

        if ($dateFrom) {
            $where[] = "c.booking_date >= ?";
            $params[] = $dateFrom;
        }
        if ($dateTo) {
            $where[] = "c.booking_date <= ?";
            $params[] = $dateTo;
        }

        $whereSql = implode(' AND ', $where);

        $sql = self::getConsignmentQuery() . " WHERE {$whereSql} ORDER BY c.id DESC LIMIT 100";
        $consignments = $db->fetchAll($sql, $params);

        // Fetch client list for filter dropdown
        $clients = $db->fetchAll("SELECT id, client_code, name FROM clients WHERE tenant_id = ? ORDER BY name ASC", [$tenantId]);

        require ROOT_PATH . '/src/Views/print/index.php';
    }

    /**
     * Render single 4x6" thermal label
     */
    public function single(): void
    {
        Auth::requireLogin();
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();

        $awb = trim($_GET['awb'] ?? '');
        $id = !empty($_GET['id']) ? (int)$_GET['id'] : null;

        if ($awb === '' && $id === null) {
            $_SESSION['flash_error'] = "Please provide an AWB number or Consignment ID to print.";
            header('Location: /bookings');
            exit;
        }

        $sql = self::getConsignmentQuery();
        if ($awb !== '') {
            $sql .= " WHERE c.consignment_no = ? AND c.tenant_id = ?";
            $params = [$awb, $tenantId];
        } else {
            $sql .= " WHERE c.id = ? AND c.tenant_id = ?";
            $params = [$id, $tenantId];
        }

        $consignment = $db->fetchOne($sql, $params);

        if (!$consignment) {
            http_response_code(404);
            echo "<h2 style='font-family:sans-serif; text-align:center; margin-top:50px;'>Consignment label not found.</h2>";
            exit;
        }

        // Generate Code 128 Barcode SVG
        $barcodeSvg = BarcodeService::generateSvg($consignment['consignment_no'], 50, 1.8, true);

        // Generate QR Code SVG with package metadata and tracking link
        $host = $_SERVER['HTTP_HOST'] ?? 'kesari.in';
        $trackingUrl = "https://{$host}/track?awb=" . urlencode($consignment['consignment_no']);
        $qrSvg = QrCodeService::generateSvg($trackingUrl, 115, 2);

        $autoprint = !empty($_GET['autoprint']);

        require ROOT_PATH . '/src/Views/print/label-single.php';
    }

    /**
     * Render batch 4x6" thermal labels with page breaks
     */
    public function batch(): void
    {
        Auth::requireLogin();
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();

        $awbs = [];
        if (!empty($_POST['awbs']) && is_array($_POST['awbs'])) {
            $awbs = array_filter(array_map('trim', $_POST['awbs']));
        } elseif (!empty($_GET['awbs'])) {
            $awbs = array_filter(array_map('trim', explode(',', $_GET['awbs'])));
        }

        if (empty($awbs)) {
            $_SESSION['flash_error'] = "No consignments selected for batch printing.";
            header('Location: /print-labels');
            exit;
        }

        // Limit batch print to 100 at a time for printer safety
        $awbs = array_slice($awbs, 0, 100);
        $placeholders = implode(',', array_fill(0, count($awbs), '?'));
        $params = array_merge([$tenantId], $awbs);

        $sql = self::getConsignmentQuery() . " WHERE c.tenant_id = ? AND c.consignment_no IN ({$placeholders}) ORDER BY c.id ASC";
        $consignments = $db->fetchAll($sql, $params);

        if (empty($consignments)) {
            $_SESSION['flash_error'] = "No valid consignments found matching selection.";
            header('Location: /print-labels');
            exit;
        }

        $host = $_SERVER['HTTP_HOST'] ?? 'kesari.in';
        $labels = [];
        foreach ($consignments as $c) {
            $barcodeSvg = BarcodeService::generateSvg($c['consignment_no'], 48, 1.7, true);
            $trackingUrl = "https://{$host}/track?awb=" . urlencode($c['consignment_no']);
            $qrSvg = QrCodeService::generateSvg($trackingUrl, 110, 2);
            $labels[] = [
                'consignment' => $c,
                'barcodeSvg' => $barcodeSvg,
                'qrSvg' => $qrSvg,
            ];
        }

        $autoprint = !empty($_GET['autoprint']) || !empty($_POST['autoprint']);

        require ROOT_PATH . '/src/Views/print/label-batch.php';
    }
}

<?php
// src/Controllers/ClientController.php - Agency B2B Corporate Client Management Controller

require_once dirname(__DIR__) . '/Database.php';
require_once dirname(__DIR__) . '/Auth.php';
require_once dirname(__DIR__) . '/TenantContext.php';

class ClientController {
    public function __construct() {
        Auth::requireLogin();
    }

    public function index(): void {
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();
        $search = trim($_GET['q'] ?? '');

        $sql = "SELECT * FROM clients WHERE tenant_id = ?";
        $params = [$tenantId];

        if (!empty($search)) {
            $sql .= " AND (name LIKE ? OR client_code LIKE ? OR city LIKE ? OR phone LIKE ?)";
            $term = "%{$search}%";
            $params = array_merge($params, [$term, $term, $term, $term]);
        }

        $sql .= " ORDER BY CAST(client_code AS INTEGER) DESC";
        $clients = $db->fetchAll($sql, $params);

        require ROOT_PATH . '/src/Views/clients/index.php';
    }

    public function create(): void {
        $db = Database::getInstance();
        $states = $db->fetchAll("SELECT * FROM states ORDER BY name ASC");
        require ROOT_PATH . '/src/Views/clients/create.php';
    }

    public function store(): void {
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();

        $name = strtoupper(trim($_POST['name'] ?? ''));
        $person = trim($_POST['contact_person'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $email = strtolower(trim($_POST['email'] ?? ''));
        $address = trim($_POST['address'] ?? '');
        $city = trim($_POST['city'] ?? '');
        $state = trim($_POST['state'] ?? '');
        $pincode = trim($_POST['pincode'] ?? '');
        $gstin = strtoupper(trim($_POST['gstin'] ?? ''));
        $pan = strtoupper(trim($_POST['pan_no'] ?? ''));

        if (empty($name) || empty($phone)) {
            $_SESSION['flash_error'] = 'Client Name and Contact Phone are mandatory.';
            header('Location: /clients/new');
            exit;
        }

        // Auto-assign next Client Code
        $maxCode = (int)$db->fetchOne("
            SELECT MAX(CAST(client_code AS INTEGER)) as max_code 
            FROM clients WHERE tenant_id = ?
        ", [$tenantId])['max_code'] ?? 92;
        $clientCode = (string)($maxCode + 1);

        $db->execute("
            INSERT INTO clients (tenant_id, client_code, name, contact_person, phone, email, address, city, state, pincode, gstin, pan_no, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')
        ", [$tenantId, $clientCode, $name, $person, $phone, $email, $address, $city, $state, $pincode, $gstin, $pan]);

        $_SESSION['flash_success'] = "✓ B2B Client '{$name}' created with Client Code #{$clientCode}.";
        header('Location: /clients');
        exit;
    }

    public function apiSearch(): void {
        header('Content-Type: application/json');
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();
        $q = trim($_GET['q'] ?? '');

        $sql = "SELECT id, client_code, name, phone, city, address, gstin FROM clients WHERE tenant_id = ? AND status = 'active'";
        $params = [$tenantId];

        if (!empty($q)) {
            $sql .= " AND (name LIKE ? OR client_code LIKE ?)";
            $params[] = "%{$q}%";
            $params[] = "%{$q}%";
        }

        $sql .= " ORDER BY name ASC LIMIT 10";
        $results = $db->fetchAll($sql, $params);

        echo json_encode($results);
        exit;
    }
}

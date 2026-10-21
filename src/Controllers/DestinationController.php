<?php
// src/Controllers/DestinationController.php - Super Admin Centralized Destination Master Controller

require_once dirname(__DIR__) . '/Database.php';
require_once dirname(__DIR__) . '/Auth.php';

class DestinationController {
    public function index(): void {
        Auth::requireRole('superadmin');
        $db = Database::getInstance();

        $search = trim($_GET['q'] ?? '');
        $stateFilter = trim($_GET['state'] ?? '');

        $sql = "
            SELECT d.*, s.name as state_name, s.code as state_code, z.name as zone_name, z.code as zone_code
            FROM destinations d
            JOIN states s ON s.id = d.state_id
            JOIN operational_zones z ON z.id = s.zone_id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($search)) {
            $sql .= " AND (d.name LIKE ? OR d.code LIKE ? OR d.pincode LIKE ?)";
            $term = "%{$search}%";
            $params = array_merge($params, [$term, $term, $term]);
        }

        if (!empty($stateFilter)) {
            $sql .= " AND s.code = ?";
            $params[] = $stateFilter;
        }

        $sql .= " ORDER BY s.name ASC, d.name ASC";
        $destinations = $db->fetchAll($sql, $params);
        $states = $db->fetchAll("SELECT * FROM states ORDER BY name ASC");

        require ROOT_PATH . '/src/Views/admin/destinations/index.php';
    }

    public function create(): void {
        Auth::requireRole('superadmin');
        $db = Database::getInstance();
        $states = $db->fetchAll("SELECT * FROM states ORDER BY name ASC");
        require ROOT_PATH . '/src/Views/admin/destinations/create.php';
    }

    public function store(): void {
        Auth::requireRole('superadmin');
        $db = Database::getInstance();

        $name = strtoupper(trim($_POST['name'] ?? ''));
        $code = strtoupper(trim($_POST['code'] ?? ''));
        $stateId = (int)($_POST['state_id'] ?? 0);
        $pincode = trim($_POST['pincode'] ?? '');
        $isHub = isset($_POST['is_hub']) ? 1 : 0;

        if (empty($name) || empty($code) || $stateId === 0) {
            $_SESSION['flash_error'] = 'Destination Name, Code, and State are mandatory.';
            header('Location: /admin/destinations/new');
            exit;
        }

        // Check duplicate code
        $exists = $db->fetchOne("SELECT id FROM destinations WHERE code = ?", [$code]);
        if ($exists) {
            $_SESSION['flash_error'] = "Destination code '{$code}' already exists.";
            header('Location: /admin/destinations/new');
            exit;
        }

        // Check duplicate pincode to prevent routing confusion
        if (!empty($pincode)) {
            $pincodeExists = $db->fetchOne("
                SELECT d.id, d.name, d.code, s.name as state_name 
                FROM destinations d 
                JOIN states s ON s.id = d.state_id 
                WHERE d.pincode = ?
            ", [$pincode]);
            if ($pincodeExists) {
                $_SESSION['flash_error'] = "⚠️ Pincode '{$pincode}' is already registered for {$pincodeExists['name']} (#{$pincodeExists['code']}), {$pincodeExists['state_name']}. Duplicate pincodes are not allowed in Central Destination Master.";
                header('Location: /admin/destinations/new');
                exit;
            }
        }

        $db->execute("
            INSERT INTO destinations (code, name, state_id, pincode, is_hub, is_active) 
            VALUES (?, ?, ?, ?, ?, 1)
        ", [$code, $name, $stateId, $pincode ?: null, $isHub]);

        $_SESSION['flash_success'] = "✓ Master Destination '{$name}' (#{$code}) created successfully.";
        header('Location: /admin/destinations');
        exit;
    }

    public function toggleActive(int $id): void {
        Auth::requireRole('superadmin');
        $db = Database::getInstance();
        $dest = $db->fetchOne("SELECT id, name, is_active FROM destinations WHERE id = ?", [$id]);
        if ($dest) {
            $newStatus = $dest['is_active'] ? 0 : 1;
            $db->execute("UPDATE destinations SET is_active = ? WHERE id = ?", [$newStatus, $id]);
            $statusLabel = $newStatus ? 'Activated' : 'Deactivated / Service Suspended';
            $_SESSION['flash_success'] = "Destination '{$dest['name']}' is now {$statusLabel}.";
        }
        header('Location: /admin/destinations');
        exit;
    }

    public function searchApi(): void {
        header('Content-Type: application/json');
        $db = Database::getInstance();
        $q = trim($_GET['q'] ?? '');

        if (strlen($q) < 1) {
            echo json_encode([]);
            exit;
        }

        $destinations = $db->fetchAll("
            SELECT d.id, d.code, d.name, d.pincode, s.name as state_name, s.code as state_code, z.name as zone_name, z.code as zone_code
            FROM destinations d
            JOIN states s ON s.id = d.state_id
            JOIN operational_zones z ON z.id = s.zone_id
            WHERE d.is_active = 1 AND (d.name LIKE ? OR d.code LIKE ? OR d.pincode LIKE ?)
            ORDER BY d.is_hub DESC, d.name ASC
            LIMIT 15
        ", ["%{$q}%", "%{$q}%", "%{$q}%"]);

        echo json_encode($destinations);
        exit;
    }
}

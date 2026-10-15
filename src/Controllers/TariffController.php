<?php
// src/Controllers/TariffController.php - Multi-Slab Tariff Matrix & Real-Time Calculation Engine

require_once dirname(__DIR__) . '/Database.php';
require_once dirname(__DIR__) . '/Auth.php';
require_once dirname(__DIR__) . '/TenantContext.php';

class TariffController {
    public function __construct() {
        // Let calculateApi be public for AJAX rate queries
    }

    public function index(): void {
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();

        $tariffs = $db->fetchAll("
            SELECT t.*, c.name as client_name, c.client_code, z.name as zone_name, z.code as zone_code
            FROM tariffs t
            LEFT JOIN clients c ON c.id = t.client_id
            JOIN operational_zones z ON z.id = t.zone_id
            WHERE t.tenant_id = ?
            ORDER BY t.client_id ASC, t.send_by ASC, z.name ASC
        ", [$tenantId]);

        foreach ($tariffs as &$t) {
            $t['slabs'] = $db->fetchAll("SELECT * FROM tariff_slabs WHERE tariff_id = ? ORDER BY weight_from ASC", [$t['id']]);
        }

        require ROOT_PATH . '/src/Views/tariffs/index.php';
    }

    public function create(): void {
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();
        $clients = $db->fetchAll("SELECT id, client_code, name FROM clients WHERE tenant_id = ? ORDER BY name ASC", [$tenantId]);
        $zones = $db->fetchAll("SELECT * FROM operational_zones ORDER BY name ASC");
        require ROOT_PATH . '/src/Views/tariffs/create.php';
    }

    public function store(): void {
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();

        $clientId = !empty($_POST['client_id']) ? (int)$_POST['client_id'] : null;
        $sendBy = $_POST['send_by'] ?? 'Air';
        $mode = $_POST['mode'] ?? 'Dox';
        $zoneId = (int)($_POST['zone_id'] ?? 1);
        $addStep = (float)($_POST['additional_weight_step'] ?? 0.5);
        $addRate = (float)($_POST['additional_weight_rate'] ?? 30.0);

        // Save Tariff Header
        $db->execute("
            INSERT INTO tariffs (tenant_id, client_id, send_by, mode, zone_id, additional_weight_step, additional_weight_rate) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ", [$tenantId, $clientId, $sendBy, $mode, $zoneId, $addStep, $addRate]);
        $tariffId = (int)$db->lastInsertId();

        // Save Slabs
        $fromWeights = $_POST['weight_from'] ?? [];
        $toWeights = $_POST['weight_to'] ?? [];
        $rates = $_POST['slab_rate'] ?? [];

        for ($i = 0; $i < count($fromWeights); $i++) {
            if (isset($fromWeights[$i]) && isset($toWeights[$i]) && isset($rates[$i])) {
                $from = (float)$fromWeights[$i];
                $to = (float)$toWeights[$i];
                $rate = (float)$rates[$i];
                if ($to > $from && $rate > 0) {
                    $db->execute("
                        INSERT INTO tariff_slabs (tariff_id, weight_from, weight_to, rate) 
                        VALUES (?, ?, ?, ?)
                    ", [$tariffId, $from, $to, $rate]);
                }
            }
        }

        $_SESSION['flash_success'] = "✓ Rate matrix tariff created successfully.";
        header('Location: /tariffs');
        exit;
    }

    public function calculateApi(): void {
        header('Content-Type: application/json');
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();

        $clientId = !empty($_GET['client_id']) ? (int)$_GET['client_id'] : null;
        $destId = (int)($_GET['destination_id'] ?? 0);
        $sendBy = $_GET['send_by'] ?? 'Air';
        $mode = $_GET['mode'] ?? 'Dox';
        $weight = max(0.1, (float)($_GET['weight'] ?? 0.1));

        // 1. Resolve Destination Zone
        $destination = $db->fetchOne("
            SELECT d.*, s.zone_id, z.name as zone_name, z.code as zone_code
            FROM destinations d
            JOIN states s ON s.id = d.state_id
            JOIN operational_zones z ON z.id = s.zone_id
            WHERE d.id = ?
        ", [$destId]);

        $zoneId = $destination ? (int)$destination['zone_id'] : 2;
        $zoneName = $destination ? $destination['zone_name'] : 'Standard Zone';

        // 2. Query Client-Specific Tariff, fallback to Agency Default Tariff
        $tariff = null;
        if ($clientId) {
            $tariff = $db->fetchOne("
                SELECT * FROM tariffs 
                WHERE tenant_id = ? AND client_id = ? AND send_by = ? AND mode = ? AND zone_id = ?
            ", [$tenantId, $clientId, $sendBy, $mode, $zoneId]);
        }

        if (!$tariff) {
            $tariff = $db->fetchOne("
                SELECT * FROM tariffs 
                WHERE tenant_id = ? AND client_id IS NULL AND send_by = ? AND mode = ? AND zone_id = ?
            ", [$tenantId, $sendBy, $mode, $zoneId]);
        }

        $subtotal = 0.0;
        $breakdown = '';

        if ($tariff) {
            $slabs = $db->fetchAll("
                SELECT * FROM tariff_slabs 
                WHERE tariff_id = ? 
                ORDER BY weight_to ASC
            ", [$tariff['id']]);

            $matchedSlab = null;
            $highestSlab = null;

            foreach ($slabs as $slab) {
                $highestSlab = $slab;
                if ($weight >= (float)$slab['weight_from'] && $weight <= (float)$slab['weight_to']) {
                    $matchedSlab = $slab;
                    break;
                }
            }

            if ($matchedSlab) {
                $subtotal = (float)$matchedSlab['rate'];
                $breakdown = "Slab {$matchedSlab['weight_from']}–{$matchedSlab['weight_to']} kg: ₹" . number_format($subtotal, 2);
            } elseif ($highestSlab && $weight > (float)$highestSlab['weight_to']) {
                $baseRate = (float)$highestSlab['rate'];
                $excess = $weight - (float)$highestSlab['weight_to'];
                $step = max(0.1, (float)$tariff['additional_weight_step']);
                $stepRate = (float)$tariff['additional_weight_rate'];
                $stepCount = ceil($excess / $step);
                $additionalCharge = $stepCount * $stepRate;
                $subtotal = $baseRate + $additionalCharge;
                $breakdown = "Base ₹{$baseRate} + {$stepCount}x extra weight step: ₹" . number_format($additionalCharge, 2);
            }
        }

        // Fallback default formula if no tariff table matches
        if ($subtotal <= 0) {
            if ($sendBy === 'Air') {
                $subtotal = 40.0 + (max(0, ceil(($weight - 0.25) / 0.5)) * 30.0);
            } else {
                $subtotal = 100.0 + (max(0, ceil(($weight - 1.0) / 1.0)) * 45.0);
            }
            $breakdown = "Standard baseline rate applied";
        }

        echo json_encode([
            'success' => true,
            'subtotal' => round($subtotal, 2),
            'breakdown' => $breakdown,
            'zone_name' => $zoneName,
            'billable_weight' => $weight
        ]);
        exit;
    }
}

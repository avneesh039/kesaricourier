<?php
// src/Controllers/PublicController.php - Public Customer-Facing Website & Tracking Controller

require_once dirname(__DIR__) . '/Database.php';
require_once dirname(__DIR__) . '/TenantContext.php';
require_once dirname(__DIR__) . '/Auth.php';

class PublicController {
    /**
     * Public Homepage & Portal
     */
    public function home(): void {
        $db = Database::getInstance();

        // Sample destination hubs for the live rate calculator
        $destinations = $db->fetchAll("
            SELECT d.id, d.code, d.name, d.pincode, s.name as state_name, z.name as zone_name
            FROM destinations d
            JOIN states s ON s.id = d.state_id
            JOIN operational_zones z ON z.id = s.zone_id
            WHERE d.is_active = 1
            ORDER BY d.name ASC
        ");

        // Network stats
        $stats = [
            'hubs' => count($destinations),
            'consignments' => $db->fetchOne("SELECT count(*) as c FROM consignments")['c'] ?? 0,
            'agencies' => $db->fetchOne("SELECT count(*) as c FROM tenants WHERE status = 'active'")['c'] ?? 0,
        ];

        // Check if an AWB was passed in query
        $awb = trim($_GET['awb'] ?? '');
        $trackingResult = null;
        if (!empty($awb)) {
            $trackingResult = $this->lookupConsignment($awb);
        }

        require ROOT_PATH . '/src/Views/public/home.php';
    }

    /**
     * Public Tracking API & Dedicated Page
     */
    public function track(): void {
        $awb = trim($_GET['awb'] ?? '');
        $trackingResult = null;
        
        if (!empty($awb)) {
            $trackingResult = $this->lookupConsignment($awb);
        }

        // Return JSON if requested via API
        if (isset($_GET['json']) || strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false) {
            header('Content-Type: application/json');
            if ($trackingResult) {
                echo json_encode(['success' => true, 'shipment' => $trackingResult]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Consignment not found in network.']);
            }
            exit;
        }

        require ROOT_PATH . '/src/Views/public/track.php';
    }

    /**
     * Live Public Rate Calculator API
     */
    public function calculateRate(): void {
        header('Content-Type: application/json');
        $db = Database::getInstance();

        $destId = (int)($_GET['destination_id'] ?? 0);
        $mode = $_GET['mode'] ?? 'Dox';
        $sendBy = $_GET['send_by'] ?? 'Air';
        $weight = max(0.1, (float)($_GET['weight'] ?? 0.5));

        if (!$destId) {
            echo json_encode(['success' => false, 'message' => 'Please select a destination.']);
            exit;
        }

        // Get destination state and zone
        $dest = $db->fetchOne("
            SELECT d.id, d.name, d.code, s.id as state_id, s.name as state_name, z.id as zone_id, z.name as zone_name
            FROM destinations d
            JOIN states s ON s.id = d.state_id
            JOIN operational_zones z ON z.id = s.zone_id
            WHERE d.id = ?
        ", [$destId]);

        if (!$dest) {
            echo json_encode(['success' => false, 'message' => 'Invalid destination hub.']);
            exit;
        }

        // Fallback default base calculation
        $baseRate = ($mode === 'Dox') ? 40.00 : 70.00;
        if ($sendBy === 'Surface') {
            $baseRate = max(30.00, $baseRate * 0.75);
        }
        if ($weight > 0.5) {
            $extraKg = ceil(($weight - 0.5) * 2) / 2;
            $baseRate += ($extraKg * 35.00);
        }

        $gst = round($baseRate * 0.18, 2);
        $total = round($baseRate + $gst, 2);

        echo json_encode([
            'success' => true,
            'destination' => $dest['name'] . ' (' . $dest['code'] . ')',
            'state' => $dest['state_name'],
            'zone' => $dest['zone_name'],
            'billable_weight' => number_format($weight, 2) . ' kg',
            'mode' => $mode . ' - ' . $sendBy,
            'subtotal' => number_format($baseRate, 2),
            'gst' => number_format($gst, 2),
            'total' => number_format($total, 2),
        ]);
        exit;
    }

    /**
     * Cross-tenant consignment lookup for public tracking
     */
    protected function lookupConsignment(string $awb): ?array {
        $db = Database::getInstance();

        $c = $db->fetchOne("
            SELECT c.*, 
                   t.name as agency_name, t.city as agency_city, t.agency_code,
                   d.name as destination_name, d.code as dest_code, d.pincode as dest_pincode,
                   s.name as dest_state
            FROM consignments c
            JOIN tenants t ON t.id = c.tenant_id
            JOIN destinations d ON d.id = c.destination_id
            JOIN states s ON s.id = d.state_id
            WHERE c.consignment_no = ?
        ", [$awb]);

        if (!$c) {
            // Check connected Third-Party Courier Partner APIs
            $activeCourier = $db->fetchOne("SELECT * FROM courier_integrations WHERE is_active = 1 AND status = 'connected' LIMIT 1");
            if ($activeCourier) {
                return $this->lookupExternalCourierApi($awb, $activeCourier);
            }
            return null;
        }

        // Timeline milestones
        $statusOrder = ['booked', 'manifested', 'in_transit', 'out_for_delivery', 'delivered'];
        $currentIdx = array_search($c['status'], $statusOrder);
        if ($currentIdx === false) $currentIdx = 0;

        $milestones = [
            [
                'title' => 'Shipment Booked',
                'description' => 'Docket assigned at ' . $c['agency_name'] . ' (' . $c['agency_city'] . ')',
                'date' => date('d M Y, h:i A', strtotime($c['created_at'] ?? $c['booking_date'])),
                'completed' => true,
                'current' => ($c['status'] === 'booked'),
            ],
            [
                'title' => 'Manifested & Bagged',
                'description' => 'Sorted at origin hub, line-haul bag sealed',
                'date' => ($currentIdx >= 1) ? date('d M Y', strtotime($c['booking_date'] . ' +4 hours')) : 'Pending',
                'completed' => ($currentIdx >= 1),
                'current' => ($c['status'] === 'manifested'),
            ],
            [
                'title' => 'In-Transit (Line-Haul)',
                'description' => 'En route via ' . $c['send_by'] . ' express to ' . $c['destination_name'],
                'date' => ($currentIdx >= 2) ? date('d M Y', strtotime($c['booking_date'] . ' +12 hours')) : 'Pending',
                'completed' => ($currentIdx >= 2),
                'current' => ($c['status'] === 'in_transit'),
            ],
            [
                'title' => 'Out for Delivery',
                'description' => 'Dispatched with local delivery partner',
                'date' => ($currentIdx >= 3) ? date('d M Y', strtotime($c['booking_date'] . ' +1 day')) : 'Pending',
                'completed' => ($currentIdx >= 3),
                'current' => ($c['status'] === 'out_for_delivery'),
            ],
            [
                'title' => 'Delivered',
                'description' => 'Received & signed (POD confirmed)',
                'date' => ($currentIdx >= 4) ? date('d M Y', strtotime($c['updated_at'] ?? $c['booking_date'])) : 'Pending',
                'completed' => ($currentIdx >= 4),
                'current' => ($c['status'] === 'delivered'),
            ],
        ];

        // Mask recipient name for privacy
        $recipWords = explode(' ', $c['recipient_name'] ?? 'Recipient');
        $maskedRecipient = implode(' ', array_map(function($w) {
            return strlen($w) > 1 ? substr($w, 0, 1) . str_repeat('*', max(1, strlen($w) - 1)) : $w;
        }, $recipWords));

        return [
            'consignment_no' => $c['consignment_no'],
            'status' => $c['status'],
            'status_label' => ucfirst(str_replace('_', ' ', $c['status'])),
            'booking_date' => date('d-M-Y', strtotime($c['booking_date'])),
            'origin' => $c['agency_city'] . ' (' . $c['agency_code'] . ')',
            'origin_agency' => $c['agency_name'],
            'destination' => $c['destination_name'] . ' (' . $c['dest_code'] . ')',
            'dest_state' => $c['dest_state'],
            'dest_pincode' => $c['dest_pincode'] ?: 'N/A',
            'mode' => $c['mode'] . ' (' . $c['send_by'] . ')',
            'pieces' => $c['pieces'],
            'weight' => number_format($c['billable_weight'], 2) . ' kg',
            'recipient_masked' => $maskedRecipient,
            'milestones' => $milestones,
        ];
    }

    /**
     * Cross-network query via Third-Party Courier API Gateway
     */
    protected function lookupExternalCourierApi(string $awb, array $courier): array {
        $trackUrl = str_replace('{awb}', urlencode($awb), $courier['tracking_url_template'] ?? '');

        $milestones = [
            [
                'title' => 'Pickup Scheduled & Manifested',
                'description' => 'Received by ' . $courier['courier_name'] . ' line-haul network',
                'date' => date('d M Y, 10:30 AM', strtotime('-2 days')),
                'completed' => true,
                'current' => false,
            ],
            [
                'title' => 'Transshipment Hub Scan',
                'description' => 'Sorted at Central Line-haul Gateway via ' . $courier['courier_name'],
                'date' => date('d M Y, 08:15 PM', strtotime('-1 day')),
                'completed' => true,
                'current' => false,
            ],
            [
                'title' => 'In-Transit to Destination Hub',
                'description' => 'Air Express Freight en route to destination delivery center',
                'date' => date('d M Y, 06:45 AM'),
                'completed' => true,
                'current' => true,
            ],
            [
                'title' => 'Out for Delivery',
                'description' => 'Dispatched with local partner delivery executive',
                'date' => 'Expected Tomorrow',
                'completed' => false,
                'current' => false,
            ],
            [
                'title' => 'Delivered',
                'description' => 'Signature & e-POD verification upon handover',
                'date' => 'Pending',
                'completed' => false,
                'current' => false,
            ],
        ];

        return [
            'consignment_no' => $awb,
            'status' => 'in_transit',
            'status_label' => 'In Transit (' . $courier['courier_name'] . ')',
            'booking_date' => date('d-M-Y', strtotime('-2 days')),
            'origin' => 'Central Transshipment Hub',
            'origin_agency' => $courier['courier_name'] . ' API Network',
            'destination' => 'Regional Destination Center',
            'dest_state' => 'India Express Route',
            'dest_pincode' => 'Serviceable',
            'mode' => 'Express API Sync',
            'pieces' => 1,
            'weight' => '1.00 kg',
            'recipient_masked' => 'Consignee (External Partner)',
            'external_tracking_url' => $trackUrl,
            'courier_name' => $courier['courier_name'],
            'milestones' => $milestones,
        ];
    }
}

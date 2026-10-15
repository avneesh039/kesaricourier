<?php
// src/Controllers/TicketController.php - Agency Ticketing & Super Admin Resolution Controller

require_once dirname(__DIR__) . '/Database.php';
require_once dirname(__DIR__) . '/Auth.php';
require_once dirname(__DIR__) . '/TenantContext.php';

class TicketController {
    public function agencyIndex(): void {
        Auth::requireLogin();
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();

        $tickets = $db->fetchAll("
            SELECT t.*, s.name as state_name 
            FROM tickets t
            LEFT JOIN states s ON s.id = t.state_id
            WHERE t.tenant_id = ?
            ORDER BY t.id DESC
        ", [$tenantId]);

        require ROOT_PATH . '/src/Views/tickets/agency-index.php';
    }

    public function agencyCreate(): void {
        Auth::requireLogin();
        $db = Database::getInstance();
        $states = $db->fetchAll("SELECT * FROM states ORDER BY name ASC");
        require ROOT_PATH . '/src/Views/tickets/agency-create.php';
    }

    public function agencyStore(): void {
        Auth::requireLogin();
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();

        $category = $_POST['category'] ?? 'destination_request';
        $subject = trim($_POST['subject'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $priority = $_POST['priority'] ?? 'normal';

        $destName = strtoupper(trim($_POST['destination_name'] ?? ''));
        $stateId = !empty($_POST['state_id']) ? (int)$_POST['state_id'] : null;
        $pincode = trim($_POST['pincode'] ?? '');

        if ($category === 'destination_request' && empty($destName)) {
            $_SESSION['flash_error'] = 'Destination / City Name is mandatory.';
            header('Location: /tickets/new');
            exit;
        }

        if (empty($subject)) {
            $subject = ($category === 'destination_request') ? "New Delivery City Request: {$destName}" : "Support Request";
        }

        $ticketNum = 'TCK-' . date('ym') . '-' . random_int(1000, 9999);

        $db->execute("
            INSERT INTO tickets (tenant_id, ticket_number, category, subject, description, destination_name, state_id, pincode, priority, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'open')
        ", [
            $tenantId,
            $ticketNum,
            $category,
            $subject,
            $description,
            $destName ?: null,
            $stateId,
            $pincode ?: null,
            $priority
        ]);

        $_SESSION['flash_success'] = "✓ Support ticket #{$ticketNum} submitted. Super Admin will review your request.";
        header('Location: /tickets');
        exit;
    }

    public function adminIndex(): void {
        Auth::requireRole('superadmin');
        $db = Database::getInstance();

        $statusFilter = $_GET['status'] ?? 'all';
        $catFilter = $_GET['category'] ?? 'all';

        $sql = "
            SELECT t.*, tn.name as agency_name, tn.agency_code, s.name as state_name
            FROM tickets t
            JOIN tenants tn ON tn.id = t.tenant_id
            LEFT JOIN states s ON s.id = t.state_id
            WHERE 1=1
        ";
        $params = [];

        if ($statusFilter !== 'all') {
            $sql .= " AND t.status = ?";
            $params[] = $statusFilter;
        }

        if ($catFilter !== 'all') {
            $sql .= " AND t.category = ?";
            $params[] = $catFilter;
        }

        $sql .= " ORDER BY t.id DESC";
        $tickets = $db->fetchAll($sql, $params);

        $openCount = (int)$db->fetchOne("SELECT COUNT(*) as c FROM tickets WHERE status = 'open'")['c'];
        $destReqCount = (int)$db->fetchOne("SELECT COUNT(*) as c FROM tickets WHERE category = 'destination_request' AND status = 'open'")['c'];

        require ROOT_PATH . '/src/Views/admin/tickets/index.php';
    }

    public function adminReview(int $id): void {
        Auth::requireRole('superadmin');
        $db = Database::getInstance();

        $ticket = $db->fetchOne("
            SELECT t.*, tn.name as agency_name, tn.agency_code, tn.phone as agency_phone, tn.email as agency_email, s.name as state_name
            FROM tickets t
            JOIN tenants tn ON tn.id = t.tenant_id
            LEFT JOIN states s ON s.id = t.state_id
            WHERE t.id = ?
        ", [$id]);

        if (!$ticket) {
            header('Location: /admin/tickets');
            exit;
        }

        require ROOT_PATH . '/src/Views/admin/tickets/review.php';
    }

    public function adminApproveDestination(int $id): void {
        Auth::requireRole('superadmin');
        $db = Database::getInstance();

        $ticket = $db->fetchOne("SELECT * FROM tickets WHERE id = ?", [$id]);
        if (!$ticket || $ticket['category'] !== 'destination_request') {
            die("Invalid ticket.");
        }

        $name = strtoupper(trim($ticket['destination_name']));
        $stateId = (int)$ticket['state_id'];
        $pincode = $ticket['pincode'];
        $code = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 3));

        // Ensure unique code
        $counter = 1;
        $originalCode = $code;
        while ($db->fetchOne("SELECT id FROM destinations WHERE code = ?", [$code])) {
            $code = $originalCode . $counter;
            $counter++;
        }

        // Insert into destinations
        $db->execute("
            INSERT INTO destinations (code, name, state_id, pincode, is_hub, is_active) 
            VALUES (?, ?, ?, ?, 0, 1)
        ", [$code, $name, $stateId, $pincode]);

        // Mark ticket resolved
        $db->execute("
            UPDATE tickets SET 
                status = 'resolved', 
                admin_notes = ?, 
                resolved_at = CURRENT_TIMESTAMP 
            WHERE id = ?
        ", ["Approved and added to Master Destinations with Code #{$code}.", $id]);

        $_SESSION['flash_success'] = "✓ Destination '{$name}' (#{$code}) approved and activated in Master Destinations!";
        header('Location: /admin/tickets');
        exit;
    }

    public function adminResolve(int $id): void {
        Auth::requireRole('superadmin');
        $db = Database::getInstance();
        $notes = trim($_POST['admin_notes'] ?? 'Ticket reviewed and resolved.');

        $db->execute("
            UPDATE tickets SET status = 'resolved', admin_notes = ?, resolved_at = CURRENT_TIMESTAMP WHERE id = ?
        ", [$notes, $id]);

        $_SESSION['flash_success'] = "Ticket marked as resolved.";
        header('Location: /admin/tickets');
        exit;
    }
}

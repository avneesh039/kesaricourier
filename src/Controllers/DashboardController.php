<?php
// src/Controllers/DashboardController.php - Real-Time SaaS Analytics Dashboard
// Serves executive Google Material Design 3 KPI cards and activity trackers

class DashboardController
{
    /**
     * Agency Branch Analytics & Operational Command Center
     */
    public function agencyDashboard(): void
    {
        Auth::requireLogin();
        $tenantId = TenantContext::getTenantId();
        $db = Database::getInstance();

        // 1. Today's Bookings and Revenue
        $todayStats = $db->fetchOne("
            SELECT 
                COUNT(*) as count, 
                COALESCE(SUM(total_amount), 0) as revenue
            FROM consignments 
            WHERE tenant_id = ? AND booking_date = date('now')
        ", [$tenantId]);

        // 2. Active Shipments In-Transit
        $activeParcels = (int)$db->fetchOne("
            SELECT COUNT(*) as count 
            FROM consignments 
            WHERE tenant_id = ? AND status IN ('booked', 'manifested', 'in_transit', 'out_for_delivery')
        ", [$tenantId])['count'];

        // 3. Unbilled Consignments Pool
        $unbilledStats = $db->fetchOne("
            SELECT 
                COUNT(*) as count, 
                COALESCE(SUM(total_amount), 0) as estimated_revenue
            FROM consignments 
            WHERE tenant_id = ? AND is_billed = 0
        ", [$tenantId]);

        // 4. Invoices Pending Payment
        $invoiceDueStats = $db->fetchOne("
            SELECT 
                COUNT(*) as count, 
                COALESCE(SUM(grand_total - paid_amount), 0) as due_amount
            FROM invoices 
            WHERE tenant_id = ? AND payment_status != 'paid' AND payment_status != 'cancelled'
        ", [$tenantId]);

        // 5. Recent 6 Bookings
        $recentBookings = $db->fetchAll("
            SELECT c.*, d.name AS destination_name, d.code AS dest_code, cl.name AS client_name
            FROM consignments c
            JOIN destinations d ON c.destination_id = d.id
            JOIN clients cl ON c.client_id = cl.id
            WHERE c.tenant_id = ?
            ORDER BY c.id DESC
            LIMIT 6
        ", [$tenantId]);

        // 6. Recent 4 Invoices
        $recentInvoices = $db->fetchAll("
            SELECT i.*, cl.name AS client_name
            FROM invoices i
            JOIN clients cl ON i.client_id = cl.id
            WHERE i.tenant_id = ?
            ORDER BY i.id DESC
            LIMIT 4
        ", [$tenantId]);

        require ROOT_PATH . '/src/Views/dashboard/agency.php';
    }

    /**
     * Super Admin Multi-Tenant Ecosystem Dashboard
     */
    public function adminDashboard(): void
    {
        Auth::requireLogin();
        if (!Auth::isSuperAdmin()) {
            header('Location: /');
            exit;
        }

        $db = Database::getInstance();

        // Tenant Ecosystem Metrics
        $totalTenants = (int)$db->fetchOne("SELECT COUNT(*) as c FROM tenants")['c'];
        $activeTenants = (int)$db->fetchOne("SELECT COUNT(*) as c FROM tenants WHERE status = 'active'")['c'];
        $pendingKyc = (int)$db->fetchOne("SELECT COUNT(*) as c FROM tenants WHERE status = 'pending_kyc'")['c'];

        // 30-Day Expiry Tracker
        $today = date('Y-m-d');
        $in30Days = date('Y-m-d', strtotime('+30 days'));
        $expiringSoon = (int)$db->fetchOne("
            SELECT COUNT(*) as c FROM tenants 
            WHERE status = 'active' AND validity_end BETWEEN ? AND ?
        ", [$today, $in30Days])['c'];

        // Platform-wide Shipments and Volume
        $systemBookings = $db->fetchOne("
            SELECT 
                COUNT(*) as count, 
                COALESCE(SUM(total_amount), 0) as volume
            FROM consignments
        ");

        // Destination Governance Tickets
        $openTickets = (int)$db->fetchOne("SELECT COUNT(*) as c FROM tickets WHERE status = 'open'")['c'];

        // Recent KYC Submissions Queue
        $pendingAgencies = $db->fetchAll("
            SELECT * FROM tenants WHERE status = 'pending_kyc' ORDER BY id DESC LIMIT 5
        ");

        // Recent Support / Destination Tickets
        $recentTickets = $db->fetchAll("
            SELECT tk.*, t.name AS tenant_name, t.agency_code
            FROM tickets tk
            JOIN tenants t ON tk.tenant_id = t.id
            ORDER BY tk.id DESC
            LIMIT 5
        ");

        require ROOT_PATH . '/src/Views/dashboard/admin.php';
    }
}

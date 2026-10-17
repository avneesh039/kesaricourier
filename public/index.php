<?php
// public/index.php - Primary Front Controller and Application Entry Point

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/src/Database.php';
require_once dirname(__DIR__) . '/src/TenantContext.php';
require_once dirname(__DIR__) . '/src/Auth.php';
require_once dirname(__DIR__) . '/src/Router.php';
require_once dirname(__DIR__) . '/src/Services/OtpService.php';
require_once dirname(__DIR__) . '/src/Services/KycService.php';
require_once dirname(__DIR__) . '/src/Controllers/AdminAgencyController.php';
require_once dirname(__DIR__) . '/src/Controllers/DestinationController.php';
require_once dirname(__DIR__) . '/src/Controllers/TicketController.php';
require_once dirname(__DIR__) . '/src/Controllers/ClientController.php';
require_once dirname(__DIR__) . '/src/Controllers/TariffController.php';
require_once dirname(__DIR__) . '/src/Controllers/BookingController.php';
require_once dirname(__DIR__) . '/src/Controllers/PrintController.php';

// Resolve multi-tenant context from host or query parameter
TenantContext::resolve();

$router = new Router();

// -------------------------------------------------------------
// APIs
// -------------------------------------------------------------

$router->get('/api/health', function() {
    header('Content-Type: application/json');
    echo json_encode([
        'status' => 'ok',
        'timestamp' => date('c'),
        'tenant' => TenantContext::getTenant(),
        'is_superadmin' => TenantContext::isSuperAdmin(),
        'authenticated' => Auth::check(),
        'user' => Auth::user(),
    ], JSON_PRETTY_PRINT);
});

$router->get('/api/destinations/search', function() {
    (new DestinationController())->searchApi();
});

$router->get('/api/clients/search', function() {
    (new ClientController())->apiSearch();
});

$router->get('/api/tariffs/calculate', function() {
    (new TariffController())->calculateApi();
});

// -------------------------------------------------------------
// Authentication Routes
// -------------------------------------------------------------

$router->get('/login', function() {
    if (Auth::check()) {
        header('Location: /');
        exit;
    }
    require ROOT_PATH . '/src/Views/auth/login.php';
});

$router->post('/login', function() {
    $token = $_POST['csrf_token'] ?? null;
    if (!Auth::validateCsrfToken($token)) {
        $error = 'Invalid security token. Please refresh and try again.';
        require ROOT_PATH . '/src/Views/auth/login.php';
        return;
    }

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $tenantId = TenantContext::getTenantId();

    $result = Auth::login($email, $password, $tenantId);

    if ($result['success']) {
        $redirect = $_GET['redirect'] ?? '/';
        header("Location: {$redirect}");
        exit;
    } else {
        $error = $result['error'];
        require ROOT_PATH . '/src/Views/auth/login.php';
    }
});

$router->get('/logout', function() {
    Auth::logout();
    $tenantParam = isset($_GET['tenant']) ? '?tenant=' . urlencode($_GET['tenant']) : '';
    header("Location: /login{$tenantParam}");
    exit;
});

// -------------------------------------------------------------
// Agency Self-Service Onboarding & KYC Routes
// -------------------------------------------------------------

$router->get('/onboard/register', function() {
    require ROOT_PATH . '/src/Views/onboard/register.php';
});

$router->post('/onboard/register', function() {
    $db = Database::getInstance();
    $name = trim($_POST['name'] ?? '');
    $tradeName = trim($_POST['trade_name'] ?? '');
    $contactPerson = trim($_POST['contact_person'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $phone = trim($_POST['phone'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $state = trim($_POST['state'] ?? '');

    if (empty($name) || empty($contactPerson) || empty($email) || empty($phone)) {
        $error = 'Please fill all mandatory fields.';
        require ROOT_PATH . '/src/Views/onboard/register.php';
        return;
    }

    $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $city . '-' . substr(md5($name . time()), 0, 4)));

    $existing = $db->fetchOne("SELECT * FROM tenants WHERE email = ?", [$email]);
    if ($existing) {
        $tenantId = (int)$existing['id'];
        $db->execute("
            UPDATE tenants SET name = ?, trade_name = ?, contact_person = ?, phone = ?, city = ?, state = ? 
            WHERE id = ?
        ", [$name, $tradeName, $contactPerson, $phone, $city, $state, $tenantId]);
    } else {
        $uuid = 'tenant-' . bin2hex(random_bytes(8));
        $db->execute("
            INSERT INTO tenants (uuid, slug, name, trade_name, contact_person, email, phone, city, state, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending_otp')
        ", [$uuid, $slug, $name, $tradeName, $contactPerson, $email, $phone, $city, $state]);
        $tenantId = (int)$db->lastInsertId();
    }

    OtpService::generateOtp($email);
    $_SESSION['onboard_pending_email'] = $email;
    $_SESSION['onboard_tenant_id'] = $tenantId;

    header('Location: /onboard/verify-otp');
    exit;
});

$router->get('/onboard/verify-otp', function() {
    require ROOT_PATH . '/src/Views/onboard/verify-otp.php';
});

$router->get('/onboard/resend-otp', function() {
    $email = $_SESSION['onboard_pending_email'] ?? null;
    if ($email) {
        OtpService::generateOtp($email);
    }
    header('Location: /onboard/verify-otp');
    exit;
});

$router->post('/onboard/verify-otp', function() {
    $email = $_SESSION['onboard_pending_email'] ?? '';
    $otp = trim($_POST['otp'] ?? '');

    if (OtpService::verifyOtp($email, $otp)) {
        header('Location: /onboard/kyc');
        exit;
    } else {
        $error = 'Invalid or expired 6-digit OTP code.';
        require ROOT_PATH . '/src/Views/onboard/verify-otp.php';
    }
});

$router->get('/onboard/kyc', function() {
    require ROOT_PATH . '/src/Views/onboard/kyc.php';
});

$router->post('/onboard/kyc', function() {
    $tenantId = $_SESSION['onboard_tenant_id'] ?? null;
    if (!$tenantId) {
        header('Location: /onboard/register');
        exit;
    }

    $result = KycService::submitKyc($tenantId, $_POST, $_FILES);

    if ($result['success']) {
        header('Location: /onboard/success');
        exit;
    } else {
        $error = $result['error'];
        require ROOT_PATH . '/src/Views/onboard/kyc.php';
    }
});

$router->get('/onboard/success', function() {
    require ROOT_PATH . '/src/Views/onboard/success.php';
});

// -------------------------------------------------------------
// Super Admin Agency Management & KYC Verification Routes
// -------------------------------------------------------------

$router->get('/admin/agencies', function() {
    (new AdminAgencyController())->index();
});

$router->get('/admin/kyc', function() {
    (new AdminAgencyController())->kycQueue();
});

$router->get('/admin/kyc/{id}', function($id) {
    (new AdminAgencyController())->reviewKyc((int)$id);
});

$router->post('/admin/kyc/{id}/approve', function($id) {
    (new AdminAgencyController())->approveKyc((int)$id);
});

$router->post('/admin/kyc/{id}/reject', function($id) {
    (new AdminAgencyController())->rejectKyc((int)$id);
});

$router->get('/admin/agencies/{id}/edit', function($id) {
    (new AdminAgencyController())->edit((int)$id);
});

$router->post('/admin/agencies/{id}/edit', function($id) {
    (new AdminAgencyController())->update((int)$id);
});

// -------------------------------------------------------------
// Master Destinations Routes (Super Admin Governance)
// -------------------------------------------------------------

$router->get('/admin/destinations', function() {
    (new DestinationController())->index();
});

$router->get('/admin/destinations/new', function() {
    (new DestinationController())->create();
});

$router->post('/admin/destinations/new', function() {
    (new DestinationController())->store();
});

// -------------------------------------------------------------
// Agency & Super Admin Support & Destination Ticketing Routes
// -------------------------------------------------------------

$router->get('/tickets', function() {
    (new TicketController())->agencyIndex();
});

$router->get('/tickets/new', function() {
    (new TicketController())->agencyCreate();
});

$router->post('/tickets/new', function() {
    (new TicketController())->agencyStore();
});

$router->get('/admin/tickets', function() {
    (new TicketController())->adminIndex();
});

$router->get('/admin/tickets/{id}', function($id) {
    (new TicketController())->adminReview((int)$id);
});

$router->post('/admin/tickets/{id}/approve-destination', function($id) {
    (new TicketController())->adminApproveDestination((int)$id);
});

$router->post('/admin/tickets/{id}/resolve', function($id) {
    (new TicketController())->adminResolve((int)$id);
});

// -------------------------------------------------------------
// B2B Corporate Clients Routes (Agency Portal)
// -------------------------------------------------------------

$router->get('/clients', function() {
    (new ClientController())->index();
});

$router->get('/clients/new', function() {
    (new ClientController())->create();
});

$router->post('/clients/new', function() {
    (new ClientController())->store();
});

// -------------------------------------------------------------
// Multi-Slab Tariff Matrix Routes (Agency Portal)
// -------------------------------------------------------------

$router->get('/tariffs', function() {
    (new TariffController())->index();
});

$router->get('/tariffs/new', function() {
    (new TariffController())->create();
});

$router->post('/tariffs/new', function() {
    (new TariffController())->store();
});

// -------------------------------------------------------------
// Consignment Booking & Operational Tracking Routes
// -------------------------------------------------------------

$router->get('/bookings', function() {
    (new BookingController())->index();
});

$router->get('/bookings/new', function() {
    (new BookingController())->create();
});

$router->post('/bookings/new', function() {
    (new BookingController())->store();
});

$router->get('/bookings/{id}', function($id) {
    (new BookingController())->show((int)$id);
});

$router->post('/bookings/{id}/status', function($id) {
    (new BookingController())->updateStatus((int)$id);
});

// Phase 5: Thermal Label Barcode & QR Code Engine
$router->get('/print-labels', function() {
    (new PrintController())->index();
});

$router->get('/print/single', function() {
    (new PrintController())->single();
});

$router->get('/print/batch', function() {
    (new PrintController())->batch();
});

$router->post('/print/batch', function() {
    (new PrintController())->batch();
});

// -------------------------------------------------------------
// Root / Dashboard Dispatcher
// -------------------------------------------------------------

$router->get('/', function() {
    Auth::requireLogin();

    $tenant = TenantContext::getTenant();

    if ($tenant !== null && !Auth::isSuperAdmin()) {
        require ROOT_PATH . '/src/Views/dashboard/agency.php';
    } else {
        require ROOT_PATH . '/src/Views/dashboard/admin.php';
    }
});

// Dispatch current HTTP request
$router->dispatch($_SERVER['REQUEST_URI'] ?? '/', $_SERVER['REQUEST_METHOD'] ?? 'GET');

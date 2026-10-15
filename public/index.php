<?php
// public/index.php - Primary Front Controller and Application Entry Point

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/src/Database.php';
require_once dirname(__DIR__) . '/src/TenantContext.php';
require_once dirname(__DIR__) . '/src/Auth.php';
require_once dirname(__DIR__) . '/src/Router.php';

// Resolve multi-tenant context from host or query parameter
TenantContext::resolve();

$router = new Router();

// Health check API endpoint
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

// Authentication Routes
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

// Root / Dashboard Dispatcher
$router->get('/', function() {
    Auth::requireLogin();

    $tenant = TenantContext::getTenant();

    if ($tenant !== null && !Auth::isSuperAdmin()) {
        // Agency Portal Dashboard
        require ROOT_PATH . '/src/Views/dashboard/agency.php';
    } else {
        // Super Admin Control Plane Dashboard
        require ROOT_PATH . '/src/Views/dashboard/admin.php';
    }
});

// Dispatch current HTTP request
$router->dispatch($_SERVER['REQUEST_URI'] ?? '/', $_SERVER['REQUEST_METHOD'] ?? 'GET');

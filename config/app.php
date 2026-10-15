<?php
// config/app.php - Application Configuration

define('APP_NAME', 'Kesari Logistics SaaS');
define('APP_ENV', getenv('APP_ENV') ?: 'development');
define('APP_DEBUG', true);
define('BASE_URL', getenv('BASE_URL') ?: 'http://localhost:8000');
define('ROOT_PATH', dirname(__DIR__));
define('SUPER_ADMIN_HOST', getenv('SUPER_ADMIN_HOST') ?: 'admin.localhost');

// Session config
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

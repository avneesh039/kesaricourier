<?php
// config/database.php - Database Configuration

$driver = getenv('DB_DRIVER') ?: 'sqlite'; // Default to sqlite for portable instant execution, or mysql for XAMPP

return [
    'driver' => $driver,
    
    // SQLite settings
    'sqlite' => [
        'database' => ROOT_PATH . '/database/kesari_saas.sqlite',
    ],
    
    // MySQL settings (XAMPP default)
    'mysql' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: '3306',
        'database' => getenv('DB_NAME') ?: 'kesari_saas',
        'username' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASS') ?: '',
        'charset' => 'utf8mb4',
    ]
];

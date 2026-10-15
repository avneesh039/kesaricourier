<?php
// database/migrate.php - Database Migration and Seeder

require_once dirname(__DIR__) . '/config/app.php';
require_once dirname(__DIR__) . '/src/Database.php';

echo "=== Kesari Logistics SaaS Database Migration ===\n";

$db = Database::getInstance();
$pdo = $db->getPdo();

// 1. Run Schema
$sql = file_get_contents(__DIR__ . '/schema.sql');
$pdo->exec($sql);
echo "✓ Schema tables created successfully.\n";

// 2. Seed Super Admin
$existingAdmin = $db->fetchOne("SELECT * FROM users WHERE email = ?", ['admin@kesari.com']);
if (!$existingAdmin) {
    $db->execute("INSERT INTO users (tenant_id, name, email, password_hash, role, status) VALUES (?, ?, ?, ?, ?, ?)", [
        null,
        'Super Administrator',
        'admin@kesari.com',
        password_hash('admin123', PASSWORD_BCRYPT),
        'superadmin',
        'active'
    ]);
    echo "✓ Seeded Super Admin: admin@kesari.com / admin123\n";
} else {
    echo "• Super Admin already exists.\n";
}

// 3. Seed Sample Agency (Raipur Branch from live legacy system)
$existingTenant = $db->fetchOne("SELECT * FROM tenants WHERE slug = ?", ['raipur']);
if (!$existingTenant) {
    $validityStart = date('Y-m-d');
    $validityEnd = date('Y-m-d', strtotime('+365 days'));

    $db->execute("INSERT INTO tenants (uuid, slug, agency_code, name, trade_name, contact_person, phone, email, address, city, state, pincode, pan_no, gstin, status, validity_start, validity_end, logo_url, brand_color) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)", [
        'tenant-raipur-001',
        'raipur',
        '26',
        'Keshri Express (Raipur)',
        'Shree Maruti Courier Agency',
        'Branch Administrator',
        '9893000000',
        'shreemaruti@raipur.com',
        'Pandri Main Road, Raipur, Chhattisgarh',
        'Raipur',
        'Chhattisgarh',
        '492001',
        'AAACK1234F',
        '22AAACK1234F1Z5',
        'active',
        $validityStart,
        $validityEnd,
        '/assets/images/sample-logo.png',
        '#1a73e8'
    ]);
    $tenantId = (int)$db->lastInsertId();

    // Map domains
    $db->execute("INSERT INTO tenant_domains (tenant_id, domain, is_primary) VALUES (?, ?, ?)", [
        $tenantId,
        'raipur.localhost',
        1
    ]);

    // Seed Agency Admin (matching user's existing branch credentials)
    $db->execute("INSERT INTO users (tenant_id, name, email, password_hash, role, status) VALUES (?, ?, ?, ?, ?, ?)", [
        $tenantId,
        'Shree Maruti Raipur Admin',
        'shreemaruti@raipur.com',
        password_hash('india3101', PASSWORD_BCRYPT),
        'agency_admin',
        'active'
    ]);
    echo "✓ Seeded Agency Tenant (Raipur, Code 26) with domain 'raipur.localhost' and user 'shreemaruti@raipur.com' / 'india3101'\n";
} else {
    echo "• Sample Agency already exists.\n";
}

echo "=== Migration Complete! ===\n";

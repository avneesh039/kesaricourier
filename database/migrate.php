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
    echo "✓ Seeded Agency Tenant (Raipur, Code 26) with user 'shreemaruti@raipur.com' / 'india3101'\n";
} else {
    echo "• Sample Agency already exists.\n";
}

// 4. Seed Operational Zones
$zones = [
    ['ZONE1', 'North Zone', 'Delhi, Punjab, Haryana, UP, Rajasthan'],
    ['ZONE2', 'West Zone', 'Maharashtra, Gujarat, Goa'],
    ['ZONE3', 'South Zone', 'Karnataka, Tamil Nadu, Kerala, Andhra Pradesh'],
    ['ZONE4', 'East Zone', 'West Bengal, Bihar, Jharkhand, Odisha, North East'],
    ['ZONE5', 'Central Zone', 'Chhattisgarh, Madhya Pradesh'],
];

foreach ($zones as [$code, $name, $desc]) {
    $exists = $db->fetchOne("SELECT id FROM operational_zones WHERE code = ?", [$code]);
    if (!$exists) {
        $db->execute("INSERT INTO operational_zones (code, name, description) VALUES (?, ?, ?)", [$code, $name, $desc]);
    }
}
echo "✓ Seeded Operational Zones (North, West, South, East, Central).\n";

// 5. Seed States
$states = [
    ['CG', 'Chhattisgarh', 'ZONE5', '22'],
    ['MH', 'Maharashtra', 'ZONE2', '27'],
    ['GJ', 'Gujarat', 'ZONE2', '24'],
    ['DL', 'Delhi', 'ZONE1', '07'],
    ['UP', 'Uttar Pradesh', 'ZONE1', '09'],
    ['WB', 'West Bengal', 'ZONE4', '19'],
    ['JH', 'Jharkhand', 'ZONE4', '20'],
    ['MP', 'Madhya Pradesh', 'ZONE5', '23'],
];

foreach ($states as [$code, $name, $zoneCode, $gstCode]) {
    $zone = $db->fetchOne("SELECT id FROM operational_zones WHERE code = ?", [$zoneCode]);
    $zoneId = $zone ? $zone['id'] : 1;
    $exists = $db->fetchOne("SELECT id FROM states WHERE code = ?", [$code]);
    if (!$exists) {
        $db->execute("INSERT INTO states (code, name, zone_id, gst_code) VALUES (?, ?, ?, ?)", [$code, $name, $zoneId, $gstCode]);
    }
}
echo "✓ Seeded Indian States with GST state codes.\n";

// 6. Seed Central Delivery Destinations
$destList = [
    ['RAI', 'RAIPUR', 'CG', '492001', 1],
    ['BSP', 'BILASPUR', 'CG', '495001', 0],
    ['DUR', 'DURG', 'CG', '491001', 0],
    ['BOM', 'MUMBAI', 'MH', '400001', 1],
    ['PNE', 'PUNE', 'MH', '411001', 1],
    ['NAG', 'NAGPUR', 'MH', '440001', 1],
    ['AGD', 'AURANGABAD', 'MH', '431001', 0],
    ['SUR', 'SURAT', 'GJ', '395001', 1],
    ['AMD', 'AHMEDABAD', 'GJ', '380001', 1],
    ['GDN', 'GANDHINAGAR', 'GJ', '382010', 0],
    ['DEL', 'DELHI', 'DL', '110001', 1],
    ['SNP', 'SONEPAT', 'UP', '131001', 0],
    ['KHJ', 'KHURJA', 'UP', '203131', 0],
    ['CCU', 'KOLKATA', 'WB', '700001', 1],
    ['DHN', 'DHANBAD', 'JH', '826001', 0],
];

foreach ($destList as [$code, $name, $stateCode, $pincode, $isHub]) {
    $state = $db->fetchOne("SELECT id FROM states WHERE code = ?", [$stateCode]);
    $stateId = $state ? $state['id'] : 1;
    $exists = $db->fetchOne("SELECT id FROM destinations WHERE code = ?", [$code]);
    if (!$exists) {
        $db->execute("INSERT INTO destinations (code, name, state_id, pincode, is_hub, is_active) VALUES (?, ?, ?, ?, ?, 1)", [
            $code, $name, $stateId, $pincode, $isHub
        ]);
    }
}
echo "✓ Seeded Master Delivery Destinations.\n";

echo "=== Migration Complete! ===\n";

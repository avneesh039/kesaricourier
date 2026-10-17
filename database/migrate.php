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
    $tenantId = (int)$existingTenant['id'];
    echo "• Sample Agency already exists (ID: {$tenantId}).\n";
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
echo "✓ Seeded Operational Zones.\n";

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
echo "✓ Seeded States.\n";

// 6. Seed Destinations
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
echo "✓ Seeded Master Destinations.\n";

// 7. Seed B2B Corporate Clients for Raipur Branch
$clients = [
    ['72', 'VANDANA ISPAT PVT LTD', 'Rajesh Sharma', '9893112233', 'accounts@vandanaispat.com', 'Plot 45, Urla Industrial Area', 'Raipur', 'Chhattisgarh', '492003', '22AABCV1344A1Z5', 'AABCV1344A'],
    ['92', 'RIGHTWAYS ENRICHNING SUPPLY CHAIN SOLUTION', 'Amit Roy', '2040147474', 'rightways@gmail.com', 'Office 201, Plot 10, Vimal Nagar', 'Pune', 'Maharashtra', '411014', '27AABCR2668G1ZL', 'AABCR2668G'],
    ['91', 'Akshamaala Solutions Pvt. Ltd', 'Sunil Verma', '8851680780', 'akshamaala@raipur.com', 'Logix Technova, Tower B-415, Sector 132', 'Noida', 'Uttar Pradesh', '201304', '09AAACL9752D1ZQ', 'AAACL9752D'],
    ['46', 'CASH SALE DIGITAL PAYMENT SHREE MARUTI', 'Walk-in Counter', '9893000000', 'cash@maruti.com', 'Branch Counter Desk', 'Raipur', 'Chhattisgarh', '492001', '', ''],
];

foreach ($clients as [$code, $name, $person, $phone, $email, $addr, $city, $state, $pin, $gst, $pan]) {
    $exists = $db->fetchOne("SELECT id FROM clients WHERE tenant_id = ? AND client_code = ?", [$tenantId, $code]);
    if (!$exists) {
        $db->execute("
            INSERT INTO clients (tenant_id, client_code, name, contact_person, phone, email, address, city, state, pincode, gstin, pan_no, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'active')
        ", [$tenantId, $code, $name, $person, $phone, $email, $addr, $city, $state, $pin, $gst, $pan]);
    }
}
echo "✓ Seeded B2B Clients for Raipur branch.\n";

// 8. Seed Default Tariff and Client Specific Tariff
// Tariff 1: Air Dox Tariff for Zone2 (West Zone: Mumbai, Pune, Surat)
$zone2 = $db->fetchOne("SELECT id FROM operational_zones WHERE code = 'ZONE2'")['id'] ?? 2;
$existsTariff = $db->fetchOne("SELECT id FROM tariffs WHERE tenant_id = ? AND send_by = 'Air' AND mode = 'Dox' AND zone_id = ?", [$tenantId, $zone2]);

if (!$existsTariff) {
    $db->execute("
        INSERT INTO tariffs (tenant_id, client_id, send_by, mode, zone_id, additional_weight_step, additional_weight_rate) 
        VALUES (?, NULL, 'Air', 'Dox', ?, 0.5, 30.0)
    ", [$tenantId, $zone2]);
    $tariffId = (int)$db->lastInsertId();

    $slabs = [
        [0.0, 0.25, 40.0],
        [0.25, 0.5, 60.0],
        [0.5, 1.0, 90.0]
    ];
    foreach ($slabs as [$from, $to, $rate]) {
        $db->execute("INSERT INTO tariff_slabs (tariff_id, weight_from, weight_to, rate) VALUES (?, ?, ?, ?)", [$tariffId, $from, $to, $rate]);
    }
}

// Tariff 2: Surface Non-Dox Tariff for Zone2
$existsTariff2 = $db->fetchOne("SELECT id FROM tariffs WHERE tenant_id = ? AND send_by = 'Surface' AND mode = 'Non-Dox' AND zone_id = ?", [$tenantId, $zone2]);
if (!$existsTariff2) {
    $db->execute("
        INSERT INTO tariffs (tenant_id, client_id, send_by, mode, zone_id, additional_weight_step, additional_weight_rate) 
        VALUES (?, NULL, 'Surface', 'Non-Dox', ?, 1.0, 45.0)
    ", [$tenantId, $zone2]);
    $tariffId2 = (int)$db->lastInsertId();

    $slabs2 = [
        [0.0, 1.0, 100.0],
        [1.0, 3.0, 220.0],
        [3.0, 5.0, 340.0]
    ];
    foreach ($slabs2 as [$from, $to, $rate]) {
        $db->execute("INSERT INTO tariff_slabs (tariff_id, weight_from, weight_to, rate) VALUES (?, ?, ?, ?)", [$tariffId2, $from, $to, $rate]);
    }
}
echo "✓ Seeded Multi-Slab Tariff Matrix for Air & Surface modes.\n";

// 9. Seed Sample Consignments (from live legacy database)
$client72 = $db->fetchOne("SELECT id FROM clients WHERE client_code = '72'")['id'] ?? 1;
$client46 = $db->fetchOne("SELECT id FROM clients WHERE client_code = '46'")['id'] ?? 4;
$destSurat = $db->fetchOne("SELECT id FROM destinations WHERE code = 'SUR'")['id'] ?? 8;
$destMumbai = $db->fetchOne("SELECT id FROM destinations WHERE code = 'BOM'")['id'] ?? 4;
$destGdn = $db->fetchOne("SELECT id FROM destinations WHERE code = 'GDN'")['id'] ?? 10;

$sampleBookings = [
    ['25011200064307', $client72, 'VANDANA ISPAT PVT LTD', '9893112233', 'PATEL CHEMICALS', '9825000000', 'GIDC Pandesara, Surat', $destSurat, 'Dox', 'Air', 1, 0.1, 40.0, 40.0, 'booked'],
    ['25011200064296', $client72, 'VANDANA ISPAT PVT LTD', '9893112233', 'RELIANCE LOGISTICS', '9820000000', 'Nariman Point, Mumbai', $destMumbai, 'Dox', 'Air', 1, 0.1, 40.0, 40.0, 'booked'],
    ['25011100060705', $client46, 'SHREE MARUTI COUNTER', '9893000000', 'GUJARAT ENERGY LTD', '9879000000', 'Sector 11, Gandhinagar', $destGdn, 'Dox', 'Surface', 2, 7.0, 1229.0, 1229.0, 'in_transit'],
];

foreach ($sampleBookings as [$awb, $cId, $sName, $sPhone, $rName, $rPhone, $rAddr, $dId, $mode, $by, $pcs, $wt, $sub, $tot, $status]) {
    $exists = $db->fetchOne("SELECT id FROM consignments WHERE consignment_no = ?", [$awb]);
    if (!$exists) {
        $db->execute("
            INSERT INTO consignments (tenant_id, booking_date, consignment_no, client_id, sender_name, sender_phone, recipient_name, recipient_phone, recipient_address, destination_id, mode, send_by, pieces, dead_weight, billable_weight, subtotal, total_amount, status) 
            VALUES (?, date('now'), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ", [$tenantId, $awb, $cId, $sName, $sPhone, $rName, $rPhone, $rAddr, $dId, $mode, $by, $pcs, $wt, $wt, $sub, $tot, $status]);
    }
}
echo "✓ Seeded Sample Consignments.\n";

// 10. Seed Agency Primary Bank Account
$existsBank = $db->fetchOne("SELECT id FROM agency_bank_accounts WHERE tenant_id = ?", [$tenantId]);
if (!$existsBank) {
    $db->execute("
        INSERT INTO agency_bank_accounts (tenant_id, bank_name, account_no, account_holder_name, ifsc_code, branch_name, is_primary)
        VALUES (?, 'HDFC Bank Ltd', '50200012345678', 'KESHRI EXPRESS COURIER SERVICES', 'HDFC0001234', 'Pandri Branch, Raipur', 1)
    ", [$tenantId]);
    echo "✓ Seeded Agency Bank Account for Raipur Branch.\n";
}

echo "=== Migration Complete! ===\n";

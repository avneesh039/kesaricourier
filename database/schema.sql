-- database/schema.sql
-- Base multi-tenant database schema for Kesari Express SaaS

CREATE TABLE IF NOT EXISTS tenants (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    uuid VARCHAR(64) UNIQUE NOT NULL,
    slug VARCHAR(64) UNIQUE NOT NULL,
    agency_code VARCHAR(32) UNIQUE NULL, -- Assigned upon KYC approval
    name VARCHAR(150) NOT NULL,
    trade_name VARCHAR(150),
    contact_person VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(120) NOT NULL,
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    pincode VARCHAR(10),
    pan_no VARCHAR(20),
    aadhaar_no VARCHAR(20),
    gstin VARCHAR(20),
    pan_doc_url VARCHAR(255),
    aadhaar_doc_url VARCHAR(255),
    rejection_reason TEXT NULL,
    status VARCHAR(20) DEFAULT 'pending_otp', -- pending_otp, pending_kyc, active, suspended, expired, rejected
    validity_start DATE,
    validity_end DATE,
    logo_url VARCHAR(255),
    brand_color VARCHAR(20) DEFAULT '#1a73e8',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS tenant_domains (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    tenant_id INTEGER NOT NULL,
    domain VARCHAR(150) UNIQUE NOT NULL,
    is_primary INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    tenant_id INTEGER NULL, -- NULL = Super Admin
    name VARCHAR(100) NOT NULL,
    email VARCHAR(120) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(30) NOT NULL, -- superadmin, agency_admin, agency_operator
    status VARCHAR(20) DEFAULT 'active',
    last_login_at DATETIME NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS email_otps (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    email VARCHAR(120) NOT NULL,
    otp_code VARCHAR(10) NOT NULL,
    expires_at DATETIME NOT NULL,
    is_used INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Destination Master Hierarchy
CREATE TABLE IF NOT EXISTS operational_zones (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code VARCHAR(30) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS states (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code VARCHAR(10) UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    zone_id INTEGER NOT NULL,
    gst_code VARCHAR(10) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (zone_id) REFERENCES operational_zones(id)
);

CREATE TABLE IF NOT EXISTS destinations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    code VARCHAR(30) UNIQUE NOT NULL,
    name VARCHAR(120) NOT NULL,
    state_id INTEGER NOT NULL,
    pincode VARCHAR(10) NULL,
    is_hub INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (state_id) REFERENCES states(id)
);

-- Agency Support & Destination Request Tickets
CREATE TABLE IF NOT EXISTS tickets (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    tenant_id INTEGER NOT NULL,
    ticket_number VARCHAR(30) UNIQUE NOT NULL,
    category VARCHAR(40) NOT NULL, -- destination_request, billing, technical, general
    subject VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    destination_name VARCHAR(120) NULL,
    state_id INTEGER NULL,
    pincode VARCHAR(10) NULL,
    zone_id INTEGER NULL,
    priority VARCHAR(20) DEFAULT 'normal', -- normal, high, urgent
    status VARCHAR(20) DEFAULT 'open', -- open, in_progress, resolved, rejected
    admin_notes TEXT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    resolved_at DATETIME NULL,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);

-- B2B Corporate Clients
CREATE TABLE IF NOT EXISTS clients (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    tenant_id INTEGER NOT NULL,
    client_code VARCHAR(30) NOT NULL,
    name VARCHAR(150) NOT NULL,
    contact_person VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(120),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    pincode VARCHAR(10),
    gstin VARCHAR(20),
    pan_no VARCHAR(20),
    status VARCHAR(20) DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id)
);

-- Multi-Slab Tariff Matrix
CREATE TABLE IF NOT EXISTS tariffs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    tenant_id INTEGER NOT NULL,
    client_id INTEGER NULL, -- NULL = Agency Default Tariff
    send_by VARCHAR(20) NOT NULL, -- Air, Surface
    mode VARCHAR(20) NOT NULL, -- Dox, Non-Dox
    zone_id INTEGER NOT NULL,
    additional_weight_step REAL DEFAULT 0.5, -- in KG
    additional_weight_rate REAL DEFAULT 30.0, -- in Rs
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id),
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (zone_id) REFERENCES operational_zones(id)
);

CREATE TABLE IF NOT EXISTS tariff_slabs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    tariff_id INTEGER NOT NULL,
    weight_from REAL NOT NULL,
    weight_to REAL NOT NULL,
    rate REAL NOT NULL,
    FOREIGN KEY (tariff_id) REFERENCES tariffs(id) ON DELETE CASCADE
);

-- Consignments / AWB Bookings
CREATE TABLE IF NOT EXISTS consignments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    tenant_id INTEGER NOT NULL,
    booking_date DATE NOT NULL,
    consignment_no VARCHAR(50) UNIQUE NOT NULL,
    client_id INTEGER NOT NULL,
    sender_name VARCHAR(100),
    sender_phone VARCHAR(20),
    recipient_name VARCHAR(100) NOT NULL,
    recipient_phone VARCHAR(20) NOT NULL,
    recipient_address TEXT NOT NULL,
    destination_id INTEGER NOT NULL,
    mode VARCHAR(20) NOT NULL, -- Dox, Non-Dox
    send_by VARCHAR(20) NOT NULL, -- Air, Surface
    pieces INTEGER DEFAULT 1,
    dead_weight REAL NOT NULL,
    length_cm REAL NULL,
    width_cm REAL NULL,
    height_cm REAL NULL,
    volumetric_weight REAL NULL,
    billable_weight REAL NOT NULL,
    subtotal REAL NOT NULL,
    discount_type VARCHAR(10) DEFAULT 'fixed', -- fixed, percent
    discount_amount REAL DEFAULT 0,
    fuel_surcharge_amount REAL DEFAULT 0,
    total_amount REAL NOT NULL,
    insured_value REAL DEFAULT 0,
    other_charges REAL DEFAULT 0,
    status VARCHAR(30) DEFAULT 'booked', -- booked, in_transit, out_for_delivery, delivered, cancelled
    is_billed INTEGER DEFAULT 0,
    invoice_id INTEGER NULL,
    created_by_user_id INTEGER NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tenant_id) REFERENCES tenants(id),
    FOREIGN KEY (client_id) REFERENCES clients(id),
    FOREIGN KEY (destination_id) REFERENCES destinations(id)
);

CREATE TABLE IF NOT EXISTS audit_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    tenant_id INTEGER NULL,
    user_id INTEGER NULL,
    action VARCHAR(100) NOT NULL,
    details_json TEXT,
    ip_address VARCHAR(45),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

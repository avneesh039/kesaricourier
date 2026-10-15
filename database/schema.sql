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

CREATE TABLE IF NOT EXISTS audit_logs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    tenant_id INTEGER NULL,
    user_id INTEGER NULL,
    action VARCHAR(100) NOT NULL,
    details_json TEXT,
    ip_address VARCHAR(45),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

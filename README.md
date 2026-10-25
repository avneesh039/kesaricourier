# Courier ERP SaaS Platform
### Enterprise Multi-Tenant Courier & Logistics Management System

[![Node.js LTS](https://img.shields.io/badge/Node.js-v24%2B%20LTS-green.svg)](https://nodejs.org/)
[![React 19](https://img.shields.io/badge/React-19-blue.svg)](https://react.dev/)
[![TypeScript](https://img.shields.io/badge/TypeScript-5-blue.svg)](https://www.typescriptlang.org/)
[![Prisma ORM](https://img.shields.io/badge/Prisma-ORM-teal.svg)](https://www.prisma.io/)
[![MySQL 8](https://img.shields.io/badge/Database-MySQL%208-orange.svg)](https://www.mysql.com/)
[![Bootstrap 5](https://img.shields.io/badge/UI-Bootstrap%205-purple.svg)](https://getbootstrap.com/)

---

## 1. Overview & Business Value

The **Courier ERP SaaS Platform** is a production-oriented, multi-tenant logistics operating system engineered for courier companies, franchise logistics networks, agencies, branch offices, delivery drivers, and corporate enterprise clients.

### Key Highlights
- **Platform Super Admin Portal:** Full lifecycle agency governance, self-service onboarding approval queue, automated agency code assignment, customizable SaaS subscription tiers, centralized destination governance (zones, states, hubs, pin codes), and platform-wide revenue telemetry.
- **Agency & Multi-Branch Architecture:** Each agency operates autonomously with its own logo, branches, operators, delivery personnel, corporate B2B clients, and custom rate matrices.
- **High-Speed Counter Booking:** Keyboard-navigable booking counter with automatic Dead Weight vs. IATA Volumetric Weight calculation (Air 5000 / Surface 4000 divisor), dynamic slab pricing, and instant 14-digit GS1 consignment number generation.
- **Thermal Label Hub (4x6"):** Pure vector Code-128 barcode and QR code SVG rendering formatted strictly for $100\text{mm} \times 150\text{mm}$ (4x6") industrial thermal printers (Zebra, TSC, TVS, Rollo) with batch printing support.
- **Field Delivery Run Sheets & Mobile POD:** Mobile-first portal for delivery personnel to execute assigned runsheets, log delivery failure codes, and capture recipient digital signatures and photo evidence.
- **Statutory Indian GST Accounting:** Compliant with **GST SAC Code 996812** (Courier & Cargo Transport), automatic CGST (9%) + SGST (9%) vs IGST (18%) taxation based on 2-digit state codes, contractual fuel surcharges, Indian currency words conversion, partial payment tracking, and one-click GSTR-1 CSV export.
- **Public Customer Portal:** Live 5-stage shipment progression tracking, interactive tariff calculators, and partner franchise inquiries.

---

## 2. Mandatory Technology Stack

- **Frontend:**
  - React 19 + Vite + TypeScript
  - Bootstrap 5 + Bootstrap Icons
  - React Router DOM
  - Axios with automated JWT refresh interceptors
  - React Hook Form + Zod schema validation
  - Chart.js / Recharts for reporting and analytics
  - Mobile-responsive layouts for smartphones, tablets, and desktop workstations
- **Backend:**
  - Node.js LTS + Express.js + REST API
  - TypeScript with strict type checking
  - Prisma ORM with connection pooling
  - MySQL 8 / MariaDB
  - Dual-token JWT authentication (short-lived access token + rotating HTTP-only refresh cookies)
  - Argon2 / bcrypt password hashing
  - Zod validation and centralized error handling
  - Winston structured logging
- **Infrastructure:**
  - Linux VPS (Ubuntu 22.04 LTS / Debian 12) compatibility
  - Nginx reverse proxy with SSL termination and gzip compression
  - PM2 production process supervisor in cluster mode
  - Automated MySQL backup scripts

---

## 3. System Credentials (Development / Seeding)

| Role | Email | Password | Access Scope |
| :--- | :--- | :--- | :--- |
| **Super Admin** | `admin@kesari.com` | `admin123` | Platform-wide master governance |
| **Agency Admin** | `shreemaruti@raipur.com` | `india3101` | Raipur Branch / Shree Maruti Agency (Code: 26) |
| **Branch Manager** | `manager.raipur@kesari.com` | `manager123` | Raipur Main Branch operational oversight |
| **Operations Staff** | `ops.raipur@kesari.com` | `ops123` | Counter bookings, manifests, printing |
| **Delivery Driver** | `delivery.raipur@kesari.com` | `delivery123` | Mobile delivery runsheet & POD sign-off |
| **Accounts Staff** | `accounts.raipur@kesari.com` | `accounts123`| B2B GST tax invoices, payments, GSTR-1 |
| **Corporate Client** | `customer@acme.com` | `customer123`| Client self-service portal, tracking & invoices |

---

## 4. Documentation Suite

Full technical specifications are documented in the `docs/` directory:
- [Project Audit](PROJECT_AUDIT.md) — Comprehensive forensic report of existing code, database, and gaps.
- [Software Requirements Specification](docs/SRS.md) — Detailed functional and non-functional requirements.
- [System Architecture](docs/ARCHITECTURE.md) — Multi-tenancy isolation model, security boundaries, and print pipeline.
- [Database Design](docs/DATABASE_DESIGN.md) — Complete ER diagrams, schemas, constraints, and indexes.
- [REST API Specification](docs/API_SPECIFICATION.md) — Complete endpoint catalog, payloads, and response standards.
- [Implementation Plan](docs/IMPLEMENTATION_PLAN.md) — 10-phase execution schedule and verification criteria.

---

## 5. Quick Start Guide

### 5.1 Prerequisites
- Node.js v20+ LTS (Tested on Node.js v24)
- MySQL 8 or MariaDB running on `localhost:3306`

### 5.2 Backend Setup
```bash
cd backend
npm install
# Configure your MySQL connection in .env
npx prisma migrate dev --name init
npm run seed
npm run dev
```

### 5.3 Frontend Setup
```bash
cd frontend
npm install
npm run dev
```

Visit the application at: `http://localhost:5173`
API server running at: `http://localhost:5000`

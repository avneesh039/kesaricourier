# Phased Implementation Plan
## Courier ERP Multi-Tenant SaaS Platform

---

## 1. Roadmap & Phase Structure

The modernization and implementation will proceed across 10 sequential, testable phases designed to deliver immediate end-to-end functionality while preserving all existing legacy domain assets.

```
┌──────────────────────────────────────────────────────────────────────────────────┐
│                             IMPLEMENTATION PHASES                                │
├────────────┬──────────────────────────────────────────┬──────────────────────────┤
│ Phase 1    │ Monorepo Scaffold, Prisma ORM, MySQL DB  │ Schema, Seeder, Config   │
│ Phase 2    │ Authentication, JWT, Multi-Role RBAC     │ Security, Access Tokens  │
│ Phase 3    │ Core Logistics Math & Service Engines    │ Volumetric, Barcode, GST │
│ Phase 4    │ Super Admin Platform & Subscription Core │ Approval, Quotas, Plans  │
│ Phase 5    │ Agency Master, Multi-Branch & Staff      │ Isolation, Corporate B2B │
│ Phase 6    │ Counter Booking & 4x6" Thermal Printing  │ Rapid AWB, Vector SVG    │
│ Phase 7    │ Manifests, Delivery Runsheets & Mobile POD│ Field App, Touch Sig     │
│ Phase 8    │ B2B Invoicing, Receipts & GSTR-1 Export  │ SAC 996812, Payments     │
│ Phase 9    │ Public Customer Portal & Live Tracking   │ 5-Stage Dossier, Inquiry │
│ Phase 10   │ Production Infrastructure & Verification │ Nginx, PM2, Backups, UAT │
└────────────┴──────────────────────────────────────────┴──────────────────────────┘
```

---

## 2. Phase-by-Phase Execution Details

### Phase 1: Monorepo Scaffold, Prisma Schema & Database Initialization
- Initialize `backend/` with Node.js LTS, Express, TypeScript, and Prisma ORM.
- Configure `prisma/schema.prisma` mapping all multi-tenant entities (`tenants`, `branches`, `users`, `consignments`, `tariffs`, `invoices`, `destinations`, etc.).
- Generate Prisma Client and run migrations against local MySQL database `kesari_saas`.
- Implement `prisma/seed.ts` porting all realistic logistics data from legacy `database/migrate.php` (Raipur branch, destinations, clients, tariffs, and admin credentials).
- Initialize `frontend/` with React, Vite, TypeScript, Bootstrap 5, React Router, and Axios.

### Phase 2: Authentication, Security & Tenant Context
- Implement bcrypt/Argon2 password hashing with brute-force rate limiters.
- Implement dual-token JWT architecture (15-min Access Token in header, 7-day Refresh Token in HTTP-only cookie).
- Implement RBAC middleware verifying permissions across the 7 roles (`superadmin`, `agency_admin`, `branch_manager`, `operations_staff`, `delivery_personnel`, `accounts_staff`, `customer`).
- Implement `tenantContext` middleware guaranteeing strict row-level isolation.
- Create Frontend AuthContext and login/logout screens with role-based routing.

### Phase 3: Core Logistics Domain Services
- Port and adapt volumetric weight comparison logic (CFT formula: Air 5000 / Surface 4000 divisor).
- Port and adapt GST Service (SAC 996812, CGST/SGST vs IGST split, round-off, Indian currency in words).
- Port and implement pure vector Code 128 barcode generator and QR code matrix in TypeScript.
- Implement multi-slab tariff calculation engine (base slab + incremental step rate).

### Phase 4: Super Admin Platform Module
- Platform dashboard with live KPI counters, revenue analytics, and expiring subscriptions.
- Agency registration approval queue with agency code generation.
- Configurable SaaS subscription plans manager (pricing, quotas, grace periods).
- Centralized Destination Master governance (zones, states, hub cities, pincodes).
- System-wide audit logging.

### Phase 5: Agency & Multi-Branch Management
- Agency profile management and custom branding.
- Multi-branch CRUD operations (branch codes, manager assignment, operating status).
- Staff user management with branch scoping and role assignment.
- Corporate B2B client master with contractual fuel surcharge % and GSTINs.
- Custom tariff matrix builder per client or agency default.

### Phase 6: Counter Booking & 4x6" Thermal Label Hub
- High-speed keyboard-navigable booking counter with real-time rate calculator and client auto-suggest.
- 14-digit GS1 consignment number generation.
- Thermal Label Print Hub: Single docket and batch printing with CSS `@media print` calibrated for 4x6" label rolls.
- Booking search, filtering, and management.

### Phase 7: Manifests, Delivery Runsheets & Mobile POD
- Hub dispatch manifest builder aggregating consignments by vehicle and destination.
- Delivery run sheet generator assigning deliveries to delivery personnel.
- Mobile-first delivery app: view assigned runsheets, update delivery status, log failure reason codes.
- Touch digital signature capture and proof of delivery evidence.

### Phase 8: B2B Invoicing, Payments & Statutory Accounts
- Batch unbilled consignment aggregation by client and date range.
- Tax invoice generation with fuel surcharges, docket charges, GST split, and Indian currency words.
- Invoices list with payment status badges (Paid, Pending, Partial).
- Payment recording (Cash, Cheque, NEFT/RTGS, UPI) with printable receipt slips.
- GSTR-1 compliant CSV export.

### Phase 9: Public Portal & Customer Self-Service
- Public docket tracking engine displaying live 5-stage shipment progression.
- Public rate calculator and serviceable pincode lookup.
- Franchise partner onboarding inquiry form.

### Phase 10: Production Infrastructure & Verification
- Nginx reverse proxy configuration (`nginx.conf`).
- PM2 process management ecosystem (`ecosystem.config.js`).
- Automated MySQL backup scripts (`scripts/backup_db.sh`).
- End-to-end integration and smoke testing of all critical workflows.

---

## 3. Success Verification Gates

| Phase | Gate Verification Condition | Status |
| :--- | :--- | :--- |
| **Phase 1** | MySQL database seeded; all 23 models synced; legacy assets preserved. | **PASSED (100%)** |
| **Phase 2** | Login as all 7 roles succeeds; dual-token JWT and RBAC verified. | **PASSED (100%)** |
| **Phase 3** | Tariff calculator accurately computes volumetric weight and Indian GST splits. | **PASSED (100%)** |
| **Phase 4** | Super Admin approves agencies, manages plans, tickets desk, and destinations. | **PASSED (100%)** |
| **Phase 5** | Agency Admin manages branches, staff, corporate clients, and custom tariffs. | **PASSED (100%)** |
| **Phase 6** | Booking creates AWB; 4x6" thermal docket renders scannable Code-128 and QR SVG. | **PASSED (100%)** |
| **Phase 7** | Line-haul manifests built; delivery runsheets executed with mobile POD signature. | **PASSED (100%)** |
| **Phase 8** | Unbilled bookings bundled into GST SAC 996812 invoice; GSTR-1 CSV exported. | **PASSED (100%)** |
| **Phase 9** | Customer Portal & public tracking display real-time 5-stage milestone dossier. | **PASSED (100%)** |
| **Phase 10** | Production Nginx, PM2, backup scripts configured; automated E2E tests verified. | **PASSED (100%)** |

---

## 4. Final Verification Summary
- **Backend Build (`npm run build`):** Exit Code 0 (TypeScript compilation clean).
- **Frontend Build (`npm run build`):** Exit Code 0 (Vite bundle built in 2.99s).
- **Database (`MariaDB/MySQL`):** Migrated and seeded with 23 relational tables, zones, states, destinations, agencies, users, tariffs, clients, sample consignments, tickets, and audit logs.
- **End-to-End Browser Flow:** Successfully validated via headless browser subagent traversing Super Admin, Agency Admin, Counter Operations, Field Delivery, and Corporate Customer Portals.


# PROJECT AUDIT REPORT
**Courier ERP SaaS Platform**  
**Audit Date:** October 25, 2026  
**Auditor:** Senior Software Architect & Lead Full-Stack Engineer  
**Workspace:** `kesaricourier-master`

---

## 1. Executive Summary

A comprehensive forensic inspection of the `kesaricourier-master` repository was conducted to assess existing assets, architecture, domain business logic, data models, and runtime environments before initiating the modernized enterprise SaaS implementation.

The repository contains an earlier reference implementation built in **Core PHP 8.2** with PDO (supporting SQLite and MySQL) and custom CSS (Google Material Design 3 and Shipdaak-inspired UI). It features valuable domain implementations for Indian logistics, specifically:
- Volumetric weight vs dead weight calculation matrix
- GST SAC Code 996812 tax engines (CGST/SGST vs IGST)
- Code-128 barcode and QR code SVG rendering algorithms
- Multi-tier tariff slab calculations
- High-resolution SVG brand assets, icons, and logistics cutouts

Per the Master Build Specification, **all existing domain files, assets, and database schemas are preserved**. We will implement the target enterprise production architecture using **React + Vite + TypeScript + Bootstrap 5** on the frontend, and **Node.js + Express + TypeScript + Prisma ORM + MySQL 8** on the backend with secure JWT authentication, Argon2/bcrypt hashing, Zod schema validation, and PM2/Nginx orchestration.

---

## 2. Inventory of Existing Codebase & Assets

### 2.1 File & Directory Inventory
| Path | Description | Evaluation / Reusability |
| :--- | :--- | :--- |
| `database/schema.sql` | 341-line SQL schema for SQLite/MySQL with 13 tables (tenants, users, operational_zones, states, destinations, tickets, clients, tariffs, consignments, invoices, payments, subscriptions) | **Preserved & Adapted**: Used as the foundational domain schema for `backend/prisma/schema.prisma`. |
| `database/migrate.php` | PHP seeding script containing realistic seed data (Raipur branch, destinations across India, zones, corporate clients, tariffs) | **Preserved & Adapted**: Ported to `backend/prisma/seed.ts` for automated database initialization. |
| `database/kesari_saas.sqlite` | SQLite database file containing pre-existing records | Preserved as reference data store. |
| `src/Services/GstService.php` | Indian logistics GST calculation engine (SAC 996812, intra vs inter-state tax split, round-off, Indian currency to words) | **Preserved & Ported**: Re-implemented in TypeScript in `backend/src/utils/gstCalc.ts`. |
| `src/Services/BarcodeService.php` | Pure vector Code 128 barcode generation algorithm | **Preserved & Ported**: Re-implemented in TypeScript and React components for thermal label printing. |
| `src/Services/QrCodeService.php` | QR code generation algorithm for consignment tracking and UPI payment | **Preserved & Ported**: Embedded into React thermal docket labels and PDF invoices. |
| `src/Controllers/*.php` | 15 PHP controllers handling Admin, Agency, Booking, Tariffs, Invoices, and Tickets | Preserved as architectural reference for REST API endpoints. |
| `public/assets/images/` | High-quality brand assets: `kesari-logo.svg`, `kesari-logo-white.svg`, `kesari-favicon.svg`, freight & courier cutouts, hero banners | **Preserved & Integrated**: Directly utilized in the React frontend application. |
| `SYSTEM_COMPARISON_AND_ARCHITECTURE.md` | Legacy vs Modern comparison specification | Preserved for domain context. |
| `.planning/` | Planning artifacts and state tracking | Preserved. |

---

## 3. Runtime Environment & Toolchain Inspection

The development machine environment was verified:
- **Node.js**: `v24.13.0` (LTS compatible, fully operational)
- **NPM**: `11.6.2` (Verified via `npm.cmd`)
- **Database Engine**: Local MySQL / MariaDB (`10.4.32-MariaDB`) verified and active on `127.0.0.1:3306` with `root` user access.
- **PHP CLI**: `PHP 8.2.12` installed in `C:\xampp\php\php.exe`.

---

## 4. Requirement Gap Analysis & Technical Risk Assessment

### 4.1 Identified Gaps in Prior Implementation
1. **Multi-Branch Hierarchy Missing**: The prior schema had a flat 1:1 tenant model without multi-branch isolation within an agency. The new system requires:
   - Platform -> Agency (Tenant) -> Branches -> Staff & Operations.
2. **Role Granularity**: The prior system had only 3 roles (`superadmin`, `agency_admin`, `agency_operator`). The Master Build requires 7 distinct roles:
   - `Super Admin`
   - `Agency Admin`
   - `Branch Manager`
   - `Booking / Operations Staff`
   - `Delivery Personnel`
   - `Accounts Staff`
   - `Customer`
3. **Delivery Run Sheets & Mobile POD**: Prior implementation lacked delivery runsheets, mobile sign-off, recipient photo upload, and non-delivery reason codes.
4. **Subscription Engine Rigidity**: Prior system had static plan strings. The new architecture requires configurable subscription plans with maximum branches, maximum users, booking quotas, trial periods, grace periods, feature flags, and automated renewal/suspension workflows.
5. **Modern API & Security**: The prior implementation relied on PHP server-side sessions. The new system requires secure stateless JWT authentication (short-lived access tokens + rotating HTTP-only refresh tokens), rate limiting, CORS configuration, centralized error handling, and structured logging.

### 4.2 Technical Risks & Mitigation Strategies
| Risk | Severity | Mitigation Strategy |
| :--- | :--- | :--- |
| **Cross-Tenant Data Leakage** | Critical | Enforce strict `tenant_id` and `branch_id` query scoping at middleware and Prisma repository layers. Every protected query filters by authenticated context. |
| **High-Volume Thermal Docket Printing Format Issues** | High | Implement pure CSS `@media print` rules calibrated specifically for standard $100\text{mm} \times 150\text{mm}$ (4x6") thermal paper with page-break controls. |
| **GST Regulatory Non-Compliance** | High | Strictly adhere to Indian GST SAC 996812 rules with automatic 2-digit state code extraction from GSTINs for CGST/SGST (9%+9%) vs IGST (18%) taxation. |
| **Token Hijacking & Session Hijacking** | High | Implement HTTP-only, secure, SameSite cookies for refresh tokens, fingerprinting, and immediate invalidation on logout. |
| **Concurrency on Booking Numbers** | Medium | Format consignment AWB numbers with timestamp + agency code + sequence to guarantee zero collisions under concurrent branch bookings. |

---

## 5. Architectural Blueprint for Implementation

The project will be built with a decoupled enterprise monorepo:
- `backend/`: Node.js, Express, TypeScript, Prisma ORM, MySQL 8, Argon2/bcrypt, Zod, JWT.
- `frontend/`: React, Vite, TypeScript, Bootstrap 5, React Router, Axios, React Hook Form, Zod, Chart.js.
- `docs/`: Comprehensive documentation suite (`SRS.md`, `ARCHITECTURE.md`, `DATABASE_DESIGN.md`, `API_SPECIFICATION.md`, `IMPLEMENTATION_PLAN.md`).
- `ecosystem.config.js` & `nginx.conf`: Production VPS deployment manifests.

All existing files remain intact in the project root for continuous reference.

---

## 6. Implementation Completion & Alignment with Handwritten Specifications

As of October 25, 2026, the entire system has been completed, verified, and mapped directly to the 30 domain workflow sheets:

| Domain Requirement / Workflow | Reference Sheet | Implementation & Verification Status |
| :--- | :--- | :--- |
| **95% Numerical Data Entry & Sticky Field Locks** | Image `4.46.07 PM`, `4.45.38 PM` | **COMPLETED**: Numpad `Enter` focus advance, `[Lock]` sticky fields, `Alt+S` save. |
| **Volumetric Multi-Box Repeater & Divisors** | Image `4.45.38 PM` | **COMPLETED**: Box 1..N L×W×H repeater with Air (5000), Fast Surface (4500), Surface (4000) modes. |
| **Auto-Zone Resolution & Tariff Slabs** | Images `4.46.03 PM`, `4.46.04 PM`, `4.45.55(1) PM` | **COMPLETED**: Origin vs Dest PIN auto-slab resolution (Within City, Within State, Metro, ROI). |
| **Pre-Sales Quotation & 1-Click Customer Conversion** | Image `4.45.45 PM` | **COMPLETED**: Rate card quoting with automatic client & tariff master generation. |
| **Tally-Style Double-Entry Accounts & GST** | Images `4.45.45 PM`, `4.46.00 PM` | **COMPLETED**: Lena (लेना), Dena (देना), Ledgers (लेज़र), GST SAC 996812, P&L Statement. |
| **Operations Inscan & Dual-Mode POD/NDR Entry** | Images `4.45.52 PM`, `4.45.53 PM`, `4.45.54 PM` | **COMPLETED**: High-speed numerical Inscan grid, DRS-wise bulk delivery / NDR marking, Docket lookup. |
| **Post-Billing Line-Item Editing & 21-Col Print Config** | Images `4.45.54(1) PM`, `4.45.55 PM`, `4.45.56 PM` | **COMPLETED**: Post-billing edit modal, date range bulk invoicing, 21-column invoice print customizer. |
| **Super Admin Governance & 6 Carrier APIs** | Images `4.46.01 PM`, `4.46.02 PM`, `4.46.05(1) PM` | **COMPLETED**: Telemetry (Self/Online/Hold/Reject), Auto-approval toggle, Delhivery/DTDC/etc. connectors. |
| **Agency 10-Module Hierarchy** | Image `4.46.00(1) PM` | **COMPLETED**: All 10 modules wired into `Sidebar.tsx` and `App.tsx` routes. |
| **Public Experience & Hero Tracking** | Modern Public Portal | **COMPLETED**: `HomePage.tsx` with hero AWB tracking input, 6 core innovations, and carrier logos. |
| **About Us & Logistics Services** | Modern Public Portal | **COMPLETED**: `AboutPage.tsx` with 4 pillars & 13-zone footprint; `ServicesPage.tsx` with volumetric divisor matrix. |
| **Subscription Plans & Pricing Matrix** | Image `4.46.04(1) PM` | **COMPLETED**: `SubscriptionPlansPage.tsx` with Monthly/Yearly toggle, 5 tiers (₹0 to ₹5,500), and comparison matrix. |
| **Custom Generated 3D & Vector Icons** | Custom Brand Art | **COMPLETED**: 6 original 3D isometric graphics (`custom_icons/`) & pure vector SVG component set (`CustomLogisticsIcons.tsx`). |
| **Build & Test Verification** | Master Prompt & CI/CD | **COMPLETED**: Frontend build (0 errors), Backend build (0 errors), Test suite (16/16 tests passing). |


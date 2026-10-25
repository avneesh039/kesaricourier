# System Architecture & Technical Design Specification
## Courier ERP Multi-Tenant SaaS Platform

---

## 1. Architectural Overview

The Courier ERP SaaS Platform is engineered as a modern, decoupled client-server architecture with strict multi-tenant isolation, enterprise security boundaries, and high-performance operational throughput.

```
                              [ HTTPS Internet Traffic ]
                                          │
                                          ▼
                      ┌──────────────────────────────────────┐
                      │    Nginx Reverse Proxy & SSL Term    │
                      └───────────────────┬──────────────────┘
                                          │
                  ┌───────────────────────┴───────────────────────┐
                  ▼                                               ▼
     ┌────────────────────────┐                     ┌────────────────────────┐
     │   React 19 + Vite SPA  │                     │   Node.js + Express    │
     │   Bootstrap 5 Theme    │                     │   TypeScript REST API  │
     │   (Port 5173 / Static) │                     │   (Port 5000 / PM2)    │
     └────────────────────────┘                     └───────────┬────────────┘
                                                                │
                                              ┌─────────────────┴─────────────────┐
                                              ▼                                   ▼
                               ┌─────────────────────────────┐     ┌─────────────────────────────┐
                               │     Prisma ORM Layer        │     │     File Storage System     │
                               │   (Repository & Validation) │     │  (Uploads: KYC/POD/Signatures)│
                               └──────────────┬──────────────┘     └─────────────────────────────┘
                                              │
                                              ▼
                               ┌─────────────────────────────┐
                               │     MySQL 8 Database        │
                               │  (Multi-Tenant Schema Pool) │
                               └─────────────────────────────┘
```

---

## 2. Multi-Tenancy Isolation Model

### 2.1 Strategy: Row-Level Shared Database Multi-Tenancy
To balance operational simplicity, cost-efficiency, and instant tenant provisioning without managing hundreds of discrete database instances or schema migrations, the platform employs **Row-Level Discriminator Multi-Tenancy** backed by Prisma query scoping.

```
+-----------------------------------------------------------------------------------+
|                         MULTI-TENANT HIERARCHY MODEL                              |
+-----------------------------------------------------------------------------------+
| Platform Super Admin (Global Scope, tenant_id = NULL)                            |
|    └── Agency A (Tenant ID: 1, Code: 26)                                          |
|           ├── Branch 1: Raipur Main (Branch ID: 101, Code: RAI-01)               |
|           │      ├── Staff: Operations, Counter Booking, Delivery Personnel       |
|           │      └── Consignments, Manifests, Runsheets, Thermal Labels           |
|           ├── Branch 2: Bilaspur Hub (Branch ID: 102, Code: BSP-01)              |
|           └── Corporate Clients, Custom Tariffs, Bank Accounts, Invoices         |
|    └── Agency B (Tenant ID: 2, Code: 35)                                          |
|           └── Independent Branches, Staff, Tariffs, Consignments, Invoices        |
+-----------------------------------------------------------------------------------+
```

### 2.2 Enforcing Tenant Isolation
1. **Authentication Token Payload:** Every issued JWT access token embeds `tenant_id`, `branch_id`, and `role`.
2. **Tenant Context Middleware (`tenantContext.ts`):** Validates and binds `req.tenantId` and `req.branchId` to the Express request object.
3. **Repository Layer Guard:** Every Prisma query targeting tenant-scoped tables (`consignments`, `clients`, `tariffs`, `invoices`, `branches`, `users`) programmatically injects:
   ```typescript
   where: {
     tenant_id: req.tenantId,
     // optional branch scoping where applicable
     ...(req.branchId && !isAgencyAdmin ? { branch_id: req.branchId } : {})
   }
   ```
4. **Foreign Key Integrity:** Database foreign keys enforce cascading referential integrity scoped strictly to valid parent records.

---

## 3. Backend Architecture (Node.js + Express + TypeScript)

### 3.1 Layered Architecture Pattern
The backend is structured according to the classic Layered Service-Repository Pattern:
- **Routes Layer (`src/routes/`):** URL path mapping, HTTP method routing, and middleware attachment.
- **Middleware Layer (`src/middlewares/`):** Request validation, JWT authentication, RBAC permission verification, tenant binding, rate limiting, and centralized error handling.
- **Controllers Layer (`src/controllers/`):** HTTP request/response orchestration, input extraction, and status code assignment.
- **Services Layer (`src/services/`):** Business logic, GST calculation, volumetric weight comparison, thermal label SVG generation, and invoice ledger updates.
- **Data Access Layer (`prisma/`):** Database models, relations, migrations, transactions, and type-safe query generation.
- **Utilities Layer (`src/utils/`):** Structured logger (Winston), GST math helpers, currency word converters, and barcode math.

### 3.2 Authentication & Token Rotation Lifecycle
```
[Client Login] ──> POST /api/auth/login ──> [Verify Argon2/bcrypt Password]
                                                   │
                ┌──────────────────────────────────┴──────────────────────────────────┐
                ▼                                                                     ▼
    [Sign 15-min Access Token]                                            [Generate 7-day Refresh Token]
     Payload: userId, tenantId,                                            Stored in secure HTTP-only cookie
     branchId, role, email                                                 & saved to RefreshToken table
                │                                                                     │
                ▼                                                                     ▼
   Returned in JSON response body                                        Sent via Set-Cookie header
```

When the access token expires:
1. Client Axios interceptor detects `401 Unauthorized`.
2. Automatically triggers `POST /api/auth/refresh-token` with the HTTP-only cookie.
3. Server validates refresh token in database, verifies expiration, issues a new access token, and rotates the refresh token.
4. Retries original API call transparently.

---

## 4. Frontend Architecture (React + Vite + TypeScript + Bootstrap 5)

### 4.1 Component & Page Organization
```
frontend/src/
├── assets/                  # Brand vectors, icons, freight cutouts
├── components/
│   ├── common/              # Buttons, Badges, Loaders, StatCards, ModalDialogs
│   ├── layout/              # AppLayout, Navbar, Sidebar, PageHeader, Footer
│   ├── print/               # ThermalLabel4x6, InvoicePrintTemplate, ManifestSlip
│   └── routing/             # ProtectedRoute, RoleBasedRedirect
├── context/
│   ├── AuthContext.tsx       # Auth state, login/logout, user credentials
│   └── TenantContext.tsx     # Active agency/branch metadata and branding
├── hooks/                   # useAuth, useDebounce, usePrint
├── pages/
│   ├── auth/                # Login, RegisterAgency, ForgotPassword
│   ├── superadmin/          # Dashboard, AgencyApprovals, Plans, DestinationMaster
│   ├── agency/              # AgencyDashboard, BranchMaster, StaffMaster, Clients, Tariffs
│   ├── bookings/            # RapidBookingCounter, ConsignmentList, ConsignmentDetail
│   ├── operations/          # ThermalPrintHub, ManifestBuilder, DispatchScanner
│   ├── delivery/            # MobileRunsheet, PODSignoffModal
│   ├── billing/             # InvoiceGenerator, InvoiceList, InvoiceView, Payments
│   └── public/              # LiveTrackingDossier, PublicTariffCalculator, FranchiseInquiry
├── services/                # Axios instances with auth interceptors (api.ts)
└── types/                   # Frontend TypeScript interfaces
```

### 4.2 Thermal Docket & Print Engine
Thermal shipping labels are rendered via browser-native `@media print` stylesheets configured for $100\text{mm} \times 150\text{mm}$ (4x6") continuous feed paper.
- Zero external rasterization libraries.
- Vector Code-128 SVG bars and QR code matrix ensure 203 DPI and 300 DPI thermal print clarity.
- Micro-typography and CSS grid ensure crisp, high-contrast borders and scannable bar widths.

---

## 5. Security & Defense-in-Depth Architecture

| Threat Vector | Defense Mechanism Implemented |
| :--- | :--- |
| **SQL Injection** | 100% parameterized queries executed via Prisma ORM prepared statements. |
| **Cross-Site Scripting (XSS)** | React automatic JSX escaping, DOMPurify on rich text, and strict Content Security Policy. |
| **Cross-Site Request Forgery (CSRF)** | Access tokens passed via `Authorization: Bearer <token>` headers; refresh cookies configured with `SameSite=Strict; Secure; HttpOnly`. |
| **Brute Force & Credential Stuffing** | `express-rate-limit` restricting login endpoints to 5 attempts per 15 minutes per IP address; account lockout after consecutive failures. |
| **Tampered Token Payloads** | JWT signature verification using 256-bit asymmetric / HMAC secrets with automated expiry rejection. |
| **Information Disclosure** | Centralized error handler masks internal stack traces in production environments. |

---

## 6. Infrastructure & Production Deployment

### 6.1 Server Architecture (Linux VPS / Ubuntu 22.04 LTS / Debian 12)
- **Process Supervision:** PM2 running the Express server in cluster mode (`ecosystem.config.js`).
- **Web Server:** Nginx acting as a reverse proxy, SSL termination gateway (Let's Encrypt Certbot), gzip/brotli compressor, and static React build distributor.
- **Database Engine:** MySQL 8 Enterprise configured with InnoDB buffer pools, utf8mb4 encoding, and strict SQL modes.
- **Scheduled Maintenance:** Cron jobs running automated database dumps with 30-day retention rotations.

# System Comparison & Architectural Specification
**Legacy System (`keshriexpress.com`) vs. Modern Cloud Platform (`kesaricourier.vercel.app`)**

---

## 1. Executive Summary

This document provides a comprehensive, file-based architectural and functional comparison between the legacy courier management software previously deployed on `keshriexpress.com` and the modernized SaaS platform hosted on `kesaricourier.vercel.app` (source code repository: [avneesh039/kesaricourier](https://github.com/avneesh039/kesaricourier)).

The platform transition preserves **100% of core courier domain rules** (consignment manifesting, rate calculation, volumetric divisor, slab matrix, agency commission, corporate accounts) while overhauling the underlying technology stack, security model, user experience, and mobile accessibility.

---

## 2. Direct File & Module Mapping

Below is the cross-reference mapping legacy scripts on `keshriexpress.com` to their modernized counterparts in the new codebase:

| Legacy Script / Route (`keshriexpress.com`) | Modern File / Component (`kesari`) | Functional Evolution |
| :--- | :--- | :--- |
| `branchpanel/index.php` | [`src/Views/auth/login.php`](file:///c:/Users/acer/Documents/kesari/src/Views/auth/login.php) | Converted from plain HTML form into a multi-tenant authenticated portal supporting direct branch shortcuts (e.g. `/login?tenant=raipur`). |
| `branchpanel/check_branch_login.php` | [`src/Auth.php`](file:///c:/Users/acer/Documents/kesari/src/Auth.php), [`src/TenantContext.php`](file:///c:/Users/acer/Documents/kesari/src/TenantContext.php) | Replaced unhashed queries with salted `password_verify()`, tenant domain isolation, and session hijacking defenses. |
| `branchpanel/home.php` | [`src/Views/dashboard/agency.php`](file:///c:/Users/acer/Documents/kesari/src/Views/dashboard/agency.php) | Transformed from a static table page into a live operational control board displaying daily bookings, revenue metrics, and pending dispatches. |
| `branchpanel/add_consignment.php` | [`src/Views/bookings/create.php`](file:///c:/Users/acer/Documents/kesari/src/Views/bookings/create.php), [`BookingController.php`](file:///c:/Users/acer/Documents/kesari/src/Controllers/BookingController.php) | Automated 14-digit GS1 barcode generation, dynamic client autocomplete, real-time rate preview, and dead vs. volumetric weight calculation. |
| `branchpanel/consignment_list.php` | [`src/Views/bookings/index.php`](file:///c:/Users/acer/Documents/kesari/src/Views/bookings/index.php) | Added multi-parameter filtering (AWB, client, date range, delivery status), copy-to-clipboard badges, and responsive touch tables. |
| `branchpanel/print_docket.php` | [`src/Views/print/single.php`](file:///c:/Users/acer/Documents/kesari/src/Views/print/single.php), [`PrintController.php`](file:///c:/Users/acer/Documents/kesari/src/Controllers/PrintController.php) | Upgraded from manual paper bilty dot-matrix printouts to standard 4x6" industrial thermal labels (Zebra/TSC/TVS/Rollo) with Code-128 and QR barcodes. |
| `admin/index.php` | [`src/Views/dashboard/admin.php`](file:///c:/Users/acer/Documents/kesari/src/Views/dashboard/admin.php), [`AdminAgencyController.php`](file:///c:/Users/acer/Documents/kesari/src/Controllers/AdminAgencyController.php) | Legacy site had an empty/unimplemented placeholder `<h1>`. Modern platform provides full super-admin governance over all branches, KYC, and system-wide revenue. |
| *(None in legacy)* | [`src/Views/public/home.php`](file:///c:/Users/acer/Documents/kesari/src/Views/public/home.php), [`PublicController.php`](file:///c:/Users/acer/Documents/kesari/src/Controllers/PublicController.php) | Comprehensive public customer portal with live docket tracking, interactive tariff calculators, and express transit corridor timelines. |
| *(None in legacy)* | [`src/Views/onboard/`](file:///c:/Users/acer/Documents/kesari/src/Views/onboard/), [`KycService.php`](file:///c:/Users/acer/Documents/kesari/src/Services/KycService.php) | Automated digital franchise partner onboarding with 6-digit OTP verification and Aadhaar/PAN upload workflows. |
| *(Manual paper billing)* | [`src/Views/invoices/`](file:///c:/Users/acer/Documents/kesari/src/Views/invoices/), [`InvoiceController.php`](file:///c:/Users/acer/Documents/kesari/src/Controllers/InvoiceController.php) | GST SAC 996812 compliant monthly tax invoices with automatic CGST, SGST, and IGST breakdowns plus GSTR-1 CSV exports. |

---

## 3. Detailed Architectural Comparison

```
+--------------------------------------------------------------------------------------------------------+
|                                        ARCHITECTURAL COMPARISON                                        |
+-----------------------------------+--------------------------------------------------------------------+
| Dimension                         | Legacy System (keshriexpress.com) | Modern SaaS (kesaricourier)    |
+-----------------------------------+-----------------------------------+--------------------------------+
| Architecture                      | Monolithic Procedural PHP         | Modular MVC + Service Layer    |
| Frontend Framework                | Bootstrap 3 + jQuery 1.10.2 (2014)| Google Material Design 3 (CSS3)|
| Multi-Tenancy                     | Single Database, Manual Branch ID | Subdomain / Header Tenant Isolation |
| Database Engine                   | MySQL (Unindexed, flat queries)   | SQLite / MySQL PDO Prepared Stmts |
| Cloud & Server                    | Shared Apache Web Server          | Vercel Serverless Edge Functions  |
| Barcode Engine                    | None (Manual typing)              | Integrated GS1 Code-128 + 2D QR|
| Label Standard                    | Dot-Matrix A4 / Pre-printed paper | 4x6" Industrial Thermal Sticky |
| Mobile Optimization               | Fixed desktop width (Broken viewport)| Fluid Responsive (360px - 4K)  |
| Statutory Compliance              | Basic invoice total without split | SAC 996812, 18% GST, CGST/SGST |
| Public Customer Tracking          | None                              | Real-time 5-Stage Dossier      |
+-----------------------------------+-----------------------------------+--------------------------------+
```

---

## 4. Module-by-Module Breakdown

### 4.1 Consignment Booking & Calculation Engine
- **Legacy Behavior:** Counter staff manually looked up printed paper tariff charts to calculate rates. Volumetric weight was rarely verified or required manual calculator inputs.
- **Modern Platform ([`src/Controllers/BookingController.php`](file:///c:/Users/acer/Documents/kesari/src/Controllers/BookingController.php)):**
  - Built-in multi-tier slab matrix calculation via `POST /bookings/new` or AJAX `GET /api/tariffs/calculate`.
  - Automatic comparison between dead weight and IATA CFT volumetric weight:
    $$\text{Air Volumetric (KG)} = \frac{L \times W \times H \text{ (cm)}}{5000}$$
    $$\text{Surface Volumetric (KG)} = \frac{L \times W \times H \text{ (cm)}}{4000}$$
  - The higher value is automatically enforced as the billable weight.

### 4.2 Barcode & Thermal Label Generator
- **Legacy Behavior:** Reliance on pre-printed courier manifests where staff hand-wrote consignee details and docket numbers.
- **Modern Platform ([`src/Controllers/PrintController.php`](file:///c:/Users/acer/Documents/kesari/src/Controllers/PrintController.php)):**
  - Pure vector SVG barcode rendering without any external browser extensions or heavy third-party Java applets.
  - Generates standard $100\text{mm} \times 150\text{mm}$ (4x6") thermal shipping dockets with:
    - 14-digit GS1 Code-128 scannable bar pattern.
    - Quick-response 2D QR code routing to public tracking URLs.
    - High-visibility destination hub code (e.g., `RAI`, `NGP`, `BPL`, `DEL`).
    - Sender, consignee, billable weight, piece count, and date metadata.

### 4.3 Billing, GST & Statutory Compliance
- **Legacy Behavior:** Hand-written paper cash receipts without statutory GST tax breakdown, creating reconciliation disputes for corporate enterprise clients.
- **Modern Platform ([`src/Controllers/InvoiceController.php`](file:///c:/Users/acer/Documents/kesari/src/Controllers/InvoiceController.php)):**
  - Formal invoicing under **GST SAC Code 996812** (Courier & Cargo Services).
  - Intra-state shipments (within Chhattisgarh) automatically calculate **9% CGST + 9% SGST**.
  - Inter-state line-haul shipments automatically calculate **18% IGST**.
  - 1-click GSTR-1 compliant monthly CSV exports for direct submission to the GST portal by company accountants.

### 4.4 Mobile-First Responsive Shell
- **Legacy Behavior:** `branchpanel` had hardcoded `table` tags and fixed layout wrappers causing severe horizontal scrolling and clipped UI on modern mobile devices.
- **Modern Platform ([`public/assets/css/google-theme.css`](file:///c:/Users/acer/Documents/kesari/public/assets/css/google-theme.css), [`src/Views/public/home.php`](file:///c:/Views/public/home.php)):**
  - Off-canvas slide-in navigation drawer with backdrop blur filter.
  - Multi-breakpoint responsive grid architecture ($1024\text{px}$, $900\text{px}$, $768\text{px}$, $680\text{px}$, $420\text{px}$).
  - Horizontally swipeable data tables (`.table-scroll-wrapper`) with touch momentum scrolling.
  - 44px minimum thumb tap targets for all buttons and interactive elements.

---

## 5. Security & Multi-Tenancy Architecture

```
                    +-------------------------------------+
                    |       Client Request (HTTPS)        |
                    +------------------+------------------+
                                       |
                                       v
                    +-------------------------------------+
                    |   TenantContext Middleware Engine   |
                    | (Resolves: Subdomain / Query / User)|
                    +------------------+------------------+
                                       |
              +------------------------+------------------------+
              |                                                 |
              v                                                 v
+-----------------------------+                   +-----------------------------+
|    Super Admin Tenant       |                   |    Branch Agency Tenant     |
|  - Platform Governance      |                   |  - Isolated Consignments    |
|  - Branch KYC Auditing      |                   |  - Agency Tariff Rules      |
|  - Destination Master Sync  |                   |  - Thermal Label Print Pool |
+-----------------------------+                   +-----------------------------+
```

1. **Strict Data Boundary:** Branch staff at Agency `#26` (Raipur) cannot view, edit, or search bookings belonging to other branches.
2. **Prepared SQL Statements:** All queries execute through parameter binding (`PDO::prepare()`), rendering SQL injection attacks impossible.
3. **Session Hardening:** Cookies enforced with `HttpOnly`, `SameSite=Lax`, and `session.save_path` virtualization.

---

## 6. Deployment Infrastructure

- **Source Code Repository:** [https://github.com/avneesh039/kesaricourier](https://github.com/avneesh039/kesaricourier)
- **Live Production URL:** [https://kesaricourier.vercel.app/](https://kesaricourier.vercel.app/)
- **Serverless Runtime:** `vercel-php@0.9.0` (PHP 8.4 Serverless Function Container)
- **Static Assets Delivery:** Global Vercel Edge Network CDN with instant asset caching.

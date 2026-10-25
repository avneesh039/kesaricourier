# Software Requirements Specification (SRS)
## Courier ERP Multi-Tenant SaaS Platform

---

## 1. Introduction

### 1.1 Purpose
This document specifies the software requirements for the **Courier ERP SaaS Platform**, an enterprise-grade, multi-tenant cloud management system designed for courier companies, logistics franchise operators, courier agencies, multi-location branches, delivery personnel, and corporate customers.

### 1.2 Scope of the System
The platform provides an end-to-end operational and financial suite that governs:
- Platform-wide governance and agency subscription lifecycle (Super Admin)
- Multi-branch logistics operations and client management (Agency Admin & Branch Manager)
- Rapid counter booking with volumetric weight computation and 4x6" thermal docket printing
- Real-time consignment status tracking with GS1 Code-128 barcode scanning
- Delivery runsheet management and mobile Proof of Delivery (POD) sign-off
- Indian GST B2B invoicing under SAC Code 996812 with CGST/SGST/IGST breakdown and GSTR-1 export
- Customer tracking portal and self-service account management

---

## 2. User Roles & Access Control Model (RBAC)

The system enforces 7 distinct user roles with server-side permission validation on every endpoint:

```
+---------------------------------------------------------------------------------+
|                                 USER ROLES HIERARCHY                            |
+-------------------+-------------------------------------------------------------+
| Super Admin       | Master platform governance, agency approvals, subscription  |
|                   | plans, global tariffs, destination master, system telemetry |
+-------------------+-------------------------------------------------------------+
| Agency Admin      | Agency tenant owner: branches, staff, corporate clients,    |
|                   | custom tariffs, financial invoices, subscription billing    |
+-------------------+-------------------------------------------------------------+
| Branch Manager    | Branch operational oversight: staff, bookings, dispatches,  |
|                   | branch runsheets, branch delivery performance, branch reports|
+-------------------+-------------------------------------------------------------+
| Operations Staff  | High-speed consignment booking, customer intake, thermal    |
|                   | label printing, bag manifests, barcode dispatch scanning    |
+-------------------+-------------------------------------------------------------+
| Delivery Personnel| Mobile-first interface: assigned runsheets, delivery attempts,|
|                   | recipient digital signature, photo POD capture, failure log |
+-------------------+-------------------------------------------------------------+
| Accounts Staff    | B2B tax invoice generation, payment receipts, outstanding   |
|                   | aging reports, bank reconciliation, GSTR-1 CSV exports      |
+-------------------+-------------------------------------------------------------+
| Customer / Client | Self-service tracking portal, booking history, PDF tax      |
|                   | invoices download, delivery status notification, tickets    |
+-------------------+-------------------------------------------------------------+
```

---

## 3. Detailed Functional Requirements

### 3.1 Module 1: Authentication & Identity Management
- **REQ-AUTH-01 (Dual Token System):** JWT-based authentication using short-lived Access Tokens (15 minutes) and cryptographically secure, rotating Refresh Tokens (7 days) stored in HTTP-only, secure, SameSite cookies.
- **REQ-AUTH-02 (Password Security):** Passwords hashed using bcrypt (cost factor 12) or Argon2id. Enforce minimum 8 characters with alphanumeric and special character complexity.
- **REQ-AUTH-03 (Agency Onboarding):** Public registration form capturing Legal Business Name, Trade Name, Owner Name, Email, Phone, Address, GSTIN, and PAN.
- **REQ-AUTH-04 (OTP Verification):** Email/SMS 6-digit OTP verification for agency onboarding and password reset workflows.
- **REQ-AUTH-05 (Staff Invitations):** Agency Admins and Branch Managers can invite staff members with pre-assigned roles and temporary credentials.
- **REQ-AUTH-06 (Account States):** Account state machine supporting `pending_approval`, `active`, `suspended`, `locked` (after 5 failed attempts), and `closed`.
- **REQ-AUTH-07 (Audit Logging):** Record every login attempt, IP address, user agent, timestamp, and security event.

### 3.2 Module 2: Super Admin Platform Governance
- **REQ-SADM-01 (Platform Telemetry Dashboard):** Real-time KPI cards for Total Agencies, Active Subscriptions, Expiring Subscriptions (within 30 days), Expired Subscriptions, Total Platform Bookings, Delivered vs Pending Shipments, and Platform Subscription Revenue.
- **REQ-SADM-02 (Agency Approval Pipeline):** Review pending registrations, inspect submitted GSTIN/PAN and verification documents, approve with unique Agency Code assignment (e.g. Code `26`), or reject with recorded reason.
- **REQ-SADM-03 (Subscription Engine):** Manage configurable subscription plans:
  - Plan Name, Description, Tier (Starter, Professional, Enterprise)
  - Price, Billing Frequency (Monthly, Quarterly, Half-Yearly, Yearly)
  - Feature limits: Max Branches, Max Users, Monthly Booking Quota, Max Storage
  - Trial Period Days, Grace Period Days, Auto-suspension switch
- **REQ-SADM-04 (Destination Master Governance):** Centralized control over:
  - Operational Zones (North, West, South, East, Central)
  - States with 2-digit Indian GST State Codes (e.g. CG=22, MH=27, DL=07)
  - Destination Cities with PIN codes, Delivery Hub flags, and Serviceability status
- **REQ-SADM-05 (Support & Routing Requests):** Ticketing desk for agency route addition requests, destination approvals, and technical support.

### 3.3 Module 3: Agency & Branch Operations
- **REQ-AGCY-01 (Multi-Branch Architecture):** Agency Admins can create and configure unlimited branches according to their subscription plan.
- **REQ-AGCY-02 (Branch Attributes):** Unique Branch Code (e.g., `RAI-01`), Branch Name, Operating Address, PIN code, Assigned Branch Manager, Phone, and Status (`active`, `inactive`).
- **REQ-AGCY-03 (Tenant Isolation):** All database operations enforce row-level `tenant_id` and optional `branch_id` scoping to prevent cross-tenant data leakage.
- **REQ-AGCY-04 (Corporate B2B Client Master):** Manage corporate clients with Client Code, Company Name, Contact Person, Phone, Email, Billing Address, GSTIN, PAN, and contractual Fuel Surcharge %.

### 3.4 Module 4: Consignment Booking & Calculation Engine
- **REQ-BOOK-01 (Rapid Booking Interface):** Keyboard-navigable counter booking form supporting Tab/Enter navigation, client auto-complete, destination lookup, and real-time rate preview.
- **REQ-BOOK-02 (Volumetric Weight Calculation):** Compare Dead Weight (kg) with Volumetric Weight (cm) using industry standards:
  $$\text{Air Volumetric Weight (kg)} = \frac{\text{Length} \times \text{Width} \times \text{Height (cm)}}{5000}$$
  $$\text{Surface Volumetric Weight (kg)} = \frac{\text{Length} \times \text{Width} \times \text{Height (cm)}}{4000}$$
  The system automatically selects the maximum value as the **Billable Weight**.
- **REQ-BOOK-03 (Multi-Slab Tariff Matrix):** Tariffs configured by Mode (Dox / Non-Dox), Service (Air / Surface), and Destination Zone.
  - Base weight slab rate (e.g. up to 0.25 kg = ₹40, up to 0.50 kg = ₹60)
  - Incremental weight steps and incremental rates (e.g. ₹30 per additional 0.5 kg)
  - Client-specific override tariffs or default agency general tariff.
- **REQ-BOOK-04 (AWB Numbering Scheme):** Guaranteed unique 14-digit GS1 consignment number formatted with agency prefix, date token, and atomic sequence.

### 3.5 Module 5: Operations, Barcode & Thermal Labeling
- **REQ-PRNT-01 (Thermal Shipping Docket):** Standard $100\text{mm} \times 150\text{mm}$ (4x6") label layout optimized for Zebra, TSC, TVS, and Rollo thermal printers:
  - Vector Code-128 scannable barcode
  - Quick-Response (QR) code resolving to public tracking URL
  - High-visibility Destination Hub Code and 6-digit PIN code badge
  - Consignor and Consignee details, piece count, billable weight, service type
- **REQ-PRNT-02 (Batch Docket Printing):** Print multiple dockets sequentially with automatic CSS `@media print` page breaks.
- **REQ-PRNT-03 (Bag Manifest Generation):** Create dispatch manifests aggregating consignments by destination hub, driver/flight vehicle number, dispatch timestamp, and total pieces/weight.

### 3.6 Module 6: Proof of Delivery (POD) & Delivery Run Sheets
- **REQ-POD-01 (Delivery Run Sheet):** Generate delivery run sheets assigning a bundle of consignments to specific delivery personnel.
- **REQ-POD-02 (Mobile Delivery Portal):** Mobile-first responsive touch interface for field delivery personnel:
  - View assigned deliveries sorted by destination locality
  - Mark status: `Delivered`, `Out for Delivery`, `Failed Attempt`
  - Record Non-Delivery Reason codes (Customer Unavailable, Address Incomplete, Consignee Shifted, Refused Delivery)
  - Capture Recipient Name, Relationship, and Digital Touch Signature
  - Optional photo attachment of delivery receipt / doorstep

### 3.7 Module 7: B2B Invoicing, GST & Accounts
- **REQ-BILL-01 (Logistics SAC 996812 Compliance):** Automatic tax computation for courier transportation under Service Accounting Code (SAC) 996812.
- **REQ-BILL-02 (Intra-State vs Inter-State Tax Determination):**
  - Intra-State (Origin and Destination in same state): 9% CGST + 9% SGST.
  - Inter-State (Origin and Destination in different states): 18% IGST.
  - Automatic extraction of state codes from first 2 digits of GSTINs.
- **REQ-BILL-03 (Batch Unbilled Invoicing):** Select date range and corporate client, aggregate all unbilled consignments, apply contractual fuel surcharge % and docket charges, compute GST, and generate GST Tax Invoice.
- **REQ-BILL-04 (Currency in Words & Round-off):** Convert grand total to Indian currency words format (Rupees ... Only) with mathematical round-off.
- **REQ-BILL-05 (Payment Receipts & Aging):** Record payments (Cash, Cheque, NEFT/RTGS, UPI), track partial payments and outstanding balances.
- **REQ-BILL-06 (GSTR-1 CSV Export):** One-click export formatted for direct upload to Indian GST Portal.

### 3.8 Module 8: Public Portal & Customer Self-Service
- **REQ-PUB-01 (Real-Time Docket Tracking):** Public tracking engine displaying 5-stage shipment progression (Booked -> Hub Dispatched -> In Transit -> Out For Delivery -> Delivered) with location timestamps.
- **REQ-PUB-02 (Rate Calculator):** Interactive public tariff calculator estimating costs based on origin, destination, weight, and dimensions.
- **REQ-PUB-03 (Agency Franchise Inquiry):** Partner onboarding inquiry form routed directly to Super Admin approval desk.

---

## 4. Non-Functional Requirements

### 4.1 Performance & Scalability
- **Sub-second API response time:** < 200ms for 95% of operational API requests.
- **High concurrency:** Support concurrent booking counter operations across hundreds of agencies and branches.
- **Database indexing:** B-tree indexes on `consignment_no`, `tenant_id`, `branch_id`, `client_id`, `status`, and `booking_date`.

### 4.2 Security & Compliance
- **Zero plain-text secrets:** Passwords, API keys, and sensitive tokens securely hashed or encrypted.
- **CORS & CSRF Defense:** Strict origin filtering and SameSite cookie policies.
- **Input Sanitization:** Zod schema validation on both frontend and backend for every incoming payload.
- **SQL Injection Prevention:** 100% prepared statement execution via Prisma ORM.

### 4.3 Reliability & Availability
- Production process supervision via PM2 cluster mode with automatic restart on failure.
- Graceful shutdown handling on SIGINT/SIGTERM.
- Automated daily database backup scripts.

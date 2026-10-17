# Roadmap: Kesari Express Courier & Logistics SaaS Platform

## Overview

This roadmap defines the end-to-end development journey for transforming the legacy PHP courier system into a premier, white-labeled Courier & Logistics SaaS platform built with Core PHP 8.2+ and Google Material Design 3. The roadmap begins with the core multi-tenancy foundation and Google-grade UI design system, moves into agency self-service onboarding and KYC verification, implements centralized destination governance and ticketing, delivers high-speed consignment booking and rate calculations, builds automated thermal barcode/QR label printing, and culminates in comprehensive GST billing, invoicing, and executive dashboards.

## Phases

- [x] **Phase 1: Core Foundation & Multi-Tenancy Architecture** - Setup modern Core PHP architecture, database schema, routing, session management, and Google Material Design 3 design system.
- [x] **Phase 2: Agency Onboarding, KYC & Super Admin Tenant Governance** - Self-service registration with email OTP, KYC document upload, admin approval pipeline, validity periods, and 30-day expiry notifications.
- [x] **Phase 3: Centralized Destination Governance & Agency Ticketing** - Super Admin master zones/states/cities/pincodes, read-only agency search, and internal ticketing with WhatsApp/Email hooks.
- [x] **Phase 4: Consignment Booking Engine & Tariff Matrix** - High-speed keyboard-navigable booking form, B2B client management, dead vs volumetric weight calculation, and multi-slab pricing.
- [x] **Phase 5: Thermal Label Barcode & QR Code Engine** - 4x6" thermal label PDF/print template with Code 128 barcode and dynamic tracking QR code.
- [x] **Phase 6: Invoicing, GST Billing, MIS Reporting & Dashboard Polish** - Batch unbilled consignment aggregation, fuel surcharge, GST tax invoice generation, bank account configuration, and Google-style analytics dashboards.

---

## Phase Details

### Phase 1: Core Foundation & Multi-Tenancy Architecture
**Goal**: Establish the modern Core PHP 8.2+ MVC/OOP architecture, database migration schema with tenant isolation, subdomain/custom-domain routing, and Google Material Design 3 design system.  
**Depends on**: Nothing (first phase)  
**Requirements**: UI-01, TENANT-01, TENANT-02  
**Success Criteria**:
  1. Multi-tenant router detects subdomain or custom domain and resolves agency tenant context automatically.
  2. Database layer uses secure PDO prepared statements with strict tenant filtering.
  3. Google Material Design 3 UI tokens, layout shells (Super Admin vs Agency Portal), responsive sidebar, and typography are rendered cleanly.
**Plans**: 3 plans

Plans:
- [ ] 01-01: Modern Core PHP project layout, config, database connection, and base multi-tenant schema.
- [ ] 01-02: Subdomain / host resolution middleware and tenant session authentication framework.
- [ ] 01-03: Google Material Design 3 CSS design system, typography, components, and layout shell.

---

### Phase 2: Agency Onboarding, KYC & Super Admin Tenant Governance
**Goal**: Implement self-service agency registration with OTP verification, KYC upload (PAN, Aadhaar, GSTIN), Super Admin verification queue, subscription validity tracking, and 30-day expiry banners.  
**Depends on**: Phase 1  
**Requirements**: ONBOARD-01, ONBOARD-02, ONBOARD-03, TENANT-03, TENANT-04  
**Success Criteria**:
  1. Agency owner can register, verify email OTP, and upload PAN/Aadhaar/business documents.
  2. Super Admin can review pending KYC submissions, view documents, and approve or reject applications.
  3. Approving an agency triggers account creation with validity period and auto-dispatches credentials.
  4. Agencies within 30 days of subscription expiry see warning banners with renewal options.
**Plans**: 3 plans

Plans:
- [ ] 02-01: Agency self-service signup wizard with email OTP verification flow.
- [ ] 02-02: KYC document submission portal and secure file storage (PAN, Aadhaar, GSTIN).
- [ ] 02-03: Super Admin tenant management portal (approvals, validity dates, active/suspend toggle, 30-day alerts).

---

### Phase 3: Centralized Destination Governance & Agency Ticketing
**Goal**: Centralize all delivery destinations (Zones, States, Cities, Pincodes) under Super Admin control to prevent duplicates, and provide agencies an internal ticketing system (plus WhatsApp/Email) to request new routes.  
**Depends on**: Phase 2  
**Requirements**: DEST-01, DEST-02, TICK-01, TICK-02, TICK-03  
**Success Criteria**:
  1. Super Admin can manage master zones, states, cities, and pincodes with bulk upload and search.
  2. Agency booking form features instant debounce autocomplete referencing only master destinations.
  3. Agency can submit a destination/support ticket or click prefilled WhatsApp/Email shortcuts.
  4. Super Admin can approve a destination ticket with 1 click, auto-adding it to master tables and notifying the agency.
**Plans**: 2 plans

Plans:
- [ ] 03-01: Super Admin master destination CRUD, hierarchical zones/states/cities/pincodes, and fast search API.
- [ ] 03-02: Agency support & destination request ticketing module with WhatsApp and Email quick actions.

---

### Phase 4: Consignment Booking Engine & Tariff Matrix
**Goal**: Deliver a rapid Google-grade consignment booking form with keyboard navigation, client rate matrices (Air/Surface, Dox/Non-Dox), and automatic volumetric weight calculation.  
**Depends on**: Phase 3  
**Requirements**: BOOK-01, BOOK-02, BOOK-03, BOOK-04, BOOK-05  
**Success Criteria**:
  1. Operator can enter a complete consignment in under 15 seconds using keyboard shortcuts (Tab/Enter).
  2. Selecting a client dynamically calculates freight based on weight slabs and destination zone.
  3. Entering package dimensions calculates volumetric weight and selects higher billable weight.
  4. Consignment listing shows real-time booking history, status chips, and date/client filters.
**Plans**: 3 plans

Plans:
- [ ] 04-01: Client master management and dynamic multi-tier rate slab configuration.
- [ ] 04-02: High-speed consignment booking UI with real-time rate calculator and volumetric weight logic.
- [ ] 04-03: Consignment management table, tracking status updates, and search/filtering.

---

### Phase 5: Thermal Label Barcode & QR Code Engine
**Goal**: Generate automated, standardized 4x6" thermal labels with Code 128 barcodes and dynamic QR codes for instant scanning and professional packaging.  
**Depends on**: Phase 4  
**Requirements**: PRINT-01, PRINT-02, PRINT-03  
**Success Criteria**:
  1. Booking confirmation provides a 1-click thermal label print preview formatted specifically for 4x6" printers.
  2. High-contrast Code 128 barcode is rendered cleanly and verified scannable.
  3. QR code encodes tracking URL and package metadata for rapid hub sorting.
**Plans**: 2 plans

Plans:
- [x] 05-01: Pure PHP Barcode (Code 128) and QR Code rendering engine (zero heavy external dependencies).
- [x] 05-02: Standard 4x6" thermal label layout, CSS print stylesheet, and single/batch print workflows.

---

### Phase 6: Invoicing, GST Billing, MIS Reporting & Dashboard Polish
**Goal**: Build the B2B monthly billing module, GST tax invoice generator with agency bank details, business intelligence reports, and executive Google-style dashboards.  
**Depends on**: Phase 5  
**Requirements**: BILL-01, BILL-02, BILL-03, BILL-04, UI-02  
**Success Criteria**:
  1. Agency can generate monthly GST invoices for clients by selecting unbilled consignments in date range.
  2. Invoices compute fuel surcharge, docket fees, and split CGST/SGST or IGST automatically.
  3. Printable GST Tax Invoice includes agency white-label logo, registered address, GSTIN, and bank account details.
  4. Super Admin and Agency Dashboards display modern Google-style KPI metrics and performance charts.
**Plans**: 3 plans

Plans:
- [x] 06-01: Agency bank account setup and unbilled consignment aggregation logic.
- [x] 06-02: GST calculation engine, tax invoice generator, and professional PDF/print layout.
- [x] 06-03: Analytics dashboard (KPI cards, booking trends, revenue breakdown, expiry tracking) and final system polish.

---

## Progress

**Execution Order:**
Phases execute in numeric order: 1 → 2 → 3 → 4 → 5 → 6

| Phase | Plans Complete | Status | Completed |
|---|---|---|---|
| 1. Core Foundation & Multi-Tenancy Architecture | 3/3 | Complete | 2026-10-15 |
| 2. Agency Onboarding, KYC & Super Admin Tenant Governance | 3/3 | Complete | 2026-10-15 |
| 3. Centralized Destination Governance & Agency Ticketing | 2/2 | Complete | 2026-10-15 |
| 4. Consignment Booking Engine & Tariff Matrix | 3/3 | Complete | 2026-10-16 |
| 5. Thermal Label Barcode & QR Code Engine | 2/2 | Complete | 2026-10-17 |
| 6. Invoicing, GST Billing, MIS Reporting & Dashboard Polish | 3/3 | Complete | 2026-10-17 |

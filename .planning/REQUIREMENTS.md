# Requirements: Kesari Express Courier & Logistics SaaS Platform

**Defined:** 2026-10-15  
**Core Value:** Empower courier agencies to operate autonomously with custom branding, rapid consignment booking, and automated GST billing, while centralizing destination governance and tenant subscription controls under the Super Admin.

## v1 Requirements

Requirements for initial release. Each maps directly to roadmap phases.

### Multi-Tenancy & White-Labeling (TENANT)

- [ ] **TENANT-01**: Agency portals route dynamically by subdomain (e.g. `agency1.domain.com`) or custom CNAME with strict row-level database tenant isolation.
- [ ] **TENANT-02**: White-label customization allows agencies to configure logo, brand accent color, agency legal name, contact info, and custom invoice header/footer.
- [ ] **TENANT-03**: Super Admin tenant management dashboard with tenant listing, status switches (Active / Suspended / Grace Period), and validity start/end dates.
- [ ] **TENANT-04**: Automated subscription expiration alert banner displayed to agency admins when account validity is within 30 days of expiry.

### Onboarding & KYC Verification (ONBOARD)

- [ ] **ONBOARD-01**: Public agency self-service signup page with email OTP verification.
- [ ] **ONBOARD-02**: Agency KYC submission portal collecting company trade details, GSTIN, PAN card document upload, and Aadhaar card document upload.
- [ ] **ONBOARD-03**: Super Admin KYC review queue with document viewer, approve action (auto-generating credentials and sending welcome email), and reject action with reason.

### Master Destinations & Ticketing (DEST & TICK)

- [ ] **DEST-01**: Centralized master destination hierarchy (Zone, State, City, Pincode) editable only by Super Admin.
- [ ] **DEST-02**: Read-only autocomplete destination search in agency booking forms to prevent duplicate destination creation.
- [ ] **TICK-01**: Internal agency ticketing interface to request new delivery destinations, pincodes, or general platform support.
- [ ] **TICK-02**: Quick-action support integrations enabling 1-click support initiation via WhatsApp API and Email ticket dispatch.
- [ ] **TICK-03**: Super Admin ticket resolution view with 1-click "Approve & Add to Master Destinations" workflow.

### Consignment Booking Engine (BOOK)

- [ ] **BOOK-01**: Google Workspace-styled rapid consignment booking form with full keyboard navigation (Tab/Enter).
- [ ] **BOOK-02**: Dynamic B2B client selection with autocomplete and auto-population of sender/client details.
- [ ] **BOOK-03**: Multi-slab tariff calculation engine supporting Air/Surface modes, Dox/Non-Dox, base weight slabs, and incremental weight rates.
- [ ] **BOOK-04**: Volumetric weight calculator comparing `(L × W × H) / 5000` against dead weight to determine billable weight.
- [ ] **BOOK-05**: Consignment list view with real-time status pills, date filters, client search, and edit/cancel actions.

### Thermal Label Printing (PRINT)

- [ ] **PRINT-01**: Direct thermal 4x6" label generator outputting clean PDF / print preview for AWB / Consignment dockets.
- [ ] **PRINT-02**: High-density Code 128 barcode rendering of AWB/CN number scannable by 1D/2D barcode scanners.
- [ ] **PRINT-03**: High-speed QR code rendering containing tracking URL, origin city, destination pincode, weight, and piece count.

### Billing & Invoicing (BILL)

- [ ] **BILL-01**: Bank account master for agencies to register bank details (Bank Name, Account No, IFSC, Branch) for invoice printing.
- [ ] **BILL-02**: Invoice generation module allowing agencies to select client, date range, and aggregate all unbilled consignments.
- [ ] **BILL-03**: Automatic computation of fuel surcharge %, docket charges, and tax split (CGST + SGST for intra-state or IGST for inter-state).
- [ ] **BILL-04**: Professional GST Tax Invoice PDF generation and print report with agency branding and payment instructions.

### Google-Grade UI/UX Design System (UI)

- [ ] **UI-01**: Google Material Design 3 design system with CSS custom properties, typography (Roboto/Inter/Google Sans style), surface elevation, and responsive layouts.
- [ ] **UI-02**: Interactive dashboard for Super Admin and Agencies featuring summary KPI cards (Today's Bookings, Revenue, Pending Deliveries, Expiry Status).

---

## v2 Requirements

Deferred to future releases:
- **CARRIER-01**: 3rd-party carrier API aggregation (FedEx, Delhivery, BlueDart webhook sync).
- **MOBILE-01**: Native Android app for delivery agents with offline digital proof of delivery (e-POD) signature capture.
- **AI-01**: AI-powered route optimization and automated dispatch planning.

---

## Out of Scope

| Feature | Reason |
|---------|--------|
| Public consumer marketplace | Platform is exclusively a B2B Courier & Agency Logistics SaaS. |
| Legacy Bootstrap 3 UI | Fully deprecated in favor of a modern Google Material Design 3 design system. |
| In-browser raw ESC/POS direct USB printing without driver | Standard 4x6" browser thermal print stylesheet is universal and printer-agnostic. |

---

## Traceability

| Requirement | Phase | Status |
|-------------|-------|--------|
| UI-01, TENANT-01, TENANT-02 | Phase 1: Core Foundation & Multi-Tenancy Architecture | Pending |
| ONBOARD-01, ONBOARD-02, ONBOARD-03, TENANT-03, TENANT-04 | Phase 2: Agency Onboarding, KYC & Super Admin Tenant Governance | Pending |
| DEST-01, DEST-02, TICK-01, TICK-02, TICK-03 | Phase 3: Centralized Destination Governance & Agency Ticketing | Pending |
| BOOK-01, BOOK-02, BOOK-03, BOOK-04, BOOK-05 | Phase 4: Consignment Booking Engine & Tariff Matrix | Pending |
| PRINT-01, PRINT-02, PRINT-03 | Phase 5: Thermal Label Barcode & QR Code Engine | Pending |
| BILL-01, BILL-02, BILL-03, BILL-04, UI-02 | Phase 6: Invoicing, GST Billing, MIS Reporting & Dashboard Polish | Pending |

**Coverage:**
- v1 requirements: 20 total
- Mapped to phases: 20
- Unmapped: 0 ✓

---
*Requirements defined: 2026-10-15*

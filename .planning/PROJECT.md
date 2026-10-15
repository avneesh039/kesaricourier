# Kesari Express Courier & Logistics SaaS Platform

## What This Is

A next-generation, white-labeled Courier & Logistics SaaS platform built with modern Core PHP and a Google Workspace/Material Design 3 aesthetic. It transforms traditional franchise and branch courier operations into independent, multi-tenant agency portals with self-service onboarding, KYC verification, centralized destination governance, automated barcode/QR thermal docket printing, and comprehensive B2B GST billing.

## Core Value

Empower courier agencies to operate autonomously with custom branding, rapid consignment booking, and automated GST billing, while centralizing destination governance and tenant subscription controls under the Super Admin.

## Business Context

- **Customer**: Independent courier agencies, franchisees, regional logistics operators, and the master platform owner (Super Admin).
- **Revenue model**: SaaS subscription licensing per agency (validity periods with 30-day renewal alerts) and master courier service operations.
- **Success metric**: 100% tenant isolation, sub-second CN/AWB booking speed, zero duplicate destination conflicts, and seamless thermal label generation.
- **Strategy notes**: Migrate from legacy unvalidated PHP scripts (`adminpanel` & `branchpanel`) to a secure, modern, multi-tenant Core PHP web application.

## Requirements

### Validated

(None yet — ship to validate)

### Active

- [ ] **TENANT-01**: Multi-tenancy architecture with independent agency portals routed via subdomains or custom domains.
- [ ] **TENANT-02**: White-label custom branding (agency logos, primary brand themes, custom headers/footers, and domain isolation).
- [ ] **TENANT-03**: Super Admin tenant management (approval queue, subscription start/end dates, instant deactivation switches, and automated 30-day expiry notifications).
- [ ] **ONBOARD-01**: Agency self-service registration with email OTP verification.
- [ ] **ONBOARD-02**: KYC document collection (Company registration, GSTIN, PAN, and Aadhaar card upload).
- [ ] **ONBOARD-03**: Super Admin KYC review queue with approval/rejection workflows and automated credential dispatch via email.
- [ ] **DEST-01**: Centralized master destination governance (Zones, States, Cities, Pincodes, Delivery Hubs) managed solely by Super Admin.
- [ ] **TICK-01**: In-app agency ticketing module for requesting new destinations, route additions, and support.
- [ ] **TICK-02**: Quick-action support integrations via direct WhatsApp API and Email ticket forwarding.
- [ ] **BOOK-01**: Fast, responsive Google Workspace-style consignment entry form with keyboard navigation and autocomplete.
- [ ] **BOOK-02**: Multi-slab tariff calculator (Dox/Non-Dox, Air/Surface, base weight + incremental weight rules).
- [ ] **PRINT-01**: Automated CN/AWB Barcode (Code 128) and QR code PDF generation tailored for 4x6" thermal label printers.
- [ ] **BILL-01**: B2B client invoicing engine with batch unbilled CN aggregation, fuel surcharge %, and CGST/SGST/IGST breakdown.
- [ ] **UI-01**: Modern Google Material Design 3 design system (Google Sans/Inter typography, clean slate surfaces, badge chips, micro-animations, and responsive data tables).

### Out of Scope

- Native iOS/Android apps (Web-first responsive mobile web app meets all field requirements for v1).
- Direct courier API carrier aggregation (e.g. FedEx/DHL public API integration deferred to v2).
- AI route optimization and vehicle telematics (future milestone).

## Context

- **Legacy System Audit**: Analyzed existing PHP panels at `keshriexpress.com/adminpanel` and `keshriexpress.com/branchpanel`.
- **Legacy Challenges**: Unsalted passwords, lack of input sanitization, non-isolated multi-branch data filtering in single tables, fragmented destination lists leading to operational chaos, and legacy Bootstrap 3 interfaces.
- **Modern Target**: Modern Core PHP (PHP 8.2+ with OOP structure, PDO prepared statements, CSRF protection, clean MVC routing, and responsive CSS with Google aesthetics).

## Constraints

- **Tech Stack**: Modern Core PHP 8.2+, MySQL / MariaDB (PDO), Vanilla CSS + modern JavaScript (No heavy runtime framework lock-in).
- **Design Standard**: Google Workspace / Material Design 3 aesthetic (clean, modern, minimalist, high contrast, accessible).
- **Multi-Tenancy**: Subdomain/custom-domain resolution with strict row-level `tenant_id` database isolation.
- **Printing**: Direct thermal printer compatibility (4x6 inch standard dockets and A4 multi-consignment slips).

## Key Decisions

| Decision | Rationale | Outcome |
|----------|-----------|---------|
| Core PHP 8.2+ Architecture | Specified by user for maximum deployment flexibility, speed, and clean low-overhead hosting. | — Pending |
| Subdomain / Custom Domain Routing | Provides authentic white-label SaaS experience for independent agencies (`agency.domain.com`). | — Pending |
| Centralized Destination Master | Prevents duplicate pincodes and spelling conflicts across multiple branches. | — Pending |
| Thermal Barcode & QR Code Engine | Eliminates manual handwriting on courier slips; speeds up dispatch and sorting. | — Pending |
| KYC Approval Gate | Ensures only verified logistics partners receive agency portal credentials. | — Pending |

## Evolution

This document evolves at phase transitions and milestone boundaries.

**After each phase transition** (via `/gsd-transition`):
1. Requirements invalidated? → Move to Out of Scope with reason
2. Requirements validated? → Move to Validated with phase reference
3. New requirements emerged? → Add to Active
4. Decisions to log? → Add to Key Decisions
5. "What This Is" still accurate? → Update if drifted

**After each milestone** (via `/gsd-complete-milestone`):
1. Full review of all sections
2. Core Value check — still the right priority?
3. Audit Out of Scope — reasons still valid?
4. Update Context with current state

---
*Last updated: 2026-10-15 after initialization*

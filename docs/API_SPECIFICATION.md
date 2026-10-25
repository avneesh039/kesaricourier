# REST API Specification
## Courier ERP Multi-Tenant SaaS Platform

---

## 1. Global Conventions & Standards

- **Base URL:** `/api/v1`
- **Content-Type:** `application/json`
- **Authentication:** `Authorization: Bearer <access_token>` header for protected endpoints.
- **Refresh Token:** Transmitted securely via `refreshToken` HTTP-only cookie.
- **Tenant Context:** Automatically inferred from JWT payload (`tenant_id`, `branch_id`).

### 1.1 Standard JSON Response Formats

#### Success Response
```json
{
  "success": true,
  "message": "Resource retrieved successfully",
  "data": { ... },
  "meta": {
    "page": 1,
    "limit": 20,
    "total": 150
  }
}
```

#### Error Response
```json
{
  "success": false,
  "error": {
    "code": "VALIDATION_FAILED",
    "message": "Invalid input parameters",
    "details": [
      { "field": "email", "issue": "Invalid email address format" }
    ]
  }
}
```

---

## 2. API Endpoints Catalog

### 2.1 Authentication & Profile (`/api/v1/auth`)

| Method | Endpoint | Access | Description |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/v1/auth/login` | Public | Authenticates user; returns Access Token & sets Refresh Cookie |
| `POST` | `/api/v1/auth/refresh-token`| Public | Rotates Refresh Token & returns new Access Token |
| `POST` | `/api/v1/auth/logout` | Authenticated | Revokes refresh token and clears session cookies |
| `GET` | `/api/v1/auth/me` | Authenticated | Returns currently authenticated user context and permissions |
| `POST` | `/api/v1/auth/register-agency`| Public | Self-service agency registration inquiry |
| `POST` | `/api/v1/auth/forgot-password`| Public | Initiates password reset OTP to user email |
| `POST` | `/api/v1/auth/reset-password` | Public | Validates OTP and updates password |

---

### 2.2 Super Admin Platform Management (`/api/v1/superadmin`)

| Method | Endpoint | Access | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/superadmin/dashboard` | Super Admin | Platform-wide KPI telemetry, active/expiring agencies, revenue |
| `GET` | `/api/v1/superadmin/agencies` | Super Admin | Paginated list of all agencies with status & search filters |
| `GET` | `/api/v1/superadmin/agencies/:id`| Super Admin | Complete agency dossier, branches, subscription, and KYC |
| `PATCH`| `/api/v1/superadmin/agencies/:id/status`| Super Admin | Approve (assigns agency code), reject, suspend, or reactivate |
| `GET` | `/api/v1/superadmin/plans` | Super Admin | List all SaaS subscription plans |
| `POST` | `/api/v1/superadmin/plans` | Super Admin | Create a new configurable subscription plan |
| `PUT` | `/api/v1/superadmin/plans/:id` | Super Admin | Update plan pricing, duration, quotas, and limits |
| `GET` | `/api/v1/superadmin/destinations` | Super Admin | Full destination master with zones and states |
| `POST` | `/api/v1/superadmin/destinations` | Super Admin | Add new master destination hub/city |
| `GET` | `/api/v1/superadmin/audit-logs` | Super Admin | Platform security and transactional audit trail |

---

### 2.3 Agency Governance & Branches (`/api/v1/agency`)

| Method | Endpoint | Access | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/agency/dashboard` | Agency Admin | Agency KPI summary: daily bookings, branch metrics, revenue |
| `GET` | `/api/v1/agency/profile` | Agency Admin | Agency profile, GSTIN, legal address, brand logo |
| `PUT` | `/api/v1/agency/profile` | Agency Admin | Update agency settings, trade name, and brand styling |
| `GET` | `/api/v1/agency/branches` | Agency Admin | List all active/inactive branches for tenant |
| `POST` | `/api/v1/agency/branches` | Agency Admin | Create new branch with unique code and address |
| `PUT` | `/api/v1/agency/branches/:id` | Agency Admin | Update branch operational parameters |
| `GET` | `/api/v1/agency/staff` | Agency Admin | List all employees across all branches |
| `POST` | `/api/v1/agency/staff` | Agency Admin | Invite new staff member with role assignment |
| `PATCH`| `/api/v1/agency/staff/:id/status`| Agency Admin | Toggle staff active/inactive state |

---

### 2.4 Clients & Commercial Tariffs (`/api/v1/clients`, `/api/v1/tariffs`)

| Method | Endpoint | Access | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/clients` | Staff+ | Search corporate B2B clients by name, code, or GSTIN |
| `POST` | `/api/v1/clients` | Staff+ | Register new corporate client account |
| `GET` | `/api/v1/clients/:id` | Staff+ | Client profile, contract rates, and unbilled bookings |
| `PUT` | `/api/v1/clients/:id` | Staff+ | Update client billing address, fuel surcharge %, contact |
| `GET` | `/api/v1/tariffs` | Staff+ | List agency default and client-specific tariff matrices |
| `POST` | `/api/v1/tariffs` | Agency Admin | Create tariff matrix with weight slabs per zone/mode |
| `POST` | `/api/v1/tariffs/calculate` | Staff+ | Real-time rate calculator comparing dead vs volumetric wt |

---

### 2.5 Consignments & Counter Bookings (`/api/v1/bookings`)

| Method | Endpoint | Access | Description |
| :--- | :--- | :--- | :--- |
| `POST` | `/api/v1/bookings` | Operations+ | Create new consignment booking with 14-digit AWB |
| `GET` | `/api/v1/bookings` | Staff+ | Filter bookings by AWB, client, branch, date, status |
| `GET` | `/api/v1/bookings/:awb` | Staff+ | Complete booking dossier, volumetric calculation, print status |
| `PATCH`| `/api/v1/bookings/:awb/status` | Staff+ | Update consignment status (In Transit, Delivered, etc.) |
| `DELETE`|`/api/v1/bookings/:awb` | Agency Admin | Cancel unbilled booking with audit reason |

---

### 2.6 Operations: Thermal Print, Manifests & Runsheets (`/api/v1/operations`)

| Method | Endpoint | Access | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/operations/print/single/:awb` | Staff+ | Renders 4x6" thermal label SVG with Code-128 & QR |
| `POST` | `/api/v1/operations/print/batch` | Staff+ | Renders multi-label batch print sheet |
| `POST` | `/api/v1/operations/manifests` | Operations+ | Creates dispatch manifest for destination hub |
| `GET` | `/api/v1/operations/manifests` | Operations+ | Lists dispatch manifests with consignment counts |
| `POST` | `/api/v1/operations/runsheets` | Operations+ | Creates delivery run sheet assigned to delivery staff |
| `GET` | `/api/v1/operations/runsheets` | Staff+ | Lists delivery run sheets and assignment statuses |

---

### 2.7 Field Execution & Proof of Delivery (`/api/v1/delivery`)

| Method | Endpoint | Access | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/delivery/my-runsheets` | Delivery+ | Lists run sheets assigned to the logged-in delivery person |
| `GET` | `/api/v1/delivery/runsheets/:id`| Delivery+ | Consignment drop list with recipient address & phones |
| `POST` | `/api/v1/delivery/pod` | Delivery+ | Submits POD sign-off (digital signature, recipient name, photo) |
| `POST` | `/api/v1/delivery/attempt-failed`| Delivery+ | Records non-delivery attempt with standardized reason code |

---

### 2.8 B2B Billing, GST & Accounts (`/api/v1/invoices`, `/api/v1/payments`)

| Method | Endpoint | Access | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/invoices/unbilled-consignments`| Accounts+ | Fetches unbilled bookings for a client within date range |
| `POST` | `/api/v1/invoices/generate` | Accounts+ | Generates GST SAC 996812 Tax Invoice with CGST/SGST/IGST |
| `GET` | `/api/v1/invoices` | Accounts+ | Lists invoices with payment statuses and aging |
| `GET` | `/api/v1/invoices/:id` | Accounts+ | Detailed invoice with line items, tax split, words, bank details |
| `POST` | `/api/v1/payments` | Accounts+ | Records payment receipt (Cash, Cheque, NEFT, UPI) |
| `GET` | `/api/v1/invoices/gstr1-export` | Accounts+ | Exports GSTR-1 compliant CSV file for GST portal |

---

### 2.9 Public Customer Portal (`/api/v1/public`)

| Method | Endpoint | Access | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/public/track/:awb` | Public | Real-time 5-stage shipment progression timeline |
| `POST` | `/api/v1/public/tariff-estimate` | Public | Estimates shipping costs based on dead/volumetric weight |
| `GET` | `/api/v1/public/destinations` | Public | Search serviceable pin codes and delivery hubs |
| `POST` | `/api/v1/public/partner-inquiry` | Public | Submits new courier agency franchise inquiry |

---

### 2.10 Support Desk, Requests & Audit Logs (`/api/v1/superadmin`, `/api/v1/agency`)

| Method | Endpoint | Access | Description |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1/superadmin/tickets` | Super Admin | Lists support and destination addition tickets across all agencies |
| `PATCH` | `/api/v1/superadmin/tickets/:id` | Super Admin | Updates ticket status, sets resolution notes, and records audit trail |
| `GET` | `/api/v1/superadmin/audit-logs` | Super Admin | Fetches immutable system compliance logs with keyword filters & paging |
| `GET` | `/api/v1/agency/tickets` | Agency Staff | Lists all tickets and request statuses submitted by the agency |
| `POST` | `/api/v1/agency/tickets` | Agency Staff | Submits new support ticket, destination addition request, or billing query |


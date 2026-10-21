# Codebase Architecture Map

## Architecture Overview
The Kesari Logistics SaaS platform is an enterprise multi-tenant logistics management and courier operational suite built with modern Core PHP (8.2+ OOP/PDO) and clean MVC separation.

```
kesaricourier-master/
├── api/
│   └── index.php              # Vercel Serverless Function entrypoint
├── config/
│   ├── app.php                # Environment, constants & session bootstrap
│   └── database.php           # PDO SQLite / MySQL configuration
├── database/
│   ├── kesari_saas.sqlite     # Portable pre-seeded SQLite database
│   └── schema.sql             # SQL schema definitions & initial seeds
├── public/
│   ├── assets/                # Static assets (CSS, JS, logos, icons)
│   └── index.php              # Primary front controller & HTTP route dispatch
├── src/
│   ├── Auth.php               # User authentication, roles & CSRF guards
│   ├── Database.php           # Singleton PDO wrapper with transaction support
│   ├── Icon.php               # Material Design SVG icons helper
│   ├── Router.php             # Lightweight high-performance URI pattern router
│   ├── TenantContext.php      # Subdomain/host & session multi-tenant isolation
│   ├── Controllers/           # Application controllers (Admin, Agency, Public, Bookings)
│   ├── Services/              # Domain services (OTP verification, KYC upload)
│   └── Views/                 # View templates & Google Material Design 3 layout shells
└── vercel.json                # Vercel serverless deployment routing config
```

## Multi-Tenancy Resolution
- **TenantContext**: Resolves active tenant from query parameter `?tenant=slug`, authenticated user session `$_SESSION['user']['tenant_id']`, or custom subdomain/domain lookup in `tenant_domains`.
- **Data Isolation**: All operational entities (consignments, clients, tariffs, invoices, bank accounts, tickets) are strictly segmented by `tenant_id`.

## Front Controller & Routing
- Handled in `public/index.php` via `Router.php`.
- Dispatches public routes (`/`, `/home`, `/track`, `/api/calculator`) to `PublicController`.
- Dispatches agency portal operations (`/bookings`, `/invoices`, `/tariffs`, `/clients`) to dedicated controllers.
- Dispatches Super Admin control plane (`/admin/agencies`, `/admin/kyc`, `/admin/destinations`, `/admin/tickets`) with strict role verification.

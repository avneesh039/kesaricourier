# Technology Stack

## Core Language & Runtime
- **PHP**: Core PHP 8.2+ (Object-Oriented, PDO Prepared Statements, Session Management)
- **Database**: SQLite (default local & Vercel `/tmp` replication) / MySQL / MariaDB (XAMPP compatibility)
- **Server Environment**: Vercel Serverless Functions (`vercel-php@0.9.0`) and Apache/Nginx (CLI / XAMPP)

## Frontend Technologies
- **Public Portal (New)**: Exact Shipdaak.com responsive design system (Bootstrap 5.3.3, Quicksand & Archivo Black Google Fonts, Font Awesome 5/4.7, Swiper, OwlCarousel, Slick Carousel)
- **Agency & Admin Portal**: Google Workspace & Material Design 3 design system (Google Sans/Inter typography, CSS custom properties, responsive sidebar drawer, glassmorphic headers)

## Key Integrations & APIs
- **Consignment Tracking**: Real-time cross-tenant AWB search & milestone timeline API (`/api/track`)
- **Tariff Engine**: Volumetric vs dead weight matrix calculator (`/api/tariffs/calculate`)
- **Thermal Label Printing**: Standard 4x6" docket printing with Code 128 barcode and QR verification
- **GST Invoicing**: CGST, SGST, IGST tax breakdown with SAC 996812 courier logistics code

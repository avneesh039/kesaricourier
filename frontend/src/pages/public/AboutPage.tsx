// frontend/src/pages/public/AboutPage.tsx
import React from 'react';
import { Link } from 'react-router-dom';
import { PublicNavbar } from '../../components/public/PublicNavbar';
import { PublicFooter } from '../../components/public/PublicFooter';

export const AboutPage: React.FC = () => {
  return (
    <div className="min-vh-100 d-flex flex-column bg-surface animate-enter">
      <PublicNavbar />

      {/* Hero Section */}
      <section className="py-5 bg-dark text-white position-relative overflow-hidden" style={{ background: 'linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%)' }}>
        <div className="container py-4 text-center text-lg-start position-relative" style={{ zIndex: 2 }}>
          <div className="row align-items-center g-4">
            <div className="col-12 col-lg-7">
              <span className="badge bg-warning text-dark text-uppercase px-3 py-1 mb-2 font-monospace">
                Our Story & Heritage
              </span>
              <h1 className="display-4 fw-extrabold mb-3">
                Built by Courier Operators, <span className="text-warning">For Courier Operators</span>
              </h1>
              <p className="lead text-white-50 mb-4" style={{ maxWidth: '620px' }}>
                Born from decades of ground-level logistics operations across Indian states. 
                We replaced clunky legacy systems and manual registers with a lightning-fast, 
                numpad-driven cloud ERP platform tailored to Indian business realities.
              </p>
              <div className="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3">
                <Link to="/register-agency" className="btn btn-primary px-4 fw-bold">
                  Join Our Agency Network
                </Link>
                <Link to="/services" className="btn btn-outline-light px-4">
                  View Logistics Services
                </Link>
              </div>
            </div>

            <div className="col-12 col-lg-5 text-center d-none d-lg-block">
              <img
                src="/manager_cutout.png"
                alt="Courier Leadership"
                className="img-fluid hero-cutout-img drop-shadow"
                style={{ maxHeight: '320px' }}
              />
            </div>
          </div>
        </div>
      </section>

      {/* Origin Story Section */}
      <section className="py-5 bg-white">
        <div className="container">
          <div className="row align-items-center g-5">
            <div className="col-12 col-lg-6">
              <img
                src="/corporate_client.jpg"
                alt="Corporate Logistics Operations"
                className="img-fluid rounded-4 shadow-md border"
              />
            </div>

            <div className="col-12 col-lg-6">
              <span className="text-primary text-uppercase fw-bold small">The Genesis</span>
              <h2 className="brand-title display-6 fw-bold mb-3">
                Solving the Counter Bottleneck
              </h2>
              <p className="text-muted leading-relaxed mb-3">
                In Indian domestic courier branches, 50 to 100 parcels are routinely booked in a single evening 
                for corporate clients like banks, law firms, and manufacturers. Using conventional mouse-heavy 
                interfaces creates frustrating bottlenecks and slow counter lines.
              </p>
              <p className="text-muted leading-relaxed mb-4">
                <strong>Our breakthrough insight (Image 4.46.07 PM):</strong> 
                <em> "Courier data entry is 95% numerical."</em> We rebuilt the counter experience from the ground up: 
                every field auto-advances on the numpad <code>Enter</code> key, and <strong>Sticky Field Locks</strong> 
                prevent clearing client codes, origin pins, and modes between sequential dockets.
              </p>

              <div className="row g-3">
                <div className="col-6">
                  <div className="p-3 bg-light rounded-3 border">
                    <h4 className="fw-bold text-primary mb-1">5 Sec</h4>
                    <div className="small text-muted">Average booking time per consignment</div>
                  </div>
                </div>
                <div className="col-6">
                  <div className="p-3 bg-light rounded-3 border">
                    <h4 className="fw-bold text-success mb-1">0 Mouse Clicks</h4>
                    <div className="small text-muted">Required during rapid counter dispatch</div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* The 4 Architectural Pillars */}
      <section className="py-5 bg-surface border-top border-bottom">
        <div className="container">
          <div className="text-center mb-5" style={{ maxWidth: '720px', margin: '0 auto' }}>
            <span className="badge bg-primary-subtle text-primary text-uppercase px-3 py-1 mb-2">
              Core Architecture
            </span>
            <h2 className="brand-title display-6 fw-bold">
              The Four Pillars of Kesari ERP
            </h2>
            <p className="text-muted">
              Designed to meet the stringent demands of high-throughput Indian logistics operations.
            </p>
          </div>

          <div className="row g-4">
            <div className="col-12 col-md-6 col-lg-3">
              <div className="app-card p-4 h-100 border text-center">
                <div className="stat-icon-wrapper bg-primary-subtle text-primary mx-auto mb-3">
                  <i className="bi bi-speedometer2"></i>
                </div>
                <h5 className="fw-bold mb-2">1. Ergonomic Speed</h5>
                <p className="text-muted small mb-0">
                  Full numpad optimization, sticky field locks, multi-box volumetric repeaters, 
                  and <code>Alt+S</code> quick save shortcuts.
                </p>
              </div>
            </div>

            <div className="col-12 col-md-6 col-lg-3">
              <div className="app-card p-4 h-100 border text-center">
                <div className="stat-icon-wrapper bg-success-subtle text-success mx-auto mb-3">
                  <i className="bi bi-shield-check"></i>
                </div>
                <h5 className="fw-bold mb-2">2. Strict Tenancy</h5>
                <p className="text-muted small mb-0">
                  True multi-tenant data isolation. Every agency, branch, customer, and invoice is 
                  cryptographically scoped to prevent data leaks.
                </p>
              </div>
            </div>

            <div className="col-12 col-md-6 col-lg-3">
              <div className="app-card p-4 h-100 border text-center">
                <div className="stat-icon-wrapper bg-warning-subtle text-warning mx-auto mb-3">
                  <i className="bi bi-receipt-cutoff"></i>
                </div>
                <h5 className="fw-bold mb-2">3. Indian Tax Harmony</h5>
                <p className="text-muted small mb-0">
                  SAC 996812 compliant taxation engine. Automatic CGST+SGST (9%+9%) vs IGST (18%) 
                  and GSTR-1 government CSV export.
                </p>
              </div>
            </div>

            <div className="col-12 col-md-6 col-lg-3">
              <div className="app-card p-4 h-100 border text-center">
                <div className="stat-icon-wrapper bg-info-subtle text-info mx-auto mb-3">
                  <i className="bi bi-diagram-3"></i>
                </div>
                <h5 className="fw-bold mb-2">4. Multi-Carrier APIs</h5>
                <p className="text-muted small mb-0">
                  Direct webhook and tracking connectors for Delhivery, Xpressbees, DTDC, GMS, 
                  Shree Maruti, and Allcargo.
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Nationwide Infrastructure & Zone Taxonomy */}
      <section className="py-5 bg-white">
        <div className="container">
          <div className="row align-items-center g-5">
            <div className="col-12 col-lg-6 order-2 order-lg-1">
              <span className="text-primary text-uppercase fw-bold small">Logistics Footprint</span>
              <h2 className="brand-title display-6 fw-bold mb-3">
                19,000+ PIN Codes Across 13 Operational Courier Zones
              </h2>
              <p className="text-muted leading-relaxed mb-4">
                Our Destination Master database maps every Indian postal code into standardized courier rating zones, 
                enabling zero-calculation rating when counter clerks enter the destination pin code:
              </p>

              <div className="row g-2 small mb-4">
                <div className="col-6"><span className="badge bg-light text-dark border p-2 w-100 text-start">📍 Within City (Local Metro)</span></div>
                <div className="col-6"><span className="badge bg-light text-dark border p-2 w-100 text-start">📍 Within State (Intra-State)</span></div>
                <div className="col-6"><span className="badge bg-light text-dark border p-2 w-100 text-start">📍 North Zone 1 & 2 (Delhi/NCR/Punjab)</span></div>
                <div className="col-6"><span className="badge bg-light text-dark border p-2 w-100 text-start">📍 West Zone 1 & 2 (MH/Gujarat)</span></div>
                <div className="col-6"><span className="badge bg-light text-dark border p-2 w-100 text-start">📍 South Zone 1 & 2 (BLR/Chennai/HYD)</span></div>
                <div className="col-6"><span className="badge bg-light text-dark border p-2 w-100 text-start">📍 East & North East (Kolkata/Assam)</span></div>
              </div>

              <Link to="/tariff-calculator" className="btn btn-primary px-4 fw-semibold">
                Try Our Interactive Tariff Estimator &rarr;
              </Link>
            </div>

            <div className="col-12 col-lg-6 order-1 order-lg-2">
              <img
                src="/hero_courier.jpg"
                alt="Nationwide Courier Fleet"
                className="img-fluid rounded-4 shadow-md border"
              />
            </div>
          </div>
        </div>
      </section>

      <PublicFooter />
    </div>
  );
};

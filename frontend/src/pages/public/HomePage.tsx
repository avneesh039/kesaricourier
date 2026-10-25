// frontend/src/pages/public/HomePage.tsx
import React, { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { PublicNavbar } from '../../components/public/PublicNavbar';
import { PublicFooter } from '../../components/public/PublicFooter';

export const HomePage: React.FC = () => {
  const [trackAwb, setTrackAwb] = useState('');
  const navigate = useNavigate();

  const handleTrackSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (trackAwb.trim()) {
      navigate(`/track/${encodeURIComponent(trackAwb.trim())}`);
    } else {
      navigate('/track');
    }
  };

  return (
    <div className="min-vh-100 d-flex flex-column bg-surface">
      <PublicNavbar />

      {/* Hero Section */}
      <section className="position-relative overflow-hidden pt-5 pb-5" style={{ background: 'linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #2563eb 100%)' }}>
        {/* Decorative background glow */}
        <div
          className="position-absolute top-0 end-0 rounded-circle opacity-20 pointer-events-none"
          style={{
            width: '600px',
            height: '600px',
            background: 'radial-gradient(circle, #60a5fa 0%, transparent 70%)',
            filter: 'blur(60px)',
            transform: 'translate(20%, -20%)',
          }}
        />

        <div className="container position-relative py-lg-4" style={{ zIndex: 2 }}>
          <div className="row align-items-center g-5">
            {/* Left Content */}
            <div className="col-12 col-lg-7 text-white animate-enter">
              <div className="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-white bg-opacity-10 border border-white border-opacity-20 mb-3 shadow-xs">
                <span className="badge bg-warning text-dark font-monospace fw-bold">NEW V2.5</span>
                <span className="small text-white-50">Handwritten Domain Specifications Calibrated</span>
              </div>

              <h1 className="display-4 fw-extrabold text-white lh-tight mb-3">
                India's Next-Gen <span className="text-warning">Courier ERP</span> & Logistics Operating System
              </h1>

              <p className="lead text-white-50 mb-4" style={{ maxWidth: '620px', fontSize: '1.15rem' }}>
                Engineered for Indian courier agencies, franchise counters, and regional hubs. 
                Experience <strong>95% numpad data entry</strong>, sticky field locks, 4x6" thermal labels, 
                Tally-style accounts, and automated <strong>SAC 996812 GST invoicing</strong>.
              </p>

              {/* Quick Tracking Widget on Hero */}
              <div className="p-3 bg-white rounded-3 shadow-lg mb-4" style={{ maxWidth: '560px' }}>
                <form onSubmit={handleTrackSubmit} className="d-flex flex-column flex-sm-row gap-2">
                  <div className="input-group">
                    <span className="input-group-text bg-white border-end-0 text-muted">
                      <i className="bi bi-upc-scan text-primary fs-5"></i>
                    </span>
                    <input
                      type="text"
                      className="form-control border-start-0 ps-1 font-monospace"
                      placeholder="Enter Consignment AWB # (e.g. KES-2026-001)"
                      value={trackAwb}
                      onChange={(e) => setTrackAwb(e.target.value)}
                    />
                  </div>
                  <button type="submit" className="btn btn-primary px-4 fw-bold text-nowrap">
                    <i className="bi bi-search me-1"></i> Track Live
                  </button>
                </form>
                <div className="d-flex justify-content-between align-items-center mt-2 px-1">
                  <span className="text-muted" style={{ fontSize: '0.75rem' }}>
                    Tracking over 6 national carriers via unified API
                  </span>
                  <Link to="/track" className="text-primary text-decoration-none fw-semibold" style={{ fontSize: '0.75rem' }}>
                    Advanced Lookup &rarr;
                  </Link>
                </div>
              </div>

              {/* Statistics Counters */}
              <div className="row g-3 pt-2 text-white text-center text-sm-start" style={{ maxWidth: '600px' }}>
                <div className="col-4">
                  <div className="fs-3 fw-bold text-warning font-monospace">19,000+</div>
                  <div className="small text-white-50">Pincodes Covered</div>
                </div>
                <div className="col-4 border-start border-white border-opacity-20 ps-3">
                  <div className="fs-3 fw-bold text-light font-monospace">5 Sec</div>
                  <div className="small text-white-50">Booking Speed</div>
                </div>
                <div className="col-4 border-start border-white border-opacity-20 ps-3">
                  <div className="fs-3 fw-bold text-success font-monospace">100%</div>
                  <div className="small text-white-50">GST SAC 996812</div>
                </div>
              </div>
            </div>

            {/* Right Graphics Image Cutout */}
            <div className="col-12 col-lg-5 text-center position-relative d-none d-lg-block">
              <div className="position-relative d-inline-block">
                <img
                  src="/van_cutout.png"
                  alt="Express Delivery Fleet"
                  className="img-fluid drop-shadow hero-cutout-img"
                  style={{ maxHeight: '340px' }}
                />

                {/* Floating Feature Glass Badges */}
                <div
                  className="position-absolute bottom-0 start-0 glass-panel p-3 text-start shadow-lg border animate-enter anim-delay-1"
                  style={{ transform: 'translate(-10%, 20%)', maxWidth: '240px' }}
                >
                  <div className="d-flex align-items-center gap-2 mb-1">
                    <span className="badge bg-success rounded-circle p-1">
                      <i className="bi bi-check text-white"></i>
                    </span>
                    <strong className="small text-dark">Sticky Locks</strong>
                  </div>
                  <div className="text-muted" style={{ fontSize: '0.74rem' }}>
                    Zero re-typing for repetitive corporate dispatches.
                  </div>
                </div>

                <div
                  className="position-absolute top-0 end-0 glass-panel p-3 text-start shadow-lg border animate-enter anim-delay-2"
                  style={{ transform: 'translate(10%, -15%)', maxWidth: '240px' }}
                >
                  <div className="d-flex align-items-center gap-2 mb-1">
                    <span className="badge bg-primary rounded-circle p-1">
                      <i className="bi bi-printer text-white"></i>
                    </span>
                    <strong className="small text-dark">4x6" Thermal Labels</strong>
                  </div>
                  <div className="text-muted" style={{ fontSize: '0.74rem' }}>
                    Pure vector Code-128 & QR code ready.
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Multi-Carrier National Integration Banner */}
      <section className="py-3 bg-light border-bottom">
        <div className="container">
          <div className="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3 text-center text-md-start">
            <span className="small text-muted fw-bold text-uppercase tracking-wider">
              <i className="bi bi-broadcast text-primary me-1"></i> Multi-Carrier Tracking Integrations:
            </span>
            <div className="d-flex flex-wrap align-items-center justify-content-center gap-3 gap-md-4">
              <span className="badge bg-white text-dark border px-3 py-2 shadow-xs fw-semibold">Delhivery Direct</span>
              <span className="badge bg-white text-dark border px-3 py-2 shadow-xs fw-semibold">Xpressbees Hub</span>
              <span className="badge bg-white text-dark border px-3 py-2 shadow-xs fw-semibold">GMS Worldwide</span>
              <span className="badge bg-white text-dark border px-3 py-2 shadow-xs fw-semibold">DTDC Express</span>
              <span className="badge bg-white text-dark border px-3 py-2 shadow-xs fw-semibold">Shree Maruti</span>
              <span className="badge bg-white text-dark border px-3 py-2 shadow-xs fw-semibold">Allcargo Gati</span>
            </div>
          </div>
        </div>
      </section>

      {/* Key Architectural Innovations (6 Domain Highlights) */}
      <section className="py-5 bg-white">
        <div className="container">
          <div className="text-center mb-5" style={{ maxWidth: '720px', margin: '0 auto' }}>
            <span className="badge bg-primary-subtle text-primary text-uppercase px-3 py-1 mb-2">
              Built for Counter Operations
            </span>
            <h2 className="brand-title display-6 fw-bold">
              Why Indian Courier Operators Love Kesari ERP
            </h2>
            <p className="text-muted">
              Standard generic ERPs slow down your counter clerks. Kesari is built around real-world 
              handwritten workflows, numpad speed, and automated tax calculations.
            </p>
          </div>

          <div className="row g-4">
            {/* Feature 1: Numpad Speed */}
            <div className="col-12 col-md-6 col-lg-4">
              <div className="app-card p-4 h-100 border text-start">
                <div className="d-flex align-items-center gap-3 mb-3">
                  <img
                    src="/custom_icons/numpad_speed.jpg"
                    alt="95% Numerical Speed"
                    width="64"
                    height="64"
                    className="rounded-3 shadow-xs border"
                  />
                  <div>
                    <span className="badge bg-primary-subtle text-primary small">Numpad Speed</span>
                    <h5 className="fw-bold mb-0 mt-1">95% Numerical Entry</h5>
                  </div>
                </div>
                <p className="text-muted small mb-0">
                  Every field auto-advances on <code>Enter</code>. Operators never need to touch the mouse 
                  during rapid counter bookings, boosting daily throughput by 300%.
                </p>
              </div>
            </div>

            {/* Feature 2: Sticky Locks */}
            <div className="col-12 col-md-6 col-lg-4">
              <div className="app-card p-4 h-100 border text-start">
                <div className="d-flex align-items-center gap-3 mb-3">
                  <img
                    src="/custom_icons/sticky_lock.jpg"
                    alt="Sticky Field Locks"
                    width="64"
                    height="64"
                    className="rounded-3 shadow-xs border"
                  />
                  <div>
                    <span className="badge bg-warning-subtle text-warning small">Ergonomics</span>
                    <h5 className="fw-bold mb-0 mt-1">Sticky Field Locks</h5>
                  </div>
                </div>
                <p className="text-muted small mb-0">
                  Lock repetitive fields (Date, Client Code, Origin, Mode, Consignee) with single-click 
                  checkboxes. Book 50 dockets for the same corporate sender in minutes.
                </p>
              </div>
            </div>

            {/* Feature 3: Volumetric Box */}
            <div className="col-12 col-md-6 col-lg-4">
              <div className="app-card p-4 h-100 border text-start">
                <div className="d-flex align-items-center gap-3 mb-3">
                  <img
                    src="/custom_icons/volumetric_box.jpg"
                    alt="Volumetric Box Repeater"
                    width="64"
                    height="64"
                    className="rounded-3 shadow-xs border"
                  />
                  <div>
                    <span className="badge bg-info-subtle text-info small">Air / Surface</span>
                    <h5 className="fw-bold mb-0 mt-1">Multi-Box Volumetric</h5>
                  </div>
                </div>
                <p className="text-muted small mb-0">
                  Box 1, Box 2... Box N repeater table with automatic mode divisors (Air: 5000, 
                  Fast Surface: 4500, Surface: 4000) and instant chargeable weight resolution.
                </p>
              </div>
            </div>

            {/* Feature 4: Tally Ledger */}
            <div className="col-12 col-md-6 col-lg-4">
              <div className="app-card p-4 h-100 border text-start">
                <div className="d-flex align-items-center gap-3 mb-3">
                  <img
                    src="/custom_icons/tally_ledger.jpg"
                    alt="Tally Bahi Khata"
                    width="64"
                    height="64"
                    className="rounded-3 shadow-xs border"
                  />
                  <div>
                    <span className="badge bg-success-subtle text-success small">Tally Harmony</span>
                    <h5 className="fw-bold mb-0 mt-1">Accounts & Ledgers (खाता)</h5>
                  </div>
                </div>
                <p className="text-muted small mb-0">
                  Double-entry accounting dashboard featuring Received (लेना), Payments (देना), 
                  customer & vendor running ledgers, and real-time net P&L statements.
                </p>
              </div>
            </div>

            {/* Feature 5: GST Invoice */}
            <div className="col-12 col-md-6 col-lg-4">
              <div className="app-card p-4 h-100 border text-start">
                <div className="d-flex align-items-center gap-3 mb-3">
                  <img
                    src="/custom_icons/gst_invoice.jpg"
                    alt="SAC 996812 Invoicing"
                    width="64"
                    height="64"
                    className="rounded-3 shadow-xs border"
                  />
                  <div>
                    <span className="badge bg-danger-subtle text-danger small">Tax Automation</span>
                    <h5 className="fw-bold mb-0 mt-1">SAC 996812 Invoicing</h5>
                  </div>
                </div>
                <p className="text-muted small mb-0">
                  Compliant logistics taxation engine. Automatically splits CGST+SGST (9%+9%) vs IGST (18%) 
                  from 2-digit GSTIN states with ready-to-file GSTR-1 CSV exports.
                </p>
              </div>
            </div>

            {/* Feature 6: Thermal Labeling */}
            <div className="col-12 col-md-6 col-lg-4">
              <div className="app-card p-4 h-100 border text-start">
                <div className="d-flex align-items-center gap-3 mb-3">
                  <img
                    src="/custom_icons/thermal_print.jpg"
                    alt="Industrial Thermal Printing"
                    width="64"
                    height="64"
                    className="rounded-3 shadow-xs border"
                  />
                  <div>
                    <span className="badge bg-secondary-subtle text-dark small">Hardware Ready</span>
                    <h5 className="fw-bold mb-0 mt-1">4x6" Thermal Labels</h5>
                  </div>
                </div>
                <p className="text-muted small mb-0">
                  Pure vector Code-128 barcode and dynamic tracking QR code rendering calibrated 
                  specifically for TSC, Zebra, TVS, and Citizen thermal printers.
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Interactive Operational Modules Showcase */}
      <section className="py-5 bg-surface border-top border-bottom">
        <div className="container">
          <div className="row align-items-center g-5">
            <div className="col-12 col-lg-6">
              <span className="badge bg-info-subtle text-info text-uppercase px-3 py-1 mb-2">
                Unified Logistics Hub
              </span>
              <h2 className="brand-title display-6 fw-bold mb-3">
                Complete Parcel Lifecycle from Inscan to Delivery Proof
              </h2>
              <p className="text-muted mb-4">
                Manage branch transfers with Line-haul Outscan Manifests, process incoming parcel sacks via high-speed 
                numerical Inscan grids, and hand off Delivery Run Sheets (DRS) with mobile touchscreen signatures.
              </p>

              <div className="d-flex flex-column gap-3">
                <div className="d-flex align-items-start gap-3">
                  <div className="p-2 bg-primary text-white rounded-circle">
                    <i className="bi bi-upc-scan fs-5"></i>
                  </div>
                  <div>
                    <h6 className="fw-bold mb-1">Hub Inscan & Forwarding Outscan</h6>
                    <p className="text-muted small mb-0">
                      Lock courier partner and received origin PIN for lightning-fast bag reconciliation.
                    </p>
                  </div>
                </div>

                <div className="d-flex align-items-start gap-3">
                  <div className="p-2 bg-success text-white rounded-circle">
                    <i className="bi bi-check2-circle fs-5"></i>
                  </div>
                  <div>
                    <h6 className="fw-bold mb-1">Dual-Mode POD & NDR Reconciliation</h6>
                    <p className="text-muted small mb-0">
                      Reconcile delivery run sheets in bulk or look up single dockets to record signature and reason codes.
                    </p>
                  </div>
                </div>

                <div className="d-flex align-items-start gap-3">
                  <div className="p-2 bg-warning text-dark rounded-circle">
                    <i className="bi bi-pencil-square fs-5"></i>
                  </div>
                  <div>
                    <h6 className="fw-bold mb-1">Post-Billing Line-Item Corrections</h6>
                    <p className="text-muted small mb-0">
                      Modify billed weights or rates on generated invoices with automatic tax recalculation.
                    </p>
                  </div>
                </div>
              </div>

              <div className="mt-4 pt-2">
                <Link to="/services" className="btn btn-outline-primary px-4 fw-semibold">
                  Explore All Logistics Services &rarr;
                </Link>
              </div>
            </div>

            <div className="col-12 col-lg-6">
              <div className="position-relative">
                <img
                  src="/freight_fleet.jpg"
                  alt="Freight Fleet & Logistics Hub"
                  className="img-fluid rounded-4 shadow-lg border"
                />
                <div className="position-absolute bottom-0 end-0 m-3 p-3 bg-white rounded-3 shadow-lg border d-none d-sm-block">
                  <div className="d-flex align-items-center gap-2">
                    <i className="bi bi-shield-check text-success fs-4"></i>
                    <div>
                      <div className="fw-bold small">Enterprise Multi-Tenant</div>
                      <div className="text-muted" style={{ fontSize: '0.72rem' }}>Strict tenant database isolation</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Subscription Plans Teaser */}
      <section className="py-5 bg-white">
        <div className="container">
          <div className="text-center mb-5" style={{ maxWidth: '720px', margin: '0 auto' }}>
            <span className="badge bg-warning text-dark text-uppercase px-3 py-1 mb-2 font-monospace">
              Transparent Pricing
            </span>
            <h2 className="brand-title display-6 fw-bold">
              Affordable SaaS Subscriptions for Agencies of Every Scale
            </h2>
            <p className="text-muted">
              From single-counter agencies to nationwide multi-branch franchise networks. 
              Zero setup fees. Choose your tier and start immediately.
            </p>
          </div>

          <div className="row g-4 justify-content-center">
            {/* Free Plan */}
            <div className="col-12 col-md-6 col-lg-3">
              <div className="app-card p-4 h-100 border text-center d-flex flex-column justify-content-between">
                <div>
                  <h5 className="fw-bold text-muted text-uppercase small">Starter Counter</h5>
                  <div className="fs-2 fw-extrabold text-dark my-2">₹0</div>
                  <div className="text-muted small mb-3">Free Forever</div>
                  <ul className="list-unstyled text-start small d-flex flex-column gap-2 mb-4">
                    <li><i className="bi bi-check-circle-fill text-success me-2"></i> 1 Operator User</li>
                    <li><i className="bi bi-check-circle-fill text-success me-2"></i> 1 Branch Counter</li>
                    <li><i className="bi bi-check-circle-fill text-success me-2"></i> 500 Dockets / Month</li>
                    <li><i className="bi bi-check-circle-fill text-success me-2"></i> Thermal 4x6" Labels</li>
                  </ul>
                </div>
                <Link to="/register-agency" className="btn btn-outline-secondary w-100 btn-sm">
                  Start Free
                </Link>
              </div>
            </div>

            {/* Monthly Plan */}
            <div className="col-12 col-md-6 col-lg-3">
              <div className="app-card p-4 h-100 border text-center d-flex flex-column justify-content-between">
                <div>
                  <h5 className="fw-bold text-primary text-uppercase small">Monthly Growth</h5>
                  <div className="fs-2 fw-extrabold text-primary my-2">₹200 <span className="fs-6 text-muted fw-normal">+GST/mo</span></div>
                  <div className="text-muted small mb-3">Billed Monthly</div>
                  <ul className="list-unstyled text-start small d-flex flex-column gap-2 mb-4">
                    <li><i className="bi bi-check-circle-fill text-success me-2"></i> 2 Operator Users</li>
                    <li><i className="bi bi-check-circle-fill text-success me-2"></i> 2 Branch Counters</li>
                    <li><i className="bi bi-check-circle-fill text-success me-2"></i> Unlimited Bookings</li>
                    <li><i className="bi bi-check-circle-fill text-success me-2"></i> Auto Zone Rate Master</li>
                  </ul>
                </div>
                <Link to="/plans" className="btn btn-outline-primary w-100 btn-sm">
                  View Monthly Tier
                </Link>
              </div>
            </div>

            {/* Yearly Plan (Featured) */}
            <div className="col-12 col-md-6 col-lg-3">
              <div className="app-card p-4 h-100 border border-primary shadow-md position-relative text-center d-flex flex-column justify-content-between bg-primary-subtle bg-opacity-25">
                <span className="position-absolute top-0 start-50 translate-middle badge bg-primary px-3 py-1">
                  MOST POPULAR
                </span>
                <div>
                  <h5 className="fw-bold text-dark text-uppercase small pt-2">Yearly Pro</h5>
                  <div className="fs-2 fw-extrabold text-dark my-2">₹2,124 <span className="fs-6 text-muted fw-normal">/year</span></div>
                  <div className="text-muted small mb-3">Includes 2 Months Free</div>
                  <ul className="list-unstyled text-start small d-flex flex-column gap-2 mb-4">
                    <li><i className="bi bi-check-circle-fill text-success me-2"></i> 10 Staff Users</li>
                    <li><i className="bi bi-check-circle-fill text-success me-2"></i> 10 Branch Hubs</li>
                    <li><i className="bi bi-check-circle-fill text-success me-2"></i> Tally-Style Accounts</li>
                    <li><i className="bi bi-check-circle-fill text-success me-2"></i> GSTR-1 CSV Exports</li>
                    <li><i className="bi bi-check-circle-fill text-success me-2"></i> 6 Courier Tracking APIs</li>
                  </ul>
                </div>
                <Link to="/register-agency" className="btn btn-primary w-100 btn-sm fw-bold">
                  Get Started Yearly
                </Link>
              </div>
            </div>

            {/* Enterprise Franchise */}
            <div className="col-12 col-md-6 col-lg-3">
              <div className="app-card p-4 h-100 border text-center d-flex flex-column justify-content-between">
                <div>
                  <h5 className="fw-bold text-muted text-uppercase small">Franchise Master</h5>
                  <div className="fs-2 fw-extrabold text-dark my-2">3 - 5 Yr</div>
                  <div className="text-muted small mb-3">Multi-City Franchise</div>
                  <ul className="list-unstyled text-start small d-flex flex-column gap-2 mb-4">
                    <li><i className="bi bi-check-circle-fill text-success me-2"></i> Unlimited Branches</li>
                    <li><i className="bi bi-check-circle-fill text-success me-2"></i> Unlimited Users</li>
                    <li><i className="bi bi-check-circle-fill text-success me-2"></i> Custom Domain White-label</li>
                    <li><i className="bi bi-check-circle-fill text-success me-2"></i> Dedicated 24/7 SLA</li>
                  </ul>
                </div>
                <Link to="/plans" className="btn btn-outline-dark w-100 btn-sm">
                  Explore Enterprise
                </Link>
              </div>
            </div>
          </div>

          <div className="text-center mt-4">
            <Link to="/plans" className="text-primary fw-bold text-decoration-none">
              Compare all subscription features in detail &rarr;
            </Link>
          </div>
        </div>
      </section>

      {/* Call to Action Banner */}
      <section className="py-5 bg-dark text-white position-relative overflow-hidden">
        <div className="container position-relative text-center py-4" style={{ zIndex: 2 }}>
          <h2 className="display-6 fw-bold mb-3">Ready to Modernize Your Courier Counters?</h2>
          <p className="text-secondary lead mb-4" style={{ maxWidth: '600px', margin: '0 auto' }}>
            Join over 1,000+ courier agencies already using Kesari ERP for 5-second docket entries and automated GST invoices.
          </p>
          <div className="d-flex flex-wrap justify-content-center gap-3">
            <Link to="/register-agency" className="btn btn-primary btn-lg px-4 shadow-sm fw-bold">
              <i className="bi bi-rocket-takeoff me-2"></i> Register Your Agency Online
            </Link>
            <Link to="/tariff-calculator" className="btn btn-outline-light btn-lg px-4">
              <i className="bi bi-calculator me-2"></i> Calculate Courier Rates
            </Link>
          </div>
        </div>
      </section>

      <PublicFooter />
    </div>
  );
};

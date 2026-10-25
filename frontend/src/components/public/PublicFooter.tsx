// frontend/src/components/public/PublicFooter.tsx
import React from 'react';
import { Link } from 'react-router-dom';

export const PublicFooter: React.FC = () => {
  return (
    <footer className="bg-dark text-white pt-5 pb-4 mt-5 border-top border-secondary">
      <div className="container">
        <div className="row g-4 mb-5">
          {/* Brand & About Column */}
          <div className="col-12 col-lg-4">
            <div className="d-flex align-items-center gap-2 mb-3">
              <img
                src="/kesari-favicon.svg"
                alt="Kesari Logo"
                width="36"
                height="36"
                className="rounded bg-white p-1"
              />
              <span className="brand-title fs-4 text-white fw-bold">KESARI COURIER ERP</span>
            </div>
            <p className="text-secondary small leading-relaxed mb-3">
              The premier SaaS operating system engineered specifically for Indian domestic courier agencies, 
              franchise operators, and regional logistics hubs. Built for 95% keyboard/numpad data entry with 
              real-time multi-carrier tracking and Indian GST SAC 996812 compliance.
            </p>
            <div className="d-flex align-items-center gap-2">
              <span className="badge bg-success-subtle text-success border border-success-subtle">
                <span className="status-dot pulse bg-success me-1"></span> All Systems Operational
              </span>
              <span className="badge bg-secondary text-light">99.98% SaaS Uptime</span>
            </div>
          </div>

          {/* Quick Links Column */}
          <div className="col-6 col-md-3 col-lg-2">
            <h6 className="text-uppercase fw-bold text-white small mb-3 tracking-wider">Platform</h6>
            <ul className="list-unstyled small mb-0 d-flex flex-column gap-2 text-secondary">
              <li><Link to="/" className="text-decoration-none text-secondary hover-text-white">Home</Link></li>
              <li><Link to="/about" className="text-decoration-none text-secondary hover-text-white">About Us</Link></li>
              <li><Link to="/services" className="text-decoration-none text-secondary hover-text-white">Logistics Services</Link></li>
              <li><Link to="/plans" className="text-decoration-none text-secondary hover-text-white">Subscription Plans</Link></li>
              <li><Link to="/track" className="text-decoration-none text-secondary hover-text-white">Consignment Tracking</Link></li>
              <li><Link to="/tariff-calculator" className="text-decoration-none text-secondary hover-text-white">Rate Estimator</Link></li>
            </ul>
          </div>

          {/* Logistics Core Modules Column */}
          <div className="col-6 col-md-3 col-lg-3">
            <h6 className="text-uppercase fw-bold text-white small mb-3 tracking-wider">Core Capabilities</h6>
            <ul className="list-unstyled small mb-0 d-flex flex-column gap-2 text-secondary">
              <li><span className="text-light">⚡ Rapid Numpad Booking</span></li>
              <li><span className="text-light">🔒 Sticky Field Locks</span></li>
              <li><span className="text-light">📦 Multi-Box Volumetric (Air/Surface)</span></li>
              <li><span className="text-light">🏷️ Pure Vector 4x6" Thermal Labels</span></li>
              <li><span className="text-light">📋 Line-haul Manifests & Runsheets</span></li>
              <li><span className="text-light">📊 Tally-Style Double Entry Accounts</span></li>
              <li><span className="text-light">🧾 GSTR-1 SAC 996812 Auto Invoicing</span></li>
            </ul>
          </div>

          {/* Contact & Support Column */}
          <div className="col-12 col-md-6 col-lg-3">
            <h6 className="text-uppercase fw-bold text-white small mb-3 tracking-wider">Franchise & Support</h6>
            <div className="d-flex flex-column gap-2 small text-secondary">
              <div className="d-flex align-items-center gap-2">
                <i className="bi bi-geo-alt text-primary"></i>
                <span>Regional Central Hub, Raipur, Chhattisgarh — 492001</span>
              </div>
              <div className="d-flex align-items-center gap-2">
                <i className="bi bi-telephone text-success"></i>
                <span>National Helpline: +91 98930 00000</span>
              </div>
              <div className="d-flex align-items-center gap-2">
                <i className="bi bi-envelope text-warning"></i>
                <span>Support: support@kesaricourier.in</span>
              </div>
              <div className="d-flex align-items-center gap-2">
                <i className="bi bi-clock text-info"></i>
                <span>Counter Hours: Mon - Sat: 08:00 AM - 10:30 PM</span>
              </div>
            </div>

            <div className="mt-3">
              <Link to="/register-agency" className="btn btn-outline-warning btn-sm w-100 fw-bold">
                <i className="bi bi-building-add me-1"></i> Register Your Agency Online
              </Link>
            </div>
          </div>
        </div>

        {/* Bottom Bar */}
        <div className="pt-3 border-top border-secondary d-flex flex-column flex-md-row justify-content-between align-items-center gap-2 small text-secondary">
          <div>
            &copy; {new Date().getFullYear()} Kesari Courier ERP SaaS Platform. All rights reserved.
          </div>
          <div className="d-flex gap-3">
            <Link to="/track" className="text-secondary text-decoration-none">Privacy Policy</Link>
            <Link to="/track" className="text-secondary text-decoration-none">Terms of Service</Link>
            <Link to="/track" className="text-secondary text-decoration-none">GST Compliance</Link>
          </div>
        </div>
      </div>
    </footer>
  );
};

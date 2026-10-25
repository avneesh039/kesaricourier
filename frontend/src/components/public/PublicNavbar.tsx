// frontend/src/components/public/PublicNavbar.tsx
import React, { useState } from 'react';
import { Link, NavLink } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';

export const PublicNavbar: React.FC = () => {
  const { user, isAuthenticated } = useAuth();
  const [isOpen, setIsOpen] = useState(false);

  const getDashboardLink = () => {
    if (!user) return '/login';
    if (user.role === 'superadmin') return '/superadmin';
    if (user.role === 'delivery_personnel') return '/delivery';
    if (user.role === 'customer') return '/customer';
    return '/agency';
  };

  return (
    <nav className="navbar navbar-expand-lg navbar-light bg-white border-bottom sticky-top py-2 shadow-sm" style={{ zIndex: 1050 }}>
      <div className="container">
        {/* Brand Logo */}
        <Link to="/" className="navbar-brand d-flex align-items-center gap-2 text-decoration-none">
          <img
            src="/kesari-favicon.svg"
            alt="Kesari Logo"
            width="38"
            height="38"
            className="rounded shadow-xs"
          />
          <div>
            <div className="brand-title fs-5 text-primary lh-1 fw-bold tracking-tight">KESARI</div>
            <div className="text-muted fw-semibold" style={{ fontSize: '0.68rem', letterSpacing: '0.05em' }}>
              COURIER ERP & LOGISTICS
            </div>
          </div>
        </Link>

        {/* Mobile Toggle Button */}
        <button
          className="navbar-toggler border-0 shadow-none"
          type="button"
          onClick={() => setIsOpen(!isOpen)}
          aria-label="Toggle navigation"
        >
          <span className="navbar-toggler-icon"></span>
        </button>

        {/* Navigation Links */}
        <div className={`collapse navbar-collapse ${isOpen ? 'show' : ''}`}>
          <ul className="navbar-nav mx-auto mb-2 mb-lg-0 gap-1 gap-lg-3 fw-semibold small">
            <li className="nav-item">
              <NavLink to="/" end className={({ isActive }) => `nav-link px-2 ${isActive ? 'text-primary fw-bold' : 'text-dark'}`} onClick={() => setIsOpen(false)}>
                Home
              </NavLink>
            </li>
            <li className="nav-item">
              <NavLink to="/about" className={({ isActive }) => `nav-link px-2 ${isActive ? 'text-primary fw-bold' : 'text-dark'}`} onClick={() => setIsOpen(false)}>
                About Us
              </NavLink>
            </li>
            <li className="nav-item">
              <NavLink to="/services" className={({ isActive }) => `nav-link px-2 ${isActive ? 'text-primary fw-bold' : 'text-dark'}`} onClick={() => setIsOpen(false)}>
                Logistics Services
              </NavLink>
            </li>
            <li className="nav-item">
              <NavLink to="/plans" className={({ isActive }) => `nav-link px-2 ${isActive ? 'text-primary fw-bold' : 'text-dark'}`} onClick={() => setIsOpen(false)}>
                <span className="badge bg-warning text-dark me-1" style={{ fontSize: '0.65rem' }}>PLANS</span>
                Subscription Pricing
              </NavLink>
            </li>
            <li className="nav-item">
              <NavLink to="/track" className={({ isActive }) => `nav-link px-2 ${isActive ? 'text-primary fw-bold' : 'text-dark'}`} onClick={() => setIsOpen(false)}>
                <i className="bi bi-search me-1 text-primary"></i> Live Tracking
              </NavLink>
            </li>
            <li className="nav-item">
              <NavLink to="/tariff-calculator" className={({ isActive }) => `nav-link px-2 ${isActive ? 'text-primary fw-bold' : 'text-dark'}`} onClick={() => setIsOpen(false)}>
                <i className="bi bi-calculator me-1 text-success"></i> Rate Estimator
              </NavLink>
            </li>
          </ul>

          {/* Action CTAs */}
          <div className="d-flex align-items-center gap-2 pt-2 pt-lg-0">
            {isAuthenticated && user ? (
              <Link to={getDashboardLink()} className="btn btn-primary btn-sm px-3 shadow-sm rounded-pill">
                <i className="bi bi-grid-1x2-fill me-1"></i> Dashboard ({user.name.split(' ')[0]})
              </Link>
            ) : (
              <>
                <Link to="/login" className="btn btn-outline-primary btn-sm px-3 rounded-pill">
                  <i className="bi bi-box-arrow-in-right me-1"></i> Login
                </Link>
                <Link to="/register-agency" className="btn btn-primary btn-sm px-3 shadow-sm rounded-pill fw-bold">
                  <i className="bi bi-lightning-charge-fill me-1"></i> Start Free Trial
                </Link>
              </>
            )}
          </div>
        </div>
      </div>
    </nav>
  );
};

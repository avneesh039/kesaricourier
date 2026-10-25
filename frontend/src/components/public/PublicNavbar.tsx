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
    <nav className="navbar navbar-expand-lg public-navbar sticky-top py-2 shadow-sm" style={{ zIndex: 1050 }}>
      <div className="container-fluid px-xl-5 px-lg-4 px-3">
        {/* Brand Logo - Guaranteed Single Line & Never Shrinks */}
        <Link to="/" className="navbar-brand d-flex align-items-center gap-2 text-decoration-none text-nowrap me-3 me-xl-4 flex-shrink-0">
          <img
            src="/kesari-favicon.svg"
            alt="Kesari Logo"
            width="36"
            height="36"
            className="rounded shadow-xs flex-shrink-0"
          />
          <div className="d-flex flex-column text-nowrap">
            <div className="brand-title fs-5 text-primary lh-1 fw-bold tracking-tight">KESARI</div>
            <div className="text-muted fw-semibold" style={{ fontSize: '0.64rem', letterSpacing: '0.06em' }}>
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

        {/* Navigation Links - Strict Single Line Enforcement */}
        <div className={`collapse navbar-collapse ${isOpen ? 'show' : ''}`}>
          <ul className="navbar-nav mx-auto mb-2 mb-lg-0 d-lg-flex flex-lg-row flex-lg-nowrap align-items-center gap-1 gap-xl-2 fw-semibold">
            <li className="nav-item text-nowrap">
              <NavLink
                to="/"
                end
                className={({ isActive }) => `nav-link text-nowrap ${isActive ? 'active' : 'text-dark'}`}
                onClick={() => setIsOpen(false)}
              >
                Home
              </NavLink>
            </li>

            <li className="nav-item text-nowrap">
              <NavLink
                to="/about"
                className={({ isActive }) => `nav-link text-nowrap ${isActive ? 'active' : 'text-dark'}`}
                onClick={() => setIsOpen(false)}
              >
                About Us
              </NavLink>
            </li>

            <li className="nav-item text-nowrap">
              <NavLink
                to="/services"
                className={({ isActive }) => `nav-link text-nowrap ${isActive ? 'active' : 'text-dark'}`}
                onClick={() => setIsOpen(false)}
              >
                Services
              </NavLink>
            </li>

            <li className="nav-item text-nowrap">
              <NavLink
                to="/plans"
                className={({ isActive }) => `nav-link text-nowrap ${isActive ? 'active' : 'text-dark'}`}
                onClick={() => setIsOpen(false)}
              >
                <span className="badge bg-warning text-dark me-1" style={{ fontSize: '0.62rem', padding: '0.25em 0.45em' }}>
                  PLANS
                </span>
                Subscription Pricing
              </NavLink>
            </li>

            <li className="nav-item text-nowrap">
              <NavLink
                to="/track"
                className={({ isActive }) => `nav-link text-nowrap ${isActive ? 'active' : 'text-dark'}`}
                onClick={() => setIsOpen(false)}
              >
                <i className="bi bi-search text-primary"></i>
                <span>Live Tracking</span>
              </NavLink>
            </li>

            <li className="nav-item text-nowrap">
              <NavLink
                to="/tariff-calculator"
                className={({ isActive }) => `nav-link text-nowrap ${isActive ? 'active' : 'text-dark'}`}
                onClick={() => setIsOpen(false)}
              >
                <i className="bi bi-calculator text-success"></i>
                <span>Rate Estimator</span>
              </NavLink>
            </li>
          </ul>

          {/* Action CTAs - Guaranteed Single Line & Never Wraps */}
          <div className="header-actions d-flex align-items-center gap-2 pt-2 pt-lg-0 ms-lg-3 flex-shrink-0 text-nowrap">
            {isAuthenticated && user ? (
              <Link to={getDashboardLink()} className="btn btn-primary btn-sm px-3 shadow-sm rounded-pill text-nowrap">
                <i className="bi bi-grid-1x2-fill me-1"></i> Dashboard ({user.name.split(' ')[0]})
              </Link>
            ) : (
              <>
                <Link to="/login" className="btn btn-outline-primary btn-sm px-3 rounded-pill text-nowrap fw-semibold">
                  <i className="bi bi-box-arrow-in-right me-1"></i> Login
                </Link>
                <Link to="/register-agency" className="btn btn-primary btn-sm px-3 shadow-sm rounded-pill fw-bold text-nowrap">
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
export default PublicNavbar;

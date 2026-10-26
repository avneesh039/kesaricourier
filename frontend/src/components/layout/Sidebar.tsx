// frontend/src/components/layout/Sidebar.tsx
import React from 'react';
import { NavLink } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';

interface SidebarProps {
  isOpen: boolean;
  onClose: () => void;
}

export const Sidebar: React.FC<SidebarProps> = ({ isOpen, onClose }) => {
  const { user, logout } = useAuth();
  const isSuperAdmin = user?.role === 'superadmin';
  const isDelivery = user?.role === 'delivery_personnel';
  const isCustomer = user?.role === 'customer';

  // Lock body scroll on mobile when sidebar drawer is open
  React.useEffect(() => {
    if (isOpen) {
      document.body.classList.add('no-scroll');
    } else {
      document.body.classList.remove('no-scroll');
    }
    return () => {
      document.body.classList.remove('no-scroll');
    };
  }, [isOpen]);

  return (
    <>
      {/* Mobile Glass Backdrop */}
      {isOpen && (
        <div
          className="sidebar-backdrop d-lg-none"
          onClick={onClose}
          aria-hidden="true"
        />
      )}

      <aside className={`app-sidebar ${isOpen ? 'show' : ''}`}>
        {/* Brand Header */}
        <div className="p-3 border-bottom d-flex align-items-center justify-content-between">
          <NavLink to="/" className="d-flex align-items-center gap-2 text-decoration-none">
            <img
              src="/kesari-favicon.svg"
              alt="Kesari Logo"
              width="36"
              height="36"
              className="rounded"
            />
            <div>
              <div className="brand-title fs-5 text-primary lh-1">KESARI</div>
              <div className="small text-muted" style={{ fontSize: '0.7rem' }}>
                COURIER ERP SAAS
              </div>
            </div>
          </NavLink>
          <button className="btn btn-sm btn-light d-lg-none" onClick={onClose}>
            <i className="bi bi-x-lg"></i>
          </button>
        </div>

        {/* Scrollable Navigation Area */}
        <div className="flex-grow-1 overflow-auto py-3">
          {/* SUPER ADMIN NAVIGATION */}
          {isSuperAdmin && (
            <div>
              <div className="px-4 text-uppercase small text-muted fw-bold mb-2" style={{ fontSize: '0.72rem' }}>
                Platform Governance
              </div>
              <NavLink to="/superadmin" end className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-speedometer2"></i> Platform Dashboard
              </NavLink>
              <NavLink to="/superadmin/agencies" className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-buildings"></i> Agency Network
              </NavLink>
              <NavLink to="/superadmin/plans" className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-cash-stack"></i> Subscription Plans
              </NavLink>
              <NavLink to="/superadmin/destinations" className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-geo-alt"></i> Destination Master
              </NavLink>
              <NavLink to="/superadmin/tickets" className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-headset"></i> Support Tickets Desk
              </NavLink>
              <NavLink to="/superadmin/courier-apis" className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-cpu"></i> Courier API Gateway
              </NavLink>
              <NavLink to="/superadmin/audit-logs" className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-shield-check"></i> Audit Logs
              </NavLink>
            </div>
          )}

          {/* CUSTOMER PORTAL NAVIGATION */}
          {isCustomer && (
            <div>
              <div className="px-4 text-uppercase small text-muted fw-bold mb-2" style={{ fontSize: '0.72rem' }}>
                Customer Portal
              </div>
              <NavLink to="/customer" end className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-box-seam"></i> My Consignments
              </NavLink>
              <NavLink to="/track" className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-search"></i> Live Tracking
              </NavLink>
              <NavLink to="/tariff-calculator" className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-calculator"></i> Rate Estimator
              </NavLink>
            </div>
          )}

          {/* AGENCY & OPERATIONS NAVIGATION */}
          {!isSuperAdmin && !isDelivery && !isCustomer && (
            <div>
              <div className="px-4 text-uppercase small text-muted fw-bold mb-2" style={{ fontSize: '0.72rem' }}>
                Operations & Dockets
              </div>
              <NavLink to="/agency" end className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-grid-1x2"></i> Agency Dashboard
              </NavLink>
              <NavLink to="/bookings/new" className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-plus-circle text-primary"></i> Rapid Booking (Sticky)
              </NavLink>
              <NavLink to="/bookings" end className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-box-seam"></i> Consignments
              </NavLink>
              <NavLink to="/operations/inscan" className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-upc-scan text-success"></i> Inscan (Incoming Hub)
              </NavLink>
              <NavLink to="/operations/print" className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-printer"></i> Thermal Labels (4x6")
              </NavLink>
              <NavLink to="/operations/manifests" className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-truck"></i> Line-haul Manifests
              </NavLink>
              <NavLink to="/operations/runsheets" className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-clipboard-check"></i> Delivery Runsheets
              </NavLink>
              <NavLink to="/operations/pod-entry" className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-check2-circle text-info"></i> POD / NDR Entry
              </NavLink>

              <div className="px-4 text-uppercase small text-muted fw-bold mt-3 mb-2" style={{ fontSize: '0.72rem' }}>
                Quotations & Accounts
              </div>
              <NavLink to="/quotations" className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-file-earmark-spreadsheet text-warning"></i> Pre-Sales Quotations
              </NavLink>
              <NavLink to="/accounts" className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-journal-bookmark text-primary"></i> Accounts & Ledgers (खाता)
              </NavLink>
              <NavLink to="/invoices" className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-receipt"></i> GST Invoices & GSTR-1
              </NavLink>
              <NavLink to="/clients" className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-people"></i> Corporate Clients
              </NavLink>

              <div className="px-4 text-uppercase small text-muted fw-bold mt-3 mb-2" style={{ fontSize: '0.72rem' }}>
                Administration
              </div>
              <NavLink to="/branches" className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-diagram-3"></i> Branches
              </NavLink>
              <NavLink to="/staff" className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-person-badge"></i> Staff & Roles
              </NavLink>
              <NavLink to="/tariffs" className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-table"></i> Tariff Matrix
              </NavLink>
              <NavLink to="/agency/tickets" className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-question-circle"></i> Support & Requests
              </NavLink>
              <NavLink to="/agency/settings" className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-gear"></i> Agency Settings
              </NavLink>
            </div>
          )}

          {/* DELIVERY PERSONNEL NAVIGATION */}
          {isDelivery && (
            <div>
              <div className="px-4 text-uppercase small text-muted fw-bold mb-2" style={{ fontSize: '0.72rem' }}>
                Field Deliveries
              </div>
              <NavLink to="/delivery" className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-bicycle"></i> My Runsheets & POD
              </NavLink>
            </div>
          )}

          {/* PUBLIC TOOLS */}
          {!isCustomer && (
            <>
              <div className="px-4 text-uppercase small text-muted fw-bold mt-3 mb-2" style={{ fontSize: '0.72rem' }}>
                Public Tools
              </div>
              <NavLink to="/track" className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-search"></i> Track Consignment
              </NavLink>
              <NavLink to="/tariff-calculator" className={({ isActive }) => `sidebar-nav-item ${isActive ? 'active' : ''}`} onClick={onClose}>
                <i className="bi bi-calculator"></i> Rate Estimator
              </NavLink>
            </>
          )}
        </div>

        {/* Footer info */}
        <div className="p-3 border-top bg-light">
          <button className="btn btn-outline-danger btn-sm w-100 d-flex align-items-center justify-content-center gap-2" onClick={logout}>
            <i className="bi bi-box-arrow-right"></i> Sign Out
          </button>
        </div>
      </aside>
    </>
  );
};

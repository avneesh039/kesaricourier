// frontend/src/components/layout/Navbar.tsx
import React from 'react';
import { useAuth } from '../../context/AuthContext';

interface NavbarProps {
  onToggleSidebar: () => void;
}

export const Navbar: React.FC<NavbarProps> = ({ onToggleSidebar }) => {
  const { user, logout } = useAuth();
  const [showDropdown, setShowDropdown] = React.useState(false);
  const dropdownRef = React.useRef<HTMLDivElement>(null);

  React.useEffect(() => {
    const handleClickOutside = (event: MouseEvent) => {
      if (dropdownRef.current && !dropdownRef.current.contains(event.target as Node)) {
        setShowDropdown(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  const getRoleBadge = (role?: string) => {
    switch (role) {
      case 'superadmin':
        return <span className="badge bg-danger">Super Admin</span>;
      case 'agency_admin':
        return <span className="badge bg-primary">Agency Admin</span>;
      case 'branch_manager':
        return <span className="badge bg-info text-dark">Branch Manager</span>;
      case 'operations_staff':
        return <span className="badge bg-success">Operations Staff</span>;
      case 'delivery_personnel':
        return <span className="badge bg-warning text-dark">Delivery Staff</span>;
      case 'accounts_staff':
        return <span className="badge bg-secondary">Accounts Staff</span>;
      default:
        return <span className="badge bg-light text-dark">User</span>;
    }
  };

  return (
    <nav className="navbar navbar-expand app-topbar px-3 px-lg-4 d-flex justify-content-between align-items-center">
      <div className="d-flex align-items-center gap-2 gap-md-3">
        <button
          className="btn btn-sm btn-light border d-lg-none p-2 shadow-xs"
          onClick={onToggleSidebar}
          aria-label="Toggle Sidebar"
        >
          <i className="bi bi-list fs-5"></i>
        </button>

        {user?.tenantName && (
          <div className="d-flex align-items-center gap-2">
            <span
              className="badge py-2 px-3 fw-semibold text-white d-none d-sm-inline-block shadow-xs"
              style={{ backgroundColor: user.brandColor || '#2563eb' }}
            >
              Agency #{user.agencyCode || '26'}
            </span>
            <span className="fw-bold text-dark text-truncate" style={{ maxWidth: '240px' }}>
              {user.tradeName || user.tenantName}
            </span>
            {user.branchName && (
              <span className="badge bg-light text-secondary border d-none d-md-inline-block">
                {user.branchName}
              </span>
            )}
          </div>
        )}

        {user?.role === 'superadmin' && (
          <div className="d-flex align-items-center gap-2">
            <span className="badge bg-dark py-2 px-3 shadow-xs">MASTER PLATFORM</span>
            <span className="fw-semibold text-secondary d-none d-sm-inline">
              Kesari SaaS Control Center
            </span>
          </div>
        )}
      </div>

      <div className="d-flex align-items-center gap-3">
        <div className="text-end d-none d-sm-block">
          <div className="fw-bold text-dark small">{user?.name}</div>
          <div>{getRoleBadge(user?.role)}</div>
        </div>

        <div className="position-relative" ref={dropdownRef}>
          <button
            className="btn btn-light rounded-circle p-0 d-flex align-items-center justify-content-center border shadow-xs"
            style={{ width: '42px', height: '42px' }}
            onClick={() => setShowDropdown(!showDropdown)}
            aria-expanded={showDropdown}
            aria-label="User profile menu"
          >
            <i className="bi bi-person-circle fs-5 text-primary"></i>
          </button>

          {showDropdown && (
            <ul
              className="dropdown-menu dropdown-menu-end shadow-lg show position-absolute end-0 mt-2 py-0 border-0"
              style={{ minWidth: '220px', zIndex: 1050 }}
            >
              <li className="p-3 bg-light rounded-top border-bottom">
                <div className="fw-bold text-dark">{user?.name}</div>
                <div className="text-muted small text-truncate">{user?.email}</div>
                <div className="mt-1">{getRoleBadge(user?.role)}</div>
              </li>
              <li>
                <a
                  className="dropdown-item py-2 d-flex align-items-center gap-2"
                  href="/track"
                  onClick={() => setShowDropdown(false)}
                >
                  <i className="bi bi-search text-primary"></i> Track Consignment
                </a>
              </li>
              <li>
                <a
                  className="dropdown-item py-2 d-flex align-items-center gap-2"
                  href="/tariff-calculator"
                  onClick={() => setShowDropdown(false)}
                >
                  <i className="bi bi-calculator text-success"></i> Rate Estimator
                </a>
              </li>
              <li>
                <hr className="dropdown-divider my-0" />
              </li>
              <li>
                <button
                  className="dropdown-item py-2 text-danger d-flex align-items-center gap-2"
                  onClick={() => {
                    setShowDropdown(false);
                    logout();
                  }}
                >
                  <i className="bi bi-box-arrow-right"></i> Sign Out
                </button>
              </li>
            </ul>
          )}
        </div>
      </div>
    </nav>
  );
};

// frontend/src/pages/auth/Login.tsx
import React, { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';

export const Login: React.FC = () => {
  const [email, setEmail] = useState('admin@kesari.com');
  const [password, setPassword] = useState('admin123');
  const [tenantSlug, setTenantSlug] = useState('');
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  const { login } = useAuth();
  const navigate = useNavigate();

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setError(null);
    setLoading(true);

    try {
      const user = await login(email, password, tenantSlug || undefined);
      // Route based on role
      if (user.role === 'superadmin') {
        navigate('/superadmin');
      } else if (user.role === 'delivery_personnel') {
        navigate('/delivery');
      } else if (user.role === 'customer') {
        navigate('/customer');
      } else {
        navigate('/agency');
      }
    } catch (err: any) {
      setError(
        err.response?.data?.error?.message ||
          'Failed to sign in. Please verify your credentials.'
      );
    } finally {
      setLoading(false);
    }
  };

  const setPreset = (em: string, pw: string, slug: string = '') => {
    setEmail(em);
    setPassword(pw);
    setTenantSlug(slug);
    setError(null);
  };

  return (
    <div className="min-vh-100 d-flex flex-column justify-content-center bg-light py-4 py-md-5">
      <div className="container">
        <div className="row justify-content-center">
          <div className="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">
            <div className="mb-2 text-start">
              <Link to="/" className="text-decoration-none text-muted small d-inline-flex align-items-center">
                <i className="bi bi-arrow-left me-1"></i> Back to Homepage
              </Link>
            </div>
            <div className="app-card p-3 p-sm-4 p-md-5 shadow-sm border-0">
              {/* Brand Header */}
              <div className="text-center mb-4">
                <Link to="/" className="text-decoration-none">
                  <img
                    src="/kesari-favicon.svg"
                    alt="Kesari Logo"
                    width="54"
                    height="54"
                    className="rounded mb-2"
                  />
                  <h2 className="brand-title text-primary mb-1">Kesari Courier ERP</h2>
                </Link>
                <p className="text-muted small">
                  Enterprise Multi-Tenant Logistics SaaS Platform
                </p>
              </div>

              {error && (
                <div className="alert alert-danger py-2 small d-flex align-items-center gap-2">
                  <i className="bi bi-exclamation-triangle-fill"></i>
                  <span>{error}</span>
                </div>
              )}

              <form onSubmit={handleSubmit}>
                <div className="mb-3">
                  <label className="form-label small fw-semibold">Email Address</label>
                  <div className="input-group">
                    <span className="input-group-text bg-light text-muted">
                      <i className="bi bi-envelope"></i>
                    </span>
                    <input
                      type="email"
                      className="form-control"
                      value={email}
                      onChange={(e) => setEmail(e.target.value)}
                      placeholder="name@agency.com"
                      required
                    />
                  </div>
                </div>

                <div className="mb-3">
                  <label className="form-label small fw-semibold">Password</label>
                  <div className="input-group">
                    <span className="input-group-text bg-light text-muted">
                      <i className="bi bi-lock"></i>
                    </span>
                    <input
                      type="password"
                      className="form-control"
                      value={password}
                      onChange={(e) => setPassword(e.target.value)}
                      placeholder="••••••••"
                      required
                    />
                  </div>
                </div>

                <div className="mb-4">
                  <label className="form-label small fw-semibold">
                    Agency Portal Slug <span className="text-muted fw-normal">(Optional)</span>
                  </label>
                  <input
                    type="text"
                    className="form-control form-control-sm"
                    value={tenantSlug}
                    onChange={(e) => setTenantSlug(e.target.value)}
                    placeholder="e.g. raipur"
                  />
                  <div className="form-text small">
                    Leave blank for direct account routing or Super Admin.
                  </div>
                </div>

                <button
                  type="submit"
                  className="btn btn-primary w-100 py-2 fw-semibold"
                  disabled={loading}
                >
                  {loading ? (
                    <>
                      <span className="spinner-border spinner-border-sm me-2"></span>
                      Authenticating...
                    </>
                  ) : (
                    <>
                      <i className="bi bi-box-arrow-in-right me-2"></i> Sign In to Portal
                    </>
                  )}
                </button>
              </form>

              {/* Demo Account Quick Switcher */}
              <div className="mt-4 pt-3 border-top">
                <div className="small fw-bold text-muted text-uppercase mb-2 text-center" style={{ fontSize: '0.75rem' }}>
                  ⚡ Quick Demo Credentials
                </div>
                <div className="d-flex flex-wrap gap-2 justify-content-center">
                  <button
                    type="button"
                    className="btn btn-sm btn-outline-danger"
                    onClick={() => setPreset('admin@kesari.com', 'admin123')}
                  >
                    Super Admin
                  </button>
                  <button
                    type="button"
                    className="btn btn-sm btn-outline-primary"
                    onClick={() => setPreset('shreemaruti@raipur.com', 'india3101', 'raipur')}
                  >
                    Raipur Agency Admin
                  </button>
                  <button
                    type="button"
                    className="btn btn-sm btn-outline-info"
                    onClick={() => setPreset('manager.raipur@kesari.com', 'manager123', 'raipur')}
                  >
                    Branch Manager
                  </button>
                  <button
                    type="button"
                    className="btn btn-sm btn-outline-success"
                    onClick={() => setPreset('ops.raipur@kesari.com', 'ops123', 'raipur')}
                  >
                    Counter Ops
                  </button>
                  <button
                    type="button"
                    className="btn btn-sm btn-outline-warning text-dark"
                    onClick={() => setPreset('delivery.raipur@kesari.com', 'delivery123', 'raipur')}
                  >
                    Delivery Driver
                  </button>
                  <button
                    type="button"
                    className="btn btn-sm btn-outline-secondary"
                    onClick={() => setPreset('accounts.raipur@kesari.com', 'accounts123', 'raipur')}
                  >
                    Accountant
                  </button>
                  <button
                    type="button"
                    className="btn btn-sm btn-outline-dark"
                    onClick={() => setPreset('customer@acme.com', 'customer123', 'raipur')}
                  >
                    Corporate Client
                  </button>
                </div>
              </div>

              {/* Bottom Quick Links */}
              <div className="mt-4 pt-3 border-top d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2 small text-muted text-center text-sm-start">
                <Link to="/register-agency" className="text-decoration-none py-1">
                  <i className="bi bi-building-add me-1"></i> Register New Agency
                </Link>
                <Link to="/track" className="text-decoration-none py-1">
                  <i className="bi bi-search me-1"></i> Track Consignment
                </Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

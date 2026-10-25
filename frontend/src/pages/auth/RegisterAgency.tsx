// frontend/src/pages/auth/RegisterAgency.tsx
import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import api from '../../services/api';

export const RegisterAgency: React.FC = () => {
  const [formData, setFormData] = useState({
    name: '',
    tradeName: '',
    contactPerson: '',
    email: '',
    phone: '',
    city: '',
    state: 'Chhattisgarh',
    pincode: '',
    gstin: '',
    panNo: '',
    planType: 'Yearly',
  });

  const [loading, setLoading] = useState(false);
  const [success, setSuccess] = useState<any | null>(null);
  const [error, setError] = useState<string | null>(null);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    setError(null);

    try {
      const res = await api.post('/auth/register-agency', formData);
      setSuccess(res.data.data);
    } catch (err: any) {
      setError(
        err.response?.data?.error?.message ||
          'Failed to submit registration. Please verify the entered details.'
      );
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-vh-100 bg-light py-5">
      <div className="container">
        <div className="row justify-content-center">
          <div className="col-12 col-lg-8">
            <div className="app-card p-4 p-md-5">
              <div className="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
                <div>
                  <h3 className="brand-title text-primary mb-1">
                    Courier Agency Franchise Registration
                  </h3>
                  <p className="text-muted small mb-0">
                    Apply for an independent multi-tenant agency portal on Kesari ERP
                  </p>
                </div>
                <Link to="/login" className="btn btn-outline-secondary btn-sm">
                  Back to Sign In
                </Link>
              </div>

              {error && (
                <div className="alert alert-danger py-2 small d-flex align-items-center gap-2">
                  <i className="bi bi-exclamation-triangle-fill"></i>
                  <span>{error}</span>
                </div>
              )}

              {success ? (
                <div className="text-center py-4">
                  <i className="bi bi-check-circle-fill text-success display-2 mb-3"></i>
                  <h4>Registration Submitted Successfully!</h4>
                  <p className="text-muted">
                    Your agency portal request for <strong>{formData.name}</strong> has been
                    routed to the Super Administrator approval desk.
                  </p>
                  <div className="alert alert-info d-inline-block text-start p-3 my-3">
                    <div><strong>Assigned Slug:</strong> {success.slug}</div>
                    <div><strong>Initial Email:</strong> {success.initialCredentials?.email}</div>
                    <div><strong>Temporary Password:</strong> <code>{success.initialCredentials?.tempPassword}</code></div>
                  </div>
                  <div>
                    <Link to="/login" className="btn btn-primary px-4 mt-2">
                      Proceed to Sign In
                    </Link>
                  </div>
                </div>
              ) : (
                <form onSubmit={handleSubmit}>
                  <div className="row g-3">
                    <div className="col-md-6">
                      <label className="form-label small fw-semibold">
                        Legal Business Name *
                      </label>
                      <input
                        type="text"
                        className="form-control"
                        value={formData.name}
                        onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                        placeholder="e.g. Keshri Express Logistics Ltd"
                        required
                      />
                    </div>

                    <div className="col-md-6">
                      <label className="form-label small fw-semibold">
                        Display Trade / Brand Name
                      </label>
                      <input
                        type="text"
                        className="form-control"
                        value={formData.tradeName}
                        onChange={(e) => setFormData({ ...formData, tradeName: e.target.value })}
                        placeholder="e.g. Shree Maruti Courier Desk"
                      />
                    </div>

                    <div className="col-md-6">
                      <label className="form-label small fw-semibold">
                        Owner / Contact Person *
                      </label>
                      <input
                        type="text"
                        className="form-control"
                        value={formData.contactPerson}
                        onChange={(e) => setFormData({ ...formData, contactPerson: e.target.value })}
                        placeholder="e.g. Rajesh Kumar"
                        required
                      />
                    </div>

                    <div className="col-md-6">
                      <label className="form-label small fw-semibold">
                        Primary Mobile Phone *
                      </label>
                      <input
                        type="tel"
                        className="form-control"
                        value={formData.phone}
                        onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
                        placeholder="10-digit mobile"
                        required
                      />
                    </div>

                    <div className="col-md-6">
                      <label className="form-label small fw-semibold">
                        Official Email Address *
                      </label>
                      <input
                        type="email"
                        className="form-control"
                        value={formData.email}
                        onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                        placeholder="agency@domain.com"
                        required
                      />
                    </div>

                    <div className="col-md-6">
                      <label className="form-label small fw-semibold">
                        Subscription Tier Plan
                      </label>
                      <select
                        className="form-select"
                        value={formData.planType}
                        onChange={(e) => setFormData({ ...formData, planType: e.target.value })}
                      >
                        <option value="Starter">Starter Plan (₹4,999/yr - 1 Branch)</option>
                        <option value="Yearly">Professional Plan (₹12,000/yr - 5 Branches)</option>
                        <option value="Enterprise">Enterprise Plan (₹29,999/yr - Unlimited)</option>
                      </select>
                    </div>

                    <div className="col-md-4">
                      <label className="form-label small fw-semibold">City *</label>
                      <input
                        type="text"
                        className="form-control"
                        value={formData.city}
                        onChange={(e) => setFormData({ ...formData, city: e.target.value })}
                        placeholder="e.g. Raipur"
                        required
                      />
                    </div>

                    <div className="col-md-4">
                      <label className="form-label small fw-semibold">State *</label>
                      <input
                        type="text"
                        className="form-control"
                        value={formData.state}
                        onChange={(e) => setFormData({ ...formData, state: e.target.value })}
                        required
                      />
                    </div>

                    <div className="col-md-4">
                      <label className="form-label small fw-semibold">PIN Code *</label>
                      <input
                        type="text"
                        className="form-control"
                        value={formData.pincode}
                        onChange={(e) => setFormData({ ...formData, pincode: e.target.value })}
                        placeholder="6-digit PIN"
                        required
                      />
                    </div>

                    <div className="col-md-6">
                      <label className="form-label small fw-semibold">GSTIN (Optional)</label>
                      <input
                        type="text"
                        className="form-control text-uppercase"
                        value={formData.gstin}
                        onChange={(e) => setFormData({ ...formData, gstin: e.target.value })}
                        placeholder="22AAAAA0000A1Z5"
                      />
                    </div>

                    <div className="col-md-6">
                      <label className="form-label small fw-semibold">PAN No. (Optional)</label>
                      <input
                        type="text"
                        className="form-control text-uppercase"
                        value={formData.panNo}
                        onChange={(e) => setFormData({ ...formData, panNo: e.target.value })}
                        placeholder="ABCDE1234F"
                      />
                    </div>
                  </div>

                  <div className="mt-4 pt-3 border-top">
                    <button
                      type="submit"
                      className="btn btn-primary px-4 py-2 fw-semibold"
                      disabled={loading}
                    >
                      {loading ? (
                        <>
                          <span className="spinner-border spinner-border-sm me-2"></span>
                          Submitting Application...
                        </>
                      ) : (
                        <>
                          <i className="bi bi-send-check me-2"></i> Submit Agency Registration
                        </>
                      )}
                    </button>
                  </div>
                </form>
              )}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

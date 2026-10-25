// frontend/src/pages/superadmin/PlansManager.tsx
import React, { useState, useEffect } from 'react';
import api from '../../services/api';

export const PlansManager: React.FC = () => {
  const [plans, setPlans] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [showAddModal, setShowAddModal] = useState(false);
  const [formData, setFormData] = useState({
    name: '',
    description: '',
    price: 9999,
    billing_cycle: 'yearly',
    duration_days: 365,
    max_branches: 3,
    max_users: 10,
    booking_quota: 5000,
  });

  const fetchPlans = async () => {
    try {
      setLoading(true);
      const res = await api.get('/superadmin/plans');
      if (res.data.success) {
        setPlans(res.data.data);
      }
    } catch (e) {
      console.error(e);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchPlans();
  }, []);

  const handleCreatePlan = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      await api.post('/superadmin/plans', formData);
      setShowAddModal(false);
      fetchPlans();
    } catch (err: any) {
      alert(err.response?.data?.error?.message || 'Error creating plan');
    }
  };

  return (
    <div>
      <div className="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2 className="brand-title mb-1">SaaS Subscription Plans</h2>
          <p className="text-muted small mb-0">
            Configure subscription tiers, price matrix, branch allowances, and booking quotas
          </p>
        </div>
        <button className="btn btn-primary btn-sm" onClick={() => setShowAddModal(true)}>
          <i className="bi bi-plus-circle me-1"></i> New Plan Tier
        </button>
      </div>

      <div className="row g-4">
        {loading ? (
          <div className="text-center py-5">
            <div className="spinner-border text-primary spinner-border-sm me-2"></div>
            Loading plans...
          </div>
        ) : (
          plans.map((plan) => (
            <div key={plan.id} className="col-12 col-md-4">
              <div className="app-card p-4 h-100 d-flex flex-column border-top border-4 border-primary">
                <div className="d-flex justify-content-between align-items-start mb-2">
                  <h4 className="fw-bold mb-0">{plan.name}</h4>
                  <span className="badge bg-light text-dark border text-uppercase">
                    {plan.billing_cycle}
                  </span>
                </div>
                <p className="text-muted small mb-3">{plan.description || 'Standard logistics tier'}</p>

                <div className="my-2">
                  <span className="fs-2 fw-bolder">₹{Number(plan.price).toLocaleString('en-IN')}</span>
                  <span className="text-muted small"> / {plan.duration_days} days</span>
                </div>

                <hr className="my-3" />

                <ul className="list-unstyled d-flex flex-column gap-2 small mb-4 flex-grow-1">
                  <li className="d-flex align-items-center gap-2">
                    <i className="bi bi-check-circle-fill text-success"></i>
                    <span>
                      Up to <strong>{plan.max_branches === 999 ? 'Unlimited' : plan.max_branches}</strong> Branches
                    </span>
                  </li>
                  <li className="d-flex align-items-center gap-2">
                    <i className="bi bi-check-circle-fill text-success"></i>
                    <span>
                      Up to <strong>{plan.max_users === 999 ? 'Unlimited' : plan.max_users}</strong> Staff Accounts
                    </span>
                  </li>
                  <li className="d-flex align-items-center gap-2">
                    <i className="bi bi-check-circle-fill text-success"></i>
                    <span>
                      Monthly Quota:{' '}
                      <strong>{plan.booking_quota > 50000 ? 'Unlimited' : plan.booking_quota.toLocaleString()}</strong> Bookings
                    </span>
                  </li>
                  <li className="d-flex align-items-center gap-2">
                    <i className="bi bi-check-circle-fill text-success"></i>
                    <span>4x6" Thermal Barcode Docket Hub</span>
                  </li>
                  <li className="d-flex align-items-center gap-2">
                    <i className="bi bi-check-circle-fill text-success"></i>
                    <span>GST SAC 996812 Invoicing & GSTR-1</span>
                  </li>
                </ul>

                <button className="btn btn-outline-primary btn-sm w-100 mt-auto">
                  Edit Plan Configuration
                </button>
              </div>
            </div>
          ))
        )}
      </div>

      {/* Add Plan Modal */}
      {showAddModal && (
        <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
          <div className="modal-dialog">
            <div className="modal-content">
              <div className="modal-header">
                <h5 className="modal-title">Create Subscription Plan</h5>
                <button type="button" className="btn-close" onClick={() => setShowAddModal(false)}></button>
              </div>
              <form onSubmit={handleCreatePlan}>
                <div className="modal-body">
                  <div className="mb-3">
                    <label className="form-label small fw-semibold">Plan Name *</label>
                    <input
                      type="text"
                      className="form-control"
                      value={formData.name}
                      onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                      placeholder="e.g. Growth Tier"
                      required
                    />
                  </div>
                  <div className="mb-3">
                    <label className="form-label small fw-semibold">Description</label>
                    <textarea
                      className="form-control"
                      rows={2}
                      value={formData.description}
                      onChange={(e) => setFormData({ ...formData, description: e.target.value })}
                    />
                  </div>
                  <div className="row g-2 mb-3">
                    <div className="col-6">
                      <label className="form-label small fw-semibold">Price (INR) *</label>
                      <input
                        type="number"
                        className="form-control"
                        value={formData.price}
                        onChange={(e) => setFormData({ ...formData, price: Number(e.target.value) })}
                        required
                      />
                    </div>
                    <div className="col-6">
                      <label className="form-label small fw-semibold">Cycle</label>
                      <select
                        className="form-select"
                        value={formData.billing_cycle}
                        onChange={(e) => setFormData({ ...formData, billing_cycle: e.target.value })}
                      >
                        <option value="monthly">Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="half_yearly">Half Yearly</option>
                        <option value="yearly">Yearly</option>
                      </select>
                    </div>
                  </div>
                  <div className="row g-2">
                    <div className="col-4">
                      <label className="form-label small fw-semibold">Max Branches</label>
                      <input
                        type="number"
                        className="form-control"
                        value={formData.max_branches}
                        onChange={(e) => setFormData({ ...formData, max_branches: Number(e.target.value) })}
                      />
                    </div>
                    <div className="col-4">
                      <label className="form-label small fw-semibold">Max Users</label>
                      <input
                        type="number"
                        className="form-control"
                        value={formData.max_users}
                        onChange={(e) => setFormData({ ...formData, max_users: Number(e.target.value) })}
                      />
                    </div>
                    <div className="col-4">
                      <label className="form-label small fw-semibold">Booking Quota</label>
                      <input
                        type="number"
                        className="form-control"
                        value={formData.booking_quota}
                        onChange={(e) => setFormData({ ...formData, booking_quota: Number(e.target.value) })}
                      />
                    </div>
                  </div>
                </div>
                <div className="modal-footer">
                  <button type="button" className="btn btn-light" onClick={() => setShowAddModal(false)}>
                    Cancel
                  </button>
                  <button type="submit" className="btn btn-primary">
                    Create Plan
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

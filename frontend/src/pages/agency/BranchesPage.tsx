// frontend/src/pages/agency/BranchesPage.tsx
import React, { useState, useEffect } from 'react';
import api from '../../services/api';
import { Branch } from '../../types';

export const BranchesPage: React.FC = () => {
  const [branches, setBranches] = useState<Branch[]>([]);
  const [loading, setLoading] = useState(true);
  const [showModal, setShowModal] = useState(false);
  const [formData, setFormData] = useState({
    branchCode: '',
    branchName: '',
    address: '',
    city: '',
    state: 'Chhattisgarh',
    pincode: '',
    phone: '',
  });

  const fetchBranches = async () => {
    try {
      setLoading(true);
      const res = await api.get('/agency/branches');
      if (res.data.success) {
        setBranches(res.data.data);
      }
    } catch (e) {
      console.error(e);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchBranches();
  }, []);

  const handleCreate = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      await api.post('/agency/branches', formData);
      setShowModal(false);
      setFormData({
        branchCode: '',
        branchName: '',
        address: '',
        city: '',
        state: 'Chhattisgarh',
        pincode: '',
        phone: '',
      });
      fetchBranches();
    } catch (err: any) {
      alert(err.response?.data?.error?.message || 'Error creating branch');
    }
  };

  return (
    <div>
      <div className="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2 className="brand-title mb-1">Branch Master Directory</h2>
          <p className="text-muted small mb-0">
            Manage multi-location branch desks, booking counters, and assigned branch managers
          </p>
        </div>
        <button className="btn btn-primary btn-sm" onClick={() => setShowModal(true)}>
          <i className="bi bi-plus-circle me-1"></i> Add Branch
        </button>
      </div>

      <div className="row g-3">
        {loading ? (
          <div className="text-center py-5">
            <div className="spinner-border text-primary spinner-border-sm me-2"></div>
            Loading branches...
          </div>
        ) : (
          branches.map((b) => (
            <div key={b.id} className="col-12 col-md-6 col-lg-4">
              <div className="app-card p-4 h-100 d-flex flex-column border-start border-4 border-primary">
                <div className="d-flex justify-content-between align-items-start mb-2">
                  <div>
                    <span className="badge bg-primary-subtle text-primary border font-monospace mb-1">
                      {b.branch_code}
                    </span>
                    <h5 className="fw-bold mb-0">{b.branch_name}</h5>
                  </div>
                  <span className="badge bg-success-subtle text-success">Active</span>
                </div>

                <div className="text-muted small my-2 lh-sm flex-grow-1">
                  <i className="bi bi-geo-alt me-1"></i> {b.address}, {b.city}, {b.state} - {b.pincode}
                  <div className="mt-1">
                    <i className="bi bi-telephone me-1"></i> {b.phone}
                  </div>
                </div>

                <hr className="my-2" />

                <div className="d-flex justify-content-between small text-muted">
                  <span>
                    Staff: <strong>{b._count?.users || 0}</strong>
                  </span>
                  <span>
                    Bookings: <strong>{b._count?.consignments || 0}</strong>
                  </span>
                </div>
              </div>
            </div>
          ))
        )}
      </div>

      {/* Add Branch Modal */}
      {showModal && (
        <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
          <div className="modal-dialog">
            <div className="modal-content">
              <div className="modal-header">
                <h5 className="modal-title">Create Operational Branch</h5>
                <button type="button" className="btn-close" onClick={() => setShowModal(false)}></button>
              </div>
              <form onSubmit={handleCreate}>
                <div className="modal-body">
                  <div className="row g-2 mb-3">
                    <div className="col-4">
                      <label className="form-label small fw-semibold">Branch Code *</label>
                      <input
                        type="text"
                        className="form-control text-uppercase font-monospace"
                        placeholder="e.g. RAI-02"
                        value={formData.branchCode}
                        onChange={(e) => setFormData({ ...formData, branchCode: e.target.value.toUpperCase() })}
                        required
                      />
                    </div>
                    <div className="col-8">
                      <label className="form-label small fw-semibold">Branch Name *</label>
                      <input
                        type="text"
                        className="form-control"
                        placeholder="e.g. Bilaspur Highway Hub"
                        value={formData.branchName}
                        onChange={(e) => setFormData({ ...formData, branchName: e.target.value })}
                        required
                      />
                    </div>
                  </div>

                  <div className="mb-3">
                    <label className="form-label small fw-semibold">Physical Address *</label>
                    <textarea
                      className="form-control"
                      rows={2}
                      placeholder="Shop/Office number, street, locality"
                      value={formData.address}
                      onChange={(e) => setFormData({ ...formData, address: e.target.value })}
                      required
                    />
                  </div>

                  <div className="row g-2 mb-3">
                    <div className="col-4">
                      <label className="form-label small fw-semibold">City *</label>
                      <input
                        type="text"
                        className="form-control"
                        value={formData.city}
                        onChange={(e) => setFormData({ ...formData, city: e.target.value })}
                        required
                      />
                    </div>
                    <div className="col-4">
                      <label className="form-label small fw-semibold">State *</label>
                      <input
                        type="text"
                        className="form-control"
                        value={formData.state}
                        onChange={(e) => setFormData({ ...formData, state: e.target.value })}
                        required
                      />
                    </div>
                    <div className="col-4">
                      <label className="form-label small fw-semibold">PIN Code *</label>
                      <input
                        type="text"
                        className="form-control"
                        placeholder="6-digit"
                        value={formData.pincode}
                        onChange={(e) => setFormData({ ...formData, pincode: e.target.value })}
                        required
                      />
                    </div>
                  </div>

                  <div className="mb-3">
                    <label className="form-label small fw-semibold">Desk Contact Phone *</label>
                    <input
                      type="tel"
                      className="form-control"
                      placeholder="Branch phone"
                      value={formData.phone}
                      onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
                      required
                    />
                  </div>
                </div>
                <div className="modal-footer">
                  <button type="button" className="btn btn-light" onClick={() => setShowModal(false)}>
                    Cancel
                  </button>
                  <button type="submit" className="btn btn-primary">
                    Create Branch
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

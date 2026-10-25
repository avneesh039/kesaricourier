// frontend/src/pages/agency/StaffPage.tsx
import React, { useState, useEffect } from 'react';
import api from '../../services/api';

export const StaffPage: React.FC = () => {
  const [staffList, setStaffList] = useState<any[]>([]);
  const [branches, setBranches] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [showModal, setShowModal] = useState(false);
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    password: '',
    role: 'operations_staff',
    branchId: undefined as number | undefined,
  });

  const fetchData = async () => {
    try {
      setLoading(true);
      const [staffRes, branchRes] = await Promise.all([
        api.get('/agency/staff'),
        api.get('/agency/branches'),
      ]);
      if (staffRes.data.success) setStaffList(staffRes.data.data);
      if (branchRes.data.success) setBranches(branchRes.data.data);
    } catch (e) {
      console.error(e);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchData();
  }, []);

  const handleCreate = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      await api.post('/agency/staff', formData);
      setShowModal(false);
      setFormData({
        name: '',
        email: '',
        phone: '',
        password: '',
        role: 'operations_staff',
        branchId: undefined,
      });
      fetchData();
    } catch (err: any) {
      alert(err.response?.data?.error?.message || 'Error creating staff');
    }
  };

  const getRoleBadge = (role: string) => {
    switch (role) {
      case 'agency_admin':
        return <span className="badge bg-primary">Agency Admin</span>;
      case 'branch_manager':
        return <span className="badge bg-info text-dark">Branch Manager</span>;
      case 'operations_staff':
        return <span className="badge bg-success">Counter Ops</span>;
      case 'delivery_personnel':
        return <span className="badge bg-warning text-dark">Delivery Person</span>;
      case 'accounts_staff':
        return <span className="badge bg-secondary">Accountant</span>;
      default:
        return <span className="badge bg-light text-dark">{role}</span>;
    }
  };

  return (
    <div>
      <div className="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2 className="brand-title mb-1">Staff & Employee Directory</h2>
          <p className="text-muted small mb-0">
            Assign role-based access control to booking operators, branch managers, delivery drivers, and accountants
          </p>
        </div>
        <button className="btn btn-primary btn-sm" onClick={() => setShowModal(true)}>
          <i className="bi bi-person-plus me-1"></i> Add Employee
        </button>
      </div>

      <div className="app-card p-3">
        {loading ? (
          <div className="text-center py-5">
            <div className="spinner-border text-primary spinner-border-sm me-2"></div>
            Loading staff accounts...
          </div>
        ) : (
          <div className="table-responsive">
            <table className="table table-hover align-middle mb-0">
              <thead className="table-light">
                <tr className="small text-muted text-uppercase">
                  <th>Name</th>
                  <th>Role</th>
                  <th>Assigned Branch</th>
                  <th>Email</th>
                  <th>Phone</th>
                  <th>Last Login</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                {staffList.map((s) => (
                  <tr key={s.id}>
                    <td>
                      <div className="fw-semibold">{s.name}</div>
                    </td>
                    <td>{getRoleBadge(s.role)}</td>
                    <td>
                      {s.assigned_branch ? (
                        <span className="badge bg-light text-dark border">
                          {s.assigned_branch.branch_code} - {s.assigned_branch.branch_name}
                        </span>
                      ) : (
                        <span className="text-muted small">All Branches</span>
                      )}
                    </td>
                    <td className="small">{s.email}</td>
                    <td className="small">{s.phone || '—'}</td>
                    <td className="small text-muted">
                      {s.last_login_at ? new Date(s.last_login_at).toLocaleString() : 'Never'}
                    </td>
                    <td>
                      <span className="badge bg-success-subtle text-success">Active</span>
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </div>

      {/* Add Staff Modal */}
      {showModal && (
        <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
          <div className="modal-dialog">
            <div className="modal-content">
              <div className="modal-header">
                <h5 className="modal-title">Add Staff Member</h5>
                <button type="button" className="btn-close" onClick={() => setShowModal(false)}></button>
              </div>
              <form onSubmit={handleCreate}>
                <div className="modal-body">
                  <div className="mb-3">
                    <label className="form-label small fw-semibold">Employee Name *</label>
                    <input
                      type="text"
                      className="form-control"
                      value={formData.name}
                      onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                      placeholder="e.g. Ramesh Kumar"
                      required
                    />
                  </div>
                  <div className="row g-2 mb-3">
                    <div className="col-6">
                      <label className="form-label small fw-semibold">Email *</label>
                      <input
                        type="email"
                        className="form-control"
                        value={formData.email}
                        onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                        required
                      />
                    </div>
                    <div className="col-6">
                      <label className="form-label small fw-semibold">Phone</label>
                      <input
                        type="tel"
                        className="form-control"
                        value={formData.phone}
                        onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
                      />
                    </div>
                  </div>
                  <div className="mb-3">
                    <label className="form-label small fw-semibold">Initial Password *</label>
                    <input
                      type="password"
                      className="form-control"
                      value={formData.password}
                      onChange={(e) => setFormData({ ...formData, password: e.target.value })}
                      placeholder="Minimum 6 characters"
                      required
                    />
                  </div>
                  <div className="row g-2">
                    <div className="col-6">
                      <label className="form-label small fw-semibold">Operational Role *</label>
                      <select
                        className="form-select"
                        value={formData.role}
                        onChange={(e) => setFormData({ ...formData, role: e.target.value })}
                      >
                        <option value="agency_admin">Agency Admin</option>
                        <option value="branch_manager">Branch Manager</option>
                        <option value="operations_staff">Counter Operator</option>
                        <option value="delivery_personnel">Delivery Personnel</option>
                        <option value="accounts_staff">Accounts Staff</option>
                      </select>
                    </div>
                    <div className="col-6">
                      <label className="form-label small fw-semibold">Assigned Branch</label>
                      <select
                        className="form-select"
                        value={formData.branchId || ''}
                        onChange={(e) =>
                          setFormData({
                            ...formData,
                            branchId: e.target.value ? Number(e.target.value) : undefined,
                          })
                        }
                      >
                        <option value="">All / Agency Wide</option>
                        {branches.map((b) => (
                          <option key={b.id} value={b.id}>
                            {b.branch_code} - {b.branch_name}
                          </option>
                        ))}
                      </select>
                    </div>
                  </div>
                </div>
                <div className="modal-footer">
                  <button type="button" className="btn btn-light" onClick={() => setShowModal(false)}>
                    Cancel
                  </button>
                  <button type="submit" className="btn btn-primary">
                    Create Account
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

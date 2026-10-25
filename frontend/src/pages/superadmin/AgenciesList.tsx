// frontend/src/pages/superadmin/AgenciesList.tsx
import React, { useState, useEffect } from 'react';
import api from '../../services/api';
import { Tenant } from '../../types';

export const AgenciesList: React.FC = () => {
  const [agencies, setAgencies] = useState<Tenant[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [statusFilter, setStatusFilter] = useState('all');
  const [selectedAgency, setSelectedAgency] = useState<Tenant | null>(null);
  const [newCode, setNewCode] = useState('');
  const [modalOpen, setModalOpen] = useState(false);
  const [autoApprove, setAutoApprove] = useState(false);

  const fetchAgencies = async () => {
    try {
      setLoading(true);
      const res = await api.get('/superadmin/agencies', {
        params: { search, status: statusFilter },
      });
      if (res.data.success) {
        setAgencies(res.data.data);
      }
    } catch (err) {
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchAgencies();
  }, [statusFilter]);

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    fetchAgencies();
  };

  const handleStatusUpdate = async (status: string, code?: string) => {
    if (!selectedAgency) return;
    try {
      await api.patch(`/superadmin/agencies/${selectedAgency.id}/status`, {
        status,
        agencyCode: code || undefined,
      });
      setModalOpen(false);
      setSelectedAgency(null);
      fetchAgencies();
    } catch (e: any) {
      alert(e.response?.data?.error?.message || 'Error updating status');
    }
  };

  return (
    <div>
      <div className="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-4">
        <div>
          <div className="d-flex align-items-center gap-2">
            <h2 className="brand-title mb-0">Agency Network Directory</h2>
            <span className="badge bg-primary-subtle text-primary border">User Approval Hub</span>
          </div>
          <p className="text-muted small mb-0 mt-1">
            Supervise all courier franchises, assign operational agency codes, and govern subscriptions (Image 4.46.05)
          </p>
        </div>

        {/* Handwritten Image 4.46.05(1) PM: Auto-Approve Switch */}
        <div className="d-flex align-items-center gap-2 bg-light p-2 rounded border">
          <label className="form-check-label small fw-bold mb-0">
            Auto-Approval of Online Agencies:
          </label>
          <div className="form-check form-switch mb-0">
            <input
              type="checkbox"
              className="form-check-input"
              role="switch"
              checked={autoApprove}
              onChange={(e) => {
                setAutoApprove(e.target.checked);
                alert(`Auto-Approval is now ${e.target.checked ? 'ACTIVATED (New online agencies will be instantly approved)' : 'DEACTIVATED (Manual admin review required)'}`);
              }}
            />
          </div>
          <span className={`badge ${autoApprove ? 'bg-success' : 'bg-secondary'}`}>
            {autoApprove ? 'Active' : 'Manual'}
          </span>
        </div>
      </div>

      {/* Filter and Search Bar */}
      <div className="app-card p-3 mb-4">
        <form onSubmit={handleSearch} className="row g-2 align-items-center">
          <div className="col-12 col-md-5">
            <div className="input-group input-group-sm">
              <span className="input-group-text bg-light border-end-0">
                <i className="bi bi-search text-muted"></i>
              </span>
              <input
                type="text"
                className="form-control border-start-0"
                placeholder="Search agency by name, code, email, city..."
                value={search}
                onChange={(e) => setSearch(e.target.value)}
              />
            </div>
          </div>

          <div className="col-6 col-md-3">
            <select
              className="form-select form-select-sm"
              value={statusFilter}
              onChange={(e) => setStatusFilter(e.target.value)}
            >
              <option value="all">All Statuses</option>
              <option value="active">Active</option>
              <option value="pending_kyc">Pending Approval</option>
              <option value="suspended">Suspended</option>
              <option value="expired">Expired</option>
            </select>
          </div>

          <div className="col-6 col-md-2">
            <button type="submit" className="btn btn-sm btn-primary w-100">
              Filter
            </button>
          </div>
        </form>
      </div>

      {/* Agencies Table */}
      <div className="app-card p-3">
        {loading ? (
          <div className="text-center py-5">
            <div className="spinner-border text-primary spinner-border-sm me-2"></div>
            Loading agency network...
          </div>
        ) : (
          <div className="table-responsive">
            <table className="table table-hover align-middle mb-0">
              <thead className="table-light">
                <tr className="small text-muted text-uppercase">
                  <th>Code</th>
                  <th>Agency & Contact</th>
                  <th>Location</th>
                  <th>Branches</th>
                  <th>Bookings</th>
                  <th>Subscription</th>
                  <th>Status</th>
                  <th className="text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                {agencies.length === 0 ? (
                  <tr>
                    <td colSpan={8} className="text-center py-4 text-muted">
                      No agencies found matching criteria.
                    </td>
                  </tr>
                ) : (
                  agencies.map((agency) => (
                    <tr key={agency.id}>
                      <td>
                        {agency.agency_code ? (
                          <span className="badge bg-primary-subtle text-primary border font-monospace fs-6 px-2">
                            #{agency.agency_code}
                          </span>
                        ) : (
                          <span className="badge bg-secondary-subtle text-secondary">Pending</span>
                        )}
                      </td>
                      <td>
                        <div className="fw-semibold">{agency.name}</div>
                        {agency.trade_name && (
                          <div className="small text-muted">{agency.trade_name}</div>
                        )}
                        <div className="small text-muted">
                          <i className="bi bi-person me-1"></i> {agency.contact_person} • {agency.phone}
                        </div>
                      </td>
                      <td>
                        <div>{agency.city || 'N/A'}</div>
                        <div className="small text-muted">{agency.state || ''}</div>
                      </td>
                      <td>
                        <span className="badge bg-light text-dark border">
                          {agency._count?.branches || 0}
                        </span>
                      </td>
                      <td>
                        <span className="badge bg-light text-dark border">
                          {agency._count?.consignments || 0}
                        </span>
                      </td>
                      <td>
                        <div className="small fw-semibold">{agency.plan_type}</div>
                        <div className="small text-muted" style={{ fontSize: '0.75rem' }}>
                          Ends: {agency.validity_end ? new Date(agency.validity_end).toLocaleDateString() : 'N/A'}
                        </div>
                      </td>
                      <td>
                        <span className={`badge-status ${agency.status}`}>
                          {agency.status === 'pending_kyc' ? 'Pending Approval' : agency.status}
                        </span>
                      </td>
                      <td className="text-end">
                        <div className="btn-group btn-group-sm">
                          {agency.status !== 'active' && (
                            <button
                              className="btn btn-success btn-xs py-1 px-2 fw-semibold"
                              title="Approve Agency (Image 4.46.05)"
                              onClick={async () => {
                                try {
                                  await api.patch(`/superadmin/agencies/${agency.id}/status`, {
                                    status: 'active',
                                    agencyCode: agency.agency_code || String(Math.floor(10 + Math.random() * 89)),
                                  });
                                  fetchAgencies();
                                } catch (e) {
                                  alert('Error approving agency');
                                }
                              }}
                            >
                              Approve
                            </button>
                          )}
                          {agency.status !== 'suspended' && (
                            <button
                              className="btn btn-warning btn-xs py-1 px-2 fw-semibold text-dark"
                              title="Hold Agency (Image 4.46.05)"
                              onClick={async () => {
                                try {
                                  await api.patch(`/superadmin/agencies/${agency.id}/status`, { status: 'suspended' });
                                  fetchAgencies();
                                } catch (e) {
                                  alert('Error placing agency on hold');
                                }
                              }}
                            >
                              Hold
                            </button>
                          )}
                          {agency.status !== 'rejected' && (
                            <button
                              className="btn btn-danger btn-xs py-1 px-2 fw-semibold"
                              title="Reject Agency (Image 4.46.05)"
                              onClick={async () => {
                                try {
                                  await api.patch(`/superadmin/agencies/${agency.id}/status`, { status: 'rejected' });
                                  fetchAgencies();
                                } catch (e) {
                                  alert('Error rejecting agency');
                                }
                              }}
                            >
                              Reject
                            </button>
                          )}
                          <button
                            className="btn btn-outline-secondary btn-xs py-1 px-2"
                            onClick={() => {
                              setSelectedAgency(agency);
                              setNewCode(agency.agency_code || '');
                              setModalOpen(true);
                            }}
                          >
                            Details
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        )}
      </div>

      {/* Action Modal */}
      {modalOpen && selectedAgency && (
        <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
          <div className="modal-dialog modal-dialog-centered">
            <div className="modal-content">
              <div className="modal-header">
                <h5 className="modal-title">Manage Agency #{selectedAgency.id}</h5>
                <button
                  type="button"
                  className="btn-close"
                  onClick={() => setModalOpen(false)}
                ></button>
              </div>
              <div className="modal-body">
                <h6>{selectedAgency.name}</h6>
                <p className="text-muted small">
                  Contact: {selectedAgency.contact_person} ({selectedAgency.email} / {selectedAgency.phone})
                </p>

                <div className="mb-3">
                  <label className="form-label small fw-semibold">Agency Code Assignment</label>
                  <input
                    type="text"
                    className="form-control font-monospace"
                    value={newCode}
                    onChange={(e) => setNewCode(e.target.value)}
                    placeholder="e.g. 26 or 27"
                  />
                  <div className="form-text small">
                    Unique code used as AWB and docket barcode prefix.
                  </div>
                </div>

                <div className="d-flex flex-column gap-2 mt-4">
                  {selectedAgency.status !== 'active' && (
                    <button
                      className="btn btn-success"
                      onClick={() => handleStatusUpdate('active', newCode)}
                    >
                      <i className="bi bi-check-circle me-1"></i> Approve & Activate Portal
                    </button>
                  )}

                  {selectedAgency.status === 'active' && (
                    <button
                      className="btn btn-warning text-dark"
                      onClick={() => handleStatusUpdate('suspended')}
                    >
                      <i className="bi bi-pause-circle me-1"></i> Suspend Agency Access
                    </button>
                  )}

                  {selectedAgency.status === 'suspended' && (
                    <button
                      className="btn btn-outline-success"
                      onClick={() => handleStatusUpdate('active')}
                    >
                      <i className="bi bi-play-circle me-1"></i> Reactivate Agency
                    </button>
                  )}

                  {selectedAgency.status === 'pending_kyc' && (
                    <button
                      className="btn btn-outline-danger"
                      onClick={() => handleStatusUpdate('rejected')}
                    >
                      <i className="bi bi-x-circle me-1"></i> Reject Application
                    </button>
                  )}
                </div>
              </div>
              <div className="modal-footer">
                <button
                  type="button"
                  className="btn btn-light"
                  onClick={() => setModalOpen(false)}
                >
                  Cancel
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

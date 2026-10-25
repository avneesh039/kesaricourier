// frontend/src/pages/superadmin/SuperAdminTickets.tsx
import React, { useState, useEffect } from 'react';
import api from '../../services/api';

interface Ticket {
  id: number;
  ticket_number: string;
  category: string;
  subject: string;
  description: string;
  destination_name?: string;
  pincode?: string;
  priority: string;
  status: string;
  admin_notes?: string;
  created_at: string;
  resolved_at?: string;
  tenant: {
    id: number;
    name: string;
    agency_code?: string;
    phone: string;
    email: string;
  };
}

export const SuperAdminTickets: React.FC = () => {
  const [tickets, setTickets] = useState<Ticket[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [statusFilter, setStatusFilter] = useState<string>('all');
  const [categoryFilter, setCategoryFilter] = useState<string>('all');
  const [selectedTicket, setSelectedTicket] = useState<Ticket | null>(null);
  const [adminNotes, setAdminNotes] = useState<string>('');
  const [newStatus, setNewStatus] = useState<string>('resolved');
  const [updating, setUpdating] = useState<boolean>(false);

  const fetchTickets = async () => {
    try {
      setLoading(true);
      const res = await api.get('/superadmin/tickets', {
        params: { status: statusFilter, category: categoryFilter },
      });
      if (res.data.success) {
        setTickets(res.data.data);
      }
    } catch (err) {
      console.error('Error fetching tickets', err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchTickets();
  }, [statusFilter, categoryFilter]);

  const openUpdateModal = (ticket: Ticket) => {
    setSelectedTicket(ticket);
    setAdminNotes(ticket.admin_notes || '');
    setNewStatus(ticket.status === 'open' ? 'in_progress' : ticket.status);
  };

  const handleUpdate = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!selectedTicket) return;

    try {
      setUpdating(true);
      const res = await api.patch(`/superadmin/tickets/${selectedTicket.id}`, {
        status: newStatus,
        adminNotes,
      });

      if (res.data.success) {
        setSelectedTicket(null);
        fetchTickets();
      }
    } catch (err: any) {
      alert(err.response?.data?.error?.message || 'Failed to update ticket');
    } finally {
      setUpdating(false);
    }
  };

  const getPriorityBadge = (priority: string) => {
    switch (priority) {
      case 'urgent':
        return <span className="badge bg-danger">Urgent</span>;
      case 'high':
        return <span className="badge bg-warning text-dark">High</span>;
      case 'normal':
        return <span className="badge bg-info text-dark">Normal</span>;
      default:
        return <span className="badge bg-secondary">Low</span>;
    }
  };

  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'open':
        return <span className="badge bg-danger">Open</span>;
      case 'in_progress':
        return <span className="badge bg-primary">In Progress</span>;
      case 'resolved':
        return <span className="badge bg-success">Resolved</span>;
      case 'closed':
        return <span className="badge bg-dark">Closed</span>;
      default:
        return <span className="badge bg-secondary">{status}</span>;
    }
  };

  return (
    <div className="container-fluid p-0">
      {/* Page Title */}
      <div className="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 className="fw-bold text-dark mb-1">Support & Request Desk</h3>
          <p className="text-muted small mb-0">
            Platform-wide governance: Agency assistance, destination approvals, and technical support.
          </p>
        </div>
        <button className="btn btn-outline-secondary btn-sm" onClick={fetchTickets}>
          <i className="bi bi-arrow-clockwise me-1"></i> Refresh
        </button>
      </div>

      {/* Filter Toolbar */}
      <div className="card shadow-sm border-0 mb-4">
        <div className="card-body p-3">
          <div className="row g-3">
            <div className="col-md-4">
              <label className="form-label small fw-bold">Filter by Status</label>
              <select
                className="form-select form-select-sm"
                value={statusFilter}
                onChange={(e) => setStatusFilter(e.target.value)}
              >
                <option value="all">All Statuses</option>
                <option value="open">Open</option>
                <option value="in_progress">In Progress</option>
                <option value="resolved">Resolved</option>
                <option value="closed">Closed</option>
              </select>
            </div>
            <div className="col-md-4">
              <label className="form-label small fw-bold">Filter by Category</label>
              <select
                className="form-select form-select-sm"
                value={categoryFilter}
                onChange={(e) => setCategoryFilter(e.target.value)}
              >
                <option value="all">All Categories</option>
                <option value="destination_request">Destination Addition Request</option>
                <option value="billing">Billing & Tax Inquiry</option>
                <option value="technical">Technical Support</option>
                <option value="general">General Query</option>
              </select>
            </div>
            <div className="col-md-4 d-flex align-items-end">
              <div className="small text-muted">
                Showing <strong>{tickets.length}</strong> ticket(s)
              </div>
            </div>
          </div>
        </div>
      </div>

      {/* Tickets List Table */}
      <div className="card shadow-sm border-0">
        <div className="table-responsive">
          <table className="table table-hover align-middle mb-0">
            <thead className="table-light text-muted small text-uppercase">
              <tr>
                <th>Ticket ID</th>
                <th>Agency</th>
                <th>Category</th>
                <th>Subject & Details</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Created</th>
                <th className="text-end">Action</th>
              </tr>
            </thead>
            <tbody>
              {loading ? (
                <tr>
                  <td colSpan={8} className="text-center py-5">
                    <div className="spinner-border text-primary spinner-border-sm" role="status"></div>
                    <div className="small text-muted mt-2">Loading support tickets...</div>
                  </td>
                </tr>
              ) : tickets.length === 0 ? (
                <tr>
                  <td colSpan={8} className="text-center py-5 text-muted">
                    <i className="bi bi-chat-square-check fs-2 d-block mb-2 text-muted"></i>
                    No tickets found matching the selected filters.
                  </td>
                </tr>
              ) : (
                tickets.map((t) => (
                  <tr key={t.id}>
                    <td className="fw-bold font-monospace text-primary">{t.ticket_number}</td>
                    <td>
                      <div className="fw-semibold text-dark">{t.tenant.name}</div>
                      <div className="small text-muted">
                        Code: <strong>{t.tenant.agency_code || 'N/A'}</strong> | {t.tenant.phone}
                      </div>
                    </td>
                    <td>
                      <span className="badge bg-light text-dark border text-uppercase">
                        {t.category.replace('_', ' ')}
                      </span>
                    </td>
                    <td>
                      <div className="fw-bold text-dark">{t.subject}</div>
                      <div className="small text-muted text-truncate" style={{ maxWidth: '300px' }}>
                        {t.description}
                      </div>
                      {t.destination_name && (
                        <div className="small text-primary mt-1">
                          <i className="bi bi-geo-alt me-1"></i>
                          Requested City: <strong>{t.destination_name}</strong> (PIN: {t.pincode || 'N/A'})
                        </div>
                      )}
                    </td>
                    <td>{getPriorityBadge(t.priority)}</td>
                    <td>{getStatusBadge(t.status)}</td>
                    <td className="small text-muted">
                      {new Date(t.created_at).toLocaleDateString('en-IN', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                      })}
                    </td>
                    <td className="text-end">
                      <button
                        className="btn btn-sm btn-outline-primary"
                        onClick={() => openUpdateModal(t)}
                      >
                        <i className="bi bi-pencil-square me-1"></i> Manage
                      </button>
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* Manage Ticket Modal */}
      {selectedTicket && (
        <div className="modal fade show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
          <div className="modal-dialog modal-lg modal-dialog-centered">
            <div className="modal-content border-0 shadow-lg">
              <div className="modal-header bg-light">
                <h5 className="modal-title fw-bold">
                  Manage Support Ticket: {selectedTicket.ticket_number}
                </h5>
                <button
                  type="button"
                  className="btn-close"
                  onClick={() => setSelectedTicket(null)}
                ></button>
              </div>
              <form onSubmit={handleUpdate}>
                <div className="modal-body">
                  <div className="alert alert-light border mb-3">
                    <div className="row g-2">
                      <div className="col-md-6">
                        <span className="text-muted small">Agency:</span>{' '}
                        <strong>{selectedTicket.tenant.name}</strong> ({selectedTicket.tenant.agency_code || 'Pending'})
                      </div>
                      <div className="col-md-6">
                        <span className="text-muted small">Contact:</span>{' '}
                        <span>{selectedTicket.tenant.email} | {selectedTicket.tenant.phone}</span>
                      </div>
                      <div className="col-12 mt-2">
                        <div className="fw-bold text-dark">{selectedTicket.subject}</div>
                        <p className="small text-secondary mb-0 mt-1">{selectedTicket.description}</p>
                      </div>
                      {selectedTicket.destination_name && (
                        <div className="col-12 mt-2 pt-2 border-top">
                          <span className="badge bg-primary me-2">New Hub Request</span>
                          <strong>{selectedTicket.destination_name}</strong> (PIN: {selectedTicket.pincode || 'N/A'})
                        </div>
                      )}
                    </div>
                  </div>

                  <div className="row g-3">
                    <div className="col-md-6">
                      <label className="form-label small fw-bold">Update Status</label>
                      <select
                        className="form-select"
                        value={newStatus}
                        onChange={(e) => setNewStatus(e.target.value)}
                        required
                      >
                        <option value="open">Open</option>
                        <option value="in_progress">In Progress</option>
                        <option value="resolved">Resolved</option>
                        <option value="closed">Closed</option>
                      </select>
                    </div>

                    <div className="col-12">
                      <label className="form-label small fw-bold">
                        Super Admin Response / Resolution Notes
                      </label>
                      <textarea
                        className="form-control"
                        rows={4}
                        placeholder="Provide resolution details or feedback for the agency operator..."
                        value={adminNotes}
                        onChange={(e) => setAdminNotes(e.target.value)}
                      />
                    </div>
                  </div>
                </div>
                <div className="modal-footer bg-light">
                  <button
                    type="button"
                    className="btn btn-secondary"
                    onClick={() => setSelectedTicket(null)}
                  >
                    Cancel
                  </button>
                  <button type="submit" className="btn btn-primary" disabled={updating}>
                    {updating ? 'Saving...' : 'Save Resolution'}
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

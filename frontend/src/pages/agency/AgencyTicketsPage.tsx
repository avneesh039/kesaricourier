// frontend/src/pages/agency/AgencyTicketsPage.tsx
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
}

export const AgencyTicketsPage: React.FC = () => {
  const [tickets, setTickets] = useState<Ticket[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [showModal, setShowModal] = useState<boolean>(false);
  const [submitting, setSubmitting] = useState<boolean>(false);

  // Form State
  const [category, setCategory] = useState<string>('destination_request');
  const [subject, setSubject] = useState<string>('');
  const [description, setDescription] = useState<string>('');
  const [destinationName, setDestinationName] = useState<string>('');
  const [pincode, setPincode] = useState<string>('');
  const [priority, setPriority] = useState<string>('normal');

  const fetchTickets = async () => {
    try {
      setLoading(true);
      const res = await api.get('/agency/tickets');
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
  }, []);

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      setSubmitting(true);
      const res = await api.post('/agency/tickets', {
        category,
        subject,
        description,
        destination_name: category === 'destination_request' ? destinationName : undefined,
        pincode: category === 'destination_request' ? pincode : undefined,
        priority,
      });

      if (res.data.success) {
        setShowModal(false);
        setSubject('');
        setDescription('');
        setDestinationName('');
        setPincode('');
        fetchTickets();
      }
    } catch (err: any) {
      alert(err.response?.data?.error?.message || 'Failed to submit ticket');
    } finally {
      setSubmitting(false);
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
        return <span className="badge bg-secondary">Closed</span>;
      default:
        return <span className="badge bg-secondary">{status}</span>;
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

  return (
    <div className="container-fluid p-0">
      {/* Title */}
      <div className="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 className="fw-bold text-dark mb-1">Agency Support & Hub Requests</h3>
          <p className="text-muted small mb-0">
            Submit service requests, request new delivery pin codes, or contact Super Admin governance.
          </p>
        </div>
        <button className="btn btn-primary btn-sm" onClick={() => setShowModal(true)}>
          <i className="bi bi-plus-lg me-1"></i> New Support Request
        </button>
      </div>

      {/* Tickets List */}
      <div className="card shadow-sm border-0">
        <div className="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
          <h6 className="fw-bold mb-0 text-dark">Ticket History & Responses</h6>
          <span className="small text-muted">{tickets.length} ticket(s) recorded</span>
        </div>
        <div className="table-responsive">
          <table className="table table-hover align-middle mb-0">
            <thead className="table-light text-muted small text-uppercase">
              <tr>
                <th>Ticket ID</th>
                <th>Category</th>
                <th>Subject & Query</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Admin Response</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              {loading ? (
                <tr>
                  <td colSpan={7} className="text-center py-5">
                    <div className="spinner-border text-primary spinner-border-sm" role="status"></div>
                    <div className="small text-muted mt-2">Loading tickets...</div>
                  </td>
                </tr>
              ) : tickets.length === 0 ? (
                <tr>
                  <td colSpan={7} className="text-center py-5 text-muted">
                    <i className="bi bi-inbox fs-2 d-block mb-2 text-muted"></i>
                    No support tickets submitted yet. Click "New Support Request" above if you need assistance.
                  </td>
                </tr>
              ) : (
                tickets.map((t) => (
                  <tr key={t.id}>
                    <td className="fw-bold font-monospace text-primary">{t.ticket_number}</td>
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
                          Hub: <strong>{t.destination_name}</strong> (PIN: {t.pincode || 'N/A'})
                        </div>
                      )}
                    </td>
                    <td>{getPriorityBadge(t.priority)}</td>
                    <td>{getStatusBadge(t.status)}</td>
                    <td>
                      {t.admin_notes ? (
                        <div className="p-2 bg-light border rounded small">
                          <span className="text-success fw-bold me-1">
                            <i className="bi bi-check-circle-fill"></i> Admin:
                          </span>
                          {t.admin_notes}
                        </div>
                      ) : (
                        <span className="text-muted small fst-italic">Pending review</span>
                      )}
                    </td>
                    <td className="small text-muted">
                      {new Date(t.created_at).toLocaleDateString('en-IN', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric',
                      })}
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>
      </div>

      {/* New Ticket Modal */}
      {showModal && (
        <div className="modal fade show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
          <div className="modal-dialog modal-lg modal-dialog-centered">
            <div className="modal-content border-0 shadow-lg">
              <div className="modal-header bg-light">
                <h5 className="modal-title fw-bold">Submit Support or Destination Request</h5>
                <button
                  type="button"
                  className="btn-close"
                  onClick={() => setShowModal(false)}
                ></button>
              </div>
              <form onSubmit={handleSubmit}>
                <div className="modal-body">
                  <div className="row g-3">
                    <div className="col-md-6">
                      <label className="form-label small fw-bold">Request Category</label>
                      <select
                        className="form-select"
                        value={category}
                        onChange={(e) => setCategory(e.target.value)}
                        required
                      >
                        <option value="destination_request">Request New Destination / Hub City</option>
                        <option value="billing">Billing & Tax Inquiry</option>
                        <option value="technical">Technical Support / Bug</option>
                        <option value="general">General Logistics Inquiry</option>
                      </select>
                    </div>

                    <div className="col-md-6">
                      <label className="form-label small fw-bold">Priority Level</label>
                      <select
                        className="form-select"
                        value={priority}
                        onChange={(e) => setPriority(e.target.value)}
                        required
                      >
                        <option value="low">Low</option>
                        <option value="normal">Normal</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                      </select>
                    </div>

                    {category === 'destination_request' && (
                      <>
                        <div className="col-md-6">
                          <label className="form-label small fw-bold">Requested City / Hub Name</label>
                          <input
                            type="text"
                            className="form-control"
                            placeholder="e.g. DURG, BILASPUR, KORBA"
                            value={destinationName}
                            onChange={(e) => setDestinationName(e.target.value.toUpperCase())}
                            required
                          />
                        </div>
                        <div className="col-md-6">
                          <label className="form-label small fw-bold">Pincode</label>
                          <input
                            type="text"
                            className="form-control"
                            placeholder="6-digit pincode"
                            maxLength={6}
                            value={pincode}
                            onChange={(e) => setPincode(e.target.value)}
                          />
                        </div>
                      </>
                    )}

                    <div className="col-12">
                      <label className="form-label small fw-bold">Subject Summary</label>
                      <input
                        type="text"
                        className="form-control"
                        placeholder="Brief summary of your request"
                        value={subject}
                        onChange={(e) => setSubject(e.target.value)}
                        required
                      />
                    </div>

                    <div className="col-12">
                      <label className="form-label small fw-bold">Detailed Description</label>
                      <textarea
                        className="form-control"
                        rows={4}
                        placeholder="Provide relevant details, business justification, or steps to reproduce..."
                        value={description}
                        onChange={(e) => setDescription(e.target.value)}
                        required
                      />
                    </div>
                  </div>
                </div>
                <div className="modal-footer bg-light">
                  <button
                    type="button"
                    className="btn btn-secondary"
                    onClick={() => setShowModal(false)}
                  >
                    Cancel
                  </button>
                  <button type="submit" className="btn btn-primary" disabled={submitting}>
                    {submitting ? 'Submitting...' : 'Submit Request'}
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

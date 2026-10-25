// frontend/src/pages/customer/CustomerPortal.tsx
import React, { useState, useEffect } from 'react';
import { useAuth } from '../../context/AuthContext';
import api from '../../services/api';

interface Consignment {
  id: number;
  consignment_no: string;
  booking_date: string;
  recipient_name: string;
  recipient_phone: string;
  recipient_address: string;
  mode: string;
  send_by: string;
  pieces: number;
  billable_weight: number | string;
  total_amount: number | string;
  status: string;
  destination: {
    code: string;
    name: string;
    state?: { name: string };
  };
}

export const CustomerPortal: React.FC = () => {
  const { user } = useAuth();
  const [consignments, setConsignments] = useState<Consignment[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [search, setSearch] = useState<string>('');
  const [activeTab, setActiveTab] = useState<'shipments' | 'track' | 'support'>('shipments');
  const [trackAwb, setTrackAwb] = useState<string>('');
  const [trackingData, setTrackingData] = useState<any>(null);
  const [trackingLoading, setTrackingLoading] = useState<boolean>(false);

  // Support inquiry form
  const [inquirySubject, setInquirySubject] = useState<string>('');
  const [inquiryMessage, setInquiryMessage] = useState<string>('');
  const [inquirySuccess, setInquirySuccess] = useState<boolean>(false);
  const [submittingInquiry, setSubmittingInquiry] = useState<boolean>(false);

  const fetchConsignments = async () => {
    try {
      setLoading(true);
      const res = await api.get('/bookings', { params: { search, limit: 50 } });
      if (res.data.success) {
        setConsignments(res.data.data);
      }
    } catch (err) {
      console.error('Error fetching customer shipments', err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchConsignments();
  }, []);

  const handleTrackSubmit = async (awbNumber: string) => {
    const awbToSearch = awbNumber || trackAwb;
    if (!awbToSearch.trim()) return;

    try {
      setTrackingLoading(true);
      setTrackAwb(awbToSearch);
      setActiveTab('track');
      const res = await api.get(`/public/track/${awbToSearch.trim()}`);
      if (res.data.success) {
        setTrackingData(res.data.data);
      } else {
        setTrackingData(null);
      }
    } catch (err) {
      setTrackingData(null);
    } finally {
      setTrackingLoading(false);
    }
  };

  const handleSupportSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      setSubmittingInquiry(true);
      const res = await api.post('/agency/tickets', {
        category: 'general',
        subject: `[Client Inquiry] ${inquirySubject}`,
        description: `Submitted by customer: ${user?.name} (${user?.email})\n\n${inquiryMessage}`,
        priority: 'normal',
      });
      if (res.data.success) {
        setInquirySuccess(true);
        setInquirySubject('');
        setInquiryMessage('');
      }
    } catch (err: any) {
      alert(err.response?.data?.error?.message || 'Failed to submit inquiry');
    } finally {
      setSubmittingInquiry(false);
    }
  };

  const getStatusBadge = (status: string) => {
    switch (status) {
      case 'delivered':
        return <span className="badge bg-success">Delivered</span>;
      case 'out_for_delivery':
        return <span className="badge bg-warning text-dark">Out For Delivery</span>;
      case 'in_transit':
        return <span className="badge bg-primary">In Transit</span>;
      case 'manifested':
        return <span className="badge bg-info text-dark">Manifested</span>;
      default:
        return <span className="badge bg-secondary">Booked</span>;
    }
  };

  const deliveredCount = consignments.filter((c) => c.status === 'delivered').length;
  const inTransitCount = consignments.filter((c) => c.status === 'in_transit' || c.status === 'out_for_delivery').length;

  return (
    <div className="container-fluid p-0">
      {/* Welcome Banner */}
      <div className="bg-primary text-white p-4 rounded-3 shadow-sm mb-4">
        <div className="row align-items-center">
          <div className="col-md-8">
            <span className="badge bg-white text-primary fw-bold text-uppercase px-2 py-1 mb-2">
              Corporate Client Self-Service Portal
            </span>
            <h2 className="fw-bold mb-1">Welcome, {user?.name || 'Valued Client'}</h2>
            <p className="mb-0 text-white-50">
              Track live freight dispatches, review consignments, and monitor delivery milestones.
            </p>
          </div>
          <div className="col-md-4 text-md-end mt-3 mt-md-0">
            <button
              className="btn btn-warning fw-bold text-dark me-2"
              onClick={() => setActiveTab('track')}
            >
              <i className="bi bi-search me-1"></i> Quick Track AWB
            </button>
            <button
              className="btn btn-outline-light"
              onClick={() => setActiveTab('support')}
            >
              <i className="bi bi-headset me-1"></i> Help Desk
            </button>
          </div>
        </div>
      </div>

      {/* KPI Cards */}
      <div className="row g-3 mb-4">
        <div className="col-sm-6 col-lg-4">
          <div className="card border-0 shadow-sm p-3 h-100 border-start border-4 border-primary">
            <div className="text-muted small text-uppercase fw-bold">Total Consignments</div>
            <div className="fs-2 fw-bold text-dark mt-1">{consignments.length}</div>
            <div className="small text-muted">All booked dockets</div>
          </div>
        </div>
        <div className="col-sm-6 col-lg-4">
          <div className="card border-0 shadow-sm p-3 h-100 border-start border-4 border-warning">
            <div className="text-muted small text-uppercase fw-bold">In-Transit / Live</div>
            <div className="fs-2 fw-bold text-warning mt-1">{inTransitCount}</div>
            <div className="small text-muted">Dispatches on road or air</div>
          </div>
        </div>
        <div className="col-sm-6 col-lg-4">
          <div className="card border-0 shadow-sm p-3 h-100 border-start border-4 border-success">
            <div className="text-muted small text-uppercase fw-bold">Delivered PODs</div>
            <div className="fs-2 fw-bold text-success mt-1">{deliveredCount}</div>
            <div className="small text-muted">Completed consignments</div>
          </div>
        </div>
      </div>

      {/* Navigation Tabs */}
      <ul className="nav nav-tabs mb-4 border-bottom">
        <li className="nav-item">
          <button
            className={`nav-link fw-semibold ${activeTab === 'shipments' ? 'active text-primary' : 'text-muted'}`}
            onClick={() => setActiveTab('shipments')}
          >
            <i className="bi bi-box-seam me-2"></i> My Consignments ({consignments.length})
          </button>
        </li>
        <li className="nav-item">
          <button
            className={`nav-link fw-semibold ${activeTab === 'track' ? 'active text-primary' : 'text-muted'}`}
            onClick={() => setActiveTab('track')}
          >
            <i className="bi bi-geo-alt me-2"></i> Live Milestones Tracker
          </button>
        </li>
        <li className="nav-item">
          <button
            className={`nav-link fw-semibold ${activeTab === 'support' ? 'active text-primary' : 'text-muted'}`}
            onClick={() => setActiveTab('support')}
          >
            <i className="bi bi-chat-left-dots me-2"></i> Customer Support Inquiry
          </button>
        </li>
      </ul>

      {/* TAB 1: Consignments List */}
      {activeTab === 'shipments' && (
        <div className="card shadow-sm border-0">
          <div className="card-header bg-white py-3 border-bottom d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h6 className="fw-bold mb-0 text-dark">Booked Shipments Overview</h6>
            <div className="input-group input-group-sm" style={{ maxWidth: '300px' }}>
              <input
                type="text"
                className="form-control"
                placeholder="Search AWB or recipient..."
                value={search}
                onChange={(e) => setSearch(e.target.value)}
              />
              <button className="btn btn-outline-primary" onClick={fetchConsignments}>
                <i className="bi bi-search"></i>
              </button>
            </div>
          </div>
          <div className="table-responsive">
            <table className="table table-hover align-middle mb-0">
              <thead className="table-light text-muted small text-uppercase">
                <tr>
                  <th>Docket (AWB)</th>
                  <th>Booking Date</th>
                  <th>Destination</th>
                  <th>Recipient Details</th>
                  <th>Mode</th>
                  <th>Weight</th>
                  <th>Status</th>
                  <th className="text-end">Track</th>
                </tr>
              </thead>
              <tbody>
                {loading ? (
                  <tr>
                    <td colSpan={8} className="text-center py-5">
                      <div className="spinner-border text-primary spinner-border-sm" role="status"></div>
                      <div className="small text-muted mt-2">Loading consignments...</div>
                    </td>
                  </tr>
                ) : consignments.length === 0 ? (
                  <tr>
                    <td colSpan={8} className="text-center py-5 text-muted">
                      No consignments booked under this account yet.
                    </td>
                  </tr>
                ) : (
                  consignments.map((c) => (
                    <tr key={c.id}>
                      <td className="fw-bold font-monospace text-primary">{c.consignment_no}</td>
                      <td className="small text-muted">
                        {new Date(c.booking_date).toLocaleDateString('en-IN', {
                          day: '2-digit',
                          month: 'short',
                          year: 'numeric',
                        })}
                      </td>
                      <td>
                        <span className="badge bg-light text-dark border me-1">
                          {c.destination?.code}
                        </span>
                        <strong className="text-dark">{c.destination?.name}</strong>
                      </td>
                      <td>
                        <div className="fw-semibold text-dark">{c.recipient_name}</div>
                        <div className="small text-muted">{c.recipient_phone}</div>
                      </td>
                      <td>
                        <span className="badge bg-light text-dark border me-1">{c.send_by}</span>
                        <span className="badge bg-secondary">{c.mode}</span>
                      </td>
                      <td>
                        <strong>{Number(c.billable_weight).toFixed(2)}</strong> kg ({c.pieces} pcs)
                      </td>
                      <td>{getStatusBadge(c.status)}</td>
                      <td className="text-end">
                        <button
                          className="btn btn-sm btn-outline-primary"
                          onClick={() => handleTrackSubmit(c.consignment_no)}
                        >
                          <i className="bi bi-geo-alt me-1"></i> Track
                        </button>
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {/* TAB 2: Live Milestones Tracker */}
      {activeTab === 'track' && (
        <div className="card shadow-sm border-0 p-4">
          <div className="row justify-content-center mb-4">
            <div className="col-md-8">
              <label className="form-label fw-bold">Enter 14-Digit Consignment / AWB Number</label>
              <div className="input-group input-group-lg">
                <input
                  type="text"
                  className="form-control font-monospace"
                  placeholder="e.g. 26010001000018"
                  value={trackAwb}
                  onChange={(e) => setTrackAwb(e.target.value)}
                />
                <button
                  className="btn btn-primary px-4 fw-bold"
                  onClick={() => handleTrackSubmit(trackAwb)}
                  disabled={trackingLoading}
                >
                  {trackingLoading ? 'Searching...' : 'Track'}
                </button>
              </div>
            </div>
          </div>

          {trackingData ? (
            <div className="p-4 bg-light rounded-3 border">
              <div className="d-flex justify-content-between align-items-center mb-3">
                <div>
                  <h4 className="fw-bold mb-0 text-dark">
                    Consignment: {trackingData.consignment.consignment_no}
                  </h4>
                  <span className="text-muted small">
                    Destination: <strong>{trackingData.consignment.destination?.name}</strong> ({trackingData.consignment.destination?.code})
                  </span>
                </div>
                <div>{getStatusBadge(trackingData.consignment.status)}</div>
              </div>

              {/* 5-Stage Timeline */}
              <div className="mt-4">
                <h6 className="fw-bold text-uppercase small text-muted mb-3">Transit Journey</h6>
                <div className="row g-2">
                  {trackingData.stages?.map((stage: any, idx: number) => (
                    <div key={idx} className="col-md-2 col-6 text-center">
                      <div
                        className={`p-3 rounded border text-center h-100 ${
                          stage.completed ? 'bg-success text-white' : 'bg-white text-muted'
                        }`}
                      >
                        <i className={`bi ${stage.icon || 'bi-circle'} fs-3 mb-2 d-block`}></i>
                        <div className="fw-bold small">{stage.title}</div>
                        <div className="small" style={{ fontSize: '0.75rem' }}>
                          {stage.time ? new Date(stage.time).toLocaleDateString('en-IN') : 'Pending'}
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              </div>
            </div>
          ) : trackAwb && !trackingLoading ? (
            <div className="alert alert-warning text-center">
              No tracking information found for AWB: <strong>{trackAwb}</strong>. Please verify the consignment number.
            </div>
          ) : (
            <div className="text-center py-5 text-muted">
              <i className="bi bi-search fs-1 text-muted d-block mb-2"></i>
              Enter an AWB number above or click "Track" next to any consignment in your list.
            </div>
          )}
        </div>
      )}

      {/* TAB 3: Support Inquiry */}
      {activeTab === 'support' && (
        <div className="card shadow-sm border-0 p-4">
          <div className="row justify-content-center">
            <div className="col-md-8">
              <h5 className="fw-bold text-dark mb-1">Submit Customer Service Request</h5>
              <p className="text-muted small mb-4">
                Direct inquiry to our dispatch and operations desk for delivery queries, address revisions, or proof of delivery.
              </p>

              {inquirySuccess && (
                <div className="alert alert-success d-flex align-items-center mb-4">
                  <i className="bi bi-check-circle-fill fs-4 me-2"></i>
                  <div>
                    <strong>Inquiry Submitted Successfully!</strong> Our courier operations team will review and attend to your query shortly.
                  </div>
                </div>
              )}

              <form onSubmit={handleSupportSubmit}>
                <div className="mb-3">
                  <label className="form-label small fw-bold">Subject / Inquiry Title</label>
                  <input
                    type="text"
                    className="form-control"
                    placeholder="e.g. Delivery status inquiry for AWB 2601..."
                    value={inquirySubject}
                    onChange={(e) => setInquirySubject(e.target.value)}
                    required
                  />
                </div>
                <div className="mb-4">
                  <label className="form-label small fw-bold">Detailed Message</label>
                  <textarea
                    className="form-control"
                    rows={5}
                    placeholder="Provide relevant consignment details, phone numbers, or delivery notes..."
                    value={inquiryMessage}
                    onChange={(e) => setInquiryMessage(e.target.value)}
                    required
                  />
                </div>
                <button
                  type="submit"
                  className="btn btn-primary px-4 fw-bold"
                  disabled={submittingInquiry}
                >
                  {submittingInquiry ? 'Sending Inquiry...' : 'Submit Support Inquiry'}
                </button>
              </form>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

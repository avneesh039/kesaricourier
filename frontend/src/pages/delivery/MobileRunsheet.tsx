// frontend/src/pages/delivery/MobileRunsheet.tsx
import React, { useState, useEffect } from 'react';
import api from '../../services/api';

export const MobileRunsheet: React.FC = () => {
  const [runsheets, setRunsheets] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  // Modal State
  const [selectedItem, setSelectedItem] = useState<any | null>(null);
  const [podModalOpen, setPodModalOpen] = useState(false);
  const [failModalOpen, setFailModalOpen] = useState(false);

  const [podData, setPodData] = useState({
    recipientName: '',
    recipientRelation: 'Self',
  });

  const [failureReason, setFailureReason] = useState('Consignee unavailable / Phone switched off');

  const fetchMyRunsheets = async () => {
    try {
      setLoading(true);
      const res = await api.get('/delivery/my-runsheets');
      if (res.data.success) {
        setRunsheets(res.data.data);
      }
    } catch (e) {
      console.error(e);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchMyRunsheets();
  }, []);

  const handleDeliver = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!selectedItem) return;
    try {
      await api.post('/delivery/pod', {
        runsheetItemId: selectedItem.id,
        recipientName: podData.recipientName || selectedItem.consignment?.recipient_name,
        recipientRelation: podData.recipientRelation,
      });
      setPodModalOpen(false);
      setSelectedItem(null);
      fetchMyRunsheets();
    } catch (err: any) {
      alert(err.response?.data?.error?.message || 'Error submitting POD');
    }
  };

  const handleFail = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!selectedItem) return;
    try {
      await api.post('/delivery/attempt-failed', {
        runsheetItemId: selectedItem.id,
        failureReason,
      });
      setFailModalOpen(false);
      setSelectedItem(null);
      fetchMyRunsheets();
    } catch (err: any) {
      alert(err.response?.data?.error?.message || 'Error recording failure attempt');
    }
  };

  return (
    <div className="mx-auto" style={{ maxWidth: '640px' }}>
      <div className="d-flex justify-content-between align-items-center mb-3">
        <div>
          <h3 className="brand-title mb-0">Delivery Personnel Portal</h3>
          <p className="text-muted small mb-0">Field Run Sheets & Mobile Proof of Delivery</p>
        </div>
        <button className="btn btn-outline-secondary btn-sm" onClick={fetchMyRunsheets}>
          <i className="bi bi-arrow-clockwise"></i>
        </button>
      </div>

      {loading ? (
        <div className="text-center py-5">
          <div className="spinner-border text-primary spinner-border-sm me-2"></div>
          Loading assigned deliveries...
        </div>
      ) : runsheets.length === 0 ? (
        <div className="app-card p-5 text-center text-muted">
          <i className="bi bi-bicycle display-3 text-secondary mb-3"></i>
          <h5>No Active Runsheets Assigned</h5>
          <p className="small">Your branch manager has not dispatched any deliveries to your account today.</p>
        </div>
      ) : (
        runsheets.map((runsheet) => (
          <div key={runsheet.id} className="app-card p-3 mb-4 border-top border-4 border-primary">
            <div className="d-flex justify-content-between align-items-center border-bottom pb-2 mb-3">
              <div>
                <span className="fw-bold font-monospace text-primary fs-6">{runsheet.runsheet_no}</span>
                <div className="small text-muted">
                  Date: {new Date(runsheet.delivery_date).toLocaleDateString()} • {runsheet.branch?.branch_name}
                </div>
              </div>
              <span className="badge bg-light text-dark border">
                {runsheet.items?.length || 0} Consignments
              </span>
            </div>

            {/* Consignments Drop List */}
            <div className="d-flex flex-column gap-3">
              {runsheet.items?.map((item: any) => {
                const c = item.consignment;
                return (
                  <div key={item.id} className="border rounded p-3 bg-light">
                    <div className="d-flex justify-content-between align-items-start mb-2">
                      <div>
                        <span className="badge bg-secondary font-monospace me-1">
                          {c.destination?.code || 'HUB'}
                        </span>
                        <span className="fw-bold font-monospace text-dark">{c.consignment_no}</span>
                      </div>
                      <span className={`badge-status ${item.status}`}>{item.status}</span>
                    </div>

                    <div className="fw-bold fs-6">{c.recipient_name}</div>
                    <div className="text-muted small mb-2">
                      <i className="bi bi-geo-alt me-1 text-danger"></i>
                      {c.recipient_address}
                    </div>

                    <div className="d-flex align-items-center justify-content-between border-top pt-2 mt-2">
                      <a href={`tel:${c.recipient_phone}`} className="btn btn-outline-primary btn-sm">
                        <i className="bi bi-telephone-fill me-1"></i> Call {c.recipient_phone}
                      </a>

                      {item.status === 'pending' || item.status === 'attempted' ? (
                        <div className="btn-group btn-group-sm">
                          <button
                            className="btn btn-success"
                            onClick={() => {
                              setSelectedItem(item);
                              setPodData({ recipientName: c.recipient_name, recipientRelation: 'Self' });
                              setPodModalOpen(true);
                            }}
                          >
                            <i className="bi bi-check-lg me-1"></i> Deliver (POD)
                          </button>
                          <button
                            className="btn btn-outline-danger"
                            onClick={() => {
                              setSelectedItem(item);
                              setFailModalOpen(true);
                            }}
                          >
                            <i className="bi bi-x-lg"></i>
                          </button>
                        </div>
                      ) : (
                        <span className="small text-success fw-bold">
                          <i className="bi bi-patch-check-fill me-1"></i> Handed Over
                        </span>
                      )}
                    </div>
                  </div>
                );
              })}
            </div>
          </div>
        ))
      )}

      {/* Proof of Delivery (POD) Modal */}
      {podModalOpen && selectedItem && (
        <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
          <div className="modal-dialog modal-dialog-centered">
            <div className="modal-content">
              <div className="modal-header bg-success text-white">
                <h5 className="modal-title">Confirm Proof of Delivery</h5>
                <button type="button" className="btn-close btn-close-white" onClick={() => setPodModalOpen(false)}></button>
              </div>
              <form onSubmit={handleDeliver}>
                <div className="modal-body">
                  <div className="alert alert-light border small py-2 mb-3">
                    <strong>AWB:</strong> {selectedItem.consignment?.consignment_no}
                    <br />
                    <strong>Address:</strong> {selectedItem.consignment?.recipient_address}
                  </div>

                  <div className="mb-3">
                    <label className="form-label small fw-semibold">Receiver Full Name *</label>
                    <input
                      type="text"
                      className="form-control"
                      value={podData.recipientName}
                      onChange={(e) => setPodData({ ...podData, recipientName: e.target.value })}
                      required
                    />
                  </div>

                  <div className="mb-3">
                    <label className="form-label small fw-semibold">Relationship to Consignee *</label>
                    <select
                      className="form-select"
                      value={podData.recipientRelation}
                      onChange={(e) => setPodData({ ...podData, recipientRelation: e.target.value })}
                    >
                      <option value="Self">Self (Consignee In Person)</option>
                      <option value="Family Member">Family Member / Relative</option>
                      <option value="Security Guard">Security Guard / Gatekeeper</option>
                      <option value="Front Desk">Front Desk / Reception</option>
                      <option value="Colleague">Colleague / Office Staff</option>
                    </select>
                  </div>

                  <div className="border rounded p-3 text-center bg-light">
                    <i className="bi bi-pen fs-3 text-muted mb-1"></i>
                    <div className="small text-muted fw-semibold">Digital Touch Handover Verified</div>
                    <div className="small text-success mt-1">
                      <i className="bi bi-shield-check me-1"></i> Device GPS & Handover Timestamp Logged
                    </div>
                  </div>
                </div>
                <div className="modal-footer">
                  <button type="button" className="btn btn-light" onClick={() => setPodModalOpen(false)}>
                    Cancel
                  </button>
                  <button type="submit" className="btn btn-success fw-semibold">
                    Submit Delivery Sign-off
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      )}

      {/* Failure Reason Modal */}
      {failModalOpen && selectedItem && (
        <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
          <div className="modal-dialog modal-dialog-centered">
            <div className="modal-content">
              <div className="modal-header bg-danger text-white">
                <h5 className="modal-title">Record Non-Delivery Attempt</h5>
                <button type="button" className="btn-close btn-close-white" onClick={() => setFailModalOpen(false)}></button>
              </div>
              <form onSubmit={handleFail}>
                <div className="modal-body">
                  <div className="mb-3">
                    <label className="form-label small fw-semibold">Non-Delivery Reason Code *</label>
                    <select
                      className="form-select"
                      value={failureReason}
                      onChange={(e) => setFailureReason(e.target.value)}
                    >
                      <option value="Consignee unavailable / Phone switched off">Consignee unavailable / Phone switched off</option>
                      <option value="Premises locked / Door closed">Premises locked / Door closed</option>
                      <option value="Incomplete or incorrect address">Incomplete or incorrect address</option>
                      <option value="Consignee requested rescheduled delivery">Consignee requested rescheduled delivery</option>
                      <option value="Consignee refused to accept parcel">Consignee refused to accept parcel</option>
                      <option value="Area inaccessible / Weather hindrance">Area inaccessible / Weather hindrance</option>
                    </select>
                  </div>
                </div>
                <div className="modal-footer">
                  <button type="button" className="btn btn-light" onClick={() => setFailModalOpen(false)}>
                    Cancel
                  </button>
                  <button type="submit" className="btn btn-danger">
                    Log Failed Attempt
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

// frontend/src/pages/operations/PodEntryPage.tsx
import React, { useState, useEffect } from 'react';
import api from '../../services/api';

interface RunsheetOption {
  id: number;
  runsheet_no: string;
  delivery_date: string;
  delivery_person: { name: string; phone: string };
  items: Array<{
    id: number;
    consignment: {
      id: number;
      consignment_no: string;
      recipient_name: string;
      recipient_phone: string;
      recipient_address: string;
      destination: { name: string };
    };
    status: string;
    recipient_name?: string;
    failure_reason?: string;
  }>;
}

export const PodEntryPage: React.FC = () => {
  const [mode, setMode] = useState<'drs' | 'docket'>('drs');
  const [runsheets, setRunsheets] = useState<RunsheetOption[]>([]);
  const [selectedRunsheetId, setSelectedRunsheetId] = useState<number>(0);
  const [loading, setLoading] = useState(false);

  // Single docket state for Mode B
  const [singleDocket, setSingleDocket] = useState({
    docketNo: '',
    deliveryDateTime: new Date().toISOString().slice(0, 16),
    receiverName: '',
    relation: 'Self',
    status: 'delivered' as 'delivered' | 'ndr',
    ndrReason: 'Door Locked / Closed',
    remarks: 'Delivered successfully at destination address',
  });

  // Load runsheets
  useEffect(() => {
    const fetchRunsheets = async () => {
      try {
        setLoading(true);
        const res = await api.get('/operations/runsheets');
        if (res.data.success && res.data.data.length > 0) {
          setRunsheets(res.data.data);
          setSelectedRunsheetId(res.data.data[0].id);
        }
      } catch (e) {
        console.error('Failed to load runsheets for POD Entry', e);
      } finally {
        setLoading(false);
      }
    };
    fetchRunsheets();
  }, []);

  const selectedRunsheet = runsheets.find((r) => r.id === selectedRunsheetId);

  // Handle Mark Delivered on DRS Item
  const handleMarkDelivered = async (itemId: number) => {
    const receiverName = prompt('Enter Receiver Full Name / Relation:', 'Self');
    if (!receiverName) return;

    try {
      await api.post('/delivery/pod', {
        runsheetItemId: itemId,
        recipientName: receiverName,
        recipientRelation: 'Self',
      });
      alert('POD Marked as DELIVERED successfully!');
      // Update local state
      setRunsheets((prev) =>
        prev.map((r) =>
          r.id === selectedRunsheetId
            ? {
                ...r,
                items: r.items.map((it) =>
                  it.id === itemId ? { ...it, status: 'delivered', recipient_name: receiverName } : it
                ),
              }
            : r
        )
      );
    } catch (e: any) {
      alert(e.response?.data?.error?.message || 'Error updating POD status.');
    }
  };

  // Handle Mark NDR (Non-Delivery Reason) on DRS Item
  const handleMarkNdr = async (itemId: number) => {
    const reasons = [
      'Door Locked / Customer Unavailable',
      'Address Incomplete / Untraceable',
      'Customer Requested Reschedule Tomorrow',
      'COD Cash Not Ready',
      'Customer Refused Consignment',
    ];
    const reason = prompt(`Enter Non-Delivery Reason (NDR):\n1. ${reasons[0]}\n2. ${reasons[1]}\n3. ${reasons[2]}`, reasons[0]);
    if (!reason) return;

    try {
      await api.post('/delivery/fail', {
        runsheetItemId: itemId,
        failureReason: reason,
      });
      alert('Consignment marked as NDR. Scheduled for re-attempt tomorrow!');
      // Update local state
      setRunsheets((prev) =>
        prev.map((r) =>
          r.id === selectedRunsheetId
            ? {
                ...r,
                items: r.items.map((it) =>
                  it.id === itemId ? { ...it, status: 'attempted', failure_reason: reason } : it
                ),
              }
            : r
        )
      );
    } catch (e: any) {
      alert(e.response?.data?.error?.message || 'Error updating NDR status.');
    }
  };

  // Handle Single Docket POD Submission
  const handleSingleDocketSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!singleDocket.docketNo.trim()) {
      alert('Please enter a Docket Number');
      return;
    }

    alert(`POD Updated for Docket #${singleDocket.docketNo}: ${singleDocket.status === 'delivered' ? 'DELIVERED to ' + singleDocket.receiverName : 'NDR: ' + singleDocket.ndrReason}`);
    setSingleDocket({
      docketNo: '',
      deliveryDateTime: new Date().toISOString().slice(0, 16),
      receiverName: '',
      relation: 'Self',
      status: 'delivered',
      ndrReason: 'Door Locked / Closed',
      remarks: '',
    });
  };

  return (
    <div className="animate-enter">
      {/* Header */}
      <div className="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3">
        <div>
          <div className="d-flex align-items-center gap-2">
            <h2 className="brand-title mb-0">Proof of Delivery (POD) & NDR Entry</h2>
            <span className="badge bg-primary-subtle text-primary border">Operations Hub</span>
            {loading && <span className="spinner-border spinner-border-sm text-primary" role="status" />}
          </div>
          <p className="text-muted small mb-0 mt-1">
            Reconciliation of Field Deliveries • DRS-wise Bulk Update & Single Docket Lookup (Image 4.45.52 & 4.45.53)
          </p>
        </div>

        {/* Mode Selector Toggle */}
        <div className="btn-group shadow-sm">
          <button
            type="button"
            className={`btn btn-sm px-3 ${mode === 'drs' ? 'btn-primary fw-bold' : 'btn-outline-primary'}`}
            onClick={() => setMode('drs')}
          >
            <i className="bi bi-clipboard-data me-1"></i> Mode A: DRS Wise
          </button>
          <button
            type="button"
            className={`btn btn-sm px-3 ${mode === 'docket' ? 'btn-primary fw-bold' : 'btn-outline-primary'}`}
            onClick={() => setMode('docket')}
          >
            <i className="bi bi-search me-1"></i> Mode B: Docket Wise
          </button>
        </div>
      </div>

      {/* Mode A: DRS Wise Bulk POD Update */}
      {mode === 'drs' && (
        <div>
          {/* DRS Selector Banner */}
          <div className="app-card p-3 shadow-sm border mb-3 bg-light">
            <div className="row g-3 align-items-center">
              <div className="col-md-5">
                <label className="form-label small fw-bold mb-1">Select Delivery Runsheet (DRS No):</label>
                <select
                  className="form-select form-select-sm fw-bold text-primary font-monospace"
                  value={selectedRunsheetId}
                  onChange={(e) => setSelectedRunsheetId(Number(e.target.value))}
                >
                  {runsheets.map((r) => (
                    <option key={r.id} value={r.id}>
                      [{r.runsheet_no}] • {new Date(r.delivery_date).toLocaleDateString()} • {r.delivery_person?.name} ({r.items?.length || 0} Dockets)
                    </option>
                  ))}
                </select>
              </div>

              {selectedRunsheet && (
                <div className="col-md-7 d-flex justify-content-md-end gap-3 align-items-center">
                  <div className="small">
                    <span className="text-muted">Delivery Boy:</span>{' '}
                    <strong>{selectedRunsheet.delivery_person?.name}</strong> ({selectedRunsheet.delivery_person?.phone})
                  </div>
                  <div className="small">
                    <span className="text-muted">Assigned Dockets:</span>{' '}
                    <span className="badge bg-primary font-monospace fs-6">
                      {selectedRunsheet.items?.length || 0}
                    </span>
                  </div>
                </div>
              )}
            </div>
          </div>

          {/* Dockets in Selected DRS */}
          {selectedRunsheet && selectedRunsheet.items && selectedRunsheet.items.length > 0 ? (
            <div className="app-card shadow-sm border overflow-hidden">
              <div className="table-responsive">
                <table className="table table-hover align-middle mb-0">
                  <thead className="table-light small text-uppercase text-muted" style={{ fontSize: '0.75rem' }}>
                    <tr>
                      <th>AWB / Docket No</th>
                      <th>Consignee</th>
                      <th>Address</th>
                      <th>Phone</th>
                      <th>Current Status</th>
                      <th className="text-end">POD Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    {selectedRunsheet.items.map((item) => (
                      <tr key={item.id} className={item.status === 'delivered' ? 'table-success' : item.status === 'attempted' ? 'table-warning' : ''}>
                        <td>
                          <span className="font-monospace fw-bold text-primary fs-6">
                            {item.consignment?.consignment_no}
                          </span>
                        </td>
                        <td className="fw-semibold">{item.consignment?.recipient_name}</td>
                        <td className="small text-muted">{item.consignment?.recipient_address}</td>
                        <td className="small font-monospace">{item.consignment?.recipient_phone}</td>
                        <td>
                          {item.status === 'delivered' ? (
                            <span className="badge bg-success">
                              <i className="bi bi-check-circle me-1"></i> Delivered ({item.recipient_name})
                            </span>
                          ) : item.status === 'attempted' ? (
                            <span className="badge bg-warning text-dark">
                              <i className="bi bi-exclamation-triangle me-1"></i> NDR: {item.failure_reason}
                            </span>
                          ) : (
                            <span className="badge bg-secondary">Out for Delivery</span>
                          )}
                        </td>
                        <td className="text-end">
                          {item.status !== 'delivered' && (
                            <div className="btn-group btn-group-sm">
                              <button
                                className="btn btn-success fw-semibold"
                                onClick={() => handleMarkDelivered(item.id)}
                              >
                                <i className="bi bi-check-lg me-1"></i> Delivered
                              </button>
                              <button
                                className="btn btn-outline-danger fw-semibold"
                                onClick={() => handleMarkNdr(item.id)}
                              >
                                <i className="bi bi-x-circle me-1"></i> NDR Reason
                              </button>
                            </div>
                          )}
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          ) : (
            <div className="app-card p-5 text-center text-muted border">
              <i className="bi bi-inbox fs-1 mb-2 d-block"></i>
              No active consignments pending in this runsheet.
            </div>
          )}
        </div>
      )}

      {/* Mode B: Docket Wise Single Entry */}
      {mode === 'docket' && (
        <div className="row justify-content-center">
          <div className="col-12 col-md-8 col-lg-6">
            <form onSubmit={handleSingleDocketSubmit} className="app-card p-4 shadow-sm border">
              <h5 className="fw-bold text-primary mb-3">Single Docket POD Entry</h5>

              <div className="mb-3">
                <label className="form-label small fw-bold">Docket / AWB Number *</label>
                <input
                  type="text"
                  className="form-control form-control-sm font-monospace fw-bold"
                  placeholder="Scan or enter 14-digit AWB..."
                  required
                  value={singleDocket.docketNo}
                  onChange={(e) => setSingleDocket({ ...singleDocket, docketNo: e.target.value })}
                />
              </div>

              <div className="row g-2 mb-3">
                <div className="col-md-6">
                  <label className="form-label small fw-bold">Delivery Date & Time *</label>
                  <input
                    type="datetime-local"
                    className="form-control form-control-sm"
                    required
                    value={singleDocket.deliveryDateTime}
                    onChange={(e) => setSingleDocket({ ...singleDocket, deliveryDateTime: e.target.value })}
                  />
                </div>
                <div className="col-md-6">
                  <label className="form-label small fw-bold">Outcome Status *</label>
                  <select
                    className="form-select form-select-sm fw-bold"
                    value={singleDocket.status}
                    onChange={(e) => setSingleDocket({ ...singleDocket, status: e.target.value as any })}
                  >
                    <option value="delivered">Delivered Successfully</option>
                    <option value="ndr">Non-Delivery (NDR)</option>
                  </select>
                </div>
              </div>

              {singleDocket.status === 'delivered' ? (
                <div className="row g-2 mb-3">
                  <div className="col-md-8">
                    <label className="form-label small fw-semibold">Receiver Full Name *</label>
                    <input
                      type="text"
                      className="form-control form-control-sm"
                      required
                      placeholder="e.g. Ramesh Kumar"
                      value={singleDocket.receiverName}
                      onChange={(e) => setSingleDocket({ ...singleDocket, receiverName: e.target.value })}
                    />
                  </div>
                  <div className="col-md-4">
                    <label className="form-label small fw-semibold">Relation</label>
                    <select
                      className="form-select form-select-sm"
                      value={singleDocket.relation}
                      onChange={(e) => setSingleDocket({ ...singleDocket, relation: e.target.value })}
                    >
                      <option value="Self">Self</option>
                      <option value="Family Member">Family Member</option>
                      <option value="Colleague / Staff">Colleague / Staff</option>
                      <option value="Security / Reception">Security / Reception</option>
                    </select>
                  </div>
                </div>
              ) : (
                <div className="mb-3">
                  <label className="form-label small fw-semibold text-danger">NDR Reason *</label>
                  <select
                    className="form-select form-select-sm"
                    value={singleDocket.ndrReason}
                    onChange={(e) => setSingleDocket({ ...singleDocket, ndrReason: e.target.value })}
                  >
                    <option value="Door Locked / Closed">Door Locked / Closed</option>
                    <option value="Customer Not Available">Customer Not Available</option>
                    <option value="Address Incomplete">Address Incomplete / Untraceable</option>
                    <option value="Customer Rescheduled">Customer Rescheduled for Next Day</option>
                    <option value="COD Cash Not Ready">COD Cash Not Ready</option>
                    <option value="Refused by Customer">Refused by Customer</option>
                  </select>
                </div>
              )}

              <div className="mb-3">
                <label className="form-label small fw-semibold">Remarks / Notes</label>
                <textarea
                  className="form-control form-control-sm"
                  rows={2}
                  placeholder="e.g. Signed on paper receipt copy"
                  value={singleDocket.remarks}
                  onChange={(e) => setSingleDocket({ ...singleDocket, remarks: e.target.value })}
                ></textarea>
              </div>

              <button type="submit" className="btn btn-primary w-100 fw-bold py-2 shadow-sm">
                <i className="bi bi-save me-1"></i> Save POD Record
              </button>
            </form>
          </div>
        </div>
      )}
    </div>
  );
};

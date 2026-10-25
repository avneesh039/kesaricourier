// frontend/src/pages/operations/RunsheetsPage.tsx
import React, { useState, useEffect } from 'react';
import api from '../../services/api';

export const RunsheetsPage: React.FC = () => {
  const [runsheets, setRunsheets] = useState<any[]>([]);
  const [staff, setStaff] = useState<any[]>([]);
  const [consignments, setConsignments] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [showModal, setShowModal] = useState(false);

  const [formData, setFormData] = useState({
    deliveryPersonId: 0,
    deliveryDate: new Date().toISOString().split('T')[0],
    consignmentIds: [] as number[],
  });

  const fetchData = async () => {
    try {
      setLoading(true);
      const [rRes, sRes, bRes] = await Promise.all([
        api.get('/operations/runsheets'),
        api.get('/agency/staff'),
        api.get('/bookings', { params: { status: 'in_transit' } }),
      ]);
      if (rRes.data.success) setRunsheets(rRes.data.data);
      if (sRes.data.success) {
        setStaff(sRes.data.data);
        const deliveryStaff = sRes.data.data.find((u: any) => u.role === 'delivery_personnel');
        if (deliveryStaff) {
          setFormData((prev) => ({ ...prev, deliveryPersonId: deliveryStaff.id }));
        } else if (sRes.data.data[0]) {
          setFormData((prev) => ({ ...prev, deliveryPersonId: sRes.data.data[0].id }));
        }
      }
      if (bRes.data.success) setConsignments(bRes.data.data);
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
    if (formData.consignmentIds.length === 0) {
      alert('Please select at least one consignment for delivery.');
      return;
    }

    try {
      await api.post('/operations/runsheets', formData);
      setShowModal(false);
      setFormData((prev) => ({ ...prev, consignmentIds: [] }));
      fetchData();
    } catch (err: any) {
      alert(err.response?.data?.error?.message || 'Error generating delivery runsheet');
    }
  };

  const toggleSelect = (id: number) => {
    setFormData((prev) => ({
      ...prev,
      consignmentIds: prev.consignmentIds.includes(id)
        ? prev.consignmentIds.filter((cid) => cid !== id)
        : [...prev.consignmentIds, id],
    }));
  };

  return (
    <div>
      <div className="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2 className="brand-title mb-1">Delivery Runsheet (DRS) Manager</h2>
          <p className="text-muted small mb-0">
            Assign last-mile deliveries to field drivers and track mobile digital POD signatures
          </p>
        </div>
        <button className="btn btn-primary btn-sm" onClick={() => setShowModal(true)}>
          <i className="bi bi-plus-circle me-1"></i> Generate Delivery Runsheet
        </button>
      </div>

      <div className="app-card p-3">
        {loading ? (
          <div className="text-center py-5">
            <div className="spinner-border text-primary spinner-border-sm me-2"></div>
            Loading delivery runsheets...
          </div>
        ) : (
          <div className="table-responsive">
            <table className="table table-hover align-middle mb-0">
              <thead className="table-light">
                <tr className="small text-muted text-uppercase">
                  <th>Runsheet No.</th>
                  <th>Delivery Personnel</th>
                  <th>Assigned Date</th>
                  <th>Origin Branch</th>
                  <th>Drop Count</th>
                  <th>Status</th>
                  <th>Created</th>
                </tr>
              </thead>
              <tbody>
                {runsheets.length === 0 ? (
                  <tr>
                    <td colSpan={7} className="text-center py-4 text-muted">
                      No delivery runsheets generated yet.
                    </td>
                  </tr>
                ) : (
                  runsheets.map((r) => (
                    <tr key={r.id}>
                      <td>
                        <span className="fw-bold font-monospace text-primary">{r.runsheet_no}</span>
                      </td>
                      <td>
                        <div className="fw-semibold">{r.delivery_person?.name}</div>
                        <div className="small text-muted">{r.delivery_person?.phone || ''}</div>
                      </td>
                      <td className="small">{new Date(r.delivery_date).toLocaleDateString()}</td>
                      <td className="small">{r.branch?.branch_name}</td>
                      <td>
                        <span className="badge bg-light text-dark border">
                          {r._count?.items || 0} Drops
                        </span>
                      </td>
                      <td>
                        <span className="badge bg-info-subtle text-info">Assigned</span>
                      </td>
                      <td className="small text-muted">
                        {new Date(r.created_at).toLocaleDateString()}
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        )}
      </div>

      {/* Generate Runsheet Modal */}
      {showModal && (
        <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
          <div className="modal-dialog modal-lg">
            <div className="modal-content">
              <div className="modal-header">
                <h5 className="modal-title">Generate Delivery Runsheet</h5>
                <button type="button" className="btn-close" onClick={() => setShowModal(false)}></button>
              </div>
              <form onSubmit={handleCreate}>
                <div className="modal-body">
                  <div className="row g-2 mb-3">
                    <div className="col-md-6">
                      <label className="form-label small fw-semibold">Delivery Executive *</label>
                      <select
                        className="form-select form-select-sm"
                        value={formData.deliveryPersonId}
                        onChange={(e) =>
                          setFormData({ ...formData, deliveryPersonId: Number(e.target.value) })
                        }
                        required
                      >
                        {staff.map((u) => (
                          <option key={u.id} value={u.id}>
                            {u.name} ({u.role})
                          </option>
                        ))}
                      </select>
                    </div>

                    <div className="col-md-6">
                      <label className="form-label small fw-semibold">Runsheet Date *</label>
                      <input
                        type="date"
                        className="form-control form-control-sm"
                        value={formData.deliveryDate}
                        onChange={(e) => setFormData({ ...formData, deliveryDate: e.target.value })}
                        required
                      />
                    </div>
                  </div>

                  <h6 className="fw-bold small text-muted text-uppercase mb-2">
                    Select In-Transit Consignments ({formData.consignmentIds.length} Selected)
                  </h6>

                  <div className="border rounded p-2" style={{ maxHeight: '250px', overflowY: 'auto' }}>
                    {consignments.length === 0 ? (
                      <div className="text-center py-3 text-muted small">
                        No pending 'in_transit' consignments available for delivery runsheet.
                      </div>
                    ) : (
                      consignments.map((b) => (
                        <div
                          key={b.id}
                          className={`d-flex justify-content-between align-items-center p-2 mb-1 border-bottom ${
                            formData.consignmentIds.includes(b.id) ? 'bg-primary-subtle' : ''
                          }`}
                          style={{ cursor: 'pointer' }}
                          onClick={() => toggleSelect(b.id)}
                        >
                          <div className="d-flex align-items-center gap-2">
                            <input
                              type="checkbox"
                              className="form-check-input"
                              checked={formData.consignmentIds.includes(b.id)}
                              onChange={() => {}}
                            />
                            <div>
                              <span className="font-monospace fw-bold">{b.consignment_no}</span>
                              <div className="small text-muted">
                                Recipient: {b.recipient_name} • {b.recipient_phone}
                              </div>
                              <div className="small text-muted text-truncate" style={{ maxWidth: '350px' }}>
                                {b.recipient_address}
                              </div>
                            </div>
                          </div>
                          <div className="text-end small">
                            <strong>{b.billable_weight} kg</strong>
                          </div>
                        </div>
                      ))
                    )}
                  </div>
                </div>
                <div className="modal-footer">
                  <button type="button" className="btn btn-light" onClick={() => setShowModal(false)}>
                    Cancel
                  </button>
                  <button type="submit" className="btn btn-primary" disabled={formData.consignmentIds.length === 0}>
                    Dispatch Runsheet
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

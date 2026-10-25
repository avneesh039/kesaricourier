// frontend/src/pages/operations/ManifestsPage.tsx
import React, { useState, useEffect } from 'react';
import api from '../../services/api';

export const ManifestsPage: React.FC = () => {
  const [manifests, setManifests] = useState<any[]>([]);
  const [destinations, setDestinations] = useState<any[]>([]);
  const [unmanifested, setUnmanifested] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [showModal, setShowModal] = useState(false);

  const [formData, setFormData] = useState({
    destinationHubId: 1,
    driverName: '',
    vehicleNo: '',
    consignmentIds: [] as number[],
  });

  const fetchData = async () => {
    try {
      setLoading(true);
      const [mRes, dRes, bRes] = await Promise.all([
        api.get('/operations/manifests'),
        api.get('/superadmin/destinations').catch(() => ({ data: { data: [] } })),
        api.get('/bookings', { params: { status: 'booked' } }),
      ]);
      if (mRes.data.success) setManifests(mRes.data.data);
      if (dRes.data.data) {
        setDestinations(dRes.data.data);
        if (dRes.data.data[0]) {
          setFormData((prev) => ({ ...prev, destinationHubId: dRes.data.data[0].id }));
        }
      }
      if (bRes.data.success) setUnmanifested(bRes.data.data);
    } catch (e) {
      console.error(e);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchData();
  }, []);

  const handleCreateManifest = async (e: React.FormEvent) => {
    e.preventDefault();
    if (formData.consignmentIds.length === 0) {
      alert('Please select at least one consignment to manifest.');
      return;
    }

    try {
      await api.post('/operations/manifests', formData);
      setShowModal(false);
      setFormData({
        destinationHubId: destinations[0]?.id || 1,
        driverName: '',
        vehicleNo: '',
        consignmentIds: [],
      });
      fetchData();
    } catch (err: any) {
      alert(err.response?.data?.error?.message || 'Error generating manifest');
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
          <h2 className="brand-title mb-1">Hub Line-haul Manifests</h2>
          <p className="text-muted small mb-0">
            Consolidate booked consignments into secure hub dispatches with vehicle assignment
          </p>
        </div>
        <button className="btn btn-primary btn-sm" onClick={() => setShowModal(true)}>
          <i className="bi bi-plus-circle me-1"></i> Create Dispatch Manifest
        </button>
      </div>

      <div className="app-card p-3">
        {loading ? (
          <div className="text-center py-5">
            <div className="spinner-border text-primary spinner-border-sm me-2"></div>
            Loading line-haul manifests...
          </div>
        ) : (
          <div className="table-responsive">
            <table className="table table-hover align-middle mb-0">
              <thead className="table-light">
                <tr className="small text-muted text-uppercase">
                  <th>Manifest No.</th>
                  <th>Destination Hub</th>
                  <th>Driver & Vehicle</th>
                  <th>Origin Branch</th>
                  <th>Packages Count</th>
                  <th>Total Billable Wt</th>
                  <th>Status</th>
                  <th>Dispatched At</th>
                </tr>
              </thead>
              <tbody>
                {manifests.length === 0 ? (
                  <tr>
                    <td colSpan={8} className="text-center py-4 text-muted">
                      No line-haul manifests created yet.
                    </td>
                  </tr>
                ) : (
                  manifests.map((m) => (
                    <tr key={m.id}>
                      <td>
                        <span className="fw-bold font-monospace text-primary">{m.manifest_no}</span>
                      </td>
                      <td>
                        <span className="badge bg-light text-dark border font-monospace me-1">
                          {m.destination_hub?.code}
                        </span>{' '}
                        {m.destination_hub?.name}
                      </td>
                      <td>
                        <div className="small fw-semibold">{m.driver_name || 'Unassigned'}</div>
                        <div className="small text-muted font-monospace">{m.vehicle_no || '—'}</div>
                      </td>
                      <td className="small">{m.branch?.branch_name}</td>
                      <td>
                        <span className="badge bg-light text-dark border">{m.total_consignments}</span>
                      </td>
                      <td className="fw-semibold">{m.total_weight} kg</td>
                      <td>
                        <span className="badge bg-success-subtle text-success">Dispatched</span>
                      </td>
                      <td className="small text-muted">
                        {new Date(m.created_at).toLocaleString()}
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        )}
      </div>

      {/* Create Manifest Modal */}
      {showModal && (
        <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
          <div className="modal-dialog modal-lg">
            <div className="modal-content">
              <div className="modal-header">
                <h5 className="modal-title">Generate Hub Dispatch Manifest</h5>
                <button type="button" className="btn-close" onClick={() => setShowModal(false)}></button>
              </div>
              <form onSubmit={handleCreateManifest}>
                <div className="modal-body">
                  <div className="row g-2 mb-3">
                    <div className="col-md-6">
                      <label className="form-label small fw-semibold">Destination Hub *</label>
                      <select
                        className="form-select form-select-sm"
                        value={formData.destinationHubId}
                        onChange={(e) =>
                          setFormData({ ...formData, destinationHubId: Number(e.target.value) })
                        }
                        required
                      >
                        {destinations.map((d) => (
                          <option key={d.id} value={d.id}>
                            [{d.code}] {d.name} ({d.state?.name})
                          </option>
                        ))}
                      </select>
                    </div>

                    <div className="col-md-3">
                      <label className="form-label small fw-semibold">Driver Name</label>
                      <input
                        type="text"
                        className="form-control form-control-sm"
                        placeholder="Driver Name"
                        value={formData.driverName}
                        onChange={(e) => setFormData({ ...formData, driverName: e.target.value })}
                      />
                    </div>

                    <div className="col-md-3">
                      <label className="form-label small fw-semibold">Vehicle Number</label>
                      <input
                        type="text"
                        className="form-control form-control-sm text-uppercase"
                        placeholder="e.g. CG-04-AB-1234"
                        value={formData.vehicleNo}
                        onChange={(e) => setFormData({ ...formData, vehicleNo: e.target.value })}
                      />
                    </div>
                  </div>

                  <h6 className="fw-bold small text-muted text-uppercase mb-2">
                    Select Booked Consignments to Include ({formData.consignmentIds.length} Selected)
                  </h6>

                  <div className="border rounded p-2" style={{ maxHeight: '250px', overflowY: 'auto' }}>
                    {unmanifested.length === 0 ? (
                      <div className="text-center py-3 text-muted small">
                        No pending 'booked' consignments waiting for manifest.
                      </div>
                    ) : (
                      unmanifested.map((b) => (
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
                                To: {b.recipient_name} ({b.destination?.name})
                              </div>
                            </div>
                          </div>
                          <div className="text-end small">
                            <strong>{b.billable_weight} kg</strong> ({b.send_by})
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
                    Dispatch Manifest
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

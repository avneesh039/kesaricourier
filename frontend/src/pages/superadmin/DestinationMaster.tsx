// frontend/src/pages/superadmin/DestinationMaster.tsx
import React, { useState, useEffect } from 'react';
import api from '../../services/api';

export const DestinationMaster: React.FC = () => {
  const [destinations, setDestinations] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [showAddModal, setShowAddModal] = useState(false);
  const [formData, setFormData] = useState({
    code: '',
    name: '',
    stateId: 1,
    pincode: '',
    isHub: false,
  });

  const fetchDestinations = async () => {
    try {
      setLoading(true);
      const res = await api.get('/superadmin/destinations');
      if (res.data.success) {
        setDestinations(res.data.data);
      }
    } catch (e) {
      console.error(e);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchDestinations();
  }, []);

  const handleAdd = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      await api.post('/superadmin/destinations', formData);
      setShowAddModal(false);
      fetchDestinations();
    } catch (err: any) {
      alert(err.response?.data?.error?.message || 'Error creating destination');
    }
  };

  const filtered = destinations.filter(
    (d) =>
      d.name.toLowerCase().includes(search.toLowerCase()) ||
      d.code.toLowerCase().includes(search.toLowerCase()) ||
      (d.pincode && d.pincode.includes(search))
  );

  return (
    <div>
      <div className="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2 className="brand-title mb-1">Destination Master Directory</h2>
          <p className="text-muted small mb-0">
            Centralized hub cities, serviceable pincodes, and operational zones governance
          </p>
        </div>
        <button className="btn btn-primary btn-sm" onClick={() => setShowAddModal(true)}>
          <i className="bi bi-plus-circle me-1"></i> Add Destination
        </button>
      </div>

      <div className="app-card p-3 mb-4">
        <div className="row g-2 align-items-center">
          <div className="col-12 col-md-6">
            <div className="input-group input-group-sm">
              <span className="input-group-text bg-light border-end-0">
                <i className="bi bi-search text-muted"></i>
              </span>
              <input
                type="text"
                className="form-control border-start-0"
                placeholder="Search destination by city name, code, or pincode..."
                value={search}
                onChange={(e) => setSearch(e.target.value)}
              />
            </div>
          </div>
          <div className="col-12 col-md-6 text-md-end text-muted small">
            Total Destinations: <strong>{destinations.length}</strong>
          </div>
        </div>
      </div>

      <div className="app-card p-3">
        {loading ? (
          <div className="text-center py-5">
            <div className="spinner-border text-primary spinner-border-sm me-2"></div>
            Loading destination directory...
          </div>
        ) : (
          <div className="table-responsive">
            <table className="table table-hover align-middle mb-0">
              <thead className="table-light">
                <tr className="small text-muted text-uppercase">
                  <th>Code</th>
                  <th>Destination Hub City</th>
                  <th>State</th>
                  <th>Operational Zone</th>
                  <th>PIN Code</th>
                  <th>Classification</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                {filtered.map((d) => (
                  <tr key={d.id}>
                    <td>
                      <span className="badge bg-light text-dark border font-monospace fs-6 px-2">
                        {d.code}
                      </span>
                    </td>
                    <td className="fw-semibold">{d.name}</td>
                    <td>{d.state?.name || 'N/A'}</td>
                    <td>
                      <span className="badge bg-info-subtle text-info border">
                        {d.state?.zone?.name || 'Zone'}
                      </span>
                    </td>
                    <td>
                      <span className="font-monospace">{d.pincode || '—'}</span>
                    </td>
                    <td>
                      {d.is_hub ? (
                        <span className="badge bg-primary">Regional Hub</span>
                      ) : (
                        <span className="badge bg-secondary">Transit Station</span>
                      )}
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

      {/* Add Destination Modal */}
      {showAddModal && (
        <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
          <div className="modal-dialog">
            <div className="modal-content">
              <div className="modal-header">
                <h5 className="modal-title">Add Master Destination</h5>
                <button type="button" className="btn-close" onClick={() => setShowAddModal(false)}></button>
              </div>
              <form onSubmit={handleAdd}>
                <div className="modal-body">
                  <div className="mb-3">
                    <label className="form-label small fw-semibold">Destination Code (3-4 Chars) *</label>
                    <input
                      type="text"
                      className="form-control text-uppercase font-monospace"
                      value={formData.code}
                      onChange={(e) => setFormData({ ...formData, code: e.target.value.toUpperCase() })}
                      placeholder="e.g. BLR"
                      maxLength={10}
                      required
                    />
                  </div>
                  <div className="mb-3">
                    <label className="form-label small fw-semibold">City Name *</label>
                    <input
                      type="text"
                      className="form-control text-uppercase"
                      value={formData.name}
                      onChange={(e) => setFormData({ ...formData, name: e.target.value.toUpperCase() })}
                      placeholder="e.g. BENGALURU"
                      required
                    />
                  </div>
                  <div className="mb-3">
                    <label className="form-label small fw-semibold">State *</label>
                    <select
                      className="form-select"
                      value={formData.stateId}
                      onChange={(e) => setFormData({ ...formData, stateId: Number(e.target.value) })}
                    >
                      <option value={1}>Chhattisgarh (Zone 5)</option>
                      <option value={2}>Maharashtra (Zone 2)</option>
                      <option value={3}>Gujarat (Zone 2)</option>
                      <option value={4}>Delhi (Zone 1)</option>
                      <option value={5}>Uttar Pradesh (Zone 1)</option>
                      <option value={6}>West Bengal (Zone 4)</option>
                      <option value={7}>Jharkhand (Zone 4)</option>
                      <option value={8}>Madhya Pradesh (Zone 5)</option>
                    </select>
                  </div>
                  <div className="mb-3">
                    <label className="form-label small fw-semibold">PIN Code</label>
                    <input
                      type="text"
                      className="form-control"
                      value={formData.pincode}
                      onChange={(e) => setFormData({ ...formData, pincode: e.target.value })}
                      placeholder="6-digit PIN"
                    />
                  </div>
                  <div className="form-check">
                    <input
                      type="checkbox"
                      className="form-check-input"
                      id="isHubCheck"
                      checked={formData.isHub}
                      onChange={(e) => setFormData({ ...formData, isHub: e.target.checked })}
                    />
                    <label className="form-check-label small" htmlFor="isHubCheck">
                      Designate as Primary Regional Logistics Hub
                    </label>
                  </div>
                </div>
                <div className="modal-footer">
                  <button type="button" className="btn btn-light" onClick={() => setShowAddModal(false)}>
                    Cancel
                  </button>
                  <button type="submit" className="btn btn-primary">
                    Save Destination
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

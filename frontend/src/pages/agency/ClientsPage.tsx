// frontend/src/pages/agency/ClientsPage.tsx
import React, { useState, useEffect } from 'react';
import api from '../../services/api';
import { Client } from '../../types';

export const ClientsPage: React.FC = () => {
  const [clients, setClients] = useState<Client[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [showModal, setShowModal] = useState(false);
  const [formData, setFormData] = useState({
    clientCode: '',
    name: '',
    contactPerson: '',
    phone: '',
    email: '',
    address: '',
    city: 'Raipur',
    state: 'Chhattisgarh',
    pincode: '492001',
    gstin: '',
    panNo: '',
    fuelSurchargePercent: 10.0,
  });

  const fetchClients = async () => {
    try {
      setLoading(true);
      const res = await api.get('/clients', { params: { search } });
      if (res.data.success) {
        setClients(res.data.data);
      }
    } catch (e) {
      console.error(e);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchClients();
  }, []);

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    fetchClients();
  };

  const handleCreate = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      await api.post('/clients', formData);
      setShowModal(false);
      setFormData({
        clientCode: '',
        name: '',
        contactPerson: '',
        phone: '',
        email: '',
        address: '',
        city: 'Raipur',
        state: 'Chhattisgarh',
        pincode: '492001',
        gstin: '',
        panNo: '',
        fuelSurchargePercent: 10.0,
      });
      fetchClients();
    } catch (err: any) {
      alert(err.response?.data?.error?.message || 'Error creating client');
    }
  };

  return (
    <div>
      <div className="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2 mb-4">
        <div>
          <h2 className="brand-title mb-1">Corporate B2B Clients</h2>
          <p className="text-muted small mb-0">
            Manage commercial client accounts, contractual fuel surcharges, GSTINs, and billing terms
          </p>
        </div>
        <button className="btn btn-primary btn-sm text-nowrap w-100 w-sm-auto" onClick={() => setShowModal(true)}>
          <i className="bi bi-person-plus me-1"></i> Add Corporate Client
        </button>
      </div>

      <div className="app-card p-3 mb-4">
        <form onSubmit={handleSearch} className="row g-2 align-items-center">
          <div className="col-12 col-md-6">
            <div className="input-group input-group-sm">
              <span className="input-group-text bg-light border-end-0">
                <i className="bi bi-search text-muted"></i>
              </span>
              <input
                type="text"
                className="form-control border-start-0"
                placeholder="Search client by code, company name, phone, or GSTIN..."
                value={search}
                onChange={(e) => setSearch(e.target.value)}
              />
            </div>
          </div>
          <div className="col-12 col-md-2">
            <button type="submit" className="btn btn-sm btn-primary w-100">
              Search
            </button>
          </div>
          <div className="col-12 col-md-4 text-md-end text-muted small">
            Total Clients: <strong>{clients.length}</strong>
          </div>
        </form>
      </div>

      <div className="app-card p-3">
        {loading ? (
          <div className="text-center py-5">
            <div className="spinner-border text-primary spinner-border-sm me-2"></div>
            Loading corporate clients...
          </div>
        ) : clients.length === 0 ? (
          <div className="text-center py-4 text-muted">No corporate clients found.</div>
        ) : (
          <>
            {/* Desktop Table View */}
            <div className="table-responsive d-none d-md-block">
              <table className="table table-hover align-middle mb-0">
                <thead className="table-light">
                  <tr className="small text-muted text-uppercase">
                    <th>Code</th>
                    <th>Company Name</th>
                    <th>Contact Details</th>
                    <th>Location</th>
                    <th>GSTIN</th>
                    <th>Fuel %</th>
                    <th>Status</th>
                  </tr>
                </thead>
                <tbody>
                  {clients.map((c) => (
                    <tr key={c.id}>
                      <td>
                        <span className="badge bg-light text-dark border font-monospace fs-6 px-2">
                          #{c.client_code}
                        </span>
                      </td>
                      <td>
                        <div className="fw-semibold">{c.name}</div>
                        {c.contact_person && (
                          <div className="small text-muted">Attn: {c.contact_person}</div>
                        )}
                      </td>
                      <td>
                        <div className="small">
                          <i className="bi bi-telephone me-1"></i> {c.phone || '—'}
                        </div>
                        <div className="small text-muted">
                          <i className="bi bi-envelope me-1"></i> {c.email || '—'}
                        </div>
                      </td>
                      <td>
                        <div>{c.city || 'N/A'}</div>
                        <div className="small text-muted">{c.state || ''}</div>
                      </td>
                      <td>
                        {c.gstin ? (
                          <span className="badge bg-light text-dark border font-monospace">
                            {c.gstin}
                          </span>
                        ) : (
                          <span className="text-muted small">Unregistered</span>
                        )}
                      </td>
                      <td>
                        <span className="badge bg-primary-subtle text-primary">
                          {c.fuel_surcharge_percent}%
                        </span>
                      </td>
                      <td>
                        <span className="badge bg-success-subtle text-success">Active</span>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>

            {/* Handheld Mobile Card View (< 768px) */}
            <div className="d-block d-md-none">
              {clients.map((c) => (
                <div key={c.id} className="shipment-mobile-card">
                  <div className="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                    <span className="badge bg-light text-dark border font-monospace">
                      #{c.client_code}
                    </span>
                    <span className="badge bg-success-subtle text-success">Active</span>
                  </div>
                  <div className="fw-bold fs-6 text-dark mb-1">{c.name}</div>
                  {c.contact_person && (
                    <div className="small text-muted mb-2">
                      <i className="bi bi-person me-1"></i> Attn: {c.contact_person}
                    </div>
                  )}
                  <div className="row g-2 small pt-1 border-top">
                    <div className="col-6">
                      <span className="text-muted d-block">Phone:</span>
                      <a href={`tel:${c.phone}`} className="text-decoration-none fw-semibold">
                        <i className="bi bi-telephone me-1"></i> {c.phone || '—'}
                      </a>
                    </div>
                    <div className="col-6">
                      <span className="text-muted d-block">Location:</span>
                      <span className="fw-semibold">{c.city || 'N/A'}, {c.state || ''}</span>
                    </div>
                    <div className="col-8">
                      <span className="text-muted d-block">GSTIN:</span>
                      <span className="font-monospace text-uppercase" style={{ fontSize: '0.78rem' }}>
                        {c.gstin || 'Unregistered'}
                      </span>
                    </div>
                    <div className="col-4 text-end">
                      <span className="text-muted d-block">Fuel Surcharge:</span>
                      <span className="badge bg-primary-subtle text-primary">
                        {c.fuel_surcharge_percent}%
                      </span>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </>
        )}
      </div>

      {/* Add Client Modal */}
      {showModal && (
        <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
          <div className="modal-dialog modal-lg">
            <div className="modal-content">
              <div className="modal-header">
                <h5 className="modal-title">Register Corporate B2B Client</h5>
                <button type="button" className="btn-close" onClick={() => setShowModal(false)}></button>
              </div>
              <form onSubmit={handleCreate}>
                <div className="modal-body">
                  <div className="row g-2 mb-3">
                    <div className="col-4">
                      <label className="form-label small fw-semibold">Client Code *</label>
                      <input
                        type="text"
                        className="form-control font-monospace"
                        placeholder="e.g. 72 or 105"
                        value={formData.clientCode}
                        onChange={(e) => setFormData({ ...formData, clientCode: e.target.value })}
                        required
                      />
                    </div>
                    <div className="col-8">
                      <label className="form-label small fw-semibold">Company / Client Name *</label>
                      <input
                        type="text"
                        className="form-control"
                        placeholder="e.g. VANDANA ISPAT PVT LTD"
                        value={formData.name}
                        onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                        required
                      />
                    </div>
                  </div>

                  <div className="row g-2 mb-3">
                    <div className="col-6">
                      <label className="form-label small fw-semibold">Contact Person</label>
                      <input
                        type="text"
                        className="form-control"
                        placeholder="Accounts Manager"
                        value={formData.contactPerson}
                        onChange={(e) => setFormData({ ...formData, contactPerson: e.target.value })}
                      />
                    </div>
                    <div className="col-6">
                      <label className="form-label small fw-semibold">Phone Number</label>
                      <input
                        type="tel"
                        className="form-control"
                        placeholder="10-digit phone"
                        value={formData.phone}
                        onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
                      />
                    </div>
                  </div>

                  <div className="mb-3">
                    <label className="form-label small fw-semibold">Official Email</label>
                    <input
                      type="email"
                      className="form-control"
                      placeholder="billing@company.com"
                      value={formData.email}
                      onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                    />
                  </div>

                  <div className="mb-3">
                    <label className="form-label small fw-semibold">Registered Billing Address</label>
                    <textarea
                      className="form-control"
                      rows={2}
                      placeholder="Plot/Office, Industrial Area"
                      value={formData.address}
                      onChange={(e) => setFormData({ ...formData, address: e.target.value })}
                    />
                  </div>

                  <div className="row g-2 mb-3">
                    <div className="col-4">
                      <label className="form-label small fw-semibold">City</label>
                      <input
                        type="text"
                        className="form-control"
                        value={formData.city}
                        onChange={(e) => setFormData({ ...formData, city: e.target.value })}
                      />
                    </div>
                    <div className="col-4">
                      <label className="form-label small fw-semibold">State</label>
                      <input
                        type="text"
                        className="form-control"
                        value={formData.state}
                        onChange={(e) => setFormData({ ...formData, state: e.target.value })}
                      />
                    </div>
                    <div className="col-4">
                      <label className="form-label small fw-semibold">PIN Code</label>
                      <input
                        type="text"
                        className="form-control"
                        value={formData.pincode}
                        onChange={(e) => setFormData({ ...formData, pincode: e.target.value })}
                      />
                    </div>
                  </div>

                  <div className="row g-2">
                    <div className="col-6">
                      <label className="form-label small fw-semibold">GSTIN (15 Digits)</label>
                      <input
                        type="text"
                        className="form-control font-monospace text-uppercase"
                        placeholder="22AAAAA0000A1Z5"
                        value={formData.gstin}
                        onChange={(e) => setFormData({ ...formData, gstin: e.target.value.toUpperCase() })}
                      />
                    </div>
                    <div className="col-6">
                      <label className="form-label small fw-semibold">Contractual Fuel Surcharge %</label>
                      <input
                        type="number"
                        step="0.5"
                        className="form-control"
                        value={formData.fuelSurchargePercent}
                        onChange={(e) =>
                          setFormData({ ...formData, fuelSurchargePercent: Number(e.target.value) })
                        }
                      />
                    </div>
                  </div>
                </div>
                <div className="modal-footer">
                  <button type="button" className="btn btn-light" onClick={() => setShowModal(false)}>
                    Cancel
                  </button>
                  <button type="submit" className="btn btn-primary">
                    Save Client
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

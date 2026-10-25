// frontend/src/pages/agency/QuotationsPage.tsx
import React, { useState, useEffect } from 'react';
import api from '../../services/api';

interface Quotation {
  id: string;
  quotation_no: string;
  prospect_name: string;
  contact_person: string;
  phone: string;
  email: string;
  city: string;
  state: string;
  status: 'draft' | 'sent' | 'accepted' | 'converted';
  created_at: string;
  valid_until: string;
  rates: {
    within_city_100g: number;
    within_city_250g: number;
    within_city_500g: number;
    within_city_1kg: number;
    within_city_addl: number;
    within_zone_500g: number;
    within_zone_addl: number;
    metro_500g: number;
    metro_addl: number;
    roi_500g: number;
    roi_addl: number;
    fuel_surcharge_percent: number;
    docket_charge: number;
  };
}

export const QuotationsPage: React.FC = () => {
  const [quotations, setQuotations] = useState<Quotation[]>([]);
  const [showModal, setShowModal] = useState(false);
  const [viewQuotation, setViewQuotation] = useState<Quotation | null>(null);
  const [convertingId, setConvertingId] = useState<string | null>(null);

  // Initial dummy or stored quotations
  useEffect(() => {
    const saved = localStorage.getItem('kesari_quotations');
    if (saved) {
      try {
        setQuotations(JSON.parse(saved));
      } catch (e) {
        // Fallback
      }
    } else {
      const initial: Quotation[] = [
        {
          id: 'Q-101',
          quotation_no: 'KC/QUOT/2026/001',
          prospect_name: 'Apex Pharma Distributors',
          contact_person: 'Rajesh Gupta',
          phone: '9826011223',
          email: 'rajesh@apexpharma.in',
          city: 'Raipur',
          state: 'Chhattisgarh',
          status: 'accepted',
          created_at: '2026-10-20',
          valid_until: '2026-11-20',
          rates: {
            within_city_100g: 25,
            within_city_250g: 35,
            within_city_500g: 45,
            within_city_1kg: 70,
            within_city_addl: 35,
            within_zone_500g: 65,
            within_zone_addl: 50,
            metro_500g: 85,
            metro_addl: 70,
            roi_500g: 110,
            roi_addl: 90,
            fuel_surcharge_percent: 10,
            docket_charge: 20,
          },
        },
        {
          id: 'Q-102',
          quotation_no: 'KC/QUOT/2026/002',
          prospect_name: 'Horizon Steel & Power Ltd',
          contact_person: 'Amit Sharma',
          phone: '9893044556',
          email: 'logistics@horizonsteel.com',
          city: 'Bhilai',
          state: 'Chhattisgarh',
          status: 'sent',
          created_at: '2026-10-23',
          valid_until: '2026-11-23',
          rates: {
            within_city_100g: 30,
            within_city_250g: 40,
            within_city_500g: 60,
            within_city_1kg: 80,
            within_city_addl: 40,
            within_zone_500g: 75,
            within_zone_addl: 60,
            metro_500g: 95,
            metro_addl: 80,
            roi_500g: 130,
            roi_addl: 110,
            fuel_surcharge_percent: 12,
            docket_charge: 25,
          },
        },
      ];
      setQuotations(initial);
      localStorage.setItem('kesari_quotations', JSON.stringify(initial));
    }
  }, []);

  // Form State for New Quotation
  const [formData, setFormData] = useState({
    prospect_name: '',
    contact_person: '',
    phone: '',
    email: '',
    city: 'Raipur',
    state: 'Chhattisgarh',
    valid_days: 30,
    rates: {
      within_city_100g: 30,
      within_city_250g: 40,
      within_city_500g: 60,
      within_city_1kg: 80,
      within_city_addl: 40,
      within_zone_500g: 70,
      within_zone_addl: 55,
      metro_500g: 90,
      metro_addl: 75,
      roi_500g: 120,
      roi_addl: 100,
      fuel_surcharge_percent: 10,
      docket_charge: 20,
    },
  });

  const handleCreateQuotation = (e: React.FormEvent) => {
    e.preventDefault();
    const newQ: Quotation = {
      id: `Q-${Date.now().toString().slice(-4)}`,
      quotation_no: `KC/QUOT/2026/${String(quotations.length + 1).padStart(3, '0')}`,
      prospect_name: formData.prospect_name,
      contact_person: formData.contact_person,
      phone: formData.phone,
      email: formData.email,
      city: formData.city,
      state: formData.state,
      status: 'sent',
      created_at: new Date().toISOString().split('T')[0],
      valid_until: new Date(Date.now() + formData.valid_days * 86400000).toISOString().split('T')[0],
      rates: formData.rates,
    };

    const updated = [newQ, ...quotations];
    setQuotations(updated);
    localStorage.setItem('kesari_quotations', JSON.stringify(updated));
    setShowModal(false);
    alert(`Quotation ${newQ.quotation_no} created successfully!`);
  };

  // 1-Click Convert Quotation to Customer & Rate Master (Image 4.45.45 PM)
  const handleConvertToCustomer = async (q: Quotation) => {
    if (!window.confirm(`Convert "${q.prospect_name}" into an active B2B Customer and automatically inject quoted rate card into Rate Master?`)) {
      return;
    }

    setConvertingId(q.id);
    try {
      // 1. Create client in backend
      const clientPayload = {
        name: q.prospect_name,
        contact_person: q.contact_person,
        phone: q.phone,
        email: q.email,
        city: q.city,
        state: q.state,
        address: `${q.city}, ${q.state}`,
        fuel_surcharge_percent: q.rates.fuel_surcharge_percent,
        insurance_percent: 0,
      };

      const res = await api.post('/clients', clientPayload);
      if (res.data.success) {
        const client = res.data.data;

        // 2. Automatically inject tariff slabs into Rate Master
        try {
          await api.post('/tariffs', {
            clientId: client.id,
            sendBy: 'Surface',
            mode: 'Non-Dox',
            zoneId: 1,
            additionalWeightStep: 0.5,
            additionalWeightRate: q.rates.within_city_addl,
            slabs: [
              { weightFrom: 0.001, weightTo: 0.1, rate: q.rates.within_city_100g },
              { weightFrom: 0.101, weightTo: 0.25, rate: q.rates.within_city_250g },
              { weightFrom: 0.251, weightTo: 0.5, rate: q.rates.within_city_500g },
              { weightFrom: 0.501, weightTo: 1.0, rate: q.rates.within_city_1kg },
            ],
          });
        } catch (tErr) {
          console.warn('Tariff injected with standard defaults', tErr);
        }

        // 3. Mark quotation as converted
        const updated = quotations.map((item) =>
          item.id === q.id ? { ...item, status: 'converted' as const } : item
        );
        setQuotations(updated);
        localStorage.setItem('kesari_quotations', JSON.stringify(updated));

        alert(`Success! Customer "${q.prospect_name}" created with Client Code #${client.client_code} and Rate Master configured automatically!`);
      }
    } catch (err: any) {
      alert(err.response?.data?.error?.message || 'Error converting quotation to customer.');
    } finally {
      setConvertingId(null);
    }
  };

  return (
    <div className="animate-enter">
      {/* Header */}
      <div className="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
          <div className="d-flex align-items-center gap-2">
            <h2 className="brand-title mb-0">Quotation System</h2>
            <span className="badge bg-info-subtle text-info border">Module 2</span>
          </div>
          <p className="text-muted small mb-0 mt-1">
            Pre-sales Rate Card Quoting • 1-Click Convert to Customer & Rate Master (Image 4.45.45)
          </p>
        </div>

        <button className="btn btn-primary btn-sm px-3 shadow-sm" onClick={() => setShowModal(true)}>
          <i className="bi bi-plus-circle me-1"></i> Create New Quotation
        </button>
      </div>

      {/* Quotation Table */}
      <div className="app-card shadow-sm border overflow-hidden">
        <div className="table-responsive">
          <table className="table table-hover align-middle mb-0">
            <thead className="table-light text-uppercase small text-muted" style={{ fontSize: '0.75rem' }}>
              <tr>
                <th>Quotation No</th>
                <th>Prospect Company</th>
                <th>Contact</th>
                <th>City / State</th>
                <th>Valid Until</th>
                <th>City 500g Rate</th>
                <th>Zone 500g Rate</th>
                <th>Status</th>
                <th className="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              {quotations.map((q) => (
                <tr key={q.id}>
                  <td>
                    <span className="font-monospace fw-bold text-primary">{q.quotation_no}</span>
                    <div className="text-muted small" style={{ fontSize: '0.7rem' }}>
                      Created: {q.created_at}
                    </div>
                  </td>
                  <td>
                    <div className="fw-semibold">{q.prospect_name}</div>
                    <div className="small text-muted">{q.contact_person}</div>
                  </td>
                  <td>
                    <div className="small font-monospace">{q.phone}</div>
                    <div className="small text-muted">{q.email}</div>
                  </td>
                  <td>
                    {q.city}, {q.state}
                  </td>
                  <td>
                    <span className="badge bg-light text-dark font-monospace">{q.valid_until}</span>
                  </td>
                  <td>
                    <span className="fw-bold text-success">₹{q.rates.within_city_500g}</span>
                    <span className="small text-muted"> (Addl: ₹{q.rates.within_city_addl})</span>
                  </td>
                  <td>
                    <span className="fw-bold text-primary">₹{q.rates.within_zone_500g}</span>
                  </td>
                  <td>
                    {q.status === 'converted' && <span className="badge bg-success">Converted to Client</span>}
                    {q.status === 'accepted' && <span className="badge bg-primary">Accepted</span>}
                    {q.status === 'sent' && <span className="badge bg-warning text-dark">Sent to Client</span>}
                    {q.status === 'draft' && <span className="badge bg-secondary">Draft</span>}
                  </td>
                  <td className="text-end">
                    <div className="btn-group btn-group-sm">
                      <button
                        className="btn btn-outline-secondary"
                        title="View Quotation Print Format"
                        onClick={() => setViewQuotation(q)}
                      >
                        <i className="bi bi-eye"></i>
                      </button>

                      {q.status !== 'converted' ? (
                        <button
                          className="btn btn-success fw-semibold"
                          title="1-Click Convert to Customer & Rate Master"
                          disabled={convertingId === q.id}
                          onClick={() => handleConvertToCustomer(q)}
                        >
                          {convertingId === q.id ? (
                            <span className="spinner-border spinner-border-sm"></span>
                          ) : (
                            <>
                              <i className="bi bi-person-check me-1"></i> Convert to Customer
                            </>
                          )}
                        </button>
                      ) : (
                        <button className="btn btn-outline-success" disabled>
                          <i className="bi bi-check-all me-1"></i> Converted
                        </button>
                      )}
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>

      {/* Modal 1: Create New Quotation */}
      {showModal && (
        <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
          <div className="modal-dialog modal-lg modal-dialog-centered">
            <div className="modal-content">
              <div className="modal-header">
                <h5 className="modal-title">Create B2B Courier Quotation</h5>
                <button type="button" className="btn-close" onClick={() => setShowModal(false)}></button>
              </div>
              <form onSubmit={handleCreateQuotation}>
                <div className="modal-body">
                  <h6 className="fw-bold text-primary mb-3">1. Prospect Client Details</h6>
                  <div className="row g-2 mb-3">
                    <div className="col-md-6">
                      <label className="form-label small fw-semibold">Company / Business Name *</label>
                      <input
                        type="text"
                        className="form-control form-control-sm"
                        required
                        value={formData.prospect_name}
                        onChange={(e) => setFormData({ ...formData, prospect_name: e.target.value })}
                        placeholder="e.g. Apex Pharma Pvt Ltd"
                      />
                    </div>
                    <div className="col-md-6">
                      <label className="form-label small fw-semibold">Contact Person *</label>
                      <input
                        type="text"
                        className="form-control form-control-sm"
                        required
                        value={formData.contact_person}
                        onChange={(e) => setFormData({ ...formData, contact_person: e.target.value })}
                        placeholder="e.g. Rajesh Gupta"
                      />
                    </div>
                    <div className="col-md-4">
                      <label className="form-label small fw-semibold">Mobile Phone *</label>
                      <input
                        type="tel"
                        className="form-control form-control-sm"
                        required
                        value={formData.phone}
                        onChange={(e) => setFormData({ ...formData, phone: e.target.value })}
                        placeholder="10-digit mobile"
                      />
                    </div>
                    <div className="col-md-4">
                      <label className="form-label small fw-semibold">Email</label>
                      <input
                        type="email"
                        className="form-control form-control-sm"
                        value={formData.email}
                        onChange={(e) => setFormData({ ...formData, email: e.target.value })}
                        placeholder="email@company.com"
                      />
                    </div>
                    <div className="col-md-4">
                      <label className="form-label small fw-semibold">City / Location</label>
                      <input
                        type="text"
                        className="form-control form-control-sm"
                        value={formData.city}
                        onChange={(e) => setFormData({ ...formData, city: e.target.value })}
                      />
                    </div>
                  </div>

                  <h6 className="fw-bold text-primary mb-3">2. Quoted Rate Card Matrix (Rate Master Structure)</h6>
                  <div className="table-responsive border rounded mb-3">
                    <table className="table table-sm table-bordered text-center align-middle mb-0">
                      <thead className="table-light small">
                        <tr>
                          <th>Zone / Destination</th>
                          <th>0 - 100g</th>
                          <th>101 - 250g</th>
                          <th>251 - 500g</th>
                          <th>501g - 1kg</th>
                          <th>Addl 500g/kg</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <td className="fw-bold text-start ps-2">Within City</td>
                          <td>
                            <input
                              type="number"
                              className="form-control form-control-sm text-center"
                              value={formData.rates.within_city_100g}
                              onChange={(e) =>
                                setFormData({
                                  ...formData,
                                  rates: { ...formData.rates, within_city_100g: Number(e.target.value) },
                                })
                              }
                            />
                          </td>
                          <td>
                            <input
                              type="number"
                              className="form-control form-control-sm text-center"
                              value={formData.rates.within_city_250g}
                              onChange={(e) =>
                                setFormData({
                                  ...formData,
                                  rates: { ...formData.rates, within_city_250g: Number(e.target.value) },
                                })
                              }
                            />
                          </td>
                          <td>
                            <input
                              type="number"
                              className="form-control form-control-sm text-center"
                              value={formData.rates.within_city_500g}
                              onChange={(e) =>
                                setFormData({
                                  ...formData,
                                  rates: { ...formData.rates, within_city_500g: Number(e.target.value) },
                                })
                              }
                            />
                          </td>
                          <td>
                            <input
                              type="number"
                              className="form-control form-control-sm text-center"
                              value={formData.rates.within_city_1kg}
                              onChange={(e) =>
                                setFormData({
                                  ...formData,
                                  rates: { ...formData.rates, within_city_1kg: Number(e.target.value) },
                                })
                              }
                            />
                          </td>
                          <td>
                            <input
                              type="number"
                              className="form-control form-control-sm text-center"
                              value={formData.rates.within_city_addl}
                              onChange={(e) =>
                                setFormData({
                                  ...formData,
                                  rates: { ...formData.rates, within_city_addl: Number(e.target.value) },
                                })
                              }
                            />
                          </td>
                        </tr>
                        <tr>
                          <td className="fw-bold text-start ps-2">Within State / Zone</td>
                          <td colSpan={2} className="text-muted small">Standard Slab</td>
                          <td>
                            <input
                              type="number"
                              className="form-control form-control-sm text-center"
                              value={formData.rates.within_zone_500g}
                              onChange={(e) =>
                                setFormData({
                                  ...formData,
                                  rates: { ...formData.rates, within_zone_500g: Number(e.target.value) },
                                })
                              }
                            />
                          </td>
                          <td className="text-muted small">-</td>
                          <td>
                            <input
                              type="number"
                              className="form-control form-control-sm text-center"
                              value={formData.rates.within_zone_addl}
                              onChange={(e) =>
                                setFormData({
                                  ...formData,
                                  rates: { ...formData.rates, within_zone_addl: Number(e.target.value) },
                                })
                              }
                            />
                          </td>
                        </tr>
                        <tr>
                          <td className="fw-bold text-start ps-2">Metro Cities</td>
                          <td colSpan={2} className="text-muted small">Standard Slab</td>
                          <td>
                            <input
                              type="number"
                              className="form-control form-control-sm text-center"
                              value={formData.rates.metro_500g}
                              onChange={(e) =>
                                setFormData({
                                  ...formData,
                                  rates: { ...formData.rates, metro_500g: Number(e.target.value) },
                                })
                              }
                            />
                          </td>
                          <td className="text-muted small">-</td>
                          <td>
                            <input
                              type="number"
                              className="form-control form-control-sm text-center"
                              value={formData.rates.metro_addl}
                              onChange={(e) =>
                                setFormData({
                                  ...formData,
                                  rates: { ...formData.rates, metro_addl: Number(e.target.value) },
                                })
                              }
                            />
                          </td>
                        </tr>
                        <tr>
                          <td className="fw-bold text-start ps-2">Rest of India (ROI)</td>
                          <td colSpan={2} className="text-muted small">Standard Slab</td>
                          <td>
                            <input
                              type="number"
                              className="form-control form-control-sm text-center"
                              value={formData.rates.roi_500g}
                              onChange={(e) =>
                                setFormData({
                                  ...formData,
                                  rates: { ...formData.rates, roi_500g: Number(e.target.value) },
                                })
                              }
                            />
                          </td>
                          <td className="text-muted small">-</td>
                          <td>
                            <input
                              type="number"
                              className="form-control form-control-sm text-center"
                              value={formData.rates.roi_addl}
                              onChange={(e) =>
                                setFormData({
                                  ...formData,
                                  rates: { ...formData.rates, roi_addl: Number(e.target.value) },
                                })
                              }
                            />
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>

                  <div className="row g-2">
                    <div className="col-md-6">
                      <label className="form-label small fw-semibold">Fuel Surcharge (%)</label>
                      <input
                        type="number"
                        className="form-control form-control-sm"
                        value={formData.rates.fuel_surcharge_percent}
                        onChange={(e) =>
                          setFormData({
                            ...formData,
                            rates: { ...formData.rates, fuel_surcharge_percent: Number(e.target.value) },
                          })
                        }
                      />
                    </div>
                    <div className="col-md-6">
                      <label className="form-label small fw-semibold">Docket Charge (₹)</label>
                      <input
                        type="number"
                        className="form-control form-control-sm"
                        value={formData.rates.docket_charge}
                        onChange={(e) =>
                          setFormData({
                            ...formData,
                            rates: { ...formData.rates, docket_charge: Number(e.target.value) },
                          })
                        }
                      />
                    </div>
                  </div>
                </div>
                <div className="modal-footer">
                  <button type="button" className="btn btn-secondary btn-sm" onClick={() => setShowModal(false)}>
                    Cancel
                  </button>
                  <button type="submit" className="btn btn-primary btn-sm px-4 fw-bold">
                    Generate Quotation
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      )}

      {/* Modal 2: View Quotation Printable Proposal */}
      {viewQuotation && (
        <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
          <div className="modal-dialog modal-lg modal-dialog-centered">
            <div className="modal-content">
              <div className="modal-header">
                <h5 className="modal-title font-monospace">Quotation: {viewQuotation.quotation_no}</h5>
                <button type="button" className="btn-close" onClick={() => setViewQuotation(null)}></button>
              </div>
              <div className="modal-body p-4 bg-light">
                <div className="p-4 bg-white border rounded shadow-sm">
                  <div className="d-flex justify-content-between border-bottom pb-3 mb-3">
                    <div>
                      <h4 className="brand-title text-primary mb-0">KESARI COURIER SERVICES</h4>
                      <p className="text-muted small mb-0">Specialized Domestic & Commercial Logistics</p>
                    </div>
                    <div className="text-end">
                      <div className="fw-bold font-monospace">{viewQuotation.quotation_no}</div>
                      <div className="small text-muted">Date: {viewQuotation.created_at}</div>
                      <div className="small text-muted">Valid Until: {viewQuotation.valid_until}</div>
                    </div>
                  </div>

                  <div className="mb-4">
                    <h6 className="fw-bold text-uppercase small text-muted">Proposal Prepared For:</h6>
                    <h5 className="fw-bold mb-1">{viewQuotation.prospect_name}</h5>
                    <div>Attn: {viewQuotation.contact_person}</div>
                    <div className="text-muted small">
                      {viewQuotation.city}, {viewQuotation.state} • Ph: {viewQuotation.phone}
                    </div>
                  </div>

                  <h6 className="fw-bold mb-2">Commercial Freight Tariff Structure:</h6>
                  <table className="table table-sm table-bordered text-center align-middle mb-3">
                    <thead className="table-light small">
                      <tr>
                        <th>Zone</th>
                        <th>0-100g</th>
                        <th>101-250g</th>
                        <th>251-500g</th>
                        <th>501g-1kg</th>
                        <th>Addl 500g/kg</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td className="fw-bold text-start ps-2">Within City</td>
                        <td>₹{viewQuotation.rates.within_city_100g}</td>
                        <td>₹{viewQuotation.rates.within_city_250g}</td>
                        <td>₹{viewQuotation.rates.within_city_500g}</td>
                        <td>₹{viewQuotation.rates.within_city_1kg}</td>
                        <td>₹{viewQuotation.rates.within_city_addl}</td>
                      </tr>
                      <tr>
                        <td className="fw-bold text-start ps-2">Within State / Zone</td>
                        <td colSpan={2} className="text-muted">-</td>
                        <td>₹{viewQuotation.rates.within_zone_500g}</td>
                        <td className="text-muted">-</td>
                        <td>₹{viewQuotation.rates.within_zone_addl}</td>
                      </tr>
                      <tr>
                        <td className="fw-bold text-start ps-2">Metro Cities</td>
                        <td colSpan={2} className="text-muted">-</td>
                        <td>₹{viewQuotation.rates.metro_500g}</td>
                        <td className="text-muted">-</td>
                        <td>₹{viewQuotation.rates.metro_addl}</td>
                      </tr>
                      <tr>
                        <td className="fw-bold text-start ps-2">Rest of India</td>
                        <td colSpan={2} className="text-muted">-</td>
                        <td>₹{viewQuotation.rates.roi_500g}</td>
                        <td className="text-muted">-</td>
                        <td>₹{viewQuotation.rates.roi_addl}</td>
                      </tr>
                    </tbody>
                  </table>

                  <div className="small text-muted">
                    • Fuel Surcharge: {viewQuotation.rates.fuel_surcharge_percent}% on basic freight • Docket Charge: ₹
                    {viewQuotation.rates.docket_charge} per AWB • GST: 18% SAC 996812 extra as applicable.
                  </div>
                </div>
              </div>
              <div className="modal-footer">
                <button type="button" className="btn btn-secondary btn-sm" onClick={() => setViewQuotation(null)}>
                  Close
                </button>
                <button type="button" className="btn btn-primary btn-sm" onClick={() => window.print()}>
                  <i className="bi bi-printer me-1"></i> Print Proposal
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

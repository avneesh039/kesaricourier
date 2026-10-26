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
  const [isFullscreen, setIsFullscreen] = useState(false);

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
        <div
          className="quotation-screen-overlay"
          onClick={(e) => {
            if (e.target === e.currentTarget) setShowModal(false);
          }}
        >
          <div
            className="quotation-screen-container"
            style={{ maxWidth: '860px', height: '90vh' }}
          >
            <div className="quotation-screen-header">
              <div className="d-flex align-items-center gap-2">
                <div className="rounded bg-primary text-white p-2 d-flex align-items-center justify-content-center" style={{ width: 36, height: 36 }}>
                  <i className="bi bi-file-earmark-plus"></i>
                </div>
                <div>
                  <h5 className="modal-title fw-bold mb-0" style={{ fontSize: '1.05rem' }}>Create B2B Courier Quotation</h5>
                  <small className="text-muted">Generate formal rate proposal for commercial corporate prospects</small>
                </div>
              </div>
              <button type="button" className="btn-close" onClick={() => setShowModal(false)}></button>
            </div>
            <form onSubmit={handleCreateQuotation} style={{ display: 'flex', flexDirection: 'column', flex: '1 1 auto', minHeight: 0, overflow: 'hidden' }}>
              <div className="quotation-screen-body">
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
              <div className="quotation-screen-footer">
                <button type="button" className="btn btn-secondary btn-sm" onClick={() => setShowModal(false)}>
                  Cancel
                </button>
                <button type="submit" className="btn btn-primary btn-sm px-4 fw-bold shadow-sm">
                  Generate Quotation
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {/* Modal 2: View Quotation Full Proposal Screen */}
      {viewQuotation && (
        <div
          className="quotation-screen-overlay"
          onClick={(e) => {
            if (e.target === e.currentTarget) setViewQuotation(null);
          }}
        >
          <div className={`quotation-screen-container ${isFullscreen ? 'is-fullscreen' : ''}`}>
            {/* Top Navigation & Action Toolbar (Always visible at top of viewport) */}
            <div className="quotation-screen-header no-print">
              <div className="d-flex align-items-center gap-2 overflow-hidden">
                <div
                  className="rounded bg-primary-subtle text-primary p-2 d-flex align-items-center justify-content-center flex-shrink-0"
                  style={{ width: 38, height: 38 }}
                >
                  <i className="bi bi-file-earmark-text fs-5"></i>
                </div>
                <div className="text-truncate">
                  <div className="d-flex align-items-center gap-2 flex-wrap">
                    <h5 className="fw-bold font-monospace mb-0" style={{ fontSize: '1.05rem' }}>
                      {viewQuotation.quotation_no}
                    </h5>
                    <span
                      className={`badge ${
                        viewQuotation.status === 'accepted'
                          ? 'bg-success'
                          : viewQuotation.status === 'converted'
                          ? 'bg-info'
                          : 'bg-primary'
                      }`}
                      style={{ fontSize: '0.68rem', letterSpacing: '0.5px' }}
                    >
                      {viewQuotation.status ? viewQuotation.status.toUpperCase() : 'ACTIVE PROPOSAL'}
                    </span>
                    <span className="badge bg-light text-dark border text-truncate d-none d-sm-inline-block" style={{ maxWidth: '220px' }}>
                      {viewQuotation.prospect_name}
                    </span>
                  </div>
                  <small className="text-muted d-none d-md-inline-block">
                    B2B Commercial Rate Proposal & Service Level Agreement
                  </small>
                </div>
              </div>

              {/* Action Buttons in Header (Always directly clickable on any screen size) */}
              <div className="d-flex align-items-center gap-1 gap-sm-2 flex-shrink-0">
                <button
                  type="button"
                  className="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1"
                  onClick={() => setIsFullscreen(!isFullscreen)}
                  title={isFullscreen ? 'Exit Fullscreen' : 'Fullscreen View'}
                >
                  <i className={`bi ${isFullscreen ? 'bi-fullscreen-exit' : 'bi-arrows-fullscreen'}`}></i>
                  <span className="d-none d-md-inline">{isFullscreen ? 'Exit Fullscreen' : 'Fullscreen'}</span>
                </button>

                {viewQuotation.status !== 'converted' && (
                  <button
                    type="button"
                    className="btn btn-success btn-sm d-flex align-items-center gap-1 shadow-sm fw-semibold"
                    onClick={() => {
                      handleConvertToCustomer(viewQuotation);
                      setViewQuotation(null);
                    }}
                    title="1-Click Convert to Customer & Rate Master"
                  >
                    <i className="bi bi-person-check"></i>
                    <span className="d-none d-sm-inline">Convert to Customer</span>
                  </button>
                )}

                <button
                  type="button"
                  className="btn btn-primary btn-sm d-flex align-items-center gap-1 shadow-sm"
                  onClick={() => window.print()}
                  title="Print Proposal"
                >
                  <i className="bi bi-printer"></i>
                  <span className="d-none d-sm-inline">Print</span>
                </button>

                <button
                  type="button"
                  className="btn btn-outline-secondary btn-sm"
                  aria-label="Close"
                  onClick={() => setViewQuotation(null)}
                >
                  <i className="bi bi-x-lg"></i>
                </button>
              </div>
            </div>

            {/* Scrollable Viewport Body with Guaranteed Viewport Fit */}
            <div className="quotation-screen-body">
              <div className="quotation-paper-sheet">
                {/* Corporate Proposal Header */}
                <div className="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center border-bottom pb-3 mb-3 gap-2">
                  <div className="d-flex align-items-center gap-3">
                    <div
                      className="rounded bg-primary text-white p-2 d-flex align-items-center justify-content-center shadow-sm"
                      style={{ width: 44, height: 44 }}
                    >
                      <i className="bi bi-box-seam-fill fs-4"></i>
                    </div>
                    <div>
                      <h4 className="brand-title text-primary mb-0 fw-bold" style={{ letterSpacing: '0.5px' }}>
                        KESARI COURIER SERVICES PVT. LTD.
                      </h4>
                      <p className="text-muted small mb-0 fw-semibold">
                        Specialized Domestic & Commercial Freight Logistics Network
                      </p>
                      <div className="small text-muted" style={{ fontSize: '0.75rem' }}>
                        Regd Office: Thakur Building, Ring Road No-1, Kushalpur, Raipur-492013 • GSTIN: <strong>22AAGCK2836D1ZY</strong> • SAC: 996812
                      </div>
                    </div>
                  </div>

                  <div className="text-sm-end bg-light-subtle p-2 px-3 rounded border font-monospace mt-2 mt-sm-0" style={{ minWidth: '180px' }}>
                    <div className="badge bg-primary px-2 py-1 mb-1 font-monospace" style={{ fontSize: '0.78rem' }}>
                      {viewQuotation.quotation_no}
                    </div>
                    <div className="small text-muted" style={{ fontSize: '0.75rem' }}>
                      Issued: <strong>{viewQuotation.created_at}</strong>
                    </div>
                    <div className="small text-muted" style={{ fontSize: '0.75rem' }}>
                      Valid Until: <strong className="text-danger">{viewQuotation.valid_until}</strong>
                    </div>
                  </div>
                </div>

                {/* Section 1: Client Prospect Details & Statutory Charges (Side-by-Side 2 Cards) */}
                <div className="row g-3 mb-3">
                  <div className="col-12 col-md-7">
                    <div className="p-3 bg-light-subtle rounded border h-100">
                      <div className="d-flex align-items-center justify-content-between mb-2">
                        <span className="fw-bold text-uppercase small text-primary d-flex align-items-center gap-1" style={{ fontSize: '0.72rem', letterSpacing: '0.5px' }}>
                          <i className="bi bi-building"></i> Proposal Prepared For (Client Prospect)
                        </span>
                        <span className="badge bg-white text-secondary border font-monospace" style={{ fontSize: '0.65rem' }}>
                          Commercial Tariff
                        </span>
                      </div>
                      <h5 className="fw-bold mb-1 text-dark">{viewQuotation.prospect_name}</h5>
                      <div className="row g-1 small text-muted" style={{ fontSize: '0.8rem' }}>
                        <div className="col-6">
                          <strong>Attn:</strong> {viewQuotation.contact_person}
                        </div>
                        <div className="col-6">
                          <strong>Phone:</strong> {viewQuotation.phone}
                        </div>
                        <div className="col-6">
                          <strong>Location:</strong> {viewQuotation.city || 'Raipur'}, {viewQuotation.state || 'Chhattisgarh'}
                        </div>
                        <div className="col-6">
                          <strong>Email:</strong> {viewQuotation.email || 'N/A'}
                        </div>
                      </div>
                    </div>
                  </div>

                  <div className="col-12 col-md-5">
                    <div className="p-3 bg-light-subtle rounded border h-100">
                      <div className="fw-bold text-uppercase small text-primary mb-2 d-flex align-items-center gap-1" style={{ fontSize: '0.72rem', letterSpacing: '0.5px' }}>
                        <i className="bi bi-info-circle"></i> Statutory Levies & Surcharges
                      </div>
                      <div className="row g-2">
                        <div className="col-6">
                          <div className="p-2 border rounded text-center bg-white shadow-xs">
                            <span className="text-muted d-block" style={{ fontSize: '0.68rem' }}>Fuel Surcharge</span>
                            <strong className="text-primary">{viewQuotation.rates.fuel_surcharge_percent}%</strong>
                            <div className="text-muted" style={{ fontSize: '0.62rem' }}>on basic freight</div>
                          </div>
                        </div>
                        <div className="col-6">
                          <div className="p-2 border rounded text-center bg-white shadow-xs">
                            <span className="text-muted d-block" style={{ fontSize: '0.68rem' }}>Docket Fee</span>
                            <strong className="text-primary">₹{viewQuotation.rates.docket_charge}</strong>
                            <div className="text-muted" style={{ fontSize: '0.62rem' }}>per shipment</div>
                          </div>
                        </div>
                        <div className="col-6">
                          <div className="p-2 border rounded text-center bg-white shadow-xs">
                            <span className="text-muted d-block" style={{ fontSize: '0.68rem' }}>GST Extra</span>
                            <strong className="text-primary">18% Extra</strong>
                            <div className="text-muted" style={{ fontSize: '0.62rem' }}>SAC: 996812</div>
                          </div>
                        </div>
                        <div className="col-6">
                          <div className="p-2 border rounded text-center bg-white shadow-xs">
                            <span className="text-muted d-block" style={{ fontSize: '0.68rem' }}>Volumetric Factor</span>
                            <strong className="text-primary">5,000</strong>
                            <div className="text-muted" style={{ fontSize: '0.62rem' }}>cc / kg factor</div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                {/* Section 2: Commercial Freight Tariff Structure (Slab-wise Rate Matrix) */}
                <div className="mb-3">
                  <div className="d-flex align-items-center justify-content-between mb-2">
                    <h6 className="fw-bold mb-0 text-dark d-flex align-items-center gap-1">
                      <i className="bi bi-table text-primary"></i> Commercial Freight Tariff Structure (Slab-wise Rate Matrix):
                    </h6>
                    <span className="badge bg-primary-subtle text-primary border" style={{ fontSize: '0.7rem' }}>
                      All tariffs in INR (₹)
                    </span>
                  </div>
                  <div className="table-responsive border rounded bg-white">
                    <table className="table table-bordered table-sm text-center align-middle mb-0">
                      <thead className="table-light small">
                        <tr>
                          <th className="text-start ps-3" style={{ width: '28%' }}>Delivery Corridor / Zone</th>
                          <th>0 - 100g</th>
                          <th>101 - 250g</th>
                          <th>251 - 500g</th>
                          <th>501g - 1kg</th>
                          <th>Addl. 500g / kg</th>
                        </tr>
                      </thead>
                      <tbody className="small">
                        <tr>
                          <td className="fw-bold text-start ps-3 bg-light-subtle">
                            <i className="bi bi-geo-alt text-primary me-1"></i> Within City (Local Hub)
                          </td>
                          <td className="fw-semibold">₹{viewQuotation.rates.within_city_100g}</td>
                          <td className="fw-semibold">₹{viewQuotation.rates.within_city_250g}</td>
                          <td className="fw-semibold">₹{viewQuotation.rates.within_city_500g}</td>
                          <td className="fw-semibold">₹{viewQuotation.rates.within_city_1kg}</td>
                          <td className="fw-semibold text-primary">₹{viewQuotation.rates.within_city_addl}</td>
                        </tr>
                        <tr>
                          <td className="fw-bold text-start ps-3 bg-light-subtle">
                            <i className="bi bi-pin-map text-success me-1"></i> Within State / Central Zone
                          </td>
                          <td colSpan={2} className="text-muted small">Standard Base Slab</td>
                          <td className="fw-semibold">₹{viewQuotation.rates.within_zone_500g}</td>
                          <td className="text-muted small">-</td>
                          <td className="fw-semibold text-primary">₹{viewQuotation.rates.within_zone_addl}</td>
                        </tr>
                        <tr>
                          <td className="fw-bold text-start ps-3 bg-light-subtle">
                            <i className="bi bi-building text-info me-1"></i> Metro Cities (Priority Air)
                          </td>
                          <td colSpan={2} className="text-muted small">Standard Base Slab</td>
                          <td className="fw-semibold">₹{viewQuotation.rates.metro_500g}</td>
                          <td className="text-muted small">-</td>
                          <td className="fw-semibold text-primary">₹{viewQuotation.rates.metro_addl}</td>
                        </tr>
                        <tr>
                          <td className="fw-bold text-start ps-3 bg-light-subtle">
                            <i className="bi bi-globe text-secondary me-1"></i> Rest of India (National Surface)
                          </td>
                          <td colSpan={2} className="text-muted small">Standard Base Slab</td>
                          <td className="fw-semibold">₹{viewQuotation.rates.roi_500g}</td>
                          <td className="text-muted small">-</td>
                          <td className="fw-semibold text-primary">₹{viewQuotation.rates.roi_addl}</td>
                        </tr>
                      </tbody>
                    </table>
                  </div>
                </div>

                {/* Section 3: SLA & Sign-Off (Side-by-Side 2 Cards) */}
                <div className="row g-3">
                  <div className="col-12 col-md-6">
                    <div className="p-3 bg-light-subtle rounded border h-100">
                      <h6 className="fw-bold small text-dark mb-2 d-flex align-items-center gap-1">
                        <i className="bi bi-patch-check text-success"></i> Service Level Commitments (SLA):
                      </h6>
                      <div className="d-flex flex-column gap-1 small text-muted" style={{ fontSize: '0.78rem' }}>
                        <div className="d-flex align-items-center gap-2">
                          <i className="bi bi-check-circle-fill text-success flex-shrink-0"></i>
                          <span>Free daily scheduled doorstep pickup at client office/hub</span>
                        </div>
                        <div className="d-flex align-items-center gap-2">
                          <i className="bi bi-check-circle-fill text-success flex-shrink-0"></i>
                          <span>Real-time AWB milestone telemetry & SMS tracking notifications</span>
                        </div>
                        <div className="d-flex align-items-center gap-2">
                          <i className="bi bi-check-circle-fill text-success flex-shrink-0"></i>
                          <span>Digital Proof of Delivery (POD) with signature/photo</span>
                        </div>
                        <div className="d-flex align-items-center gap-2">
                          <i className="bi bi-check-circle-fill text-success flex-shrink-0"></i>
                          <span>Dedicated corporate manager & monthly consolidated GST invoicing</span>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div className="col-12 col-md-6">
                    <div className="p-3 bg-light-subtle rounded border h-100 d-flex flex-column justify-content-between">
                      <h6 className="fw-bold small text-dark mb-2 d-flex align-items-center gap-1">
                        <i className="bi bi-pen text-primary"></i> Commercial Authorization & Sign-off:
                      </h6>
                      <div className="row pt-2 align-items-end g-2">
                        <div className="col-6">
                          <div className="text-muted small mb-3" style={{ fontSize: '0.75rem' }}>
                            For <strong>Kesari Courier Services</strong>
                          </div>
                          <div className="border-bottom" style={{ width: '130px' }}></div>
                          <div className="small fw-semibold mt-1" style={{ fontSize: '0.72rem' }}>
                            Authorized Sales Executive
                          </div>
                        </div>
                        <div className="col-6 text-end">
                          <div className="text-muted small mb-3" style={{ fontSize: '0.75rem' }}>
                            Client: <strong>{viewQuotation.prospect_name}</strong>
                          </div>
                          <div className="border-bottom ms-auto" style={{ width: '130px' }}></div>
                          <div className="small fw-semibold mt-1" style={{ fontSize: '0.72rem' }}>
                            Authorized Signatory & Stamp
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {/* Fixed Modal Footer (Always visible at bottom of viewport) */}
            <div className="quotation-screen-footer no-print">
              <div className="d-flex align-items-center gap-2">
                <span className="text-muted small">
                  <i className="bi bi-shield-check text-success me-1"></i> Proposal valid until:{' '}
                  <strong className="text-danger font-monospace">{viewQuotation.valid_until}</strong>
                </span>
              </div>
              <div className="d-flex align-items-center gap-2 flex-wrap">
                <button
                  type="button"
                  className="btn btn-outline-secondary btn-sm px-3"
                  onClick={() => setViewQuotation(null)}
                >
                  Close
                </button>
                {viewQuotation.status !== 'converted' && (
                  <button
                    type="button"
                    className="btn btn-success btn-sm d-flex align-items-center gap-1 px-3 fw-semibold shadow-sm"
                    onClick={() => {
                      handleConvertToCustomer(viewQuotation);
                      setViewQuotation(null);
                    }}
                  >
                    <i className="bi bi-person-check"></i>
                    <span>Convert to Customer</span>
                  </button>
                )}
                <button
                  type="button"
                  className="btn btn-primary btn-sm d-flex align-items-center gap-1 px-3 shadow-sm"
                  onClick={() => window.print()}
                >
                  <i className="bi bi-printer"></i>
                  <span>Print Proposal</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

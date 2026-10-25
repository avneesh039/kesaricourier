// frontend/src/pages/agency/AgencySettingsPage.tsx
import React, { useState } from 'react';

export const AgencySettingsPage: React.FC = () => {
  const [savedSuccess, setSavedSuccess] = useState(false);

  // Settings state
  const [docketPrefix, setDocketPrefix] = useState('26');
  const [invoicePrefix, setInvoicePrefix] = useState('INV-26-');
  const [manifestPrefix, setManifestPrefix] = useState('MNF-');
  const [drsPrefix, setDrsPrefix] = useState('DRS-');
  const [airDivisor, setAirDivisor] = useState(5000);
  const [surfaceDivisor, setSurfaceDivisor] = useState(4000);
  const [gstSacCode, setGstSacCode] = useState('996812');
  const [autoEmailPod, setAutoEmailPod] = useState(true);
  const [smsDeliveryAlert, setSmsDeliveryAlert] = useState(true);
  const [taxRounding, setTaxRounding] = useState('nearest_rupee');

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setSavedSuccess(true);
    setTimeout(() => setSavedSuccess(false), 3000);
  };

  return (
    <div className="container-fluid p-0">
      {/* Page Header */}
      <div className="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 className="fw-bold text-dark mb-1">Agency Settings & Operational Rules</h3>
          <p className="text-muted small mb-0">
            Configure numbering sequences, volumetric calculation formulas, tax SAC codes, and notification alerts.
          </p>
        </div>
      </div>

      {savedSuccess && (
        <div className="alert alert-success d-flex align-items-center mb-4 shadow-sm">
          <i className="bi bi-check-circle-fill fs-4 me-2"></i>
          <div>
            <strong>Configuration Saved!</strong> Agency operational parameters updated successfully.
          </div>
        </div>
      )}

      <form onSubmit={handleSubmit}>
        <div className="row g-4">
          {/* Section 1: Document Sequence Numbering */}
          <div className="col-lg-6">
            <div className="card shadow-sm border-0 h-100">
              <div className="card-header bg-white py-3 border-bottom">
                <h6 className="fw-bold text-dark mb-0">
                  <i className="bi bi-123 text-primary me-2"></i> Document Numbering Sequences
                </h6>
              </div>
              <div className="card-body">
                <div className="mb-3">
                  <label className="form-label small fw-bold">Consignment / Docket Prefix</label>
                  <input
                    type="text"
                    className="form-control"
                    value={docketPrefix}
                    onChange={(e) => setDocketPrefix(e.target.value)}
                    required
                  />
                  <div className="form-text small">
                    Agency 2-digit code used as prefix for 14-digit GS1 consignment barcode numbers.
                  </div>
                </div>

                <div className="mb-3">
                  <label className="form-label small fw-bold">B2B GST Tax Invoice Prefix</label>
                  <input
                    type="text"
                    className="form-control"
                    value={invoicePrefix}
                    onChange={(e) => setInvoicePrefix(e.target.value)}
                    required
                  />
                  <div className="form-text small">
                    Example: {invoicePrefix}2026-0001
                  </div>
                </div>

                <div className="mb-3">
                  <label className="form-label small fw-bold">Line-haul Manifest Prefix</label>
                  <input
                    type="text"
                    className="form-control"
                    value={manifestPrefix}
                    onChange={(e) => setManifestPrefix(e.target.value)}
                    required
                  />
                </div>

                <div className="mb-3">
                  <label className="form-label small fw-bold">Delivery Runsheet (DRS) Prefix</label>
                  <input
                    type="text"
                    className="form-control"
                    value={drsPrefix}
                    onChange={(e) => setDrsPrefix(e.target.value)}
                    required
                  />
                </div>
              </div>
            </div>
          </div>

          {/* Section 2: Logistics Domain Math & GST SAC */}
          <div className="col-lg-6">
            <div className="card shadow-sm border-0 h-100">
              <div className="card-header bg-white py-3 border-bottom">
                <h6 className="fw-bold text-dark mb-0">
                  <i className="bi bi-calculator text-primary me-2"></i> Logistics Math & Taxation Defaults
                </h6>
              </div>
              <div className="card-body">
                <div className="mb-3">
                  <label className="form-label small fw-bold">Air Express Volumetric Divisor</label>
                  <input
                    type="number"
                    className="form-control"
                    value={airDivisor}
                    onChange={(e) => setAirDivisor(Number(e.target.value))}
                    required
                  />
                  <div className="form-text small">
                    IATA standard divisor is 5000: (L × W × H in cm) / 5000 = Volumetric kg.
                  </div>
                </div>

                <div className="mb-3">
                  <label className="form-label small fw-bold">Surface Cargo Volumetric Divisor</label>
                  <input
                    type="number"
                    className="form-control"
                    value={surfaceDivisor}
                    onChange={(e) => setSurfaceDivisor(Number(e.target.value))}
                    required
                  />
                  <div className="form-text small">
                    Domestic road transport standard divisor is 4000: (L × W × H in cm) / 4000 = Volumetric kg.
                  </div>
                </div>

                <div className="mb-3">
                  <label className="form-label small fw-bold">Default GST SAC Code</label>
                  <input
                    type="text"
                    className="form-control"
                    value={gstSacCode}
                    onChange={(e) => setGstSacCode(e.target.value)}
                    required
                  />
                  <div className="form-text small">
                    Statutory code 996812 (Courier and cargo transportation services).
                  </div>
                </div>

                <div className="mb-3">
                  <label className="form-label small fw-bold">Financial Tax Round-Off Strategy</label>
                  <select
                    className="form-select"
                    value={taxRounding}
                    onChange={(e) => setTaxRounding(e.target.value)}
                  >
                    <option value="nearest_rupee">Nearest Whole Rupee (Math.round)</option>
                    <option value="exact_paise">Exact 2-Decimal Paise</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          {/* Section 3: Notification Channels */}
          <div className="col-12">
            <div className="card shadow-sm border-0">
              <div className="card-header bg-white py-3 border-bottom">
                <h6 className="fw-bold text-dark mb-0">
                  <i className="bi bi-bell text-primary me-2"></i> Customer Event Notifications
                </h6>
              </div>
              <div className="card-body">
                <div className="row g-3">
                  <div className="col-md-6">
                    <div className="form-check form-switch">
                      <input
                        className="form-check-input"
                        type="checkbox"
                        id="emailSwitch"
                        checked={autoEmailPod}
                        onChange={(e) => setAutoEmailPod(e.target.checked)}
                      />
                      <label className="form-check-label fw-semibold" htmlFor="emailSwitch">
                        Automated Email on Booking Confirmation & POD
                      </label>
                      <div className="small text-muted">
                        Sends digital docket receipt with barcode and milestone link to consignor.
                      </div>
                    </div>
                  </div>

                  <div className="col-md-6">
                    <div className="form-check form-switch">
                      <input
                        className="form-check-input"
                        type="checkbox"
                        id="smsSwitch"
                        checked={smsDeliveryAlert}
                        onChange={(e) => setSmsDeliveryAlert(e.target.checked)}
                      />
                      <label className="form-check-label fw-semibold" htmlFor="smsSwitch">
                        SMS / WhatsApp Out-For-Delivery Alerts
                      </label>
                      <div className="small text-muted">
                        Notifies consignee phone when delivery run sheet is initiated.
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div className="card-footer bg-light text-end py-3">
                <button type="submit" className="btn btn-primary px-4 fw-bold">
                  <i className="bi bi-save me-1"></i> Save Configuration
                </button>
              </div>
            </div>
          </div>
        </div>
      </form>
    </div>
  );
};

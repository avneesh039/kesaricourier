// frontend/src/pages/agency/TariffsPage.tsx
import React, { useState, useEffect } from 'react';
import api from '../../services/api';

export const TariffsPage: React.FC = () => {
  const [tariffs, setTariffs] = useState<any[]>([]);
  const [destinations, setDestinations] = useState<any[]>([]);
  const [loading, setLoading] = useState(true);

  // Rate calculator test state
  const [calcData, setCalcData] = useState({
    destinationId: 1,
    sendBy: 'Air',
    mode: 'Dox',
    deadWeight: 0.5,
    lengthCm: 20,
    widthCm: 15,
    heightCm: 10,
  });
  const [calcResult, setCalcResult] = useState<any | null>(null);
  const [calcLoading, setCalcLoading] = useState(false);

  const fetchData = async () => {
    try {
      setLoading(true);
      const [tariffsRes, destRes] = await Promise.all([
        api.get('/tariffs'),
        api.get('/superadmin/destinations').catch(() => ({ data: { data: [] } })),
      ]);
      if (tariffsRes.data.success) setTariffs(tariffsRes.data.data);
      if (destRes.data.data) {
        setDestinations(destRes.data.data);
        if (destRes.data.data[0]) {
          setCalcData((prev) => ({ ...prev, destinationId: destRes.data.data[0].id }));
        }
      }
    } catch (e) {
      console.error(e);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchData();
  }, []);

  const handleCalculate = async (e: React.FormEvent) => {
    e.preventDefault();
    try {
      setCalcLoading(true);
      const res = await api.post('/tariffs/calculate', calcData);
      if (res.data.success) {
        setCalcResult(res.data.data);
      }
    } catch (err: any) {
      alert(err.response?.data?.error?.message || 'Error calculating rate');
    } finally {
      setCalcLoading(false);
    }
  };

  return (
    <div>
      <div className="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2 className="brand-title mb-1">Commercial Tariff Matrix</h2>
          <p className="text-muted small mb-0">
            Multi-slab rate cards by operational zone, transport mode (Air / Surface), and consignment type
          </p>
        </div>
      </div>

      <div className="row g-4">
        {/* Left Column: Active Tariffs */}
        <div className="col-12 col-lg-7">
          <div className="app-card p-3">
            <h5 className="fw-bold mb-3">Configured Tariff Slabs</h5>
            {loading ? (
              <div className="text-center py-4 text-muted">
                <div className="spinner-border spinner-border-sm text-primary me-2"></div>
                Loading tariffs...
              </div>
            ) : tariffs.length === 0 ? (
              <div className="text-muted py-3 text-center">
                No custom tariffs defined yet. System will use base default rates.
              </div>
            ) : (
              tariffs.map((t) => (
                <div key={t.id} className="border rounded p-3 mb-3 bg-light">
                  <div className="d-flex justify-content-between align-items-center mb-2">
                    <div>
                      <span className="badge bg-primary me-2">{t.send_by} Express</span>
                      <span className="badge bg-secondary me-2">{t.mode}</span>
                      <span className="fw-semibold">{t.zone?.name || 'Zone'}</span>
                    </div>
                    <span className="small text-muted">
                      {t.client ? `Client: ${t.client.name}` : 'Agency Default'}
                    </span>
                  </div>

                  <div className="table-responsive">
                    <table className="table table-sm table-bordered bg-white mb-2 text-center">
                      <thead className="table-light small">
                        <tr>
                          <th>Weight From</th>
                          <th>Weight To</th>
                          <th>Freight Rate</th>
                        </tr>
                      </thead>
                      <tbody>
                        {t.slabs?.map((s: any) => (
                          <tr key={s.id}>
                            <td>{s.weight_from} kg</td>
                            <td>{s.weight_to} kg</td>
                            <td className="fw-bold text-primary">₹{Number(s.rate).toFixed(2)}</td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>

                  <div className="small text-muted d-flex justify-content-between">
                    <span>
                      Incremental Step: <strong>{t.additional_weight_step} kg</strong>
                    </span>
                    <span>
                      Additional Rate: <strong>₹{t.additional_weight_rate}</strong>
                    </span>
                  </div>
                </div>
              ))
            )}
          </div>
        </div>

        {/* Right Column: Live Interactive Rate & Volumetric Calculator */}
        <div className="col-12 col-lg-5">
          <div className="app-card p-4">
            <div className="d-flex align-items-center gap-2 mb-3">
              <i className="bi bi-calculator text-primary fs-4"></i>
              <div>
                <h5 className="fw-bold mb-0">Live Tariff & CFT Calculator</h5>
                <small className="text-muted">Instant rate engine test</small>
              </div>
            </div>

            <form onSubmit={handleCalculate}>
              <div className="mb-3">
                <label className="form-label small fw-semibold">Destination *</label>
                <select
                  className="form-select form-select-sm"
                  value={calcData.destinationId}
                  onChange={(e) => setCalcData({ ...calcData, destinationId: Number(e.target.value) })}
                >
                  {destinations.map((d) => (
                    <option key={d.id} value={d.id}>
                      {d.code} - {d.name} ({d.state?.name || ''})
                    </option>
                  ))}
                </select>
              </div>

              <div className="row g-2 mb-3">
                <div className="col-6">
                  <label className="form-label small fw-semibold">Service Mode</label>
                  <select
                    className="form-select form-select-sm"
                    value={calcData.sendBy}
                    onChange={(e) => setCalcData({ ...calcData, sendBy: e.target.value })}
                  >
                    <option value="Air">Air Express (CFT 5000)</option>
                    <option value="Surface">Surface Cargo (CFT 4000)</option>
                  </select>
                </div>
                <div className="col-6">
                  <label className="form-label small fw-semibold">Package Type</label>
                  <select
                    className="form-select form-select-sm"
                    value={calcData.mode}
                    onChange={(e) => setCalcData({ ...calcData, mode: e.target.value })}
                  >
                    <option value="Dox">Document (Dox)</option>
                    <option value="Non-Dox">Parcel (Non-Dox)</option>
                  </select>
                </div>
              </div>

              <div className="mb-3">
                <label className="form-label small fw-semibold">Dead Weight (kg) *</label>
                <input
                  type="number"
                  step="0.01"
                  className="form-control form-control-sm"
                  value={calcData.deadWeight}
                  onChange={(e) => setCalcData({ ...calcData, deadWeight: Number(e.target.value) })}
                  required
                />
              </div>

              <div className="row g-2 mb-3">
                <div className="col-4">
                  <label className="form-label small fw-semibold">Length (cm)</label>
                  <input
                    type="number"
                    className="form-control form-control-sm"
                    value={calcData.lengthCm}
                    onChange={(e) => setCalcData({ ...calcData, lengthCm: Number(e.target.value) })}
                  />
                </div>
                <div className="col-4">
                  <label className="form-label small fw-semibold">Width (cm)</label>
                  <input
                    type="number"
                    className="form-control form-control-sm"
                    value={calcData.widthCm}
                    onChange={(e) => setCalcData({ ...calcData, widthCm: Number(e.target.value) })}
                  />
                </div>
                <div className="col-4">
                  <label className="form-label small fw-semibold">Height (cm)</label>
                  <input
                    type="number"
                    className="form-control form-control-sm"
                    value={calcData.heightCm}
                    onChange={(e) => setCalcData({ ...calcData, heightCm: Number(e.target.value) })}
                  />
                </div>
              </div>

              <button type="submit" className="btn btn-primary btn-sm w-100 py-2 fw-semibold" disabled={calcLoading}>
                {calcLoading ? 'Calculating...' : 'Compute Billable Rate'}
              </button>
            </form>

            {/* Calculation Result Breakdown */}
            {calcResult && (
              <div className="rate-preview-card mt-3">
                <div className="d-flex justify-content-between align-items-center border-bottom border-light pb-2 mb-2">
                  <span className="small text-uppercase">Billable Weight:</span>
                  <span className="fs-5 fw-bold">{calcResult.weights?.billableWeight} kg</span>
                </div>
                <div className="small opacity-75 mb-2">
                  Dead: {calcResult.weights?.deadWeight} kg | Volumetric: {calcResult.weights?.volumetricWeight || 0} kg
                </div>
                <div className="d-flex justify-content-between small">
                  <span>Applied Slab:</span>
                  <span className="fw-semibold">{calcResult.rateCalculation?.appliedSlab}</span>
                </div>
                <div className="d-flex justify-content-between small">
                  <span>Subtotal:</span>
                  <span>₹{calcResult.rateCalculation?.subtotal}</span>
                </div>
                <div className="d-flex justify-content-between small">
                  <span>Fuel Surcharge:</span>
                  <span>₹{calcResult.rateCalculation?.fuelAmount}</span>
                </div>
                <div className="d-flex justify-content-between border-top border-light pt-2 mt-2 fs-5 fw-bold">
                  <span>Estimated Total:</span>
                  <span>₹{calcResult.rateCalculation?.totalAmount}</span>
                </div>
              </div>
            )}
          </div>
        </div>
      </div>
    </div>
  );
};

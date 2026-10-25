// frontend/src/pages/public/PublicTariffCalculator.tsx
import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import api from '../../services/api';

export const PublicTariffCalculator: React.FC = () => {
  const [destinations, setDestinations] = useState<any[]>([]);
  const [formData, setFormData] = useState({
    destinationId: 1,
    deadWeight: 0.5,
    sendBy: 'Air',
    lengthCm: 20,
    widthCm: 15,
    heightCm: 10,
  });
  const [result, setResult] = useState<any | null>(null);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    const fetchDest = async () => {
      try {
        const res = await api.get('/public/destinations');
        if (res.data.success) {
          setDestinations(res.data.data);
          if (res.data.data[0]) {
            setFormData((prev) => ({ ...prev, destinationId: res.data.data[0].id }));
          }
        }
      } catch (e) {
        console.error(e);
      }
    };
    fetchDest();
  }, []);

  const handleEstimate = async (e: React.FormEvent) => {
    e.preventDefault();
    setLoading(true);
    try {
      const res = await api.post('/public/tariff-estimate', formData);
      if (res.data.success) {
        setResult(res.data.data);
      }
    } catch (err: any) {
      alert(err.response?.data?.error?.message || 'Error estimating shipping tariff');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="min-vh-100 bg-light py-5">
      <div className="container" style={{ maxWidth: '640px' }}>
        <div className="text-center mb-4">
          <Link to="/" className="text-decoration-none">
            <img src="/kesari-favicon.svg" alt="Logo" width="48" height="48" className="rounded mb-2" />
            <h2 className="brand-title text-primary mb-0">Public Shipping Rate Estimator</h2>
          </Link>
          <p className="text-muted small">Compare dead weight vs IATA volumetric weight instantly</p>
        </div>

        <div className="app-card p-4 shadow-sm border-0 mb-4">
          <form onSubmit={handleEstimate}>
            <div className="mb-3">
              <label className="form-label small fw-semibold">Destination City / Hub *</label>
              <select
                className="form-select"
                value={formData.destinationId}
                onChange={(e) => setFormData({ ...formData, destinationId: Number(e.target.value) })}
              >
                {destinations.map((d) => (
                  <option key={d.id} value={d.id}>
                    [{d.code}] {d.name} — PIN: {d.pincode || 'N/A'} ({d.state?.name})
                  </option>
                ))}
              </select>
            </div>

            <div className="row g-2 mb-3">
              <div className="col-6">
                <label className="form-label small fw-semibold">Transit Service</label>
                <select
                  className="form-select"
                  value={formData.sendBy}
                  onChange={(e) => setFormData({ ...formData, sendBy: e.target.value })}
                >
                  <option value="Air">Air Express (CFT 5000)</option>
                  <option value="Surface">Surface Cargo (CFT 4000)</option>
                </select>
              </div>

              <div className="col-6">
                <label className="form-label small fw-semibold">Dead Weight (kg) *</label>
                <input
                  type="number"
                  step="0.01"
                  className="form-control"
                  value={formData.deadWeight}
                  onChange={(e) => setFormData({ ...formData, deadWeight: Number(e.target.value) })}
                  required
                />
              </div>
            </div>

            <div className="row g-2 mb-4">
              <div className="col-4">
                <label className="form-label small fw-semibold">Length (cm)</label>
                <input
                  type="number"
                  className="form-control"
                  value={formData.lengthCm}
                  onChange={(e) => setFormData({ ...formData, lengthCm: Number(e.target.value) })}
                />
              </div>
              <div className="col-4">
                <label className="form-label small fw-semibold">Width (cm)</label>
                <input
                  type="number"
                  className="form-control"
                  value={formData.widthCm}
                  onChange={(e) => setFormData({ ...formData, widthCm: Number(e.target.value) })}
                />
              </div>
              <div className="col-4">
                <label className="form-label small fw-semibold">Height (cm)</label>
                <input
                  type="number"
                  className="form-control"
                  value={formData.heightCm}
                  onChange={(e) => setFormData({ ...formData, heightCm: Number(e.target.value) })}
                />
              </div>
            </div>

            <button type="submit" className="btn btn-primary w-100 py-2 fw-semibold" disabled={loading}>
              {loading ? 'Calculating Tariff...' : 'Calculate Shipping Estimate'}
            </button>
          </form>
        </div>

        {result && (
          <div className="rate-preview-card shadow-sm">
            <div className="d-flex justify-content-between align-items-center border-bottom border-light pb-2 mb-3">
              <div>
                <span className="small text-uppercase">Destination Hub:</span>
                <div className="fs-5 fw-bold">{result.destination} ({result.state})</div>
              </div>
              <span className="badge bg-light text-primary fs-6 px-3 py-2">
                Billable: {result.weights?.billableWeight} kg
              </span>
            </div>

            <div className="small opacity-75 mb-3">
              Actual Weight: {result.weights?.deadWeight} kg | Volumetric (CFT): {result.weights?.volumetricWeight || 0} kg
            </div>

            <div className="d-flex justify-content-between small mb-1">
              <span>Estimated Freight:</span>
              <span>₹{result.estimatedSubtotal?.toFixed(2)}</span>
            </div>

            <div className="d-flex justify-content-between small mb-1">
              <span>GST SAC 996812 (18%):</span>
              <span>₹{result.gstAmount?.toFixed(2)}</span>
            </div>

            <div className="d-flex justify-content-between border-top border-light pt-2 mt-2 fs-4 fw-bolder">
              <span>Estimated Total:</span>
              <span>₹{result.estimatedTotal?.toFixed(2)}</span>
            </div>
          </div>
        )}

        <div className="text-center mt-4">
          <Link to="/login" className="text-decoration-none small text-muted">
            <i className="bi bi-box-arrow-in-right me-1"></i> Sign In to Franchise Portal
          </Link>
        </div>
      </div>
    </div>
  );
};

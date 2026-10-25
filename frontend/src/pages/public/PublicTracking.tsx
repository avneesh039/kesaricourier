// frontend/src/pages/public/PublicTracking.tsx
import React, { useState, useEffect } from 'react';
import { useParams, Link } from 'react-router-dom';
import api from '../../services/api';

export const PublicTracking: React.FC = () => {
  const { awb: paramAwb } = useParams();
  const [awbInput, setAwbInput] = useState(paramAwb || '');
  const [data, setData] = useState<any | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const handleTrack = async (awbToTrack: string) => {
    if (!awbToTrack.trim()) return;
    setLoading(true);
    setError(null);
    try {
      const res = await api.get(`/public/track/${awbToTrack.trim()}`);
      if (res.data.success) {
        setData(res.data.data);
      }
    } catch (err: any) {
      setError(
        err.response?.data?.error?.message ||
          `No shipment records found for tracking number ${awbToTrack}.`
      );
      setData(null);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (paramAwb) {
      handleTrack(paramAwb);
    }
  }, [paramAwb]);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    handleTrack(awbInput);
  };

  return (
    <div className="min-vh-100 bg-light py-5">
      <div className="container" style={{ maxWidth: '780px' }}>
        {/* Header */}
        <div className="text-center mb-4">
          <Link to="/" className="text-decoration-none">
            <img src="/kesari-favicon.svg" alt="Logo" width="50" height="50" className="rounded mb-2" />
            <h2 className="brand-title text-primary mb-0">Kesari Express Tracking</h2>
          </Link>
          <p className="text-muted small">Live 5-Stage Multi-Hub Delivery Dossier</p>
        </div>

        {/* Search Bar */}
        <div className="app-card p-3 mb-4 shadow-sm border-0">
          <form onSubmit={handleSubmit} className="d-flex gap-2">
            <div className="input-group">
              <span className="input-group-text bg-white border-end-0">
                <i className="bi bi-search text-muted"></i>
              </span>
              <input
                type="text"
                className="form-control border-start-0 font-monospace fs-6"
                placeholder="Enter 14-digit AWB (e.g. 25011200064307)..."
                value={awbInput}
                onChange={(e) => setAwbInput(e.target.value)}
                required
              />
            </div>
            <button type="submit" className="btn btn-primary px-4 fw-semibold" disabled={loading}>
              {loading ? 'Tracking...' : 'Track'}
            </button>
          </form>
        </div>

        {/* Quick Demo AWBs */}
        <div className="d-flex justify-content-center align-items-center gap-2 small text-muted mb-4">
          <span>Try Demo AWBs:</span>
          <button
            type="button"
            className="btn btn-sm btn-outline-secondary py-0 font-monospace"
            onClick={() => {
              setAwbInput('25011200064307');
              handleTrack('25011200064307');
            }}
          >
            25011200064307
          </button>
          <button
            type="button"
            className="btn btn-sm btn-outline-secondary py-0 font-monospace"
            onClick={() => {
              setAwbInput('25011200064296');
              handleTrack('25011200064296');
            }}
          >
            25011200064296
          </button>
        </div>

        {error && <div className="alert alert-danger text-center py-2">{error}</div>}

        {/* Tracking Result Dossier */}
        {data && (
          <div className="app-card p-4 shadow-sm border-0">
            {/* Dossier Header */}
            <div className="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center border-bottom pb-3 mb-4 gap-2">
              <div>
                <span className="text-muted small text-uppercase fw-bold">AWB Number</span>
                <div className="fs-4 fw-bolder font-monospace text-primary">{data.consignmentNo}</div>
              </div>
              <span className={`badge-status ${data.status} fs-6 px-3 py-2`}>
                {data.status.replace('_', ' ')}
              </span>
            </div>

            {/* Quick Meta Grid */}
            <div className="row g-3 bg-light p-3 rounded mb-4 text-center">
              <div className="col-4 border-end">
                <div className="text-muted small">Origin</div>
                <div className="fw-bold">{data.origin}</div>
              </div>
              <div className="col-4 border-end">
                <div className="text-muted small">Destination</div>
                <div className="fw-bold">{data.destination}</div>
              </div>
              <div className="col-4">
                <div className="text-muted small">Weight & Service</div>
                <div className="fw-bold">
                  {data.billableWeight} kg • {data.service}
                </div>
              </div>
            </div>

            {/* 5-Stage Progression Timeline */}
            <h6 className="fw-bold text-uppercase text-muted small mb-3">Shipment Progress Journey</h6>

            <div className="position-relative ps-4 ms-2">
              <div
                className="position-absolute top-0 start-0 h-100 border-start border-2 border-primary"
                style={{ left: '7px' }}
              ></div>

              {data.milestones?.map((m: any, idx: number) => (
                <div key={idx} className="position-relative mb-4">
                  <div
                    className={`position-absolute rounded-circle border border-2 border-white d-flex align-items-center justify-content-center ${
                      m.completed ? 'bg-primary text-white' : 'bg-secondary text-white'
                    }`}
                    style={{
                      left: '-28px',
                      top: '0px',
                      width: '20px',
                      height: '20px',
                      fontSize: '10px',
                    }}
                  >
                    {m.completed ? <i className="bi bi-check"></i> : idx + 1}
                  </div>

                  <div>
                    <div className="d-flex justify-content-between align-items-baseline">
                      <h6 className={`mb-0 ${m.completed ? 'fw-bold text-dark' : 'text-muted'}`}>
                        {m.title}
                      </h6>
                      {m.timestamp && (
                        <span className="small text-muted">
                          {new Date(m.timestamp).toLocaleString()}
                        </span>
                      )}
                    </div>
                    <p className="text-muted small mb-0 mt-1">{m.description}</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}
      </div>
    </div>
  );
};

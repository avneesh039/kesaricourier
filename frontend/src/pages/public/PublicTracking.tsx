// frontend/src/pages/public/PublicTracking.tsx
import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import api from '../../services/api';
import { PublicNavbar } from '../../components/public/PublicNavbar';
import { PublicFooter } from '../../components/public/PublicFooter';

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
    <div className="min-vh-100 d-flex flex-column bg-surface animate-enter">
      <PublicNavbar />
      <div className="flex-grow-1 py-4 py-md-5">
        <div className="container" style={{ maxWidth: '820px' }}>
          {/* Header */}
          <div className="text-center mb-4">
            <h2 className="brand-title text-primary mb-1 display-6 fs-3">Live Consignment Tracking</h2>
            <p className="text-muted small">5-Stage Multi-Carrier & Regional Hub Line-Haul Dossier</p>
          </div>

        {/* Search Bar */}
        <div className="app-card p-3 p-sm-4 mb-4 shadow-sm border-0">
          <form onSubmit={handleSubmit} className="d-flex flex-column flex-sm-row gap-2">
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
            <button type="submit" className="btn btn-primary px-4 fw-semibold w-100 w-sm-auto text-nowrap" disabled={loading}>
              {loading ? (
                <>
                  <span className="spinner-border spinner-border-sm me-2"></span>
                  Tracking...
                </>
              ) : (
                <>
                  <i className="bi bi-search me-1"></i> Track Consignment
                </>
              )}
            </button>
          </form>
        </div>

        {/* Quick Demo AWBs */}
        <div className="d-flex flex-wrap justify-content-center align-items-center gap-2 small text-muted mb-4">
          <span className="fw-semibold">Try Demo AWBs:</span>
          <button
            type="button"
            className="btn btn-sm btn-outline-secondary py-1 px-2 font-monospace"
            onClick={() => {
              setAwbInput('25011200064307');
              handleTrack('25011200064307');
            }}
          >
            25011200064307
          </button>
          <button
            type="button"
            className="btn btn-sm btn-outline-secondary py-1 px-2 font-monospace"
            onClick={() => {
              setAwbInput('25011200064296');
              handleTrack('25011200064296');
            }}
          >
            25011200064296
          </button>
        </div>

        {error && (
          <div className="alert alert-danger text-center py-3 mb-4 rounded-3 border-0 shadow-xs">
            <i className="bi bi-exclamation-circle-fill me-2"></i>
            {error}
          </div>
        )}

        {/* Tracking Result Dossier */}
        {data && (
          <div className="app-card p-3 p-sm-4 shadow-sm border-0">
            {/* Dossier Header */}
            <div className="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center border-bottom pb-3 mb-4 gap-2">
              <div>
                <span className="text-muted small text-uppercase fw-bold">AWB Number</span>
                <div className="fs-4 fw-bolder font-monospace text-primary">{data.consignmentNo}</div>
              </div>
              <span className={`badge-status ${data.status} fs-6 px-3 py-2 align-self-start align-self-sm-center`}>
                {data.status.replace('_', ' ')}
              </span>
            </div>

            {/* Quick Meta Grid */}
            <div className="row g-2 g-sm-3 bg-light p-3 rounded mb-4 text-center">
              <div className="col-12 col-sm-4 border-bottom border-bottom-sm-0 border-end-sm pb-2 pb-sm-0">
                <div className="text-muted small">Origin Hub</div>
                <div className="fw-bold">{data.origin}</div>
              </div>
              <div className="col-12 col-sm-4 border-bottom border-bottom-sm-0 border-end-sm pb-2 pb-sm-0">
                <div className="text-muted small">Destination Hub</div>
                <div className="fw-bold">{data.destination}</div>
              </div>
              <div className="col-12 col-sm-4 pt-1 pt-sm-0">
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
                      m.completed ? 'bg-primary text-white shadow-xs' : 'bg-secondary text-white'
                    }`}
                    style={{
                      left: '-28px',
                      top: '0px',
                      width: '22px',
                      height: '22px',
                      fontSize: '11px',
                    }}
                  >
                    {m.completed ? <i className="bi bi-check-lg"></i> : idx + 1}
                  </div>

                  <div>
                    <div className="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-baseline gap-1">
                      <h6 className={`mb-0 ${m.completed ? 'fw-bold text-dark' : 'text-muted'}`}>
                        {m.title}
                      </h6>
                      {m.timestamp && (
                        <span className="small text-muted font-monospace" style={{ fontSize: '0.75rem' }}>
                          {new Date(m.timestamp).toLocaleString('en-IN', {
                            dateStyle: 'medium',
                            timeStyle: 'short',
                          })}
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
      <PublicFooter />
    </div>
  );
};

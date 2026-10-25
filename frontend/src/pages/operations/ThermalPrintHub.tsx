// frontend/src/pages/operations/ThermalPrintHub.tsx
import React, { useState, useEffect } from 'react';
import { useSearchParams } from 'react-router-dom';
import api from '../../services/api';
import { ThermalLabel } from '../../components/print/ThermalLabel';

export const ThermalPrintHub: React.FC = () => {
  const [searchParams] = useSearchParams();
  const awbFromUrl = searchParams.get('awb');

  const [awbInput, setAwbInput] = useState(awbFromUrl || '');
  const [labelData, setLabelData] = useState<any | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const fetchLabel = async (awb: string) => {
    if (!awb.trim()) return;
    setLoading(true);
    setError(null);
    try {
      const res = await api.get(`/operations/print/single/${awb.trim()}`);
      if (res.data.success) {
        setLabelData(res.data.data);
      }
    } catch (err: any) {
      setError(err.response?.data?.error?.message || 'Consignment not found for printing.');
      setLabelData(null);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (awbFromUrl) {
      fetchLabel(awbFromUrl);
    }
  }, [awbFromUrl]);

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    fetchLabel(awbInput);
  };

  return (
    <div>
      <div className="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
          <h2 className="brand-title mb-1">Thermal Label Print Hub (4x6")</h2>
          <p className="text-muted small mb-0">
            Industrial thermal sticky label generation calibrated for Zebra, TSC, TVS, and Rollo 4x6" printers
          </p>
        </div>

        {labelData && (
          <button className="btn btn-primary" onClick={() => window.print()}>
            <i className="bi bi-printer-fill me-1"></i> Print Thermal Docket
          </button>
        )}
      </div>

      {/* AWB Search Input */}
      <div className="app-card p-3 mb-4 no-print">
        <form onSubmit={handleSubmit} className="row g-2 align-items-center">
          <div className="col-12 col-md-6">
            <div className="input-group">
              <span className="input-group-text bg-light">
                <i className="bi bi-barcode"></i>
              </span>
              <input
                type="text"
                className="form-control font-monospace"
                placeholder="Scan or enter 14-digit AWB number..."
                value={awbInput}
                onChange={(e) => setAwbInput(e.target.value)}
              />
            </div>
          </div>
          <div className="col-12 col-md-2">
            <button type="submit" className="btn btn-primary w-100" disabled={loading}>
              {loading ? 'Searching...' : 'Load Docket'}
            </button>
          </div>
        </form>
      </div>

      {error && <div className="alert alert-danger py-2 small no-print">{error}</div>}

      {/* Label Preview Card */}
      {labelData ? (
        <div className="d-flex flex-column align-items-center">
          <div className="alert alert-info py-2 small mb-3 no-print text-center" style={{ maxWidth: '100mm' }}>
            <i className="bi bi-info-circle me-1"></i> Ready for 4x6" thermal printer. Click <strong>Print Thermal Docket</strong>.
          </div>

          <div className="print-area shadow-sm">
            <ThermalLabel
              consignment={labelData.booking}
              barcodeSvg={labelData.barcodeSvg}
              trackingUrl={labelData.trackingUrl}
            />
          </div>
        </div>
      ) : (
        !loading && (
          <div className="text-center py-5 text-muted no-print">
            <i className="bi bi-printer display-3 text-secondary mb-2"></i>
            <p>Scan a barcode or enter an AWB number above to preview and print shipping labels.</p>
          </div>
        )
      )}
    </div>
  );
};

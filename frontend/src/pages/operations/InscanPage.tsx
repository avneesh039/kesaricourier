// frontend/src/pages/operations/InscanPage.tsx
import React, { useState, useRef } from 'react';

interface InscanRow {
  id: number;
  docketNo: string;
  pieces: number;
  weight: number;
  destinationPin: string;
  status: 'pending' | 'saved';
}

export const InscanPage: React.FC = () => {
  // Locked header fields as per handwritten image 4.45.54 PM
  const [courierName, setCourierName] = useState('Kesari Express Hub');
  const [lockCourier, setLockCourier] = useState(true);

  const [receivedFromPin, setReceivedFromPin] = useState('492001');
  const [lockReceivedPin, setLockReceivedPin] = useState(true);

  const [rows, setRows] = useState<InscanRow[]>([
    { id: 1, docketNo: '', pieces: 1, weight: 0.5, destinationPin: '400001', status: 'pending' },
    { id: 2, docketNo: '', pieces: 1, weight: 0.25, destinationPin: '400001', status: 'pending' },
    { id: 3, docketNo: '', pieces: 1, weight: 1.0, destinationPin: '400001', status: 'pending' },
    { id: 4, docketNo: '', pieces: 1, weight: 0.5, destinationPin: '400001', status: 'pending' },
  ]);

  const [savedCount, setSavedCount] = useState(0);
  const tableRef = useRef<HTMLTableElement>(null);

  const handleRowChange = (id: number, field: keyof InscanRow, value: any) => {
    setRows((prev) =>
      prev.map((r) => (r.id === id ? { ...r, [field]: value } : r))
    );
  };

  const handleSaveRow = (id: number) => {
    const row = rows.find((r) => r.id === id);
    if (!row || !row.docketNo.trim()) {
      alert('Please enter a valid Docket Number.');
      return;
    }

    setRows((prev) =>
      prev.map((r) => (r.id === id ? { ...r, status: 'saved' } : r))
    );
    setSavedCount((c) => c + 1);

    // Auto append next empty row if last
    if (rows[rows.length - 1].id === id) {
      setRows((prev) => [
        ...prev,
        {
          id: Date.now(),
          docketNo: '',
          pieces: 1,
          weight: 0.5,
          destinationPin: row.destinationPin,
          status: 'pending',
        },
      ]);
    }
  };

  const addEmptyRow = () => {
    setRows((prev) => [
      ...prev,
      {
        id: Date.now(),
        docketNo: '',
        pieces: 1,
        weight: 0.5,
        destinationPin: rows[rows.length - 1]?.destinationPin || '400001',
        status: 'pending',
      },
    ]);
  };

  // Keyboard navigation: Enter on a field saves or advances
  const handleKeyDown = (e: React.KeyboardEvent, id: number, field: keyof InscanRow) => {
    if (e.key === 'Enter') {
      e.preventDefault();
      if (field === 'destinationPin') {
        handleSaveRow(id);
      }
    }
  };

  return (
    <div className="animate-enter">
      {/* Header */}
      <div className="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3">
        <div>
          <div className="d-flex align-items-center gap-2">
            <h2 className="brand-title mb-0">High-Speed Inscan Operations</h2>
            <span className="badge bg-warning text-dark border">Numerical Entry Grid</span>
          </div>
          <p className="text-muted small mb-0 mt-1">
            Incoming Branch Parcel Processing • Locked Courier & Origin • Keyboard Barcode Inscan (Image 4.45.54)
          </p>
        </div>

        <div className="d-flex align-items-center gap-2">
          <span className="badge bg-success font-monospace fs-6 px-3 py-2">
            Scanned & Saved: {savedCount} Dockets
          </span>
          <button className="btn btn-outline-primary btn-sm" onClick={addEmptyRow}>
            + Add Row
          </button>
        </div>
      </div>

      {/* Header Locks Card */}
      <div className="app-card p-3 shadow-sm border mb-3 bg-light">
        <div className="row g-3 align-items-center">
          <div className="col-md-6">
            <div className="d-flex justify-content-between align-items-center mb-1">
              <label className="form-label small fw-bold mb-0">Courier / Agency Name:</label>
              <label className="form-check-label small text-muted d-flex align-items-center gap-1" style={{ fontSize: '0.75rem' }}>
                <input
                  type="checkbox"
                  className="form-check-input mt-0"
                  checked={lockCourier}
                  onChange={(e) => setLockCourier(e.target.checked)}
                />
                Lock
              </label>
            </div>
            <select
              className={`form-select form-select-sm fw-semibold ${lockCourier ? 'border-primary bg-white' : ''}`}
              value={courierName}
              onChange={(e) => setCourierName(e.target.value)}
            >
              <option value="Kesari Express Hub">Kesari Express Hub (Main Branch)</option>
              <option value="Delhivery Logistics">Delhivery Partner Hub</option>
              <option value="Xpressbees Logistics">Xpressbees Incoming Van</option>
              <option value="GMS Worldwide">GMS Worldwide Express</option>
            </select>
          </div>

          <div className="col-md-6">
            <div className="d-flex justify-content-between align-items-center mb-1">
              <label className="form-label small fw-bold mb-0">Received From PIN Code:</label>
              <label className="form-check-label small text-muted d-flex align-items-center gap-1" style={{ fontSize: '0.75rem' }}>
                <input
                  type="checkbox"
                  className="form-check-input mt-0"
                  checked={lockReceivedPin}
                  onChange={(e) => setLockReceivedPin(e.target.checked)}
                />
                Lock
              </label>
            </div>
            <input
              type="text"
              maxLength={6}
              className={`form-control form-control-sm font-monospace fw-bold ${lockReceivedPin ? 'border-primary bg-white' : ''}`}
              value={receivedFromPin}
              onChange={(e) => setReceivedFromPin(e.target.value)}
              placeholder="e.g. 492001 (Raipur)"
            />
          </div>
        </div>
      </div>

      {/* Numerical Inscan Grid Table */}
      <div className="app-card shadow-sm border overflow-hidden">
        <div className="table-responsive">
          <table ref={tableRef} className="table table-bordered align-middle mb-0 text-center">
            <thead className="table-dark small text-uppercase" style={{ fontSize: '0.75rem' }}>
              <tr>
                <th style={{ width: '60px' }}>SL</th>
                <th style={{ width: '280px' }}>Docket Number (Barcode / Scan)</th>
                <th style={{ width: '100px' }}>PCS</th>
                <th style={{ width: '120px' }}>Weight (KG)</th>
                <th style={{ width: '180px' }}>Destination PIN</th>
                <th style={{ width: '120px' }}>Action</th>
              </tr>
            </thead>
            <tbody>
              {rows.map((row, idx) => (
                <tr key={row.id} className={row.status === 'saved' ? 'table-success' : ''}>
                  <td className="fw-bold font-monospace">{idx + 1}</td>
                  <td>
                    <input
                      type="text"
                      className="form-control form-control-sm font-monospace fw-bold text-center"
                      placeholder="Scan or enter AWB..."
                      value={row.docketNo}
                      disabled={row.status === 'saved'}
                      onChange={(e) => handleRowChange(row.id, 'docketNo', e.target.value)}
                      onKeyDown={(e) => handleKeyDown(e, row.id, 'docketNo')}
                    />
                  </td>
                  <td>
                    <input
                      type="number"
                      min={1}
                      className="form-control form-control-sm text-center"
                      value={row.pieces}
                      disabled={row.status === 'saved'}
                      onChange={(e) => handleRowChange(row.id, 'pieces', Number(e.target.value))}
                      onKeyDown={(e) => handleKeyDown(e, row.id, 'pieces')}
                    />
                  </td>
                  <td>
                    <input
                      type="number"
                      step="0.01"
                      className="form-control form-control-sm text-center"
                      value={row.weight}
                      disabled={row.status === 'saved'}
                      onChange={(e) => handleRowChange(row.id, 'weight', Number(e.target.value))}
                      onKeyDown={(e) => handleKeyDown(e, row.id, 'weight')}
                    />
                  </td>
                  <td>
                    <input
                      type="text"
                      maxLength={6}
                      className="form-control form-control-sm font-monospace text-center"
                      value={row.destinationPin}
                      disabled={row.status === 'saved'}
                      onChange={(e) => handleRowChange(row.id, 'destinationPin', e.target.value)}
                      onKeyDown={(e) => handleKeyDown(e, row.id, 'destinationPin')}
                    />
                  </td>
                  <td>
                    {row.status === 'saved' ? (
                      <span className="badge bg-success">
                        <i className="bi bi-check-circle me-1"></i> Saved
                      </span>
                    ) : (
                      <button
                        type="button"
                        className="btn btn-primary btn-sm px-3 fw-bold"
                        onClick={() => handleSaveRow(row.id)}
                      >
                        [Save]
                      </button>
                    )}
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </div>
    </div>
  );
};

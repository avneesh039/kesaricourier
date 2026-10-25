// frontend/src/pages/bookings/BookingCounter.tsx
import React, { useState, useEffect, useRef } from 'react';
import api from '../../services/api';
import { Client, Destination } from '../../types';
import { ThermalLabel } from '../../components/print/ThermalLabel';

interface BoxDimension {
  id: number;
  length: number;
  width: number;
  height: number;
  count: number;
}

export const BookingCounter: React.FC = () => {
  const [clients, setClients] = useState<Client[]>([]);
  const [destinations, setDestinations] = useState<Destination[]>([]);
  const [submitting, setSubmitting] = useState(false);
  const formRef = useRef<HTMLFormElement>(null);

  // Field Lock States (Sticky Fields - as specified in handwritten spec)
  const [locks, setLocks] = useState({
    date: true,
    customer: false,
    fromPin: true,
    destPin: false,
    pieces: true,
    weight: false,
    volumetric: false,
    consignee: false,
    product: false,
    mode: true,
    risk: true,
  });

  // Multi-box volumetric dimensions repeater
  const [boxes, setBoxes] = useState<BoxDimension[]>([
    { id: 1, length: 0, width: 0, height: 0, count: 1 },
  ]);

  // Form Data State
  const [formData, setFormData] = useState({
    bookingDate: new Date().toISOString().split('T')[0],
    customerCode: '',
    clientId: 0,
    clientName: '',
    senderPhone: '',
    fromPincode: '492001',
    fromCityState: 'Raipur, Chhattisgarh',
    destinationPincode: '',
    destinationCityState: '',
    destinationZone: '',
    destinationId: 0,
    pieces: 1,
    deadWeight: 0.25,
    volumetricWeight: 0,
    chargeableWeight: 0.25,
    recipientName: '',
    recipientPhone: '',
    recipientAddress: '',
    productDescription: 'Commercial Documents / Samples',
    mode: 'Dox' as 'Dox' | 'Non-Dox',
    sendBy: 'Surface' as 'Air' | 'Surface' | 'Fast Surface' | 'By Hand' | 'Self Vehicle',
    insuredValue: 0,
    riskType: 'Owner Risk' as 'Owner Risk' | 'Carrier Risk',
    subtotal: 40,
    fuelSurchargeAmount: 0,
    docketCharge: 0,
    totalAmount: 40,
  });

  const [bookedConsignment, setBookedConsignment] = useState<any | null>(null);
  const [showPrintModal, setShowPrintModal] = useState(false);

  // Load initial clients and destinations
  useEffect(() => {
    const initData = async () => {
      try {
        const [cRes, dRes] = await Promise.all([
          api.get('/clients'),
          api.get('/superadmin/destinations').catch(() => ({ data: { data: [] } })),
        ]);
        if (cRes.data.success && cRes.data.data.length > 0) {
          setClients(cRes.data.data);
          const first = cRes.data.data[0];
          setFormData((prev) => ({
            ...prev,
            clientId: first.id,
            customerCode: first.client_code,
            clientName: first.name,
            senderPhone: first.phone || '9893000000',
          }));
        }
        if (dRes.data.data && dRes.data.data.length > 0) {
          setDestinations(dRes.data.data);
          const firstDest = dRes.data.data[0];
          setFormData((prev) => ({
            ...prev,
            destinationId: firstDest.id,
            destinationPincode: firstDest.pincode || '400001',
            destinationCityState: `${firstDest.name}, ${firstDest.state?.name || ''}`,
            destinationZone: firstDest.state?.zone?.name || 'West Zone',
          }));
        }
      } catch (e) {
        console.error('Failed to load initial booking data', e);
      }
    };
    initData();
  }, []);

  // Compute total volumetric weight across all box dimensions
  useEffect(() => {
    const divisor = formData.sendBy === 'Air' ? 5000 : formData.sendBy === 'Fast Surface' ? 4500 : 4000;
    let totalVol = 0;
    boxes.forEach((b) => {
      if (b.length > 0 && b.width > 0 && b.height > 0) {
        const volPerBox = (b.length * b.width * b.height) / divisor;
        totalVol += volPerBox * (b.count || 1);
      }
    });
    const roundedVol = Math.round(totalVol * 100) / 100;
    const finalBillable = Math.max(formData.deadWeight, roundedVol);

    setFormData((prev) => ({
      ...prev,
      volumetricWeight: roundedVol,
      chargeableWeight: finalBillable,
    }));
  }, [boxes, formData.sendBy, formData.deadWeight]);

  // Destination PIN Code auto-lookup & zone resolution
  const handleDestPinChange = (pin: string) => {
    setFormData((prev) => ({ ...prev, destinationPincode: pin }));
    if (pin.length >= 6) {
      const match = destinations.find((d) => d.pincode === pin);
      if (match) {
        // Check if within same city or same state as origin
        const isWithinCity = match.pincode?.startsWith(formData.fromPincode.slice(0, 3));
        const zoneTag = isWithinCity
          ? 'Within City'
          : match.state?.name?.toLowerCase().includes('chhattisgarh')
          ? 'Within State'
          : match.state?.zone?.name || 'Regional Zone';

        setFormData((prev) => ({
          ...prev,
          destinationId: match.id,
          destinationCityState: `${match.name}, ${match.state?.name || ''}`,
          destinationZone: zoneTag,
        }));
      }
    }
  };

  // Customer Code auto-search
  const handleCustomerCodeSearch = (code: string) => {
    setFormData((prev) => ({ ...prev, customerCode: code }));
    const match = clients.find(
      (c) => c.client_code.toLowerCase() === code.toLowerCase() || c.name.toLowerCase().includes(code.toLowerCase())
    );
    if (match) {
      setFormData((prev) => ({
        ...prev,
        clientId: match.id,
        customerCode: match.client_code,
        clientName: match.name,
        senderPhone: match.phone || prev.senderPhone,
      }));
    }
  };

  // Recalculate freight rate whenever parameters change
  useEffect(() => {
    if (!formData.destinationId || formData.chargeableWeight <= 0) return;

    const compute = async () => {
      try {
        const res = await api.post('/tariffs/calculate', {
          destinationId: formData.destinationId,
          sendBy: formData.sendBy === 'Fast Surface' ? 'Air' : formData.sendBy,
          mode: formData.mode,
          deadWeight: formData.chargeableWeight,
          clientId: formData.clientId || undefined,
        });

        if (res.data.success) {
          const r = res.data.data.rateCalculation;
          setFormData((prev) => ({
            ...prev,
            subtotal: r.subtotal,
            fuelSurchargeAmount: r.fuelAmount,
            docketCharge: r.docketCharge,
            totalAmount: r.totalAmount,
          }));
        }
      } catch (err) {
        // Fallback calculations
      }
    };

    const timer = setTimeout(compute, 250);
    return () => clearTimeout(timer);
  }, [
    formData.destinationId,
    formData.sendBy,
    formData.mode,
    formData.chargeableWeight,
    formData.clientId,
  ]);

  // Multi-box dimension management
  const addBoxRow = () => {
    setBoxes((prev) => [
      ...prev,
      { id: Date.now(), length: 0, width: 0, height: 0, count: 1 },
    ]);
  };

  const removeBoxRow = (id: number) => {
    if (boxes.length === 1) return;
    setBoxes((prev) => prev.filter((b) => b.id !== id));
  };

  const updateBox = (id: number, field: keyof BoxDimension, val: number) => {
    setBoxes((prev) =>
      prev.map((b) => (b.id === id ? { ...b, [field]: val } : b))
    );
  };

  // Keyboard navigation: Enter key advances to next input field
  const handleKeyDown = (e: React.KeyboardEvent) => {
    if (e.key === 'Enter' && e.target instanceof HTMLElement && e.target.tagName !== 'BUTTON') {
      e.preventDefault();
      const form = formRef.current;
      if (!form) return;
      const elements = Array.from(
        form.querySelectorAll<HTMLInputElement | HTMLSelectElement>('input:not([type="checkbox"]):not([type="hidden"]), select')
      );
      const index = elements.indexOf(e.target as any);
      if (index > -1 && index < elements.length - 1) {
        elements[index + 1].focus();
      }
    }
  };

  // Keyboard shortcut: Alt+S saves booking
  useEffect(() => {
    const handleGlobalShortcuts = (e: KeyboardEvent) => {
      if ((e.altKey && e.key.toLowerCase() === 's') || e.key === 'F2') {
        e.preventDefault();
        formRef.current?.requestSubmit();
      }
    };
    window.addEventListener('keydown', handleGlobalShortcuts);
    return () => window.removeEventListener('keydown', handleGlobalShortcuts);
  }, []);

  // Form Submission with Sticky Fields Lock Logic
  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault();
    setSubmitting(true);
    try {
      const payload = {
        bookingDate: formData.bookingDate,
        clientId: formData.clientId,
        senderName: formData.clientName,
        senderPhone: formData.senderPhone,
        recipientName: formData.recipientName,
        recipientPhone: formData.recipientPhone,
        recipientAddress: formData.recipientAddress,
        destinationId: formData.destinationId,
        destinationPincode: formData.destinationPincode,
        fromPincode: formData.fromPincode,
        productDescription: formData.productDescription,
        mode: formData.mode,
        sendBy: formData.sendBy,
        pieces: formData.pieces,
        deadWeight: formData.deadWeight,
        volumetricWeight: formData.volumetricWeight,
        lengthCm: boxes[0]?.length || undefined,
        widthCm: boxes[0]?.width || undefined,
        heightCm: boxes[0]?.height || undefined,
        subtotal: formData.subtotal,
        fuelSurchargeAmount: formData.fuelSurchargeAmount,
        docketCharge: formData.docketCharge,
        insuredValue: formData.insuredValue,
        totalAmount: formData.totalAmount,
      };

      const res = await api.post('/bookings', payload);
      if (res.data.success) {
        setBookedConsignment(res.data.data);
        setShowPrintModal(true);

        // STICKY FIELDS RESET: Only reset fields whose lock is FALSE
        setFormData((prev) => ({
          ...prev,
          bookingDate: locks.date ? prev.bookingDate : new Date().toISOString().split('T')[0],
          customerCode: locks.customer ? prev.customerCode : '',
          clientId: locks.customer ? prev.clientId : 0,
          clientName: locks.customer ? prev.clientName : '',
          fromPincode: locks.fromPin ? prev.fromPincode : '492001',
          destinationPincode: locks.destPin ? prev.destinationPincode : '',
          destinationCityState: locks.destPin ? prev.destinationCityState : '',
          destinationZone: locks.destPin ? prev.destinationZone : '',
          pieces: locks.pieces ? prev.pieces : 1,
          deadWeight: locks.weight ? prev.deadWeight : 0.25,
          volumetricWeight: locks.volumetric ? prev.volumetricWeight : 0,
          chargeableWeight: locks.weight ? prev.deadWeight : 0.25,
          recipientName: locks.consignee ? prev.recipientName : '',
          recipientPhone: locks.consignee ? prev.recipientPhone : '',
          recipientAddress: locks.consignee ? prev.recipientAddress : '',
          productDescription: locks.product ? prev.productDescription : 'Commercial Documents / Samples',
          sendBy: locks.mode ? prev.sendBy : 'Surface',
          riskType: locks.risk ? prev.riskType : 'Owner Risk',
          insuredValue: 0,
        }));

        if (!locks.volumetric) {
          setBoxes([{ id: 1, length: 0, width: 0, height: 0, count: 1 }]);
        }
      }
    } catch (err: any) {
      alert(err.response?.data?.error?.message || 'Error creating consignment booking');
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <div className="animate-enter" onKeyDown={handleKeyDown}>
      {/* Page Title & Hotkey Banner */}
      <div className="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2 mb-3">
        <div>
          <div className="d-flex align-items-center gap-2">
            <h2 className="brand-title mb-0">Rapid Consignment Booking Counter</h2>
            <span className="badge bg-primary-subtle text-primary border font-monospace">
              Handwritten Spec Calibrated
            </span>
          </div>
          <p className="text-muted small mb-0 mt-1">
            Numerical-optimized counter entry • Sticky Field Locks for bulk dockets • Auto-zone rating
          </p>
        </div>

        <div className="d-none d-sm-flex align-items-center gap-2">
          <span className="badge bg-dark text-white px-2 py-1 font-monospace" style={{ fontSize: '0.75rem' }}>
            Enter = Next Field
          </span>
          <span className="badge bg-dark text-white px-2 py-1 font-monospace" style={{ fontSize: '0.75rem' }}>
            Alt+S = Quick Save
          </span>
        </div>
      </div>

      <div className="row g-3">
        {/* Main 14-Field Form with Locks */}
        <div className="col-12 col-xl-8">
          <form ref={formRef} onSubmit={handleSubmit} className="app-card p-3 shadow-sm border">
            {/* Top Quick Action Bar */}
            <div className="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
              <span className="small fw-bold text-uppercase text-primary">
                <i className="bi bi-pencil-square me-1"></i> Docket Entry Window (Revenue Engine)
              </span>
              <div className="d-flex gap-2">
                <button
                  type="button"
                  className="btn btn-outline-secondary btn-xs py-0 px-2"
                  style={{ fontSize: '0.75rem' }}
                  onClick={() =>
                    setLocks({
                      date: true,
                      customer: true,
                      fromPin: true,
                      destPin: true,
                      pieces: true,
                      weight: true,
                      volumetric: true,
                      consignee: true,
                      product: true,
                      mode: true,
                      risk: true,
                    })
                  }
                >
                  <i className="bi bi-lock-fill me-1"></i> Lock All
                </button>
                <button
                  type="button"
                  className="btn btn-outline-secondary btn-xs py-0 px-2"
                  style={{ fontSize: '0.75rem' }}
                  onClick={() =>
                    setLocks({
                      date: false,
                      customer: false,
                      fromPin: false,
                      destPin: false,
                      pieces: false,
                      weight: false,
                      volumetric: false,
                      consignee: false,
                      product: false,
                      mode: false,
                      risk: false,
                    })
                  }
                >
                  <i className="bi bi-unlock me-1"></i> Unlock All
                </button>
              </div>
            </div>

            {/* Row 1: 1. Date & 2. Customer Code */}
            <div className="row g-2 mb-2">
              <div className="col-md-4">
                <div className="d-flex justify-content-between align-items-center mb-1">
                  <label className="form-label small fw-bold mb-0">1. Date *</label>
                  <label className="form-check-label small text-muted d-flex align-items-center gap-1" style={{ fontSize: '0.75rem' }}>
                    <input
                      type="checkbox"
                      className="form-check-input mt-0"
                      checked={locks.date}
                      onChange={(e) => setLocks({ ...locks, date: e.target.checked })}
                    />
                    Lock
                  </label>
                </div>
                <input
                  type="date"
                  className={`form-control form-control-sm ${locks.date ? 'border-primary bg-light' : ''}`}
                  value={formData.bookingDate}
                  onChange={(e) => setFormData({ ...formData, bookingDate: e.target.value })}
                  required
                />
              </div>

              <div className="col-md-4">
                <div className="d-flex justify-content-between align-items-center mb-1">
                  <label className="form-label small fw-bold mb-0">2. Customer Code *</label>
                  <label className="form-check-label small text-muted d-flex align-items-center gap-1" style={{ fontSize: '0.75rem' }}>
                    <input
                      type="checkbox"
                      className="form-check-input mt-0"
                      checked={locks.customer}
                      onChange={(e) => setLocks({ ...locks, customer: e.target.checked })}
                    />
                    Lock
                  </label>
                </div>
                <div className="input-group input-group-sm">
                  <input
                    type="text"
                    className={`form-control form-control-sm font-monospace fw-bold ${locks.customer ? 'border-primary bg-light' : ''}`}
                    placeholder="e.g. 1001 or name"
                    value={formData.customerCode}
                    onChange={(e) => handleCustomerCodeSearch(e.target.value)}
                    required
                  />
                  <button
                    className="btn btn-outline-secondary dropdown-toggle"
                    type="button"
                    data-bs-toggle="dropdown"
                  ></button>
                  <ul className="dropdown-menu dropdown-menu-end shadow-sm" style={{ maxHeight: '250px', overflowY: 'auto' }}>
                    {clients.map((c) => (
                      <li key={c.id}>
                        <button
                          type="button"
                          className="dropdown-item small"
                          onClick={() => {
                            setFormData((prev) => ({
                              ...prev,
                              clientId: c.id,
                              customerCode: c.client_code,
                              clientName: c.name,
                              senderPhone: c.phone || prev.senderPhone,
                            }));
                          }}
                        >
                          <span className="badge bg-secondary font-monospace me-2">{c.client_code}</span>
                          {c.name}
                        </button>
                      </li>
                    ))}
                  </ul>
                </div>
              </div>

              <div className="col-md-4">
                <label className="form-label small fw-semibold text-muted mb-1">Customer Name (Show)</label>
                <input
                  type="text"
                  className="form-control form-control-sm bg-light text-primary fw-bold"
                  value={formData.clientName || 'Select Customer'}
                  readOnly
                />
              </div>
            </div>

            {/* Row 2: 3. From PIN Code & 4. Destination PIN Code */}
            <div className="row g-2 mb-2">
              <div className="col-md-6">
                <div className="d-flex justify-content-between align-items-center mb-1">
                  <label className="form-label small fw-bold mb-0">3. From PIN Code *</label>
                  <label className="form-check-label small text-muted d-flex align-items-center gap-1" style={{ fontSize: '0.75rem' }}>
                    <input
                      type="checkbox"
                      className="form-check-input mt-0"
                      checked={locks.fromPin}
                      onChange={(e) => setLocks({ ...locks, fromPin: e.target.checked })}
                    />
                    Lock
                  </label>
                </div>
                <div className="input-group input-group-sm">
                  <input
                    type="text"
                    maxLength={6}
                    className={`form-control form-control-sm font-monospace ${locks.fromPin ? 'border-primary bg-light' : ''}`}
                    placeholder="Origin PIN (e.g. 492001)"
                    value={formData.fromPincode}
                    onChange={(e) => setFormData({ ...formData, fromPincode: e.target.value })}
                    required
                  />
                  <span className="input-group-text small bg-white text-muted">{formData.fromCityState}</span>
                </div>
              </div>

              <div className="col-md-6">
                <div className="d-flex justify-content-between align-items-center mb-1">
                  <label className="form-label small fw-bold mb-0">4. Destination PIN Code *</label>
                  <label className="form-check-label small text-muted d-flex align-items-center gap-1" style={{ fontSize: '0.75rem' }}>
                    <input
                      type="checkbox"
                      className="form-check-input mt-0"
                      checked={locks.destPin}
                      onChange={(e) => setLocks({ ...locks, destPin: e.target.checked })}
                    />
                    Lock
                  </label>
                </div>
                <div className="input-group input-group-sm">
                  <input
                    type="text"
                    maxLength={6}
                    className={`form-control form-control-sm font-monospace fw-bold text-success ${locks.destPin ? 'border-primary bg-light' : ''}`}
                    placeholder="Dest PIN (e.g. 400001)"
                    value={formData.destinationPincode}
                    onChange={(e) => handleDestPinChange(e.target.value)}
                    required
                  />
                  <span className="input-group-text small bg-white text-muted">
                    {formData.destinationCityState || 'Detecting...'}
                  </span>
                  <span className="input-group-text small bg-success-subtle text-success fw-bold">
                    {formData.destinationZone || 'Zone'}
                  </span>
                </div>
              </div>
            </div>

            {/* Row 3: 5. PCS, 6. Weight Actual, 7. Weight Volumetric, 8. Chargeable WT */}
            <div className="p-2 border rounded bg-light-subtle mb-3">
              <div className="row g-2 align-items-center">
                <div className="col-6 col-md-3">
                  <div className="d-flex justify-content-between align-items-center mb-1">
                    <label className="form-label small fw-bold mb-0">5. PCS *</label>
                    <label className="form-check-label small text-muted d-flex align-items-center gap-1" style={{ fontSize: '0.75rem' }}>
                      <input
                        type="checkbox"
                        className="form-check-input mt-0"
                        checked={locks.pieces}
                        onChange={(e) => setLocks({ ...locks, pieces: e.target.checked })}
                      />
                      Lock
                    </label>
                  </div>
                  <input
                    type="number"
                    min={1}
                    className="form-control form-control-sm fw-bold text-center"
                    value={formData.pieces}
                    onChange={(e) => setFormData({ ...formData, pieces: Number(e.target.value) })}
                    required
                  />
                </div>

                <div className="col-6 col-md-3">
                  <div className="d-flex justify-content-between align-items-center mb-1">
                    <label className="form-label small fw-bold mb-0">6. Actual Wt (KG) *</label>
                    <label className="form-check-label small text-muted d-flex align-items-center gap-1" style={{ fontSize: '0.75rem' }}>
                      <input
                        type="checkbox"
                        className="form-check-input mt-0"
                        checked={locks.weight}
                        onChange={(e) => setLocks({ ...locks, weight: e.target.checked })}
                      />
                      Lock
                    </label>
                  </div>
                  <input
                    type="number"
                    step="0.01"
                    className="form-control form-control-sm fw-bold text-center"
                    value={formData.deadWeight}
                    onChange={(e) => setFormData({ ...formData, deadWeight: Number(e.target.value) })}
                    required
                  />
                </div>

                <div className="col-6 col-md-3">
                  <div className="d-flex justify-content-between align-items-center mb-1">
                    <label className="form-label small fw-bold mb-0">7. Volumetric Wt</label>
                    <label className="form-check-label small text-muted d-flex align-items-center gap-1" style={{ fontSize: '0.75rem' }}>
                      <input
                        type="checkbox"
                        className="form-check-input mt-0"
                        checked={locks.volumetric}
                        onChange={(e) => setLocks({ ...locks, volumetric: e.target.checked })}
                      />
                      Lock
                    </label>
                  </div>
                  <input
                    type="number"
                    step="0.01"
                    className="form-control form-control-sm bg-light text-center"
                    value={formData.volumetricWeight}
                    readOnly
                  />
                </div>

                <div className="col-6 col-md-3">
                  <label className="form-label small fw-bold text-danger mb-1">8. Chargeable WT (KG)</label>
                  <input
                    type="number"
                    step="0.01"
                    className="form-control form-control-sm bg-danger-subtle text-danger fw-bolder fs-6 text-center"
                    value={formData.chargeableWeight}
                    readOnly
                  />
                </div>
              </div>

              {/* 7. Volumetric Box Dimensions Repeater (Handwritten Box 1, Box 2...) */}
              <div className="mt-2 pt-2 border-top">
                <div className="d-flex justify-content-between align-items-center mb-1">
                  <span className="small text-muted fw-semibold">
                    <i className="bi bi-box me-1"></i> Multi-Box Dimensions (L × W × H in cm)
                  </span>
                  <button
                    type="button"
                    className="btn btn-outline-primary btn-xs py-0 px-2"
                    style={{ fontSize: '0.72rem' }}
                    onClick={addBoxRow}
                  >
                    + Add Box Size
                  </button>
                </div>
                {boxes.map((box, idx) => (
                  <div key={box.id} className="p-2 mb-2 border rounded bg-white">
                    <div className="row g-2 align-items-center">
                      <div className="col-12 col-sm-auto d-flex justify-content-between align-items-center">
                        <span className="badge bg-secondary" style={{ fontSize: '0.75rem' }}>
                          Box {idx + 1}
                        </span>
                        <div className="d-flex align-items-center gap-1 d-sm-none">
                          <span className="small text-muted">Qty:</span>
                          <input
                            type="number"
                            min={1}
                            className="form-control form-control-sm text-center py-0"
                            style={{ width: '60px' }}
                            value={box.count || 1}
                            onChange={(e) => updateBox(box.id, 'count', Number(e.target.value))}
                          />
                          {boxes.length > 1 && (
                            <button
                              type="button"
                              className="btn btn-outline-danger btn-sm py-0 px-2 ms-1"
                              onClick={() => removeBoxRow(box.id)}
                            >
                              <i className="bi bi-trash"></i>
                            </button>
                          )}
                        </div>
                      </div>
                      <div className="col-4 col-sm">
                        <div className="input-group input-group-sm">
                          <span className="input-group-text px-1 px-sm-2 text-muted">L</span>
                          <input
                            type="number"
                            placeholder="cm"
                            className="form-control form-control-sm text-center px-1"
                            value={box.length || ''}
                            onChange={(e) => updateBox(box.id, 'length', Number(e.target.value))}
                          />
                        </div>
                      </div>
                      <div className="col-4 col-sm">
                        <div className="input-group input-group-sm">
                          <span className="input-group-text px-1 px-sm-2 text-muted">W</span>
                          <input
                            type="number"
                            placeholder="cm"
                            className="form-control form-control-sm text-center px-1"
                            value={box.width || ''}
                            onChange={(e) => updateBox(box.id, 'width', Number(e.target.value))}
                          />
                        </div>
                      </div>
                      <div className="col-4 col-sm">
                        <div className="input-group input-group-sm">
                          <span className="input-group-text px-1 px-sm-2 text-muted">H</span>
                          <input
                            type="number"
                            placeholder="cm"
                            className="form-control form-control-sm text-center px-1"
                            value={box.height || ''}
                            onChange={(e) => updateBox(box.id, 'height', Number(e.target.value))}
                          />
                        </div>
                      </div>
                      <div className="col-sm-2 d-none d-sm-block">
                        <div className="input-group input-group-sm">
                          <span className="input-group-text px-1 text-muted">Qty</span>
                          <input
                            type="number"
                            min={1}
                            className="form-control form-control-sm text-center px-1"
                            value={box.count || 1}
                            onChange={(e) => updateBox(box.id, 'count', Number(e.target.value))}
                          />
                        </div>
                      </div>
                      {boxes.length > 1 && (
                        <div className="col-auto d-none d-sm-block">
                          <button
                            type="button"
                            className="btn btn-outline-danger btn-sm p-0 px-2"
                            onClick={() => removeBoxRow(box.id)}
                            title="Remove Box"
                          >
                            <i className="bi bi-x"></i>
                          </button>
                        </div>
                      )}
                    </div>
                  </div>
                ))}
              </div>
            </div>

            {/* Row 4: 9. Consignee Details */}
            <div className="mb-2 pb-2 border-bottom">
              <div className="d-flex justify-content-between align-items-center mb-1">
                <label className="form-label small fw-bold mb-0">9. Consignee (Receiver Details) *</label>
                <label className="form-check-label small text-muted d-flex align-items-center gap-1" style={{ fontSize: '0.75rem' }}>
                  <input
                    type="checkbox"
                    className="form-check-input mt-0"
                    checked={locks.consignee}
                    onChange={(e) => setLocks({ ...locks, consignee: e.target.checked })}
                  />
                  Lock
                </label>
              </div>
              <div className="row g-2">
                <div className="col-md-5">
                  <input
                    type="text"
                    className="form-control form-control-sm"
                    placeholder="Recipient Full Name / Company"
                    value={formData.recipientName}
                    onChange={(e) => setFormData({ ...formData, recipientName: e.target.value })}
                    required
                  />
                </div>
                <div className="col-md-3">
                  <input
                    type="tel"
                    className="form-control form-control-sm"
                    placeholder="10-digit Phone"
                    value={formData.recipientPhone}
                    onChange={(e) => setFormData({ ...formData, recipientPhone: e.target.value })}
                    required
                  />
                </div>
                <div className="col-md-4">
                  <input
                    type="text"
                    className="form-control form-control-sm"
                    placeholder="Delivery Street Address"
                    value={formData.recipientAddress}
                    onChange={(e) => setFormData({ ...formData, recipientAddress: e.target.value })}
                    required
                  />
                </div>
              </div>
            </div>

            {/* Row 5: 10. Products, 11. Dox/Non Dox, 12. Mode, 13. Value, 14. Owner Risk */}
            <div className="row g-2 mb-3">
              <div className="col-md-4">
                <div className="d-flex justify-content-between align-items-center mb-1">
                  <label className="form-label small fw-bold mb-0">10. Products / Description</label>
                  <label className="form-check-label small text-muted d-flex align-items-center gap-1" style={{ fontSize: '0.75rem' }}>
                    <input
                      type="checkbox"
                      className="form-check-input mt-0"
                      checked={locks.product}
                      onChange={(e) => setLocks({ ...locks, product: e.target.checked })}
                    />
                    Lock
                  </label>
                </div>
                <input
                  type="text"
                  className="form-control form-control-sm"
                  placeholder="e.g. Legal papers, spare parts"
                  value={formData.productDescription}
                  onChange={(e) => setFormData({ ...formData, productDescription: e.target.value })}
                />
              </div>

              <div className="col-md-2">
                <label className="form-label small fw-bold mb-1">11. Type</label>
                <select
                  className="form-select form-select-sm fw-semibold"
                  value={formData.mode}
                  onChange={(e) => setFormData({ ...formData, mode: e.target.value as any })}
                >
                  <option value="Dox">Dox</option>
                  <option value="Non-Dox">Non Dox</option>
                </select>
              </div>

              <div className="col-md-3">
                <div className="d-flex justify-content-between align-items-center mb-1">
                  <label className="form-label small fw-bold mb-0">12. Mode *</label>
                  <label className="form-check-label small text-muted d-flex align-items-center gap-1" style={{ fontSize: '0.75rem' }}>
                    <input
                      type="checkbox"
                      className="form-check-input mt-0"
                      checked={locks.mode}
                      onChange={(e) => setLocks({ ...locks, mode: e.target.checked })}
                    />
                    Lock
                  </label>
                </div>
                <select
                  className="form-select form-select-sm fw-bold text-primary"
                  value={formData.sendBy}
                  onChange={(e) => setFormData({ ...formData, sendBy: e.target.value as any })}
                >
                  <option value="Surface">Surface Cargo</option>
                  <option value="Air">Air Express</option>
                  <option value="Fast Surface">Fast Surface (Fast Track)</option>
                  <option value="By Hand">By Hand Delivery</option>
                  <option value="Self Vehicle">By Self Vehicle</option>
                </select>
              </div>

              <div className="col-md-3">
                <label className="form-label small fw-bold mb-1">13. Value (INR)</label>
                <input
                  type="number"
                  placeholder="Declared Value"
                  className="form-control form-control-sm text-end"
                  value={formData.insuredValue || ''}
                  onChange={(e) => setFormData({ ...formData, insuredValue: Number(e.target.value) })}
                />
              </div>
            </div>

            {/* Row 6: 14. Risk Type & Save Buttons */}
            <div className="d-flex flex-column flex-sm-row justify-content-between align-items-stretch align-items-sm-center pt-2 border-top gap-2">
              <div className="d-flex align-items-center gap-2">
                <label className="small fw-bold mb-0">14. Risk Policy:</label>
                <select
                  className="form-select form-select-sm d-inline-block w-auto"
                  value={formData.riskType}
                  onChange={(e) => setFormData({ ...formData, riskType: e.target.value as any })}
                >
                  <option value="Owner Risk">Owner's Own Risk</option>
                  <option value="Carrier Risk">Carrier Risk (Insured)</option>
                </select>
                <label className="form-check-label small text-muted d-flex align-items-center gap-1 ms-1" style={{ fontSize: '0.75rem' }}>
                  <input
                    type="checkbox"
                    className="form-check-input mt-0"
                    checked={locks.risk}
                    onChange={(e) => setLocks({ ...locks, risk: e.target.checked })}
                  />
                  Lock
                </label>
              </div>

              <div className="d-flex gap-2">
                <button
                  type="submit"
                  className="btn btn-primary px-4 py-2 fw-bold shadow-sm w-100 w-sm-auto"
                  disabled={submitting}
                >
                  {submitting ? (
                    <>
                      <span className="spinner-border spinner-border-sm me-2"></span>
                      Saving...
                    </>
                  ) : (
                    <>
                      <i className="bi bi-save me-1"></i> [Save] & Approve Rate
                    </>
                  )}
                </button>
              </div>
            </div>
          </form>
        </div>

        {/* Live Billable Summary Card (Right Column) */}
        <div className="col-12 col-xl-4">
          <div className="rate-preview-card shadow-sm sticky-top" style={{ top: '80px' }}>
            <div className="d-flex justify-content-between align-items-center mb-3 border-bottom border-light pb-2">
              <h5 className="mb-0 fw-bold">Billing Calculation</h5>
              <span className="badge bg-light text-primary fw-bold">
                {formData.sendBy} • {formData.mode}
              </span>
            </div>

            <div className="mb-3">
              <div className="small opacity-75">Applicable Destination Zone:</div>
              <div className="fs-5 fw-bold text-warning">{formData.destinationZone || 'Detecting Zone'}</div>
              <div className="small opacity-75">
                PIN: {formData.destinationPincode || 'None'} ({formData.destinationCityState || 'India'})
              </div>
            </div>

            <div className="mb-3">
              <div className="small opacity-75">Chargeable Weight:</div>
              <div className="fs-2 fw-bolder">{formData.chargeableWeight} kg</div>
              <div className="small opacity-75">
                (Actual: {formData.deadWeight} kg | Vol: {formData.volumetricWeight} kg across {boxes.length} box)
              </div>
            </div>

            <hr className="border-light opacity-50" />

            <div className="d-flex justify-content-between small mb-1">
              <span>Freight Rate ({formData.destinationZone}):</span>
              <span className="fw-bold">₹{formData.subtotal.toFixed(2)}</span>
            </div>

            <div className="d-flex justify-content-between small mb-1">
              <span>Fuel Surcharge:</span>
              <span className="fw-bold">₹{formData.fuelSurchargeAmount.toFixed(2)}</span>
            </div>

            <div className="d-flex justify-content-between small mb-1">
              <span>Docket Charges:</span>
              <span className="fw-bold">₹{formData.docketCharge.toFixed(2)}</span>
            </div>

            <div className="d-flex justify-content-between border-top border-light pt-2 mt-3 fs-3 fw-bolder">
              <span>Total Bill:</span>
              <span>₹{formData.totalAmount.toFixed(2)}</span>
            </div>

            <div className="mt-3 p-2 bg-white bg-opacity-10 rounded small">
              <i className="bi bi-lightning-charge me-1"></i>
              <strong>One-Click Workflow:</strong> Rate auto-calculated based on client tariff. Locked fields remain intact for seamless bulk docket generation!
            </div>
          </div>
        </div>
      </div>

      {/* Booking Success & Thermal Print Modal */}
      {showPrintModal && bookedConsignment && (
        <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
          <div className="modal-dialog modal-dialog-centered">
            <div className="modal-content">
              <div className="modal-header">
                <h5 className="modal-title">Booking Saved: {bookedConsignment.consignment_no}</h5>
                <button
                  type="button"
                  className="btn-close"
                  onClick={() => setShowPrintModal(false)}
                ></button>
              </div>
              <div className="modal-body text-center">
                <div className="print-area">
                  <ThermalLabel consignment={bookedConsignment} />
                </div>
              </div>
              <div className="modal-footer no-print">
                <button
                  type="button"
                  className="btn btn-secondary"
                  onClick={() => setShowPrintModal(false)}
                >
                  Close & Continue Next
                </button>
                <button
                  type="button"
                  className="btn btn-primary"
                  onClick={() => window.print()}
                >
                  <i className="bi bi-printer me-1"></i> Print 4x6" Thermal Label
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

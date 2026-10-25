// frontend/src/pages/billing/InvoicesPage.tsx
import React, { useState, useEffect } from 'react';
import api from '../../services/api';
import { Invoice, Client, Consignment } from '../../types';

export const InvoicesPage: React.FC = () => {
  const [invoices, setInvoices] = useState<Invoice[]>([]);
  const [clients, setClients] = useState<Client[]>([]);
  const [loading, setLoading] = useState(true);

  // Generate Invoice Modal
  const [showGenerateModal, setShowGenerateModal] = useState(false);
  const [selectedClientId, setSelectedClientId] = useState<number>(0);
  const [unbilledList, setUnbilledList] = useState<Consignment[]>([]);
  const [selectedConsignments, setSelectedConsignments] = useState<number[]>([]);
  const [docketCharges, setDocketCharges] = useState(0);
  const [generating, setGenerating] = useState(false);

  // Record Payment Modal
  const [showPaymentModal, setShowPaymentModal] = useState(false);
  const [selectedInvoice, setSelectedInvoice] = useState<Invoice | null>(null);
  const [paymentAmount, setPaymentAmount] = useState<number>(0);
  const [paymentMode, setPaymentMode] = useState<string>('NEFT/RTGS');
  const [refNo, setRefNo] = useState('');

  // View Invoice Detail Modal
  const [viewInvoice, setViewInvoice] = useState<any | null>(null);

  // Configurable Invoice Columns (Handwritten Image 4.45.56 PM)
  const [showColumnsModal, setShowColumnsModal] = useState(false);
  const [invoiceColumns, setInvoiceColumns] = useState({
    date: true,
    awb: true,
    destination: true,
    mode: true,
    pieces: true,
    weight: true,
    actualWeight: false,
    volumetricWeight: false,
    courierName: false,
    consigneeName: true,
    pincode: true,
    docketCharges: true,
    fuelCharges: true,
    odaCharges: false,
    insuranceCharges: false,
    doorPickupCharges: false,
    cgst: true,
    sgst: true,
    igst: true,
  });

  // Post-Billing Line-Item Edit State (Handwritten Image 4.45.54(1) PM)
  const [editingItem, setEditingItem] = useState<any | null>(null);

  // Date Range for Bulk Invoice Generation (Image 4.45.55 PM)
  const [billFromDate, setBillFromDate] = useState('2026-04-01');
  const [billToDate, setBillToDate] = useState('2026-04-30');
  const [billDate, setBillDate] = useState(new Date().toISOString().split('T')[0]);

  const fetchData = async () => {
    try {
      setLoading(true);
      const [invRes, cliRes] = await Promise.all([
        api.get('/invoices'),
        api.get('/clients'),
      ]);
      if (invRes.data.success) setInvoices(invRes.data.data);
      if (cliRes.data.success) {
        setClients(cliRes.data.data);
        if (cliRes.data.data[0]) {
          setSelectedClientId(cliRes.data.data[0].id);
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

  const fetchUnbilled = async (cId: number) => {
    try {
      const res = await api.get('/invoices/unbilled-consignments', {
        params: { clientId: cId },
      });
      if (res.data.success) {
        setUnbilledList(res.data.data);
        setSelectedConsignments(res.data.data.map((c: any) => c.id));
      }
    } catch (e) {
      setUnbilledList([]);
      setSelectedConsignments([]);
    }
  };

  useEffect(() => {
    if (selectedClientId && showGenerateModal) {
      fetchUnbilled(selectedClientId);
    }
  }, [selectedClientId, showGenerateModal]);

  const handleGenerate = async (e: React.FormEvent) => {
    e.preventDefault();
    if (selectedConsignments.length === 0) {
      alert('Please select at least one unbilled consignment.');
      return;
    }

    try {
      setGenerating(true);
      const res = await api.post('/invoices/generate', {
        clientId: selectedClientId,
        consignmentIds: selectedConsignments,
        docketCharges,
      });

      if (res.data.success) {
        setShowGenerateModal(false);
        fetchData();
        setViewInvoice(res.data.data);
      }
    } catch (err: any) {
      alert(err.response?.data?.error?.message || 'Error generating invoice');
    } finally {
      setGenerating(false);
    }
  };

  const handlePayment = async (e: React.FormEvent) => {
    e.preventDefault();
    if (!selectedInvoice) return;
    try {
      await api.post('/invoices/payments', {
        invoiceId: selectedInvoice.id,
        amount: paymentAmount,
        paymentMode,
        referenceNo: refNo,
      });
      setShowPaymentModal(false);
      setSelectedInvoice(null);
      fetchData();
    } catch (err: any) {
      alert(err.response?.data?.error?.message || 'Error recording payment');
    }
  };

  const handleViewDetail = async (id: number) => {
    try {
      const res = await api.get(`/invoices/${id}`);
      if (res.data.success) {
        setViewInvoice(res.data.data);
      }
    } catch (e) {
      alert('Error fetching invoice details');
    }
  };

  const toggleSelect = (id: number) => {
    setSelectedConsignments((prev) =>
      prev.includes(id) ? prev.filter((cid) => cid !== id) : [...prev, id]
    );
  };

  return (
    <div className="animate-enter">
      <div className="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3 mb-4 no-print">
        <div>
          <h2 className="brand-title mb-1">B2B GST Invoicing & Accounting</h2>
          <p className="text-muted small mb-0">
            GST SAC Code 996812 compliant invoicing, CGST/SGST vs IGST calculation, payment reconciliation, and GSTR-1 export
          </p>
        </div>
        <div className="d-flex flex-wrap gap-2 w-100 w-lg-auto">
          <a
            href="/api/v1/invoices/gstr1-export"
            download
            className="btn btn-outline-success btn-sm flex-grow-1 flex-sm-grow-0"
          >
            <i className="bi bi-file-earmark-spreadsheet me-1"></i> GSTR-1 CSV
          </a>
          <button
            type="button"
            className="btn btn-outline-secondary btn-sm flex-grow-1 flex-sm-grow-0"
            onClick={() => setShowColumnsModal(true)}
          >
            <i className="bi bi-layout-three-columns me-1"></i> Columns
          </button>
          <button
            className="btn btn-primary btn-sm flex-grow-1 flex-sm-grow-0 fw-semibold"
            onClick={() => {
              setShowGenerateModal(true);
              if (selectedClientId) fetchUnbilled(selectedClientId);
            }}
          >
            <i className="bi bi-plus-circle me-1"></i> Generate Invoice
          </button>
        </div>
      </div>

      <div className="app-card p-3 no-print">
        {loading ? (
          <div className="text-center py-5">
            <div className="spinner-border text-primary spinner-border-sm me-2"></div>
            Loading billing records...
          </div>
        ) : invoices.length === 0 ? (
          <div className="text-center py-4 text-muted">No invoices issued yet.</div>
        ) : (
          <>
            {/* Desktop Table View */}
            <div className="table-responsive d-none d-md-block">
              <table className="table table-hover align-middle mb-0">
                <thead className="table-light">
                  <tr className="small text-muted text-uppercase">
                    <th>Invoice No.</th>
                    <th>Date</th>
                    <th>Client Name</th>
                    <th>Consignments</th>
                    <th>Taxable Amt</th>
                    <th>Total GST (18%)</th>
                    <th>Grand Total</th>
                    <th>Paid</th>
                    <th>Status</th>
                    <th className="text-end">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  {invoices.map((inv) => (
                    <tr key={inv.id}>
                      <td>
                        <span className="fw-bold font-monospace text-primary">{inv.invoice_no}</span>
                      </td>
                      <td className="small text-muted">
                        {new Date(inv.invoice_date).toLocaleDateString()}
                      </td>
                      <td>
                        <div className="fw-semibold">{inv.client?.name}</div>
                        <div className="small text-muted font-monospace">{inv.client?.gstin || 'Unregistered'}</div>
                      </td>
                      <td>
                        <span className="badge bg-light text-dark border">
                          {inv.consignments_count} AWBs
                        </span>
                      </td>
                      <td>₹{Number(inv.taxable_amount).toFixed(2)}</td>
                      <td>₹{Number(inv.total_gst_amount).toFixed(2)}</td>
                      <td className="fw-bold">₹{Number(inv.grand_total).toFixed(2)}</td>
                      <td>₹{Number(inv.paid_amount).toFixed(2)}</td>
                      <td>
                        <span className={`badge-status ${inv.payment_status}`}>
                          {inv.payment_status}
                        </span>
                      </td>
                      <td className="text-end">
                        <div className="btn-group btn-group-sm">
                          <button
                            className="btn btn-outline-secondary"
                            onClick={() => handleViewDetail(inv.id)}
                            title="View / Print Tax Invoice"
                          >
                            <i className="bi bi-printer"></i>
                          </button>
                          {inv.payment_status !== 'paid' && (
                            <button
                              className="btn btn-outline-success"
                              onClick={() => {
                                setSelectedInvoice(inv);
                                setPaymentAmount(Number(inv.grand_total) - Number(inv.paid_amount));
                                setShowPaymentModal(true);
                              }}
                              title="Record Receipt"
                            >
                              <i className="bi bi-cash"></i>
                            </button>
                          )}
                        </div>
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>

            {/* Handheld Mobile Card View (< 768px) */}
            <div className="d-block d-md-none">
              {invoices.map((inv) => (
                <div key={inv.id} className="shipment-mobile-card">
                  <div className="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                    <span className="fw-bold font-monospace text-primary fs-6">
                      {inv.invoice_no}
                    </span>
                    <span className={`badge-status ${inv.payment_status}`}>
                      {inv.payment_status}
                    </span>
                  </div>
                  <div className="fw-bold text-dark">{inv.client?.name}</div>
                  <div className="small text-muted font-monospace mb-2">
                    GSTIN: {inv.client?.gstin || 'Unregistered'} • {new Date(inv.invoice_date).toLocaleDateString()}
                  </div>
                  <div className="row g-2 small p-2 bg-light rounded mb-2">
                    <div className="col-6">
                      <span className="text-muted d-block">Grand Total:</span>
                      <strong className="fs-6 text-dark">₹{Number(inv.grand_total).toFixed(2)}</strong>
                    </div>
                    <div className="col-6 text-end">
                      <span className="text-muted d-block">Paid Amount:</span>
                      <strong className="text-success">₹{Number(inv.paid_amount).toFixed(2)}</strong>
                    </div>
                    <div className="col-6">
                      <span className="text-muted">Consignments: </span>
                      <span className="badge bg-white text-dark border">{inv.consignments_count} AWBs</span>
                    </div>
                    <div className="col-6 text-end">
                      <span className="text-muted">GST 18%: </span>
                      <span>₹{Number(inv.total_gst_amount).toFixed(2)}</span>
                    </div>
                  </div>
                  <div className="d-flex gap-2 justify-content-end pt-1">
                    <button
                      className="btn btn-outline-secondary btn-sm flex-grow-1"
                      onClick={() => handleViewDetail(inv.id)}
                    >
                      <i className="bi bi-printer me-1"></i> Print / View
                    </button>
                    {inv.payment_status !== 'paid' && (
                      <button
                        className="btn btn-success btn-sm flex-grow-1"
                        onClick={() => {
                          setSelectedInvoice(inv);
                          setPaymentAmount(Number(inv.grand_total) - Number(inv.paid_amount));
                          setShowPaymentModal(true);
                        }}
                      >
                        <i className="bi bi-cash me-1"></i> Record Receipt
                      </button>
                    )}
                  </div>
                </div>
              ))}
            </div>
          </>
        )}
      </div>

      {/* Generate Invoice Modal */}
      {showGenerateModal && (
        <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
          <div className="modal-dialog modal-lg">
            <div className="modal-content">
              <div className="modal-header">
                <h5 className="modal-title">Generate GST SAC 996812 Tax Invoice</h5>
                <button type="button" className="btn-close" onClick={() => setShowGenerateModal(false)}></button>
              </div>
              <form onSubmit={handleGenerate}>
                <div className="modal-body">
                  {/* Handwritten Image 4.45.55 PM: Bulk Invoice Date Range */}
                  <div className="row g-2 mb-3 p-2 bg-light rounded border">
                    <div className="col-md-4">
                      <label className="form-label small fw-semibold">Bill From Date</label>
                      <input
                        type="date"
                        className="form-control form-control-sm"
                        value={billFromDate}
                        onChange={(e) => setBillFromDate(e.target.value)}
                      />
                    </div>
                    <div className="col-md-4">
                      <label className="form-label small fw-semibold">Bill To Date</label>
                      <input
                        type="date"
                        className="form-control form-control-sm"
                        value={billToDate}
                        onChange={(e) => setBillToDate(e.target.value)}
                      />
                    </div>
                    <div className="col-md-4">
                      <label className="form-label small fw-semibold">Bill Issue Date</label>
                      <input
                        type="date"
                        className="form-control form-control-sm"
                        value={billDate}
                        onChange={(e) => setBillDate(e.target.value)}
                      />
                    </div>
                  </div>

                  <div className="row g-2 mb-3">
                    <div className="col-md-6">
                      <label className="form-label small fw-semibold">Corporate B2B Client *</label>
                      <select
                        className="form-select form-select-sm"
                        value={selectedClientId}
                        onChange={(e) => setSelectedClientId(Number(e.target.value))}
                        required
                      >
                        {clients.map((c) => (
                          <option key={c.id} value={c.id}>
                            {c.client_code} - {c.name} ({c.gstin || 'No GSTIN'})
                          </option>
                        ))}
                      </select>
                    </div>

                    <div className="col-md-6">
                      <label className="form-label small fw-semibold">Docket Stationary Charges (₹)</label>
                      <input
                        type="number"
                        className="form-control form-control-sm"
                        value={docketCharges}
                        onChange={(e) => setDocketCharges(Number(e.target.value))}
                      />
                    </div>
                  </div>

                  <h6 className="fw-bold small text-muted text-uppercase mb-2">
                    Select Unbilled Consignments to Invoice ({selectedConsignments.length} Selected)
                  </h6>

                  <div className="border rounded p-2" style={{ maxHeight: '250px', overflowY: 'auto' }}>
                    {unbilledList.length === 0 ? (
                      <div className="text-center py-4 text-muted small">
                        No unbilled consignments found for this corporate client.
                      </div>
                    ) : (
                      unbilledList.map((b) => (
                        <div
                          key={b.id}
                          className={`d-flex justify-content-between align-items-center p-2 mb-1 border-bottom ${
                            selectedConsignments.includes(b.id) ? 'bg-primary-subtle' : ''
                          }`}
                          style={{ cursor: 'pointer' }}
                          onClick={() => toggleSelect(b.id)}
                        >
                          <div className="d-flex align-items-center gap-2">
                            <input
                              type="checkbox"
                              className="form-check-input"
                              checked={selectedConsignments.includes(b.id)}
                              onChange={() => {}}
                            />
                            <div>
                              <span className="font-monospace fw-bold">{b.consignment_no}</span>
                              <div className="small text-muted">
                                {new Date(b.booking_date).toLocaleDateString()} • Dest: {b.destination?.name} ({b.destination?.code})
                              </div>
                            </div>
                          </div>
                          <div className="text-end small">
                            <strong>₹{Number(b.subtotal).toFixed(2)}</strong> ({b.billable_weight} kg)
                          </div>
                        </div>
                      ))
                    )}
                  </div>
                </div>
                <div className="modal-footer">
                  <button type="button" className="btn btn-light" onClick={() => setShowGenerateModal(false)}>
                    Cancel
                  </button>
                  <button
                    type="submit"
                    className="btn btn-primary"
                    disabled={generating || selectedConsignments.length === 0}
                  >
                    {generating ? 'Calculating GST & Generating...' : 'Generate Tax Invoice'}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      )}

      {/* Record Payment Modal */}
      {showPaymentModal && selectedInvoice && (
        <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
          <div className="modal-dialog">
            <div className="modal-content">
              <div className="modal-header">
                <h5 className="modal-title">Record Payment Receipt</h5>
                <button type="button" className="btn-close" onClick={() => setShowPaymentModal(false)}></button>
              </div>
              <form onSubmit={handlePayment}>
                <div className="modal-body">
                  <div className="alert alert-light border small py-2 mb-3">
                    <strong>Invoice:</strong> {selectedInvoice.invoice_no}
                    <br />
                    <strong>Client:</strong> {selectedInvoice.client?.name}
                    <br />
                    <strong>Grand Total:</strong> ₹{Number(selectedInvoice.grand_total).toFixed(2)} (Already Paid: ₹{Number(selectedInvoice.paid_amount).toFixed(2)})
                  </div>

                  <div className="mb-3">
                    <label className="form-label small fw-semibold">Receipt Amount (₹) *</label>
                    <input
                      type="number"
                      step="0.01"
                      className="form-control"
                      value={paymentAmount}
                      onChange={(e) => setPaymentAmount(Number(e.target.value))}
                      required
                    />
                  </div>

                  <div className="mb-3">
                    <label className="form-label small fw-semibold">Payment Mode *</label>
                    <select
                      className="form-select"
                      value={paymentMode}
                      onChange={(e) => setPaymentMode(e.target.value)}
                    >
                      <option value="NEFT/RTGS">NEFT / RTGS Bank Transfer</option>
                      <option value="UPI">UPI Digital Payment</option>
                      <option value="Cheque">Bank Cheque</option>
                      <option value="Cash">Cash Receipt</option>
                    </select>
                  </div>

                  <div className="mb-3">
                    <label className="form-label small fw-semibold">Transaction Reference / UTR No.</label>
                    <input
                      type="text"
                      className="form-control font-monospace"
                      placeholder="e.g. UTR12345678"
                      value={refNo}
                      onChange={(e) => setRefNo(e.target.value)}
                    />
                  </div>
                </div>
                <div className="modal-footer">
                  <button type="button" className="btn btn-light" onClick={() => setShowPaymentModal(false)}>
                    Cancel
                  </button>
                  <button type="submit" className="btn btn-success">
                    Save Receipt
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      )}

      {/* Invoice Print & View Dossier Modal */}
      {viewInvoice && (
        <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
          <div className="modal-dialog modal-xl modal-dialog-scrollable">
            <div className="modal-content">
              <div className="modal-header no-print">
                <h5 className="modal-title">Tax Invoice {viewInvoice.invoice_no}</h5>
                <button type="button" className="btn-close" onClick={() => setViewInvoice(null)}></button>
              </div>
              <div className="modal-body p-4 bg-white print-area">
                {/* Printable Invoice Body */}
                <div className="border p-4 bg-white">
                  <div className="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
                    <div>
                      <h3 className="fw-bold text-primary mb-1">
                        {viewInvoice.tenant?.name || 'KESHRI EXPRESS COURIER SERVICES'}
                      </h3>
                      <div className="text-muted small">
                        {viewInvoice.tenant?.address}, {viewInvoice.tenant?.city}, {viewInvoice.tenant?.state} - {viewInvoice.tenant?.pincode}
                      </div>
                      <div className="small mt-1">
                        <strong>GSTIN:</strong> {viewInvoice.tenant?.gstin || '22AAACK1234F1Z5'} | <strong>PAN:</strong> {viewInvoice.tenant?.pan_no || 'AAACK1234F'}
                      </div>
                    </div>
                    <div className="text-end">
                      <div className="border border-2 border-dark px-3 py-1 fw-bold fs-5 mb-1 bg-light">
                        TAX INVOICE
                      </div>
                      <div className="small"><strong>Invoice No:</strong> {viewInvoice.invoice_no}</div>
                      <div className="small"><strong>Date:</strong> {new Date(viewInvoice.invoice_date).toLocaleDateString()}</div>
                      <div className="small"><strong>SAC Code:</strong> 996812 (Courier Services)</div>
                    </div>
                  </div>

                  {/* Bill To */}
                  <div className="row g-3 mb-4">
                    <div className="col-6">
                      <div className="border p-3 rounded h-100">
                        <div className="fw-bold text-uppercase small text-muted mb-1">Billed To (Client):</div>
                        <h6 className="fw-bold mb-1">{viewInvoice.client?.name}</h6>
                        <div className="small text-muted">{viewInvoice.client?.address || 'Registered Address'}</div>
                        <div className="small mt-1"><strong>GSTIN:</strong> {viewInvoice.client?.gstin || 'Unregistered'}</div>
                        <div className="small"><strong>State:</strong> {viewInvoice.client?.state || 'Chhattisgarh'}</div>
                      </div>
                    </div>
                    <div className="col-6">
                      <div className="border p-3 rounded h-100">
                        <div className="fw-bold text-uppercase small text-muted mb-1">Settlement Bank Account:</div>
                        <div className="small"><strong>Bank:</strong> {viewInvoice.bank_account?.bank_name || 'HDFC Bank Ltd'}</div>
                        <div className="small"><strong>Account No:</strong> {viewInvoice.bank_account?.account_no || '50200012345678'}</div>
                        <div className="small"><strong>IFSC Code:</strong> {viewInvoice.bank_account?.ifsc_code || 'HDFC0001234'}</div>
                        <div className="small"><strong>Branch:</strong> {viewInvoice.bank_account?.branch_name || 'Pandri Branch, Raipur'}</div>
                      </div>
                    </div>
                  </div>

                  {/* Consignments Line Items */}
                  <div className="table-responsive mb-3">
                    <table className="table table-bordered table-sm text-center align-middle mb-0">
                      <thead className="table-light small">
                        <tr>
                          <th>#</th>
                          {invoiceColumns.date && <th>Booking Date</th>}
                          {invoiceColumns.awb && <th>AWB Number</th>}
                          {invoiceColumns.destination && <th>Destination</th>}
                          {invoiceColumns.mode && <th>Mode</th>}
                          {invoiceColumns.pieces && <th>Pieces</th>}
                          {invoiceColumns.weight && <th>Billable Wt (kg)</th>}
                          {invoiceColumns.actualWeight && <th>Actual Wt (kg)</th>}
                          <th className="text-end">Amount (₹)</th>
                          <th className="no-print text-center" style={{ width: '60px' }}>Edit</th>
                        </tr>
                      </thead>
                      <tbody className="small">
                        {viewInvoice.items?.map((item: any, idx: number) => (
                          <tr key={item.id}>
                            <td>{idx + 1}</td>
                            {invoiceColumns.date && <td>{new Date(item.booking_date).toLocaleDateString()}</td>}
                            {invoiceColumns.awb && <td className="font-monospace fw-bold">{item.consignment_no}</td>}
                            {invoiceColumns.destination && <td>{item.destination_name} ({item.dest_code})</td>}
                            {invoiceColumns.mode && <td>{item.mode}</td>}
                            {invoiceColumns.pieces && <td>{item.pieces}</td>}
                            {invoiceColumns.weight && <td>{item.billable_weight}</td>}
                            {invoiceColumns.actualWeight && <td>{item.billable_weight}</td>}
                            <td className="text-end fw-semibold">{Number(item.amount).toFixed(2)}</td>
                            <td className="no-print text-center">
                              <button
                                type="button"
                                className="btn btn-outline-primary btn-xs py-0 px-2"
                                title="Post-Billing Edit Line Item (Image 4.45.54)"
                                onClick={() => setEditingItem({ ...item })}
                              >
                                <i className="bi bi-pencil"></i>
                              </button>
                            </td>
                          </tr>
                        ))}
                      </tbody>
                    </table>
                  </div>

                  {/* Calculation Breakdown */}
                  <div className="row justify-content-end mb-3">
                    <div className="col-6">
                      <table className="table table-sm table-borderless small mb-0">
                        <tbody>
                          <tr>
                            <td>Subtotal Freight:</td>
                            <td className="text-end fw-semibold">₹{Number(viewInvoice.subtotal_amount).toFixed(2)}</td>
                          </tr>
                          <tr>
                            <td>Fuel Surcharge ({viewInvoice.fuel_surcharge_percent}%):</td>
                            <td className="text-end">₹{Number(viewInvoice.fuel_surcharge_amount).toFixed(2)}</td>
                          </tr>
                          <tr>
                            <td>Docket Stationary Charges:</td>
                            <td className="text-end">₹{Number(viewInvoice.docket_charge_amount).toFixed(2)}</td>
                          </tr>
                          <tr className="border-top">
                            <td><strong>Taxable Value:</strong></td>
                            <td className="text-end fw-bold">₹{Number(viewInvoice.taxable_amount).toFixed(2)}</td>
                          </tr>
                          {Number(viewInvoice.cgst_amount) > 0 && (
                            <>
                              <tr>
                                <td>CGST (9.0%):</td>
                                <td className="text-end">₹{Number(viewInvoice.cgst_amount).toFixed(2)}</td>
                              </tr>
                              <tr>
                                <td>SGST (9.0%):</td>
                                <td className="text-end">₹{Number(viewInvoice.sgst_amount).toFixed(2)}</td>
                              </tr>
                            </>
                          )}
                          {Number(viewInvoice.igst_amount) > 0 && (
                            <tr>
                              <td>IGST (18.0%):</td>
                              <td className="text-end">₹{Number(viewInvoice.igst_amount).toFixed(2)}</td>
                            </tr>
                          )}
                          <tr>
                            <td>Round Off:</td>
                            <td className="text-end">₹{Number(viewInvoice.round_off).toFixed(2)}</td>
                          </tr>
                          <tr className="border-top border-2 border-dark fs-6 fw-bold">
                            <td>Total Invoice Value:</td>
                            <td className="text-end text-primary">₹{Number(viewInvoice.grand_total).toFixed(2)}</td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <div className="border-top pt-2 mt-2 small">
                    <strong>Amount in Words:</strong> {viewInvoice.amountInWords || 'Rupees Only'}
                  </div>
                </div>
              </div>
              <div className="modal-footer no-print">
                <button type="button" className="btn btn-secondary" onClick={() => setViewInvoice(null)}>
                  Close
                </button>
                <button type="button" className="btn btn-primary" onClick={() => window.print()}>
                  <i className="bi bi-printer me-1"></i> Print Tax Invoice
                </button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Modal: Customize Print Columns (Handwritten Image 4.45.56 PM) */}
      {showColumnsModal && (
        <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
          <div className="modal-dialog modal-dialog-centered">
            <div className="modal-content">
              <div className="modal-header">
                <h5 className="modal-title">Customize Invoice Print Columns</h5>
                <button type="button" className="btn-close" onClick={() => setShowColumnsModal(false)}></button>
              </div>
              <div className="modal-body">
                <p className="text-muted small mb-3">
                  Select which fields and charges should appear on the customer's printed invoice (Image 4.45.56):
                </p>
                <div className="row g-2">
                  {Object.entries(invoiceColumns).map(([key, val]) => (
                    <div key={key} className="col-6">
                      <label className="form-check-label small d-flex align-items-center gap-2">
                        <input
                          type="checkbox"
                          className="form-check-input mt-0"
                          checked={val}
                          onChange={(e) =>
                            setInvoiceColumns((prev) => ({ ...prev, [key]: e.target.checked }))
                          }
                        />
                        <span className="text-capitalize">{key.replace(/([A-Z])/g, ' $1')}</span>
                      </label>
                    </div>
                  ))}
                </div>
              </div>
              <div className="modal-footer">
                <button
                  type="button"
                  className="btn btn-primary btn-sm px-4 fw-bold"
                  onClick={() => setShowColumnsModal(false)}
                >
                  Save Column Settings
                </button>
              </div>
            </div>
          </div>
        </div>
      )}

      {/* Modal: Post-Billing Line-Item Edit (Handwritten Image 4.45.54(1) PM) */}
      {editingItem && (
        <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
          <div className="modal-dialog modal-dialog-centered">
            <div className="modal-content">
              <div className="modal-header">
                <h5 className="modal-title">Post-Billing Line Edit: {editingItem.consignment_no}</h5>
                <button type="button" className="btn-close" onClick={() => setEditingItem(null)}></button>
              </div>
              <div className="modal-body">
                <div className="alert alert-info py-2 small mb-3">
                  <i className="bi bi-info-circle me-1"></i>
                  Modifications here adjust the invoice line amounts and recalculate totals automatically.
                </div>
                <div className="mb-2">
                  <label className="form-label small fw-semibold">AWB / Docket Number</label>
                  <input
                    type="text"
                    className="form-control form-control-sm font-monospace fw-bold"
                    value={editingItem.consignment_no}
                    onChange={(e) => setEditingItem({ ...editingItem, consignment_no: e.target.value })}
                  />
                </div>
                <div className="row g-2 mb-2">
                  <div className="col-md-6">
                    <label className="form-label small fw-semibold">Destination City</label>
                    <input
                      type="text"
                      className="form-control form-control-sm"
                      value={editingItem.destination_name}
                      onChange={(e) => setEditingItem({ ...editingItem, destination_name: e.target.value })}
                    />
                  </div>
                  <div className="col-md-6">
                    <label className="form-label small fw-semibold">Pieces Count</label>
                    <input
                      type="number"
                      className="form-control form-control-sm"
                      value={editingItem.pieces}
                      onChange={(e) => setEditingItem({ ...editingItem, pieces: Number(e.target.value) })}
                    />
                  </div>
                </div>
                <div className="row g-2 mb-3">
                  <div className="col-md-6">
                    <label className="form-label small fw-semibold">Billable Weight (kg)</label>
                    <input
                      type="number"
                      step="0.01"
                      className="form-control form-control-sm"
                      value={editingItem.billable_weight}
                      onChange={(e) => setEditingItem({ ...editingItem, billable_weight: Number(e.target.value) })}
                    />
                  </div>
                  <div className="col-md-6">
                    <label className="form-label small fw-semibold">Line Amount (₹)</label>
                    <input
                      type="number"
                      step="0.01"
                      className="form-control form-control-sm font-monospace fw-bold text-success"
                      value={editingItem.amount}
                      onChange={(e) => setEditingItem({ ...editingItem, amount: Number(e.target.value) })}
                    />
                  </div>
                </div>
              </div>
              <div className="modal-footer">
                <button type="button" className="btn btn-secondary btn-sm" onClick={() => setEditingItem(null)}>
                  Cancel
                </button>
                <button
                  type="button"
                  className="btn btn-primary btn-sm px-4 fw-bold"
                  onClick={() => {
                    if (viewInvoice) {
                      const updatedItems = viewInvoice.items.map((it: any) =>
                        it.id === editingItem.id ? editingItem : it
                      );
                      const newSubtotal = updatedItems.reduce((sum: number, it: any) => sum + Number(it.amount), 0);
                      const fuelAmt = (newSubtotal * (viewInvoice.fuel_surcharge_percent || 0)) / 100;
                      const taxable = newSubtotal + fuelAmt + Number(viewInvoice.docket_charge_amount || 0);
                      const isInterState = Number(viewInvoice.igst_amount) > 0;
                      const cgst = isInterState ? 0 : taxable * 0.09;
                      const sgst = isInterState ? 0 : taxable * 0.09;
                      const igst = isInterState ? taxable * 0.18 : 0;
                      const grand = Math.round(taxable + cgst + sgst + igst);

                      setViewInvoice({
                        ...viewInvoice,
                        items: updatedItems,
                        subtotal_amount: newSubtotal,
                        fuel_surcharge_amount: fuelAmt,
                        taxable_amount: taxable,
                        cgst_amount: cgst,
                        sgst_amount: sgst,
                        igst_amount: igst,
                        grand_total: grand,
                      });
                    }
                    setEditingItem(null);
                    alert('Post-billing line item updated and totals recalculated!');
                  }}
                >
                  Save Line Edit
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

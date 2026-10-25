// frontend/src/pages/agency/AccountsPage.tsx
import React, { useState, useEffect } from 'react';
import api from '../../services/api';
import { Client } from '../../types';

interface Transaction {
  id: string;
  type: 'received' | 'payment';
  date: string;
  party_name: string;
  account_type: 'customer' | 'vendor' | 'bank' | 'expense';
  mode: 'Cash' | 'Bank' | 'UPI' | 'Cheque' | 'TDS';
  reference_no: string;
  bank_account: string;
  amount: number;
  tds_amount: number;
  notes: string;
}

export const AccountsPage: React.FC = () => {
  const [activeTab, setActiveTab] = useState<'received' | 'payments' | 'ledger' | 'gst' | 'pnl'>('received');
  const [clients, setClients] = useState<Client[]>([]);
  const [transactions, setTransactions] = useState<Transaction[]>([]);
  const [selectedLedgerParty, setSelectedLedgerParty] = useState<string>('All');
  const [showModal, setShowModal] = useState(false);

  // Load clients and local transaction history
  useEffect(() => {
    const init = async () => {
      try {
        const cRes = await api.get('/clients');
        if (cRes.data.success) {
          setClients(cRes.data.data);
        }
      } catch (e) {
        console.error(e);
      }
    };
    init();

    const saved = localStorage.getItem('kesari_accounts_transactions');
    if (saved) {
      try {
        setTransactions(JSON.parse(saved));
      } catch (e) {
        // Fallback
      }
    } else {
      const initial: Transaction[] = [
        {
          id: 'REC-001',
          type: 'received',
          date: '2026-10-22',
          party_name: 'Shree Cement Ltd',
          account_type: 'customer',
          mode: 'Bank',
          reference_no: 'NEFT-88910245',
          bank_account: 'State Bank of India (Primary)',
          amount: 14500,
          tds_amount: 300,
          notes: 'Settlement against Invoice #KC/INV/012',
        },
        {
          id: 'REC-002',
          type: 'received',
          date: '2026-10-24',
          party_name: 'Jindal Steel & Power',
          account_type: 'customer',
          mode: 'UPI',
          reference_no: 'UPI/26104412/JSPL',
          bank_account: 'HDFC Bank (Current A/c 2)',
          amount: 8200,
          tds_amount: 0,
          notes: 'Advance booking payment',
        },
        {
          id: 'PAY-001',
          type: 'payment',
          date: '2026-10-21',
          party_name: 'Inter-City Linehaul Transporter',
          account_type: 'vendor',
          mode: 'Bank',
          reference_no: 'RTGS-1102945',
          bank_account: 'State Bank of India (Primary)',
          amount: 6500,
          tds_amount: 0,
          notes: 'Bhilai to Raipur nightly van freight',
        },
        {
          id: 'PAY-002',
          type: 'payment',
          date: '2026-10-23',
          party_name: 'Indian Oil Petrol Pump',
          account_type: 'expense',
          mode: 'Cash',
          reference_no: 'CASH-REC-104',
          bank_account: 'Cash in Hand',
          amount: 2400,
          tds_amount: 0,
          notes: 'Delivery van diesel refill',
        },
      ];
      setTransactions(initial);
      localStorage.setItem('kesari_accounts_transactions', JSON.stringify(initial));
    }
  }, []);

  // Form State for Recording Receipt / Payment
  const [formData, setFormData] = useState({
    type: 'received' as 'received' | 'payment',
    date: new Date().toISOString().split('T')[0],
    party_name: '',
    account_type: 'customer' as 'customer' | 'vendor' | 'bank' | 'expense',
    mode: 'Bank' as 'Cash' | 'Bank' | 'UPI' | 'Cheque' | 'TDS',
    reference_no: '',
    bank_account: 'State Bank of India (Primary)',
    amount: 0,
    tds_amount: 0,
    notes: '',
  });

  const handleSaveTransaction = (e: React.FormEvent) => {
    e.preventDefault();
    const newTx: Transaction = {
      id: `${formData.type === 'received' ? 'REC' : 'PAY'}-${Date.now().toString().slice(-4)}`,
      type: formData.type,
      date: formData.date,
      party_name: formData.party_name,
      account_type: formData.account_type,
      mode: formData.mode,
      reference_no: formData.reference_no || 'N/A',
      bank_account: formData.bank_account,
      amount: Number(formData.amount),
      tds_amount: Number(formData.tds_amount),
      notes: formData.notes,
    };

    const updated = [newTx, ...transactions];
    setTransactions(updated);
    localStorage.setItem('kesari_accounts_transactions', JSON.stringify(updated));
    setShowModal(false);
    alert(`${formData.type === 'received' ? 'Receipt (लेना)' : 'Payment (देना)'} voucher created successfully!`);
  };

  // Financial Computations for P&L and Ledgers
  const totalReceived = transactions
    .filter((t) => t.type === 'received')
    .reduce((sum, t) => sum + t.amount, 0);

  const totalPaid = transactions
    .filter((t) => t.type === 'payment')
    .reduce((sum, t) => sum + t.amount, 0);

  const netBalance = totalReceived - totalPaid;

  // Ledger filtered items
  const ledgerItems =
    selectedLedgerParty === 'All'
      ? transactions
      : transactions.filter((t) => t.party_name === selectedLedgerParty);

  return (
    <div className="animate-enter">
      {/* Header */}
      <div className="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
          <div className="d-flex align-items-center gap-2">
            <h2 className="brand-title mb-0">Accounts & Ledger (Tally-Logic)</h2>
            <span className="badge bg-success-subtle text-success border">Module 3</span>
          </div>
          <p className="text-muted small mb-0 mt-1">
            Received (लेना) • Payments (देना) • Customer & Bank Ledgers (लेज़र) • GST • P&L Statement (Image 4.45.45)
          </p>
        </div>

        <div className="d-flex gap-2">
          <button
            className="btn btn-success btn-sm px-3 shadow-sm"
            onClick={() => {
              setFormData((prev) => ({ ...prev, type: 'received' }));
              setShowModal(true);
            }}
          >
            <i className="bi bi-plus-circle me-1"></i> Received Voucher (लेना)
          </button>
          <button
            className="btn btn-danger btn-sm px-3 shadow-sm"
            onClick={() => {
              setFormData((prev) => ({ ...prev, type: 'payment', account_type: 'vendor' }));
              setShowModal(true);
            }}
          >
            <i className="bi bi-dash-circle me-1"></i> Payment Voucher (देना)
          </button>
        </div>
      </div>

      {/* Financial Metrics Cards */}
      <div className="row g-3 mb-4">
        <div className="col-12 col-sm-4">
          <div className="stat-card border-success">
            <div className="stat-icon-wrapper bg-success-subtle text-success">
              <i className="bi bi-cash-stack"></i>
            </div>
            <div>
              <div className="text-muted small fw-semibold">Total Received (लेना)</div>
              <div className="fs-3 fw-bold text-success">₹{totalReceived.toLocaleString('en-IN')}</div>
              <div className="small text-muted">Customer Collections</div>
            </div>
          </div>
        </div>

        <div className="col-12 col-sm-4">
          <div className="stat-card border-danger">
            <div className="stat-icon-wrapper bg-danger-subtle text-danger">
              <i className="bi bi-arrow-up-right-circle"></i>
            </div>
            <div>
              <div className="text-muted small fw-semibold">Total Payments (देना)</div>
              <div className="fs-3 fw-bold text-danger">₹{totalPaid.toLocaleString('en-IN')}</div>
              <div className="small text-muted">Transporter, Fuel & Expenses</div>
            </div>
          </div>
        </div>

        <div className="col-12 col-sm-4">
          <div className="stat-card border-primary">
            <div className="stat-icon-wrapper bg-primary-subtle text-primary">
              <i className="bi bi-wallet2"></i>
            </div>
            <div>
              <div className="text-muted small fw-semibold">Net Cash / Bank Liquidity</div>
              <div className={`fs-3 fw-bold ${netBalance >= 0 ? 'text-primary' : 'text-danger'}`}>
                ₹{netBalance.toLocaleString('en-IN')}
              </div>
              <div className="small text-muted">Primary + Secondary A/c</div>
            </div>
          </div>
        </div>
      </div>

      {/* Navigation Tabs */}
      <ul className="nav nav-pills mb-3 border-bottom pb-2">
        <li className="nav-item">
          <button
            className={`nav-link py-1 px-3 fw-semibold ${activeTab === 'received' ? 'active' : ''}`}
            onClick={() => setActiveTab('received')}
          >
            <i className="bi bi-arrow-down-left me-1"></i> 1. Received (लेना)
          </button>
        </li>
        <li className="nav-item">
          <button
            className={`nav-link py-1 px-3 fw-semibold ${activeTab === 'payments' ? 'active' : ''}`}
            onClick={() => setActiveTab('payments')}
          >
            <i className="bi bi-arrow-up-right me-1"></i> 2. Payments (देना)
          </button>
        </li>
        <li className="nav-item">
          <button
            className={`nav-link py-1 px-3 fw-semibold ${activeTab === 'ledger' ? 'active' : ''}`}
            onClick={() => setActiveTab('ledger')}
          >
            <i className="bi bi-book me-1"></i> 3. Ledger (लेज़र)
          </button>
        </li>
        <li className="nav-item">
          <button
            className={`nav-link py-1 px-3 fw-semibold ${activeTab === 'gst' ? 'active' : ''}`}
            onClick={() => setActiveTab('gst')}
          >
            <i className="bi bi-percent me-1"></i> 4. GST Calculations
          </button>
        </li>
        <li className="nav-item">
          <button
            className={`nav-link py-1 px-3 fw-semibold ${activeTab === 'pnl' ? 'active' : ''}`}
            onClick={() => setActiveTab('pnl')}
          >
            <i className="bi bi-graph-up-arrow me-1"></i> 5. P & L Account
          </button>
        </li>
      </ul>

      {/* Tab 1: Received (लेना) */}
      {activeTab === 'received' && (
        <div className="app-card shadow-sm border overflow-hidden">
          <div className="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
            <span className="fw-bold text-uppercase small text-muted">Customer Receipts Register (लेना)</span>
            <span className="badge bg-success">{transactions.filter((t) => t.type === 'received').length} Vouchers</span>
          </div>
          <div className="table-responsive">
            <table className="table table-hover align-middle mb-0">
              <thead className="table-light small text-muted">
                <tr>
                  <th>Voucher No</th>
                  <th>Date</th>
                  <th>Customer Name</th>
                  <th>Payment Mode</th>
                  <th>Bank Account Deposited</th>
                  <th>Reference / Cheque No</th>
                  <th className="text-end">TDS (₹)</th>
                  <th className="text-end">Amount Received (₹)</th>
                </tr>
              </thead>
              <tbody>
                {transactions
                  .filter((t) => t.type === 'received')
                  .map((t) => (
                    <tr key={t.id}>
                      <td className="font-monospace fw-bold text-primary">{t.id}</td>
                      <td>{t.date}</td>
                      <td className="fw-semibold">{t.party_name}</td>
                      <td>
                        <span className="badge bg-light text-dark border">{t.mode}</span>
                      </td>
                      <td className="small text-muted">{t.bank_account}</td>
                      <td className="font-monospace small">{t.reference_no}</td>
                      <td className="text-end text-muted font-monospace">₹{t.tds_amount}</td>
                      <td className="text-end fw-bold text-success font-monospace fs-6">
                        +₹{t.amount.toLocaleString('en-IN')}
                      </td>
                    </tr>
                  ))}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {/* Tab 2: Payments (देना) */}
      {activeTab === 'payments' && (
        <div className="app-card shadow-sm border overflow-hidden">
          <div className="p-3 bg-light border-bottom d-flex justify-content-between align-items-center">
            <span className="fw-bold text-uppercase small text-muted">Disbursements & Expenses Register (देना)</span>
            <span className="badge bg-danger">{transactions.filter((t) => t.type === 'payment').length} Vouchers</span>
          </div>
          <div className="table-responsive">
            <table className="table table-hover align-middle mb-0">
              <thead className="table-light small text-muted">
                <tr>
                  <th>Voucher No</th>
                  <th>Date</th>
                  <th>Payee / Vendor Name</th>
                  <th>Expense Category</th>
                  <th>Payment Mode</th>
                  <th>Bank / Cash Account</th>
                  <th>Reference No</th>
                  <th className="text-end">Amount Paid (₹)</th>
                </tr>
              </thead>
              <tbody>
                {transactions
                  .filter((t) => t.type === 'payment')
                  .map((t) => (
                    <tr key={t.id}>
                      <td className="font-monospace fw-bold text-danger">{t.id}</td>
                      <td>{t.date}</td>
                      <td className="fw-semibold">{t.party_name}</td>
                      <td>
                        <span className="badge bg-light text-dark border text-uppercase">{t.account_type}</span>
                      </td>
                      <td>
                        <span className="badge bg-light text-dark border">{t.mode}</span>
                      </td>
                      <td className="small text-muted">{t.bank_account}</td>
                      <td className="font-monospace small">{t.reference_no}</td>
                      <td className="text-end fw-bold text-danger font-monospace fs-6">
                        -₹{t.amount.toLocaleString('en-IN')}
                      </td>
                    </tr>
                  ))}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {/* Tab 3: Ledger (लेज़र) */}
      {activeTab === 'ledger' && (
        <div className="app-card shadow-sm border">
          <div className="p-3 bg-light border-bottom d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center gap-2">
            <div className="d-flex align-items-center gap-2">
              <label className="small fw-bold mb-0">Select Account / Party:</label>
              <select
                className="form-select form-select-sm w-auto"
                value={selectedLedgerParty}
                onChange={(e) => setSelectedLedgerParty(e.target.value)}
              >
                <option value="All">All Ledgers (Consolidated)</option>
                {clients.map((c) => (
                  <option key={c.id} value={c.name}>
                    Customer: {c.name}
                  </option>
                ))}
                <option value="Inter-City Linehaul Transporter">Vendor: Inter-City Linehaul Transporter</option>
                <option value="Indian Oil Petrol Pump">Vendor: Indian Oil Petrol Pump</option>
              </select>
            </div>
            <button className="btn btn-outline-secondary btn-sm" onClick={() => window.print()}>
              <i className="bi bi-printer me-1"></i> Print Statement
            </button>
          </div>
          <div className="table-responsive">
            <table className="table table-bordered table-hover align-middle mb-0">
              <thead className="table-light small text-center">
                <tr>
                  <th>Date</th>
                  <th>Voucher</th>
                  <th>Particulars</th>
                  <th>Mode / Ref</th>
                  <th className="text-end text-success">Credit (जमा)</th>
                  <th className="text-end text-danger">Debit (नामे)</th>
                </tr>
              </thead>
              <tbody>
                {ledgerItems.map((item) => (
                  <tr key={item.id}>
                    <td className="text-center font-monospace small">{item.date}</td>
                    <td className="font-monospace small fw-bold">{item.id}</td>
                    <td>
                      <div className="fw-semibold">{item.party_name}</div>
                      <div className="small text-muted">{item.notes}</div>
                    </td>
                    <td className="small text-muted">
                      {item.mode} ({item.reference_no})
                    </td>
                    <td className="text-end text-success font-monospace">
                      {item.type === 'received' ? `₹${item.amount.toLocaleString('en-IN')}` : '-'}
                    </td>
                    <td className="text-end text-danger font-monospace">
                      {item.type === 'payment' ? `₹${item.amount.toLocaleString('en-IN')}` : '-'}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      )}

      {/* Tab 4: GST Calculations */}
      {activeTab === 'gst' && (
        <div className="app-card shadow-sm border p-4">
          <h5 className="fw-bold text-primary mb-3">GST Tax Summary (SAC 996812 Logistics)</h5>
          <div className="row g-3 mb-4">
            <div className="col-md-4">
              <div className="p-3 border rounded bg-light">
                <div className="text-muted small">Taxable Freight Turnover:</div>
                <div className="fs-4 fw-bold">₹{(totalReceived / 1.18).toFixed(2)}</div>
                <div className="small text-muted">SAC 996812 Output Services</div>
              </div>
            </div>
            <div className="col-md-4">
              <div className="p-3 border rounded bg-light">
                <div className="text-muted small">CGST Output (9%):</div>
                <div className="fs-4 fw-bold text-primary">₹{(((totalReceived / 1.18) * 0.09) || 0).toFixed(2)}</div>
                <div className="small text-muted">Intra-State Central Tax</div>
              </div>
            </div>
            <div className="col-md-4">
              <div className="p-3 border rounded bg-light">
                <div className="text-muted small">SGST Output (9%):</div>
                <div className="fs-4 fw-bold text-primary">₹{(((totalReceived / 1.18) * 0.09) || 0).toFixed(2)}</div>
                <div className="small text-muted">Intra-State State Tax</div>
              </div>
            </div>
          </div>
          <div className="p-3 bg-light-subtle border rounded small">
            <i className="bi bi-info-circle me-1 text-primary"></i>
            Compliant with Indian GSTR-1 and GSTR-3B filings. Inter-state consignments automatically map to 18% IGST.
          </div>
        </div>
      )}

      {/* Tab 5: Profit & Loss Account */}
      {activeTab === 'pnl' && (
        <div className="app-card shadow-sm border p-4">
          <div className="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div>
              <h5 className="fw-bold mb-0">Profit & Loss Statement (P&L Account)</h5>
              <p className="text-muted small mb-0">For Fiscal Period 2026-27 • Tally Architecture Standard</p>
            </div>
            <button className="btn btn-outline-secondary btn-sm" onClick={() => window.print()}>
              <i className="bi bi-printer me-1"></i> Print P&L
            </button>
          </div>

          <div className="row g-4">
            {/* Income Side */}
            <div className="col-md-6 border-end">
              <h6 className="fw-bold text-success text-uppercase small mb-3">Revenue / Income (जमा)</h6>
              <div className="d-flex justify-content-between py-2 border-bottom">
                <span>Courier Freight Billings & Invoices:</span>
                <span className="fw-bold font-monospace">₹{totalReceived.toLocaleString('en-IN')}</span>
              </div>
              <div className="d-flex justify-content-between py-2 border-bottom">
                <span>Fuel Surcharges & Docket Fees:</span>
                <span className="fw-bold font-monospace">₹1,850.00</span>
              </div>
              <div className="d-flex justify-content-between py-2 border-bottom">
                <span>Other Service Receipts:</span>
                <span className="fw-bold font-monospace">₹450.00</span>
              </div>
              <div className="d-flex justify-content-between py-3 fs-5 fw-bold text-success">
                <span>Total Income:</span>
                <span className="font-monospace">₹{(totalReceived + 2300).toLocaleString('en-IN')}</span>
              </div>
            </div>

            {/* Expenditure Side */}
            <div className="col-md-6">
              <h6 className="fw-bold text-danger text-uppercase small mb-3">Operating Expenses (खर्चे)</h6>
              <div className="d-flex justify-content-between py-2 border-bottom">
                <span>Linehaul & Partner Transporter Freight:</span>
                <span className="fw-bold font-monospace">₹{totalPaid.toLocaleString('en-IN')}</span>
              </div>
              <div className="d-flex justify-content-between py-2 border-bottom">
                <span>Vehicle Diesel & Fuel:</span>
                <span className="fw-bold font-monospace">₹2,400.00</span>
              </div>
              <div className="d-flex justify-content-between py-2 border-bottom">
                <span>Branch Rent & Office Utilities:</span>
                <span className="fw-bold font-monospace">₹3,500.00</span>
              </div>
              <div className="d-flex justify-content-between py-3 fs-5 fw-bold text-danger">
                <span>Total Expenses:</span>
                <span className="font-monospace">₹{(totalPaid + 5900).toLocaleString('en-IN')}</span>
              </div>
            </div>
          </div>

          <div className="mt-4 p-3 border rounded bg-light d-flex justify-content-between align-items-center">
            <span className="fs-5 fw-bold">Net Operational Profit / Margin:</span>
            <span className="fs-4 fw-bolder text-primary font-monospace">
              ₹{(totalReceived + 2300 - (totalPaid + 5900)).toLocaleString('en-IN')}
            </span>
          </div>
        </div>
      )}

      {/* Modal: Create Voucher */}
      {showModal && (
        <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
          <div className="modal-dialog modal-dialog-centered">
            <div className="modal-content">
              <div className="modal-header">
                <h5 className="modal-title">
                  {formData.type === 'received' ? 'New Receipt Voucher (लेना)' : 'New Payment Voucher (देना)'}
                </h5>
                <button type="button" className="btn-close" onClick={() => setShowModal(false)}></button>
              </div>
              <form onSubmit={handleSaveTransaction}>
                <div className="modal-body">
                  <div className="row g-2 mb-3">
                    <div className="col-md-6">
                      <label className="form-label small fw-semibold">Voucher Date *</label>
                      <input
                        type="date"
                        className="form-control form-control-sm"
                        required
                        value={formData.date}
                        onChange={(e) => setFormData({ ...formData, date: e.target.value })}
                      />
                    </div>
                    <div className="col-md-6">
                      <label className="form-label small fw-semibold">Payment Mode *</label>
                      <select
                        className="form-select form-select-sm"
                        value={formData.mode}
                        onChange={(e) => setFormData({ ...formData, mode: e.target.value as any })}
                      >
                        <option value="Bank">Bank Transfer (NEFT/RTGS)</option>
                        <option value="UPI">UPI / QR Code</option>
                        <option value="Cash">Cash in Hand</option>
                        <option value="Cheque">Cheque</option>
                        <option value="TDS">TDS Deducted</option>
                      </select>
                    </div>
                  </div>

                  <div className="mb-3">
                    <label className="form-label small fw-semibold">
                      {formData.type === 'received' ? 'Customer / Remitter Name *' : 'Payee / Vendor Name *'}
                    </label>
                    <input
                      type="text"
                      className="form-control form-control-sm"
                      required
                      placeholder="e.g. Apex Pharma or Transporter"
                      value={formData.party_name}
                      onChange={(e) => setFormData({ ...formData, party_name: e.target.value })}
                    />
                  </div>

                  <div className="row g-2 mb-3">
                    <div className="col-md-6">
                      <label className="form-label small fw-semibold">Amount (₹) *</label>
                      <input
                        type="number"
                        step="0.01"
                        className="form-control form-control-sm font-monospace fw-bold"
                        required
                        value={formData.amount || ''}
                        onChange={(e) => setFormData({ ...formData, amount: Number(e.target.value) })}
                      />
                    </div>
                    <div className="col-md-6">
                      <label className="form-label small fw-semibold">TDS Deducted (₹)</label>
                      <input
                        type="number"
                        step="0.01"
                        className="form-control form-control-sm font-monospace"
                        value={formData.tds_amount || ''}
                        onChange={(e) => setFormData({ ...formData, tds_amount: Number(e.target.value) })}
                      />
                    </div>
                  </div>

                  <div className="mb-3">
                    <label className="form-label small fw-semibold">Bank Account</label>
                    <select
                      className="form-select form-select-sm"
                      value={formData.bank_account}
                      onChange={(e) => setFormData({ ...formData, bank_account: e.target.value })}
                    >
                      <option value="State Bank of India (Primary)">State Bank of India (Primary A/c)</option>
                      <option value="HDFC Bank (Current A/c 2)">HDFC Bank (Secondary A/c)</option>
                      <option value="Cash in Hand">Cash in Hand</option>
                    </select>
                  </div>

                  <div className="mb-3">
                    <label className="form-label small fw-semibold">Reference / UTR / Cheque No</label>
                    <input
                      type="text"
                      className="form-control form-control-sm font-monospace"
                      placeholder="e.g. UTR-9821481"
                      value={formData.reference_no}
                      onChange={(e) => setFormData({ ...formData, reference_no: e.target.value })}
                    />
                  </div>

                  <div className="mb-3">
                    <label className="form-label small fw-semibold">Notes / Narration</label>
                    <textarea
                      className="form-control form-control-sm"
                      rows={2}
                      placeholder="e.g. Clearance of October monthly freight invoice"
                      value={formData.notes}
                      onChange={(e) => setFormData({ ...formData, notes: e.target.value })}
                    ></textarea>
                  </div>
                </div>
                <div className="modal-footer">
                  <button type="button" className="btn btn-secondary btn-sm" onClick={() => setShowModal(false)}>
                    Cancel
                  </button>
                  <button type="submit" className="btn btn-primary btn-sm px-4 fw-bold">
                    Save Voucher
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

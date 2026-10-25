import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import api from '../../services/api';

export const AgencyDashboard: React.FC = () => {
  const [data, setData] = useState<any | null>(null);
  const [loading, setLoading] = useState(true);

  const fetchDashboard = async () => {
    try {
      setLoading(true);
      const res = await api.get('/agency/dashboard');
      if (res.data.success) {
        setData(res.data.data);
      }
    } catch (e) {
      console.error(e);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchDashboard();
  }, []);

  if (loading) {
    return (
      <div className="text-center py-5">
        <div className="spinner-border text-primary" role="status"></div>
        <div className="text-muted mt-2 small">Loading agency operations...</div>
      </div>
    );
  }

  const kpis = data?.kpis || {};
  const agency = data?.agency || {};

  return (
    <div className="animate-enter">
      {/* Premium Logistics Hero Banner with Animated Graphics */}
      <div className="hero-logistics-card mb-4 d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-4">
        <div style={{ zIndex: 1, maxWidth: '640px' }}>
          <div className="d-flex align-items-center gap-2 mb-2">
            <span className="glass-badge">
              <i className="bi bi-shield-check text-warning"></i> Certified Agency Network
            </span>
            <span className="badge bg-warning text-dark font-monospace fw-bold">#{agency.agency_code || '26'}</span>
          </div>
          <h2 className="text-white fw-bold mb-1 display-6 fs-3">
            {agency.trade_name || agency.name || 'Express Logistics Control Desk'}
          </h2>
          <p className="text-white-50 small mb-3">
            High-speed numpad booking, real-time line-haul dispatch, Tally-compatible ledgers, and SAC 996812 GST automation.
          </p>
          <div className="d-flex flex-wrap gap-2">
            <Link to="/bookings/new" className="btn btn-light btn-sm fw-bold px-3 shadow-sm">
              <i className="bi bi-lightning-charge-fill text-primary"></i> Rapid Booking (Sticky)
            </Link>
            <Link to="/operations/inscan" className="btn btn-outline-light btn-sm px-3">
              <i className="bi bi-upc-scan"></i> Hub Inscan
            </Link>
            <Link to="/quotations" className="btn btn-outline-light btn-sm px-3">
              <i className="bi bi-file-earmark-spreadsheet"></i> Pre-Sales Quotation
            </Link>
            <Link to="/accounts" className="btn btn-outline-light btn-sm px-3">
              <i className="bi bi-journal-bookmark"></i> Ledgers (खाता)
            </Link>
          </div>
        </div>

        <div className="d-none d-lg-flex align-items-center justify-content-center pe-3" style={{ zIndex: 1 }}>
          <img
            src="/van_cutout.png"
            alt="Express Delivery Fleet"
            className="hero-cutout-img"
          />
        </div>
      </div>

      {/* KPI Stats Grid - Tight 2x2 on Mobile */}
      <div className="row g-2 g-md-3 mb-4">
        <div className="col-6 col-lg-3">
          <div className="stat-card">
            <div className="stat-icon-wrapper bg-primary-subtle text-primary">
              <i className="bi bi-calendar2-check"></i>
            </div>
            <div>
              <div className="text-muted small fw-semibold">Today</div>
              <div className="fs-3 fw-bold">{kpis.todayBookings || 0}</div>
              <div className="small text-muted d-none d-sm-block">Total: {kpis.totalBookings || 0} AWB</div>
            </div>
          </div>
        </div>

        <div className="col-6 col-lg-3">
          <div className="stat-card">
            <div className="stat-icon-wrapper bg-warning-subtle text-warning">
              <i className="bi bi-truck"></i>
            </div>
            <div>
              <div className="text-muted small fw-semibold">In Transit</div>
              <div className="fs-3 fw-bold">{kpis.inTransitCount || 0}</div>
              <div className="small text-muted d-none d-sm-block">Out for Deliv: {kpis.outForDeliveryCount || 0}</div>
            </div>
          </div>
        </div>

        <div className="col-6 col-lg-3">
          <div className="stat-card">
            <div className="stat-icon-wrapper bg-success-subtle text-success">
              <i className="bi bi-check-circle"></i>
            </div>
            <div>
              <div className="text-muted small fw-semibold">Delivered (POD)</div>
              <div className="fs-3 fw-bold">{kpis.deliveredCount || 0}</div>
              <div className="small text-success d-none d-sm-block">Completed shipments</div>
            </div>
          </div>
        </div>

        <div className="col-6 col-lg-3">
          <div className="stat-card">
            <div className="stat-icon-wrapper bg-info-subtle text-info">
              <i className="bi bi-cash-stack"></i>
            </div>
            <div>
              <div className="text-muted small fw-semibold">Revenue</div>
              <div className="fs-3 fw-bold">₹{kpis.totalFreightRevenue?.toLocaleString('en-IN') || 0}</div>
              <div className="small text-muted d-none d-sm-block">Unbilled: {kpis.unbilledCount || 0} AWBs</div>
            </div>
          </div>
        </div>
      </div>

      {/* Operational Highlights - 2x2 on Mobile */}
      <div className="row g-2 g-md-3 mb-4">
        <div className="col-6 col-lg-3">
          <Link to="/branches" className="text-decoration-none">
            <div className="app-card p-2 p-sm-3 h-100">
              <div className="d-flex justify-content-between align-items-center mb-1">
                <span className="fw-semibold text-dark small">Branches</span>
                <span className="badge bg-primary rounded-pill">{kpis.branchesCount || 1}</span>
              </div>
              <small className="text-muted d-none d-sm-block">Multi-location counters</small>
            </div>
          </Link>
        </div>

        <div className="col-6 col-lg-3">
          <Link to="/clients" className="text-decoration-none">
            <div className="app-card p-2 p-sm-3 h-100">
              <div className="d-flex justify-content-between align-items-center mb-1">
                <span className="fw-semibold text-dark small">B2B Clients</span>
                <span className="badge bg-info text-dark rounded-pill">{kpis.clientsCount || 0}</span>
              </div>
              <small className="text-muted d-none d-sm-block">Corporate accounts</small>
            </div>
          </Link>
        </div>

        <div className="col-6 col-lg-3">
          <Link to="/invoices" className="text-decoration-none">
            <div className="app-card p-2 p-sm-3 h-100">
              <div className="d-flex justify-content-between align-items-center mb-1">
                <span className="fw-semibold text-dark small">Pending Billing</span>
                <span className="badge bg-warning text-dark rounded-pill">{kpis.unbilledCount || 0}</span>
              </div>
              <small className="text-muted d-none d-sm-block">Awaiting GST invoice</small>
            </div>
          </Link>
        </div>

        <div className="col-6 col-lg-3">
          <Link to="/tariffs" className="text-decoration-none">
            <div className="app-card p-2 p-sm-3 h-100">
              <div className="d-flex justify-content-between align-items-center mb-1">
                <span className="fw-semibold text-dark small">Tariff Matrix</span>
                <i className="bi bi-arrow-right text-primary"></i>
              </div>
              <small className="text-muted d-none d-sm-block">Weight slabs</small>
            </div>
          </Link>
        </div>
      </div>

      {/* Recent Bookings Table */}
      <div className="app-card p-4">
        <div className="d-flex justify-content-between align-items-center mb-3">
          <div>
            <h5 className="mb-0 fw-bold">Recent Consignments</h5>
            <small className="text-muted">Latest counter bookings across your branches</small>
          </div>
          <Link to="/bookings" className="btn btn-outline-primary btn-sm">
            View All Shipments
          </Link>
        </div>

        <div className="table-responsive">
          <table className="table table-hover align-middle mb-0">
            <thead className="table-light">
              <tr className="small text-muted text-uppercase">
                <th>Consignment AWB</th>
                <th>Destination</th>
                <th>Client / Consignor</th>
                <th>Recipient</th>
                <th>Weight (Billable)</th>
                <th>Total</th>
                <th>Status</th>
                <th className="text-end">Label</th>
              </tr>
            </thead>
            <tbody>
              {data?.recentBookings?.map((b: any) => (
                <tr key={b.id}>
                  <td>
                    <span className="fw-bold font-monospace text-primary">{b.consignment_no}</span>
                    <div className="small text-muted">{new Date(b.booking_date).toLocaleDateString()}</div>
                  </td>
                  <td>
                    <span className="badge bg-light text-dark border font-monospace">
                      {b.destination?.code}
                    </span>{' '}
                    {b.destination?.name}
                  </td>
                  <td className="small">{b.client?.name || 'Walk-in Counter'}</td>
                  <td>
                    <div className="fw-semibold small">{b.recipient_name}</div>
                    <div className="small text-muted">{b.recipient_phone}</div>
                  </td>
                  <td>
                    <span className="fw-semibold">{b.billable_weight} kg</span>{' '}
                    <span className="badge bg-secondary-subtle text-secondary small">
                      {b.send_by}
                    </span>
                  </td>
                  <td className="fw-bold">₹{Number(b.total_amount).toFixed(2)}</td>
                  <td>
                    <span className={`badge-status ${b.status}`}>{b.status}</span>
                  </td>
                  <td className="text-end">
                    <Link
                      to={`/operations/print?awb=${b.consignment_no}`}
                      className="btn btn-sm btn-outline-secondary py-1"
                    >
                      <i className="bi bi-printer me-1"></i> Print
                    </Link>
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

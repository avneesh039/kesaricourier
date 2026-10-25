// frontend/src/pages/public/SubscriptionPlansPage.tsx
import React, { useState } from 'react';
import { Link } from 'react-router-dom';
import { PublicNavbar } from '../../components/public/PublicNavbar';
import { PublicFooter } from '../../components/public/PublicFooter';

export const SubscriptionPlansPage: React.FC = () => {
  const [billingCycle, setBillingCycle] = useState<'monthly' | 'yearly'>('yearly');

  return (
    <div className="min-vh-100 d-flex flex-column bg-surface animate-enter">
      <PublicNavbar />

      {/* Hero Header */}
      <section className="py-5 bg-dark text-white text-center position-relative overflow-hidden" style={{ background: 'linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%)' }}>
        <div className="container py-4 position-relative" style={{ zIndex: 2 }}>
          <span className="badge bg-warning text-dark text-uppercase px-3 py-1 mb-2 font-monospace">
            Transparent SaaS Pricing (Spec 4.46.04)
          </span>
          <h1 className="display-4 fw-extrabold mb-3">
            SaaS Subscription Plans for <span className="text-warning">Agencies of Every Size</span>
          </h1>
          <p className="lead text-white-50 mb-4" style={{ maxWidth: '680px', margin: '0 auto' }}>
            Zero setup fees. Choose your tier according to your counter operators and branch network. 
            Upgrade anytime with seamless database continuity.
          </p>

          {/* Billing Cycle Toggle */}
          <div className="d-inline-flex flex-column flex-sm-row align-items-center p-1 bg-white bg-opacity-10 border border-white border-opacity-20 rounded-4 rounded-sm-pill gap-1 gap-sm-0" style={{ maxWidth: '100%' }}>
            <button
              className={`btn btn-sm rounded-pill px-3 px-sm-4 fw-bold transition-all w-100 w-sm-auto ${
                billingCycle === 'monthly' ? 'btn-primary text-white shadow-sm' : 'text-white-50 border-0'
              }`}
              onClick={() => setBillingCycle('monthly')}
            >
              Monthly Billing
            </button>
            <button
              className={`btn btn-sm rounded-pill px-3 px-sm-4 fw-bold transition-all w-100 w-sm-auto ${
                billingCycle === 'yearly' ? 'btn-primary text-white shadow-sm' : 'text-white-50 border-0'
              }`}
              onClick={() => setBillingCycle('yearly')}
            >
              Yearly (Save 20% • 2 Mo Free)
            </button>
          </div>
        </div>
      </section>

      {/* Pricing Cards Grid */}
      <section className="py-5 bg-white">
        <div className="container">
          <div className="row g-4 justify-content-center">
            {/* Plan 1: Free Starter Counter */}
            <div className="col-12 col-md-6 col-lg-4 col-xl-2">
              <div className="app-card p-3 h-100 border text-center d-flex flex-column justify-content-between">
                <div>
                  <h6 className="fw-bold text-muted text-uppercase small mb-1">Starter</h6>
                  <div className="fs-3 fw-extrabold text-dark my-1">₹0</div>
                  <div className="text-muted small mb-3">Free Forever</div>
                  <hr />
                  <ul className="list-unstyled text-start small d-flex flex-column gap-2 mb-3">
                    <li><i className="bi bi-check-circle-fill text-success me-1"></i> 1 Staff User</li>
                    <li><i className="bi bi-check-circle-fill text-success me-1"></i> 1 Counter Branch</li>
                    <li><i className="bi bi-check-circle-fill text-success me-1"></i> 500 Dockets/Mo</li>
                    <li><i className="bi bi-check-circle-fill text-success me-1"></i> Code-128 Labels</li>
                    <li><i className="bi bi-check-circle-fill text-success me-1"></i> Auto-Approved</li>
                  </ul>
                </div>
                <Link to="/register-agency" className="btn btn-outline-secondary w-100 btn-sm">
                  Start Free
                </Link>
              </div>
            </div>

            {/* Plan 2: Monthly Growth */}
            <div className="col-12 col-md-6 col-lg-4 col-xl-2">
              <div className="app-card p-3 h-100 border text-center d-flex flex-column justify-content-between">
                <div>
                  <h6 className="fw-bold text-primary text-uppercase small mb-1">Monthly</h6>
                  <div className="fs-3 fw-extrabold text-primary my-1">
                    {billingCycle === 'yearly' ? '₹180' : '₹200'}
                    <span className="fs-6 text-muted fw-normal">/mo</span>
                  </div>
                  <div className="text-muted small mb-3">+ GST Billed Monthly</div>
                  <hr />
                  <ul className="list-unstyled text-start small d-flex flex-column gap-2 mb-3">
                    <li><i className="bi bi-check-circle-fill text-success me-1"></i> 2 Staff Users</li>
                    <li><i className="bi bi-check-circle-fill text-success me-1"></i> 2 Counter Branches</li>
                    <li><i className="bi bi-check-circle-fill text-success me-1"></i> Unlimited Dockets</li>
                    <li><i className="bi bi-check-circle-fill text-success me-1"></i> Sticky Field Locks</li>
                    <li><i className="bi bi-check-circle-fill text-success me-1"></i> Auto Zone Tariffs</li>
                  </ul>
                </div>
                <Link to="/register-agency" className="btn btn-outline-primary w-100 btn-sm">
                  Choose Monthly
                </Link>
              </div>
            </div>

            {/* Plan 3: Quarterly Pro */}
            <div className="col-12 col-md-6 col-lg-4 col-xl-2">
              <div className="app-card p-3 h-100 border text-center d-flex flex-column justify-content-between">
                <div>
                  <h6 className="fw-bold text-info text-uppercase small mb-1">Quarterly</h6>
                  <div className="fs-3 fw-extrabold text-dark my-1">
                    ₹620
                    <span className="fs-6 text-muted fw-normal">/qtr</span>
                  </div>
                  <div className="text-muted small mb-3">Billed Every 3 Months</div>
                  <hr />
                  <ul className="list-unstyled text-start small d-flex flex-column gap-2 mb-3">
                    <li><i className="bi bi-check-circle-fill text-success me-1"></i> 5 Staff Users</li>
                    <li><i className="bi bi-check-circle-fill text-success me-1"></i> 5 Counter Branches</li>
                    <li><i className="bi bi-check-circle-fill text-success me-1"></i> Line-haul Manifests</li>
                    <li><i className="bi bi-check-circle-fill text-success me-1"></i> Runsheets & POD</li>
                    <li><i className="bi bi-check-circle-fill text-success me-1"></i> Inscan Hub Scanner</li>
                  </ul>
                </div>
                <Link to="/register-agency" className="btn btn-outline-info w-100 btn-sm">
                  Choose Quarterly
                </Link>
              </div>
            </div>

            {/* Plan 4: Yearly Enterprise (FEATURED) */}
            <div className="col-12 col-md-6 col-lg-4 col-xl-3">
              <div className="app-card p-3 h-100 border border-primary shadow-lg position-relative text-center d-flex flex-column justify-content-between bg-primary-subtle bg-opacity-25">
                <span className="position-absolute top-0 start-50 translate-middle badge bg-primary px-3 py-1 fw-bold">
                  RECOMMENDED
                </span>
                <div>
                  <h6 className="fw-bold text-dark text-uppercase small mb-1 pt-2">Yearly Pro</h6>
                  <div className="fs-2 fw-extrabold text-primary my-1">
                    ₹2,124
                    <span className="fs-6 text-muted fw-normal">/year</span>
                  </div>
                  <div className="text-success small fw-semibold mb-3">Includes 2 Months Free</div>
                  <hr />
                  <ul className="list-unstyled text-start small d-flex flex-column gap-2 mb-3">
                    <li><i className="bi bi-check-circle-fill text-success me-1"></i> <strong>10 Staff Users</strong></li>
                    <li><i className="bi bi-check-circle-fill text-success me-1"></i> <strong>10 Branch Hubs</strong></li>
                    <li><i className="bi bi-check-circle-fill text-success me-1"></i> Tally Accounts (खाता)</li>
                    <li><i className="bi bi-check-circle-fill text-success me-1"></i> SAC 996812 GST Engine</li>
                    <li><i className="bi bi-check-circle-fill text-success me-1"></i> GSTR-1 CSV Exports</li>
                    <li><i className="bi bi-check-circle-fill text-success me-1"></i> Post-Billing Item Edits</li>
                    <li><i className="bi bi-check-circle-fill text-success me-1"></i> 6 Courier Tracking APIs</li>
                  </ul>
                </div>
                <Link to="/register-agency" className="btn btn-primary w-100 btn-sm fw-bold shadow-sm">
                  Subscribe Yearly &rarr;
                </Link>
              </div>
            </div>

            {/* Plan 5: 3-Year Master */}
            <div className="col-12 col-md-6 col-lg-4 col-xl-3">
              <div className="app-card p-3 h-100 border text-center d-flex flex-column justify-content-between">
                <div>
                  <h6 className="fw-bold text-dark text-uppercase small mb-1">Franchise Master</h6>
                  <div className="fs-2 fw-extrabold text-dark my-1">
                    ₹5,500
                    <span className="fs-6 text-muted fw-normal">/3 years</span>
                  </div>
                  <div className="text-muted small mb-3">3-Year Long Term Franchise</div>
                  <hr />
                  <ul className="list-unstyled text-start small d-flex flex-column gap-2 mb-3">
                    <li><i className="bi bi-check-circle-fill text-success me-1"></i> 25 Staff Users</li>
                    <li><i className="bi bi-check-circle-fill text-success me-1"></i> 25 Branch Hubs</li>
                    <li><i className="bi bi-check-circle-fill text-success me-1"></i> Custom Branding / Logo</li>
                    <li><i className="bi bi-check-circle-fill text-success me-1"></i> Multi-City Linehaul Sync</li>
                    <li><i className="bi bi-check-circle-fill text-success me-1"></i> Priority Phone Support</li>
                  </ul>
                </div>
                <Link to="/register-agency" className="btn btn-outline-dark w-100 btn-sm">
                  Subscribe 3-Year
                </Link>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Feature Comparison Matrix Table */}
      <section className="py-5 bg-surface border-top border-bottom">
        <div className="container">
          <div className="text-center mb-5">
            <h3 className="brand-title fw-bold">Detailed Plan Feature Matrix</h3>
            <p className="text-muted small">Compare capabilities side-by-side across all subscription levels</p>
          </div>

          <div className="table-responsive bg-white rounded-3 border shadow-sm">
            <table className="table table-hover align-middle mb-0 text-center">
              <thead className="table-light">
                <tr>
                  <th className="text-start ps-3" style={{ width: '30%' }}>Platform Feature</th>
                  <th>Free</th>
                  <th>Monthly (₹200)</th>
                  <th>Quarterly (₹620)</th>
                  <th className="table-primary text-primary fw-bold">Yearly (₹2,124)</th>
                  <th>Franchise (₹5,500)</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td className="text-start ps-3 fw-semibold">User Logins Allowed</td>
                  <td>1</td>
                  <td>2</td>
                  <td>5</td>
                  <td className="fw-bold text-primary">10</td>
                  <td className="fw-bold">25+</td>
                </tr>
                <tr>
                  <td className="text-start ps-3 fw-semibold">Branch Counter Scopes</td>
                  <td>1</td>
                  <td>2</td>
                  <td>5</td>
                  <td className="fw-bold text-primary">10</td>
                  <td className="fw-bold">25+</td>
                </tr>
                <tr>
                  <td className="text-start ps-3 fw-semibold">Monthly Docket Bookings</td>
                  <td>500 / mo</td>
                  <td>Unlimited</td>
                  <td>Unlimited</td>
                  <td className="fw-bold text-primary">Unlimited</td>
                  <td className="fw-bold">Unlimited</td>
                </tr>
                <tr>
                  <td className="text-start ps-3 fw-semibold">Sticky Field Locks (Rapid Entry)</td>
                  <td><i className="bi bi-check2 text-success fs-5"></i></td>
                  <td><i className="bi bi-check2 text-success fs-5"></i></td>
                  <td><i className="bi bi-check2 text-success fs-5"></i></td>
                  <td><i className="bi bi-check2 text-success fs-5"></i></td>
                  <td><i className="bi bi-check2 text-success fs-5"></i></td>
                </tr>
                <tr>
                  <td className="text-start ps-3 fw-semibold">Vector 4x6" Thermal Labels</td>
                  <td><i className="bi bi-check2 text-success fs-5"></i></td>
                  <td><i className="bi bi-check2 text-success fs-5"></i></td>
                  <td><i className="bi bi-check2 text-success fs-5"></i></td>
                  <td><i className="bi bi-check2 text-success fs-5"></i></td>
                  <td><i className="bi bi-check2 text-success fs-5"></i></td>
                </tr>
                <tr>
                  <td className="text-start ps-3 fw-semibold">Linehaul Outscan Manifests</td>
                  <td><i className="bi bi-x text-muted fs-5"></i></td>
                  <td><i className="bi bi-x text-muted fs-5"></i></td>
                  <td><i className="bi bi-check2 text-success fs-5"></i></td>
                  <td><i className="bi bi-check2 text-success fs-5"></i></td>
                  <td><i className="bi bi-check2 text-success fs-5"></i></td>
                </tr>
                <tr>
                  <td className="text-start ps-3 fw-semibold">Delivery Run Sheets (DRS) & POD</td>
                  <td><i className="bi bi-x text-muted fs-5"></i></td>
                  <td><i className="bi bi-x text-muted fs-5"></i></td>
                  <td><i className="bi bi-check2 text-success fs-5"></i></td>
                  <td><i className="bi bi-check2 text-success fs-5"></i></td>
                  <td><i className="bi bi-check2 text-success fs-5"></i></td>
                </tr>
                <tr>
                  <td className="text-start ps-3 fw-semibold">Tally-Style Accounts (Lena/Dena)</td>
                  <td><i className="bi bi-x text-muted fs-5"></i></td>
                  <td><i className="bi bi-x text-muted fs-5"></i></td>
                  <td><i className="bi bi-x text-muted fs-5"></i></td>
                  <td><i className="bi bi-check2 text-success fs-5"></i></td>
                  <td><i className="bi bi-check2 text-success fs-5"></i></td>
                </tr>
                <tr>
                  <td className="text-start ps-3 fw-semibold">SAC 996812 Invoicing & GSTR-1</td>
                  <td><i className="bi bi-x text-muted fs-5"></i></td>
                  <td><i className="bi bi-x text-muted fs-5"></i></td>
                  <td><i className="bi bi-x text-muted fs-5"></i></td>
                  <td><i className="bi bi-check2 text-success fs-5"></i></td>
                  <td><i className="bi bi-check2 text-success fs-5"></i></td>
                </tr>
                <tr>
                  <td className="text-start ps-3 fw-semibold">Multi-Carrier Tracking APIs (6 National)</td>
                  <td><i className="bi bi-x text-muted fs-5"></i></td>
                  <td><i className="bi bi-x text-muted fs-5"></i></td>
                  <td><i className="bi bi-x text-muted fs-5"></i></td>
                  <td><i className="bi bi-check2 text-success fs-5"></i></td>
                  <td><i className="bi bi-check2 text-success fs-5"></i></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </section>

      {/* Frequently Asked Questions (FAQ) */}
      <section className="py-5 bg-white">
        <div className="container" style={{ maxWidth: '820px' }}>
          <div className="text-center mb-5">
            <h3 className="brand-title fw-bold">Frequently Asked Questions</h3>
            <p className="text-muted small">Everything you need to know about our courier ERP platform</p>
          </div>

          <div className="d-flex flex-column gap-3">
            <div className="app-card p-4 border">
              <h6 className="fw-bold text-dark mb-2">Can I upgrade my plan when I open new branches?</h6>
              <p className="text-muted small mb-0">
                Yes, upgrades take effect immediately. All your existing consignments, clients, and accounting ledgers 
                remain completely intact with zero downtime.
              </p>
            </div>

            <div className="app-card p-4 border">
              <h6 className="fw-bold text-dark mb-2">Are industrial 4x6" thermal label printers supported?</h6>
              <p className="text-muted small mb-0">
                Yes, Kesari ERP outputs pure vector SVG Code-128 barcodes calibrated precisely for standard 
                100mm × 150mm thermal rolls (TSC, Zebra, TVS, Citizen, and Dymo).
              </p>
            </div>

            <div className="app-card p-4 border">
              <h6 className="fw-bold text-dark mb-2">How does Indian GST SAC 996812 taxation work?</h6>
              <p className="text-muted small mb-0">
                The platform reads the first 2 digits of the Agency and Client GSTINs. If state codes match, 
                it applies CGST (9%) + SGST (9%). If inter-state, it applies IGST (18%) and generates ready-to-file GSTR-1 CSVs.
              </p>
            </div>

            <div className="app-card p-4 border">
              <h6 className="fw-bold text-dark mb-2">How fast is agency account approval?</h6>
              <p className="text-muted small mb-0">
                Agencies registered online can be auto-approved instantaneously if Super Admin auto-approval is toggled on, 
                or within 15 minutes following KYC document review.
              </p>
            </div>
          </div>
        </div>
      </section>

      <PublicFooter />
    </div>
  );
};

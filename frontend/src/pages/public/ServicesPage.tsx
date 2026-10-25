// frontend/src/pages/public/ServicesPage.tsx
import React from 'react';
import { Link } from 'react-router-dom';
import { PublicNavbar } from '../../components/public/PublicNavbar';
import { PublicFooter } from '../../components/public/PublicFooter';

export const ServicesPage: React.FC = () => {
  return (
    <div className="min-vh-100 d-flex flex-column bg-surface animate-enter">
      <PublicNavbar />

      {/* Hero Section */}
      <section className="py-5 bg-dark text-white position-relative overflow-hidden" style={{ background: 'linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%)' }}>
        <div className="container py-4 text-center text-lg-start position-relative" style={{ zIndex: 2 }}>
          <div className="row align-items-center g-4">
            <div className="col-12 col-lg-7">
              <span className="badge bg-primary text-white text-uppercase px-3 py-1 mb-2 font-monospace">
                End-to-End Capabilities
              </span>
              <h1 className="display-4 fw-extrabold mb-3">
                Logistics Services Built for <span className="text-warning">Speed & Precision</span>
              </h1>
              <p className="lead text-white-50 mb-4" style={{ maxWidth: '620px' }}>
                From same-day express air documents to multi-ton surface line-haul freight, 
                Kesari ERP powers the complete lifecycle of Indian domestic cargo.
              </p>
              <div className="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3">
                <Link to="/tariff-calculator" className="btn btn-warning px-4 fw-bold text-dark">
                  <i className="bi bi-calculator me-1"></i> Calculate Freight Cost
                </Link>
                <Link to="/plans" className="btn btn-outline-light px-4">
                  View SaaS Plans
                </Link>
              </div>
            </div>

            <div className="col-12 col-lg-5 text-center d-none d-lg-block">
              <img
                src="/courier_cutout.png"
                alt="Express Delivery Boy"
                className="img-fluid hero-cutout-img drop-shadow"
                style={{ maxHeight: '320px' }}
              />
            </div>
          </div>
        </div>
      </section>

      {/* 6 Core Services Grid */}
      <section className="py-5 bg-white">
        <div className="container">
          <div className="text-center mb-5" style={{ maxWidth: '720px', margin: '0 auto' }}>
            <span className="badge bg-primary-subtle text-primary text-uppercase px-3 py-1 mb-2">
              Our Service Spectrum
            </span>
            <h2 className="brand-title display-6 fw-bold">
              Comprehensive Logistics Solutions
            </h2>
            <p className="text-muted">
              Precision-tuned delivery modes with automated volumetric calculators, 
              real-time tracking, and GST SAC 996812 invoice generation.
            </p>
          </div>

          <div className="row g-4">
            {/* Service 1: Express Air Cargo */}
            <div className="col-12 col-md-6 col-lg-4">
              <div className="app-card p-4 h-100 border position-relative overflow-hidden">
                <div className="stat-icon-wrapper bg-primary-subtle text-primary mb-3">
                  <i className="bi bi-airplane-fill"></i>
                </div>
                <h5 className="fw-bold mb-2">1. Express Air Cargo</h5>
                <p className="text-muted small leading-relaxed mb-3">
                  High-priority time-critical transit for documents, passports, and lightweight samples. 
                  Operates under IATA standard volumetric formula <code>(L × W × H) / 5000</code>.
                </p>
                <ul className="list-unstyled small text-muted d-flex flex-column gap-1 mb-0 border-top pt-3">
                  <li><i className="bi bi-check2 text-primary me-1"></i> Same-day & Next-day Metro delivery</li>
                  <li><i className="bi bi-check2 text-primary me-1"></i> Airport bay clearance priority</li>
                  <li><i className="bi bi-check2 text-primary me-1"></i> Automated airline flight tagging</li>
                </ul>
              </div>
            </div>

            {/* Service 2: Fast Surface Line-haul */}
            <div className="col-12 col-md-6 col-lg-4">
              <div className="app-card p-4 h-100 border position-relative overflow-hidden">
                <div className="stat-icon-wrapper bg-warning-subtle text-warning mb-3">
                  <i className="bi bi-truck"></i>
                </div>
                <h5 className="fw-bold mb-2">2. Fast Surface Line-haul</h5>
                <p className="text-muted small leading-relaxed mb-3">
                  Economical road freight network covering state-to-state corridors. Supported by 
                  divisors 4000 (Standard) and 4500 (Fast Surface) with dedicated container vehicles.
                </p>
                <ul className="list-unstyled small text-muted d-flex flex-column gap-1 mb-0 border-top pt-3">
                  <li><i className="bi bi-check2 text-warning me-1"></i> Heavy commercial shipments & boxes</li>
                  <li><i className="bi bi-check2 text-warning me-1"></i> Line-haul Outscan Manifest creation</li>
                  <li><i className="bi bi-check2 text-warning me-1"></i> Scheduled inter-hub vehicle tracking</li>
                </ul>
              </div>
            </div>

            {/* Service 3: Corporate B2B Contract Logistics */}
            <div className="col-12 col-md-6 col-lg-4">
              <div className="app-card p-4 h-100 border position-relative overflow-hidden">
                <div className="stat-icon-wrapper bg-success-subtle text-success mb-3">
                  <i className="bi bi-buildings"></i>
                </div>
                <h5 className="fw-bold mb-2">3. Corporate B2B Contracts</h5>
                <p className="text-muted small leading-relaxed mb-3">
                  Customized tariff cards for banks, law firms, and manufacturing enterprises. 
                  Includes monthly billing credit cycles, fuel surcharge indexing, and stationary fees.
                </p>
                <ul className="list-unstyled small text-muted d-flex flex-column gap-1 mb-0 border-top pt-3">
                  <li><i className="bi bi-check2 text-success me-1"></i> Negotiated slab rates (100g to 50kg)</li>
                  <li><i className="bi bi-check2 text-success me-1"></i> Date-range consolidated GST invoicing</li>
                  <li><i className="bi bi-check2 text-success me-1"></i> Post-billing line-item editability</li>
                </ul>
              </div>
            </div>

            {/* Service 4: E-Commerce COD & NDR Desk */}
            <div className="col-12 col-md-6 col-lg-4">
              <div className="app-card p-4 h-100 border position-relative overflow-hidden">
                <div className="stat-icon-wrapper bg-danger-subtle text-danger mb-3">
                  <i className="bi bi-cash-stack"></i>
                </div>
                <h5 className="fw-bold mb-2">4. E-Commerce COD & NDR</h5>
                <p className="text-muted small leading-relaxed mb-3">
                  Cash-on-Delivery handling with automated remittance ledgers and structured 
                  Non-Delivery Reason (NDR) tracking to reduce Return-To-Origin (RTO) rates.
                </p>
                <ul className="list-unstyled small text-muted d-flex flex-column gap-1 mb-0 border-top pt-3">
                  <li><i className="bi bi-check2 text-danger me-1"></i> 3-attempt automated re-dispatch workflow</li>
                  <li><i className="bi bi-check2 text-danger me-1"></i> Door locked & address exception tagging</li>
                  <li><i className="bi bi-check2 text-danger me-1"></i> Touchscreen signature proof capture</li>
                </ul>
              </div>
            </div>

            {/* Service 5: Secure Document Transit */}
            <div className="col-12 col-md-6 col-lg-4">
              <div className="app-card p-4 h-100 border position-relative overflow-hidden">
                <div className="stat-icon-wrapper bg-info-subtle text-info mb-3">
                  <i className="bi bi-file-lock2"></i>
                </div>
                <h5 className="fw-bold mb-2">5. High-Value Document Transit</h5>
                <p className="text-muted small leading-relaxed mb-3">
                  Specialized protocol for confidential tenders, court documents, cheque books, 
                  and examination papers with tamper-evident barcoded seals.
                </p>
                <ul className="list-unstyled small text-muted d-flex flex-column gap-1 mb-0 border-top pt-3">
                  <li><i className="bi bi-check2 text-info me-1"></i> Tamper-evident barcode bag locking</li>
                  <li><i className="bi bi-check2 text-info me-1"></i> End-to-end chain of custody audit trail</li>
                  <li><i className="bi bi-check2 text-info me-1"></i> Owner's Risk vs Carrier Risk declaration</li>
                </ul>
              </div>
            </div>

            {/* Service 6: Thermal Label & Hub Scanning */}
            <div className="col-12 col-md-6 col-lg-4">
              <div className="app-card p-4 h-100 border position-relative overflow-hidden">
                <div className="stat-icon-wrapper bg-secondary-subtle text-dark mb-3">
                  <i className="bi bi-upc"></i>
                </div>
                <h5 className="fw-bold mb-2">6. Thermal Labeling & Inscan</h5>
                <p className="text-muted small leading-relaxed mb-3">
                  Pure vector Code-128 barcode rendering calibrated specifically for industrial 
                  100mm × 150mm (4x6") thermal label roll printers.
                </p>
                <ul className="list-unstyled small text-muted d-flex flex-column gap-1 mb-0 border-top pt-3">
                  <li><i className="bi bi-check2 text-dark me-1"></i> High-speed incoming bag inscan table</li>
                  <li><i className="bi bi-check2 text-dark me-1"></i> Embedded QR tracking code for consignees</li>
                  <li><i className="bi bi-check2 text-dark me-1"></i> Zero-blur crisp vector SVG output</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Volumetric Calculation Explanation */}
      <section className="py-5 bg-surface border-top border-bottom">
        <div className="container">
          <div className="row align-items-center g-5">
            <div className="col-12 col-lg-6">
              <span className="badge bg-warning text-dark text-uppercase px-3 py-1 mb-2 font-monospace">
                Domain Standards
              </span>
              <h2 className="brand-title display-6 fw-bold mb-3">
                Transparent Volumetric Divisors
              </h2>
              <p className="text-muted leading-relaxed mb-4">
                Volumetric weight accounts for parcel density. Kesari ERP embeds automatic 
                multi-box repeater calculation calibrated directly from Indian logistics rules:
              </p>

              <div className="table-responsive bg-white rounded-3 border shadow-xs mb-3">
                <table className="table mb-0">
                  <thead className="table-light">
                    <tr>
                      <th>Transit Mode</th>
                      <th>Divisor</th>
                      <th>Formula</th>
                      <th>Application</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td className="fw-bold text-primary">Express Air</td>
                      <td className="font-monospace fw-bold">5000</td>
                      <td className="small font-monospace">(L × W × H) / 5000</td>
                      <td className="small text-muted">Airlines Belly Cargo</td>
                    </tr>
                    <tr>
                      <td className="fw-bold text-warning">Fast Surface</td>
                      <td className="font-monospace fw-bold">4500</td>
                      <td className="small font-monospace">(L × W × H) / 4500</td>
                      <td className="small text-muted">Express Highway Vans</td>
                    </tr>
                    <tr>
                      <td className="fw-bold text-secondary">Standard Surface</td>
                      <td className="font-monospace fw-bold">4000</td>
                      <td className="small font-monospace">(L × W × H) / 4000</td>
                      <td className="small text-muted">Heavy Cargo Trucks</td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <div className="small text-muted">
                * Chargeable weight is always the higher of Actual Dead Weight vs. Calculated Volumetric Weight.
              </div>
            </div>

            <div className="col-12 col-lg-6">
              <div className="app-card p-4 border bg-white shadow-sm">
                <h5 className="fw-bold mb-3 text-dark">
                  <i className="bi bi-calculator-fill text-primary me-2"></i> Quick Rate Estimator
                </h5>
                <p className="text-muted small mb-4">
                  Check estimated domestic courier charges between any two Indian pincodes with instant mode breakdown.
                </p>
                <div className="d-flex flex-column gap-3">
                  <Link to="/tariff-calculator" className="btn btn-primary fw-bold py-2 shadow-sm">
                    Open Interactive Rate Calculator &rarr;
                  </Link>
                  <Link to="/track" className="btn btn-outline-secondary py-2">
                    Track Existing Consignment AWB
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <PublicFooter />
    </div>
  );
};

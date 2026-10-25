// frontend/src/components/print/ThermalLabel.tsx
import React from 'react';
import { Consignment } from '../../types';

interface ThermalLabelProps {
  consignment: Consignment;
  barcodeSvg?: string;
  trackingUrl?: string;
}

export const ThermalLabel: React.FC<ThermalLabelProps> = ({
  consignment,
  barcodeSvg,
  trackingUrl,
}) => {
  const destCode = consignment.destination?.code || 'HUB';
  const destName = consignment.destination?.name || 'DESTINATION';
  const pincode = consignment.destination?.pincode || '000000';
  const agencyName = consignment.branch?.branch_name || 'KESARI EXPRESS LOGISTICS';

  return (
    <div className="thermal-label-container">
      {/* Header with Agency Branding & Destination Hub Code */}
      <div className="border-bottom border-2 border-dark pb-1 mb-1 d-flex justify-content-between align-items-center">
        <div>
          <div className="fw-bold small text-uppercase text-truncate" style={{ maxWidth: '65mm' }}>
            {agencyName}
          </div>
          <div style={{ fontSize: '7pt' }} className="text-muted">
            STANDARD 4X6" THERMAL SHIPPING DOCKET
          </div>
        </div>
        <div className="text-end">
          <div className="border border-2 border-dark px-2 py-0 fw-bold fs-4 bg-light">
            {destCode}
          </div>
        </div>
      </div>

      {/* Barcode Section */}
      <div className="text-center py-1 border-bottom border-2 border-dark">
        {barcodeSvg ? (
          <div
            dangerouslySetInnerHTML={{ __html: barcodeSvg }}
            className="d-flex justify-content-center"
          />
        ) : (
          <div className="py-2 fw-bold font-monospace fs-5">
            *{consignment.consignment_no}*
          </div>
        )}
      </div>

      {/* Pincode & Routing Box */}
      <div className="row g-0 border-bottom border-2 border-dark py-1 text-center align-items-center">
        <div className="col-7 border-end border-2 border-dark">
          <div style={{ fontSize: '7.5pt' }} className="fw-bold text-uppercase">
            DESTINATION PINCODE
          </div>
          <div className="fs-3 fw-bolder letter-spacing-1">{pincode}</div>
        </div>
        <div className="col-5">
          <div style={{ fontSize: '7.5pt' }} className="fw-bold text-uppercase">
            SERVICE
          </div>
          <div className="fw-bold fs-6">
            {consignment.send_by} {consignment.mode}
          </div>
        </div>
      </div>

      {/* Consignee (To) Details */}
      <div className="border-bottom border-2 border-dark py-1" style={{ fontSize: '8.5pt' }}>
        <div className="fw-bold text-uppercase d-flex justify-content-between">
          <span>SHIP TO / RECIPIENT:</span>
          <span className="fw-bold">TEL: {consignment.recipient_phone}</span>
        </div>
        <div className="fw-bold fs-6 text-uppercase">{consignment.recipient_name}</div>
        <div className="text-break lh-sm" style={{ minHeight: '12mm' }}>
          {consignment.recipient_address}
        </div>
        <div className="fw-bold mt-1">
          {destName}, PIN: {pincode}
        </div>
      </div>

      {/* Consignor (From) Details */}
      <div className="border-bottom border-2 border-dark py-1" style={{ fontSize: '7.5pt' }}>
        <div className="fw-bold text-uppercase d-flex justify-content-between">
          <span>SENDER / SHIPPER:</span>
          <span>TEL: {consignment.sender_phone}</span>
        </div>
        <div className="fw-semibold text-truncate">{consignment.sender_name}</div>
        <div className="text-muted">
          Client: {consignment.client?.name || 'Walk-in Counter'}
        </div>
      </div>

      {/* Shipment Meta Footer with QR and Weights */}
      <div className="row g-0 py-1 align-items-center mt-auto" style={{ fontSize: '7.5pt' }}>
        <div className="col-8">
          <div className="d-flex justify-content-between border-bottom pb-1 mb-1">
            <span>PIECES: <strong>{consignment.pieces}</strong></span>
            <span>ACTUAL WT: <strong>{consignment.dead_weight} kg</strong></span>
            <span>BILL WT: <strong>{consignment.billable_weight} kg</strong></span>
          </div>
          <div className="d-flex justify-content-between">
            <span>DATE: {new Date(consignment.booking_date).toLocaleDateString('en-GB')}</span>
            <span>AWB: {consignment.consignment_no}</span>
          </div>
          <div style={{ fontSize: '6.5pt' }} className="text-muted mt-1">
            Scan QR to track milestone delivery dossier
          </div>
        </div>
        <div className="col-4 text-center">
          <img
            src={`https://api.qrserver.com/v1/create-qr-code/?size=70x70&data=${encodeURIComponent(
              trackingUrl || `https://kesaricourier.vercel.app/track/${consignment.consignment_no}`
            )}`}
            alt="Tracking QR"
            width="65"
            height="65"
            className="border p-1 bg-white"
          />
        </div>
      </div>
    </div>
  );
};

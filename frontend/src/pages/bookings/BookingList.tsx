// frontend/src/pages/bookings/BookingList.tsx
import React, { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import api from '../../services/api';
import { Consignment } from '../../types';

export const BookingList: React.FC = () => {
  const [bookings, setBookings] = useState<Consignment[]>([]);
  const [loading, setLoading] = useState(true);
  const [search, setSearch] = useState('');
  const [statusFilter, setStatusFilter] = useState('all');
  const [selectedBooking, setSelectedBooking] = useState<Consignment | null>(null);
  const [showStatusModal, setShowStatusModal] = useState(false);
  const [newStatus, setNewStatus] = useState('in_transit');

  const fetchBookings = async () => {
    try {
      setLoading(true);
      const res = await api.get('/bookings', {
        params: { search, status: statusFilter },
      });
      if (res.data.success) {
        setBookings(res.data.data);
      }
    } catch (e) {
      console.error(e);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchBookings();
  }, [statusFilter]);

  const handleSearch = (e: React.FormEvent) => {
    e.preventDefault();
    fetchBookings();
  };

  const handleStatusUpdate = async () => {
    if (!selectedBooking) return;
    try {
      await api.patch(`/bookings/${selectedBooking.consignment_no}/status`, {
        status: newStatus,
      });
      setShowStatusModal(false);
      setSelectedBooking(null);
      fetchBookings();
    } catch (err: any) {
      alert(err.response?.data?.error?.message || 'Error updating status');
    }
  };

  return (
    <div>
      <div className="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h2 className="brand-title mb-1">Consignment Register</h2>
          <p className="text-muted small mb-0">
            Search shipments, inspect volumetric computations, print thermal labels, and advance consignment lifecycle
          </p>
        </div>
        <Link to="/bookings/new" className="btn btn-primary btn-sm">
          <i className="bi bi-plus-circle me-1"></i> New Booking
        </Link>
      </div>

      {/* Filter and Search Bar */}
      <div className="app-card p-3 mb-4">
        <form onSubmit={handleSearch} className="row g-2 align-items-center">
          <div className="col-12 col-md-5">
            <div className="input-group input-group-sm">
              <span className="input-group-text bg-light border-end-0">
                <i className="bi bi-search text-muted"></i>
              </span>
              <input
                type="text"
                className="form-control border-start-0"
                placeholder="Search AWB, recipient name, phone..."
                value={search}
                onChange={(e) => setSearch(e.target.value)}
              />
            </div>
          </div>

          <div className="col-6 col-md-3">
            <select
              className="form-select form-select-sm"
              value={statusFilter}
              onChange={(e) => setStatusFilter(e.target.value)}
            >
              <option value="all">All Statuses</option>
              <option value="booked">Booked</option>
              <option value="in_manifest">In Manifest</option>
              <option value="in_transit">In Transit</option>
              <option value="out_for_delivery">Out for Delivery</option>
              <option value="delivered">Delivered (POD)</option>
              <option value="cancelled">Cancelled</option>
            </select>
          </div>

          <div className="col-6 col-md-2">
            <button type="submit" className="btn btn-sm btn-primary w-100">
              Filter
            </button>
          </div>

          <div className="col-12 col-md-2 text-md-end text-muted small">
            Found: <strong>{bookings.length}</strong>
          </div>
        </form>
      </div>

      {/* Bookings Table */}
      <div className="app-card p-3">
        {loading ? (
          <div className="text-center py-5">
            <div className="spinner-border text-primary spinner-border-sm me-2"></div>
            Loading consignment register...
          </div>
        ) : (
          <div className="table-responsive">
            <table className="table table-hover align-middle mb-0">
              <thead className="table-light">
                <tr className="small text-muted text-uppercase">
                  <th>AWB Number</th>
                  <th>Date</th>
                  <th>Destination Hub</th>
                  <th>Client / Sender</th>
                  <th>Recipient</th>
                  <th>Weight (Billable)</th>
                  <th>Amount</th>
                  <th>Status</th>
                  <th className="text-end">Actions</th>
                </tr>
              </thead>
              <tbody>
                {bookings.length === 0 ? (
                  <tr>
                    <td colSpan={9} className="text-center py-4 text-muted">
                      No consignments found.
                    </td>
                  </tr>
                ) : (
                  bookings.map((b) => (
                    <tr key={b.id}>
                      <td>
                        <span className="fw-bold font-monospace text-primary">{b.consignment_no}</span>
                      </td>
                      <td className="small text-muted">
                        {new Date(b.booking_date).toLocaleDateString()}
                      </td>
                      <td>
                        <span className="badge bg-light text-dark border font-monospace me-1">
                          {b.destination?.code}
                        </span>{' '}
                        <span className="small">{b.destination?.name}</span>
                      </td>
                      <td className="small">{b.client?.name || 'Walk-in'}</td>
                      <td>
                        <div className="fw-semibold small">{b.recipient_name}</div>
                        <div className="small text-muted">{b.recipient_phone}</div>
                      </td>
                      <td>
                        <span className="fw-bold">{b.billable_weight} kg</span>{' '}
                        <span className="badge bg-light text-secondary border small">{b.send_by}</span>
                      </td>
                      <td className="fw-bold">₹{Number(b.total_amount).toFixed(2)}</td>
                      <td>
                        <span className={`badge-status ${b.status}`}>{b.status}</span>
                      </td>
                      <td className="text-end">
                        <div className="btn-group btn-group-sm">
                          <Link
                            to={`/operations/print?awb=${b.consignment_no}`}
                            className="btn btn-outline-secondary"
                            title="Print 4x6 Label"
                          >
                            <i className="bi bi-printer"></i>
                          </Link>
                          <button
                            className="btn btn-outline-primary"
                            onClick={() => {
                              setSelectedBooking(b);
                              setNewStatus(b.status);
                              setShowStatusModal(true);
                            }}
                            title="Update Status"
                          >
                            <i className="bi bi-arrow-repeat"></i>
                          </button>
                        </div>
                      </td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
        )}
      </div>

      {/* Update Status Modal */}
      {showStatusModal && selectedBooking && (
        <div className="modal show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
          <div className="modal-dialog modal-dialog-centered">
            <div className="modal-content">
              <div className="modal-header">
                <h5 className="modal-title">Update Status: {selectedBooking.consignment_no}</h5>
                <button
                  type="button"
                  className="btn-close"
                  onClick={() => setShowStatusModal(false)}
                ></button>
              </div>
              <div className="modal-body">
                <div className="mb-3">
                  <label className="form-label small fw-semibold">Shipment Lifecycle Status</label>
                  <select
                    className="form-select"
                    value={newStatus}
                    onChange={(e) => setNewStatus(e.target.value)}
                  >
                    <option value="booked">Booked (At Counter)</option>
                    <option value="in_manifest">In Manifest (Hub Bagged)</option>
                    <option value="in_transit">In Transit (Line-haul Vehicle)</option>
                    <option value="out_for_delivery">Out for Delivery (Assigned to Field)</option>
                    <option value="delivered">Delivered (Handed to Consignee)</option>
                    <option value="cancelled">Cancelled</option>
                  </select>
                </div>
              </div>
              <div className="modal-footer">
                <button
                  type="button"
                  className="btn btn-light"
                  onClick={() => setShowStatusModal(false)}
                >
                  Cancel
                </button>
                <button type="button" className="btn btn-primary" onClick={handleStatusUpdate}>
                  Update Status
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

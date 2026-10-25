// frontend/src/pages/superadmin/AuditLogsPage.tsx
import React, { useState, useEffect } from 'react';
import api from '../../services/api';

interface AuditLog {
  id: number;
  action: string;
  details_json?: string;
  ip_address?: string;
  created_at: string;
  tenant?: {
    id: number;
    name: string;
    agency_code?: string;
  };
  user?: {
    id: number;
    name: string;
    email: string;
    role: string;
  };
}

export const AuditLogsPage: React.FC = () => {
  const [logs, setLogs] = useState<AuditLog[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const [search, setSearch] = useState<string>('');
  const [page, setPage] = useState<number>(1);
  const [totalPages, setTotalPages] = useState<number>(1);
  const [totalCount, setTotalCount] = useState<number>(0);
  const [selectedLog, setSelectedLog] = useState<AuditLog | null>(null);

  const fetchLogs = async () => {
    try {
      setLoading(true);
      const res = await api.get('/superadmin/audit-logs', {
        params: { page, limit: 25, search },
      });
      if (res.data.success) {
        setLogs(res.data.data);
        setTotalPages(res.data.meta.totalPages);
        setTotalCount(res.data.meta.total);
      }
    } catch (err) {
      console.error('Error fetching audit logs', err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchLogs();
  }, [page]);

  const handleSearchSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    setPage(1);
    fetchLogs();
  };

  return (
    <div className="container-fluid p-0">
      {/* Title */}
      <div className="d-flex justify-content-between align-items-center mb-4">
        <div>
          <h3 className="fw-bold text-dark mb-1">System Audit & Compliance Logs</h3>
          <p className="text-muted small mb-0">
            Immutable tracking of administrative actions, status updates, logins, and agency mutations.
          </p>
        </div>
        <button className="btn btn-outline-secondary btn-sm" onClick={fetchLogs}>
          <i className="bi bi-arrow-clockwise me-1"></i> Refresh
        </button>
      </div>

      {/* Search Toolbar */}
      <div className="card shadow-sm border-0 mb-4">
        <div className="card-body p-3">
          <form onSubmit={handleSearchSubmit} className="row g-2 align-items-center">
            <div className="col-md-6">
              <div className="input-group">
                <span className="input-group-text bg-white text-muted">
                  <i className="bi bi-search"></i>
                </span>
                <input
                  type="text"
                  className="form-control"
                  placeholder="Search by action keyword or details..."
                  value={search}
                  onChange={(e) => setSearch(e.target.value)}
                />
                <button type="submit" className="btn btn-primary">
                  Filter Logs
                </button>
              </div>
            </div>
            <div className="col-md-6 text-md-end text-muted small">
              Total Records Logged: <strong>{totalCount}</strong>
            </div>
          </form>
        </div>
      </div>

      {/* Logs Table */}
      <div className="card shadow-sm border-0">
        <div className="table-responsive">
          <table className="table table-hover align-middle mb-0">
            <thead className="table-light text-muted small text-uppercase">
              <tr>
                <th>Timestamp</th>
                <th>Action Code</th>
                <th>Initiator</th>
                <th>Agency</th>
                <th>IP Address</th>
                <th className="text-end">Payload</th>
              </tr>
            </thead>
            <tbody>
              {loading ? (
                <tr>
                  <td colSpan={6} className="text-center py-5">
                    <div className="spinner-border text-primary spinner-border-sm" role="status"></div>
                    <div className="small text-muted mt-2">Loading audit trail...</div>
                  </td>
                </tr>
              ) : logs.length === 0 ? (
                <tr>
                  <td colSpan={6} className="text-center py-5 text-muted">
                    No audit records found.
                  </td>
                </tr>
              ) : (
                logs.map((log) => (
                  <tr key={log.id}>
                    <td className="small text-muted font-monospace">
                      {new Date(log.created_at).toLocaleString('en-IN')}
                    </td>
                    <td>
                      <span className="badge bg-secondary font-monospace text-uppercase">
                        {log.action}
                      </span>
                    </td>
                    <td>
                      {log.user ? (
                        <div>
                          <span className="fw-semibold text-dark">{log.user.name}</span>
                          <span className="badge bg-light text-dark border ms-1" style={{ fontSize: '0.68rem' }}>
                            {log.user.role}
                          </span>
                          <div className="small text-muted">{log.user.email}</div>
                        </div>
                      ) : (
                        <span className="text-muted small">System Automation</span>
                      )}
                    </td>
                    <td>
                      {log.tenant ? (
                        <div>
                          <span className="fw-semibold text-dark">{log.tenant.name}</span>
                          <span className="text-muted ms-1 small">
                            ({log.tenant.agency_code || 'Pending'})
                          </span>
                        </div>
                      ) : (
                        <span className="text-muted small">Platform Global</span>
                      )}
                    </td>
                    <td className="font-monospace small text-muted">
                      {log.ip_address || '127.0.0.1'}
                    </td>
                    <td className="text-end">
                      {log.details_json ? (
                        <button
                          className="btn btn-sm btn-outline-secondary"
                          onClick={() => setSelectedLog(log)}
                        >
                          <i className="bi bi-code-slash me-1"></i> View Payload
                        </button>
                      ) : (
                        <span className="text-muted small">—</span>
                      )}
                    </td>
                  </tr>
                ))
              )}
            </tbody>
          </table>
        </div>

        {/* Pagination */}
        {totalPages > 1 && (
          <div className="card-footer bg-white d-flex justify-content-between align-items-center py-2">
            <span className="small text-muted">
              Page {page} of {totalPages}
            </span>
            <div className="btn-group btn-group-sm">
              <button
                className="btn btn-outline-secondary"
                disabled={page <= 1}
                onClick={() => setPage((p) => p - 1)}
              >
                Previous
              </button>
              <button
                className="btn btn-outline-secondary"
                disabled={page >= totalPages}
                onClick={() => setPage((p) => p + 1)}
              >
                Next
              </button>
            </div>
          </div>
        )}
      </div>

      {/* JSON Payload Modal */}
      {selectedLog && (
        <div className="modal fade show d-block" style={{ backgroundColor: 'rgba(0,0,0,0.5)' }}>
          <div className="modal-dialog modal-dialog-centered">
            <div className="modal-content border-0 shadow-lg">
              <div className="modal-header bg-light">
                <h5 className="modal-title fw-bold">
                  Audit Payload: {selectedLog.action}
                </h5>
                <button
                  type="button"
                  className="btn-close"
                  onClick={() => setSelectedLog(null)}
                ></button>
              </div>
              <div className="modal-body">
                <div className="small text-muted mb-2">
                  Initiated by: <strong>{selectedLog.user?.email || 'System'}</strong> at{' '}
                  {new Date(selectedLog.created_at).toLocaleString('en-IN')}
                </div>
                <pre className="bg-dark text-success p-3 rounded small font-monospace mb-0" style={{ maxHeight: '300px', overflow: 'auto' }}>
                  {JSON.stringify(JSON.parse(selectedLog.details_json || '{}'), null, 2)}
                </pre>
              </div>
              <div className="modal-footer bg-light">
                <button
                  type="button"
                  className="btn btn-secondary btn-sm"
                  onClick={() => setSelectedLog(null)}
                >
                  Close
                </button>
              </div>
            </div>
          </div>
        </div>
      )}
    </div>
  );
};

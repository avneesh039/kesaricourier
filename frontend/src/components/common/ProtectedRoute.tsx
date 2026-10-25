// frontend/src/components/common/ProtectedRoute.tsx
import React from 'react';
import { Navigate, useLocation } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import { Role } from '../../types';

interface ProtectedRouteProps {
  children: React.ReactElement;
  allowedRoles?: Role[];
}

export const ProtectedRoute: React.FC<ProtectedRouteProps> = ({ children, allowedRoles }) => {
  const { user, isAuthenticated, isLoading } = useAuth();
  const location = useLocation();

  if (isLoading) {
    return (
      <div className="d-flex justify-content-center align-items-center vh-100 bg-light">
        <div className="spinner-border text-primary" role="status">
          <span className="visually-hidden">Loading...</span>
        </div>
      </div>
    );
  }

  if (!isAuthenticated || !user) {
    return <Navigate to="/login" state={{ from: location }} replace />;
  }

  if (allowedRoles && !allowedRoles.includes(user.role) && user.role !== 'superadmin') {
    return (
      <div className="container py-5 text-center">
        <div className="app-card p-5 mx-auto" style={{ maxWidth: '500px' }}>
          <i className="bi bi-shield-lock-fill text-danger display-1 mb-3"></i>
          <h3>Access Denied</h3>
          <p className="text-muted">
            You do not have permission to access this module. Your current role is{' '}
            <strong>{user.role}</strong>.
          </p>
          <a href="/" className="btn btn-primary mt-2">
            Back to Dashboard
          </a>
        </div>
      </div>
    );
  }

  return children;
};

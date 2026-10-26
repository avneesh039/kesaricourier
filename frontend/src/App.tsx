// frontend/src/App.tsx
import React from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';
import { useAuth } from './context/AuthContext';
import { ProtectedRoute } from './components/common/ProtectedRoute';
import { AppLayout } from './components/layout/AppLayout';

// Auth & Public Pages
import { Login } from './pages/auth/Login';
import { RegisterAgency } from './pages/auth/RegisterAgency';
import { PublicTracking } from './pages/public/PublicTracking';
import { PublicTariffCalculator } from './pages/public/PublicTariffCalculator';
import { HomePage } from './pages/public/HomePage';
import { AboutPage } from './pages/public/AboutPage';
import { ServicesPage } from './pages/public/ServicesPage';
import { SubscriptionPlansPage } from './pages/public/SubscriptionPlansPage';

// Super Admin Pages
import { SuperAdminDashboard } from './pages/superadmin/SuperAdminDashboard';
import { AgenciesList } from './pages/superadmin/AgenciesList';
import { PlansManager } from './pages/superadmin/PlansManager';
import { DestinationMaster } from './pages/superadmin/DestinationMaster';
import { SuperAdminTickets } from './pages/superadmin/SuperAdminTickets';
import { AuditLogsPage } from './pages/superadmin/AuditLogsPage';
import { CourierApiSettings } from './pages/superadmin/CourierApiSettings';

// Agency & Operations Pages
import { AgencyDashboard } from './pages/agency/AgencyDashboard';
import { BranchesPage } from './pages/agency/BranchesPage';
import { StaffPage } from './pages/agency/StaffPage';
import { ClientsPage } from './pages/agency/ClientsPage';
import { TariffsPage } from './pages/agency/TariffsPage';
import { BookingCounter } from './pages/bookings/BookingCounter';
import { BookingList } from './pages/bookings/BookingList';
import { ThermalPrintHub } from './pages/operations/ThermalPrintHub';
import { ManifestsPage } from './pages/operations/ManifestsPage';
import { RunsheetsPage } from './pages/operations/RunsheetsPage';
import { InvoicesPage } from './pages/billing/InvoicesPage';
import { AgencyTicketsPage } from './pages/agency/AgencyTicketsPage';
import { AgencySettingsPage } from './pages/agency/AgencySettingsPage';
import { QuotationsPage } from './pages/agency/QuotationsPage';
import { AccountsPage } from './pages/agency/AccountsPage';
import { InscanPage } from './pages/operations/InscanPage';
import { PodEntryPage } from './pages/operations/PodEntryPage';
import { MobileRunsheet } from './pages/delivery/MobileRunsheet';

// Customer Portal
import { CustomerPortal } from './pages/customer/CustomerPortal';

const RootRedirect: React.FC = () => {
  const { user, isAuthenticated, isLoading } = useAuth();

  if (isLoading) {
    return (
      <div className="d-flex justify-content-center align-items-center vh-100 bg-light">
        <div className="spinner-border text-primary" role="status"></div>
      </div>
    );
  }

  if (!isAuthenticated || !user) {
    return <Navigate to="/login" replace />;
  }

  if (user.role === 'superadmin') {
    return <Navigate to="/superadmin" replace />;
  }

  if (user.role === 'delivery_personnel') {
    return <Navigate to="/delivery" replace />;
  }

  if (user.role === 'customer') {
    return <Navigate to="/customer" replace />;
  }

  return <Navigate to="/agency" replace />;
};

export const App: React.FC = () => {
  return (
    <Routes>
      {/* Public Pages */}
      <Route path="/" element={<HomePage />} />
      <Route path="/about" element={<AboutPage />} />
      <Route path="/services" element={<ServicesPage />} />
      <Route path="/plans" element={<SubscriptionPlansPage />} />
      <Route path="/pricing" element={<SubscriptionPlansPage />} />
      <Route path="/login" element={<Login />} />
      <Route path="/register-agency" element={<RegisterAgency />} />
      <Route path="/track" element={<PublicTracking />} />
      <Route path="/track/:awb" element={<PublicTracking />} />
      <Route path="/tariff-calculator" element={<PublicTariffCalculator />} />

      {/* Protected App Layout */}
      <Route
        element={
          <ProtectedRoute>
            <AppLayout />
          </ProtectedRoute>
        }
      >
        <Route path="/app" element={<RootRedirect />} />
        <Route path="/dashboard" element={<RootRedirect />} />

        {/* Super Admin Module */}
        <Route
          path="superadmin"
          element={
            <ProtectedRoute allowedRoles={['superadmin']}>
              <SuperAdminDashboard />
            </ProtectedRoute>
          }
        />
        <Route
          path="superadmin/agencies"
          element={
            <ProtectedRoute allowedRoles={['superadmin']}>
              <AgenciesList />
            </ProtectedRoute>
          }
        />
        <Route
          path="superadmin/plans"
          element={
            <ProtectedRoute allowedRoles={['superadmin']}>
              <PlansManager />
            </ProtectedRoute>
          }
        />
        <Route
          path="superadmin/destinations"
          element={
            <ProtectedRoute allowedRoles={['superadmin']}>
              <DestinationMaster />
            </ProtectedRoute>
          }
        />
        <Route
          path="superadmin/tickets"
          element={
            <ProtectedRoute allowedRoles={['superadmin']}>
              <SuperAdminTickets />
            </ProtectedRoute>
          }
        />
        <Route
          path="superadmin/audit-logs"
          element={
            <ProtectedRoute allowedRoles={['superadmin']}>
              <AuditLogsPage />
            </ProtectedRoute>
          }
        />
        <Route
          path="superadmin/courier-apis"
          element={
            <ProtectedRoute allowedRoles={['superadmin']}>
              <CourierApiSettings />
            </ProtectedRoute>
          }
        />
        <Route
          path="superadmin/api-settings"
          element={
            <ProtectedRoute allowedRoles={['superadmin']}>
              <CourierApiSettings />
            </ProtectedRoute>
          }
        />

        {/* Agency & Operations Module */}
        <Route
          path="agency"
          element={
            <ProtectedRoute
              allowedRoles={[
                'agency_admin',
                'branch_manager',
                'operations_staff',
                'accounts_staff',
              ]}
            >
              <AgencyDashboard />
            </ProtectedRoute>
          }
        />
        <Route
          path="bookings"
          element={
            <ProtectedRoute
              allowedRoles={[
                'agency_admin',
                'branch_manager',
                'operations_staff',
                'accounts_staff',
              ]}
            >
              <BookingList />
            </ProtectedRoute>
          }
        />
        <Route
          path="bookings/new"
          element={
            <ProtectedRoute
              allowedRoles={['agency_admin', 'branch_manager', 'operations_staff']}
            >
              <BookingCounter />
            </ProtectedRoute>
          }
        />
        <Route
          path="operations/print"
          element={
            <ProtectedRoute
              allowedRoles={[
                'agency_admin',
                'branch_manager',
                'operations_staff',
                'accounts_staff',
              ]}
            >
              <ThermalPrintHub />
            </ProtectedRoute>
          }
        />
        <Route
          path="operations/inscan"
          element={
            <ProtectedRoute
              allowedRoles={['agency_admin', 'branch_manager', 'operations_staff']}
            >
              <InscanPage />
            </ProtectedRoute>
          }
        />
        <Route
          path="operations/pod-entry"
          element={
            <ProtectedRoute
              allowedRoles={['agency_admin', 'branch_manager', 'operations_staff']}
            >
              <PodEntryPage />
            </ProtectedRoute>
          }
        />
        <Route
          path="operations/manifests"
          element={
            <ProtectedRoute
              allowedRoles={['agency_admin', 'branch_manager', 'operations_staff']}
            >
              <ManifestsPage />
            </ProtectedRoute>
          }
        />
        <Route
          path="operations/runsheets"
          element={
            <ProtectedRoute
              allowedRoles={['agency_admin', 'branch_manager', 'operations_staff']}
            >
              <RunsheetsPage />
            </ProtectedRoute>
          }
        />
        <Route
          path="quotations"
          element={
            <ProtectedRoute
              allowedRoles={[
                'agency_admin',
                'branch_manager',
                'operations_staff',
                'accounts_staff',
              ]}
            >
              <QuotationsPage />
            </ProtectedRoute>
          }
        />
        <Route
          path="accounts"
          element={
            <ProtectedRoute
              allowedRoles={['agency_admin', 'branch_manager', 'accounts_staff']}
            >
              <AccountsPage />
            </ProtectedRoute>
          }
        />
        <Route
          path="invoices"
          element={
            <ProtectedRoute
              allowedRoles={['agency_admin', 'branch_manager', 'accounts_staff']}
            >
              <InvoicesPage />
            </ProtectedRoute>
          }
        />
        <Route
          path="clients"
          element={
            <ProtectedRoute
              allowedRoles={[
                'agency_admin',
                'branch_manager',
                'operations_staff',
                'accounts_staff',
              ]}
            >
              <ClientsPage />
            </ProtectedRoute>
          }
        />
        <Route
          path="branches"
          element={
            <ProtectedRoute allowedRoles={['agency_admin', 'branch_manager']}>
              <BranchesPage />
            </ProtectedRoute>
          }
        />
        <Route
          path="staff"
          element={
            <ProtectedRoute allowedRoles={['agency_admin', 'branch_manager']}>
              <StaffPage />
            </ProtectedRoute>
          }
        />
        <Route
          path="tariffs"
          element={
            <ProtectedRoute
              allowedRoles={[
                'agency_admin',
                'branch_manager',
                'operations_staff',
                'accounts_staff',
              ]}
            >
              <TariffsPage />
            </ProtectedRoute>
          }
        />
        <Route
          path="agency/tickets"
          element={
            <ProtectedRoute
              allowedRoles={[
                'agency_admin',
                'branch_manager',
                'operations_staff',
                'accounts_staff',
              ]}
            >
              <AgencyTicketsPage />
            </ProtectedRoute>
          }
        />
        <Route
          path="agency/settings"
          element={
            <ProtectedRoute allowedRoles={['agency_admin']}>
              <AgencySettingsPage />
            </ProtectedRoute>
          }
        />

        {/* Customer Self-Service Portal */}
        <Route
          path="customer"
          element={
            <ProtectedRoute
              allowedRoles={['customer', 'superadmin', 'agency_admin']}
            >
              <CustomerPortal />
            </ProtectedRoute>
          }
        />

        {/* Delivery Field Module */}
        <Route
          path="delivery"
          element={
            <ProtectedRoute
              allowedRoles={[
                'delivery_personnel',
                'operations_staff',
                'agency_admin',
                'superadmin',
              ]}
            >
              <MobileRunsheet />
            </ProtectedRoute>
          }
        />
      </Route>

      {/* Fallback */}
      <Route path="*" element={<Navigate to="/" replace />} />
    </Routes>
  );
};

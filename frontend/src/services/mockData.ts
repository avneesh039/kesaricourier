// frontend/src/services/mockData.ts
// Comprehensive Mock Engine for Vercel Static Preview & Offline Demos

export interface DemoUser {
  id: number;
  name: string;
  email: string;
  phone: string;
  role: 'superadmin' | 'agency_manager' | 'operator' | 'delivery_personnel' | 'customer';
  status: string;
  tenantId?: number;
  branchId?: number;
  branchName?: string;
  tenantName?: string;
  tradeName?: string;
  agencyCode?: string;
  brandColor?: string;
}

export const DEMO_USERS: Record<string, DemoUser> = {
  'admin@kesari.com': {
    id: 1,
    name: 'Avneesh Sharma (Super Admin)',
    email: 'admin@kesari.com',
    phone: '+91 9893000000',
    role: 'superadmin',
    status: 'active',
  },
  'agent@raipur.com': {
    id: 2,
    name: 'Ramesh Verma (Raipur Hub)',
    email: 'agent@raipur.com',
    phone: '+91 9827100000',
    role: 'agency_manager',
    status: 'active',
    tenantId: 1,
    branchId: 1,
    branchName: 'Central Station Counter (#26)',
    tenantName: 'Raipur Express Logistics',
    tradeName: 'Raipur Express Counter',
    agencyCode: 'KES-RAI-01',
    brandColor: '#2563eb',
  },
  'driver@raipur.com': {
    id: 3,
    name: 'Sunil Sahu (Van Rider #4)',
    email: 'driver@raipur.com',
    phone: '+91 9425200000',
    role: 'delivery_personnel',
    status: 'active',
    tenantId: 1,
    branchId: 1,
    branchName: 'Central Station Counter (#26)',
  },
  'client@tata.com': {
    id: 4,
    name: 'Tata Steel B2B Logistics',
    email: 'client@tata.com',
    phone: '+91 9820000000',
    role: 'customer',
    status: 'active',
    tradeName: 'Tata Steel B2B Division',
  },
};

export const getDemoUser = (email: string, _password?: string, _tenantSlug?: string): DemoUser => {
  const normalized = email.trim().toLowerCase();
  if (DEMO_USERS[normalized]) {
    return DEMO_USERS[normalized];
  }
  // Generic fallback if user enters custom email
  const isSuper = normalized.includes('admin');
  return {
    id: 99,
    name: email.split('@')[0].toUpperCase(),
    email: normalized,
    phone: '+91 9800000000',
    role: isSuper ? 'superadmin' : 'agency_manager',
    status: 'active',
    tenantId: 1,
    branchId: 1,
    branchName: 'Central Express Hub',
    tenantName: 'Kesari Express Logistics',
    tradeName: 'Kesari Express',
    agencyCode: 'KES-DEMO-99',
    brandColor: '#2563eb',
  };
};

export const handleMockRequest = (url: string = '', method: string = 'GET', requestData?: any): any => {
  const normUrl = url.toLowerCase();
  const normMethod = method.toUpperCase();

  // 1. Auth Login
  if (normUrl.includes('/auth/login') && normMethod === 'POST') {
    let payload = requestData;
    if (typeof payload === 'string') {
      try { payload = JSON.parse(payload); } catch (e) {}
    }
    const email = payload?.email || 'admin@kesari.com';
    const user = getDemoUser(email, payload?.password, payload?.tenantSlug);
    return {
      success: true,
      message: 'Demo login successful',
      data: {
        accessToken: `kesari_demo_token_${Date.now()}`,
        refreshToken: `kesari_demo_refresh_${Date.now()}`,
        user,
      },
    };
  }

  // 2. Auth Profile
  if (normUrl.includes('/auth/me')) {
    const saved = localStorage.getItem('kesari_user');
    const user = saved ? JSON.parse(saved) : DEMO_USERS['admin@kesari.com'];
    return {
      success: true,
      data: user,
    };
  }

  // 3. Super Admin Dashboard Telemetry
  if (normUrl.includes('/superadmin/dashboard')) {
    return {
      success: true,
      data: {
        kpis: {
          totalAgencies: 42,
          pendingKyc: 3,
          todayBookings: 1248,
          todayRevenue: 284500,
          activeDestinations: 19400,
          openTickets: 4,
        },
        onlineVsSelf: {
          onlineSharePct: 68,
          counterSharePct: 32,
          onlineCount: 849,
          counterCount: 399,
        },
        carrierApis: [
          { id: 'delhivery', name: 'Delhivery Surface & Air', status: 'connected', latencyMs: 82, lastPing: 'Just now' },
          { id: 'bluedart', name: 'BlueDart Apex Air', status: 'connected', latencyMs: 114, lastPing: 'Just now' },
          { id: 'maruti', name: 'Shree Maruti Courier', status: 'connected', latencyMs: 96, lastPing: 'Just now' },
          { id: 'dtdc', name: 'DTDC Express Dot', status: 'connected', latencyMs: 142, lastPing: '1 min ago' },
          { id: 'shiprocket', name: 'Shiprocket B2B Cargo', status: 'connected', latencyMs: 130, lastPing: 'Just now' },
          { id: 'self', name: 'Kesari Dedicated Line-Haul', status: 'connected', latencyMs: 24, lastPing: 'Real-time' },
        ],
        pendingAgencies: [
          { id: 101, name: 'Bilaspur Logistics Junction', contactPerson: 'Vikram Patel', email: 'vikram@bilaspurlog.com', phone: '+91 9827011223', city: 'Bilaspur', planType: 'Yearly', createdAt: '2026-10-25' },
          { id: 102, name: 'Durg FastCargo Hub', contactPerson: 'Anita Soni', email: 'anita@durgcargo.com', phone: '+91 9425122334', city: 'Durg', planType: 'Quarterly', createdAt: '2026-10-25' },
          { id: 103, name: 'Korba Industrial Delivery', contactPerson: 'Sanjay Dewangan', email: 'sanjay@korbadelivery.in', phone: '+91 9826133445', city: 'Korba', planType: 'Yearly', createdAt: '2026-10-24' },
        ],
      },
    };
  }

  // 4. Super Admin Agencies List
  if (normUrl.includes('/superadmin/agencies')) {
    return {
      success: true,
      data: [
        { id: 1, name: 'Raipur Express Logistics', code: 'KES-RAI-01', contactPerson: 'Ramesh Verma', email: 'agent@raipur.com', phone: '+91 9827100000', city: 'Raipur', state: 'Chhattisgarh', status: 'active', planType: 'Enterprise Franchise', createdAt: '2026-10-01' },
        { id: 2, name: 'Bhilai Express Hub', code: 'KES-BHI-02', contactPerson: 'Alok Gupta', email: 'alok@bhilaiexpress.com', phone: '+91 9425012345', city: 'Bhilai', state: 'Chhattisgarh', status: 'active', planType: 'Growth Hub', createdAt: '2026-10-05' },
        { id: 3, name: 'Nagpur Transit Gateway', code: 'KES-NAG-03', contactPerson: 'Rajesh Sharma', email: 'rajesh@nagpurlogistics.com', phone: '+91 9890123456', city: 'Nagpur', state: 'Maharashtra', status: 'active', planType: 'Regional Master', createdAt: '2026-09-15' },
        { id: 101, name: 'Bilaspur Logistics Junction', code: 'KES-BIL-04', contactPerson: 'Vikram Patel', email: 'vikram@bilaspurlog.com', phone: '+91 9827011223', city: 'Bilaspur', state: 'Chhattisgarh', status: 'pending', planType: 'Yearly', createdAt: '2026-10-25' },
      ],
    };
  }

  // 5. Agency Dashboard
  if (normUrl.includes('/agency/dashboard')) {
    return {
      success: true,
      data: {
        kpis: {
          todayBookings: 148,
          todayRevenue: 34250,
          pendingInscan: 18,
          outForDelivery: 42,
          unbilledAmount: 58400,
          reconciliationPending: 5,
        },
        agency: {
          id: 1,
          name: 'Raipur Express Logistics Hub',
          trade_name: 'Raipur Express Central Counter (#26)',
          agency_code: 'KES-RAI-01',
          plan: 'Enterprise Franchise',
        },
        recentBookings: [
          { id: 1, awb_number: 'KES-2026-88910', client_name: 'Shree Krishna Traders', destination: 'NAGPUR (MH)', pieces: 2, weight: 1.5, total_amount: 380, status: 'IN_TRANSIT', created_at: '2026-10-25 18:30' },
          { id: 2, awb_number: 'KES-2026-88911', client_name: 'Walk-in Cash Counter', destination: 'MUMBAI (MH)', pieces: 1, weight: 0.5, total_amount: 190, status: 'BOOKED', created_at: '2026-10-25 18:45' },
          { id: 3, awb_number: 'KES-2026-88912', client_name: 'Tata Steel B2B', destination: 'DELHI NCR', pieces: 5, weight: 12.0, total_amount: 2450, status: 'MANIFESTED', created_at: '2026-10-25 19:10' },
          { id: 4, awb_number: 'KES-2026-88913', client_name: 'Apollo Medical Supplies', destination: 'HYDERABAD (TS)', pieces: 1, weight: 0.25, total_amount: 160, status: 'DELIVERED', created_at: '2026-10-25 19:25' },
        ],
      },
    };
  }

  // 6. Bookings List & Create
  if (normUrl.includes('/bookings')) {
    if (normMethod === 'POST') {
      const docketNo = `KES-2026-${Math.floor(10000 + Math.random() * 90000)}`;
      return {
        success: true,
        message: 'Consignment booked successfully',
        data: {
          id: Date.now(),
          awb_number: docketNo,
          docket_number: docketNo,
          ...requestData,
          created_at: new Date().toISOString(),
        },
      };
    }
    return {
      success: true,
      data: [
        { id: 1, awb_number: 'KES-2026-88910', client_name: 'Shree Krishna Traders', consignee_name: 'Mahalaxmi Textiles', destination: 'NAGPUR (MH)', pieces: 2, weight: 1.5, total_amount: 380, status: 'IN_TRANSIT', created_at: '2026-10-25' },
        { id: 2, awb_number: 'KES-2026-88911', client_name: 'Walk-in Cash Counter', consignee_name: 'Rohan Sharma', destination: 'MUMBAI (MH)', pieces: 1, weight: 0.5, total_amount: 190, status: 'BOOKED', created_at: '2026-10-25' },
        { id: 3, awb_number: 'KES-2026-88912', client_name: 'Tata Steel B2B', consignee_name: 'Jindal Steels Ltd', destination: 'DELHI NCR', pieces: 5, weight: 12.0, total_amount: 2450, status: 'MANIFESTED', created_at: '2026-10-25' },
      ],
    };
  }

  // 7. Corporate Clients
  if (normUrl.includes('/clients')) {
    return {
      success: true,
      data: [
        { id: 1, code: 'CL-001', name: 'Tata Steel B2B Division', contactPerson: 'Sunil Mehta', phone: '+91 9820000000', email: 'logistics@tatasteel.com', gstin: '22AAACT1234M1Z1', city: 'Raipur', creditLimit: 250000, currentBalance: 42100 },
        { id: 2, code: 'CL-002', name: 'Shree Krishna Traders', contactPerson: 'Gopal Agarwal', phone: '+91 9827112233', email: 'krishna.traders@gmail.com', gstin: '22AABCK9988P1Z5', city: 'Raipur', creditLimit: 100000, currentBalance: 18350 },
        { id: 3, code: 'CL-003', name: 'Apollo Medical Supplies', contactPerson: 'Dr. Vivek Soni', phone: '+91 9425233445', email: 'raipur.desk@apollomed.com', gstin: '22AAACA5566K1Z9', city: 'Bhilai', creditLimit: 50000, currentBalance: 6400 },
      ],
    };
  }

  // 8. Invoices & Billing
  if (normUrl.includes('/invoices')) {
    return {
      success: true,
      data: [
        { id: 1, invoiceNumber: 'INV-2026-10-001', clientName: 'Tata Steel B2B Division', billingPeriod: 'Oct 2026', totalDockets: 48, taxableAmount: 42100, gstAmount: 7578, grandTotal: 49678, status: 'UNPAID', sacCode: '996812', createdAt: '2026-10-24' },
        { id: 2, invoiceNumber: 'INV-2026-10-002', clientName: 'Shree Krishna Traders', billingPeriod: 'Oct 2026', totalDockets: 26, taxableAmount: 18350, gstAmount: 3303, grandTotal: 21653, status: 'PAID', sacCode: '996812', createdAt: '2026-10-20' },
      ],
    };
  }

  // 9. Operations (Inscan, POD, Manifests, Runsheets)
  if (normUrl.includes('/operations/inscan')) {
    return {
      success: true,
      message: 'Barcode unscanned successfully',
      data: {
        totalScanned: 18,
        recentScans: [
          { awb: 'KES-2026-88910', origin: 'RAIPUR', dest: 'NAGPUR', weight: 1.5, status: 'INSCAN_RECEIVED' },
          { awb: 'KES-2026-88911', origin: 'RAIPUR', dest: 'MUMBAI', weight: 0.5, status: 'INSCAN_RECEIVED' },
        ],
      },
    };
  }

  if (normUrl.includes('/operations/pod')) {
    return {
      success: true,
      message: 'POD updated successfully',
      data: [
        { awb_number: 'KES-2026-88910', receiver_name: 'M. Patel', relation: 'Self', delivery_time: '2026-10-25 16:40', status: 'DELIVERED' },
      ],
    };
  }

  // 10. Central Destinations Search
  if (normUrl.includes('/destinations')) {
    return {
      success: true,
      data: [
        { id: 1, name: 'RAIPUR', state: 'Chhattisgarh', pincode: '492001', hub: 'Central Sorting Hub', isServiced: true },
        { id: 2, name: 'BILASPUR', state: 'Chhattisgarh', pincode: '495001', hub: 'Bilaspur Gateway', isServiced: true },
        { id: 3, name: 'BHILAI', state: 'Chhattisgarh', pincode: '490001', hub: 'Durg Hub', isServiced: true },
        { id: 4, name: 'NAGPUR', state: 'Maharashtra', pincode: '440001', hub: 'Transit Gateway', isServiced: true },
        { id: 5, name: 'MUMBAI', state: 'Maharashtra', pincode: '400001', hub: 'Western Air Gateway', isServiced: true },
        { id: 6, name: 'DELHI NCR', state: 'Delhi', pincode: '110001', hub: 'Northern Air Express', isServiced: true },
      ],
    };
  }

  // Generic fallback
  return {
    success: true,
    data: {},
  };
};

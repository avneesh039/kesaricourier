// frontend/src/services/mockData.ts
// Comprehensive Persistent Mock Database Engine for Vercel Static Preview & Offline Demos

export interface DemoUser {
  id: number;
  name: string;
  email: string;
  phone: string;
  role: 'superadmin' | 'agency_admin' | 'branch_manager' | 'operations_staff' | 'delivery_personnel' | 'accounts_staff' | 'customer';
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
    name: 'Super Administrator',
    email: 'admin@kesari.com',
    phone: '+91 9900000000',
    role: 'superadmin',
    status: 'active',
  },
  'shreemaruti@raipur.com': {
    id: 2,
    name: 'Shree Maruti Raipur Admin',
    email: 'shreemaruti@raipur.com',
    phone: '+91 9893000000',
    role: 'agency_admin',
    status: 'active',
    tenantId: 1,
    branchId: 1,
    branchName: 'Raipur Main Counter (Pandri)',
    tenantName: 'Keshri Express (Raipur)',
    tradeName: 'Shree Maruti Courier Agency',
    agencyCode: '26',
    brandColor: '#1a73e8',
  },
  'manager.raipur@kesari.com': {
    id: 3,
    name: 'Raipur Branch Manager',
    email: 'manager.raipur@kesari.com',
    phone: '+91 9827100000',
    role: 'branch_manager',
    status: 'active',
    tenantId: 1,
    branchId: 1,
    branchName: 'Raipur Main Counter (Pandri)',
    tenantName: 'Keshri Express (Raipur)',
    tradeName: 'Shree Maruti Courier Agency',
    agencyCode: '26',
    brandColor: '#1a73e8',
  },
  'ops.raipur@kesari.com': {
    id: 4,
    name: 'Booking Counter Operator',
    email: 'ops.raipur@kesari.com',
    phone: '+91 9827200000',
    role: 'operations_staff',
    status: 'active',
    tenantId: 1,
    branchId: 1,
    branchName: 'Raipur Main Counter (Pandri)',
    tenantName: 'Keshri Express (Raipur)',
    tradeName: 'Shree Maruti Courier Agency',
    agencyCode: '26',
  },
  'delivery.raipur@kesari.com': {
    id: 5,
    name: 'Field Delivery Executive',
    email: 'delivery.raipur@kesari.com',
    phone: '+91 9425200000',
    role: 'delivery_personnel',
    status: 'active',
    tenantId: 1,
    branchId: 1,
    branchName: 'Raipur Main Counter (Pandri)',
  },
  'customer@acme.com': {
    id: 6,
    name: 'Tata Steel B2B Logistics',
    email: 'customer@acme.com',
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
  const isSuper = normalized.includes('admin');
  return {
    id: 99,
    name: email.split('@')[0].toUpperCase(),
    email: normalized,
    phone: '+91 9800000000',
    role: isSuper ? 'superadmin' : 'agency_admin',
    status: 'active',
    tenantId: 1,
    branchId: 1,
    branchName: 'Central Express Hub',
    tenantName: 'Kesari Express Logistics',
    tradeName: 'Kesari Express',
    agencyCode: '26',
    brandColor: '#1a73e8',
  };
};

// ==========================================
// PERSISTENT STORAGE ENGINE (LOCALSTORAGE)
// ==========================================

const INITIAL_DESTINATIONS = [
  { id: 1053, code: 'RAI-1053', name: 'RAIPUR (Kushalpur Apex Hub)', pincode: '492013', is_hub: true, is_active: true, state_id: 1, state: { id: 1, code: 'CG', name: 'Chhattisgarh', zone: { id: 5, code: 'ZONE5', name: 'Central Zone' } } },
  { id: 154, code: 'BHI-154', name: 'BHILAI (Supela Hub)', pincode: '490023', is_hub: true, is_active: true, state_id: 1, state: { id: 1, code: 'CG', name: 'Chhattisgarh', zone: { id: 5, code: 'ZONE5', name: 'Central Zone' } } },
  { id: 1391, code: 'BSP-1391', name: 'BILASPUR (Link Road Hub)', pincode: '495001', is_hub: true, is_active: true, state_id: 1, state: { id: 1, code: 'CG', name: 'Chhattisgarh', zone: { id: 5, code: 'ZONE5', name: 'Central Zone' } } },
  { id: 714, code: 'KRB-714', name: 'KORBA (Transport Nagar)', pincode: '495677', is_hub: true, is_active: true, state_id: 1, state: { id: 1, code: 'CG', name: 'Chhattisgarh', zone: { id: 5, code: 'ZONE5', name: 'Central Zone' } } },
  { id: 1070, code: 'RGH-1070', name: 'RAIGARH (Station Road)', pincode: '496001', is_hub: true, is_active: true, state_id: 1, state: { id: 1, code: 'CG', name: 'Chhattisgarh', zone: { id: 5, code: 'ZONE5', name: 'Central Zone' } } },
  { id: 565, code: 'JGD-565', name: 'JAGDALPUR (Bastar Corridor)', pincode: '494001', is_hub: true, is_active: true, state_id: 1, state: { id: 1, code: 'CG', name: 'Chhattisgarh', zone: { id: 5, code: 'ZONE5', name: 'Central Zone' } } },
  { id: 3, code: 'AMB-3', name: 'AMBIKAPUR (Surguja Hub)', pincode: '497001', is_hub: true, is_active: true, state_id: 1, state: { id: 1, code: 'CG', name: 'Chhattisgarh', zone: { id: 5, code: 'ZONE5', name: 'Central Zone' } } },
  { id: 1077, code: 'RJN-1077', name: 'RAJNANDGAON (G.E Road)', pincode: '491441', is_hub: false, is_active: true, state_id: 1, state: { id: 1, code: 'CG', name: 'Chhattisgarh', zone: { id: 5, code: 'ZONE5', name: 'Central Zone' } } },
  { id: 1397, code: 'BHT-1397', name: 'BHATAPARA (Mandi Hub)', pincode: '493118', is_hub: false, is_active: true, state_id: 1, state: { id: 1, code: 'CG', name: 'Chhattisgarh', zone: { id: 5, code: 'ZONE5', name: 'Central Zone' } } },
  { id: 1390, code: 'MSM-1390', name: 'MAHASAMUND', pincode: '493445', is_hub: false, is_active: true, state_id: 1, state: { id: 1, code: 'CG', name: 'Chhattisgarh', zone: { id: 5, code: 'ZONE5', name: 'Central Zone' } } },
  { id: 1379, code: 'SRP-1379', name: 'SARAIPALI', pincode: '493558', is_hub: false, is_active: true, state_id: 1, state: { id: 1, code: 'CG', name: 'Chhattisgarh', zone: { id: 5, code: 'ZONE5', name: 'Central Zone' } } },
  { id: 1385, code: 'BMT-1385', name: 'BEMETARA', pincode: '491335', is_hub: false, is_active: true, state_id: 1, state: { id: 1, code: 'CG', name: 'Chhattisgarh', zone: { id: 5, code: 'ZONE5', name: 'Central Zone' } } },
  { id: 1386, code: 'KWD-1386', name: 'KAWARDHA', pincode: '491995', is_hub: false, is_active: true, state_id: 1, state: { id: 1, code: 'CG', name: 'Chhattisgarh', zone: { id: 5, code: 'ZONE5', name: 'Central Zone' } } },
  { id: 330, code: 'DHM-330', name: 'DHAMTARI', pincode: '493773', is_hub: false, is_active: true, state_id: 1, state: { id: 1, code: 'CG', name: 'Chhattisgarh', zone: { id: 5, code: 'ZONE5', name: 'Central Zone' } } },
  { id: 4, code: 'BOM-4', name: 'MUMBAI (BOM Apex)', pincode: '400001', is_hub: true, is_active: true, state_id: 2, state: { id: 2, code: 'MH', name: 'Maharashtra', zone: { id: 2, code: 'ZONE2', name: 'West Zone' } } },
  { id: 6, code: 'NAG-6', name: 'NAGPUR (Central Transit Hub)', pincode: '440001', is_hub: true, is_active: true, state_id: 2, state: { id: 2, code: 'MH', name: 'Maharashtra', zone: { id: 2, code: 'ZONE2', name: 'West Zone' } } },
  { id: 9, code: 'DEL-9', name: 'DELHI (DEL National Hub)', pincode: '110001', is_hub: true, is_active: true, state_id: 4, state: { id: 4, code: 'DL', name: 'Delhi', zone: { id: 1, code: 'ZONE1', name: 'North Zone' } } },
  { id: 10, code: 'CCU-10', name: 'KOLKATA (CCU Eastern Hub)', pincode: '700001', is_hub: true, is_active: true, state_id: 5, state: { id: 5, code: 'WB', name: 'West Bengal', zone: { id: 4, code: 'ZONE4', name: 'East Zone' } } },
];

const INITIAL_CLIENTS = [
  { id: 1, tenant_id: 1, client_code: 'WALKIN', name: 'Walk-in Cash Retail Customer', contact_person: 'Retail Cash Desk', phone: '9399676408', email: 'shreemaruti@raipur.com', city: 'Raipur', state: 'Chhattisgarh', pincode: '492001', fuel_surcharge_percent: 10, status: 'active' },
  { id: 2, tenant_id: 1, client_code: 'CL-1-1', name: 'IDBI BANK LTD (CIVIL LINE BRANCH)', contact_person: 'Suresh Swain', phone: '0771-4222801', email: 'suresh.swain@idbi.co.in', gstin: '22AABCI8842G1ZP', address: 'Dev Tower, Nr Holy Heart School Civil Line, Raipur 492001', city: 'Raipur', state: 'Chhattisgarh', pincode: '492001', fuel_surcharge_percent: 12, status: 'active' },
  { id: 3, tenant_id: 1, client_code: 'CL-2-2', name: 'KOTAK MAHINDRA BANK LTD', contact_person: 'Branch Operations Head', phone: '0771-400123', email: 'raipur@kotak.net', gstin: '22AAACK4409J1ZU', address: 'Ground Floor, Dhand Compound, Civil Lines, Raipur', city: 'Raipur', state: 'Chhattisgarh', pincode: '492001', fuel_surcharge_percent: 10, status: 'active' },
  { id: 4, tenant_id: 1, client_code: 'CL-3-3', name: 'REAL ISPAT & POWER LTD (BORJHARA PLANT)', contact_person: 'Logistics Head', phone: '0771-4224111', email: 'real@realispat.com', gstin: '22AABCR9986L1ZQ', address: 'Bendri Road Borjhara, Raipur', city: 'Raipur', state: 'Chhattisgarh', pincode: '493221', fuel_surcharge_percent: 15, status: 'active' },
  { id: 5, tenant_id: 1, client_code: 'CL-4-4', name: 'IDBI BANK LTD (MOWA BRANCH)', contact_person: 'Operations Desk', phone: '0771-6050026', email: 'raipur.mowa@idbi.co.in', gstin: '22AABCI8842G1ZP', address: 'C.G Elite, Ground Floor Opp. Mandi Gate Pandri, Mowa', city: 'Raipur', state: 'Chhattisgarh', pincode: '492001', fuel_surcharge_percent: 10, status: 'active' },
  { id: 6, tenant_id: 1, client_code: 'CL-6-6', name: 'UNION BANK OF INDIA', contact_person: 'Logistics Officer', phone: '09425242949', email: 'raipur@unionbankofindia.bank', gstin: '22AAACU0564G1ZR', address: 'Bhatiya Complex Main Road Pandri, Raipur', city: 'Raipur', state: 'Chhattisgarh', pincode: '492001', fuel_surcharge_percent: 10, status: 'active' },
  { id: 7, tenant_id: 1, client_code: 'CL-7-7', name: 'METHODEX SYSTEMS PVT LTD', contact_person: 'Ashok Varma', phone: '09009061119', email: 'raipur@methodexsystems.com', gstin: '22AAACM1924E1Z5', address: 'Ashoka Millenium, Ring Rd No: 1, New Rajendra Nagar, Raipur', city: 'Raipur', state: 'Chhattisgarh', pincode: '492001', fuel_surcharge_percent: 10, status: 'active' },
  { id: 8, tenant_id: 1, client_code: 'CL-10-10', name: 'TIRUPATI CORPORATION PVT LTD', contact_person: 'Accounts Head', phone: '08818880845', email: 'accounts@tirupatisteel.com', gstin: '22AAECT0745N1ZA', address: 'Plot No 189/1 Nandanvan Road Village Atari Tatibandh, Raipur', city: 'Raipur', state: 'Chhattisgarh', pincode: '492099', fuel_surcharge_percent: 12, status: 'active' },
  { id: 9, tenant_id: 1, client_code: 'CL-11-11', name: 'C & S ELECTRIC LTD', contact_person: 'Dispatch Desk', phone: '0771-4061404', email: 'raipur@cselectric.co.in', gstin: '09AAACC0909K1ZR', address: 'Maruti Height Mohba Bazar, Raipur', city: 'Raipur', state: 'Chhattisgarh', pincode: '492001', fuel_surcharge_percent: 10, status: 'active' },
  { id: 10, tenant_id: 1, client_code: 'CL-13-13', name: 'REAL ISPAT & POWER LTD (URLA PLANT)', contact_person: 'Dispatch Yard', phone: '0771-3051000', email: 'steel@gktmt.com', gstin: '22AABCR9986L1ZQ', address: 'Plot No 315 & 321, Urla Industrial Area, Raipur', city: 'Raipur', state: 'Chhattisgarh', pincode: '493221', fuel_surcharge_percent: 15, status: 'active' },
  { id: 11, tenant_id: 1, client_code: 'CL-93-130', name: 'MANAPPURAM FINANCE LIMITED', contact_person: 'Branch Coordinator', phone: '07489548691', email: '1322484@manappuram.com', gstin: '22AABCM6882E1ZL', address: 'Aswani Nagar Verma House Mahadev Ghat Road, Raipur', city: 'Raipur', state: 'Chhattisgarh', pincode: '492013', fuel_surcharge_percent: 10, status: 'active' },
  { id: 12, tenant_id: 1, client_code: 'CL-64-500', name: 'NATCURE BIOTECH PVT LTD', contact_person: 'Logistics Head', phone: '0771-4041234', email: 'customercare@natcurebiotech.com', gstin: '22AAGCN5770R1ZV', address: 'House No. 7, Third Floor Hanuman Nagar, Raipur', city: 'Raipur', state: 'Chhattisgarh', pincode: '492001', fuel_surcharge_percent: 10, status: 'active' },
];

const INITIAL_BRANCHES = [
  { id: 1, tenant_id: 1, branch_code: 'BR-1', branch_name: 'KESHRI EXPRESS (Head Office - Kushalpur)', address: 'Thakur Building, Opp Wallfort City Gate, Ring Road No-1, Kushalpur', city: 'Raipur', state: 'Chhattisgarh', pincode: '492013', phone: '0771-4004544', status: 'active', _count: { users: 5, consignments: 1240 } },
  { id: 2, tenant_id: 1, branch_code: 'BR-2', branch_name: 'M/S KESHRI EXPRESS (Amleshwar Durg)', address: '301, C/3, Wood I Land Amleshwar, Dist-Durg', city: 'Durg', state: 'Chhattisgarh', pincode: '491111', phone: '9303340016', status: 'active', _count: { users: 2, consignments: 340 } },
  { id: 3, tenant_id: 1, branch_code: 'BR-3', branch_name: 'KESHRI EXPRESS (Bemetara Branch)', address: 'Main Road Bemetara Hub', city: 'Bemetara', state: 'Chhattisgarh', pincode: '491335', phone: '0771-4004544', status: 'active', _count: { users: 2, consignments: 180 } },
  { id: 4, tenant_id: 1, branch_code: 'BR-4', branch_name: 'KESHRI EXPRESS (Supela Bhilai)', address: '268 First Floor, Dhillon Complex, Supela', city: 'Bhilai', state: 'Chhattisgarh', pincode: '490023', phone: '9303340016', status: 'active', _count: { users: 3, consignments: 490 } },
  { id: 5, tenant_id: 1, branch_code: 'BR-5', branch_name: 'KESHRI EXPRESS (Rajim Branch)', address: 'Main Road Rajim Counter', city: 'Rajim', state: 'Chhattisgarh', pincode: '493885', phone: '9926142214', status: 'active', _count: { users: 1, consignments: 120 } },
  { id: 6, tenant_id: 1, branch_code: 'BR-6', branch_name: 'KESHRI EXPRESS (Mahasamund)', address: 'Station Road Mahasamund Hub', city: 'Mahasamund', state: 'Chhattisgarh', pincode: '493445', phone: '7089168878', status: 'active', _count: { users: 2, consignments: 210 } },
  { id: 7, tenant_id: 1, branch_code: 'BR-7', branch_name: 'KESHRI EXPRESS (Saraipali)', address: 'Main Market Saraipali', city: 'Saraipali', state: 'Chhattisgarh', pincode: '493558', phone: '9165581693', status: 'active', _count: { users: 1, consignments: 150 } },
  { id: 8, tenant_id: 1, branch_code: 'BR-8', branch_name: 'KESHRI EXPRESS (Jashpur)', address: 'Jashpur Bus Stand Counter', city: 'Jashpur', state: 'Chhattisgarh', pincode: '496331', phone: '9669848914', status: 'active', _count: { users: 1, consignments: 95 } },
  { id: 9, tenant_id: 1, branch_code: 'BR-9', branch_name: 'KESHRI EXPRESS (Bilaspur Link Road)', address: 'Link Road Bilaspur Regional Hub', city: 'Bilaspur', state: 'Chhattisgarh', pincode: '495001', phone: '9827422074', status: 'active', _count: { users: 4, consignments: 680 } },
  { id: 10, tenant_id: 1, branch_code: 'BR-10', branch_name: 'KESHRI EXPRESS (Bhatapara)', address: 'Mandi Road Bhatapara Hub', city: 'Bhatapara', state: 'Chhattisgarh', pincode: '493118', phone: '0771-4004544', status: 'active', _count: { users: 2, consignments: 140 } },
  { id: 11, tenant_id: 1, branch_code: 'BR-11', branch_name: 'KESHRI EXPRESS (Ambikapur)', address: 'Ring Road Ambikapur Apex Hub', city: 'Ambikapur', state: 'Chhattisgarh', pincode: '497001', phone: '0771-4004544', status: 'active', _count: { users: 3, consignments: 290 } },
  { id: 13, tenant_id: 1, branch_code: 'BR-13', branch_name: 'KESHRI EXPRESS (Korba Transport Nagar)', address: 'K M Complex, Transport Nagar, Power House Road', city: 'Korba', state: 'Chhattisgarh', pincode: '495677', phone: '0771-4004544', status: 'active', _count: { users: 3, consignments: 410 } },
  { id: 14, tenant_id: 1, branch_code: 'BR-14', branch_name: 'KESHRI EXPRESS (Raigarh Hub)', address: 'Station Road Raigarh', city: 'Raigarh', state: 'Chhattisgarh', pincode: '496001', phone: '0771-4004544', status: 'active', _count: { users: 2, consignments: 320 } },
  { id: 15, tenant_id: 1, branch_code: 'BR-15', branch_name: 'KESHRI EXPRESS (Jagdalpur Bastar)', address: 'Main Road Jagdalpur', city: 'Jagdalpur', state: 'Chhattisgarh', pincode: '494001', phone: '0771-4004544', status: 'active', _count: { users: 2, consignments: 240 } },
  { id: 26, tenant_id: 1, branch_code: 'BR-26', branch_name: 'KESHRI EXPRESS (Wallfort City Pandri Express)', address: 'Beside Corner House Restaurant, Opp Wallfort City, Ring Road No 1', city: 'Raipur', state: 'Chhattisgarh', pincode: '492013', phone: '9399676408', status: 'active', _count: { users: 6, consignments: 2150 } },
];

const INITIAL_BOOKINGS = [
  {
    id: 1,
    tenant_id: 1,
    branch_id: 1,
    booking_date: '2026-10-25',
    consignment_no: '26102601000001',
    client_id: 3,
    sender_name: 'Shree Krishna Traders',
    sender_phone: '9827112233',
    recipient_name: 'Mahalaxmi Textiles',
    recipient_phone: '9890123456',
    recipient_address: '42 Itwari Cloth Market, Nagpur',
    destination_id: 6,
    mode: 'Dox',
    send_by: 'Air',
    pieces: 2,
    dead_weight: 1.5,
    billable_weight: 1.5,
    subtotal: 320,
    fuel_surcharge_amount: 32,
    docket_charge: 28,
    total_amount: 380,
    status: 'in_transit',
    is_billed: false,
    destination: { id: 6, code: 'NAG', name: 'NAGPUR', pincode: '440001', state: { name: 'Maharashtra', zone: { name: 'West Zone' } } },
    client: { id: 3, client_code: 'KRISHNA02', name: 'Shree Krishna Traders' },
    branch: { id: 1, branch_code: 'RAI-01', branch_name: 'Raipur Main Counter (Pandri)' },
  },
  {
    id: 2,
    tenant_id: 1,
    branch_id: 1,
    booking_date: '2026-10-25',
    consignment_no: '26102601000002',
    client_id: 1,
    sender_name: 'Walk-in Cash Counter',
    sender_phone: '9893000000',
    recipient_name: 'Rohan Sharma',
    recipient_phone: '9820012345',
    recipient_address: 'Flat 302, Bandra West, Mumbai',
    destination_id: 4,
    mode: 'Dox',
    send_by: 'Surface',
    pieces: 1,
    dead_weight: 0.5,
    billable_weight: 0.5,
    subtotal: 160,
    fuel_surcharge_amount: 16,
    docket_charge: 14,
    total_amount: 190,
    status: 'booked',
    is_billed: false,
    destination: { id: 4, code: 'BOM', name: 'MUMBAI', pincode: '400001', state: { name: 'Maharashtra', zone: { name: 'West Zone' } } },
    client: { id: 1, client_code: 'WALKIN', name: 'Walk-in Cash Retail Customer' },
    branch: { id: 1, branch_code: 'RAI-01', branch_name: 'Raipur Main Counter (Pandri)' },
  },
  {
    id: 3,
    tenant_id: 1,
    branch_id: 1,
    booking_date: '2026-10-25',
    consignment_no: '26102601000003',
    client_id: 2,
    sender_name: 'Tata Steel B2B Division',
    sender_phone: '9820000000',
    recipient_name: 'Jindal Steels Ltd',
    recipient_phone: '9811122233',
    recipient_address: 'Connaught Place Barakhamba Rd, New Delhi',
    destination_id: 9,
    mode: 'Non-Dox',
    send_by: 'Air',
    pieces: 5,
    dead_weight: 12.0,
    billable_weight: 12.0,
    subtotal: 2100,
    fuel_surcharge_amount: 252,
    docket_charge: 98,
    total_amount: 2450,
    status: 'in_manifest',
    is_billed: false,
    destination: { id: 9, code: 'DEL', name: 'DELHI', pincode: '110001', state: { name: 'Delhi', zone: { name: 'North Zone' } } },
    client: { id: 2, client_code: 'TATA01', name: 'Tata Steel B2B Division' },
    branch: { id: 1, branch_code: 'RAI-01', branch_name: 'Raipur Main Counter (Pandri)' },
  },
];

const INITIAL_INVOICES = [
  { id: 1, invoiceNumber: 'INV-26-2610-0001', clientName: 'Tata Steel B2B Division', billingPeriod: 'Oct 2026', totalDockets: 48, taxableAmount: 42100, gstAmount: 7578, grandTotal: 49678, status: 'UNPAID', sacCode: '996812', createdAt: '2026-10-24' },
  { id: 2, invoiceNumber: 'INV-26-2610-0002', clientName: 'Shree Krishna Traders', billingPeriod: 'Oct 2026', totalDockets: 26, taxableAmount: 18350, gstAmount: 3303, grandTotal: 21653, status: 'PAID', sacCode: '996812', createdAt: '2026-10-20' },
];

export const INITIAL_COURIER_APIS = [
  {
    id: 1,
    name: 'Delhivery API',
    code: 'delhivery',
    category: 'Air & Surface Express',
    domain: 'delhivery.com',
    status: 'connected',
    ping: '24ms',
    latency_ms: 24,
    environment: 'production',
    endpoint: 'https://track.delhivery.com/api/v1/packages/json/?waybill={awb}',
    tracking_url_template: 'https://www.delhivery.com/track/package/{awb}',
    api_key: 'dlhv_live_89f3b149c48e77a2840d',
    api_secret: 'sec_delh_9801482937',
    client_id: 'DELH-CORP-9842',
    webhook_url: 'https://api.kesaricourier.com/webhooks/delhivery',
    webhook_secret: 'whsec_dlhv_77b491902a',
    is_active: true,
    last_tested: 'Just now',
    docs_url: 'https://delhivery.com/developer/docs',
    notes: 'Primary national express gateway for metro-to-metro air dockets and North India surface logistics network.',
  },
  {
    id: 2,
    name: 'Xpressbees API',
    code: 'xpressbees',
    category: 'E-Commerce & Surface Cargo',
    domain: 'xpressbees.com',
    status: 'connected',
    ping: '31ms',
    latency_ms: 31,
    environment: 'production',
    endpoint: 'https://shipment.xpressbees.com/api/shipments2/trackShipmentWithAwb',
    tracking_url_template: 'https://www.xpressbees.com/track?awb={awb}',
    api_key: 'xb_jwt_bearer_token_e30948acfe29',
    api_secret: 'xb_sec_66401928374',
    client_id: 'XB-MERCHANT-4410',
    webhook_url: 'https://api.kesaricourier.com/webhooks/xpressbees',
    webhook_secret: 'whsec_xb_991823410',
    is_active: true,
    last_tested: '1 min ago',
    docs_url: 'https://xpressbees.com/developer-api',
    notes: 'Specialized for Tier-2 / Tier-3 high-reach pin codes and automated COD rematches.',
  },
  {
    id: 3,
    name: 'GMS Worldwide',
    code: 'gmsworldwide',
    category: 'Domestic & International Air',
    domain: 'gmsworldwide.com',
    status: 'connected',
    ping: '45ms',
    latency_ms: 45,
    environment: 'production',
    endpoint: 'https://api.gmsworldwide.com/rest/tracking/v1?docket={awb}',
    tracking_url_template: 'https://gmsworldwide.com/track?docket={awb}',
    api_key: 'gms_live_key_990142839a',
    api_secret: 'gms_sec_10924827',
    client_id: 'GMS-CORP-RAIPUR',
    webhook_url: 'https://api.kesaricourier.com/webhooks/gms',
    webhook_secret: 'whsec_gms_448102',
    is_active: true,
    last_tested: '2 mins ago',
    docs_url: 'https://gmsworldwide.com/api-docs',
    notes: 'South India express corridor coverage and global international air express forwarding.',
  },
  {
    id: 4,
    name: 'Shree Maruti Express',
    code: 'shreemaruti',
    category: 'Western Line-Haul & Cargo',
    domain: 'shreemaruti.com',
    status: 'connected',
    ping: '28ms',
    latency_ms: 28,
    environment: 'production',
    endpoint: 'https://api.shreemaruti.com/v1/tracking/search?awb={awb}',
    tracking_url_template: 'https://www.shreemaruti.com/track/{awb}',
    api_key: 'sm_api_key_883019842',
    api_secret: 'sm_secret_449102',
    client_id: 'SM-AGENCY-26',
    webhook_url: 'https://api.kesaricourier.com/webhooks/shreemaruti',
    webhook_secret: 'whsec_sm_229048',
    is_active: true,
    last_tested: 'Just now',
    docs_url: 'https://shreemaruti.com/developer-hub',
    notes: 'Primary line-haul partner for Western India, Gujarat, and Maharashtra express corridors.',
  },
  {
    id: 5,
    name: 'Allcargo Logistics',
    code: 'allcargo',
    category: 'Heavy Freight & Surface Cargo',
    domain: 'allcargo.com',
    status: 'connected',
    ping: '39ms',
    latency_ms: 39,
    environment: 'production',
    endpoint: 'https://api.allcargogati.com/v1/surface/docket/status/{awb}',
    tracking_url_template: 'https://www.allcargologistics.com/track-and-trace?docket={awb}',
    api_key: 'ac_cargo_token_119284729',
    api_secret: 'ac_sec_99482710',
    client_id: 'ALLCARGO-CLIENT-773',
    webhook_url: 'https://api.kesaricourier.com/webhooks/allcargo',
    webhook_secret: 'whsec_ac_339104',
    is_active: true,
    last_tested: '3 mins ago',
    docs_url: 'https://allcargologistics.com/developer-portal',
    notes: 'Bulk shipments above 50 kg and heavy industrial consignment multi-modal transit.',
  },
  {
    id: 6,
    name: 'DTDC Courier API',
    code: 'dtdc',
    category: 'Priority Express & Dot Services',
    domain: 'dtdc.com',
    status: 'connected',
    ping: '22ms',
    latency_ms: 22,
    environment: 'production',
    endpoint: 'https://smarttrack.dtdc.com/api/v1/tracking/v1/customer/awbs?trkType=cn&strCnno={awb}',
    tracking_url_template: 'https://www.dtdc.in/tracking/shipment-tracking.asp?awb={awb}',
    api_key: 'dtdc_live_token_7739104829a',
    api_secret: 'dtdc_pass_990142',
    client_id: 'GL001239',
    webhook_url: 'https://api.kesaricourier.com/webhooks/dtdc',
    webhook_secret: 'whsec_dtdc_119482',
    is_active: true,
    last_tested: 'Just now',
    docs_url: 'https://dtdc.in/api-integration',
    notes: 'Pan-India 19,000+ pincodes coverage with DTDC Dot and Priority air delivery services.',
  },
];

function getStored<T>(key: string, defaultVal: T): T {
  try {
    const raw = localStorage.getItem(key);
    if (!raw) {
      localStorage.setItem(key, JSON.stringify(defaultVal));
      return defaultVal;
    }
    const parsed = JSON.parse(raw);
    if (Array.isArray(parsed) && Array.isArray(defaultVal) && parsed.length < defaultVal.length) {
      localStorage.setItem(key, JSON.stringify(defaultVal));
      return defaultVal;
    }
    return parsed;
  } catch (e) {
    return defaultVal;
  }
}

function setStored<T>(key: string, val: T): void {
  try {
    localStorage.setItem(key, JSON.stringify(val));
  } catch (e) {}
}

// Generate simple SVG Barcode for label printing preview
export function generateMockBarcodeSvg(text: string): string {
  return `<svg width="240" height="50" viewBox="0 0 240 50" xmlns="http://www.w3.org/2000/svg">
    <rect width="240" height="50" fill="#ffffff"/>
    <g fill="#000000">
      ${text.split('').map((char, i) => {
        const x = 15 + i * 15;
        const width = (char.charCodeAt(0) % 3) + 2;
        return `<rect x="${x}" y="5" width="${width}" height="32"/>`;
      }).join('')}
    </g>
    <text x="120" y="46" font-family="monospace" font-size="11" text-anchor="middle" fill="#000000">${text}</text>
  </svg>`;
}

export const handleMockRequest = (url: string = '', method: string = 'GET', requestData?: any): any => {
  const normUrl = url.toLowerCase();
  const normMethod = method.toUpperCase();

  let payload = requestData;
  if (typeof payload === 'string') {
    try { payload = JSON.parse(payload); } catch (e) {}
  }

  // 1. Auth Login
  if (normUrl.includes('/auth/login') && normMethod === 'POST') {
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
    return { success: true, data: user };
  }

  // 3. Super Admin Dashboard Telemetry
  if (normUrl.includes('/superadmin/dashboard')) {
    const bookings = getStored('kesari_db_bookings', INITIAL_BOOKINGS);

    return {
      success: true,
      data: {
        kpis: {
          totalAgencies: 42,
          activeAgencies: 39,
          pendingAgencies: 3,
          suspendedAgencies: 0,
          expiringAgencies: 4,
          expiredAgencies: 0,
          totalBookings: bookings.length + 1240,
          deliveredBookings: 840,
          inTransitBookings: 320,
          totalRevenue: 284500,
        },
        carrierApis: [
          { id: 'delhivery', name: 'Delhivery Surface & Air', status: 'connected', latencyMs: 82, lastPing: 'Just now' },
          { id: 'bluedart', name: 'BlueDart Apex Air', status: 'connected', latencyMs: 114, lastPing: 'Just now' },
          { id: 'maruti', name: 'Shree Maruti Courier', status: 'connected', latencyMs: 96, lastPing: 'Just now' },
          { id: 'dtdc', name: 'DTDC Express Dot', status: 'connected', latencyMs: 142, lastPing: '1 min ago' },
          { id: 'shiprocket', name: 'Shiprocket B2B Cargo', status: 'connected', latencyMs: 130, lastPing: 'Just now' },
          { id: 'self', name: 'Kesari Dedicated Line-Haul', status: 'connected', latencyMs: 24, lastPing: 'Real-time' },
        ],
        recentAgencies: [
          { id: 1, name: 'Raipur Express Logistics', slug: 'raipur', agency_code: '26', status: 'active', city: 'Raipur', state: 'Chhattisgarh', created_at: '2026-10-01' },
          { id: 2, name: 'Bhilai Express Hub', slug: 'bhilai', agency_code: '27', status: 'active', city: 'Bhilai', state: 'Chhattisgarh', created_at: '2026-10-05' },
          { id: 3, name: 'Nagpur Transit Gateway', slug: 'nagpur', agency_code: '28', status: 'active', city: 'Nagpur', state: 'Maharashtra', created_at: '2026-09-15' },
        ],
      },
    };
  }

  // 4. Super Admin Agencies List
  if (normUrl.includes('/superadmin/agencies')) {
    return {
      success: true,
      data: [
        { id: 1, name: 'Raipur Express Logistics', agency_code: '26', contact_person: 'Ramesh Verma', email: 'shreemaruti@raipur.com', phone: '+91 9827100000', city: 'Raipur', state: 'Chhattisgarh', status: 'active', plan_type: 'Yearly', created_at: '2026-10-01' },
        { id: 2, name: 'Bhilai Express Hub', agency_code: '27', contact_person: 'Alok Gupta', email: 'alok@bhilaiexpress.com', phone: '+91 9425012345', city: 'Bhilai', state: 'Chhattisgarh', status: 'active', plan_type: 'Growth Hub', created_at: '2026-10-05' },
        { id: 3, name: 'Nagpur Transit Gateway', agency_code: '28', contact_person: 'Rajesh Sharma', email: 'rajesh@nagpurlogistics.com', phone: '+91 9890123456', city: 'Nagpur', state: 'Maharashtra', status: 'active', plan_type: 'Regional Master', created_at: '2026-09-15' },
      ],
    };
  }

  // 5. Agency Dashboard
  if (normUrl.includes('/agency/dashboard')) {
    const bookings = getStored('kesari_db_bookings', INITIAL_BOOKINGS);
    const clients = getStored('kesari_db_clients', INITIAL_CLIENTS);
    const branches = getStored('kesari_db_branches', INITIAL_BRANCHES);
    const totalFreightRevenue = bookings.reduce((sum: number, b: any) => sum + Number(b.total_amount || 0), 0);

    return {
      success: true,
      data: {
        agency: {
          id: 1,
          name: 'Keshri Express (Raipur)',
          trade_name: 'Shree Maruti Courier Agency',
          agency_code: '26',
          status: 'active',
          plan_type: 'Yearly',
          brand_color: '#1a73e8',
        },
        kpis: {
          totalBookings: bookings.length,
          todayBookings: bookings.length,
          deliveredCount: bookings.filter((b: any) => b.status === 'delivered').length,
          inTransitCount: bookings.filter((b: any) => b.status === 'in_transit').length,
          outForDeliveryCount: bookings.filter((b: any) => b.status === 'out_for_delivery').length,
          branchesCount: branches.length,
          clientsCount: clients.length,
          unbilledCount: bookings.filter((b: any) => !b.is_billed).length,
          totalFreightRevenue,
        },
        recentBookings: bookings.slice(0, 5),
      },
    };
  }

  // 6. Tariffs & Rate Calculation
  if (normUrl.includes('/tariffs/calculate') && normMethod === 'POST') {
    const sendBy = payload?.sendBy || 'Surface';
    const deadWeight = Number(payload?.deadWeight) || 0.5;
    const l = Number(payload?.lengthCm) || 0;
    const w = Number(payload?.widthCm) || 0;
    const h = Number(payload?.heightCm) || 0;

    const divisor = sendBy === 'Air' ? 5000 : 4000;
    const volWeight = (l > 0 && w > 0 && h > 0) ? Math.round(((l * w * h) / divisor) * 100) / 100 : 0;
    const billableWeight = Math.max(deadWeight, volWeight);

    const baseRate = sendBy === 'Air' ? 50 : 35;
    const stepRate = sendBy === 'Air' ? 40 : 25;
    const additionalKg = Math.max(0, Math.ceil(billableWeight - 0.5));
    const subtotal = baseRate + additionalKg * stepRate;
    const fuelPercent = 10;
    const fuelAmount = Math.round(subtotal * (fuelPercent / 100) * 100) / 100;
    const docketCharge = 20;
    const totalAmount = Math.round((subtotal + fuelAmount + docketCharge) * 100) / 100;

    const destinations = getStored('kesari_db_destinations', INITIAL_DESTINATIONS);
    const dest = destinations.find((d: any) => d.id === payload?.destinationId) || destinations[0];

    return {
      success: true,
      data: {
        destination: dest,
        weights: {
          deadWeight,
          volumetricWeight: volWeight,
          billableWeight,
          isVolumetricApplied: volWeight > deadWeight,
        },
        rateCalculation: {
          subtotal,
          appliedSlab: `₹${baseRate} base + ${additionalKg}x ₹${stepRate} incremental`,
          fuelPercent,
          fuelAmount,
          docketCharge,
          totalAmount,
        },
      },
    };
  }

  if (normUrl.includes('/tariffs')) {
    return {
      success: true,
      data: [
        {
          id: 1,
          send_by: 'Air',
          mode: 'Dox',
          zone: { name: 'Central Zone', code: 'ZONE5' },
          additional_weight_step: 0.5,
          additional_weight_rate: 35.0,
          slabs: [
            { id: 1, weight_from: 0.01, weight_to: 0.25, rate: 50.0 },
            { id: 2, weight_from: 0.26, weight_to: 0.5, rate: 70.0 },
            { id: 3, weight_from: 0.51, weight_to: 1.0, rate: 110.0 },
          ],
        },
        {
          id: 2,
          send_by: 'Surface',
          mode: 'Non-Dox',
          zone: { name: 'West Zone', code: 'ZONE2' },
          additional_weight_step: 1.0,
          additional_weight_rate: 25.0,
          slabs: [
            { id: 4, weight_from: 0.01, weight_to: 1.0, rate: 45.0 },
            { id: 5, weight_from: 1.01, weight_to: 3.0, rate: 90.0 },
          ],
        },
      ],
    };
  }

  // 7. Consignment Bookings (CREATE, LIST, UPDATE STATUS)
  if (normUrl.includes('/bookings')) {
    const bookings = getStored('kesari_db_bookings', INITIAL_BOOKINGS);
    const destinations = getStored('kesari_db_destinations', INITIAL_DESTINATIONS);
    const clients = getStored('kesari_db_clients', INITIAL_CLIENTS);
    const branches = getStored('kesari_db_branches', INITIAL_BRANCHES);

    // PATCH /bookings/:awb/status
    if (normMethod === 'PATCH') {
      const matchAwb = url.split('/bookings/')[1]?.split('/')[0];
      const newStatus = payload?.status || 'in_transit';
      const index = bookings.findIndex((b: any) => b.consignment_no === matchAwb);
      if (index !== -1) {
        bookings[index].status = newStatus;
        setStored('kesari_db_bookings', bookings);
        return { success: true, message: `Status updated to ${newStatus}`, data: bookings[index] };
      }
      return { success: false, error: { message: 'Consignment not found' } };
    }

    // POST /bookings (SAVE NEW BOOKING)
    if (normMethod === 'POST') {
      const destId = payload.destinationId || 1;
      const matchedDest = destinations.find((d: any) => d.id === destId || d.pincode === payload.destinationPincode) || destinations[0];
      const matchedClient = clients.find((c: any) => c.id === payload.clientId) || clients[0];
      const matchedBranch = branches[0];

      const awbNo = payload.consignmentNo?.trim() || `26102601${String(Math.floor(100000 + Math.random() * 900000))}`;
      const deadWeight = Number(payload.deadWeight) || 0.25;
      const volWeight = Number(payload.volumetricWeight) || 0;
      const billable = Math.max(deadWeight, volWeight);

      const newBooking = {
        id: Date.now(),
        tenant_id: 1,
        branch_id: matchedBranch.id,
        booking_date: payload.bookingDate || new Date().toISOString().split('T')[0],
        consignment_no: awbNo,
        client_id: matchedClient.id,
        sender_name: payload.senderName || matchedClient.name,
        sender_phone: payload.senderPhone || '9893000000',
        recipient_name: payload.recipientName || 'Walk-in Recipient',
        recipient_phone: payload.recipientPhone || '9820000000',
        recipient_address: payload.recipientAddress || `${matchedDest.name}, India`,
        destination_id: matchedDest.id,
        mode: payload.mode || 'Dox',
        send_by: payload.sendBy || 'Surface',
        pieces: Number(payload.pieces) || 1,
        dead_weight: deadWeight,
        volumetric_weight: volWeight,
        billable_weight: billable,
        subtotal: Number(payload.subtotal) || 120,
        fuel_surcharge_amount: Number(payload.fuelSurchargeAmount) || 12,
        docket_charge: Number(payload.docketCharge) || 20,
        other_charges: Number(payload.otherCharges) || 0,
        discount_amount: Number(payload.discountAmount) || 0,
        total_amount: Number(payload.totalAmount) || 152,
        insured_value: Number(payload.insuredValue) || 0,
        status: 'booked',
        is_billed: false,
        created_at: new Date().toISOString(),
        destination: matchedDest,
        client: matchedClient,
        branch: matchedBranch,
      };

      // Save into persistent localStorage
      bookings.unshift(newBooking);
      setStored('kesari_db_bookings', bookings);

      return {
        success: true,
        message: `Consignment ${awbNo} booked successfully.`,
        data: {
          ...newBooking,
          barcodeSvg: generateMockBarcodeSvg(awbNo),
        },
      };
    }

    // GET /bookings
    return {
      success: true,
      data: bookings,
      meta: {
        page: 1,
        limit: 25,
        total: bookings.length,
        totalPages: Math.ceil(bookings.length / 25),
      },
    };
  }

  // 8. Destinations Master
  if (normUrl.includes('/destinations')) {
    const destinations = getStored('kesari_db_destinations', INITIAL_DESTINATIONS);

    if (normMethod === 'POST') {
      const newDest = {
        id: Date.now(),
        code: (payload.code || 'HUB').toUpperCase(),
        name: (payload.name || 'CITY').toUpperCase(),
        pincode: payload.pincode || '400001',
        is_hub: !!payload.isHub,
        is_active: true,
        state_id: payload.stateId || 1,
        state: { id: payload.stateId || 1, code: 'CG', name: 'Chhattisgarh', zone: { id: 5, code: 'ZONE5', name: 'Central Zone' } },
      };
      destinations.push(newDest);
      setStored('kesari_db_destinations', destinations);
      return { success: true, data: newDest };
    }

    return {
      success: true,
      data: destinations,
    };
  }

  // 9. Corporate Clients (CREATE, LIST)
  if (normUrl.includes('/clients')) {
    const clients = getStored('kesari_db_clients', INITIAL_CLIENTS);

    if (normMethod === 'POST') {
      const newClient = {
        id: Date.now(),
        tenant_id: 1,
        client_code: payload.clientCode || `CL-${Date.now().toString().slice(-4)}`,
        name: payload.name || 'New Client Company',
        contact_person: payload.contactPerson || 'Account Desk',
        phone: payload.phone || '+91 9800000000',
        email: payload.email || 'client@example.com',
        address: payload.address || 'Commercial Center',
        billing_address: payload.billingAddress || payload.address,
        city: payload.city || 'Raipur',
        state: payload.state || 'Chhattisgarh',
        pincode: payload.pincode || '492001',
        gstin: payload.gstin || '22AAACT9999M1Z1',
        pan_no: payload.panNo || 'AAACT9999M',
        fuel_surcharge_percent: Number(payload.fuelSurchargePercent) || 10.0,
        status: 'active',
        created_at: new Date().toISOString(),
      };
      clients.push(newClient);
      setStored('kesari_db_clients', clients);
      return { success: true, data: newClient };
    }

    return {
      success: true,
      data: clients,
    };
  }

  // 10. Branches (CREATE, LIST)
  if (normUrl.includes('/branches')) {
    const branches = getStored('kesari_db_branches', INITIAL_BRANCHES);

    if (normMethod === 'POST') {
      const newBranch = {
        id: Date.now(),
        tenant_id: 1,
        branch_code: payload.branchCode?.toUpperCase() || `BR-${Date.now().toString().slice(-3)}`,
        branch_name: payload.branchName || 'New Branch Counter',
        address: payload.address || 'Station Road',
        city: payload.city || 'Raipur',
        state: payload.state || 'Chhattisgarh',
        pincode: payload.pincode || '492001',
        phone: payload.phone || '0771-4001234',
        status: 'active',
        created_at: new Date().toISOString(),
        _count: { users: 1, consignments: 0 },
      };
      branches.push(newBranch);
      setStored('kesari_db_branches', branches);
      return { success: true, message: 'Branch created.', data: newBranch };
    }

    return {
      success: true,
      data: branches,
    };
  }

  // 11. Invoices & Billing (GENERATE, PAY, LIST)
  if (normUrl.includes('/invoices')) {
    const invoices = getStored('kesari_db_invoices', INITIAL_INVOICES);

    if (normUrl.includes('/payments') && normMethod === 'POST') {
      return { success: true, message: 'Payment recorded successfully.' };
    }

    if (normUrl.includes('/generate') && normMethod === 'POST') {
      const invNo = `INV-26-${new Date().toISOString().slice(2, 7).replace('-', '')}-${String(invoices.length + 1).padStart(4, '0')}`;
      const newInv = {
        id: Date.now(),
        invoiceNumber: invNo,
        clientName: 'Corporate B2B Client',
        billingPeriod: 'Oct 2026',
        totalDockets: payload.consignmentIds?.length || 1,
        taxableAmount: 12500,
        gstAmount: 2250,
        grandTotal: 14750,
        status: 'UNPAID',
        sacCode: '996812',
        createdAt: new Date().toISOString().split('T')[0],
      };
      invoices.unshift(newInv);
      setStored('kesari_db_invoices', invoices);
      return { success: true, message: 'Invoice generated successfully.', data: newInv };
    }

    return {
      success: true,
      data: invoices,
    };
  }

  // 12. Operations
  if (normUrl.includes('/operations/inscan')) {
    return {
      success: true,
      message: 'Barcode unscanned successfully',
      data: {
        totalScanned: 18,
        recentScans: [
          { awb: '26102601000001', origin: 'RAIPUR', dest: 'NAGPUR', weight: 1.5, status: 'INSCAN_RECEIVED' },
          { awb: '26102601000002', origin: 'RAIPUR', dest: 'MUMBAI', weight: 0.5, status: 'INSCAN_RECEIVED' },
        ],
      },
    };
  }

  if (normUrl.includes('/operations/pod')) {
    return {
      success: true,
      message: 'POD updated successfully',
      data: [
        { awb_number: '26102601000001', receiver_name: 'M. Patel', relation: 'Self', delivery_time: '2026-10-25 16:40', status: 'DELIVERED' },
      ],
    };
  }

  // 13. Subscription Plans
  if (normUrl.includes('/superadmin/plans')) {
    return {
      success: true,
      data: [
        { id: 1, name: 'Starter Plan', description: 'Single-counter agency', price: 4999, billing_cycle: 'yearly', max_branches: 1, max_users: 3, booking_quota: 1000 },
        { id: 2, name: 'Growth Agency', description: 'Multi-counter operations', price: 12000, billing_cycle: 'yearly', max_branches: 3, max_users: 10, booking_quota: 5000 },
        { id: 3, name: 'Enterprise Hub', description: 'Unlimited scale logistics', price: 24000, billing_cycle: 'yearly', max_branches: 10, max_users: 30, booking_quota: 25000 },
      ],
    };
  }

  // 14. Super Admin Courier Tracking API Integrations
  if (normUrl.includes('/superadmin/courier-apis')) {
    const apis = getStored('kesari_db_courier_apis', INITIAL_COURIER_APIS);

    // Test ping handshake
    if (normUrl.includes('/test') && normMethod === 'POST') {
      const parts = normUrl.split('/');
      const testIdx = parts.indexOf('test');
      const idStr = testIdx > 0 ? parts[testIdx - 1] : null;
      const apiId = idStr ? parseInt(idStr, 10) : null;

      const randomLatency = Math.floor(Math.random() * 26 + 18); // 18ms - 44ms
      let testedCarrier = null;

      const updated = apis.map((item: any) => {
        if (item.id === apiId || item.code === idStr) {
          testedCarrier = {
            ...item,
            status: 'connected',
            ping: `${randomLatency}ms`,
            latency_ms: randomLatency,
            last_tested: 'Just now',
          };
          return testedCarrier;
        }
        return item;
      });

      setStored('kesari_db_courier_apis', updated);
      return {
        success: true,
        message: `Handshake successful with ${testedCarrier ? (testedCarrier as any).name : 'Carrier'} (${randomLatency}ms)`,
        data: testedCarrier || { latency_ms: randomLatency, status: 'connected' },
      };
    }

    // Update existing gateway configuration
    if (normMethod === 'PUT') {
      const parts = normUrl.split('/');
      const lastPart = parts[parts.length - 1];
      const apiId = parseInt(lastPart, 10);

      let found = false;
      const updated = apis.map((item: any) => {
        if (item.id === apiId || (payload && payload.id === item.id)) {
          found = true;
          return {
            ...item,
            ...payload,
            updated_at: new Date().toISOString(),
          };
        }
        return item;
      });

      if (!found && payload) {
        updated.push(payload);
      }

      setStored('kesari_db_courier_apis', updated);
      return {
        success: true,
        message: 'Courier gateway configuration saved successfully.',
        data: payload,
      };
    }

    // Create new custom gateway
    if (normMethod === 'POST') {
      const newGateway = {
        id: Date.now(),
        name: payload.name || 'Custom Carrier API',
        code: payload.code || `carrier_${Date.now().toString().slice(-4)}`,
        category: payload.category || 'Regional & Surface Express',
        domain: payload.domain || 'carrier.com',
        status: 'connected',
        ping: '35ms',
        latency_ms: 35,
        environment: payload.environment || 'production',
        endpoint: payload.endpoint || 'https://api.carrier.com/v1/track/{awb}',
        tracking_url_template: payload.tracking_url_template || 'https://carrier.com/track/{awb}',
        api_key: payload.api_key || '',
        api_secret: payload.api_secret || '',
        client_id: payload.client_id || '',
        webhook_url: `https://api.kesaricourier.com/webhooks/${payload.code || 'custom'}`,
        webhook_secret: `whsec_${Date.now().toString().slice(-6)}`,
        is_active: true,
        last_tested: 'Just now',
        docs_url: `https://${payload.domain || 'carrier.com'}/docs`,
        notes: payload.notes || 'Custom courier gateway connector.',
      };

      apis.push(newGateway);
      setStored('kesari_db_courier_apis', apis);
      return {
        success: true,
        message: 'Custom courier gateway created successfully.',
        data: newGateway,
      };
    }

    // Default GET: list all configured gateways
    return {
      success: true,
      data: apis,
    };
  }

  // Generic fallback
  return {
    success: true,
    data: {},
  };
};

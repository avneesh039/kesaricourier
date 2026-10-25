// frontend/src/types/index.ts

export type Role =
  | 'superadmin'
  | 'agency_admin'
  | 'branch_manager'
  | 'operations_staff'
  | 'delivery_personnel'
  | 'accounts_staff'
  | 'customer';

export interface User {
  id: number;
  name: string;
  email: string;
  phone?: string | null;
  role: Role;
  status: string;
  tenantId?: number | null;
  branchId?: number | null;
  branchName?: string | null;
  tenantName?: string | null;
  tradeName?: string | null;
  agencyCode?: string | null;
  brandColor?: string | null;
}

export interface Tenant {
  id: number;
  uuid: string;
  slug: string;
  agency_code?: string | null;
  name: string;
  trade_name?: string | null;
  contact_person: string;
  phone: string;
  email: string;
  address?: string | null;
  city?: string | null;
  state?: string | null;
  pincode?: string | null;
  gstin?: string | null;
  pan_no?: string | null;
  status: 'pending_otp' | 'pending_kyc' | 'active' | 'suspended' | 'expired' | 'rejected';
  plan_type: string;
  plan_price: number;
  validity_start?: string | null;
  validity_end?: string | null;
  grace_period_days: number;
  brand_color: string;
  created_at: string;
  _count?: {
    branches: number;
    users: number;
    consignments: number;
  };
}

export interface Branch {
  id: number;
  tenant_id: number;
  branch_code: string;
  branch_name: string;
  address: string;
  city: string;
  state: string;
  pincode: string;
  phone: string;
  status: string;
  manager?: {
    id: number;
    name: string;
    email: string;
    phone?: string | null;
  } | null;
  _count?: {
    users: number;
    consignments: number;
  };
}

export interface Destination {
  id: number;
  code: string;
  name: string;
  state_id: number;
  pincode?: string | null;
  is_hub: boolean;
  is_active: boolean;
  state?: {
    id: number;
    code: string;
    name: string;
    zone?: {
      id: number;
      code: string;
      name: string;
    };
  };
}

export interface Client {
  id: number;
  tenant_id: number;
  client_code: string;
  name: string;
  contact_person?: string | null;
  phone?: string | null;
  email?: string | null;
  address?: string | null;
  city?: string | null;
  state?: string | null;
  pincode?: string | null;
  gstin?: string | null;
  pan_no?: string | null;
  fuel_surcharge_percent: number;
  status: string;
}

export interface Consignment {
  id: number;
  tenant_id: number;
  branch_id: number;
  booking_date: string;
  consignment_no: string;
  client_id: number;
  sender_name: string;
  sender_phone: string;
  recipient_name: string;
  recipient_phone: string;
  recipient_address: string;
  destination_id: number;
  mode: 'Dox' | 'Non-Dox';
  send_by: 'Air' | 'Surface';
  pieces: number;
  dead_weight: number;
  length_cm?: number | null;
  width_cm?: number | null;
  height_cm?: number | null;
  volumetric_weight?: number | null;
  billable_weight: number;
  subtotal: number;
  fuel_surcharge_amount: number;
  docket_charge: number;
  other_charges: number;
  discount_amount: number;
  total_amount: number;
  status: 'booked' | 'in_manifest' | 'in_transit' | 'out_for_delivery' | 'delivered' | 'cancelled';
  is_billed: boolean;
  operator_name?: string | null;
  created_at: string;
  destination?: Destination;
  client?: Client;
  branch?: Branch;
  barcodeSvg?: string;
}

export interface Invoice {
  id: number;
  tenant_id: number;
  invoice_no: string;
  invoice_date: string;
  due_date: string;
  client_id: number;
  period_from: string;
  period_to: string;
  consignments_count: number;
  subtotal_amount: number;
  fuel_surcharge_percent: number;
  fuel_surcharge_amount: number;
  docket_charge_amount: number;
  taxable_amount: number;
  cgst_rate: number;
  cgst_amount: number;
  sgst_rate: number;
  sgst_amount: number;
  igst_rate: number;
  igst_amount: number;
  total_gst_amount: number;
  round_off: number;
  grand_total: number;
  payment_status: 'pending' | 'partially_paid' | 'paid' | 'cancelled';
  paid_amount: number;
  payment_mode?: string | null;
  amountInWords?: string;
  client?: Client;
  items?: InvoiceItem[];
}

export interface InvoiceItem {
  id: number;
  invoice_id: number;
  consignment_id: number;
  consignment_no: string;
  booking_date: string;
  destination_name: string;
  dest_code: string;
  mode: string;
  pieces: number;
  billable_weight: number;
  amount: number;
}

// backend/prisma/seed.ts
import { PrismaClient } from '@prisma/client';
import bcrypt from 'bcryptjs';

const prisma = new PrismaClient();

async function main() {
  console.log('🌱 Starting Courier ERP Database Seeding...');

  // 1. Seed Operational Zones
  const zones = [
    { code: 'ZONE1', name: 'North Zone', description: 'Delhi, Punjab, Haryana, UP, Rajasthan' },
    { code: 'ZONE2', name: 'West Zone', description: 'Maharashtra, Gujarat, Goa' },
    { code: 'ZONE3', name: 'South Zone', description: 'Karnataka, Tamil Nadu, Kerala, Andhra Pradesh' },
    { code: 'ZONE4', name: 'East Zone', description: 'West Bengal, Bihar, Jharkhand, Odisha, North East' },
    { code: 'ZONE5', name: 'Central Zone', description: 'Chhattisgarh, Madhya Pradesh' },
  ];

  for (const z of zones) {
    await prisma.operationalZone.upsert({
      where: { code: z.code },
      update: {},
      create: z,
    });
  }
  console.log('✓ Operational Zones seeded.');

  // 2. Seed States
  const zoneMap = await prisma.operationalZone.findMany();
  const getZoneId = (code: string) => zoneMap.find((z) => z.code === code)?.id || 1;

  const states = [
    { code: 'CG', name: 'Chhattisgarh', zoneId: getZoneId('ZONE5'), gstCode: '22' },
    { code: 'MH', name: 'Maharashtra', zoneId: getZoneId('ZONE2'), gstCode: '27' },
    { code: 'GJ', name: 'Gujarat', zoneId: getZoneId('ZONE2'), gstCode: '24' },
    { code: 'DL', name: 'Delhi', zoneId: getZoneId('ZONE1'), gstCode: '07' },
    { code: 'UP', name: 'Uttar Pradesh', zoneId: getZoneId('ZONE1'), gstCode: '09' },
    { code: 'WB', name: 'West Bengal', zoneId: getZoneId('ZONE4'), gstCode: '19' },
    { code: 'JH', name: 'Jharkhand', zoneId: getZoneId('ZONE4'), gstCode: '20' },
    { code: 'MP', name: 'Madhya Pradesh', zoneId: getZoneId('ZONE5'), gstCode: '23' },
  ];

  for (const s of states) {
    await prisma.state.upsert({
      where: { code: s.code },
      update: {},
      create: {
        code: s.code,
        name: s.name,
        zone_id: s.zoneId,
        gst_code: s.gstCode,
      },
    });
  }
  console.log('✓ States seeded with GST codes.');

  // 3. Seed Master Destinations
  const stateMap = await prisma.state.findMany();
  const getStateId = (code: string) => stateMap.find((s) => s.code === code)?.id || 1;

  const destinations = [
    { code: 'RAI', name: 'RAIPUR', stateCode: 'CG', pincode: '492001', isHub: true },
    { code: 'BSP', name: 'BILASPUR', stateCode: 'CG', pincode: '495001', isHub: false },
    { code: 'DUR', name: 'DURG', stateCode: 'CG', pincode: '491001', isHub: false },
    { code: 'BOM', name: 'MUMBAI', stateCode: 'MH', pincode: '400001', isHub: true },
    { code: 'PNE', name: 'PUNE', stateCode: 'MH', pincode: '411001', isHub: true },
    { code: 'NAG', name: 'NAGPUR', stateCode: 'MH', pincode: '440001', isHub: true },
    { code: 'SUR', name: 'SURAT', stateCode: 'GJ', pincode: '395001', isHub: true },
    { code: 'AMD', name: 'AHMEDABAD', stateCode: 'GJ', pincode: '380001', isHub: true },
    { code: 'DEL', name: 'DELHI', stateCode: 'DL', pincode: '110001', isHub: true },
    { code: 'CCU', name: 'KOLKATA', stateCode: 'WB', pincode: '700001', isHub: true },
  ];

  for (const d of destinations) {
    await prisma.destination.upsert({
      where: { code: d.code },
      update: {},
      create: {
        code: d.code,
        name: d.name,
        state_id: getStateId(d.stateCode),
        pincode: d.pincode,
        is_hub: d.isHub,
        is_active: true,
      },
    });
  }
  console.log('✓ Master Destinations seeded.');

  // 4. Seed SaaS Subscription Plans
  const plans = [
    {
      name: 'Starter Plan',
      description: 'Ideal for independent single-counter courier agencies.',
      price: 4999.0,
      billing_cycle: 'yearly',
      duration_days: 365,
      max_branches: 1,
      max_users: 3,
      booking_quota: 1000,
      features_json: JSON.stringify({ thermal_printing: true, gst_invoicing: true, api_access: false }),
    },
    {
      name: 'Professional Plan',
      description: 'Built for growing multi-branch regional logistics franchises.',
      price: 12000.0,
      billing_cycle: 'yearly',
      duration_days: 365,
      max_branches: 5,
      max_users: 15,
      booking_quota: 10000,
      features_json: JSON.stringify({ thermal_printing: true, gst_invoicing: true, mobile_pod: true, gstr1_export: true }),
    },
    {
      name: 'Enterprise Plan',
      description: 'Full unmetered power for statewide cargo & courier carriers.',
      price: 29999.0,
      billing_cycle: 'yearly',
      duration_days: 365,
      max_branches: 999,
      max_users: 999,
      booking_quota: 999999,
      features_json: JSON.stringify({ thermal_printing: true, gst_invoicing: true, mobile_pod: true, gstr1_export: true, api_access: true }),
    },
  ];

  for (const p of plans) {
    const existing = await prisma.subscriptionPlan.findFirst({ where: { name: p.name } });
    if (!existing) {
      await prisma.subscriptionPlan.create({ data: p });
    }
  }
  console.log('✓ SaaS Subscription Plans seeded.');

  // 5. Seed Super Administrator
  const adminPasswordHash = await bcrypt.hash('admin123', 10);
  await prisma.user.upsert({
    where: { email: 'admin@kesari.com' },
    update: {},
    create: {
      tenant_id: null,
      branch_id: null,
      name: 'Super Administrator',
      email: 'admin@kesari.com',
      phone: '9900000000',
      password_hash: adminPasswordHash,
      role: 'superadmin',
      status: 'active',
    },
  });
  console.log('✓ Super Admin created (admin@kesari.com / admin123).');

  // 6. Seed Sample Agency (Raipur Branch from live domain data)
  const validityStart = new Date();
  const validityEnd = new Date();
  validityEnd.setFullYear(validityEnd.getFullYear() + 1);

  const agency = await prisma.tenant.upsert({
    where: { slug: 'raipur' },
    update: {},
    create: {
      uuid: 'tenant-raipur-001',
      slug: 'raipur',
      agency_code: '26',
      name: 'Keshri Express (Raipur)',
      trade_name: 'Shree Maruti Courier Agency',
      contact_person: 'Branch Administrator',
      phone: '9893000000',
      email: 'shreemaruti@raipur.com',
      address: 'Pandri Main Road, Raipur, Chhattisgarh',
      city: 'Raipur',
      state: 'Chhattisgarh',
      pincode: '492001',
      pan_no: 'AAACK1234F',
      gstin: '22AAACK1234F1Z5',
      status: 'active',
      plan_type: 'Yearly',
      plan_price: 12000.0,
      grace_period_days: 7,
      validity_start: validityStart,
      validity_end: validityEnd,
      brand_color: '#1a73e8',
    },
  });
  console.log(`✓ Agency Tenant seeded: ${agency.name} (Code: ${agency.agency_code})`);

  // 7. Seed Primary Branch for Raipur
  let branch = await prisma.branch.findFirst({
    where: { tenant_id: agency.id, branch_code: 'RAI-01' },
  });
  if (!branch) {
    branch = await prisma.branch.create({
      data: {
        tenant_id: agency.id,
        branch_code: 'RAI-01',
        branch_name: 'Raipur Main Counter (Pandri)',
        address: 'Plot 12, Pandri Commercial Complex',
        city: 'Raipur',
        state: 'Chhattisgarh',
        pincode: '492001',
        phone: '0771-4001234',
        status: 'active',
      },
    });
  }
  console.log(`✓ Agency Branch seeded: ${branch.branch_name}`);

  // 8. Seed Agency Users Across Roles
  const staffAccounts = [
    {
      name: 'Shree Maruti Raipur Admin',
      email: 'shreemaruti@raipur.com',
      password: 'india3101',
      role: 'agency_admin',
    },
    {
      name: 'Raipur Branch Manager',
      email: 'manager.raipur@kesari.com',
      password: 'manager123',
      role: 'branch_manager',
    },
    {
      name: 'Booking Counter Operator',
      email: 'ops.raipur@kesari.com',
      password: 'ops123',
      role: 'operations_staff',
    },
    {
      name: 'Field Delivery Executive',
      email: 'delivery.raipur@kesari.com',
      password: 'delivery123',
      role: 'delivery_personnel',
    },
    {
      name: 'Branch Accountant',
      email: 'accounts.raipur@kesari.com',
      password: 'accounts123',
      role: 'accounts_staff',
    },
  ];

  for (const staff of staffAccounts) {
    const hash = await bcrypt.hash(staff.password, 10);
    await prisma.user.upsert({
      where: { email: staff.email },
      update: {},
      create: {
        tenant_id: agency.id,
        branch_id: branch.id,
        name: staff.name,
        email: staff.email,
        phone: '9893000000',
        password_hash: hash,
        role: staff.role,
        status: 'active',
      },
    });
  }
  console.log('✓ Agency staff accounts seeded for all operational roles.');

  // 9. Seed Corporate B2B Clients
  const clientsData = [
    {
      code: '72',
      name: 'VANDANA ISPAT PVT LTD',
      person: 'Rajesh Sharma',
      phone: '9893112233',
      email: 'accounts@vandanaispat.com',
      address: 'Plot 45, Urla Industrial Area',
      city: 'Raipur',
      state: 'Chhattisgarh',
      pincode: '492003',
      gstin: '22AABCV1344A1Z5',
      pan: 'AABCV1344A',
      fuel: 10.0,
    },
    {
      code: '92',
      name: 'RIGHTWAYS ENRICHING SUPPLY CHAIN SOLUTION',
      person: 'Amit Roy',
      phone: '2040147474',
      email: 'rightways@gmail.com',
      address: 'Office 201, Plot 10, Vimal Nagar',
      city: 'Pune',
      state: 'Maharashtra',
      pincode: '411014',
      gstin: '27AABCR2668G1ZL',
      pan: 'AABCR2668G',
      fuel: 12.0,
    },
    {
      code: '91',
      name: 'Akshamaala Solutions Pvt. Ltd',
      person: 'Sunil Verma',
      phone: '8851680780',
      email: 'akshamaala@raipur.com',
      address: 'Logix Technova, Tower B-415, Sector 132',
      city: 'Noida',
      state: 'Uttar Pradesh',
      pincode: '201304',
      gstin: '09AAACL9752D1ZQ',
      pan: 'AAACL9752D',
      fuel: 10.0,
    },
    {
      code: '46',
      name: 'CASH SALE DIGITAL PAYMENT DESK',
      person: 'Walk-in Counter',
      phone: '9893000000',
      email: 'cash@maruti.com',
      address: 'Branch Counter Desk',
      city: 'Raipur',
      state: 'Chhattisgarh',
      pincode: '492001',
      gstin: '',
      pan: '',
      fuel: 0.0,
    },
  ];

  for (const c of clientsData) {
    const existingClient = await prisma.client.findFirst({
      where: { tenant_id: agency.id, client_code: c.code },
    });
    if (!existingClient) {
      await prisma.client.create({
        data: {
          tenant_id: agency.id,
          client_code: c.code,
          name: c.name,
          contact_person: c.person,
          phone: c.phone,
          email: c.email,
          address: c.address,
          city: c.city,
          state: c.state,
          pincode: c.pincode,
          gstin: c.gstin,
          pan_no: c.pan,
          fuel_surcharge_percent: c.fuel,
          status: 'active',
        },
      });
    }
  }
  console.log('✓ Corporate B2B Clients seeded.');

  // 10. Seed Agency Tariffs
  const westZone = await prisma.operationalZone.findUnique({ where: { code: 'ZONE2' } });
  if (westZone) {
    const existingTariff = await prisma.tariff.findFirst({
      where: {
        tenant_id: agency.id,
        send_by: 'Air',
        mode: 'Dox',
        zone_id: westZone.id,
        client_id: null,
      },
    });

    if (!existingTariff) {
      const tariff = await prisma.tariff.create({
        data: {
          tenant_id: agency.id,
          client_id: null,
          send_by: 'Air',
          mode: 'Dox',
          zone_id: westZone.id,
          additional_weight_step: 0.5,
          additional_weight_rate: 30.0,
          slabs: {
            create: [
              { weight_from: 0.0, weight_to: 0.25, rate: 40.0 },
              { weight_from: 0.25, weight_to: 0.5, rate: 60.0 },
              { weight_from: 0.5, weight_to: 1.0, rate: 90.0 },
            ],
          },
        },
      });
      console.log(`✓ Tariff seeded with slabs (ID: ${tariff.id})`);
    }
  }

  // 11. Seed Agency Primary Bank Account
  const existingBank = await prisma.agencyBankAccount.findFirst({
    where: { tenant_id: agency.id },
  });
  if (!existingBank) {
    await prisma.agencyBankAccount.create({
      data: {
        tenant_id: agency.id,
        bank_name: 'HDFC Bank Ltd',
        account_no: '50200012345678',
        account_holder_name: 'KESHRI EXPRESS COURIER SERVICES',
        ifsc_code: 'HDFC0001234',
        branch_name: 'Pandri Branch, Raipur',
        is_primary: true,
      },
    });
    console.log('✓ Agency Settlement Bank Account seeded.');
  }

  // 12. Seed Sample Consignments
  const clientVandana = await prisma.client.findFirst({
    where: { tenant_id: agency.id, client_code: '72' },
  });
  const destSurat = await prisma.destination.findUnique({ where: { code: 'SUR' } });
  const destMumbai = await prisma.destination.findUnique({ where: { code: 'BOM' } });

  if (clientVandana && destSurat && destMumbai) {
    const sampleBookings = [
      {
        consignment_no: '25011200064307',
        recipient_name: 'PATEL CHEMICALS',
        recipient_phone: '9825000000',
        recipient_address: 'Plot 401, GIDC Pandesara, Surat, Gujarat',
        destination_id: destSurat.id,
        mode: 'Dox',
        send_by: 'Air',
        pieces: 1,
        dead_weight: 0.1,
        billable_weight: 0.1,
        subtotal: 40.0,
        total_amount: 40.0,
        status: 'booked',
      },
      {
        consignment_no: '25011200064296',
        recipient_name: 'RELIANCE LOGISTICS PVT LTD',
        recipient_phone: '9820000000',
        recipient_address: '14th Floor, Maker Chambers IV, Nariman Point, Mumbai',
        destination_id: destMumbai.id,
        mode: 'Dox',
        send_by: 'Air',
        pieces: 1,
        dead_weight: 0.25,
        billable_weight: 0.25,
        subtotal: 40.0,
        total_amount: 40.0,
        status: 'in_transit',
      },
    ];

    for (const b of sampleBookings) {
      await prisma.consignment.upsert({
        where: { consignment_no: b.consignment_no },
        update: {},
        create: {
          tenant_id: agency.id,
          branch_id: branch.id,
          booking_date: new Date(),
          consignment_no: b.consignment_no,
          client_id: clientVandana.id,
          sender_name: clientVandana.name,
          sender_phone: clientVandana.phone || '9893112233',
          recipient_name: b.recipient_name,
          recipient_phone: b.recipient_phone,
          recipient_address: b.recipient_address,
          destination_id: b.destination_id,
          mode: b.mode,
          send_by: b.send_by,
          pieces: b.pieces,
          dead_weight: b.dead_weight,
          billable_weight: b.billable_weight,
          subtotal: b.subtotal,
          total_amount: b.total_amount,
          status: b.status,
        },
      });
    }
    console.log('✓ Sample Consignments seeded.');
    // 12. Seed Customer User for Client
    const customerPasswordHash = await bcrypt.hash('customer123', 10);
    await prisma.user.upsert({
      where: { email: 'customer@acme.com' },
      update: {},
      create: {
        tenant_id: agency.id,
        name: 'Rajesh Sharma (ACME Corp)',
        email: 'customer@acme.com',
        phone: '9820011223',
        password_hash: customerPasswordHash,
        role: 'customer',
        status: 'active',
      },
    });
    console.log('✓ Customer user seeded: customer@acme.com / customer123');

    // 13. Seed Sample Support Tickets
    await prisma.ticket.upsert({
      where: { ticket_number: 'TKT-1-1001' },
      update: {},
      create: {
        tenant_id: agency.id,
        ticket_number: 'TKT-1-1001',
        category: 'destination_request',
        subject: 'Request to add new Hub: Durg (CG)',
        description: 'Please add Durg pin 491001 under Central Zone CG for industrial dispatch.',
        destination_name: 'DURG',
        pincode: '491001',
        priority: 'high',
        status: 'open',
      },
    });

    await prisma.ticket.upsert({
      where: { ticket_number: 'TKT-1-1002' },
      update: {},
      create: {
        tenant_id: agency.id,
        ticket_number: 'TKT-1-1002',
        category: 'billing',
        subject: 'Inquiry on SAC 996812 B2B GST Rate Calculation',
        description: 'Confirming if intra-state CGST+SGST is 9%+9% for B2B invoices.',
        priority: 'normal',
        status: 'resolved',
        admin_notes: 'Confirmed. SAC 996812 carries 18% standard GST (9% CGST + 9% SGST or 18% IGST).',
        resolved_at: new Date(),
      },
    });
    console.log('✓ Sample Tickets seeded.');

    // 14. Seed Audit Logs
    await prisma.auditLog.create({
      data: {
        tenant_id: agency.id,
        action: 'AGENCY_ONBOARDING_APPROVED',
        details_json: JSON.stringify({ agencyCode: '26', approvedBy: 'Super Admin' }),
        ip_address: '127.0.0.1',
      },
    });
    console.log('✓ Sample Audit Log seeded.');
  }

  console.log('🎉 Database Seeding Completed Successfully!');

}

main()
  .catch((e) => {
    console.error('❌ Seeding error:', e);
    process.exit(1);
  })
  .finally(async () => {
    await prisma.$disconnect();
  });

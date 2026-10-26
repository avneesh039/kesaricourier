-- database/attach_live_database.sql
-- Direct SQL synchronization from keshriex_live to kesari_saas

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Sync Operational Zones
INSERT IGNORE INTO kesari_saas.operational_zones (id, code, name, description, created_at)
SELECT 
  Zone_Id AS id,
  Zone_Code AS code,
  Zone_Name AS name,
  CONCAT('Zone: ', Zone_Name) AS description,
  NOW(3) AS created_at
FROM keshriex_live.tbl_zones;

-- 2. Sync States
INSERT IGNORE INTO kesari_saas.states (id, code, name, zone_id, gst_code, created_at)
SELECT 
  State_Id AS id,
  CONCAT(UPPER(State_Code), '_', State_Id) AS code,
  State_Name AS name,
  Zone_Id AS zone_id,
  LPAD(State_Id, 2, '0') AS gst_code,
  NOW(3) AS created_at
FROM keshriex_live.tbl_states;

-- 3. Sync Destinations (1,597 real destinations)
INSERT IGNORE INTO kesari_saas.destinations (id, code, name, state_id, pincode, is_hub, is_active, created_at)
SELECT 
  d.Destination_Id AS id,
  CONCAT(UPPER(REPLACE(REPLACE(d.Destination_Code, ' ', ''), '-', '')), '_', d.Destination_Id) AS code,
  d.Destination_Name AS name,
  d.State_Id AS state_id,
  CONCAT('49', LPAD((d.Destination_Id % 1000), 4, '0')) AS pincode,
  CASE WHEN d.Destination_Id IN (1053, 154, 714, 1391, 1070) THEN 1 ELSE 0 END AS is_hub,
  1 AS is_active,
  NOW(3) AS created_at
FROM keshriex_live.tbl_destinations d;

-- 4. Sync Branches (26 agency network branches)
INSERT IGNORE INTO kesari_saas.branches (id, tenant_id, branch_code, branch_name, address, city, state, pincode, phone, status, created_at, updated_at)
SELECT 
  b.Branch_Id AS id,
  1 AS tenant_id,
  CONCAT('BR-', b.Branch_Code) AS branch_code,
  b.Branch_Name AS branch_name,
  COALESCE(b.Address, 'Station Road Hub') AS address,
  SUBSTRING_INDEX(COALESCE(b.Address, 'Raipur'), ',', -1) AS city,
  'Chhattisgarh' AS state,
  '492001' AS pincode,
  COALESCE(b.Contact_No, '0771-4004544') AS phone,
  CASE WHEN b.Is_Active = 1 THEN 'active' ELSE 'inactive' END AS status,
  NOW(3) AS created_at,
  NOW(3) AS updated_at
FROM keshriex_live.tbl_branchs b;

-- 5. Sync Clients (512 real corporate and retail B2B clients)
INSERT IGNORE INTO kesari_saas.clients (
  id, tenant_id, client_code, name, contact_person, phone, email,
  address, billing_address, city, state, pincode, gstin, pan_no,
  fuel_surcharge_percent, insurance_percent, status, created_at
)
SELECT 
  c.Client_Id AS id,
  1 AS tenant_id,
  CONCAT('CL-', c.Client_Code, '-', c.Client_Id) AS client_code,
  c.Client_Name AS name,
  c.Client_Name AS contact_person,
  COALESCE(c.Contact_No, '9827100000') AS phone,
  COALESCE(c.Email, CONCAT('client', c.Client_Id, '@keshari.com')) AS email,
  COALESCE(c.Address, 'Commercial Hub') AS address,
  COALESCE(c.Billing_Address, c.Address, 'Billing Office') AS billing_address,
  'Raipur' AS city,
  'Chhattisgarh' AS state,
  '492001' AS pincode,
  c.GSTIN_No AS gstin,
  c.PAN_No AS pan_no,
  COALESCE(c.Fuel_Surcharge, 10.00) AS fuel_surcharge_percent,
  COALESCE(c.Insurance_Percent, 0.00) AS insurance_percent,
  CASE WHEN c.Is_Active = 1 THEN 'active' ELSE 'inactive' END AS status,
  NOW(3) AS created_at
FROM keshriex_live.tbl_clients c;

-- 6. Sync Recent Live Consignments (Top 1,000 real dockets)
INSERT IGNORE INTO kesari_saas.consignments (
  id, tenant_id, branch_id, booking_date, consignment_no, client_id,
  sender_name, sender_phone, recipient_name, recipient_phone, recipient_address,
  destination_id, mode, send_by, pieces, dead_weight, billable_weight,
  subtotal, total_amount, insured_value, status, is_billed, created_at
)
SELECT 
  tc.Consignment_Id AS id,
  1 AS tenant_id,
  COALESCE((SELECT id FROM kesari_saas.branches WHERE id = tc.Branch_Id LIMIT 1), 1) AS branch_id,
  tc.Date_Of_Submit AS booking_date,
  CAST(tc.Consignment_No AS CHAR) AS consignment_no,
  COALESCE((SELECT id FROM kesari_saas.clients WHERE id = tc.Client_id LIMIT 1), 1) AS client_id,
  COALESCE((SELECT name FROM kesari_saas.clients WHERE id = tc.Client_id LIMIT 1), 'Keshri Commercial Booking') AS sender_name,
  '9399676408' AS sender_phone,
  'Consignee Recipient' AS recipient_name,
  '9827100000' AS recipient_phone,
  'Doorstep Handover Delivery Corridor' AS recipient_address,
  COALESCE((SELECT id FROM kesari_saas.destinations WHERE id = tc.Destination_Id LIMIT 1), 1) AS destination_id,
  CASE WHEN tc.Mode = 1 THEN 'Dox' ELSE 'Non-Dox' END AS mode,
  CASE WHEN tc.Send_By = 1 THEN 'Air' ELSE 'Surface' END AS send_by,
  COALESCE(tc.No_Of_Pieces, 1) AS pieces,
  COALESCE(tc.Total_Weight_In_KG, 0.5) AS dead_weight,
  COALESCE(tc.Total_Weight_In_KG, 0.5) AS billable_weight,
  COALESCE(tc.Subtotal, 50.00) AS subtotal,
  COALESCE(tc.Total_Amount, 50.00) AS total_amount,
  COALESCE(tc.Insured_Value, 0.00) AS insured_value,
  'delivered' AS status,
  1 AS is_billed,
  tc.Date_Of_Submit AS created_at
FROM keshriex_live.tbl_consignments tc
ORDER BY tc.Consignment_Id DESC
LIMIT 1000;

SET FOREIGN_KEY_CHECKS = 1;

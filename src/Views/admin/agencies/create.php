<?php
// src/Views/admin/agencies/create.php - Super Admin Manual Agency & Franchise Creation Form
$pageTitle = 'Create New Agency / Franchise';
$activeNav = 'agencies';
$error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_error']);

ob_start();
?>

<div class="page-header">
    <div>
        <a href="/admin/agencies" style="font-size:13px; color:var(--text-secondary); text-decoration:none;">← Back to Agency Directory</a>
        <h1 class="page-title" style="margin-top:6px;">🏢 Add New Agency / Franchise Branch</h1>
        <p style="color:var(--text-secondary); font-size:13px; margin-top:4px;">
            Directly provision an agency portal with branch code, login credentials, and subscription plan.
        </p>
    </div>
</div>

<?php if ($error): ?>
    <div style="background:#fce8e6; border-left:4px solid var(--google-red); color:#c5221f; padding:14px 18px; border-radius:4px; margin-bottom:20px; font-size:13px;">
        ⚠️ <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<form method="POST" action="/admin/agencies/new" id="agencyCreateForm">
    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:24px;">
        <!-- Left: Agency & Franchise Details -->
        <div>
            <!-- Section 1: Business Profile -->
            <div class="g-card" style="margin-bottom:20px;">
                <h3 style="font-size:15px; margin-bottom:16px; color:var(--brand-primary); display:flex; align-items:center; gap:8px;">
                    <?= Icon::svg('agencies') ?> 1. Franchise Business Profile
                </h3>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-group" style="grid-column:span 2;">
                        <label class="form-label">Agency / Hub Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Shree Maruti Courier (Bilaspur Hub)" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Trade / Display Name</label>
                        <input type="text" name="trade_name" class="form-control" placeholder="e.g. Keshri Express Bilaspur">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Branch / Agency Code *</label>
                        <input type="text" name="agency_code" class="form-control" placeholder="e.g. 27" value="<?= htmlspecialchars($nextBranchCode ?? '') ?>" required style="font-weight:700;">
                        <small style="color:var(--text-secondary); font-size:11px;">Numeric unique branch identifier.</small>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Owner / Contact Person *</label>
                        <input type="text" name="contact_person" class="form-control" placeholder="e.g. Rajesh Sharma" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mobile Number *</label>
                        <input type="tel" name="phone" class="form-control" placeholder="10-digit mobile" required pattern="[0-9]{10}">
                    </div>

                    <div class="form-group" style="grid-column:span 2;">
                        <label class="form-label">Official Email (Username for Login) *</label>
                        <input type="email" name="email" class="form-control" placeholder="e.g. shreemaruti@bilaspur.com" required>
                    </div>

                    <div class="form-group" style="grid-column:span 2;">
                        <label class="form-label">Office Address *</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Shop / Office No, Complex, Street" required></textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label">City *</label>
                        <input type="text" name="city" class="form-control" placeholder="e.g. Bilaspur" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">State *</label>
                        <input type="text" name="state" class="form-control" placeholder="e.g. Chhattisgarh" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Pincode *</label>
                        <input type="text" name="pincode" class="form-control" placeholder="6-digit pincode" required maxlength="6" pattern="[0-9]{6}">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Subdomain Slug *</label>
                        <div style="display:flex; align-items:center;">
                            <input type="text" name="slug" class="form-control" placeholder="e.g. bilaspur" required pattern="[a-z0-9\-]+" style="border-top-right-radius:0; border-bottom-right-radius:0;">
                            <span style="background:#e8f0fe; color:#1967d2; padding:9px 12px; font-size:12px; border:1px solid #dadce0; border-left:none; border-top-right-radius:6px; border-bottom-right-radius:6px;">.localhost</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Tax & Compliance Identification -->
            <div class="g-card">
                <h3 style="font-size:15px; margin-bottom:16px; color:var(--brand-primary); display:flex; align-items:center; gap:8px;">
                    <?= Icon::svg('kyc') ?> 2. Tax Identification
                </h3>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px;">
                    <div class="form-group">
                        <label class="form-label">PAN Number</label>
                        <input type="text" name="pan_no" class="form-control" placeholder="ABCDE1234F" style="text-transform:uppercase;">
                    </div>

                    <div class="form-group">
                        <label class="form-label">GSTIN (Optional)</label>
                        <input type="text" name="gstin" class="form-control" placeholder="22AAAAA0000A1Z5" style="text-transform:uppercase;">
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Login, Subscription Plan & Status Controls -->
        <div>
            <!-- Section 3: Initial Login Credentials -->
            <div class="g-card" style="margin-bottom:20px;">
                <h3 style="font-size:15px; margin-bottom:14px; display:flex; align-items:center; gap:8px;">
                    <?= Icon::svg('lock') ?> Initial Agency Login
                </h3>

                <div class="form-group" style="margin-bottom:12px;">
                    <label class="form-label">Admin Password *</label>
                    <input type="text" name="password" class="form-control" value="kesari@<?= random_int(1000, 9999) ?>" required minlength="6" style="font-family:monospace; font-weight:700;">
                    <small style="color:var(--text-secondary); font-size:11px;">Share this credential with the branch owner.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Portal Status Switch *</label>
                    <select name="status" class="form-control" required>
                        <option value="active" selected>Active (Full Access Enabled)</option>
                        <option value="suspended">Suspended (Login Locked)</option>
                        <option value="pending_kyc">Pending KYC Review</option>
                    </select>
                </div>
            </div>

            <!-- Section 4: Subscription & Billing Plan -->
            <div class="g-card" style="margin-bottom:20px;">
                <h3 style="font-size:15px; margin-bottom:14px; display:flex; align-items:center; gap:8px;">
                    <?= Icon::svg('receipt-tax') ?> Subscription Plan
                </h3>

                <div class="form-group" style="margin-bottom:12px;">
                    <label class="form-label">Billing Cycle / Plan Type *</label>
                    <select name="plan_type" id="plan_type" class="form-control" required onchange="onPlanCycleChange(this.value)">
                        <option value="Monthly">Monthly Plan (30 Days)</option>
                        <option value="Quarterly">Quarterly Plan (90 Days)</option>
                        <option value="Half-Yearly">Half-Yearly Plan (180 Days)</option>
                        <option value="Yearly" selected>Yearly Plan (365 Days)</option>
                        <option value="Custom">Custom / Lifetime Plan</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom:12px;">
                    <label class="form-label">Plan Fee (₹) *</label>
                    <input type="number" step="100" name="plan_price" id="plan_price" class="form-control" value="12000" required>
                </div>

                <div class="form-group" style="margin-bottom:12px;">
                    <label class="form-label">Subscription Start Date *</label>
                    <input type="date" name="validity_start" id="validity_start" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="form-group" style="margin-bottom:12px;">
                    <label class="form-label">Subscription Expiry Date *</label>
                    <input type="date" name="validity_end" id="validity_end" class="form-control" value="<?= date('Y-m-d', strtotime('+365 days')) ?>" required>
                </div>

                <div class="form-group" style="margin-bottom:12px;">
                    <label class="form-label">Grace Period (Days) *</label>
                    <input type="number" name="grace_period_days" class="form-control" value="7" required min="0" max="60">
                    <small style="color:var(--text-secondary); font-size:11px;">Agency is locked when expiry date + grace period passes.</small>
                </div>

                <div class="form-group" style="margin-bottom:12px;">
                    <label class="form-label">Initial Payment Status</label>
                    <select name="payment_status" class="form-control">
                        <option value="paid" selected>Full Payment Received</option>
                        <option value="partial">Partially Paid</option>
                        <option value="pending">Payment Pending (Due)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Amount Received Today (₹)</label>
                    <input type="number" step="100" name="paid_amount" class="form-control" value="12000">
                </div>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary" style="width:100%; padding:14px; font-size:15px; font-weight:600; display:flex; align-items:center; justify-content:center; gap:8px;">
                <?= Icon::svg('check') ?> Create & Provision Agency
            </button>
        </div>
    </div>
</form>

<script>
function onPlanCycleChange(plan) {
    var start = new Date(document.getElementById('validity_start').value || new Date());
    var endInput = document.getElementById('validity_end');
    var priceInput = document.getElementById('plan_price');

    if (plan === 'Monthly') {
        start.setDate(start.getDate() + 30);
        priceInput.value = '1500';
    } else if (plan === 'Quarterly') {
        start.setDate(start.getDate() + 90);
        priceInput.value = '4000';
    } else if (plan === 'Half-Yearly') {
        start.setDate(start.getDate() + 180);
        priceInput.value = '7500';
    } else if (plan === 'Yearly') {
        start.setDate(start.getDate() + 365);
        priceInput.value = '12000';
    } else {
        start.setDate(start.getDate() + 730);
        priceInput.value = '25000';
    }

    endInput.value = start.toISOString().split('T')[0];
}
</script>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/src/Views/layout.php';
?>

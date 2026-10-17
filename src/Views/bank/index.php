<?php
// src/Views/bank/index.php - Agency Bank Accounts Master View
$pageTitle = 'Bank Account Settings';
$activeNav = 'bank';

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

ob_start();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">🏦 Agency Bank Accounts</h1>
        <p style="color:var(--text-secondary); font-size:13px; margin-top:4px;">
            Configure official banking details printed on GST Tax Invoices for client NEFT/RTGS/IMPS remittances.
        </p>
    </div>
</div>

<?php if ($flashSuccess): ?>
    <div style="background:#e6f4ea; border-left:4px solid var(--google-green); color:#137333; padding:12px 18px; border-radius:4px; margin-bottom:20px; font-size:13px;">
        <?= htmlspecialchars($flashSuccess) ?>
    </div>
<?php endif; ?>

<?php if ($flashError): ?>
    <div style="background:#fce8e6; border-left:4px solid var(--google-red); color:#c5221f; padding:12px 18px; border-radius:4px; margin-bottom:20px; font-size:13px;">
        <?= htmlspecialchars($flashError) ?>
    </div>
<?php endif; ?>

<div style="display:grid; grid-template-columns: 1.8fr 1.2fr; gap:24px; align-items:start;">
    
    <!-- Registered Bank Accounts List -->
    <div>
        <div class="g-card">
            <h2 style="font-size:16px; margin-bottom:16px;">Registered Settlement Accounts</h2>

            <?php if (empty($accounts)): ?>
                <div style="text-align:center; padding:32px; color:var(--text-secondary);">
                    No bank accounts registered yet. Add your settlement account on the right.
                </div>
            <?php else: ?>
                <div style="display:flex; flex-direction:column; gap:16px;">
                    <?php foreach ($accounts as $acc): ?>
                        <div style="border:1px solid <?= $acc['is_primary'] ? 'var(--google-blue)' : '#dadce0' ?>; border-radius:8px; padding:16px; background:<?= $acc['is_primary'] ? '#f8fafd' : '#ffffff' ?>; position:relative;">
                            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                                <div>
                                    <div style="font-size:16px; font-weight:700; color:#202124;">
                                        <?= htmlspecialchars($acc['bank_name']) ?>
                                    </div>
                                    <div style="font-size:12px; color:var(--text-secondary); margin-top:2px;">
                                        Branch: <?= htmlspecialchars($acc['branch_name']) ?>
                                    </div>
                                </div>
                                <div>
                                    <?php if ($acc['is_primary']): ?>
                                        <span class="g-chip chip-success" style="font-size:11px;">★ Primary Invoice Account</span>
                                    <?php else: ?>
                                        <form method="POST" action="/bank-accounts/<?= $acc['id'] ?>/make-primary">
                                            <button type="submit" class="btn btn-outlined" style="padding:3px 10px; font-size:11px;">
                                                Set as Primary
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:12px; margin-top:14px; padding-top:12px; border-top:1px solid #f1f3f4; font-size:12px;">
                                <div>
                                    <span style="color:var(--text-secondary); display:block; font-size:11px;">Account Number</span>
                                    <strong style="font-family:monospace; font-size:14px;"><?= htmlspecialchars($acc['account_no']) ?></strong>
                                </div>
                                <div>
                                    <span style="color:var(--text-secondary); display:block; font-size:11px;">IFSC Code</span>
                                    <strong style="font-family:monospace; font-size:14px;"><?= htmlspecialchars($acc['ifsc_code']) ?></strong>
                                </div>
                                <div>
                                    <span style="color:var(--text-secondary); display:block; font-size:11px;">Beneficiary Name</span>
                                    <strong style="font-size:13px;"><?= htmlspecialchars($acc['account_holder_name']) ?></strong>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add New Bank Account Form -->
    <div>
        <div class="g-card">
            <h2 style="font-size:16px; margin-bottom:16px;">+ Add New Bank Account</h2>
            
            <form method="POST" action="/bank-accounts">
                <div class="form-group">
                    <label class="form-label">Bank Name *</label>
                    <input type="text" name="bank_name" class="form-control" placeholder="e.g. State Bank of India, HDFC Bank" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Account Number *</label>
                    <input type="text" name="account_no" class="form-control" placeholder="Enter bank account number" style="font-family:monospace;" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Beneficiary / Account Holder Name *</label>
                    <input type="text" name="account_holder_name" class="form-control" placeholder="Exact legal name on bank records" required>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px;">
                    <div class="form-group">
                        <label class="form-label">IFSC Code *</label>
                        <input type="text" name="ifsc_code" class="form-control" placeholder="e.g. HDFC0001234" style="font-family:monospace; text-transform:uppercase;" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Branch Name *</label>
                        <input type="text" name="branch_name" class="form-control" placeholder="e.g. Pandri Branch" required>
                    </div>
                </div>

                <div class="form-group" style="margin-top:8px;">
                    <label style="display:flex; align-items:center; gap:8px; font-size:13px; cursor:pointer;">
                        <input type="checkbox" name="is_primary" value="1" <?= empty($accounts) ? 'checked' : '' ?>>
                        <span>Set as Primary Billing Account for GST Invoices</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; margin-top:16px;">
                    Save Bank Account
                </button>
            </form>
        </div>
    </div>

</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/src/Views/layout.php';
?>

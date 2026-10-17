<?php
// src/Views/admin/agencies/kyc-review.php - Super Admin KYC Review Interface
$pageTitle = 'Review Agency KYC | ' . $tenant['name'];
$activeNav = 'kyc';

ob_start();
?>

<div class="page-header">
    <div>
        <a href="/admin/agencies" style="font-size:13px; color:var(--text-secondary); text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
            <span style="transform:rotate(180deg); display:inline-flex;"><?= Icon::svg('chevron-right', '', 14) ?></span> Back to Agency Directory
        </a>
        <h1 class="page-title" style="margin-top:6px; display:flex; align-items:center; gap:8px;">
            <span style="color:var(--brand-primary);"><?= Icon::svg('kyc', '', 24) ?></span>
            KYC Verification: <?= htmlspecialchars($tenant['name']) ?>
        </h1>
    </div>
    <span class="g-chip chip-warning" style="font-size:13px; padding:6px 14px;">Status: Under Review</span>
</div>

<div style="display:grid; grid-template-columns: 1fr 1fr; gap:24px;">
    <!-- Left Column: Business Profile & Submitted Data -->
    <div class="g-card">
        <h2 style="font-size:16px; margin-bottom:16px;">Applicant & Company Profile</h2>

        <div style="display:flex; flex-direction:column; gap:14px; font-size:13px;">
            <div style="display:flex; justify-content:space-between; border-bottom:1px solid #f1f3f4; padding-bottom:8px;">
                <span style="color:var(--text-secondary);">Agency Legal Name:</span>
                <strong><?= htmlspecialchars($tenant['name']) ?></strong>
            </div>
            <div style="display:flex; justify-content:space-between; border-bottom:1px solid #f1f3f4; padding-bottom:8px;">
                <span style="color:var(--text-secondary);">Trade / Franchise Brand:</span>
                <span><?= htmlspecialchars($tenant['trade_name'] ?: 'N/A') ?></span>
            </div>
            <div style="display:flex; justify-content:space-between; border-bottom:1px solid #f1f3f4; padding-bottom:8px;">
                <span style="color:var(--text-secondary);">Proprietor / Contact Person:</span>
                <strong><?= htmlspecialchars($tenant['contact_person']) ?></strong>
            </div>
            <div style="display:flex; justify-content:space-between; border-bottom:1px solid #f1f3f4; padding-bottom:8px;">
                <span style="color:var(--text-secondary);">Email:</span>
                <code><?= htmlspecialchars($tenant['email']) ?></code>
            </div>
            <div style="display:flex; justify-content:space-between; border-bottom:1px solid #f1f3f4; padding-bottom:8px;">
                <span style="color:var(--text-secondary);">Phone:</span>
                <span><?= htmlspecialchars($tenant['phone']) ?></span>
            </div>
            <div style="display:flex; justify-content:space-between; border-bottom:1px solid #f1f3f4; padding-bottom:8px;">
                <span style="color:var(--text-secondary);">Operating Location:</span>
                <span><?= htmlspecialchars($tenant['city']) ?>, <?= htmlspecialchars($tenant['state']) ?> (<?= htmlspecialchars($tenant['pincode'] ?? '') ?>)</span>
            </div>
            <div style="display:flex; justify-content:space-between; border-bottom:1px solid #f1f3f4; padding-bottom:8px;">
                <span style="color:var(--text-secondary);">Registered Address:</span>
                <span><?= htmlspecialchars($tenant['address'] ?? '') ?></span>
            </div>
            <div style="display:flex; justify-content:space-between; border-bottom:1px solid #f1f3f4; padding-bottom:8px;">
                <span style="color:var(--text-secondary);">PAN Number:</span>
                <code><?= htmlspecialchars($tenant['pan_no']) ?></code>
            </div>
            <div style="display:flex; justify-content:space-between; border-bottom:1px solid #f1f3f4; padding-bottom:8px;">
                <span style="color:var(--text-secondary);">Aadhaar Number:</span>
                <code><?= htmlspecialchars($tenant['aadhaar_no']) ?></code>
            </div>
            <div style="display:flex; justify-content:space-between;">
                <span style="color:var(--text-secondary);">GSTIN:</span>
                <span><?= htmlspecialchars($tenant['gstin'] ?: 'Unregistered') ?></span>
            </div>
        </div>
    </div>

    <!-- Right Column: Uploaded Documents & Decision Actions -->
    <div>
        <div class="g-card">
            <h2 style="font-size:16px; margin-bottom:16px;">Verification Documents</h2>

            <!-- PAN Document Preview -->
            <div style="background:#f8f9fa; border:1px solid #dadce0; border-radius:10px; padding:16px; margin-bottom:16px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <div style="font-weight:600; font-size:14px; display:flex; align-items:center; gap:6px;">
                            <?= Icon::svg('reports', '', 16) ?> PAN Card Document
                        </div>
                        <div style="font-size:11px; color:var(--text-secondary); margin-top:2px;">Number: <code><?= htmlspecialchars($tenant['pan_no']) ?></code></div>
                    </div>
                    <?php if (!empty($tenant['pan_doc_url'])): ?>
                        <a href="<?= htmlspecialchars($tenant['pan_doc_url']) ?>" target="_blank" class="btn btn-outlined" style="padding:4px 12px; font-size:12px; display:inline-flex; align-items:center; gap:4px;">
                            View File ↗
                        </a>
                    <?php else: ?>
                        <span class="g-chip chip-danger">Missing</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Aadhaar Document Preview -->
            <div style="background:#f8f9fa; border:1px solid #dadce0; border-radius:10px; padding:16px; margin-bottom:24px;">
                <div style="display:flex; justify-content:space-between; align-items:center;">
                    <div>
                        <div style="font-weight:600; font-size:14px; display:flex; align-items:center; gap:6px;">
                            <?= Icon::svg('kyc', '', 16) ?> Aadhaar Card Document
                        </div>
                        <div style="font-size:11px; color:var(--text-secondary); margin-top:2px;">Number: <code><?= htmlspecialchars($tenant['aadhaar_no']) ?></code></div>
                    </div>
                    <?php if (!empty($tenant['aadhaar_doc_url'])): ?>
                        <a href="<?= htmlspecialchars($tenant['aadhaar_doc_url']) ?>" target="_blank" class="btn btn-outlined" style="padding:4px 12px; font-size:12px; display:inline-flex; align-items:center; gap:4px;">
                            View File ↗
                        </a>
                    <?php else: ?>
                        <span class="g-chip chip-danger">Missing</span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Approval / Rejection Decision Boxes -->
            <div style="display:flex; flex-direction:column; gap:16px;">
                <!-- Approve Form -->
                <form method="POST" action="/admin/kyc/<?= $tenant['id'] ?>/approve" onsubmit="return confirm('Approve this agency and generate login credentials?');">
                    <button type="submit" class="btn btn-primary" style="width:100%; padding:12px; font-size:14px; background-color:var(--google-green); display:inline-flex; align-items:center; justify-content:center; gap:8px;">
                        <?= Icon::svg('check', '', 18) ?> Approve Agency & Generate Credentials
                    </button>
                    <small style="display:block; text-align:center; color:var(--text-secondary); font-size:11px; margin-top:6px;">
                        Sets 1-year subscription, generates agency branch code, and sends setup email.
                    </small>
                </form>

                <!-- Reject Form -->
                <form method="POST" action="/admin/kyc/<?= $tenant['id'] ?>/reject">
                    <div style="margin-bottom:8px;">
                        <input type="text" name="reason" class="form-control" placeholder="Rejection reason (sent to applicant)" required>
                    </div>
                    <button type="submit" class="btn btn-danger" style="width:100%; padding:9px; font-size:13px; display:inline-flex; align-items:center; justify-content:center; gap:6px;">
                        <?= Icon::svg('alert-triangle', '', 15) ?> Reject Application
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/src/Views/layout.php';
?>

<?php
// src/Views/operators/index.php - Agency Operators (Desk Staff) Master View
$pageTitle = 'Branch Operators (Desk Staff)';
$activeNav = 'operators';

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

ob_start();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">👨‍💼 Branch Desk Operators</h1>
        <p style="color:var(--text-secondary); font-size:13px; margin-top:4px;">
            Manage counter clerks and booking desk operators for this branch. Track bookings by operator.
        </p>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="/operators/new" class="btn btn-primary"><?= Icon::svg('plus') ?> Add New Operator</a>
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

<div class="g-card" style="padding:0; overflow:hidden;">
    <div style="padding:16px 20px; border-bottom:1px solid #dadce0; display:flex; justify-content:space-between; align-items:center; background:#f8f9fa;">
        <div style="font-weight:700; font-size:14px;">
            Authorized Branch Users (<?= count($operators) ?>)
        </div>
        <div style="font-size:12px; color:var(--text-secondary);">
            Branch #<?= htmlspecialchars(TenantContext::getTenant()['agency_code'] ?? '26') ?>
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table class="g-table">
            <thead>
                <tr>
                    <th style="width:50px;">Sno.</th>
                    <th>Operator Name</th>
                    <th>Email (Username)</th>
                    <th>Contact No.</th>
                    <th>Role</th>
                    <th>Bookings Done</th>
                    <th>Last Active</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($operators)): ?>
                    <tr>
                        <td colspan="9" style="text-align:center; padding:32px; color:var(--text-secondary);">
                            No desk operators added yet. Click "+ Add New Operator" to create counter staff.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $sno = 1; foreach ($operators as $op): ?>
                        <tr>
                            <td><?= $sno++ ?></td>
                            <td>
                                <div style="font-weight:700; color:#202124;">
                                    <?= htmlspecialchars($op['name']) ?>
                                </div>
                            </td>
                            <td>
                                <code style="font-size:13px; color:#1a73e8;"><?= htmlspecialchars($op['email']) ?></code>
                            </td>
                            <td><?= htmlspecialchars($op['contact_no'] ?? '—') ?></td>
                            <td>
                                <?php if ($op['role'] === 'agency_admin'): ?>
                                    <span class="g-chip chip-info" style="font-size:11px;">Branch Admin</span>
                                <?php else: ?>
                                    <span class="g-chip" style="font-size:11px; background:#e8f0fe; color:#1967d2;">Counter Desk</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= (int)$op['bookings_count'] ?></strong> dockets
                            </td>
                            <td style="font-size:12px; color:var(--text-secondary);">
                                <?= $op['last_booking_date'] ? date('d-M-Y', strtotime($op['last_booking_date'])) : '—' ?>
                            </td>
                            <td>
                                <?php if ($op['status'] === 'active'): ?>
                                    <span class="g-chip chip-success" style="font-size:11px;">Active</span>
                                <?php else: ?>
                                    <span class="g-chip chip-danger" style="font-size:11px;">Blocked</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:right;">
                                <div style="display:inline-flex; gap:6px;">
                                    <form method="POST" action="/operators/<?= $op['id'] ?>/toggle" style="margin:0;">
                                        <button type="submit" class="btn btn-outlined" style="padding:4px 10px; font-size:11px;" title="Toggle Active / Blocked">
                                            <?= $op['status'] === 'active' ? 'Block' : 'Activate' ?>
                                        </button>
                                    </form>
                                    <?php if ($op['role'] !== 'agency_admin'): ?>
                                        <form method="POST" action="/operators/<?= $op['id'] ?>/delete" onsubmit="return confirm('Are you sure you want to delete this operator?');" style="margin:0;">
                                            <button type="submit" class="btn btn-outlined" style="padding:4px 8px; font-size:11px; color:#c5221f; border-color:#fce8e6;" title="Delete">
                                                ✕
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/src/Views/layout.php';
?>

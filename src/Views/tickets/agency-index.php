<?php
// src/Views/tickets/agency-index.php - Agency Support & City Requests History
$pageTitle = 'Support & Destination Requests';
$activeNav = 'tickets';
$flash = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_success']);

ob_start();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Support & Destination Requests</h1>
        <p style="color:var(--text-secondary); font-size:13px; margin-top:4px;">
            Request new delivery cities/pincodes to be added by Super Admin or contact centralized operations support.
        </p>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="https://wa.me/919893000000?text=Hello+Kesari+Logistics+Admin%2C+I+need+support+for+my+branch." target="_blank" class="btn btn-outlined" style="color:#25D366; border-color:#25D366;">
            💬 WhatsApp Support
        </a>
        <a href="/tickets/new" class="btn btn-primary">+ Request New City / Support</a>
    </div>
</div>

<?php if ($flash): ?>
    <div style="background:#e6f4ea; border-left:4px solid var(--google-green); color:#137333; padding:12px 18px; border-radius:4px; margin-bottom:20px; font-size:13px;">
        <?= htmlspecialchars($flash) ?>
    </div>
<?php endif; ?>

<div class="g-card">
    <div class="table-responsive">
        <table class="g-table">
            <thead>
                <tr>
                    <th>Ticket ID</th>
                    <th>Category</th>
                    <th>Subject</th>
                    <th>Requested Destination</th>
                    <th>Priority</th>
                    <th>Submitted Date</th>
                    <th>Status</th>
                    <th>Admin Response</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tickets)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center; padding:32px; color:var(--text-secondary);">
                            No support tickets submitted yet. Click "+ Request New City" if you need a destination added.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($tickets as $t): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($t['ticket_number']) ?></strong></td>
                        <td>
                            <?php if ($t['category'] === 'destination_request'): ?>
                                <span class="g-chip chip-info">City Request</span>
                            <?php else: ?>
                                <span class="g-chip" style="background:#f1f3f4; color:#5f6368;"><?= ucfirst($t['category']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="font-weight:500;"><?= htmlspecialchars($t['subject']) ?></td>
                        <td>
                            <?php if (!empty($t['destination_name'])): ?>
                                <strong><?= htmlspecialchars($t['destination_name']) ?></strong> (<?= htmlspecialchars($t['state_name'] ?? '') ?>, <?= htmlspecialchars($t['pincode'] ?: 'N/A') ?>)
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="g-chip <?= $t['priority'] === 'urgent' ? 'chip-danger' : ($t['priority'] === 'high' ? 'chip-warning' : 'chip-info') ?>">
                                <?= ucfirst($t['priority']) ?>
                            </span>
                        </td>
                        <td><?= date('d M Y, h:i A', strtotime($t['created_at'])) ?></td>
                        <td>
                            <?php if ($t['status'] === 'resolved'): ?>
                                <span class="g-chip chip-success">Resolved ✓</span>
                            <?php elseif ($t['status'] === 'open'): ?>
                                <span class="g-chip chip-warning">Under Review</span>
                            <?php else: ?>
                                <span class="g-chip chip-danger"><?= ucfirst($t['status']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="font-size:12px; color:var(--text-secondary);">
                                <?= htmlspecialchars($t['admin_notes'] ?: 'Pending admin response') ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/src/Views/layout.php';
?>

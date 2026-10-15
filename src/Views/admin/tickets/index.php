<?php
// src/Views/admin/tickets/index.php - Super Admin Support & Destination Requests Queue
$pageTitle = 'Support Tickets & City Requests';
$activeNav = 'tickets';
$flash = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_success']);

ob_start();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Support & Destination Requests Queue</h1>
        <p style="color:var(--text-secondary); font-size:13px; margin-top:4px;">
            Central management of agency route extension requests and support tickets.
        </p>
    </div>
</div>

<?php if ($flash): ?>
    <div style="background:#e6f4ea; border-left:4px solid var(--google-green); color:#137333; padding:12px 18px; border-radius:4px; margin-bottom:20px; font-size:13px;">
        <?= htmlspecialchars($flash) ?>
    </div>
<?php endif; ?>

<!-- Filter Tabs -->
<div style="display:flex; gap:8px; margin-bottom:20px; border-bottom:1px solid var(--border-color); padding-bottom:10px;">
    <a href="/admin/tickets?status=all" class="btn <?= ($statusFilter ?? 'all') === 'all' ? 'btn-primary' : 'btn-outlined' ?>" style="border-radius:20px; padding:4px 14px; font-size:12px;">
        All Tickets (<?= count($tickets) ?>)
    </a>
    <a href="/admin/tickets?status=open" class="btn <?= ($statusFilter ?? '') === 'open' ? 'btn-primary' : 'btn-outlined' ?>" style="border-radius:20px; padding:4px 14px; font-size:12px;">
        Open / Pending (<?= $openCount ?>)
    </a>
    <a href="/admin/tickets?category=destination_request&status=open" class="btn <?= ($catFilter ?? '') === 'destination_request' ? 'btn-primary' : 'btn-outlined' ?>" style="border-radius:20px; padding:4px 14px; font-size:12px;">
        City Requests (<?= $destReqCount ?>)
    </a>
</div>

<div class="g-card">
    <div class="table-responsive">
        <table class="g-table">
            <thead>
                <tr>
                    <th>Ticket ID</th>
                    <th>Agency (Branch)</th>
                    <th>Category</th>
                    <th>Subject / Details</th>
                    <th>Requested Destination</th>
                    <th>Priority</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tickets)): ?>
                    <tr>
                        <td colspan="9" style="text-align:center; padding:32px; color:var(--text-secondary);">
                            No support tickets matching this filter.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($tickets as $t): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($t['ticket_number']) ?></strong></td>
                        <td>
                            <div style="font-weight:500; font-size:13px;"><?= htmlspecialchars($t['agency_name']) ?></div>
                            <div style="font-size:11px; color:var(--text-secondary);">Branch #<?= htmlspecialchars($t['agency_code']) ?></div>
                        </td>
                        <td>
                            <?php if ($t['category'] === 'destination_request'): ?>
                                <span class="g-chip chip-info">City Request</span>
                            <?php else: ?>
                                <span class="g-chip" style="background:#f1f3f4; color:#5f6368;"><?= ucfirst($t['category']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="max-width:260px;">
                            <div style="font-weight:500;"><?= htmlspecialchars($t['subject']) ?></div>
                            <div style="font-size:11px; color:var(--text-secondary); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                <?= htmlspecialchars($t['description']) ?>
                            </div>
                        </td>
                        <td>
                            <?php if (!empty($t['destination_name'])): ?>
                                <strong><?= htmlspecialchars($t['destination_name']) ?></strong> (<?= htmlspecialchars($t['state_name'] ?? '') ?>)
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="g-chip <?= $t['priority'] === 'urgent' ? 'chip-danger' : ($t['priority'] === 'high' ? 'chip-warning' : 'chip-info') ?>">
                                <?= ucfirst($t['priority']) ?>
                            </span>
                        </td>
                        <td><?= date('d M, h:i A', strtotime($t['created_at'])) ?></td>
                        <td>
                            <?php if ($t['status'] === 'resolved'): ?>
                                <span class="g-chip chip-success">Resolved ✓</span>
                            <?php elseif ($t['status'] === 'open'): ?>
                                <span class="g-chip chip-warning">Open</span>
                            <?php else: ?>
                                <span class="g-chip chip-danger"><?= ucfirst($t['status']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="/admin/tickets/<?= $t['id'] ?>" class="btn btn-primary" style="padding:4px 10px; font-size:11px;">
                                Review & Resolve ➔
                            </a>
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

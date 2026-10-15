<?php
// src/Views/admin/tickets/review.php - Super Admin Ticket Resolution Interface
$pageTitle = 'Resolve Ticket #' . $ticket['ticket_number'];
$activeNav = 'tickets';

ob_start();
?>

<div class="page-header">
    <div>
        <a href="/admin/tickets" style="font-size:13px; color:var(--text-secondary); text-decoration:none;">← Back to Ticket Queue</a>
        <h1 class="page-title" style="margin-top:6px;">Support Ticket: <?= htmlspecialchars($ticket['ticket_number']) ?></h1>
    </div>
    <span class="g-chip <?= $ticket['status'] === 'resolved' ? 'chip-success' : 'chip-warning' ?>" style="font-size:13px; padding:6px 14px;">
        Status: <?= ucfirst($ticket['status']) ?>
    </span>
</div>

<div style="display:grid; grid-template-columns: 2fr 1fr; gap:24px;">
    <!-- Main Ticket Details -->
    <div class="g-card">
        <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:1px solid #f1f3f4; padding-bottom:12px; margin-bottom:16px;">
            <div>
                <span class="g-chip chip-info"><?= ucfirst($ticket['category']) ?></span>
                <span class="g-chip <?= $ticket['priority'] === 'urgent' ? 'chip-danger' : 'chip-info' ?>"><?= ucfirst($ticket['priority']) ?> Priority</span>
            </div>
            <div style="font-size:12px; color:var(--text-secondary);">
                Submitted: <?= date('d M Y, h:i A', strtotime($ticket['created_at'])) ?>
            </div>
        </div>

        <h2 style="font-size:18px; margin-bottom:12px;"><?= htmlspecialchars($ticket['subject']) ?></h2>
        
        <div style="background:#f8f9fa; border-radius:8px; padding:16px; font-size:14px; line-height:1.6; margin-bottom:24px; white-space:pre-wrap;">
<?= htmlspecialchars($ticket['description']) ?>
        </div>

        <!-- If Destination Request, Show 1-Click Approve Box -->
        <?php if ($ticket['category'] === 'destination_request'): ?>
            <div style="border:2px solid #e8f0fe; background:#f0f7ff; border-radius:12px; padding:20px; margin-bottom:24px;">
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
                    <span style="font-size:20px;">📍</span>
                    <h3 style="font-size:16px; color:var(--google-blue);">Requested Master Destination</h3>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr 1fr; gap:16px; font-size:13px; margin-bottom:16px;">
                    <div>
                        <span style="color:var(--text-secondary); display:block;">City Name:</span>
                        <strong style="font-size:15px;"><?= htmlspecialchars($ticket['destination_name']) ?></strong>
                    </div>
                    <div>
                        <span style="color:var(--text-secondary); display:block;">State:</span>
                        <strong><?= htmlspecialchars($ticket['state_name'] ?? 'Not Specified') ?></strong>
                    </div>
                    <div>
                        <span style="color:var(--text-secondary); display:block;">Pincode:</span>
                        <code><?= htmlspecialchars($ticket['pincode'] ?: 'N/A') ?></code>
                    </div>
                </div>

                <?php if ($ticket['status'] === 'open'): ?>
                    <form method="POST" action="/admin/tickets/<?= $ticket['id'] ?>/approve-destination" onsubmit="return confirm('Approve this destination and insert it into the master delivery database?');">
                        <button type="submit" class="btn btn-primary" style="background-color:var(--google-green); padding:10px 20px;">
                            ✓ 1-Click Approve & Add to Master Destinations
                        </button>
                    </form>
                <?php else: ?>
                    <div class="g-chip chip-success">
                        ✓ Destination Approved & Activated in Master Table
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <!-- Resolution Notes -->
        <?php if ($ticket['status'] === 'open'): ?>
            <form method="POST" action="/admin/tickets/<?= $ticket['id'] ?>/resolve">
                <div class="form-group">
                    <label class="form-label">Response / Resolution Note</label>
                    <textarea name="admin_notes" class="form-control" rows="3" placeholder="Provide response to agency operator..."></textarea>
                </div>
                <button type="submit" class="btn btn-outlined">Mark Ticket as Resolved</button>
            </form>
        <?php else: ?>
            <div style="background:#f1f3f4; border-radius:8px; padding:14px; font-size:13px;">
                <strong>Admin Resolution Note:</strong>
                <p style="margin-top:4px;"><?= htmlspecialchars($ticket['admin_notes'] ?: 'No notes recorded.') ?></p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Agency Details -->
    <div>
        <div class="g-card">
            <h3 style="font-size:15px; margin-bottom:14px;">Requesting Agency</h3>
            <div style="font-size:13px; display:flex; flex-direction:column; gap:10px;">
                <div>
                    <span style="color:var(--text-secondary);">Agency:</span>
                    <div style="font-weight:500;"><?= htmlspecialchars($ticket['agency_name']) ?></div>
                </div>
                <div>
                    <span style="color:var(--text-secondary);">Branch Code:</span>
                    <div><strong>#<?= htmlspecialchars($ticket['agency_code']) ?></strong></div>
                </div>
                <div>
                    <span style="color:var(--text-secondary);">Contact Phone:</span>
                    <div><?= htmlspecialchars($ticket['agency_phone']) ?></div>
                </div>
                <div>
                    <span style="color:var(--text-secondary);">Contact Email:</span>
                    <div><code><?= htmlspecialchars($ticket['agency_email']) ?></code></div>
                </div>
            </div>

            <div style="margin-top:18px;">
                <a href="https://wa.me/91<?= preg_replace('/[^0-9]/', '', $ticket['agency_phone']) ?>?text=Hello+from+Kesari+Logistics+Admin+regarding+ticket+<?= urlencode($ticket['ticket_number']) ?>." target="_blank" class="btn btn-outlined" style="width:100%; justify-content:center; color:#25D366; border-color:#25D366; font-size:12px;">
                    💬 Reply via WhatsApp
                </a>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/src/Views/layout.php';
?>

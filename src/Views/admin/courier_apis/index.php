<?php
// src/Views/admin/courier_apis/index.php - Super Admin Courier API Integrations & Gateway Hub
$pageTitle = 'Courier API Integrations';
$activeNav = 'courier_apis';

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

ob_start();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">⚡ Third-Party Courier API Gateway</h1>
        <p style="color:var(--text-secondary); font-size:13px; margin-top:4px;">
            Configure external courier partner APIs (Delhivery, Shiprocket, BlueDart, DTDC, Trackon, Shree Maruti) for live multi-carrier AWB tracking.
        </p>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="/admin/courier-apis/new" class="btn btn-primary">+ Add New Courier API</a>
    </div>
</div>

<?php if ($flashSuccess): ?>
    <div style="background:#e6f4ea; border-left:4px solid var(--google-green); color:#137333; padding:12px 18px; border-radius:4px; margin-bottom:20px; font-size:13px;">
        <?= htmlspecialchars($flashSuccess) ?>
    </div>
<?php endif; ?>

<?php if ($flashError): ?>
    <div style="background:#fce8e6; border-left:4px solid var(--google-red); color:#c5221f; padding:12px 18px; border-radius:4px; margin-bottom:20px; font-size:13px;">
        ⚠️ <?= htmlspecialchars($flashError) ?>
    </div>
<?php endif; ?>

<!-- Quick Status Cards -->
<div class="metrics-grid" style="grid-template-columns: repeat(4, 1fr); margin-bottom:20px;">
    <div class="metric-card">
        <div class="metric-label">Configured Carriers</div>
        <div class="metric-value"><?= $stats['total'] ?></div>
        <div class="metric-sub" style="color:var(--text-secondary);">Supported courier gateways</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Connected & Active</div>
        <div class="metric-value" style="color:var(--google-green);"><?= $stats['connected'] ?></div>
        <div class="metric-sub" style="color:var(--google-green);">Live API handshake verified</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Connection Failed</div>
        <div class="metric-value" style="color:var(--google-red);"><?= $stats['failed'] ?></div>
        <div class="metric-sub" style="color:var(--google-red);">Authentication / network issue</div>
    </div>
    <div class="metric-card">
        <div class="metric-label">Disabled / Inactive</div>
        <div class="metric-value" style="color:var(--text-tertiary);"><?= $stats['inactive'] ?></div>
        <div class="metric-sub" style="color:var(--text-secondary);">Manually deactivated</div>
    </div>
</div>

<!-- Courier Integrations Table -->
<div class="g-card" style="padding:0; overflow:hidden;">
    <div class="table-responsive">
        <table class="g-table">
            <thead>
                <tr>
                    <th>Courier Name & Code</th>
                    <th>API Endpoint URL</th>
                    <th>Account / Client ID</th>
                    <th>API Key (Masked)</th>
                    <th>Status</th>
                    <th>Last Connection Test & Error Log</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($apis)): ?>
                    <tr>
                        <td colspan="7" style="text-align:center; padding:32px; color:var(--text-secondary);">
                            No courier APIs configured yet. Click "+ Add New Courier API" above.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($apis as $a): ?>
                    <tr style="<?= empty($a['is_active']) ? 'opacity:0.65; background:#fafafa;' : '' ?>">
                        <td>
                            <div style="font-weight:600; font-size:14px;"><?= htmlspecialchars($a['courier_name']) ?></div>
                            <code style="font-size:11px;"><?= htmlspecialchars($a['courier_code']) ?></code>
                        </td>
                        <td>
                            <div style="font-family:monospace; font-size:12px; color:var(--google-blue); word-break:break-all;">
                                <?= htmlspecialchars($a['api_endpoint']) ?>
                            </div>
                            <?php if (!empty($a['tracking_url_template'])): ?>
                                <div style="font-size:11px; color:var(--text-secondary); margin-top:2px;">
                                    Track Template: <code><?= htmlspecialchars($a['tracking_url_template']) ?></code>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($a['client_id'] ?: 'N/A') ?>
                        </td>
                        <td>
                            <?php if (!empty($a['api_key'])): ?>
                                <span style="font-family:monospace; font-size:12px;">
                                    <?= substr($a['api_key'], 0, 4) . '••••••••' . substr($a['api_key'], -4) ?>
                                </span>
                            <?php else: ?>
                                <span style="color:var(--text-tertiary);">None</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (empty($a['is_active'])): ?>
                                <span class="g-chip" style="background:#f1f3f4; color:#5f6368;">Inactive</span>
                            <?php elseif ($a['status'] === 'connected'): ?>
                                <span class="g-chip chip-success">● Connected</span>
                            <?php elseif ($a['status'] === 'failed'): ?>
                                <span class="g-chip chip-danger">● Failed</span>
                            <?php else: ?>
                                <span class="g-chip chip-warning">● <?= ucfirst($a['status']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="max-width:260px;">
                            <div style="font-size:11px; color:var(--text-secondary); font-family:monospace;">
                                <?= htmlspecialchars($a['last_error_log'] ?? 'No test run yet.') ?>
                            </div>
                            <?php if (!empty($a['last_tested_at'])): ?>
                                <div style="font-size:10px; color:var(--text-tertiary); margin-top:2px;">
                                    Tested: <?= htmlspecialchars($a['last_tested_at']) ?>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div style="display:flex; gap:6px; flex-wrap:wrap;">
                                <form method="POST" action="/admin/courier-apis/<?= $a['id'] ?>/test" style="display:inline;">
                                    <button type="submit" class="btn btn-outlined" style="padding:4px 8px; font-size:11px;" title="Send live handshake test">
                                        ⚡ Test Ping
                                    </button>
                                </form>
                                <a href="/admin/courier-apis/<?= $a['id'] ?>/edit" class="btn btn-outlined" style="padding:4px 8px; font-size:11px;">Edit</a>
                                <form method="POST" action="/admin/courier-apis/<?= $a['id'] ?>/toggle" style="display:inline;">
                                    <button type="submit" class="btn btn-outlined" style="padding:4px 8px; font-size:11px;">
                                        <?= !empty($a['is_active']) ? 'Disable' : 'Enable' ?>
                                    </button>
                                </form>
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

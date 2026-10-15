<?php
// src/Views/tariffs/index.php - Dynamic Rate Matrix (Tariff) Overview
$pageTitle = 'Rate Matrix (Tariff)';
$activeNav = 'tariffs';
$flash = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_success']);

ob_start();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Rate Matrix & Tariff Management</h1>
        <p style="color:var(--text-secondary); font-size:13px; margin-top:4px;">
            Configure multi-tier pricing slabs, mode rules (Air / Surface), and excess weight charges.
        </p>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="/clients" class="btn btn-outlined">👥 B2B Clients</a>
        <a href="/tariffs/new" class="btn btn-primary">+ Add New Rate Matrix</a>
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
                    <th>Tariff Scope</th>
                    <th>Mode</th>
                    <th>Package Type</th>
                    <th>Destination Zone</th>
                    <th>Weight Slabs & Pricing</th>
                    <th>Incremental Weight Surcharge</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($tariffs)): ?>
                    <tr>
                        <td colspan="6" style="text-align:center; padding:32px; color:var(--text-secondary);">
                            No rate matrices configured.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($tariffs as $t): ?>
                    <tr>
                        <td>
                            <?php if ($t['client_id']): ?>
                                <div style="font-weight:500; color:var(--google-blue);">
                                    <?= htmlspecialchars($t['client_name']) ?> (#<?= htmlspecialchars($t['client_code']) ?>)
                                </div>
                                <span class="g-chip chip-info" style="font-size:10px;">Contract Client Rate</span>
                            <?php else: ?>
                                <strong>Default Branch Tariff</strong>
                                <span class="g-chip" style="font-size:10px; background:#f1f3f4; color:#5f6368;">Standard Fallback</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="g-chip <?= $t['send_by'] === 'Air' ? 'chip-info' : 'chip-warning' ?>">
                                <?= htmlspecialchars($t['send_by']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($t['mode']) ?></td>
                        <td><strong><?= htmlspecialchars($t['zone_name']) ?></strong> (<code><?= htmlspecialchars($t['zone_code']) ?></code>)</td>
                        <td>
                            <div style="display:flex; flex-direction:column; gap:4px; font-size:12px;">
                                <?php foreach ($t['slabs'] as $slab): ?>
                                    <div>
                                        <code><?= $slab['weight_from'] ?> kg – <?= $slab['weight_to'] ?> kg</code> ➔ 
                                        <strong>₹<?= number_format($slab['rate'], 2) ?></strong>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </td>
                        <td>
                            <div>
                                + <strong>₹<?= number_format($t['additional_weight_rate'], 2) ?></strong> per <?= $t['additional_weight_step'] ?> kg
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

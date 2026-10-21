<?php
// src/Views/admin/destinations/index.php - Master Delivery Destinations Table
$pageTitle = 'Master Delivery Destinations';
$activeNav = 'destinations';
$flash = $_SESSION['flash_success'] ?? null;
$error = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

ob_start();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">Central Master Destinations</h1>
        <p style="color:var(--text-secondary); font-size:13px; margin-top:4px;">
            Centrally governed delivery locations, operational zones, and pincodes across India.
        </p>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="/admin/tickets" class="btn btn-outlined">🎫 Review City Requests</a>
        <a href="/admin/destinations/new" class="btn btn-primary">+ Add New Destination</a>
    </div>
</div>

<?php if ($flash): ?>
    <div style="background:#e6f4ea; border-left:4px solid var(--google-green); color:#137333; padding:12px 18px; border-radius:4px; margin-bottom:20px; font-size:13px;">
        <?= htmlspecialchars($flash) ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div style="background:#fce8e6; border-left:4px solid var(--google-red); color:#c5221f; padding:12px 18px; border-radius:4px; margin-bottom:20px; font-size:13px;">
        ⚠️ <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<!-- Filter & Search Bar -->
<div class="g-card" style="padding:16px; margin-bottom:20px;">
    <form method="GET" action="/admin/destinations" style="display:flex; gap:16px; align-items:center;">
        <div style="flex:1;">
            <input type="text" name="q" class="form-control" placeholder="Search by city name, airport code, or pincode..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
        </div>

        <div style="width:220px;">
            <select name="state" class="form-control" onchange="this.form.submit()">
                <option value="">All States</option>
                <?php foreach ($states as $s): ?>
                    <option value="<?= htmlspecialchars($s['code']) ?>" <?= ($_GET['state'] ?? '') === $s['code'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['name']) ?> (<?= htmlspecialchars($s['code']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary" style="padding:10px 20px;">Filter</button>
        <?php if (!empty($_GET['q']) || !empty($_GET['state'])): ?>
            <a href="/admin/destinations" class="btn btn-outlined">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- Destinations Table -->
<div class="g-card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <h2 style="font-size:16px;">Deliverable Destinations</h2>
        <span class="g-chip chip-info"><?= count($destinations) ?> cities registered</span>
    </div>

    <div class="table-responsive">
        <table class="g-table">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>City / Destination Name</th>
                    <th>State</th>
                    <th>Zone</th>
                    <th>Default Pincode</th>
                    <th>Hub Classification</th>
                    <th>Serviceability</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($destinations)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center; padding:32px; color:var(--text-secondary);">
                            No destinations found matching your criteria.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($destinations as $d): ?>
                    <tr style="<?= empty($d['is_active']) ? 'opacity:0.6; background:#fafafa;' : '' ?>">
                        <td><strong><?= htmlspecialchars($d['code']) ?></strong></td>
                        <td style="font-weight:500; font-size:14px;">
                            <?= htmlspecialchars($d['name']) ?>
                        </td>
                        <td><?= htmlspecialchars($d['state_name']) ?> (<code><?= htmlspecialchars($d['state_code']) ?></code>)</td>
                        <td><span class="g-chip chip-info"><?= htmlspecialchars($d['zone_name']) ?></span></td>
                        <td><code><?= htmlspecialchars($d['pincode'] ?: '—') ?></code></td>
                        <td>
                            <?php if ($d['is_hub']): ?>
                                <span class="g-chip chip-success">Airport / Primary Hub</span>
                            <?php else: ?>
                                <span class="g-chip" style="background:#f1f3f4; color:#5f6368;">Branch Delivery Node</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($d['is_active'])): ?>
                                <span class="g-chip chip-success">Serviceable</span>
                            <?php else: ?>
                                <span class="g-chip chip-danger">Suspended</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" action="/admin/destinations/<?= $d['id'] ?>/toggle" style="display:inline;">
                                <button type="submit" class="btn btn-outlined" style="padding:3px 8px; font-size:11px;" onclick="return confirm('Change serviceability status for <?= htmlspecialchars($d['name']) ?>?')">
                                    <?= !empty($d['is_active']) ? 'Deactivate' : 'Activate' ?>
                                </button>
                            </form>
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

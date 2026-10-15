<?php
// src/Views/clients/index.php - B2B Corporate Clients Directory
$pageTitle = 'B2B Corporate Clients';
$activeNav = 'clients';
$flash = $_SESSION['flash_success'] ?? null;
unset($_SESSION['flash_success']);

ob_start();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">B2B Corporate Clients</h1>
        <p style="color:var(--text-secondary); font-size:13px; margin-top:4px;">
            Manage commercial client accounts, billing GSTIN, and custom contracted rate profiles.
        </p>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="/tariffs" class="btn btn-outlined">⚡ Rate Matrix (Tariff)</a>
        <a href="/clients/new" class="btn btn-primary">+ Add New Client</a>
    </div>
</div>

<?php if ($flash): ?>
    <div style="background:#e6f4ea; border-left:4px solid var(--google-green); color:#137333; padding:12px 18px; border-radius:4px; margin-bottom:20px; font-size:13px;">
        <?= htmlspecialchars($flash) ?>
    </div>
<?php endif; ?>

<!-- Search Card -->
<div class="g-card" style="padding:16px; margin-bottom:20px;">
    <form method="GET" action="/clients" style="display:flex; gap:16px;">
        <input type="text" name="q" class="form-control" placeholder="Search by client name, client code (#), phone, or city..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
        <button type="submit" class="btn btn-primary" style="padding:10px 20px;">Search</button>
        <?php if (!empty($_GET['q'])): ?>
            <a href="/clients" class="btn btn-outlined">Clear</a>
        <?php endif; ?>
    </form>
</div>

<div class="g-card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
        <h2 style="font-size:16px;">Active Client Accounts</h2>
        <span class="g-chip chip-info"><?= count($clients) ?> clients registered</span>
    </div>

    <div class="table-responsive">
        <table class="g-table">
            <thead>
                <tr>
                    <th>Client Code</th>
                    <th>Corporate Client Name</th>
                    <th>Contact Person</th>
                    <th>Phone / Email</th>
                    <th>Location</th>
                    <th>GSTIN</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($clients)): ?>
                    <tr>
                        <td colspan="8" style="text-align:center; padding:32px; color:var(--text-secondary);">
                            No B2B clients found.
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($clients as $c): ?>
                    <tr>
                        <td><strong>#<?= htmlspecialchars($c['client_code']) ?></strong></td>
                        <td>
                            <div style="font-weight:500; font-size:14px;"><?= htmlspecialchars($c['name']) ?></div>
                            <div style="font-size:11px; color:var(--text-secondary);"><?= htmlspecialchars($c['address'] ?? '') ?></div>
                        </td>
                        <td><?= htmlspecialchars($c['contact_person'] ?: '—') ?></td>
                        <td>
                            <div><?= htmlspecialchars($c['phone']) ?></div>
                            <div style="font-size:11px; color:var(--text-secondary);"><?= htmlspecialchars($c['email'] ?: '—') ?></div>
                        </td>
                        <td><?= htmlspecialchars($c['city']) ?>, <?= htmlspecialchars($c['state']) ?></td>
                        <td><code><?= htmlspecialchars($c['gstin'] ?: 'Unregistered') ?></code></td>
                        <td><span class="g-chip chip-success">Active</span></td>
                        <td>
                            <a href="/bookings/new?client_id=<?= $c['id'] ?>" class="btn btn-outlined" style="padding:3px 10px; font-size:11px;">
                                Book AWB
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

<?php
// src/Views/print/index.php - Thermal Label Print Hub
$pageTitle = 'Thermal Label Print Hub';
$activeNav = 'print';

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError = $_SESSION['flash_error'] ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);

ob_start();
?>

<div class="page-header">
    <div>
        <h1 class="page-title">🏷️ Thermal Label Print Hub</h1>
        <p style="color:var(--text-secondary); font-size:13px; margin-top:4px;">
            Commercial 4x6" barcode & QR label generator for thermal roll printers (TSC, Zebra, TVS, Rollo).
        </p>
    </div>
    <div style="display:flex; gap:10px;">
        <a href="/bookings/new" class="btn btn-primary">+ New Booking</a>
    </div>
</div>

<?php if ($flashSuccess): ?>
    <div style="background:#e6f4ea; border-left:4px solid var(--google-green); color:#137333; padding:12px 18px; border-radius:4px; margin-bottom:20px; font-size:13px; display:flex; justify-content:space-between; align-items:center;">
        <div><?= htmlspecialchars($flashSuccess) ?></div>
        <?php if (!empty($targetAwb)): ?>
            <a href="/print/single?awb=<?= urlencode($targetAwb) ?>&autoprint=1" target="_blank" class="btn btn-primary" style="padding:6px 14px; font-size:12px;">
                🖨️ Open Label Now
            </a>
        <?php endif; ?>
    </div>
<?php endif; ?>

<?php if ($flashError): ?>
    <div style="background:#fce8e6; border-left:4px solid var(--google-red); color:#c5221f; padding:12px 18px; border-radius:4px; margin-bottom:20px; font-size:13px;">
        <?= htmlspecialchars($flashError) ?>
    </div>
<?php endif; ?>

<!-- Quick Single AWB Print Card -->
<div class="g-card" style="margin-bottom:20px; background:#f8f9fa;">
    <form method="GET" action="/print/single" target="_blank" style="display:flex; gap:12px; align-items:center; flex-wrap:wrap;">
        <div style="font-weight:600; font-size:13px;">⚡ Instant Single Docket Print:</div>
        <input 
            type="text" 
            name="awb" 
            class="form-control" 
            placeholder="Scan or enter AWB (e.g. KSR-2026-0001)" 
            value="<?= htmlspecialchars($targetAwb) ?>" 
            style="width:280px; font-family:monospace; text-transform:uppercase; font-weight:600;" 
            required
        >
        <button type="submit" class="btn btn-primary" style="padding:8px 16px;">
            🏷️ Print 4x6" Label
        </button>
        <span style="font-size:12px; color:var(--text-secondary);">Tip: Pressing Enter opens standard 4x6" thermal format.</span>
    </form>
</div>

<!-- Filter Bar -->
<div class="g-card" style="margin-bottom:20px; padding:16px;">
    <form method="GET" action="/print-labels" style="display:grid; grid-template-columns: 2fr 1.5fr 1fr 1fr auto; gap:12px; align-items:end;">
        <div>
            <label class="form-label">Search Consignment</label>
            <input type="text" name="q" class="form-control" placeholder="AWB # or recipient name" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
        </div>
        <div>
            <label class="form-label">Client</label>
            <select name="client_id" class="form-control">
                <option value="">All Corporate Clients</option>
                <?php foreach ($clients as $cl): ?>
                    <option value="<?= $cl['id'] ?>" <?= ($_GET['client_id'] ?? '') == $cl['id'] ? 'selected' : '' ?>>
                        #<?= htmlspecialchars($cl['client_code']) ?> - <?= htmlspecialchars($cl['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div>
            <label class="form-label">From Date</label>
            <input type="date" name="date_from" class="form-control" value="<?= htmlspecialchars($_GET['date_from'] ?? '') ?>">
        </div>
        <div>
            <label class="form-label">To Date</label>
            <input type="date" name="date_to" class="form-control" value="<?= htmlspecialchars($_GET['date_to'] ?? '') ?>">
        </div>
        <div style="display:flex; gap:8px;">
            <button type="submit" class="btn btn-secondary">Filter</button>
            <?php if (!empty($_GET['q']) || !empty($_GET['client_id']) || !empty($_GET['date_from'])): ?>
                <a href="/print-labels" class="btn btn-outlined">Reset</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- Batch Print Table Form -->
<form method="POST" action="/print/batch" target="_blank" id="batchPrintForm">
    
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px;">
        <div style="font-size:14px; font-weight:600;">
            Showing <?= count($consignments) ?> Ready Consignments
        </div>
        <div style="display:flex; gap:12px; align-items:center;">
            <span id="selectedCount" style="font-size:13px; color:var(--text-secondary);">0 labels selected</span>
            <button type="submit" class="btn btn-primary" id="batchPrintBtn" disabled>
                🖨️ Batch Print Selected (4x6)
            </button>
        </div>
    </div>

    <div class="g-card" style="padding:0; overflow:hidden;">
        <table class="g-table">
            <thead>
                <tr>
                    <th style="width:40px; text-align:center;">
                        <input type="checkbox" id="selectAllCheckbox" title="Select all on this page">
                    </th>
                    <th>Booking Date</th>
                    <th>AWB / Docket #</th>
                    <th>Destination</th>
                    <th>Mode</th>
                    <th>Weight</th>
                    <th>B2B Client</th>
                    <th>Status</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($consignments)): ?>
                    <tr>
                        <td colspan="9" style="text-align:center; padding:36px; color:var(--text-secondary);">
                            No consignments found for thermal printing.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($consignments as $c): ?>
                        <tr>
                            <td style="text-align:center;">
                                <input 
                                    type="checkbox" 
                                    name="awbs[]" 
                                    value="<?= htmlspecialchars($c['consignment_no']) ?>" 
                                    class="row-checkbox"
                                    <?= ($targetAwb === $c['consignment_no']) ? 'checked' : '' ?>
                                >
                            </td>
                            <td><?= date('d-M-Y', strtotime($c['booking_date'])) ?></td>
                            <td>
                                <a href="/bookings/<?= $c['id'] ?>" style="font-family:monospace; font-weight:700; color:var(--google-blue); text-decoration:none;">
                                    <?= htmlspecialchars($c['consignment_no']) ?>
                                </a>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($c['destination_name']) ?></strong> (<?= htmlspecialchars($c['dest_code']) ?>)
                                <div style="font-size:11px; color:var(--text-secondary);">PIN: <?= htmlspecialchars($c['dest_pincode'] ?: 'N/A') ?></div>
                            </td>
                            <td>
                                <span class="g-chip" style="font-size:11px;"><?= htmlspecialchars($c['send_by']) ?></span>
                                <span style="font-size:11px; color:var(--text-secondary);"><?= htmlspecialchars($c['mode']) ?></span>
                            </td>
                            <td>
                                <strong><?= number_format($c['billable_weight'], 2) ?> kg</strong>
                                <div style="font-size:11px; color:var(--text-secondary);"><?= $c['pieces'] ?> pcs</div>
                            </td>
                            <td>
                                <div><?= htmlspecialchars($c['client_name']) ?></div>
                                <div style="font-size:11px; color:var(--text-secondary);">#<?= htmlspecialchars($c['client_code']) ?></div>
                            </td>
                            <td>
                                <span class="g-chip <?= $c['status'] === 'delivered' ? 'chip-success' : 'chip-info' ?>" style="font-size:11px;">
                                    <?= ucfirst(str_replace('_', ' ', $c['status'])) ?>
                                </span>
                            </td>
                            <td style="text-align:right;">
                                <a href="/print/single?awb=<?= urlencode($c['consignment_no']) ?>" target="_blank" class="btn btn-outlined" style="padding:4px 10px; font-size:12px;">
                                    🏷️ Print Label
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</form>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const selectAll = document.getElementById('selectAllCheckbox');
        const checkboxes = document.querySelectorAll('.row-checkbox');
        const countSpan = document.getElementById('selectedCount');
        const printBtn = document.getElementById('batchPrintBtn');

        function updateSelection() {
            let count = 0;
            checkboxes.forEach(cb => {
                if (cb.checked) count++;
            });
            countSpan.textContent = count + ' labels selected';
            printBtn.disabled = (count === 0);
        }

        if (selectAll) {
            selectAll.addEventListener('change', () => {
                checkboxes.forEach(cb => {
                    cb.checked = selectAll.checked;
                });
                updateSelection();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                updateSelection();
                if (!cb.checked && selectAll) {
                    selectAll.checked = false;
                }
            });
        });

        updateSelection();
    });
</script>

<?php
$content = ob_get_clean();
require ROOT_PATH . '/src/Views/layout.php';
?>

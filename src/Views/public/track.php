<?php
// src/Views/public/track.php - Public & Agency Consignment Live Tracking View
$pageTitle = 'Consignment Tracking | Kesari Logistics Network';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-primary: #1967d2;
            --brand-secondary: #0d47a1;
            --text-primary: #202124;
            --text-secondary: #5f6368;
            --border-color: #dadce0;
            --bg-page: #f8fafd;
            --card-bg: #ffffff;
            --green: #188038;
            --blue: #1a73e8;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg-page); color: var(--text-primary); padding: 24px 16px; }
        .container { max-width: 780px; margin: 0 auto; }
        .track-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 1px solid var(--border-color); }
        .track-logo { font-size: 20px; font-weight: 800; color: #1e3a8a; text-decoration: none; }
        .g-card { background: var(--card-bg); border: 1px solid var(--border-color); border-radius: 16px; padding: 24px; margin-bottom: 20px; box-shadow: 0 1px 3px rgba(60,64,67,0.08); }
        .search-bar { display: flex; gap: 10px; margin-bottom: 24px; }
        .search-bar input { flex: 1; padding: 12px 16px; border: 1px solid var(--border-color); border-radius: 10px; font-size: 15px; font-weight: 600; text-transform: uppercase; }
        .search-bar button { background: #ff5722; color: #fff; border: none; padding: 12px 24px; border-radius: 10px; font-weight: 700; cursor: pointer; }
        .badge { display: inline-block; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 700; text-transform: uppercase; }
        .badge-success { background: #e6f4ea; color: #137333; }
        .badge-transit { background: #e8f0fe; color: #1967d2; }
        .badge-carrier { background: #fef7e0; color: #b06000; }
        .info-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin: 16px 0; padding: 16px 0; border-top: 1px solid #f1f3f4; border-bottom: 1px solid #f1f3f4; }
        .info-item label { display: block; font-size: 11px; text-transform: uppercase; color: var(--text-secondary); margin-bottom: 4px; font-weight: 600; }
        .info-item span { font-weight: 700; font-size: 14px; }
        .timeline { position: relative; padding-left: 32px; margin-top: 20px; }
        .timeline::before { content: ''; position: absolute; left: 11px; top: 6px; bottom: 6px; width: 2px; background: #dadce0; }
        .timeline-step { position: relative; margin-bottom: 24px; }
        .timeline-step:last-child { margin-bottom: 0; }
        .timeline-dot { position: absolute; left: -32px; top: 2px; width: 24px; height: 24px; border-radius: 50%; background: #fff; border: 2px solid #dadce0; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 800; color: #5f6368; }
        .timeline-step.completed .timeline-dot { border-color: var(--green); background: var(--green); color: #fff; }
        .timeline-step.current .timeline-dot { border-color: var(--blue); background: var(--blue); color: #fff; box-shadow: 0 0 0 4px rgba(26,115,232,0.2); }
        .timeline-step h4 { font-size: 14px; font-weight: 700; margin-bottom: 2px; }
        .timeline-step p { font-size: 12px; color: var(--text-secondary); margin-bottom: 2px; }
        .timeline-step small { font-size: 11px; color: var(--text-secondary); font-weight: 600; }
    </style>
</head>
<body>
<div class="container">
    <div class="track-header">
        <a href="/" class="track-logo">Kesari Express Logistics</a>
        <a href="/" style="font-size: 13px; color: var(--text-secondary); text-decoration: none;">← Back to Home</a>
    </div>

    <!-- Tracking Input Bar -->
    <form method="GET" action="/track" class="search-bar">
        <input type="text" name="awb" placeholder="Enter Consignment / Docket / AWB Number..." value="<?= htmlspecialchars($_GET['awb'] ?? '') ?>" required autofocus>
        <button type="submit">Track Shipment</button>
    </form>

    <?php if (empty($trackingResult) && !empty($_GET['awb'])): ?>
        <div class="g-card" style="text-align:center; padding:48px 24px;">
            <div style="font-size:40px; margin-bottom:12px;">🔍</div>
            <h3 style="margin-bottom:8px;">Docket Number Not Found</h3>
            <p style="color:var(--text-secondary); font-size:13px; max-width:440px; margin:0 auto;">
                We could not find any active booking or external courier scan for <code><?= htmlspecialchars($_GET['awb']) ?></code>. Please verify the AWB number or check with your booking branch.
            </p>
        </div>
    <?php elseif (!empty($trackingResult)): ?>
        <div class="g-card">
            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                <div>
                    <span style="font-size:12px; color:var(--text-secondary); font-weight:600;">CONSIGNMENT DOCKET</span>
                    <h2 style="font-size:24px; font-family:'Space Grotesk',sans-serif; letter-spacing:1px; margin:2px 0 6px 0;">
                        <?= htmlspecialchars($trackingResult['consignment_no']) ?>
                    </h2>
                    <div style="display:flex; gap:8px; align-items:center;">
                        <span class="badge badge-transit"><?= htmlspecialchars($trackingResult['status_label']) ?></span>
                        <?php if (!empty($trackingResult['courier_name'])): ?>
                            <span class="badge badge-carrier">⚡ <?= htmlspecialchars($trackingResult['courier_name']) ?> Gateway</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (!empty($trackingResult['external_tracking_url'])): ?>
                    <a href="<?= htmlspecialchars($trackingResult['external_tracking_url']) ?>" target="_blank" style="font-size:12px; color:var(--brand-primary); font-weight:700; text-decoration:none; padding:6px 12px; border:1px solid var(--border-color); border-radius:8px;">
                        Open Carrier Tracking ↗
                    </a>
                <?php endif; ?>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <label>Origin Branch</label>
                    <span><?= htmlspecialchars($trackingResult['origin']) ?></span>
                </div>
                <div class="info-item">
                    <label>Destination City</label>
                    <span><?= htmlspecialchars($trackingResult['destination']) ?></span>
                </div>
                <div class="info-item">
                    <label>Booking Date</label>
                    <span><?= htmlspecialchars($trackingResult['booking_date']) ?></span>
                </div>
                <div class="info-item">
                    <label>Consignee / Recipient</label>
                    <span><?= htmlspecialchars($trackingResult['recipient_masked']) ?></span>
                </div>
                <div class="info-item">
                    <label>Mode / Speed</label>
                    <span><?= htmlspecialchars($trackingResult['mode']) ?></span>
                </div>
                <div class="info-item">
                    <label>Billable Weight</label>
                    <span><?= htmlspecialchars($trackingResult['weight']) ?></span>
                </div>
            </div>

            <!-- Milestones Timeline -->
            <div style="font-weight:700; font-size:14px; margin-bottom:14px;">Shipment Activity & Delivery Milestones</div>
            <div class="timeline">
                <?php foreach ($trackingResult['milestones'] as $idx => $m): ?>
                    <div class="timeline-step <?= !empty($m['current']) ? 'current' : (!empty($m['completed']) ? 'completed' : '') ?>">
                        <div class="timeline-dot">
                            <?= !empty($m['completed']) ? '✓' : ($idx + 1) ?>
                        </div>
                        <h4><?= htmlspecialchars($m['title']) ?></h4>
                        <p><?= htmlspecialchars($m['description']) ?></p>
                        <small><?= htmlspecialchars($m['date']) ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
</body>
</html>

<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('staff');

$title = 'Staff Dashboard';
$is_dash = true;
require_once __DIR__ . '/../includes/header_dash.php';

// Quick stats
$statPendingFacility = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='pending'")->fetchColumn();
$statPendingEquipment = $pdo->query("SELECT COUNT(*) FROM equipment_bookings WHERE status='pending'")->fetchColumn();
$statPendingBookings = $statPendingFacility + $statPendingEquipment;
$statMaintenance = $pdo->query("SELECT COUNT(*) FROM maintenance WHERE status IN ('scheduled', 'in_progress')")->fetchColumn();
?>

<div class="sb-page-header">
    <h2><?= render_icon('home', '', 20) ?> Staff Overview</h2>
</div>

<div class="sb-stat-grid" style="margin-bottom:2rem;">
    <div class="sb-stat-card sb-fade">
        <div class="sb-stat-icon" style="color:#d97706;background:#fef3c7;"><?= render_icon('calendar', '', 24) ?></div>
        <div class="sb-stat-info">
            <div class="sb-stat-value"><?= number_format($statPendingBookings) ?></div>
            <div class="sb-stat-label">Pending Bookings</div>
        </div>
    </div>
    <div class="sb-stat-card sb-fade" style="animation-delay:0.1s;">
        <div class="sb-stat-icon" style="color:#2563eb;background:#dbeafe;"><?= render_icon('tool', '', 24) ?></div>
        <div class="sb-stat-info">
            <div class="sb-stat-value"><?= number_format($statMaintenance) ?></div>
            <div class="sb-stat-label">Active Maintenance</div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr;gap:2rem;">
    <div class="sb-card sb-fade" style="animation-delay:0.2s;">
        <div class="sb-card-header">
            <h3><?= render_icon('calendar', '', 16) ?> Action Required (Pending Bookings)</h3>
            <a href="bookings.php" class="sb-btn sb-btn-sm sb-btn-ghost">Manage Bookings</a>
        </div>
        <div class="sb-table-wrap">
            <table class="sb-table">
                <thead><tr><th>Student</th><th>Facility</th><th>Equipment</th><th>Date</th><th>Status</th></tr></thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT b.*, u.name as user_name, u.avatar as user_avatar, f.name as facility_name,
                                         (SELECT GROUP_CONCAT(CONCAT(eb.quantity, 'x ', e.name) SEPARATOR ', ')
                                          FROM equipment_bookings eb 
                                          JOIN equipments e ON eb.equipment_id = e.id 
                                          WHERE eb.booking_id = b.id) as equipment_details
                                         FROM bookings b 
                                         JOIN users u ON b.user_id = u.id 
                                         JOIN facilities f ON b.facility_id = f.id 
                                         WHERE b.status='pending' ORDER BY b.created ASC LIMIT 5");
                    $pending = $stmt->fetchAll();
                    foreach ($pending as $p):
                    ?>
                    <tr>
                        <td>
                            <div class="sb-table-avatar">
                                <?php if(!empty($p['user_avatar'])): ?>
                                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars($p['user_avatar']) ?>" alt="avatar" class="sb-mini-avatar" style="padding:0; object-fit:cover; border:none;">
                                <?php else: ?>
                                    <div class="sb-mini-avatar"><?= strtoupper(substr($p['user_name'],0,1)) ?></div>
                                <?php endif; ?>
                                <strong style="white-space: nowrap;"><?= htmlspecialchars($p['user_name']) ?></strong>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($p['facility_name']) ?></td>
                        <td>
                            <?php if ($p['equipment_details']): ?>
                                <span style="color:var(--muted-foreground); font-size:0.9rem;"><?= htmlspecialchars($p['equipment_details']) ?></span>
                            <?php else: ?>
                                <span style="color:var(--border);">—</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($p['booking_date']) ?></td>
                        <td>
                            <div style="display:flex; gap: 0.5rem; align-items: center;">
                                <span class="sb-badge sb-badge-<?= htmlspecialchars($p['status']) ?>"><?= htmlspecialchars($p['status']) ?></span>
                                <a href="bookings.php" class="sb-btn sb-btn-sm sb-btn-primary" style="padding: 0.2rem 0.5rem; font-size: 0.75rem;">Manage</a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; if(empty($pending)): ?>
                    <tr class="sb-table-empty"><td colspan="5">No pending bookings.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/charts.php'; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

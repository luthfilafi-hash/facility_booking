<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('staff');

// Handle Maintenance Quick Complete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'complete_maintenance' && isset($_POST['maintenance_id'])) {
    $mId = $_POST['maintenance_id'];
    $stmt = $pdo->prepare("UPDATE maintenance SET status = 'completed', modified = NOW() WHERE id = ?");
    $stmt->execute([$mId]);
    setFlash('Maintenance marked as completed.');
    header('Location: index.php');
    exit;
}

$title = 'Staff Dashboard';
$is_dash = true;
require_once __DIR__ . '/../includes/header_dash.php';

// Quick stats
$statPendingFacility = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='pending'")->fetchColumn();
$statPendingEquipment = $pdo->query("SELECT COUNT(*) FROM equipment_bookings WHERE status='pending'")->fetchColumn();
$statPendingBookings = $statPendingFacility + $statPendingEquipment;
$statMaintenance = $pdo->query("SELECT COUNT(*) FROM maintenance WHERE status IN ('scheduled', 'in_progress')")->fetchColumn();

// Check for overdue maintenance
$overdueMaintenance = $pdo->query("
    SELECT m.*, f.name as facility_name 
    FROM maintenance m 
    JOIN facilities f ON m.facility_id = f.id 
    WHERE COALESCE(m.end_date, m.start_date) < CURDATE() 
      AND m.status != 'completed'
    LIMIT 1
")->fetch();
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

<?php if ($overdueMaintenance): ?>
<div id="maintenanceModalOverlay" style="position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.7); z-index:9999; display:flex; justify-content:center; align-items:center; backdrop-filter:blur(5px);">
    <div class="sb-card sb-fade" style="width: 100%; max-width: 500px; margin: 1rem; border: 1px solid var(--border);">
        <div class="sb-card-header" style="background: var(--card); border-bottom: 1px solid var(--border);">
            <h3><?= render_icon('alert-triangle', '', 20) ?> Overdue Maintenance</h3>
        </div>
        <div class="sb-card-body" style="padding: 1.5rem;">
            <p style="margin-bottom: 1rem;">The maintenance for <strong><?= htmlspecialchars($overdueMaintenance['facility_name']) ?></strong> is overdue.</p>
            <p style="margin-bottom: 1.5rem; color: var(--muted-foreground); font-size: 0.9rem;">
                Scheduled: <?= htmlspecialchars($overdueMaintenance['start_date']) ?> 
                <?= $overdueMaintenance['end_date'] ? ' to ' . htmlspecialchars($overdueMaintenance['end_date']) : '' ?>
            </p>
            <div style="display:flex; flex-wrap: wrap; gap: 1rem;">
                <form method="POST" style="margin:0;">
                    <input type="hidden" name="action" value="complete_maintenance">
                    <input type="hidden" name="maintenance_id" value="<?= $overdueMaintenance['id'] ?>">
                    <button type="submit" class="sb-btn sb-btn-primary"><?= render_icon('check', '', 16) ?> Mark as Complete</button>
                </form>
                <a href="maintenance.php?action=edit&id=<?= $overdueMaintenance['id'] ?>" class="sb-btn sb-btn-outline"><?= render_icon('calendar', '', 16) ?> Change Date</a>
                <button type="button" class="sb-btn sb-btn-ghost" onclick="document.getElementById('maintenanceModalOverlay').style.display='none'"><?= render_icon('x', '', 16) ?> Dismiss</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

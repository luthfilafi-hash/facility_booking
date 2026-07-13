<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');

// Handle Maintenance Quick Complete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'complete_maintenance' && isset($_POST['maintenance_id'])) {
    $mId = $_POST['maintenance_id'];
    $stmt = $pdo->prepare("UPDATE maintenance SET status = 'completed', modified = NOW() WHERE id = ?");
    $stmt->execute([$mId]);
    setFlash('Maintenance marked as completed.');
    header('Location: index.php');
    exit;
}

$title = 'Admin Dashboard';
$is_dash = true;
require_once __DIR__ . '/../includes/header_dash.php';

// Quick stats
$statUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$statFacilities = $pdo->query("SELECT COUNT(*) FROM facilities")->fetchColumn();
$statFacilityBookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$statEquipmentBookings = $pdo->query("SELECT COUNT(*) FROM equipment_bookings")->fetchColumn();
$statBookings = $statFacilityBookings + $statEquipmentBookings;
$statEquipments = $pdo->query("SELECT COUNT(*) FROM equipments")->fetchColumn();

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
    <h2><?= render_icon('home', '', 20) ?> Overview</h2>
</div>

<div class="sb-stat-grid" style="margin-bottom:2rem;">
    <div class="sb-stat-card sb-fade">
        <div class="sb-stat-icon" style="color:var(--primary);background:var(--accent);"><?= render_icon('users', '', 24) ?></div>
        <div class="sb-stat-info">
            <div class="sb-stat-value"><?= number_format($statUsers) ?></div>
            <div class="sb-stat-label">Total Users</div>
        </div>
    </div>
    <div class="sb-stat-card sb-fade" style="animation-delay:0.1s;">
        <div class="sb-stat-icon" style="color:#059669;background:#d1fae5;"><?= render_icon('building', '', 24) ?></div>
        <div class="sb-stat-info">
            <div class="sb-stat-value"><?= number_format($statFacilities) ?></div>
            <div class="sb-stat-label">Facilities</div>
        </div>
    </div>
    <div class="sb-stat-card sb-fade" style="animation-delay:0.2s;">
        <div class="sb-stat-icon" style="color:#2563eb;background:#dbeafe;"><?= render_icon('calendar', '', 24) ?></div>
        <div class="sb-stat-info">
            <div class="sb-stat-value"><?= number_format($statBookings) ?></div>
            <div class="sb-stat-label">Total Bookings</div>
        </div>
    </div>
    <div class="sb-stat-card sb-fade" style="animation-delay:0.3s;">
        <div class="sb-stat-icon" style="color:#d97706;background:#fef3c7;"><?= render_icon('box', '', 24) ?></div>
        <div class="sb-stat-info">
            <div class="sb-stat-value"><?= number_format($statEquipments) ?></div>
            <div class="sb-stat-label">Equipment Items</div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr;gap:2rem;">
    <div class="sb-card sb-fade" style="animation-delay:0.4s;">
        <div class="sb-card-header">
            <h3><?= render_icon('calendar', '', 16) ?> Recent Bookings</h3>
            <a href="bookings.php" class="sb-btn sb-btn-sm sb-btn-ghost">View All</a>
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
                                         ORDER BY b.created DESC LIMIT 5");
                    $recent = $stmt->fetchAll();
                    foreach ($recent as $r):
                    ?>
                    <tr>
                        <td>
                            <div class="sb-table-avatar">
                                <?php if(!empty($r['user_avatar'])): ?>
                                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars($r['user_avatar']) ?>" alt="avatar" class="sb-mini-avatar" style="padding:0; object-fit:cover; border:none;">
                                <?php else: ?>
                                    <div class="sb-mini-avatar"><?= strtoupper(substr($r['user_name'],0,1)) ?></div>
                                <?php endif; ?>
                                <strong style="white-space: nowrap;"><?= htmlspecialchars($r['user_name']) ?></strong>
                            </div>
                        </td>
                        <td><?= htmlspecialchars($r['facility_name']) ?></td>
                        <td>
                            <?php if ($r['equipment_details']): ?>
                                <span style="color:var(--muted-foreground); font-size:0.9rem;"><?= htmlspecialchars($r['equipment_details']) ?></span>
                            <?php else: ?>
                                <span style="color:var(--border);">—</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($r['booking_date']) ?></td>
                        <td><span class="sb-badge sb-badge-<?= htmlspecialchars($r['status']) ?>"><?= htmlspecialchars($r['status']) ?></span></td>
                    </tr>
                    <?php endforeach; if(empty($recent)): ?>
                    <tr class="sb-table-empty"><td colspan="5">No recent bookings.</td></tr>
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

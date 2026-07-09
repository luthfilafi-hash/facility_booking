<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('student');
$user = getUser();

if (!empty($_POST)) {
    file_put_contents(__DIR__ . '/debug_post.txt', print_r($_POST, true) . "\nUser ID: " . $user['id'] . "\n", FILE_APPEND);
}

if (isset($_POST['action']) && isset($_POST['id'])) {
    if ($_POST['action'] === 'cancel' || $_POST['action'] === 'delete') {
        try {
            $pdo->beginTransaction();
            if ($_POST['type'] === 'facility') {
                $b_stmt = $pdo->prepare("SELECT facility_id, booking_date, status FROM bookings WHERE id = ? AND user_id = ?");
                $b_stmt->execute([$_POST['id'], $user['id']]);
                $booking = $b_stmt->fetch();
                
                if ($booking) {
                    $eq_b_stmt = $pdo->prepare("SELECT id, equipment_id, quantity, status FROM equipment_bookings WHERE booking_id = ?");
                    $eq_b_stmt->execute([$_POST['id']]);
                    $assoc_eq = $eq_b_stmt->fetchAll();
                    
                    if (!empty($assoc_eq)) {
                        $delete_eq_stmt = $pdo->prepare("DELETE FROM equipment_bookings WHERE id = ?");
                        $restore_eq_stmt = $pdo->prepare("UPDATE equipments SET available_quantity = available_quantity + ? WHERE id = ?");
                        foreach ($assoc_eq as $eq) {
                            if (in_array($eq['status'], ['pending', 'approved'])) {
                                $restore_eq_stmt->execute([$eq['quantity'], $eq['equipment_id']]);
                            }
                            $delete_eq_stmt->execute([$eq['id']]);
                        }
                    }
                    
                    $pdo->prepare("DELETE FROM bookings WHERE id = ?")->execute([$_POST['id']]);
                    setFlash('Booking deleted successfully.');
                } else {
                    setFlash('Could not delete booking.', 'error');
                }
            } else {
                $b_stmt = $pdo->prepare("SELECT equipment_id, quantity, status FROM equipment_bookings WHERE id = ? AND user_id = ?");
                $b_stmt->execute([$_POST['id'], $user['id']]);
                $booking = $b_stmt->fetch();
                
                if ($booking) {
                    if (in_array($booking['status'], ['pending', 'approved'])) {
                        $pdo->prepare("UPDATE equipments SET available_quantity = available_quantity + ? WHERE id = ?")->execute([$booking['quantity'], $booking['equipment_id']]);
                    }
                    $pdo->prepare("DELETE FROM equipment_bookings WHERE id = ?")->execute([$_POST['id']]);
                    setFlash('Equipment booking deleted successfully.');
                } else {
                    setFlash('Could not delete equipment booking.', 'error');
                }
            }
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            setFlash('An error occurred while deleting: ' . $e->getMessage(), 'error');
        }
        header('Location: index.php'); exit;
    }
}

$title = 'My Dashboard';
$is_dash = true;
require_once __DIR__ . '/../includes/header_dash.php';

$stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ?");
$stmt->execute([$user['id']]);
$totalFacilityBookings = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM equipment_bookings WHERE user_id = ?");
$stmt->execute([$user['id']]);
$totalEquipmentBookings = $stmt->fetchColumn();

$totalBookings = $totalFacilityBookings + $totalEquipmentBookings;

$stmt = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = ? AND status='approved'");
$stmt->execute([$user['id']]);
$approvedFacility = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT COUNT(*) FROM equipment_bookings WHERE user_id = ? AND status='approved'");
$stmt->execute([$user['id']]);
$approvedEquipment = $stmt->fetchColumn();

$approvedBookings = $approvedFacility + $approvedEquipment;
?>

<div class="sb-page-header">
    <h2><?= render_icon('home', '', 20) ?> Welcome, <?= htmlspecialchars(explode(' ', $user['name'])[0]) ?></h2>
    <a href="book.php" class="sb-btn sb-btn-primary"><?= render_icon('plus', '', 15) ?> New Booking</a>
</div>

<div class="sb-stat-grid" style="margin-bottom:2rem;">
    <div class="sb-stat-card sb-fade">
        <div class="sb-stat-icon" style="color:var(--primary);background:var(--accent);"><?= render_icon('calendar', '', 24) ?></div>
        <div class="sb-stat-info">
            <div class="sb-stat-value"><?= number_format($totalBookings) ?></div>
            <div class="sb-stat-label">Total Bookings</div>
        </div>
    </div>
    <div class="sb-stat-card sb-fade" style="animation-delay:0.1s;">
        <div class="sb-stat-icon" style="color:#059669;background:#d1fae5;"><?= render_icon('check', '', 24) ?></div>
        <div class="sb-stat-info">
            <div class="sb-stat-value"><?= number_format($approvedBookings) ?></div>
            <div class="sb-stat-label">Approved</div>
        </div>
    </div>
</div>

<div class="sb-card sb-fade" style="animation-delay:0.2s;">
    <div class="sb-card-header">
        <h3><?= render_icon('calendar', '', 16) ?> My Recent Bookings</h3>
    </div>
    <div class="sb-table-wrap">
        <table class="sb-table">
            <thead><tr><th>Facility</th><th>Equipment</th><th>Date</th><th>Timeslot</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
                <?php
                $stmt = $pdo->prepare("SELECT b.*, f.name as facility_name, t.start_time, t.end_time,
                                       (SELECT GROUP_CONCAT(CONCAT(eb.quantity, 'x ', e.name) SEPARATOR ', ')
                                        FROM equipment_bookings eb 
                                        JOIN equipments e ON eb.equipment_id = e.id 
                                        WHERE eb.booking_id = b.id) as equipment_details
                                       FROM bookings b JOIN facilities f ON b.facility_id = f.id 
                                       LEFT JOIN availability t ON b.timeslot_id = t.id 
                                       WHERE b.user_id = ? ORDER BY b.created DESC");
                $stmt->execute([$user['id']]);
                $myBookings = $stmt->fetchAll();
                foreach ($myBookings as $b):
                ?>
                <tr>
                    <td><strong><?= htmlspecialchars($b['facility_name']) ?></strong></td>
                    <td>
                        <?php if ($b['equipment_details']): ?>
                            <span style="color:var(--muted-foreground); font-size:0.9rem;"><?= htmlspecialchars($b['equipment_details']) ?></span>
                        <?php else: ?>
                            <span style="color:var(--border);">—</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($b['booking_date']) ?></td>
                    <td><?= htmlspecialchars($b['start_time'] ? substr($b['start_time'],0,5).' - '.substr($b['end_time'],0,5) : '—') ?></td>
                    <td><span class="sb-badge sb-badge-<?= htmlspecialchars($b['status']) ?>"><?= htmlspecialchars($b['status']) ?></span></td>
                    <td class="sb-actions">
                        <?php if ($b['status'] === 'pending'): ?>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Cancel this facility booking?');">
                            <input type="hidden" name="action" value="cancel"><input type="hidden" name="type" value="facility"><input type="hidden" name="id" value="<?= $b['id'] ?>">
                            <button type="submit" class="sb-btn sb-btn-sm sb-btn-danger" title="Cancel Booking"><?= render_icon('x', '', 14) ?></button>
                        </form>
                        <?php elseif (in_array($b['status'], ['cancelled', 'rejected'])): ?>
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Permanently delete this record?');">
                            <input type="hidden" name="action" value="delete"><input type="hidden" name="type" value="facility"><input type="hidden" name="id" value="<?= $b['id'] ?>">
                            <button type="submit" class="sb-btn sb-btn-sm" style="color:var(--muted-foreground); border-color:var(--border);" title="Delete Record"><?= render_icon('trash', '', 14) ?></button>
                        </form>
                        <?php else: ?>
                        <span style="color:var(--muted-foreground);font-size:0.8rem;">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; if(empty($myBookings)): ?>
                <tr class="sb-table-empty"><td colspan="6">You haven't made any facility bookings yet.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/charts.php'; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

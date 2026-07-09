<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');

if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
    $stmt = $pdo->prepare("DELETE FROM availability WHERE id = ?");
    if ($stmt->execute([$_POST['id']])) setFlash('Timeslot deleted.');
    else setFlash('Failed to delete timeslot.', 'error');
    header('Location: availability.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($_POST['action']??'', ['add', 'edit'])) {
    $id = $_POST['id'] ?? null;
    $facility_id = empty($_POST['facility_id']) ? null : $_POST['facility_id'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];
    $day_of_week = empty($_POST['day_of_week']) ? null : $_POST['day_of_week'];
    $status = $_POST['status'];

    if ($_POST['action'] === 'add') {
        $stmt = $pdo->prepare("INSERT INTO availability (facility_id, start_time, end_time, day_of_week, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$facility_id, $start_time, $end_time, $day_of_week, $status]);
        setFlash('Timeslot added.');
    } else {
        $stmt = $pdo->prepare("UPDATE availability SET facility_id=?, start_time=?, end_time=?, day_of_week=?, status=? WHERE id=?");
        $stmt->execute([$facility_id, $start_time, $end_time, $day_of_week, $status, $id]);
        setFlash('Timeslot updated.');
    }
    header('Location: availability.php'); exit;
}

$action = $_GET['action'] ?? 'index';
$title = 'Availability';
$is_dash = true;
require_once __DIR__ . '/../includes/header_dash.php';

if ($action === 'index'):
    $availability = $pdo->query("SELECT t.*, f.name as facility_name FROM availability t LEFT JOIN facilities f ON t.facility_id = f.id ORDER BY t.day_of_week, t.start_time")->fetchAll();
?>
<div class="sb-page-header">
    <h2><?= render_icon('clock', '', 20) ?> Availability</h2>
    <a href="?action=add" class="sb-btn sb-btn-primary"><?= render_icon('plus', '', 15) ?> Add Timeslot</a>
</div>
<div class="sb-card sb-fade">
    <div class="sb-table-wrap">
    <table class="sb-table">
        <thead><tr><th>Facility</th><th>Time</th><th>Day</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($availability as $t): ?>
        <tr>
            <td><strong><?= htmlspecialchars($t['facility_name'] ?? '—') ?></strong></td>
            <td><?= htmlspecialchars(substr($t['start_time'],0,5)) ?> - <?= htmlspecialchars(substr($t['end_time'],0,5)) ?></td>
            <td><?= htmlspecialchars($t['day_of_week'] ?? '—') ?></td>
            <td><span class="sb-badge sb-badge-<?= htmlspecialchars($t['status'] ?? 'available') ?>"><?= htmlspecialchars($t['status'] ?? 'available') ?></span></td>
            <td class="sb-actions">
                <a href="?action=edit&id=<?= $t['id'] ?>" class="sb-btn sb-btn-sm sb-btn-outline" title="Edit"><?= render_icon('edit', '', 14) ?></a>
                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete timeslot?');">
                    <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $t['id'] ?>">
                    <button type="submit" class="sb-btn sb-btn-sm sb-btn-danger" title="Delete"><?= render_icon('trash', '', 14) ?></button>
                </form>
            </td>
        </tr>
        <?php endforeach; if(empty($availability)): ?><tr class="sb-table-empty"><td colspan="5">No availability found.</td></tr><?php endif; ?>
        </tbody>
    </table>
    </div>
</div>
<?php elseif ($action === 'add' || $action === 'edit'): 
    $t = null;
    if ($action === 'edit') {
        $stmt = $pdo->prepare("SELECT * FROM availability WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $t = $stmt->fetch();
    }
    $facilities = $pdo->query("SELECT id, name FROM facilities")->fetchAll();
?>
<div class="sb-page-header">
    <h2><?= render_icon($action==='add'?'plus':'edit', '', 20) ?> <?= $action==='add'?'Add':'Edit' ?> Timeslot</h2>
    <a href="availability.php" class="sb-btn sb-btn-ghost">Back</a>
</div>
<div class="sb-form-card sb-fade">
    <div class="sb-form-header"><?= render_icon('clock', '', 18) ?><h2>Timeslot Details</h2></div>
    <div class="sb-form-body">
        <form method="POST">
            <input type="hidden" name="action" value="<?= $action ?>">
            <?php if($t): ?><input type="hidden" name="id" value="<?= $t['id'] ?>"><?php endif; ?>
            <div class="sb-form-group">
                <label>Facility</label>
                <select name="facility_id" class="sb-form-input">
                    <option value="">-- Any Facility --</option>
                    <?php foreach($facilities as $f): ?>
                    <option value="<?= $f['id'] ?>" <?= ($t['facility_id']??'')==$f['id']?'selected':'' ?>><?= htmlspecialchars($f['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="sb-form-row">
                <div class="sb-form-group"><label>Start Time</label><input type="time" name="start_time" class="sb-form-input" required value="<?= htmlspecialchars($t['start_time'] ?? '') ?>"></div>
                <div class="sb-form-group"><label>End Time</label><input type="time" name="end_time" class="sb-form-input" required value="<?= htmlspecialchars($t['end_time'] ?? '') ?>"></div>
            </div>
            <div class="sb-form-row">
                <div class="sb-form-group">
                    <label>Day of Week</label>
                    <select name="day_of_week" class="sb-form-input">
                        <option value="">-- Any Day --</option>
                        <?php foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $d): ?>
                        <option value="<?= $d ?>" <?= ($t['day_of_week']??'')===$d?'selected':'' ?>><?= $d ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="sb-form-group">
                    <label>Status</label>
                    <select name="status" class="sb-form-input">
                        <option value="available" <?= ($t['status']??'')==='available'?'selected':'' ?>>Available</option>
                        <option value="booked" <?= ($t['status']??'')==='booked'?'selected':'' ?>>Booked</option>
                        <option value="unavailable" <?= ($t['status']??'')==='unavailable'?'selected':'' ?>>Unavailable</option>
                    </select>
                </div>
            </div>
            <div class="sb-form-actions">
                <button type="submit" class="sb-btn sb-btn-primary"><?= render_icon('check', '', 15) ?> Save Timeslot</button>
                <a href="availability.php" class="sb-btn sb-btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php endif; require_once __DIR__ . '/../includes/footer.php'; ?>

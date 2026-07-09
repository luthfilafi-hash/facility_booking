<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');

if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
    $stmt = $pdo->prepare("DELETE FROM maintenance WHERE id = ?");
    if ($stmt->execute([$_POST['id']])) setFlash('Maintenance record deleted.');
    else setFlash('Failed to delete record.', 'error');
    header('Location: maintenance.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($_POST['action']??'', ['add', 'edit'])) {
    $id = $_POST['id'] ?? null;
    $facility_id = $_POST['facility_id'];
    $start_date = $_POST['start_date'];
    $end_date = empty($_POST['end_date']) ? null : $_POST['end_date'];
    $status = $_POST['status'];
    $description = $_POST['description'];

    if ($_POST['action'] === 'add') {
        $stmt = $pdo->prepare("INSERT INTO maintenance (facility_id, start_date, end_date, status, description, created, modified) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([$facility_id, $start_date, $end_date, $status, $description]);
        
        if (in_array($status, ['scheduled', 'in_progress'])) {
            if (empty($end_date)) {
                $cancelStmt = $pdo->prepare("UPDATE bookings SET status='cancelled', notes='Automatically cancelled due to facility maintenance' WHERE facility_id = ? AND booking_date = ? AND status IN ('pending', 'approved')");
                $cancelStmt->execute([$facility_id, $start_date]);
            } else {
                $cancelStmt = $pdo->prepare("UPDATE bookings SET status='cancelled', notes='Automatically cancelled due to facility maintenance' WHERE facility_id = ? AND booking_date >= ? AND booking_date <= ? AND status IN ('pending', 'approved')");
                $cancelStmt->execute([$facility_id, $start_date, $end_date]);
            }
        }
        
        setFlash('Maintenance scheduled.');
    } else {
        $stmt = $pdo->prepare("UPDATE maintenance SET facility_id=?, start_date=?, end_date=?, status=?, description=?, modified=NOW() WHERE id=?");
        $stmt->execute([$facility_id, $start_date, $end_date, $status, $description, $id]);
        
        if (in_array($status, ['scheduled', 'in_progress'])) {
            if (empty($end_date)) {
                $cancelStmt = $pdo->prepare("UPDATE bookings SET status='cancelled', notes='Automatically cancelled due to facility maintenance' WHERE facility_id = ? AND booking_date = ? AND status IN ('pending', 'approved')");
                $cancelStmt->execute([$facility_id, $start_date]);
            } else {
                $cancelStmt = $pdo->prepare("UPDATE bookings SET status='cancelled', notes='Automatically cancelled due to facility maintenance' WHERE facility_id = ? AND booking_date >= ? AND booking_date <= ? AND status IN ('pending', 'approved')");
                $cancelStmt->execute([$facility_id, $start_date, $end_date]);
            }
        }
        
        setFlash('Maintenance updated.');
    }
    header('Location: maintenance.php'); exit;
}

$action = $_GET['action'] ?? 'index';
$title = 'Maintenance';
$is_dash = true;
require_once __DIR__ . '/../includes/header_dash.php';

if ($action === 'index'):
    $maint = $pdo->query("SELECT m.*, f.name as facility_name FROM maintenance m JOIN facilities f ON m.facility_id = f.id ORDER BY m.start_date DESC")->fetchAll();
?>
<div class="sb-page-header">
    <h2><?= render_icon('tool', '', 20) ?> Maintenance</h2>
    <a href="?action=add" class="sb-btn sb-btn-primary"><?= render_icon('plus', '', 15) ?> Schedule</a>
</div>
<div class="sb-card sb-fade">
    <div class="sb-table-wrap">
    <table class="sb-table">
        <thead><tr><th>Facility</th><th>Start Date</th><th>End Date</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($maint as $m): ?>
        <tr>
            <td><strong><?= htmlspecialchars($m['facility_name']) ?></strong></td>
            <td><?= htmlspecialchars($m['start_date']) ?></td>
            <td><?= htmlspecialchars($m['end_date'] ?? '—') ?></td>
            <td><span class="sb-badge sb-badge-<?= htmlspecialchars($m['status']) ?>"><?= htmlspecialchars($m['status']) ?></span></td>
            <td class="sb-actions">
                <a href="?action=edit&id=<?= $m['id'] ?>" class="sb-btn sb-btn-sm sb-btn-outline" title="Edit"><?= render_icon('edit', '', 14) ?></a>
                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete record?');">
                    <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $m['id'] ?>">
                    <button type="submit" class="sb-btn sb-btn-sm sb-btn-danger" title="Delete"><?= render_icon('trash', '', 14) ?></button>
                </form>
            </td>
        </tr>
        <?php endforeach; if(empty($maint)): ?><tr class="sb-table-empty"><td colspan="5">No maintenance records.</td></tr><?php endif; ?>
        </tbody>
    </table>
    </div>
</div>
<?php elseif ($action === 'add' || $action === 'edit'): 
    $m = null;
    if ($action === 'edit') {
        $stmt = $pdo->prepare("SELECT * FROM maintenance WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $m = $stmt->fetch();
    }
    $facilities = $pdo->query("SELECT id, name FROM facilities")->fetchAll();
?>
<div class="sb-page-header">
    <h2><?= render_icon($action==='add'?'plus':'edit', '', 20) ?> <?= $action==='add'?'Schedule':'Edit' ?> Maintenance</h2>
    <a href="maintenance.php" class="sb-btn sb-btn-ghost">Back</a>
</div>
<div class="sb-form-card sb-fade">
    <div class="sb-form-header"><?= render_icon('tool', '', 18) ?><h2>Maintenance Details</h2></div>
    <div class="sb-form-body">
        <form method="POST">
            <input type="hidden" name="action" value="<?= $action ?>">
            <?php if($m): ?><input type="hidden" name="id" value="<?= $m['id'] ?>"><?php endif; ?>
            <div class="sb-form-row">
                <div class="sb-form-group">
                    <label>Facility</label>
                    <select name="facility_id" class="sb-form-input" required>
                        <option value="">-- Select --</option>
                        <?php foreach($facilities as $f): ?>
                        <option value="<?= $f['id'] ?>" <?= ($m['facility_id']??'')==$f['id']?'selected':'' ?>><?= htmlspecialchars($f['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="sb-form-group">
                    <label>Status</label>
                    <select name="status" class="sb-form-input" required>
                        <option value="scheduled" <?= ($m['status']??'')==='scheduled'?'selected':'' ?>>Scheduled</option>
                        <option value="in_progress" <?= ($m['status']??'')==='in_progress'?'selected':'' ?>>In Progress</option>
                        <option value="completed" <?= ($m['status']??'')==='completed'?'selected':'' ?>>Completed</option>
                    </select>
                </div>
            </div>
            <div class="sb-form-row">
                <div class="sb-form-group"><label>Start Date</label><input type="date" name="start_date" class="sb-form-input" required value="<?= htmlspecialchars($m['start_date'] ?? '') ?>"></div>
                <div class="sb-form-group"><label>End Date</label><input type="date" name="end_date" class="sb-form-input" value="<?= htmlspecialchars($m['end_date'] ?? '') ?>"></div>
            </div>
            <div class="sb-form-group">
                <label>Description</label>
                <textarea name="description" class="sb-form-input" rows="3"><?= htmlspecialchars($m['description'] ?? '') ?></textarea>
            </div>
            <div class="sb-form-actions">
                <button type="submit" class="sb-btn sb-btn-primary"><?= render_icon('check', '', 15) ?> Save Record</button>
                <a href="maintenance.php" class="sb-btn sb-btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php endif; require_once __DIR__ . '/../includes/footer.php'; ?>

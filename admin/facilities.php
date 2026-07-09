<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');

if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
    $stmt = $pdo->prepare("DELETE FROM facilities WHERE id = ?");
    if ($stmt->execute([$_POST['id']])) setFlash('Facility deleted.');
    else setFlash('Failed to delete facility.', 'error');
    header('Location: facilities.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && in_array($_POST['action']??'', ['add', 'edit'])) {
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'];
    $location = $_POST['location'];
    $capacity = empty($_POST['capacity']) ? null : $_POST['capacity'];
    $status = $_POST['status'];
    $description = $_POST['description'];

    $maintenance_time = empty($_POST['maintenance_time']) ? null : $_POST['maintenance_time'];
    $maintenance_end_time = empty($_POST['maintenance_end_time']) ? null : $_POST['maintenance_end_time'];

    $image_path = '';
    if ($_POST['action'] === 'edit') {
        $stmtImg = $pdo->prepare("SELECT image_path FROM facilities WHERE id = ?");
        $stmtImg->execute([$id]);
        $existing = $stmtImg->fetchColumn();
        $image_path = $existing ? $existing : '';
        
        if (!empty($_POST['remove_image']) && $_POST['remove_image'] == '1') {
            $image_path = '';
        }
    }

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../uploads/facilities/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $filename = uniqid() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = 'uploads/facilities/' . $filename;
        }
    }

    if ($_POST['action'] === 'add') {
        $stmt = $pdo->prepare("INSERT INTO facilities (name, location, capacity, status, maintenance_time, maintenance_end_time, description, image_path, created, modified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([$name, $location, $capacity, $status, $maintenance_time, $maintenance_end_time, $description, $image_path]);
        $fid = $pdo->lastInsertId();
        
        // Generate default slots so it appears in Check Availability
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $default_slots = [
            ['08:00:00', '10:00:00'],
            ['10:00:00', '12:00:00'],
            ['12:00:00', '14:00:00'],
            ['14:00:00', '16:00:00'],
            ['16:00:00', '18:00:00'],
        ];
        $slot_stmt = $pdo->prepare("INSERT INTO availability (facility_id, start_time, end_time, day_of_week, status) VALUES (?, ?, ?, ?, 'available')");
        foreach ($days as $day) {
            foreach ($default_slots as $slot) {
                $slot_stmt->execute([$fid, $slot[0], $slot[1], $day]);
            }
        }
        
        setFlash('Facility added.');
    } else {
        $stmt = $pdo->prepare("UPDATE facilities SET name=?, location=?, capacity=?, status=?, maintenance_time=?, maintenance_end_time=?, description=?, image_path=?, modified=NOW() WHERE id=?");
        $stmt->execute([$name, $location, $capacity, $status, $maintenance_time, $maintenance_end_time, $description, $image_path, $id]);
        $fid = $id;
        setFlash('Facility updated.');
    }

    // Smart Conflict Resolution
    if (in_array($status, ['maintenance', 'unavailable']) && $maintenance_time && $maintenance_end_time) {
        // Find conflicting bookings between start and end time
        // A booking is conflicting if booking_date + slot_start_time is between maintenance_time and maintenance_end_time
        // Or if it overlaps. To simplify, we check if the booking's exact start time falls within the maintenance window.
        $conflicts = $pdo->prepare("SELECT b.id, b.user_id, f.name as fname FROM bookings b JOIN facilities f ON b.facility_id = f.id JOIN availability a ON b.timeslot_id = a.id WHERE b.facility_id = ? AND b.status IN ('pending', 'approved') AND STR_TO_DATE(CONCAT(b.booking_date, ' ', a.start_time), '%Y-%m-%d %H:%i:%s') BETWEEN ? AND ?");
        $conflicts->execute([$fid, $maintenance_time, $maintenance_end_time]);
        $conflicting_bookings = $conflicts->fetchAll();

        foreach ($conflicting_bookings as $cb) {
            // Cancel booking
            $pdo->query("UPDATE bookings SET status = 'cancelled' WHERE id = " . (int)$cb['id']);
            
            // Send notification
            $notif = $pdo->prepare("INSERT INTO notifications (user_id, title, message, is_read, created) VALUES (?, ?, ?, 0, NOW())");
            $title = "Booking Cancelled (Maintenance)";
            $message = "We apologize, but your booking for {$cb['fname']} was automatically cancelled due to unexpected maintenance. Please re-book for another time.";
            $notif->execute([$cb['user_id'], $title, $message]);
        }
    }

    header('Location: facilities.php'); exit;
}

$action = $_GET['action'] ?? 'index';
$title = 'Facilities';
$is_dash = true;
require_once __DIR__ . '/../includes/header_dash.php';

if ($action === 'index'):
    $facilities = $pdo->query("SELECT * FROM facilities ORDER BY name ASC")->fetchAll();
?>
<div class="sb-page-header">
    <h2><?= render_icon('building', '', 20) ?> Facilities</h2>
    <a href="?action=add" class="sb-btn sb-btn-primary"><?= render_icon('plus', '', 15) ?> Add Facility</a>
</div>
<div class="sb-card sb-fade">
    <div class="sb-table-wrap">
    <table class="sb-table">
        <thead><tr><th>Name</th><th>Location</th><th>Status</th><th>Capacity</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($facilities as $f): ?>
        <tr>
            <td><strong><?= htmlspecialchars($f['name']) ?></strong></td>
            <td><?= render_icon('map-pin', '', 13) ?> <?= htmlspecialchars($f['location']) ?></td>
            <td><span class="sb-badge sb-badge-<?= htmlspecialchars($f['status']) ?>"><?= htmlspecialchars($f['status']) ?></span></td>
            <td><?= htmlspecialchars($f['capacity'] ?? '—') ?></td>
            <td class="sb-actions">
                <a href="?action=edit&id=<?= $f['id'] ?>" class="sb-btn sb-btn-sm sb-btn-outline" title="Edit"><?= render_icon('edit', '', 14) ?></a>
                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete facility?');">
                    <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $f['id'] ?>">
                    <button type="submit" class="sb-btn sb-btn-sm sb-btn-danger" title="Delete"><?= render_icon('trash', '', 14) ?></button>
                </form>
            </td>
        </tr>
        <?php endforeach; if(empty($facilities)): ?><tr class="sb-table-empty"><td colspan="5">No facilities found.</td></tr><?php endif; ?>
        </tbody>
    </table>
    </div>
</div>
<?php elseif ($action === 'add' || $action === 'edit'): 
    $f = null;
    if ($action === 'edit') {
        $stmt = $pdo->prepare("SELECT * FROM facilities WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $f = $stmt->fetch();
    }
?>
<div class="sb-page-header">
    <h2><?= render_icon($action==='add'?'plus':'edit', '', 20) ?> <?= $action==='add'?'Add':'Edit' ?> Facility</h2>
    <a href="facilities.php" class="sb-btn sb-btn-ghost">Back</a>
</div>
<div class="sb-form-card sb-fade">
    <div class="sb-form-header"><?= render_icon('building', '', 18) ?><h2>Facility Details</h2></div>
    <div class="sb-form-body">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="<?= $action ?>">
            <?php if($f): ?><input type="hidden" name="id" value="<?= $f['id'] ?>"><?php endif; ?>
            <div class="sb-form-row">
                <div class="sb-form-group">
                    <label>Name</label><input type="text" name="name" class="sb-form-input" required value="<?= htmlspecialchars($f['name'] ?? '') ?>">
                </div>
                <div class="sb-form-group">
                    <label>Location</label><input type="text" name="location" class="sb-form-input" value="<?= htmlspecialchars($f['location'] ?? '') ?>">
                </div>
            </div>
            <div class="sb-form-row">
                <div class="sb-form-group">
                    <label>Status</label>
                    <select name="status" class="sb-form-input" required>
                        <option value="available" <?= ($f['status']??'')==='available'?'selected':'' ?>>Available</option>
                        <option value="maintenance" <?= ($f['status']??'')==='maintenance'?'selected':'' ?>>Maintenance</option>
                        <option value="unavailable" <?= ($f['status']??'')==='unavailable'?'selected':'' ?>>Unavailable</option>
                    </select>
                </div>
                <div class="sb-form-group">
                    <label>Capacity</label><input type="number" name="capacity" class="sb-form-input" value="<?= htmlspecialchars($f['capacity'] ?? '') ?>">
                </div>
            </div>
            <div class="sb-form-row" id="maintenance-time-group" style="display: <?= in_array($f['status']??'', ['maintenance', 'unavailable']) ? 'flex' : 'none' ?>;">
                <div class="sb-form-group">
                    <label>Maintenance Start (Date & Time)</label>
                    <input type="datetime-local" name="maintenance_time" class="sb-form-input" value="<?= htmlspecialchars($f['maintenance_time'] ?? '') ?>">
                </div>
                <div class="sb-form-group">
                    <label>Maintenance End (Date & Time)</label>
                    <input type="datetime-local" name="maintenance_end_time" class="sb-form-input" value="<?= htmlspecialchars($f['maintenance_end_time'] ?? '') ?>">
                </div>
            </div>
            <div class="sb-form-group">
                <label>Description</label>
                <textarea name="description" class="sb-form-input" rows="3"><?= htmlspecialchars($f['description'] ?? '') ?></textarea>
            </div>
            <style>
                .sb-file-upload {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    padding: 2.5rem 2rem;
                    border: 2px dashed #4b5563;
                    border-radius: 12px;
                    background-color: rgba(31, 41, 55, 0.4);
                    color: #9ca3af;
                    cursor: pointer;
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                    text-align: center;
                    position: relative;
                    overflow: hidden;
                }
                .sb-file-upload:hover {
                    border-color: #3b82f6;
                    background-color: rgba(59, 130, 246, 0.1);
                    color: #e5e7eb;
                }
                .sb-file-upload:active {
                    transform: scale(0.99);
                }
                .sb-file-upload.has-file {
                    border-color: #10b981;
                    border-style: solid;
                    background-color: rgba(16, 185, 129, 0.05);
                }
                .sb-file-upload input[type="file"] {
                    display: none;
                }
                .sb-file-upload .upload-icon {
                    margin-bottom: 12px;
                    color: #60a5fa;
                    transition: transform 0.3s ease;
                }
                .sb-file-upload:hover .upload-icon {
                    transform: translateY(-4px);
                }
                .sb-file-upload-text {
                    font-size: 1.05rem;
                    font-weight: 600;
                    color: #e5e7eb;
                }
                .sb-file-upload-subtext {
                    font-size: 0.85rem;
                    margin-top: 6px;
                    opacity: 0.6;
                }
                .sb-file-preview-img {
                    max-height: 200px;
                    border-radius: 10px;
                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2);
                    border: 1px solid #374151;
                    object-fit: cover;
                }
            </style>

            <div class="sb-form-group">
                <label>Facility Image</label>
                <?php if(!empty($f['image_path'])): ?>
                    <div style="margin-bottom: 15px; text-align: center; position: relative; display: inline-block;">
                        <img src="../<?= htmlspecialchars($f['image_path']) ?>" alt="Current Image" class="sb-file-preview-img" id="current-image-preview">
                        <label style="position: absolute; top: 10px; right: 10px; background: rgba(239,68,68,0.9); color: white; padding: 6px 12px; border-radius: 6px; cursor: pointer; font-size: 0.85rem; display: flex; align-items: center; gap: 6px; box-shadow: 0 4px 6px rgba(0,0,0,0.3); transition: all 0.2s;" onmouseover="this.style.background='#dc2626'" onmouseout="this.style.background='rgba(239,68,68,0.9)'">
                            <input type="checkbox" name="remove_image" value="1" style="accent-color: #ef4444; width: 16px; height: 16px; margin: 0;" onchange="document.getElementById('current-image-preview').style.opacity = this.checked ? '0.3' : '1';">
                            Remove Photo
                        </label>
                    </div>
                <?php endif; ?>
                <label class="sb-file-upload" id="sb-image-dropzone">
                    <div class="upload-icon">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                    </div>
                    <div class="sb-file-upload-text" id="sb-file-upload-text">Click to upload or drag and drop</div>
                    <div class="sb-file-upload-subtext">Supported formats: JPG, PNG, SVG or GIF</div>
                    <input type="file" name="image" id="facility-image-input" accept="image/*">
                </label>
            </div>

            <script>
                document.getElementById('facility-image-input').addEventListener('change', function(e) {
                    var dropzone = document.getElementById('sb-image-dropzone');
                    var textElement = document.getElementById('sb-file-upload-text');
                    if (e.target.files && e.target.files[0]) {
                        textElement.textContent = e.target.files[0].name;
                        dropzone.classList.add('has-file');
                    } else {
                        textElement.textContent = 'Click to upload or drag and drop';
                        dropzone.classList.remove('has-file');
                    }
                });
            </script>
            <div class="sb-form-actions">
                <button type="submit" class="sb-btn sb-btn-primary"><?= render_icon('check', '', 15) ?> Save Facility</button>
                <a href="facilities.php" class="sb-btn sb-btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
<script>
document.querySelector('select[name="status"]').addEventListener('change', function() {
    var mtGroup = document.getElementById('maintenance-time-group');
    if(this.value === 'maintenance' || this.value === 'unavailable') {
        mtGroup.style.display = 'flex';
    } else {
        mtGroup.style.display = 'none';
        mtGroup.querySelectorAll('input').forEach(i => i.value = '');
    }
});
</script>
<?php endif; require_once __DIR__ . '/../includes/footer.php'; ?>

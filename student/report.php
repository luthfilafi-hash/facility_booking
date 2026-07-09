<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('student');
$user = getUser();

$facilities = $pdo->query("SELECT id, name FROM facilities ORDER BY name")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'report') {
    $facility_id = $_POST['facility_id'];
    $description = $_POST['description'];
    $image_path = null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = __DIR__ . '/../uploads/reports/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        $filename = uniqid() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = 'uploads/reports/' . $filename;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO maintenance_reports (user_id, facility_id, description, image_path) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$user['id'], $facility_id, $description, $image_path])) {
        
        // Notify all admins and staff
        $facStmt = $pdo->prepare("SELECT name FROM facilities WHERE id = ?");
        $facStmt->execute([$facility_id]);
        $facName = $facStmt->fetchColumn();
        
        $admins = $pdo->query("SELECT id FROM users WHERE role IN ('admin', 'staff')")->fetchAll(PDO::FETCH_COLUMN);
        $notifStmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message, is_read, created) VALUES (?, 'New Maintenance Report', ?, 0, NOW())");
        $msg = "A student has reported a maintenance issue regarding: " . $facName;
        foreach ($admins as $adminId) {
            $notifStmt->execute([$adminId, $msg]);
        }

        setFlash('Report submitted successfully! Staff will review it shortly.');
        header('Location: report.php');
        exit;
    } else {
        setFlash('Failed to submit report. Please try again.', 'error');
    }
}

// Fetch user's previous reports
$reportsStmt = $pdo->prepare("SELECT mr.*, f.name as facility_name FROM maintenance_reports mr JOIN facilities f ON mr.facility_id = f.id WHERE mr.user_id = ? ORDER BY mr.created DESC");
$reportsStmt->execute([$user['id']]);
$myReports = $reportsStmt->fetchAll();

$title = 'Report Maintenance Issue';
require_once __DIR__ . '/../includes/header_dash.php';
?>

<div class="sb-page-header">
    <h2><?= render_icon('alert-triangle', '', 20) ?> Report Maintenance Issue</h2>
</div>

<div class="sb-form-card sb-fade">
    <div class="sb-form-header">
        <?= render_icon('tool', '', 18) ?><h2>Submit a New Report</h2>
    </div>
    <div class="sb-form-body">
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="report">
            <div class="sb-form-group">
                <label>Facility</label>
                <select name="facility_id" class="sb-form-input" required>
                    <option value="">Select a facility</option>
                    <?php foreach ($facilities as $f): ?>
                        <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="sb-form-group">
                <label>Description of Issue</label>
                <textarea name="description" class="sb-form-input" rows="4" required placeholder="E.g., The net on court 2 is torn..."></textarea>
            </div>
            
            <style>
                .sb-file-upload {
                    display: flex; flex-direction: column; align-items: center; justify-content: center;
                    padding: 2rem; border: 2px dashed #4b5563; border-radius: 12px;
                    background-color: rgba(31, 41, 55, 0.4); color: #9ca3af; cursor: pointer;
                    transition: all 0.3s ease; text-align: center; position: relative;
                }
                .sb-file-upload:hover { border-color: #3b82f6; background-color: rgba(59, 130, 246, 0.1); color: #e5e7eb; }
                .sb-file-upload.has-file { border-color: #10b981; border-style: solid; background-color: rgba(16, 185, 129, 0.05); }
                .sb-file-upload input[type="file"] { display: none; }
                .sb-file-upload .upload-icon { margin-bottom: 10px; color: #60a5fa; }
            </style>
            
            <div class="sb-form-group">
                <label>Upload Photo (Optional)</label>
                <label class="sb-file-upload" id="sb-image-dropzone">
                    <div class="upload-icon">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                            <polyline points="17 8 12 3 7 8"></polyline>
                            <line x1="12" y1="3" x2="12" y2="15"></line>
                        </svg>
                    </div>
                    <div class="sb-file-upload-text" id="sb-file-upload-text">Click to upload image</div>
                    <input type="file" name="image" id="report-image-input" accept="image/*">
                </label>
            </div>
            <script>
                document.getElementById('report-image-input').addEventListener('change', function(e) {
                    var dropzone = document.getElementById('sb-image-dropzone');
                    var textElement = document.getElementById('sb-file-upload-text');
                    if (e.target.files && e.target.files[0]) {
                        textElement.textContent = e.target.files[0].name;
                        dropzone.classList.add('has-file');
                    } else {
                        textElement.textContent = 'Click to upload image';
                        dropzone.classList.remove('has-file');
                    }
                });
            </script>
            <div class="sb-form-actions">
                <button type="submit" class="sb-btn sb-btn-primary"><?= render_icon('send', '', 15) ?> Submit Report</button>
            </div>
        </form>
    </div>
</div>

<?php if (!empty($myReports)): ?>
<div class="sb-card sb-fade" style="margin-top: 2rem;">
    <div class="sb-card-header">
        <h3><?= render_icon('list', '', 16) ?> My Previous Reports</h3>
    </div>
    <div class="sb-table-wrap">
        <table class="sb-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Facility</th>
                    <th>Status</th>
                    <th>Admin Reply</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($myReports as $r): ?>
                <tr>
                    <td><?= date('M d, Y H:i', strtotime($r['created'])) ?></td>
                    <td><strong><?= htmlspecialchars($r['facility_name']) ?></strong></td>
                    <td>
                        <span class="sb-badge sb-badge-<?= htmlspecialchars($r['status']) ?>" style="display:inline-flex; align-items:center; gap:6px;">
                            <?php 
                                if ($r['status'] === 'resolved') echo render_icon('check', '', 14);
                                elseif ($r['status'] === 'rejected') echo render_icon('x', '', 14);
                            ?>
                            <?= htmlspecialchars(str_replace('_', ' ', $r['status'])) ?>
                        </span>
                    </td>
                    <td>
                        <?php if ($r['admin_reply']): ?>
                            <span style="color:var(--primary); font-size: 0.9rem;"><?= nl2br(htmlspecialchars($r['admin_reply'])) ?></span>
                        <?php else: ?>
                            <span style="color:var(--muted-foreground);font-style:italic;">No reply yet</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

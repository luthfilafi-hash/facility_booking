<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('staff');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $report_id = $_POST['report_id'];
    
    if ($_POST['action'] === 'manage') {
        $status = $_POST['status'];
        $admin_reply = $_POST['admin_reply'];
        
        $currStmt = $pdo->prepare("SELECT status, admin_reply FROM maintenance_reports WHERE id = ?");
        $currStmt->execute([$report_id]);
        $curr = $currStmt->fetch();
        
        $stmt = $pdo->prepare("UPDATE maintenance_reports SET status = ?, admin_reply = ? WHERE id = ?");
        $stmt->execute([$status, $admin_reply, $report_id]);
        
        if ($curr['status'] !== $status || $curr['admin_reply'] !== $admin_reply) {
            $infoStmt = $pdo->prepare("SELECT user_id, facility_id FROM maintenance_reports WHERE id = ?");
            $infoStmt->execute([$report_id]);
            $info = $infoStmt->fetch();
            
            if ($info) {
                $facStmt = $pdo->prepare("SELECT name FROM facilities WHERE id = ?");
                $facStmt->execute([$info['facility_id']]);
                $facName = $facStmt->fetchColumn();
                
                $notifMsg = "Your report for " . $facName . " was updated. Status: " . ucfirst(str_replace('_', ' ', $status)) . ".";
                if (!empty($admin_reply) && $curr['admin_reply'] !== $admin_reply) {
                    $notifMsg .= " Staff Reply: \"" . substr($admin_reply, 0, 50) . (strlen($admin_reply) > 50 ? '...' : '') . "\"";
                }
                
                $notifStmt = $pdo->prepare("INSERT INTO notifications (user_id, title, message, is_read, created) VALUES (?, 'Report Updated', ?, 0, NOW())");
                $notifStmt->execute([$info['user_id'], $notifMsg]);
            }
            
            // Log to audit
            $auditStmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, details, ip_address, created) VALUES (?, ?, ?, ?, NOW())");
            $actionLabel = $status === 'rejected' ? 'rejected_report' : 'updated_report';
            $auditStmt->execute([$_SESSION['user_id'], $actionLabel, "Updated maintenance report #$report_id to status: $status", $_SERVER['REMOTE_ADDR']]);
        }

        if ($status === 'rejected') {
            $delStmt = $pdo->prepare("DELETE FROM maintenance_reports WHERE id = ?");
            $delStmt->execute([$report_id]);
            setFlash('Report was rejected and permanently deleted.');
        } else {
            setFlash('Report updated successfully.');
        }
    } elseif ($_POST['action'] === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM maintenance_reports WHERE id = ?");
        $stmt->execute([$report_id]);
        setFlash('Report removed successfully.');
    }
    
    header("Location: maintenance_reports.php");
    exit;
}

$stmt = $pdo->query("SELECT mr.*, f.name as facility_name, u.name as student_name, u.student_id 
                     FROM maintenance_reports mr 
                     JOIN facilities f ON mr.facility_id = f.id 
                     JOIN users u ON mr.user_id = u.id 
                     WHERE mr.status != 'rejected'
                     ORDER BY mr.created DESC");
$reports = $stmt->fetchAll();

$title = 'Student Maintenance Reports';
require_once __DIR__ . '/../includes/header_dash.php';
?>

<div class="sb-page-header">
    <h2><?= render_icon('alert-triangle', '', 20) ?> Student Maintenance Reports</h2>
</div>

<div class="sb-card sb-fade">
    <div class="sb-table-wrap">
        <table class="sb-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Student</th>
                    <th>Facility</th>
                    <th>Issue Description</th>
                    <th>Image</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $modals = ''; ?>
                <?php foreach ($reports as $r): ?>
                <tr>
                    <td style="white-space: nowrap;"><?= date('M d, Y H:i', strtotime($r['created'])) ?></td>
                    <td>
                        <strong><?= htmlspecialchars($r['student_name']) ?></strong><br>
                        <small class="sb-text-muted"><?= htmlspecialchars($r['student_id']) ?></small>
                    </td>
                    <td><?= htmlspecialchars($r['facility_name']) ?></td>
                    <td style="max-width: 300px; white-space: normal;">
                        <?= nl2br(htmlspecialchars($r['description'])) ?>
                        <?php if ($r['admin_reply']): ?>
                            <div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid var(--border); color: var(--primary);">
                                <strong>Your Reply:</strong><br>
                                <?= nl2br(htmlspecialchars($r['admin_reply'])) ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($r['image_path']): ?>
                            <a href="<?= BASE_URL ?>/<?= htmlspecialchars($r['image_path']) ?>" target="_blank">
                                <img src="<?= BASE_URL ?>/<?= htmlspecialchars($r['image_path']) ?>" alt="Report Image" style="width: 80px; height: 60px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border);">
                            </a>
                        <?php else: ?>
                            <span class="sb-text-muted">No image</span>
                        <?php endif; ?>
                    </td>
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
                        <div style="display:flex; gap:5px; align-items:center;">
                            <button type="button" class="sb-btn sb-btn-sm" onclick="document.getElementById('manage-modal-<?= $r['id'] ?>').style.display='flex'">Manage</button>
                            <?php if ($r['status'] === 'resolved'): ?>
                            <form method="POST" style="margin:0;" onsubmit="return confirm('Are you sure you want to permanently remove this resolved report?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="report_id" value="<?= $r['id'] ?>">
                                <button type="submit" class="sb-btn sb-btn-sm sb-btn-danger" title="Remove Report">
                                    <?= render_icon('trash-2', '', 14) ?>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>

                <?php ob_start(); ?>
                <!-- Manage Modal -->
                <div id="manage-modal-<?= $r['id'] ?>" class="sb-modal">
                    <div class="sb-modal-content">
                        <div class="sb-modal-header">
                            <h2>Manage Report #<?= $r['id'] ?></h2>
                            <button type="button" class="sb-modal-close" onclick="document.getElementById('manage-modal-<?= $r['id'] ?>').style.display='none'">&times;</button>
                        </div>
                        <form method="POST">
                            <input type="hidden" name="action" value="manage">
                            <input type="hidden" name="report_id" value="<?= $r['id'] ?>">
                            
                            <div class="sb-form-group">
                                <label>Status</label>
                                <select name="status" class="sb-form-input">
                                    <option value="pending" <?= $r['status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                    <option value="in_progress" <?= $r['status'] == 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                                    <option value="resolved" <?= $r['status'] == 'resolved' ? 'selected' : '' ?>>Resolved</option>
                                    <option value="rejected" <?= $r['status'] == 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                </select>
                            </div>

                            <div class="sb-form-group">
                                <label>Message to Student</label>
                                <textarea name="admin_reply" class="sb-form-input" rows="4" placeholder="Optional: Type your reply here..."><?= htmlspecialchars($r['admin_reply'] ?? '') ?></textarea>
                            </div>
                            <div class="sb-form-actions">
                                <button type="button" class="sb-btn" onclick="document.getElementById('manage-modal-<?= $r['id'] ?>').style.display='none'">Cancel</button>
                                <button type="submit" class="sb-btn sb-btn-primary">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
                <?php $modals .= ob_get_clean(); ?>
                <?php endforeach; ?>
                <?php if (empty($reports)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 2rem;">No student reports found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $modals ?>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');

if (isset($_POST['action']) && $_POST['action'] === 'delete' && isset($_POST['id'])) {
    $userIdToDelete = $_POST['id'];
    
    // Fetch user details for the audit log before deleting
    $uStmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
    $uStmt->execute([$userIdToDelete]);
    $deletedUser = $uStmt->fetch();
    
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    if ($stmt->execute([$userIdToDelete])) {
        if ($deletedUser) {
            $details = "Deleted user account: " . $deletedUser['name'] . " (" . $deletedUser['email'] . ")";
            $auditStmt = $pdo->prepare("INSERT INTO audit_logs (user_id, action, details, ip_address, created) VALUES (?, ?, ?, ?, NOW())");
            $auditStmt->execute([$_SESSION['user_id'], 'Delete User', $details, $_SERVER['REMOTE_ADDR'] ?? '']);
        }
        setFlash('User deleted successfully.');
    } else {
        setFlash('Failed to delete user.', 'error');
    }
    header('Location: users.php'); exit;
}

// Handle Form Submission (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && in_array($_POST['action'], ['add', 'edit'])) {
    $id = $_POST['id'] ?? null;
    $name = $_POST['name'];
    $email = $_POST['email'];
    $student_id = $_POST['student_id'] ?? null;
    $role = $_POST['role'] ?? null;
    $password = $_POST['password'];

    if ($_POST['action'] === 'add') {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, student_id, password, role, created, modified) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([$name, $email, $student_id, $hash, $role]);
        setFlash('User created successfully.');
    } else {
        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, student_id=?, password=?, modified=NOW() WHERE id=?");
            $stmt->execute([$name, $email, $student_id, $hash, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name=?, email=?, student_id=?, modified=NOW() WHERE id=?");
            $stmt->execute([$name, $email, $student_id, $id]);
        }
        setFlash('User updated successfully.');
    }
    header('Location: users.php'); exit;
}

$action = $_GET['action'] ?? 'index';
$title = 'Users';
$is_dash = true;
require_once __DIR__ . '/../includes/header_dash.php';

if ($action === 'index'):
    $users = $pdo->query("SELECT * FROM users ORDER BY created DESC")->fetchAll();
?>
<div class="sb-page-header">
    <h2><?= render_icon('users', '', 20) ?> Users</h2>
    <a href="?action=add" class="sb-btn sb-btn-primary"><?= render_icon('plus', '', 15) ?> Add User</a>
</div>
<div class="sb-card sb-fade">
    <div class="sb-table-wrap">
    <table class="sb-table">
        <thead><tr><th>Name</th><th>Email / ID</th><th>Role</th><th>Created</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
            <td>
                <div class="sb-table-avatar">
                    <?php if(!empty($u['avatar'])): ?>
                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($u['avatar']) ?>" alt="avatar" class="sb-mini-avatar" style="padding:0; object-fit:cover; border:none;">
                    <?php else: ?>
                        <div class="sb-mini-avatar"><?= strtoupper(substr($u['name'],0,1)) ?></div>
                    <?php endif; ?>
                    <strong style="white-space: nowrap;"><?= htmlspecialchars($u['name']) ?></strong>
                </div>
            </td>
            <td>
                <div style="color:var(--foreground);"><?= htmlspecialchars($u['email']) ?></div>
                <?php if($u['student_id']): ?><div style="color:var(--muted-foreground);font-size:0.8rem;">ID: <?= htmlspecialchars($u['student_id']) ?></div><?php endif; ?>
            </td>
            <td><span class="sb-badge <?= $u['role']==='admin'?'sb-badge-scheduled':($u['role']==='staff'?'sb-badge-available':'sb-badge-pending') ?>"><?= ucfirst(htmlspecialchars($u['role'])) ?></span></td>
            <td style="color:var(--muted-foreground);font-size:0.8rem;"><?= date('d M Y', strtotime($u['created'])) ?></td>
            <td class="sb-actions">
                <a href="?action=edit&id=<?= $u['id'] ?>" class="sb-btn sb-btn-sm sb-btn-outline" title="Edit"><?= render_icon('edit', '', 14) ?></a>
                <form method="POST" style="display:inline;" onsubmit="return confirm('Delete this user?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="<?= $u['id'] ?>">
                    <button type="submit" class="sb-btn sb-btn-sm sb-btn-danger" title="Delete"><?= render_icon('trash', '', 14) ?></button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>
<?php elseif ($action === 'add' || $action === 'edit'): 
    $user = null;
    if ($action === 'edit') {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $user = $stmt->fetch();
    }
?>
<div class="sb-page-header">
    <h2><?= render_icon($action==='add'?'plus':'edit', '', 20) ?> <?= $action==='add'?'Add':'Edit' ?> User</h2>
    <a href="users.php" class="sb-btn sb-btn-ghost">Back</a>
</div>
<div class="sb-form-card sb-fade">
    <div class="sb-form-header"><?= render_icon('user', '', 18) ?><h2>User Details</h2></div>
    <div class="sb-form-body">
        <form method="POST">
            <input type="hidden" name="action" value="<?= $action ?>">
            <?php if($user): ?><input type="hidden" name="id" value="<?= $user['id'] ?>"><?php endif; ?>
            <div class="sb-form-row">
                <div class="sb-form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" class="sb-form-input" required value="<?= $user ? htmlspecialchars($user['name']) : '' ?>">
                </div>
                <div class="sb-form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="sb-form-input" required value="<?= $user ? htmlspecialchars($user['email']) : '' ?>">
                </div>
            </div>
            <div class="sb-form-row">
                <div class="sb-form-group">
                    <label>Student ID (Optional)</label>
                    <input type="text" name="student_id" class="sb-form-input" value="<?= $user ? htmlspecialchars($user['student_id'] ?? '') : '' ?>" placeholder="e.g. 2025231816">
                </div>
                <div class="sb-form-group">
                    <label>Role</label>
                    <?php if($action==='edit'): ?>
                        <input type="text" class="sb-form-input" value="<?= ucfirst(htmlspecialchars($user['role'] ?? '')) ?>" disabled style="opacity:0.7;cursor:not-allowed;background:var(--muted);">
                        <small style="color: var(--muted-foreground); display: block; margin-top: 0.3rem;">Role cannot be changed after creation.</small>
                    <?php else: ?>
                        <select name="role" class="sb-form-input" required>
                            <option value="admin">Admin</option>
                            <option value="staff">Staff</option>
                            <option value="student">Student</option>
                        </select>
                    <?php endif; ?>
                </div>
            </div>
            <div class="sb-form-row">
                <div class="sb-form-group">
                    <label><?= $action==='edit'?'New Password':'Password' ?></label>
                    <?php if($action==='edit'): ?><p style="font-size:0.75rem;color:var(--muted-foreground);margin-bottom:4px;">Leave blank to keep current</p><?php endif; ?>
                    <input type="password" name="password" class="sb-form-input" <?= $action==='add'?'required':'' ?>>
                </div>
                <div class="sb-form-group" style="visibility: hidden;"></div>
            </div>
            <div class="sb-form-actions">
                <button type="submit" class="sb-btn sb-btn-primary"><?= render_icon('check', '', 15) ?> Save User</button>
                <a href="users.php" class="sb-btn sb-btn-ghost">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

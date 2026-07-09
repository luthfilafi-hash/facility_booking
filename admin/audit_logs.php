<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('admin');

$title = 'Audit Logs';
$is_dash = true;
require_once __DIR__ . '/../includes/header_dash.php';

$logs = $pdo->query("SELECT a.*, u.name as user_name, u.avatar as user_avatar FROM audit_logs a LEFT JOIN users u ON a.user_id = u.id ORDER BY a.created DESC")->fetchAll();
?>
<div class="sb-page-header">
    <h2><?= render_icon('file-text', '', 20) ?> Audit Logs</h2>
</div>
<div class="sb-card sb-fade">
    <div class="sb-table-wrap">
    <table class="sb-table">
        <thead><tr><th>User</th><th>Action</th><th>Description</th><th>IP Address</th><th>Timestamp</th></tr></thead>
        <tbody>
        <?php foreach ($logs as $log): ?>
        <tr>
            <td>
                <div class="sb-table-avatar">
                    <?php if(!empty($log['user_avatar'])): ?>
                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($log['user_avatar']) ?>" alt="avatar" class="sb-mini-avatar" style="padding:0; object-fit:cover; border:none;">
                    <?php else: ?>
                        <div class="sb-mini-avatar"><?= strtoupper(substr($log['user_name']??'S',0,1)) ?></div>
                    <?php endif; ?>
                    <strong style="white-space: nowrap;"><?= htmlspecialchars($log['user_name']??'System') ?></strong>
                </div>
            </td>
            <td><code style="background:var(--accent);padding:2px 6px;border-radius:4px;font-size:0.75rem;"><?= htmlspecialchars($log['action']??'—') ?></code></td>
            <td style="color:var(--muted-foreground);max-width:240px;"><?= htmlspecialchars($log['details']??'') ?></td>
            <td style="font-family:monospace;font-size:0.78rem;"><?= htmlspecialchars($log['ip_address']??'—') ?></td>
            <td style="font-size:0.78rem;color:var(--muted-foreground);"><?= htmlspecialchars($log['created']) ?></td>
        </tr>
        <?php endforeach; if(empty($logs)): ?>
        <tr class="sb-table-empty"><td colspan="5">No audit logs found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
    </div>
</div>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>

<?php
require_once __DIR__ . '/includes/auth.php';
requireLogin();

$user = getUser();
$title = 'Notifications';
$is_dash = true;

// Mark all as read when opening page
$stmt = $pdo->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
$stmt->execute([$user['id']]);

require_once __DIR__ . '/includes/header_dash.php';

// Fetch notifications
$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id = ? ORDER BY created DESC LIMIT 50");
$stmt->execute([$user['id']]);
$notifications = $stmt->fetchAll();
?>

<div class="sb-page-header">
    <h2><?= render_icon('bell', '', 20) ?> Notifications</h2>
</div>

<div class="sb-card sb-fade">
    <div class="sb-card-body" style="padding: 0;">
        <?php if (empty($notifications)): ?>
            <div style="padding: 3rem; text-align: center; color: var(--muted-foreground);">
                <div style="margin-bottom: 1rem; opacity: 0.5;"><?= render_icon('bell', '', 40) ?></div>
                <p>You have no notifications yet.</p>
            </div>
        <?php else: ?>
            <ul style="list-style: none; margin: 0; padding: 0;">
                <?php foreach ($notifications as $n): ?>
                    <li style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); display: flex; gap: 1rem; align-items: flex-start; <?= !$n['is_read'] ? 'background: var(--accent);' : '' ?>">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--teal-light); color: var(--teal-dark); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                            <?= render_icon('info', '', 20) ?>
                        </div>
                        <div>
                            <h4 style="margin: 0 0 0.25rem; font-weight: 700; font-size: 0.95rem;"><?= htmlspecialchars($n['title']) ?></h4>
                            <p style="margin: 0 0 0.5rem; color: var(--muted-foreground); font-size: 0.85rem; line-height: 1.5;"><?= htmlspecialchars($n['message']) ?></p>
                            <span style="font-size: 0.75rem; color: var(--muted-foreground);"><?= date('M j, Y g:i A', strtotime($n['created'])) ?></span>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

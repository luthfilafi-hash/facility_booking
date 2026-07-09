<?php
$role = $user['role'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$navAdmin = [
    ['icon'=>'home',      'label'=>'Home',        'url'=>BASE_URL.'/index.php'],
    ['icon'=>'grid',      'label'=>'Dashboard',   'url'=>BASE_URL.'/admin/index.php'],
    ['icon'=>'calendar',  'label'=>'Calendar',    'url'=>BASE_URL.'/calendar.php'],
    ['icon'=>'users',     'label'=>'Users',       'url'=>BASE_URL.'/admin/users.php'],
    ['icon'=>'building',  'label'=>'Facilities',  'url'=>BASE_URL.'/admin/facilities.php'],
    ['icon'=>'file-text', 'label'=>'Bookings',    'url'=>BASE_URL.'/admin/bookings.php'],
    ['icon'=>'clock',     'label'=>'Availability',   'url'=>BASE_URL.'/admin/availability.php'],
    ['icon'=>'tool',      'label'=>'Maintenance', 'url'=>BASE_URL.'/admin/maintenance.php'],
    ['icon'=>'alert-triangle', 'label'=>'Student Reports', 'url'=>BASE_URL.'/admin/maintenance_reports.php'],
    ['icon'=>'box',       'label'=>'Equipment',   'url'=>BASE_URL.'/admin/equipments.php'],
    ['icon'=>'file-text', 'label'=>'Audit Logs',  'url'=>BASE_URL.'/admin/audit_logs.php'],
];
$navStaff = [
    ['icon'=>'home',      'label'=>'Home',        'url'=>BASE_URL.'/index.php'],
    ['icon'=>'grid',      'label'=>'Dashboard',   'url'=>BASE_URL.'/staff/index.php'],
    ['icon'=>'calendar',  'label'=>'Calendar',    'url'=>BASE_URL.'/calendar.php'],
    ['icon'=>'file-text', 'label'=>'Bookings',    'url'=>BASE_URL.'/staff/bookings.php'],
    ['icon'=>'tool',      'label'=>'Maintenance', 'url'=>BASE_URL.'/staff/maintenance.php'],
    ['icon'=>'alert-triangle', 'label'=>'Student Reports', 'url'=>BASE_URL.'/staff/maintenance_reports.php'],
];
$navStudent = [
    ['icon'=>'home',      'label'=>'Home',        'url'=>BASE_URL.'/index.php'],
    ['icon'=>'grid',      'label'=>'Dashboard',   'url'=>BASE_URL.'/student/index.php'],
    ['icon'=>'calendar',  'label'=>'Calendar',    'url'=>BASE_URL.'/calendar.php'],
    ['icon'=>'plus',      'label'=>'Book Facility','url'=>BASE_URL.'/student/book.php'],
    ['icon'=>'building',  'label'=>'Facilities',  'url'=>BASE_URL.'/student/facilities.php'],
    ['icon'=>'alert-triangle', 'label'=>'Report Issue', 'url'=>BASE_URL.'/student/report.php'],
    ['icon'=>'phone',     'label'=>'Contact Us',  'url'=>BASE_URL.'/student/contact.php'],
];
$navItems = $role === 'admin' ? $navAdmin : ($role === 'staff' ? $navStaff : $navStudent);
$roleBadge = ['admin'=>'Admin Panel','staff'=>'Staff Panel','student'=>'Student Panel'][$role] ?? 'Panel';
?>
<aside class="sb-sidebar">
    <a href="<?= BASE_URL ?>/index.php" style="text-decoration: none;">
        <div class="sb-sb-brand">
            <div class="ico"><?= render_icon('volleyball', '', 18) ?></div>
            UniReserve
        </div>
    </a>
    <nav class="sb-sb-nav">
        <div class="sb-sb-section"><?= $roleBadge ?></div>
        <?php foreach ($navItems as $item):
            // Check if current path matches URL path
            $itemPath = parse_url($item['url'], PHP_URL_PATH);
            $isActive = ($path === $itemPath) ? ' active' : '';
        ?>
        <a href="<?= $item['url'] ?>" class="sb-sb-link<?= $isActive ?>">
            <?= render_icon($item['icon'], '', 17) ?>
            <?= htmlspecialchars($item['label']) ?>
        </a>
        <?php endforeach; ?>
        <div class="sb-sb-divider"></div>
        <div class="sb-sb-section">Account</div>
        <a href="<?= BASE_URL ?>/profile.php" class="sb-sb-link">
            <?= render_icon('user', '', 17) ?> My Profile
        </a>
    </nav>
    <div class="sb-sb-footer">
        <div class="sb-sb-user">
            <div class="sb-avatar" style="overflow: hidden; padding: 0;">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="<?= BASE_URL ?>/<?= htmlspecialchars($user['avatar']) ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="avatar">
                <?php else: ?>
                    <?= strtoupper(substr($user['name'], 0, 1)) ?>
                <?php endif; ?>
            </div>
            <div>
                <div class="sb-sb-user-name"><?= htmlspecialchars($user['name']) ?></div>
                <div class="sb-sb-user-role"><?= htmlspecialchars($role) ?></div>
            </div>
        </div>
        <a href="<?= BASE_URL ?>/logout.php" class="sb-logout-link">
            <?= render_icon('log-out', '', 14) ?> Sign Out
        </a>
    </div>
</aside>

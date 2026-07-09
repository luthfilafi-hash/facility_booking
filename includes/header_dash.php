<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/icons.php';
requireLogin();
$user = getUser();
$init = strtoupper(substr($user['name'], 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'Dashboard') ?> — UniReserve</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/sportbook.css?v=<?= time() ?>">
    <!-- Premium Date/Time Picker CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="https://npmcdn.com/flatpickr/dist/themes/dark.css">
    <!-- Premium Select Dropdown CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
    <style>
        /* Custom Overrides for Flatpickr to match our Glassmorphism Theme */
        .flatpickr-calendar.dark {
            background: rgba(15, 23, 42, 0.95);
            border: 1px solid var(--border);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange, .flatpickr-day.selected.inRange, .flatpickr-day.startRange.inRange, .flatpickr-day.endRange.inRange, .flatpickr-day.selected:focus, .flatpickr-day.startRange:focus, .flatpickr-day.endRange:focus, .flatpickr-day.selected:hover, .flatpickr-day.startRange:hover, .flatpickr-day.endRange:hover, .flatpickr-day.selected.prevMonthDay, .flatpickr-day.startRange.prevMonthDay, .flatpickr-day.endRange.prevMonthDay, .flatpickr-day.selected.nextMonthDay, .flatpickr-day.startRange.nextMonthDay, .flatpickr-day.endRange.nextMonthDay {
            background: var(--primary);
            border-color: var(--primary);
            color: var(--primary-foreground);
        }

        /* Custom Overrides for Choices.js to match our Glassmorphism Theme */
        .choices {
            margin-bottom: 0;
            font-family: var(--font-sans);
        }
        .choices__inner {
            background-color: var(--input) !important;
            border: 1px solid var(--border) !important;
            border-radius: var(--radius) !important;
            color: var(--foreground) !important;
            min-height: 48px;
            display: flex;
            align-items: center;
            padding-left: 1rem;
        }
        .choices.is-open .choices__inner {
            border-radius: var(--radius) !important;
            border-color: var(--ring) !important;
        }
        .choices__list--dropdown {
            background: rgba(15, 17, 26, 0.95) !important;
            border: 1px solid var(--border) !important;
            border-radius: var(--radius) !important;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.5) !important;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            color: var(--foreground) !important;
            margin-top: 4px;
        }
        .choices__list--dropdown .choices__item {
            font-size: 0.95rem;
            padding: 10px 16px;
            color: var(--foreground) !important;
        }
        .choices__list--dropdown .choices__item--selectable.is-highlighted {
            background-color: var(--primary) !important;
            color: var(--primary-foreground) !important;
        }
        .choices[data-type*="select-one"] .choices__input {
            background-color: transparent !important;
            color: var(--foreground) !important;
            border-bottom: 1px solid var(--border) !important;
        }
        .choices__list--single {
            padding: 0 !important;
        }
    </style>
</head>
<body>
<div class="sb-overlay" id="sidebarOverlay"></div>
<div class="sb-dash">
    <?php require __DIR__ . '/sidebar.php'; ?>
    <div class="sb-dash-body">
        <header class="sb-dash-header">
            <div class="sb-dash-header-left">
                <button class="sb-mobile-menu-btn" id="dashMobileMenuBtn" aria-label="Toggle sidebar" style="margin-right: 1rem;">
                    <?= render_icon('menu', '', 24) ?>
                </button>
                <h1><?= htmlspecialchars($title ?? 'Dashboard') ?></h1>
            </div>
<?php
$stmt = $pdo->prepare("SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0");
$stmt->execute([$user['id']]);
$unreadNotifs = $stmt->fetchColumn();
?>
            <div class="sb-dash-header-right" style="display:flex; align-items:center; gap: 1.5rem;">
                <a href="<?= BASE_URL ?>/notifications.php" style="position:relative; color:var(--muted-foreground);">
                    <?= render_icon('bell', '', 20) ?>
                    <?php if($unreadNotifs > 0): ?>
                    <span style="position:absolute; top:-5px; right:-5px; background:var(--destructive); color:white; font-size:0.6rem; font-weight:bold; padding:0.1rem 0.35rem; border-radius:10px;"><?= $unreadNotifs ?></span>
                    <?php endif; ?>
                </a>
                <div class="sb-avatar" style="cursor:default; overflow: hidden; padding: 0;" title="<?= htmlspecialchars($user['name']) ?>">
                    <?php if (!empty($user['avatar'])): ?>
                        <img src="<?= BASE_URL ?>/<?= htmlspecialchars($user['avatar']) ?>" style="width: 100%; height: 100%; object-fit: cover;" alt="avatar">
                    <?php else: ?>
                        <?= $init ?>
                    <?php endif; ?>
                </div>
            </div>
        </header>
        <main class="sb-dash-content">
            <?= getFlash() ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dashMobileBtn = document.getElementById('dashMobileMenuBtn');
    const sidebar = document.querySelector('.sb-sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (dashMobileBtn && sidebar && overlay) {
        function toggleSidebar() {
            sidebar.classList.toggle('show');
            overlay.classList.toggle('show');
        }
        
        dashMobileBtn.addEventListener('click', toggleSidebar);
        overlay.addEventListener('click', toggleSidebar);
    }
});
</script>

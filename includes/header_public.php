<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/icons.php';
$user = getUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'UniReserve') ?> — UniReserve</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/sportbook.css?v=<?= time() ?>">
</head>
<body>
<?php if (!isset($hide_nav) || !$hide_nav): ?>
<header class="sb-topnav">
    <a href="<?= BASE_URL ?>/index.php" class="sb-topnav-brand">
        <span class="brand-icon"><?= render_icon('volleyball', '', 20) ?></span>
        UniReserve
    </a>
    <button class="sb-mobile-menu-btn" id="publicMobileMenuBtn" aria-label="Toggle menu">
        <?= render_icon('menu', '', 24) ?>
    </button>
    <nav class="sb-topnav-links" id="publicTopnavLinks">
        <a href="<?= BASE_URL ?>/index.php">Home</a>
        <a href="<?= BASE_URL ?>/facilities.php">Facilities</a>
        <a href="<?= BASE_URL ?>/index.php#how-it-works">How It Works</a>
        <?php if ($user): ?>
            <?php $dashUrl = match($user['role']) { 'admin'=>BASE_URL.'/admin/index.php', 'staff'=>BASE_URL.'/staff/index.php', default=>BASE_URL.'/student/index.php' }; ?>
            <a href="<?= $dashUrl ?>" class="sb-btn sb-btn-primary">Dashboard</a>
            <a href="<?= BASE_URL ?>/logout.php" class="sb-btn sb-btn-outline" style="margin-left: 0.5rem; border-radius: 999px;"><?= render_icon('log-out', '', 16) ?> Sign Out</a>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/login.php" class="sb-btn sb-btn-primary" style="margin-left: 0.5rem; border-radius: 999px;">Sign In to Book</a>
        <?php endif; ?>
    </nav>
</header>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileBtn = document.getElementById('publicMobileMenuBtn');
    const topnavLinks = document.getElementById('publicTopnavLinks');
    if (mobileBtn && topnavLinks) {
        mobileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            topnavLinks.classList.toggle('show');
        });
        document.addEventListener('click', function(e) {
            if (!topnavLinks.contains(e.target) && e.target !== mobileBtn) {
                topnavLinks.classList.remove('show');
            }
        });
    }
});
</script>
<?php endif; ?>

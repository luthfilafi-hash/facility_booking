<?php
// config.php
session_start();

$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$base_dir = str_replace($_SERVER['DOCUMENT_ROOT'], '', str_replace('\\', '/', __DIR__));
define('BASE_URL', $protocol . "://" . $host . $base_dir);

$db_host = 'localhost';
$db_name = 'facility_booking';
$db_user = 'root';
$db_pass = ''; // Default laragon password

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database Connection failed: " . $e->getMessage());
}

// Helper for Flash messages
function setFlash($message, $type = 'success') {
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return '<div class="message ' . $flash['type'] . '">' . render_icon($flash['type'] === 'success' ? 'check' : ($flash['type'] === 'error' ? 'x' : 'zap'), '', 16) . ' ' . htmlspecialchars($flash['message']) . '</div>';
    }
    return '';
}

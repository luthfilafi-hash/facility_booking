<?php
// includes/auth.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/auto_maintenance.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function getUser() {
    if (!isLoggedIn()) return null;
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'],
        'email' => $_SESSION['user_email'],
        'role' => $_SESSION['user_role'],
        'avatar' => $_SESSION['user_avatar'] ?? null
    ];
}

function requireLogin() {
    if (!isLoggedIn()) {
        setFlash('Please log in to access this page.', 'warning');
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

function requireRole($allowedRoles) {
    requireLogin();
    $user = getUser();
    if (!in_array($user['role'], (array)$allowedRoles)) {
        setFlash('You do not have permission to access this page.', 'error');
        // Redirect to their respective dashboard
        $dash = BASE_URL . '/student/index.php';
        if ($user['role'] === 'admin') $dash = BASE_URL . '/admin/index.php';
        if ($user['role'] === 'staff') $dash = BASE_URL . '/staff/index.php';
        header('Location: ' . $dash);
        exit;
    }
}

function loginUser($user) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_avatar'] = $user['avatar'] ?? null;
}

function logoutUser() {
    session_destroy();
}

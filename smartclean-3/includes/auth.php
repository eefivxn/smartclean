<?php
function requireLogin() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: /smartclean/login.php');
        exit;
    }
}

function requireAdmin() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        header('Location: /smartclean/login.php');
        exit;
    }
}

function isLoggedIn() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

// Generate CSRF token (buat baru jika belum ada)
function getCsrfToken() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Output hidden input CSRF untuk form
function csrfInput() {
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(getCsrfToken()) . '">';
}

// Validasi CSRF token dari POST
function validateCsrf() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    return isset($_POST['csrf_token'])
        && isset($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

function statusBadge($s) {
    $map = [
        'menunggu'   => ['badge-warning', '⏳'],
        'diproses'   => ['badge-info',    '⚙️'],
        'selesai'    => ['badge-success', '✅'],
        'dibatalkan' => ['badge-danger',  '❌'],
    ];
    [$cls, $icon] = $map[$s] ?? ['badge-info', '?'];
    return "<span class='badge $cls'>$icon " . ucfirst($s) . "</span>";
}
?>

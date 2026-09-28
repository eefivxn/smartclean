<?php
if (session_status() === PHP_SESSION_NONE) session_start();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $page_title ?? 'Smart Clean' ?> — Shinning Clean</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Sora:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/smartclean/assets/css/style.css">
</head>
<body>
<nav class="navbar">
    <div class="navbar-brand">
        <div class="logo-icon">👟</div>
        <div>Smart <span>Clean</span></div>
    </div>
    <div class="nav-links">
        <a href="/smartclean/user/dashboard.php"
           class="nav-link <?= $current_page === 'dashboard.php' ? 'active' : '' ?>">
           🏠 <span>Beranda</span>
        </a>
        <a href="/smartclean/user/services.php"
           class="nav-link <?= $current_page === 'services.php' ? 'active' : '' ?>">
           🧹 <span>Layanan</span>
        </a>
        <a href="/smartclean/user/order.php"
           class="nav-link <?= $current_page === 'order.php' ? 'active' : '' ?>">
           📦 <span>Pesan</span>
        </a>
        <a href="/smartclean/user/history.php"
           class="nav-link <?= $current_page === 'history.php' ? 'active' : '' ?>">
           📋 <span>Riwayat</span>
        </a>
        <a href="/smartclean/proses/logout.php" class="nav-link btn-logout">
           🚪 <span>Keluar</span>
        </a>
    </div>
</nav>

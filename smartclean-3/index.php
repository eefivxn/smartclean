<?php
session_start();
if (isset($_SESSION['user_id'])) {
    $redirect = $_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php';
    header("Location: $redirect"); exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Smart Clean — Shinning Clean</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Sora:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/smartclean/assets/css/style.css">
</head>
<body>

<nav class="navbar">
    <div class="navbar-brand">
        <div class="logo-icon">👟</div>
        <div>Smart <span>Clean</span></div>
    </div>
    <div class="nav-links">
        <a href="login.php" class="nav-link">Masuk</a>
        <a href="register.php" class="btn btn-primary">Daftar Gratis</a>
    </div>
</nav>

<section class="hero">
    <div class="hero-inner">
        <div class="hero-badge">✨ LAYANAN PROFESIONAL</div>
        <h1>Sepatu Bersih,<br><span class="accent">Hati Senang</span></h1>
        <p>Layanan cuci sepatu profesional dari <strong>Shinning Clean</strong>. Percayakan perawatan sepatu kesayangan Anda kepada kami.</p>
        <div class="hero-btns">
            <a href="register.php" class="btn btn-white btn-lg">🚀 Mulai Sekarang</a>
            <a href="login.php" class="btn btn-ghost btn-lg">Sudah Punya Akun</a>
        </div>
    </div>
</section>

<section class="features">
    <div class="features-label">✦ KEUNGGULAN KAMI</div>
    <h2>Mengapa Memilih Shinning Clean?</h2>
    <p class="features-sub">Kepercayaan Anda adalah prioritas utama kami</p>
    <div class="features-grid">
        <div class="feature-item">
            <div class="fi-icon">🧴</div>
            <h3>Produk Premium</h3>
            <p>Menggunakan bahan pembersih khusus sepatu yang aman dan teruji</p>
        </div>
        <div class="feature-item">
            <div class="fi-icon">⚡</div>
            <h3>Proses Cepat</h3>
            <p>Selesai dalam 2–7 hari sesuai jenis layanan yang dipilih</p>
        </div>
        <div class="feature-item">
            <div class="fi-icon">📦</div>
            <h3>Tracking Pesanan</h3>
            <p>Pantau status pesanan Anda secara real-time kapan saja</p>
        </div>
        <div class="feature-item">
            <div class="fi-icon">💯</div>
            <h3>Garansi Kepuasan</h3>
            <p>Tidak puas? Kami akan bersihkan ulang secara gratis</p>
        </div>
    </div>
</section>

<script src="assets/js/main.js"></script>
<footer class="site-footer">
    <p>© <?= date('Y') ?> <strong>Shinning Clean</strong> — Smart Clean System. All rights reserved.</p>
</footer>
</body>
</html>

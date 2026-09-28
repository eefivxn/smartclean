<?php
$page_title = 'Layanan';
require_once '../includes/auth.php';
requireLogin();
require_once '../config/database.php';
include '../includes/header.php';

$result = $conn->query("SELECT * FROM services WHERE is_active = 1 ORDER BY price ASC");
$icons  = ['👟','🧽','✨','🎨','⚪','🧸'];
$i      = 0;
?>

<div class="wrapper">
    <div class="page-header">
        <h1>🧹 Daftar Layanan</h1>
        <p>Pilih layanan yang sesuai untuk sepatu Anda</p>
    </div>

    <div class="service-grid">
    <?php while ($s = $result->fetch_assoc()): ?>
        <div class="service-card">
            <div class="service-icon"><?= $icons[$i++ % count($icons)] ?></div>
            <h3><?= htmlspecialchars($s['name']) ?></h3>
            <p><?= htmlspecialchars($s['description']) ?></p>
            <div class="service-price">
                Rp <?= number_format($s['price'], 0, ',', '.') ?>
                <span style="font-size:.8rem;color:#6b7280;font-weight:400"> / pasang</span>
            </div>
            <div class="service-duration">🕐 Estimasi <?= $s['duration_days'] ?> hari</div>
            <a href="order.php?service_id=<?= $s['id'] ?>" class="btn btn-primary btn-block">Pesan Sekarang</a>
        </div>
    <?php endwhile; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

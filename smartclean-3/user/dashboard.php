<?php
$page_title = 'Dashboard';
require_once '../includes/auth.php';
requireLogin();
require_once '../config/database.php';
include '../includes/header.php';

$uid = $_SESSION['user_id'];

$stats = [];
foreach (['menunggu','diproses','selesai'] as $s) {
    $r = $conn->prepare("SELECT COUNT(*) AS c FROM orders WHERE user_id=? AND status=?");
    $r->bind_param("is", $uid, $s);
    $r->execute();
    $stats[$s] = $r->get_result()->fetch_assoc()['c'];
    $r->close();
}

$recent = $conn->prepare("
    SELECT o.*, s.name AS service_name
    FROM orders o
    JOIN services s ON o.service_id = s.id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC LIMIT 5
");
$recent->bind_param("i", $uid);
$recent->execute();
$orders = $recent->get_result();
$recent->close();

$error = $success = '';
if (isset($_SESSION['order_error']))   { $error   = $_SESSION['order_error'];   unset($_SESSION['order_error']); }
if (isset($_SESSION['order_success'])) { $success = $_SESSION['order_success']; unset($_SESSION['order_success']); }
?>

<div class="wrapper">
    <div class="page-header">
        <h1>👋 Halo, <?= htmlspecialchars($_SESSION['user_name']) ?>!</h1>
        <p>Selamat datang di Smart Clean — Shinning Clean</p>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
    <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon yellow">⏳</div>
            <div class="stat-info">
                <h3><?= $stats['menunggu'] ?></h3>
                <p>Menunggu</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon blue">⚙️</div>
            <div class="stat-info">
                <h3><?= $stats['diproses'] ?></h3>
                <p>Diproses</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon green">✅</div>
            <div class="stat-info">
                <h3><?= $stats['selesai'] ?></h3>
                <p>Selesai</p>
            </div>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            📋 Pesanan Terbaru
            <a href="history.php" class="btn btn-outline btn-sm" style="margin-left:auto">Lihat Semua</a>
        </div>
        <div class="table-wrapper">
            <?php if ($orders->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Layanan</th>
                        <th>Sepatu</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php $no = 1; while ($o = $orders->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= htmlspecialchars($o['service_name']) ?></td>
                        <td><?= $o['quantity'] ?> pasang</td>
                        <td><?= date('d M Y', strtotime($o['order_date'])) ?></td>
                        <td><strong>Rp <?= number_format($o['total_price'], 0, ',', '.') ?></strong></td>
                        <td><?= statusBadge($o['status']) ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <div class="es-icon">📦</div>
                <p>Belum ada pesanan. <a href="order.php">Buat pesanan sekarang!</a></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="d-flex gap-2">
        <a href="order.php" class="btn btn-primary">➕ Buat Pesanan Baru</a>
        <a href="services.php" class="btn btn-outline">🧹 Lihat Layanan</a>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

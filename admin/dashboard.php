<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/database.php';

$total_orders   = $conn->query("SELECT COUNT(*) AS c FROM orders")->fetch_assoc()['c'];
$total_users    = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role='user'")->fetch_assoc()['c'];
$total_services = $conn->query("SELECT COUNT(*) AS c FROM services WHERE is_active=1")->fetch_assoc()['c'];
$total_revenue  = $conn->query("SELECT COALESCE(SUM(total_price),0) AS c FROM orders WHERE status='selesai'")->fetch_assoc()['c'];

$stats_status = [];
foreach (['menunggu','diproses','selesai','dibatalkan'] as $s) {
    $r = $conn->prepare("SELECT COUNT(*) AS c FROM orders WHERE status = ?");
    $r->bind_param("s", $s);
    $r->execute();
    $stats_status[$s] = $r->get_result()->fetch_assoc()['c'];
    $r->close();
}

$recent = $conn->query("
    SELECT o.*, u.name AS user_name, s.name AS service_name
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN services s ON o.service_id = s.id
    ORDER BY o.created_at DESC LIMIT 8
");

$success = '';
if (isset($_SESSION['ord_success'])) {
    $success = $_SESSION['ord_success'];
    unset($_SESSION['ord_success']);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard — Smart Clean</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Sora:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/smartclean/assets/css/style.css">
</head>

<body>
<div class="admin-layout">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">👟</div>
            <div>
                <h2>Smart Clean</h2>
                <p>Shinning Clean</p>
            </div>
        </div>
        <p class="sidebar-section-label">Menu</p>
        <ul class="sidebar-menu">
            <li><a href="dashboard.php" class="active">🏠 Dashboard</a></li>
            <li><a href="services.php">🧹 Kelola Layanan</a></li>
            <li><a href="orders.php">📦 Kelola Pesanan</a></li>
            <li><a href="/smartclean/proses/logout.php">🚪 Keluar</a></li>
        </ul>
        <div class="sidebar-user">
            <div class="sidebar-avatar">👤</div>
            <div>
                <strong><?= htmlspecialchars($_SESSION['user_name']) ?></strong>
                Administrator
            </div>
        </div>
    </aside>

    <!-- Content -->
    <main class="admin-content">
        <div class="page-header">
            <h1>📊 Dashboard Admin</h1>
            <p>Ringkasan data Smart Clean — <?= date('d F Y') ?></p>
        </div>

        <?php if ($success) : ?>
        <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- Stat Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">📦</div>
                <div class="stat-info">
                    <h3><?= $total_orders ?></h3>
                    <p>Total Pesanan</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">💰</div>
                <div class="stat-info">
                    <h3>Rp <?= number_format($total_revenue, 0, ',', '.') ?></h3>
                    <p>Total Pendapatan</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon yellow">👥</div>
                <div class="stat-info">
                    <h3><?= $total_users ?></h3>
                    <p>Total Pelanggan</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue">🧹</div>
                <div class="stat-info">
                    <h3><?= $total_services ?></h3>
                    <p>Layanan Aktif</p>
                </div>
            </div>
        </div>

        <!-- Status Summary -->
        <div class="stats-grid mb-3">
            <div class="stat-card">
                <div class="stat-icon yellow">⏳</div>
                <div class="stat-info">
                    <h3><?= $stats_status['menunggu'] ?></h3>
                    <p>Menunggu</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon blue">⚙️</div>
                <div class="stat-info">
                    <h3><?= $stats_status['diproses'] ?></h3>
                    <p>Diproses</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green">✅</div>
                <div class="stat-info">
                    <h3><?= $stats_status['selesai'] ?></h3>
                    <p>Selesai</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red">❌</div>
                <div class="stat-info">
                    <h3><?= $stats_status['dibatalkan'] ?></h3>
                    <p>Dibatalkan</p>
                </div>
            </div>
        </div>

        <!-- Pesanan Terbaru -->
        <div class="card">
            <div class="card-header">
                📋 Pesanan Terbaru
                <a href="orders.php" class="btn btn-outline btn-sm" style="margin-left:auto">Lihat Semua</a>
            </div>
            <div class="table-wrapper">
                <?php if ($recent->num_rows > 0) : ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Pelanggan</th>
                            <th>Layanan</th>
                            <th>Sepatu</th>
                            <th>Total</th>
                            <th>Tgl Order</th>
                            <th>Catatan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $n = 1;
                    $map = [
                        'menunggu'   => ['badge-warning','⏳'],
                        'diproses'   => ['badge-info','⚙️'],
                        'selesai'    => ['badge-success','✅'],
                        'dibatalkan' => ['badge-danger','❌'],
                    ];
                    while ($o = $recent->fetch_assoc()) :
                        [$cls, $icon] = $map[$o['status']] ?? ['badge-info','?'];
                        ?>
                        <tr>
                            <td><?= $n++ ?></td>
                            <td><strong><?= htmlspecialchars($o['user_name']) ?></strong></td>
                            <td><?= htmlspecialchars($o['service_name']) ?></td>
                            <td><?= $o['quantity'] ?> pasang</td>
                            <td>Rp <?= number_format($o['total_price'], 0, ',', '.') ?></td>
                            <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
                            <td>
                                <?php if (isset($o['notes']) && trim($o['notes']) !== '') : ?>
                                    <button 
                                        class="btn btn-sm btn-outline"
                                        data-notes="<?= htmlspecialchars($o['notes'], ENT_QUOTES) ?>"
                                        style="font-size:11px;padding:2px 6px"
                                    >
                                        📝 Lihat
                                    </button>
                                <?php else : ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><span class="badge <?= $cls ?>"><?= $icon ?> <?= ucfirst($o['status']) ?></span></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else : ?>
                <div class="empty-state">
                    <div class="es-icon">📭</div>
                    <p>Belum ada pesanan masuk.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
<div id="notesModal" class="notes-modal">
    <div class="notes-box">
        <h3>📝 Detail Catatan</h3>
        <div id="notesContent"></div>
        <button id="closeNotes">Tutup</button>
    </div>
</div>

<script src="/smartclean/assets/js/main.js"></script>
</body>
</html>

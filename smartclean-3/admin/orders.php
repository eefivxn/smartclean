<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/database.php';

$success = $error = '';
if (isset($_SESSION['ord_success'])) { $success = $_SESSION['ord_success']; unset($_SESSION['ord_success']); }
if (isset($_SESSION['ord_error']))   { $error   = $_SESSION['ord_error'];   unset($_SESSION['ord_error']); }

$filter = $_GET['status'] ?? '';
$allowed_filters = ['menunggu','diproses','selesai','dibatalkan'];

if ($filter && in_array($filter, $allowed_filters)) {
    $stmt = $conn->prepare("
        SELECT o.*, u.name AS user_name, u.phone AS user_phone, s.name AS service_name
        FROM orders o
        JOIN users u ON o.user_id = u.id
        JOIN services s ON o.service_id = s.id
        WHERE o.status = ?
        ORDER BY o.created_at DESC
    ");
    $stmt->bind_param("s", $filter);
    $stmt->execute();
    $orders = $stmt->get_result();
    $stmt->close();
} else {
    $orders = $conn->query("
        SELECT o.*, u.name AS user_name, u.phone AS user_phone, s.name AS service_name
        FROM orders o
        JOIN users u ON o.user_id = u.id
        JOIN services s ON o.service_id = s.id
        ORDER BY o.created_at DESC
    ");
}

$map = [
    'menunggu'   => ['badge-warning','⏳'],
    'diproses'   => ['badge-info','⚙️'],
    'selesai'    => ['badge-success','✅'],
    'dibatalkan' => ['badge-danger','❌'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Pesanan — Smart Clean</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Sora:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/smartclean/assets/css/style.css">
</head>
<body>
<div class="admin-layout">

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
            <li><a href="dashboard.php">🏠 Dashboard</a></li>
            <li><a href="services.php">🧹 Kelola Layanan</a></li>
            <li><a href="orders.php" class="active">📦 Kelola Pesanan</a></li>
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

    <main class="admin-content">
        <div class="page-header">
            <h1>📦 Kelola Pesanan</h1>
            <p>Pantau dan perbarui status semua pesanan masuk</p>
        </div>

        <?php if ($success): ?>
        <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <!-- FILTER -->
        <div class="d-flex gap-1 mb-3" style="flex-wrap:wrap">
            <a href="orders.php" class="btn btn-sm <?= !$filter ? 'btn-primary' : 'btn-outline' ?>">📋 Semua</a>
            <a href="orders.php?status=menunggu" class="btn btn-sm <?= $filter==='menunggu' ? 'btn-primary' : 'btn-outline' ?>">⏳ Menunggu</a>
            <a href="orders.php?status=diproses" class="btn btn-sm <?= $filter==='diproses' ? 'btn-primary' : 'btn-outline' ?>">⚙️ Diproses</a>
            <a href="orders.php?status=selesai" class="btn btn-sm <?= $filter==='selesai' ? 'btn-primary' : 'btn-outline' ?>">✅ Selesai</a>
            <a href="orders.php?status=dibatalkan" class="btn btn-sm <?= $filter==='dibatalkan' ? 'btn-primary' : 'btn-outline' ?>">❌ Dibatalkan</a>
        </div>

        <div class="card">
            <div class="card-header">
                📋 Daftar Pesanan
                <span style="margin-left:auto;font-size:.85rem;color:#6b7280">
                    <?= $orders->num_rows ?> pesanan ditemukan
                </span>
            </div>

            <div class="table-wrapper">
                <?php if ($orders->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Pelanggan</th>
                            <th>Layanan</th>
                            <th>Sepatu</th>
                            <th>Tgl Antar</th>
                            <th>Tgl Ambil</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Ubah Status</th>
                        </tr>
                    </thead>
                    <tbody>

                    <?php $n = 1; while ($o = $orders->fetch_assoc()):
                        [$cls, $icon] = $map[$o['status']] ?? ['badge-info','?'];
                    ?>

                    <tr>
                        <td><?= $n++ ?></td>

                        <td>
                            <strong><?= htmlspecialchars($o['user_name']) ?></strong>

                            <?php if ($o['user_phone']): ?>
                                <br>
                                <small style="color:#6b7280">
                                    <?= htmlspecialchars($o['user_phone']) ?>
                                </small>
                            <?php endif; ?>

                            <!-- ✅ FIX UTAMA DI SINI -->
                            <?php if (!empty($o['notes'])): ?>
                                <br>
                                <small 
                                    style="color:#93c5fd"
                                    title="<?= htmlspecialchars($o['notes']) ?>"
                                >
                                    📝 <?= htmlspecialchars(substr($o['notes'], 0, 50)) ?>...
                                </small>
                            <?php endif; ?>
                        </td>

                        <td><?= htmlspecialchars($o['service_name']) ?></td>

                        <td><?= $o['quantity'] ?> pasang</td>

                        <td><?= date('d M Y', strtotime($o['order_date'])) ?></td>

                        <td>
                            <?= $o['pickup_date'] 
                                ? date('d M Y', strtotime($o['pickup_date'])) 
                                : '-' ?>
                        </td>

                        <td>
                            <strong>Rp <?= number_format($o['total_price'], 0, ',', '.') ?></strong>
                        </td>

                        <td>
                            <span class="badge <?= $cls ?>">
                                <?= $icon ?> <?= ucfirst($o['status']) ?>
                            </span>
                        </td>

                        <td>
                            <form method="POST" action="/smartclean/proses/status_proses.php">
                                <input type="hidden" name="order_id" value="<?= $o['id'] ?>">

                                <div style="display:flex;gap:4px;align-items:center">
                                    <select name="status" class="form-control"
                                        style="padding:.3rem .5rem;font-size:.82rem;min-width:120px">

                                        <option value="menunggu"   <?= $o['status']==='menunggu' ? 'selected':'' ?>>⏳ Menunggu</option>
                                        <option value="diproses"   <?= $o['status']==='diproses' ? 'selected':'' ?>>⚙️ Diproses</option>
                                        <option value="selesai"    <?= $o['status']==='selesai' ? 'selected':'' ?>>✅ Selesai</option>
                                        <option value="dibatalkan" <?= $o['status']==='dibatalkan' ? 'selected':'' ?>>❌ Dibatalkan</option>
                                    </select>

                                    <button type="submit" class="btn btn-primary btn-sm">💾</button>
                                </div>
                            </form>
                        </td>

                    </tr>

                    <?php endwhile; ?>

                    </tbody>
                </table>

                <?php else: ?>
                <div class="empty-state">
                    <div class="es-icon">📭</div>
                    <p>
                        Tidak ada pesanan<?= $filter ? ' dengan status <strong>' . htmlspecialchars($filter) . '</strong>' : '' ?>.
                    </p>

                    <?php if ($filter): ?>
                    <a href="orders.php" class="btn btn-outline mt-2">Lihat Semua Pesanan</a>
                    <?php endif; ?>
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
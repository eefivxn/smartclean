<?php
$page_title = 'Riwayat Pesanan';
require_once '../includes/auth.php';
requireLogin();
require_once '../config/database.php';
include '../includes/header.php';

$uid = $_SESSION['user_id'];

// Flash messages (dari redirect order_proses.php)
$success = $error = '';
if (isset($_SESSION['order_success'])) { $success = $_SESSION['order_success']; unset($_SESSION['order_success']); }
if (isset($_SESSION['order_error']))   { $error   = $_SESSION['order_error'];   unset($_SESSION['order_error']); }

$stmt = $conn->prepare("
    SELECT o.*, s.name AS service_name
    FROM orders o
    JOIN services s ON o.service_id = s.id
    WHERE o.user_id = ?
    ORDER BY o.created_at DESC
");
$stmt->bind_param("i", $uid);
$stmt->execute();
$orders = $stmt->get_result();
$stmt->close();
?>

<div class="wrapper">
    <div class="page-header">
        <h1>📋 Riwayat Pesanan</h1>
        <p>Semua pesanan yang pernah Anda buat</p>
    </div>

    <?php if ($success): ?>
    <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="card">
        <div class="table-wrapper">
            <?php if ($orders->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Layanan</th>
                        <th>Sepatu</th>
                        <th>Tgl Antar</th>
                        <th>Tgl Ambil</th>
                        <th>Total</th>
                        <th>Catatan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php $no = 1; while ($o = $orders->fetch_assoc()): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><strong><?= htmlspecialchars($o['service_name']) ?></strong></td>
                        <td><?= $o['quantity'] ?> pasang</td>
                        <td><?= date('d M Y', strtotime($o['order_date'])) ?></td>
                        <td><?= $o['pickup_date'] ? date('d M Y', strtotime($o['pickup_date'])) : '-' ?></td>
                        <td><strong>Rp <?= number_format($o['total_price'], 0, ',', '.') ?></strong></td>
                        <td>
                            <small style="color:#6b7280">
                                <?= $o['notes'] ? htmlspecialchars(mb_substr($o['notes'], 0, 35)) . '...' : '-' ?>
                            </small>
                        </td>
                        <td><?= statusBadge($o['status']) ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            <?php else: ?>
            <div class="empty-state">
                <div class="es-icon">📭</div>
                <p>Belum ada riwayat pesanan.</p>
                <a href="order.php" class="btn btn-primary mt-2">Buat Pesanan Pertama</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

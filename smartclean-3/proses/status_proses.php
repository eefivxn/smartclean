<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../admin/orders.php'); exit;
}

$order_id = intval($_POST['order_id'] ?? 0);
$status   = $_POST['status'] ?? '';
$allowed  = ['menunggu', 'diproses', 'selesai', 'dibatalkan'];

if (!$order_id || !in_array($status, $allowed)) {
    header('Location: ../admin/orders.php'); exit;
}

$stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
$stmt->bind_param("si", $status, $order_id);

if ($stmt->execute()) {
    $_SESSION['ord_success'] = "Status pesanan #$order_id berhasil diubah menjadi '$status'.";
} else {
    $_SESSION['ord_error'] = 'Gagal mengubah status.';
}
$stmt->close();
$conn->close();

header('Location: ../admin/orders.php');

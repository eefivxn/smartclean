<?php
session_start();
require_once '../config/database.php';
require_once '../includes/auth.php';
requireAdmin();

$action          = $_POST['action'] ?? $_GET['action'] ?? '';
$allowed_actions = ['add', 'edit', 'delete'];

// Validasi action sebelum dijalankan
if (!in_array($action, $allowed_actions)) {
    $_SESSION['svc_error'] = 'Aksi tidak valid.';
    header('Location: ../admin/services.php'); exit;
}

if ($action === 'add') {
    // Add hanya boleh via POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ../admin/services.php'); exit;
    }
    $name     = trim($_POST['name'] ?? '');
    $desc     = trim($_POST['description'] ?? '');
    $price    = floatval($_POST['price'] ?? 0);
    $duration = intval($_POST['duration_days'] ?? 3);

    if (empty($name) || $price <= 0) {
        $_SESSION['svc_error'] = 'Nama dan harga wajib diisi.';
        header('Location: ../admin/services.php'); exit;
    }

    if (strlen($name) > 100) {
        $_SESSION['svc_error'] = 'Nama layanan maksimal 100 karakter.';
        header('Location: ../admin/services.php'); exit;
    }

    if ($duration < 1 || $duration > 30) {
        $_SESSION['svc_error'] = 'Estimasi hari harus antara 1 sampai 30.';
        header('Location: ../admin/services.php'); exit;
    }

    $s = $conn->prepare("INSERT INTO services (name, description, price, duration_days) VALUES (?,?,?,?)");
    $s->bind_param("ssdi", $name, $desc, $price, $duration);
    if ($s->execute()) {
        $_SESSION['svc_success'] = 'Layanan berhasil ditambahkan!';
    } else {
        $_SESSION['svc_error'] = 'Gagal menambahkan layanan.';
    }
    $s->close();

} elseif ($action === 'edit') {
    // Edit hanya boleh via POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: ../admin/services.php'); exit;
    }

    $id       = intval($_POST['id'] ?? 0);
    $name     = trim($_POST['name'] ?? '');
    $desc     = trim($_POST['description'] ?? '');
    $price    = floatval($_POST['price'] ?? 0);
    $duration = intval($_POST['duration_days'] ?? 3);
    $active   = intval($_POST['is_active'] ?? 1);
    $active   = ($active === 1) ? 1 : 0; // pastikan hanya 0 atau 1

    if (!$id || empty($name) || $price <= 0) {
        $_SESSION['svc_error'] = 'Data tidak valid.';
        header('Location: ../admin/services.php'); exit;
    }

    if ($duration < 1 || $duration > 30) {
        $_SESSION['svc_error'] = 'Estimasi hari harus antara 1 sampai 30.';
        header('Location: ../admin/services.php'); exit;
    }

    $s = $conn->prepare("UPDATE services SET name=?, description=?, price=?, duration_days=?, is_active=? WHERE id=?");
    $s->bind_param("ssdiii", $name, $desc, $price, $duration, $active, $id);
    if ($s->execute()) {
        $_SESSION['svc_success'] = 'Layanan berhasil diperbarui!';
    } else {
        $_SESSION['svc_error'] = 'Gagal memperbarui layanan.';
    }
    $s->close();

} elseif ($action === 'delete') {
    // Hapus hanya boleh via GET dengan id yang valid
    $id = intval($_GET['id'] ?? 0);
    if (!$id) {
        header('Location: ../admin/services.php'); exit;
    }

    // Cek layanan masih digunakan pesanan aktif
    $check = $conn->prepare("SELECT COUNT(*) AS c FROM orders WHERE service_id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $used = $check->get_result()->fetch_assoc()['c'];
    $check->close();

    if ($used > 0) {
        // Nonaktifkan saja, jangan hapus (menjaga integritas data)
        $s = $conn->prepare("UPDATE services SET is_active = 0 WHERE id = ?");
        $s->bind_param("i", $id);
        $s->execute();
        $s->close();
        $_SESSION['svc_success'] = 'Layanan dinonaktifkan karena masih ada pesanan terkait.';
    } else {
        $s = $conn->prepare("DELETE FROM services WHERE id = ?");
        $s->bind_param("i", $id);
        if ($s->execute()) {
            $_SESSION['svc_success'] = 'Layanan berhasil dihapus!';
        } else {
            $_SESSION['svc_error'] = 'Gagal menghapus layanan.';
        }
        $s->close();
    }
}

header('Location: ../admin/services.php');
$conn->close();

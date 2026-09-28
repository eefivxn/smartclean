<?php
session_start();
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register.php'); exit;
}

$name     = trim($_POST['name'] ?? '');
$email    = trim($_POST['email'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$password = $_POST['password'] ?? '';
$confirm  = $_POST['confirm_password'] ?? '';

if (empty($name) || empty($email) || empty($password)) {
    $_SESSION['error'] = 'Semua field wajib diisi.';
    header('Location: ../register.php'); exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Format email tidak valid.';
    header('Location: ../register.php'); exit;
}

if (strlen($password) < 6) {
    $_SESSION['error'] = 'Password minimal 6 karakter.';
    header('Location: ../register.php'); exit;
}

if ($password !== $confirm) {
    $_SESSION['error'] = 'Konfirmasi password tidak cocok.';
    header('Location: ../register.php'); exit;
}

$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $_SESSION['error'] = 'Email sudah terdaftar.';
    header('Location: ../register.php'); exit;
}
$stmt->close();

$hashed = password_hash($password, PASSWORD_DEFAULT);
$stmt   = $conn->prepare("INSERT INTO users (name, email, password, phone) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $hashed, $phone);

if ($stmt->execute()) {
    $_SESSION['success'] = 'Registrasi berhasil! Silakan masuk.';
    header('Location: ../login.php');
} else {
    $_SESSION['error'] = 'Registrasi gagal. Coba lagi.';
    header('Location: ../register.php');
}
$stmt->close();
$conn->close();

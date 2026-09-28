<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: user/dashboard.php"); exit;
}
$error = $success = '';
if (isset($_SESSION['error']))   { $error   = $_SESSION['error'];   unset($_SESSION['error']); }
if (isset($_SESSION['success'])) { $success = $_SESSION['success']; unset($_SESSION['success']); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar — Smart Clean</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Sora:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/smartclean/assets/css/style.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <div class="brand-icon">👟</div>
            <h1>Smart Clean</h1>
            <p>Shinning Clean — Buat akun baru</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
        <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="proses/register_proses.php">
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" placeholder="Masukkan nama lengkap" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="email@example.com" required>
            </div>
            <div class="form-group">
                <label class="form-label">Nomor HP</label>
                <input type="text" name="phone" class="form-control" placeholder="08xxxxxxxxxx">
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Min. 6 karakter" required minlength="6">
            </div>
            <div class="form-group">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="confirm_password" class="form-control" placeholder="Ulangi password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg">Daftar Sekarang</button>
        </form>

        <p class="text-center mt-2" style="color:#6b7280;font-size:.9rem;">
            Sudah punya akun? <a href="login.php">Masuk di sini</a>
        </p>
    </div>
</div>
<script src="assets/js/main.js"></script>
</body>
</html>

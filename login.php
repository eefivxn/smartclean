<?php
session_start();
if (isset($_SESSION['user_id'])) {
    $redirect = $_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php';
    header("Location: $redirect"); exit;
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
<title>Masuk — Smart Clean</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Sora:wght@700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/smartclean/assets/css/style.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <div class="brand-icon">👟</div>
            <h1>Smart Clean</h1>
            <p>Shinning Clean — Silakan masuk</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
        <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="POST" action="proses/login_proses.php">
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" placeholder="email@example.com" required>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Password Anda" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block btn-lg">Masuk</button>
        </form>

        <p class="text-center mt-2" style="color:#6b7280;font-size:.9rem;">
            Belum punya akun? <a href="register.php">Daftar gratis</a>
        </p>

        <div class="alert alert-info mt-2" style="font-size:.82rem;">
            🔑 <strong>Demo Admin:</strong> admin@shinningclean.com / password
        </div>
    </div>
</div>
<script src="assets/js/main.js"></script>
</body>
</html>

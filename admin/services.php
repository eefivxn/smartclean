<?php
require_once '../includes/auth.php';
requireAdmin();
require_once '../config/database.php';

$error = $success = '';
if (isset($_SESSION['svc_error'])) {
    $error   = $_SESSION['svc_error'];
    unset($_SESSION['svc_error']);
}
if (isset($_SESSION['svc_success'])) {
    $success = $_SESSION['svc_success'];
    unset($_SESSION['svc_success']);
}

$edit_data = null;
if (isset($_GET['edit'])) {
    $s = $conn->prepare("SELECT * FROM services WHERE id = ?");
    $s->bind_param("i", $_GET['edit']);
    $s->execute();
    $edit_data = $s->get_result()->fetch_assoc();
    $s->close();
}

$services = $conn->query("SELECT * FROM services ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kelola Layanan — Smart Clean</title>
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Sora:wght@700;800&display=swap" rel="stylesheet">
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
            <li><a href="services.php" class="active">🧹 Kelola Layanan</a></li>
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

    <main class="admin-content">
        <div class="page-header">
            <h1>🧹 Kelola Layanan</h1>
            <p>Tambah, edit, dan hapus layanan cuci sepatu</p>
        </div>

        <?php if ($error) : ?>
        <div class="alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if ($success) : ?>
        <div class="alert alert-success">✅ <?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(320px, 1fr));gap:1.5rem;align-items:start">

            <!-- Form Tambah / Edit -->
            <div class="card">
                <div class="card-header">
                    <?= $edit_data ? '✏️ Edit Layanan' : '➕ Tambah Layanan' ?>
                </div>
                <div class="card-body">
                    <form method="POST" action="/smartclean/proses/servis_proses.php">
                        <?php if ($edit_data) : ?>
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="id" value="<?= $edit_data['id'] ?>">
                        <?php else : ?>
                        <input type="hidden" name="action" value="add">
                        <?php endif; ?>

                        <div class="form-group">
                            <label class="form-label">Nama Layanan</label>
                            <input type="text" name="name" class="form-control"
                                   value="<?= htmlspecialchars($edit_data['name'] ?? '') ?>"
                                   placeholder="Contoh: Deep Clean" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control"
                                      placeholder="Deskripsi layanan..."><?= htmlspecialchars($edit_data['description'] ?? '') ?></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Harga (Rp)</label>
                            <input type="number" name="price" class="form-control"
                                   value="<?= $edit_data['price'] ?? '' ?>"
                                   min="1000" placeholder="Contoh: 45000" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Estimasi Selesai (hari)</label>
                            <input type="number" name="duration_days" class="form-control"
                                   value="<?= $edit_data['duration_days'] ?? 3 ?>"
                                   min="1" max="30" required>
                        </div>
                        <?php if ($edit_data) : ?>
                        <div class="form-group">
                            <label class="form-label">Status Layanan</label>
                            <select name="is_active" class="form-control">
                                <option value="1" <?= ($edit_data['is_active'] ?? 1) ? 'selected' : '' ?>>✅ Aktif</option>
                                <option value="0" <?= !($edit_data['is_active'] ?? 1) ? 'selected' : '' ?>>❌ Nonaktif</option>
                            </select>
                        </div>
                        <?php endif; ?>

                        <div class="d-flex gap-1">
                            <button type="submit" class="btn btn-primary">
                                <?= $edit_data ? '💾 Simpan Perubahan' : '➕ Tambah Layanan' ?>
                            </button>
                            <?php if ($edit_data) : ?>
                            <a href="services.php" class="btn btn-outline">Batal</a>
                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabel Layanan -->
            <div class="card">
                <div class="card-header">📋 Semua Layanan (<?= $services->num_rows ?>)</div>
                <div class="table-wrapper">
                    <?php if ($services->num_rows > 0) : ?>
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Layanan</th>
                                <th>Harga</th>
                                <th>Estimasi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php $n = 1; while ($s = $services->fetch_assoc()) : ?>
                            <tr>
                                <td><?= $n++ ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($s['name']) ?></strong>
                                    <?php if ($s['description']) : ?>
                                    <br><small style="color:#6b7280"><?= htmlspecialchars(mb_substr($s['description'], 0, 40)) ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td>Rp <?= number_format($s['price'], 0, ',', '.') ?></td>
                                <td><?= $s['duration_days'] ?> hari</td>
                                <td>
                                    <?php if ($s['is_active']) : ?>
                                    <span class="badge badge-success">✅ Aktif</span>
                                    <?php else : ?>
                                    <span class="badge badge-danger">❌ Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="services.php?edit=<?= $s['id'] ?>"
                                       class="btn btn-warning btn-sm">✏️ Edit</a>
                                    <a href="/smartclean/proses/servis_proses.php?action=delete&id=<?= $s['id'] ?>"
                                       class="btn btn-danger btn-sm"
                                       data-confirm="Yakin ingin menghapus layanan '<?= htmlspecialchars($s['name']) ?>'?">
                                       🗑️ Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php else : ?>
                    <div class="empty-state">
                        <div class="es-icon">🧹</div>
                        <p>Belum ada layanan. Tambahkan layanan pertama!</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </main>
</div>

<script src="/smartclean/assets/js/main.js"></script>
</body>
</html>

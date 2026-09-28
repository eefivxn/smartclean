<?php
$page_title = 'Buat Pesanan';
require_once '../includes/auth.php';
requireLogin();
require_once '../config/database.php';
include '../includes/header.php';

$services = $conn->query("SELECT * FROM services WHERE is_active = 1 ORDER BY name ASC");
$services_list = [];
while ($s = $services->fetch_assoc()) $services_list[] = $s;

$selected_id = intval($_GET['service_id'] ?? 0);

$error = '';
if (isset($_SESSION['order_error'])) { $error = $_SESSION['order_error']; unset($_SESSION['order_error']); }
?>

<div class="wrapper">
    <div class="page-header">
        <h1>📦 Buat Pesanan</h1>
        <p>Isi formulir di bawah untuk membuat pesanan baru</p>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div style="max-width:600px">
        <div class="card">
            <div class="card-header">📝 Detail Pesanan</div>
            <div class="card-body">
                <form method="POST" action="/smartclean/proses/order_proses.php">

                    <div class="form-group">
                        <label class="form-label">Layanan</label>
                        <select name="service_id" class="form-control" id="serviceSelect" required onchange="updatePrice()">
                            <option value="">-- Pilih Layanan --</option>
                            <?php foreach ($services_list as $s): ?>
                            <option value="<?= $s['id'] ?>"
                                data-price="<?= $s['price'] ?>"
                                data-duration="<?= $s['duration_days'] ?>"
                                <?= $selected_id === $s['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s['name']) ?> — Rp <?= number_format($s['price'], 0, ',', '.') ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jumlah Sepatu (pasang)</label>
                        <input type="number" name="quantity" class="form-control" id="quantityInput"
                               min="1" max="20" value="1" required onchange="updatePrice()">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal Antar</label>
                        <input type="date" name="order_date" class="form-control"
                               min="<?= date('Y-m-d') ?>"
                               value="<?= date('Y-m-d') ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea name="notes" class="form-control"
                            placeholder="Contoh: Sepatu putih, ada noda tinta di bagian kiri..."></textarea>
                    </div>

                    <!-- Preview Harga -->
                    <div id="pricePreview" class="price-preview" style="display:none">
                        <div class="price-preview-row">
                            <span style="color:#6b7280">Harga per pasang</span>
                            <span id="pricePerUnit">-</span>
                        </div>
                        <div class="price-preview-row">
                            <span style="color:#6b7280">Jumlah</span>
                            <span id="previewQty">-</span>
                        </div>
                        <div class="price-preview-total">
                            <span>Total Pembayaran</span>
                            <span id="totalPrice" style="color:#1a73e8">-</span>
                        </div>
                        <p id="estimasiText" style="color:#6b7280;font-size:.82rem;margin-top:.5rem"></p>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block btn-lg">🛒 Buat Pesanan</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function formatRupiah(n) {
    return 'Rp ' + Math.round(n).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}
function updatePrice() {
    const sel     = document.getElementById('serviceSelect');
    const qty     = parseInt(document.getElementById('quantityInput').value) || 1;
    const opt     = sel.options[sel.selectedIndex];
    const preview = document.getElementById('pricePreview');
    if (sel.value && opt.dataset.price) {
        const price = parseFloat(opt.dataset.price);
        document.getElementById('pricePerUnit').textContent = formatRupiah(price);
        document.getElementById('previewQty').textContent   = qty + ' pasang';
        document.getElementById('totalPrice').textContent   = formatRupiah(price * qty);
        document.getElementById('estimasiText').textContent = '🕐 Estimasi selesai: ' + opt.dataset.duration + ' hari kerja';
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}
window.addEventListener('load', updatePrice);
</script>

<?php include '../includes/footer.php'; ?>

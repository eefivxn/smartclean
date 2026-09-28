
document.addEventListener('DOMContentLoaded', function () {

    // ── AUTO HIDE ALERT ──────────────────────────
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(function () { alert.remove(); }, 500);
        }, 4000);
    });

    // ── KONFIRMASI HAPUS ─────────────────────────
    const deleteBtns = document.querySelectorAll('[data-confirm]');
    deleteBtns.forEach(function (btn) {
        btn.addEventListener('click', function (e) {
            const msg = btn.getAttribute('data-confirm') || 'Yakin ingin menghapus?';
            if (!confirm(msg)) e.preventDefault();
        });
    });

    // ── PREVIEW HARGA ORDER ──────────────────────
    const serviceSelect = document.getElementById('serviceSelect');
    const quantityInput = document.getElementById('quantityInput');

    if (serviceSelect && quantityInput) {
        serviceSelect.addEventListener('change', updatePricePreview);
        quantityInput.addEventListener('input', updatePricePreview);
        updatePricePreview();
    }

    function updatePricePreview() {
        if (!serviceSelect || !quantityInput) return;

        const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
        const qty = parseInt(quantityInput.value) || 1;
        const preview = document.getElementById('pricePreview');
        if (!preview) return;

        if (serviceSelect.value && selectedOption.dataset.price) {
            const price = parseFloat(selectedOption.dataset.price);
            const duration = selectedOption.dataset.duration || '-';
            const total = price * qty;

            const elPerUnit = document.getElementById('pricePerUnit');
            const elQty = document.getElementById('previewQty');
            const elTotal = document.getElementById('totalPrice');
            const elEst = document.getElementById('estimasiText');

            if (elPerUnit) elPerUnit.textContent = formatRupiah(price);
            if (elQty) elQty.textContent = qty + ' pasang';
            if (elTotal) elTotal.textContent = formatRupiah(total);
            if (elEst) elEst.textContent = '🕐 Estimasi selesai: ' + duration + ' hari kerja';

            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }
    }

    // ── FORMAT RUPIAH ────────────────────────────
    function formatRupiah(angka) {
        return 'Rp ' + Math.round(angka).toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    }

    // ── STATUS BADGE REALTIME ────────────────────
    const statusSelects = document.querySelectorAll('select[name="status"]');
    statusSelects.forEach(function (sel) {
        sel.addEventListener('change', function () {
            const row = this.closest('tr');
            if (!row) return;

            const badge = row.querySelector('.badge');
            if (!badge) return;

            const map = {
                'menunggu': ['badge-warning', '⏳ Menunggu'],
                'diproses': ['badge-info', '⚙️ Diproses'],
                'selesai': ['badge-success', '✅ Selesai'],
                'dibatalkan': ['badge-danger', '❌ Dibatalkan'],
            };

            const [cls, label] = map[this.value] || ['badge-info', this.value];
            badge.className = 'badge ' + cls;
            badge.textContent = label;
        });
    });

    // ── MODAL CATATAN (FIX STABIL) ───────────────
    const modal = document.getElementById('notesModal');
    const notesContent = document.getElementById('notesContent');
    const closeBtn = document.getElementById('closeNotes');

    // gunakan event delegation (lebih aman & fleksibel)
    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-notes]');
        if (btn) {
            const text = btn.getAttribute('data-notes') || '';

            if (notesContent) {
                notesContent.innerText = text;
            }

            if (modal) {
                modal.style.display = 'flex';
            }
        }
    });

    // tombol close
    if (closeBtn) {
        closeBtn.addEventListener('click', function () {
            if (modal) modal.style.display = 'none';
        });
    }

    // klik luar modal
    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    }

});
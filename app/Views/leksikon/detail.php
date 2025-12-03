<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="row">
        <!-- Kolom Konten Utama (8/12) -->
        <div class="col-md-8">
            <div class="card-neo">
                <div class="card-body p-4">
                    <!-- Bagian Header Konten -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <!-- Menampilkan Term (Judul Utama) -->
                            <h1 class="card-title fw-bold" style="color: var(--primary-color);"><?= htmlspecialchars($entri["term"]) ?></h1>
                        </div>
                        <!-- Tombol Favorit -->
                        <button id="favorite-btn" 
                                class="btn btn-neo btn-sm <?= $isFavorited ? 'btn-warning-neo' : 'btn-primary-neo' ?>" 
                                data-id="<?= $entri['id'] ?>"
                                data-favorited="<?= $isFavorited ? 'true' : 'false' ?>"
                        >
                            <i class="fa-solid fa-star"></i> 
                            <span class="ms-2 favorite-text"><?= $isFavorited ? 'Sudah jadi Favorit' : 'Tandai Favorit' ?></span>
                        </button>
                    </div>

                    <!-- Menampilkan Nama Sumber (jika ada) -->
                    <span class="badge badge-neo bg-info text-dark mb-3">Sumber: <?= htmlspecialchars($entri["nama_sumber"] ?? "Umum") ?></span>

                    <!-- Menampilkan Definisi -->
                    <h4 class="mt-4 fw-bold">Definisi</h4>
                    <p style="white-space: pre-wrap;"><?= htmlspecialchars($entri["definition"]) ?></p>

                    <!-- Menampilkan Deskripsi Sumber -->
                    <?php if (!empty($entri["deskripsi_sumber"])): ?>
                        <h5 class="mt-4 text-muted fw-bold">Deskripsi Sumber</h5>
                        <p><em><?= htmlspecialchars($entri["deskripsi_sumber"]) ?></em></p>
                    <?php endif; ?>

                    <!-- Menampilkan Contoh Penggunaan (jika ada) -->
                    <?php if ($contoh): ?>
                    <h4 class="mt-4 fw-bold">Contoh Penggunaan</h4>
                    <?php foreach ($contoh as $c): ?>
                        <div class="card-neo p-3 mb-3">
                            <p class="mb-1"><strong><?= htmlspecialchars($c["contoh_teks"]) ?></strong></p>
                            <?php if ($c["terjemahan"]): ?>
                            <p class="text-muted fst-italic mb-0"><?= htmlspecialchars($c["terjemahan"]) ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Sidebar Kanan (4/12) -->
        <div class="col-md-4">
            <div class="card-neo">
                <div class="card-header-neo">Navigasi</div>
                <div class="card-body">
                    <a href="<?= site_url('/') ?>" class="btn btn-neo btn-primary-neo w-100">
                        <i class="fas fa-arrow-left me-2"></i> Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const favoriteBtn = document.getElementById('favorite-btn');
    if (favoriteBtn) {
        favoriteBtn.addEventListener('click', function() {
            const entriId = this.dataset.id;
            const isFavorited = this.dataset.favorited === 'true';
            const url = `<?= base_url('leksikon/toggle_favorite/') ?>${entriId}`;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    // CSRF token might be needed for POST requests in CI4
                    // '<?= csrf_header() ?>': '<?= csrf_hash() ?>' 
                },
                // body: JSON.stringify({ entri_id: entriId }) // If controller expects JSON body
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update button state and text
                    const favoriteText = favoriteBtn.querySelector('.favorite-text');
                    if (data.action === 'added') {
                        favoriteBtn.classList.remove('btn-primary-neo');
                        favoriteBtn.classList.add('btn-warning-neo');
                        favoriteText.textContent = 'Sudah jadi Favorit';
                        favoriteBtn.dataset.favorited = 'true';
                    } else {
                        favoriteBtn.classList.remove('btn-warning-neo');
                        favoriteBtn.classList.add('btn-primary-neo');
                        favoriteText.textContent = 'Tandai Favorit';
                        favoriteBtn.dataset.favorited = 'false';
                    }
                    // Optionally show a toast/alert
                    console.log(data.message);
                } else {
                    alert(data.message); // e.g., "Anda harus login..."
                }
            })
            .catch(error => {
                console.error('Error toggling favorite:', error);
                alert('Terjadi kesalahan saat mengubah status favorit.');
            });
        });
    }
});
</script>
<?= $this->endSection() ?>
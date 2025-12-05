<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            

            <!-- Header -->
            <div class="card-neo p-4 mb-4" style="background-color: var(--primary-color); color: var(--white);">
                <h1 class="display-6 fw-bold">Jelajahi Leksikon</h1>
                <p>Selamat datang di pusat data leksikon bahasa Jawa, Kawi, dan Sanskerta. Gunakan filter di bawah atau cari langsung untuk menemukan istilah yang Anda inginkan.</p>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-8 mx-auto">
                    <form action="" method="get">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-neo" 
                                   style="border-width: 3px; font-size: 1.1rem;"
                                   placeholder="Cari istilah atau arti..." 
                                   name="keyword" 
                                   value="<?= esc($keyword ?? '') ?>">
                            <button class="btn btn-primary-neo px-4" type="submit">
                                <i class="fas fa-search me-2"></i>CARI
                            </button>
                        </div>
                        <?php if ($keyword) : ?>
                            <input type="hidden" name="sumber" value="<?= esc($selected_sumber ?? '') ?>">
                            <input type="hidden" name="sort_by" value="<?= esc($sort_by ?? '') ?>">
                            <input type="hidden" name="sort_order" value="<?= esc($sort_order ?? '') ?>">
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <?php if (isset($keyword) && empty($daftar_entri)): ?>
                <div class="alert alert-warning border-3 border-dark fw-bold text-center">
                    Data tidak ditemukan untuk kata kunci "<?= esc($keyword) ?>"
                </div>
            <?php endif; ?>

            <!-- Filters -->
            <div class="card-neo p-3 mb-4">
                <form action="<?= site_url('/') ?>" method="get" class="row g-3 align-items-center">
                    <div class="col-md-6">
                        <label for="sumber_filter" class="form-label fw-bold">Filter Sumber:</label>
                        <select class="form-control form-control-neo" name="sumber" id="sumber_filter" onchange="this.form.submit()">
                            <option value="">Semua Sumber</option>
                            <?php foreach ($sumber_list as $sumber_item) : ?>
                                <option value="<?= $sumber_item['id'] ?>" <?= ($selected_sumber ?? null) == $sumber_item['id'] ? 'selected' : '' ?>>
                                    <?= esc($sumber_item['nama_sumber']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="sort_by_filter" class="form-label fw-bold">Urutkan:</label>
                        <div class="d-flex">
                            <select class="form-control form-control-neo" name="sort_by" id="sort_by_filter" onchange="this.form.submit()">
                                <option value="term" <?= ($sort_by ?? 'term') == 'term' ? 'selected' : '' ?>>Istilah</option>
                                <option value="id" <?= ($sort_by ?? 'term') == 'id' ? 'selected' : '' ?>>ID</option>
                                <option value="created_at" <?= ($sort_by ?? 'term') == 'created_at' ? 'selected' : '' ?>>Tanggal</option>
                            </select>
                            <select class="form-control form-control-neo ms-2" name="sort_order" onchange="this.form.submit()">
                                <option value="ASC" <?= ($sort_order ?? 'ASC') == 'ASC' ? 'selected' : '' ?>>A-Z</option>
                                <option value="DESC" <?= ($sort_order ?? 'ASC') == 'DESC' ? 'selected' : '' ?>>Z-A</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Entry List -->
            <div class="card-neo">
                <div class="card-header-neo d-flex justify-content-between align-items-center">
                    <span>Daftar Entri</span>
                    
                    <span class="badge bg-white text-primary rounded-pill shadow-sm" style="font-size: 0.9rem; color: var(--primary-color) !important;">
                        <i class="fas fa-database me-1"></i> 
                        <?= number_format($pager->getTotal(), 0, ',', '.') ?> Entri
                    </span>
                </div>

                <div class="list-group list-group-flush">
                    <?php if (!empty($daftar_entri)) : ?>
                        <?php foreach ($daftar_entri as $entri) : ?>
                            <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-3">
                                <a href="<?= site_url('leksikon/detail/' . $entri['id']) ?>" class="d-flex flex-grow-1 text-decoration-none text-dark">
                                    <div>
                                        <h6 class="mb-1 fw-bold" style="color: var(--primary-color);"><?= highlight_keyword(esc($entri['term']), $keyword ?? '') ?></h6>
                                        <small class="text-muted"><?= highlight_keyword(esc(substr($entri['definition'], 0, 120)), $keyword ?? '') ?>...</small>
                                    </div>
                                </a>
                                <button class="btn btn-link favorite-toggle p-0 ms-3" data-id="<?= $entri['id'] ?>" data-favorited="<?= $entri['isFavorited'] ? 'true' : 'false' ?>">
                                    <i class="<?= $entri['isFavorited'] ? 'fa-solid fa-star text-warning' : 'fa-regular fa-star' ?>" style="font-size: 1.5rem;"></i>
                                </button>
                            </div>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <?php if (empty($daftar_entri) && $keyword) : ?>
                            <div class="alert alert-warning border-2 border-dark shadow-sm text-center py-4 my-3 mx-3">
                                <h4><i class="fas fa-search-minus mb-2"></i></h4>
                                <p class="mb-0 fw-bold">Data tidak ditemukan untuk kata kunci "<?= esc($keyword) ?>"</p>
                                <a href="<?= base_url('/') ?>" class="btn btn-sm btn-outline-dark mt-2 fw-bold">Reset Pencarian</a>
                            </div>
                        <?php else : ?>
                            <div class="list-group-item p-5 text-center">
                                <h5 class="text-muted">Tidak ada entri ditemukan.</h5>
                                <p>Coba ganti filter atau tambahkan data baru.</p>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
                <?php if (empty($entri)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-search fa-3x mb-3 text-gray-300"></i>
                            <p>Istilah tidak ditemukan.</p>
                        </div>
                    <?php endif; ?>

                    <div class="mt-5 mb-5 d-flex justify-content-center">
                        <?= $pager->links('default', 'default_full') ?>
                    </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <?php if (isset($notif_kuis) && $notif_kuis) : ?>
                <div class="alert alert-<?= $notif_kuis['tipe'] == 'success' ? 'success' : 'danger' ?> border-2 border-dark shadow-sm fw-bold mb-4" role="alert">
                    <i class="fas <?= $notif_kuis['tipe'] == 'success' ? 'fa-check-circle' : 'fa-times-circle' ?> me-2"></i> <?= $notif_kuis['pesan'] ?>
                </div>
            <?php endif; ?>
            <div class="card-neo mb-4">
                <div class="card-header-neo">Kata Hari Ini</div>
                <div class="card-body">
                    <?php if ($kata_hari_ini) : ?>
                        <h5 class="card-title fw-bold"><?= esc($kata_hari_ini['term']) ?></h5>
                        <p class="card-text text-muted"><?= esc(substr($kata_hari_ini['definition'], 0, 100)) ?>...</p>
                        <a href="<?= site_url('leksikon/detail/' . $kata_hari_ini['id']) ?>" class="btn btn-sm btn-neo btn-primary-neo">Lihat Detail</a>
                    <?php else : ?>
                         <p class="text-muted">Tidak ada kata untuk ditampilkan.</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card-neo mb-4">
                <div class="card-header-neo">Kuis Harian</div>
                <div class="card-body">
                    <?php if ($kuis_soal) : ?>
                        <p><strong>Definisi:</strong> "<?= esc($kuis_soal['definition']) ?>"</p>
                        <p class="fw-bold">Manakah istilah yang tepat?</p>
                        <form action="<?= site_url('leksikon/checkQuiz') ?>" method="POST">
                            <?php foreach ($kuis_pilihan as $p) : ?>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="jawaban_user" id="kuis_<?= esc($p) ?>" value="<?= esc($p) ?>" required>
                                <label class="form-check-label" for="kuis_<?= esc($p) ?>">
                                    <?= esc($p) ?>
                                </label>
                            </div>
                            <?php endforeach; ?>
                            <button type="submit" class="btn btn-neo btn-warning-neo w-100 mt-3">Jawab Kuis</button>
                        </form>
                    <?php else : ?>
                        <p class="text-muted">Kamu sudah menjawab kuis hari ini. Terima kasih!</p>
                        <a href="<?= site_url('leksikon/resetQuiz') ?>" class="btn btn-neo btn-outline-dark">
                            <i class="fas fa-redo me-1"></i> Coba Kuis Lagi
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card-neo mb-4">
                <div class="card-header-neo"><i class="fa-solid fa-star text-warning"></i> Favorit Saya</div>
                <ul class="list-group" id="favorites-list">
                    <li class="list-group-item text-muted small p-3">Memuat favorit...</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const favoritesList = document.getElementById('favorites-list');

    function loadFavorites() {
        if (!favoritesList) return;
        
        // Add a loading indicator
        favoritesList.innerHTML = '<li class="list-group-item text-muted small p-3">Memuat favorit...</li>';

        fetch('<?= site_url('leksikon/getFavorites') ?>', {
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        })
        .then(response => response.json())
        .then(result => {
            favoritesList.innerHTML = ''; // Clear loading state
            if (result.status === 'success' && result.data.length > 0) {
                result.data.forEach(item => {
                    const li = document.createElement('a');
                    li.href = `<?= site_url('leksikon/detail/') ?>${item.id}`;
                    li.className = 'list-group-item list-group-item-action';
                    li.textContent = item.term;
                    favoritesList.appendChild(li);
                });
            } else if (result.status === 'error') {
                 const li = document.createElement('li');
                 li.className = 'list-group-item text-muted small p-3';
                 li.innerHTML = `Silakan <a href="<?=site_url('login')?>">login</a> untuk melihat favorit.`;
                 favoritesList.appendChild(li);
            }
            else {
                const li = document.createElement('li');
                li.className = 'list-group-item text-muted small p-3';
                li.textContent = 'Anda belum memiliki favorit.';
                favoritesList.appendChild(li);
            }
        })
        .catch(err => {
            console.error('Error fetching favorites:', err);
            favoritesList.innerHTML = '<li class="list-group-item text-danger small p-3">Gagal memuat favorit.</li>';
        });
    }
    
    // Load favorites on page load if user is potentially logged in
    <?php if (session()->get('isLoggedIn')) : ?>
        loadFavorites();
    <?php else : ?>
        favoritesList.innerHTML = `<li class="list-group-item text-muted small p-3">Silakan <a href="<?=site_url('login')?>">login</a> untuk melihat favorit.</li>`;
    <?php endif; ?>

    // Handle favorite toggle clicks
    document.querySelectorAll('.favorite-toggle').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default button action

            const entriId = this.dataset.id;
            const isFavorited = this.dataset.favorited === 'true';
            const icon = this.querySelector('i');
            const self = this; // Store reference to the button

            fetch(`<?= site_url('leksikon/toggleFavorite/') ?>${entriId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    '<?= csrf_header() ?>': '<?= csrf_hash() ?>' // CSRF Token
                },
                // For POST, you might need to send a body, even if empty, for CSRF.
                // Or you can skip Content-Type for empty body POSTs.
                body: JSON.stringify({}) 
            })
            .then(response => response.json())
            .then(result => {
                if (result.status === 'success') {
                    // Update icon and dataset
                    if (result.action === 'added') {
                        icon.classList.remove('fa-regular');
                        icon.classList.add('fa-solid', 'text-warning');
                        self.dataset.favorited = 'true';
                    } else {
                        icon.classList.remove('fa-solid', 'text-warning');
                        icon.classList.add('fa-regular');
                        self.dataset.favorited = 'false';
                    }
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: result.message,
                        showConfirmButton: false,
                        timer: 1500
                    });
                    loadFavorites(); // Reload favorites list in sidebar
                } else if (result.status === 'error') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: result.message,
                        showConfirmButton: false,
                        timer: 3000
                    });
                     if (result.message.includes('login')) { // Redirect to login if user needs to login
                        setTimeout(() => window.location.href = '<?= site_url('login') ?>', 1500);
                    }
                }
            })
            .catch(error => {
                console.error('Error toggling favorite:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error Jaringan!',
                    text: 'Gagal terhubung ke server.',
                    showConfirmButton: false,
                    timer: 3000
                });
            });
        });
    });
});
</script>
<?= $this->endSection() ?>
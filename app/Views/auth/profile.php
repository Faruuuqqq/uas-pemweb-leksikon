<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="display-5 fw-bold mb-4" style="color: var(--primary-color);">Profil Saya</h1>
            
            <div class="card-neo mb-5">
                <div class="card-header-neo">
                    Edit Informasi Akun
                </div>
                <div class="card-body p-4">
                    <?php if (session()->getFlashdata('message')): ?>
                        <div class="alert alert-success border-2 border-dark fw-bold">
                            <?= session()->getFlashdata('message') ?>
                        </div>
                    <?php endif; ?>
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger border-2 border-dark fw-bold">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= site_url('profile/update') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label for="username" class="form-label fw-bold">Username</label>
                            <input type="text" class="form-control form-control-neo" id="username" name="username" value="<?= esc(old('username', $user['username'] ?? '')) ?>" required>
                            <?php if (isset($validation) && $validation->hasError('username')): ?>
                                <div class="text-danger mt-1 small"><?= $validation->getError('username') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control form-control-neo" id="email" name="email" value="<?= esc(old('email', $user['email'] ?? '')) ?>" required>
                            <?php if (isset($validation) && $validation->hasError('email')): ?>
                                <div class="text-danger mt-1 small"><?= $validation->getError('email') ?></div>
                            <?php endif; ?>
                        </div>

                        <hr class="my-4">
                        <p class="text-muted">Kosongkan password jika Anda tidak ingin mengubahnya.</p>
                        
                        <div class="mb-3">
                            <label for="password" class="form-label fw-bold">Password Baru</label>
                            <input type="password" class="form-control form-control-neo" id="password" name="password">
                            <?php if (isset($validation) && $validation->hasError('password')): ?>
                                <div class="text-danger mt-1 small"><?= $validation->getError('password') ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-3">
                            <label for="pass_confirm" class="form-label fw-bold">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control form-control-neo" id="pass_confirm" name="pass_confirm">
                            <?php if (isset($validation) && $validation->hasError('pass_confirm')): ?>
                                <div class="text-danger mt-1 small"><?= $validation->getError('pass_confirm') ?></div>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-neo btn-primary-neo w-100 mt-3 py-2">Simpan Perubahan</button>
                    </form>
                </div>
            </div>

            <div class="card-neo">
                 <div class="card-header-neo d-flex align-items-center">
                    <i class="fa-solid fa-star text-warning me-2"></i> Entri Favorit Saya
                </div>
                <div class="list-group list-group-flush" id="profile-favorites-list">
                    <div class="p-4 text-muted">Memuat favorit...</div>
                </div>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const favoritesList = document.getElementById('profile-favorites-list');

    function loadFavorites() {
        if (!favoritesList) return;
        
        fetch('<?= site_url('leksikon/get_favorites') ?>', {
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        })
        .then(response => response.json())
        .then(result => {
            favoritesList.innerHTML = ''; // Clear loading state
            if (result.status === 'success' && result.data.length > 0) {
                result.data.forEach(item => {
                    const a = document.createElement('a');
                    a.href = `<?= site_url('leksikon/detail/') ?>${item.id}`;
                    a.className = 'list-group-item list-group-item-action fw-bold';
                    a.textContent = item.term;
                    favoritesList.appendChild(a);
                });
            } else if (result.status === 'error') {
                 const div = document.createElement('div');
                 div.className = 'p-4 text-danger';
                 div.textContent = 'Gagal memuat favorit.';
                 favoritesList.appendChild(div);
            }
            else {
                const div = document.createElement('div');
                div.className = 'p-4 text-muted';
                div.textContent = 'Anda belum memiliki entri favorit.';
                favoritesList.appendChild(div);
            }
        })
        .catch(err => {
            console.error('Error fetching favorites:', err);
            favoritesList.innerHTML = '<div class="p-4 text-danger">Gagal memuat favorit.</div>';
        });
    }
    
    loadFavorites();
});
</script>
<?= $this->endSection() ?>

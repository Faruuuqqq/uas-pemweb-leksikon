<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Leksikon Jawa Modern' ?></title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-custom sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('/') ?>">
                <i class="fas fa-shapes me-2"></i>Leksikon<span style="color:var(--text-color)">Dev</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('/') ?>">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('/portal') ?>">Portal Pencarian</a></li>
                    
                    <?php if (session()->get('isLoggedIn')) : ?>
                         <li class="nav-item ms-3">
                            <span class="fw-bold me-2">Halo, <?= session()->get('username') ?>!</span>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-danger-neo btn-sm ms-2" href="<?= base_url('/logout') ?>">
                                <i class="fas fa-sign-out-alt me-1"></i> Logout
                            </a>
                        </li>
                    <?php else : ?>
                        <li class="nav-item">
                            <a class="btn btn-primary-neo btn-sm ms-3" href="<?= base_url('/login') ?>">
                                <i class="fas fa-user me-1"></i> Login
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="main-content py-5">
        <?= $this->renderSection('content') ?>
    </div>

    <footer class="py-4 mt-auto" style="border-top: 3px solid var(--border-color); background: var(--white);">
        <div class="container text-center">
            <p class="mb-0 fw-bold">&copy; <?= date('Y') ?> Leksikon Modern. <span class="text-muted fw-normal">Dibuat dengan gaya Neobrutalism.</span></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        setTimeout(function() {
            let alert = document.querySelector('.alert');
            if(alert) {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 3000);

        // SweetAlert2 for flash messages
        <?php if (session()->getFlashdata('message')) : ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '<?= session()->getFlashdata('message') ?>',
                showConfirmButton: false,
                timer: 3000
            });
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')) : ?>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: '<?= session()->getFlashdata('error') ?>',
                showConfirmButton: false,
                timer: 3000
            });
        <?php endif; ?>
        <?php if (session('validation')) : ?>
            Swal.fire({
                icon: 'error',
                title: 'Validation Error!',
                html: '<?= str_replace(["\n", "\r"], '', session('validation')->listErrors('list')) ?>', // 'list' format for ul/li
                showConfirmButton: false,
                timer: 5000 // Give more time for multiple errors
            });
        <?php endif; ?>
    </script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>

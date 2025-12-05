<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title><?= $this->renderSection('title', true) ?? 'Leksikon' ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('css/style.css') ?>">

    <style>
        body {
            background-color: #f4f7f6;
            background-image: linear-gradient(180deg, #e6e9f0 0%, #eef1f5 100%);
            min-height: 100vh;
        }
    </style>
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <script>
        const CI_BASE_URL = "<?= base_url() ?>";
        const CI_SITE_URL = "<?= site_url() ?>";
        const CI_CSRF_TOKEN = "<?= csrf_hash() ?>";
        const CI_CSRF_HEADER = "<?= csrf_header() ?>";
    </script>
</head>
<body class="bg-light"> 

<!-- Navbar Utama -->
<nav class="navbar navbar-expand-lg navbar-light shadow-sm navbar-glass sticky-top">
  <div class="container">
    <a class="navbar-brand" href="<?= site_url('/') ?>">Sastra Jawa - Leksikon</a>
    <div class="ms-auto d-flex align-items-center">
        <?php if (session()->isLoggedIn && session()->get('role') === 'admin') : ?>
            <a href="<?= site_url('admin') ?>" class="btn btn-outline-dark btn-sm me-3">Manajemen Entri (CRUD)</a>
        <?php endif; ?>

        <?php if (session()->isLoggedIn) : ?>
            <span class="navbar-text me-3">
                Halo, <?= session()->get('username') ?>
            </span>
            <a href="<?= site_url('logout') ?>" class="btn btn-outline-danger btn-sm">Logout</a>
        <?php else : ?>
            <a href="<?= site_url('login') ?>" class="btn btn-outline-primary btn-sm me-2">Login</a>
            <a href="<?= site_url('register') ?>" class="btn btn-primary btn-sm">Register</a>
        <?php endif; ?>
    </div>
  </div>
</nav>

<!-- Kontainer Utama Halaman -->
<div class="container mt-4 page-fade-in">
    <?= $this->renderSection('content') ?>
</div>

<footer class="text-center p-4 bg-white border-top mt-5">
    <p class="mb-0 text-muted">UTS Pemrograman Web 2025 - Achmad Faruq Mahdison - 140810240080</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('js/script.js') ?>"></script>

<?= $this->renderSection('scripts') ?>

</body>
</html>
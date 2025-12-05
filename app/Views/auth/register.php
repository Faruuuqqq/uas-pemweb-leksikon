<!DOCTYPE html>
<html lang="id">
<head>
    <title>Register - Leksikon</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <style>
        body {
            background-color: var(--secondary-color); /* Yellow background for register */
            background-image: 
                radial-gradient(var(--border-color) 1px, transparent 1px),
                radial-gradient(var(--border-color) 1px, transparent 1px);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 0;
        }
        .register-card {
            background: var(--white);
            border: 4px solid var(--border-color);
            box-shadow: 8px 8px 0px var(--border-color);
            border-radius: 15px;
            padding: 40px;
            width: 100%;
            max-width: 500px;
        }
    </style>
</head>
<body>

    <div class="register-card">
        <div class="text-center mb-4">
            <h1 class="fw-800 display-6" style="color: var(--primary-color);">BUAT AKUN BARU</h1>
            <p class="text-muted">Isi form di bawah untuk mulai berkontribusi.</p>
        </div>

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger border-2 border-dark fw-bold">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('message')) : ?>
            <div class="alert alert-success border-2 border-dark fw-bold">
                <?= session()->getFlashdata('message') ?>
            </div>
        <?php endif; ?>
        <?php if (session('validation')) : ?>
            <div class="alert alert-danger border-2 border-dark fw-bold">
                <?= session('validation')->listErrors() ?>
            </div>
        <?php endif; ?>
        
        <form action="<?= site_url('register/save') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label for="username" class="form-label fw-bold">Username</label>
                <input type="text" class="form-control form-control-neo" id="username" name="username" value="<?= esc(old('username')) ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label fw-bold">Email</label>
                <input type="email" class="form-control form-control-neo" id="email" name="email" value="<?= esc(old('email')) ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label fw-bold">Password</label>
                <input type="password" class="form-control form-control-neo" id="password" name="password" required>
            </div>
            <div class="mb-4">
                <label for="pass_confirm" class="form-label fw-bold">Konfirmasi Password</label>
                <input type="password" class="form-control form-control-neo" id="pass_confirm" name="pass_confirm" required>
            </div>
            <button type="submit" class="btn btn-neo btn-primary-neo w-100 py-3">DAFTAR SEKARANG</button>
        </form>

        <div class="text-center mt-4 pt-3 border-top border-2 border-light">
            <small>Sudah punya akun? <a href="<?= site_url('login') ?>" class="fw-bold text-dark">Login di sini</a></small>
        </div>
    </div>

</body>
</html>
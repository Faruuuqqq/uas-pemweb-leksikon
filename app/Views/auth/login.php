<!DOCTYPE html>
<html lang="id">
<head>
    <title>Login - Leksikon</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <style>
        body {
            background-color: var(--primary-color);
            background-image: 
                radial-gradient(var(--white) 1px, transparent 1px),
                radial-gradient(var(--white) 1px, transparent 1px);
            background-size: 20px 20px;
            background-position: 0 0, 10px 10px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: var(--white);
            border: 4px solid var(--border-color);
            box-shadow: 8px 8px 0px var(--border-color);
            border-radius: 15px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="text-center mb-4">
            <h1 class="fw-800 display-6">WELCOME BACK!</h1>
            <p class="text-muted">Silakan masuk untuk mengelola data leksikon.</p>
        </div>

        <?php if (session()->getFlashdata('error')) : ?>
            <div class="alert alert-danger border-2 border-dark fw-bold">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('login') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="fw-bold mb-1">Email / Username</label>
                <input type="text" name="email" class="form-control form-control-neo" placeholder="user@leksikon.id">
            </div>
            <div class="mb-4">
                <label class="fw-bold mb-1">Password</label>
                <input type="password" name="password" class="form-control form-control-neo" placeholder="******">
            </div>
            <button type="submit" class="btn btn-neo btn-primary-neo w-100 py-3">MASUK SEKARANG</button>
        </form>

        <div class="text-center mt-4 pt-3 border-top border-2 border-light">
            <small>Belum punya akun? <a href="<?= base_url('register') ?>" class="fw-bold text-dark">Daftar di sini</a></small>
        </div>
    </div>

</body>
</html>

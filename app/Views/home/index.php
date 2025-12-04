<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="card-neo p-5 text-center">
        <h1 class="fw-bold display-4 mb-3">Selamat Datang di Leksikon Jawa Modern</h1>
        <p class="lead text-muted mb-4">Temukan istilah-istilah Jawa kuno dan modern beserta definisinya.</p>
        <a href="<?= base_url('/leksikon') ?>" class="btn btn-primary-neo btn-lg-neo">Mulai Jelajahi</a>
    </div>
</div>
<?= $this->endSection() ?>

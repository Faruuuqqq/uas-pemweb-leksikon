<?php require 'partials/header.php'; 

// Kata Hari Ini (W0TD)
if (!isset($_SESSION['wotd_id'])) {
    $kata_hari_ini = $pdo->query("SELECT id, term, definition FROM entri ORDER BY RAND() LIMIT 1")->fetch();
    if ($kata_hari_ini) {
        $_SESSION['wotd_id'] = $kata_hari_ini['id'];
    }
} else {
    $stmt_wotd = $pdo->prepare("SELECT id, term, definition FROM entri WHERE id = ?");
    $stmt_wotd->execute([$_SESSION['wotd_id']]);
    $kata_hari_ini = $stmt_wotd->fetch();
    if (!$kata_hari_ini) {
        $kata_hari_ini = $pdo->query("SELECT id, term, definition FROM entri ORDER BY RAND() LIMIT 1")->fetch();
        if ($kata_hari_ini) {
            $_SESSION['wotd_id'] = $kata_hari_ini['id'];
        }
    }
}

// Kuis Harian
$kuis_soal = null;
$kuis_pilihan = [];
if (!isset($_SESSION['kuis_selesai'])) {
    if (!isset($_SESSION['kuis_jawaban'])) {
        $kuis_soal_db = $pdo->query("SELECT * FROM entri ORDER BY RAND() LIMIT 1")->fetch();
        if ($kuis_soal_db) {
            $_SESSION['kuis_jawaban'] = $kuis_soal_db['term'];
            $_SESSION['kuis_soal_definisi'] = $kuis_soal_db['definition']; 
            $id_benar = $kuis_soal_db['id'];
            $pengecoh = $pdo->query("SELECT term FROM entri WHERE id != $id_benar ORDER BY RAND() LIMIT 3")->fetchAll();
            $kuis_pilihan_db = [$_SESSION['kuis_jawaban']];
            foreach ($pengecoh as $p) { $kuis_pilihan_db[] = $p['term']; }
            shuffle($kuis_pilihan_db);
            $_SESSION['kuis_pilihan'] = $kuis_pilihan_db; 
            $kuis_soal = ['definition' => $_SESSION['kuis_soal_definisi']];
            $kuis_pilihan = $_SESSION['kuis_pilihan'];
        }
    } else {
        $kuis_soal = ['definition' => $_SESSION['kuis_soal_definisi']];
        $kuis_pilihan = $_SESSION['kuis_pilihan'];
    }
}

// Logika Notifikasi Kuis
$notif_kuis = null;
if (isset($_SESSION['notif_kuis'])) {
    $notif_kuis = $_SESSION['notif_kuis'];
    unset($_SESSION['notif_kuis']);
}

$batas = 10; 
$halaman_sekarang = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
if ($halaman_sekarang < 1) $halaman_sekarang = 1;
$total_entri = $pdo->query("SELECT COUNT(*) FROM entri")->fetchColumn();
$total_halaman = ceil($total_entri / $batas);
if ($halaman_sekarang > $total_halaman && $total_halaman > 0) $halaman_sekarang = $total_halaman;
$offset = ($halaman_sekarang - 1) * $batas;

$stmt_entri = $pdo->prepare("SELECT id, term, definition FROM entri ORDER BY term ASC LIMIT :batas OFFSET :offset"); 
$stmt_entri->bindParam(':batas', $batas, PDO::PARAM_INT);
$stmt_entri->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt_entri->execute();
$daftar_entri = $stmt_entri->fetchAll();

?>

<div class="row">
    <div class="col-md-8">
        
        <?php if ($notif_kuis): ?>
            <div class="alert alert-<?= $notif_kuis['tipe'] ?> alert-dismissible fade show shadow-sm" role="alert">
                <?= $notif_kuis['pesan'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="mb-4 position-relative">
            <input type="text" class="form-control form-control-lg" id="search-input" placeholder="🔍 Cari term (kata) Leksikon...">
            <div id="search-results-container" class="list-group position-absolute w-100" style="z-index: 1000;">
                </div>
        </div>

        <div id="main-entry-list">
            <h4>Selamat Datang di Leksikon - Sastra Jawa</h4>
            <p class="text-muted">Ini adalah prototipe yang dibuat ulang dari `sastra.org/leksikon`. Gunakan search di atas untuk menemukan term, atau jelajahi daftar di bawah ini.</p>
            
            <div class="card mt-4">
                <div class="card-header">Daftar Entri (Halaman <?= $halaman_sekarang ?> dari <?= $total_halaman ?>)</div>
                <div class="list-group list-group-flush">
                    <?php if ($daftar_entri): ?>
                        <?php foreach ($daftar_entri as $entri): ?>
                            <a href="detail.php?id=<?= $entri['id'] ?>" class="list-group-item list-group-item-action">
                                <h6 class="mb-1"><?= htmlspecialchars($entri['term']) ?></h6>
                                <small class="text-muted"><?= htmlspecialchars(substr($entri['definition'], 0, 150)) ?>...</small>
                            </a>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="list-group-item">Tidak ada entri ditemukan.</div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($total_halaman > 1): ?>
            <nav aria-label="Navigasi Halaman" class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item <?= ($halaman_sekarang <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?halaman=<?= $halaman_sekarang - 1 ?>">Previous</a>
                    </li>
                    
                    <?php 
                    $range = 2;
                    $mulai = max(1, $halaman_sekarang - $range);
                    $akhir = min($total_halaman, $halaman_sekarang + $range);

                    if ($mulai > 1) {
                        echo '<li class="page-item"><a class="page-link" href="?halaman=1">1</a></li>';
                        if ($mulai > 2) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                    }

                    for ($i = $mulai; $i <= $akhir; $i++): 
                    ?>
                        <li class="page-item <?= ($i == $halaman_sekarang) ? 'active' : '' ?>">
                            <a class="page-link" href="?halaman=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; 

                    if ($akhir < $total_halaman) {
                        if ($akhir < $total_halaman - 1) echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        echo '<li class="page-item"><a class="page-link" href="?halaman='.$total_halaman.'">'.$total_halaman.'</a></li>';
                    }
                    ?>
                    
                    <li class="page-item <?= ($halaman_sekarang >= $total_halaman) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?halaman=<?= $halaman_sekarang + 1 ?>">Next</a>
                    </li>
                </ul>
            </nav>
            <?php endif; ?>
            </div>

    </div>

    <div class="col-md-4">
        
        <div class="card mb-4">
            <div class="card-header"><strong>Kata Hari Ini</strong></div>
            <div class="card-body">
                <?php if ($kata_hari_ini): ?>
                    <h5 class="card-title"><?= htmlspecialchars($kata_hari_ini['term']) ?></h5>
                    <p class="card-text text-muted"><?= htmlspecialchars(substr($kata_hari_ini['definition'], 0, 100)) ?>...</p>
                    <a href="detail.php?id=<?= $kata_hari_ini['id'] ?>" class="btn btn-sm btn-outline-primary">Lihat Detail</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><strong>Kuis Harian</strong></div>
            <div class="card-body">
                <?php if ($kuis_soal): ?>
                    <p><strong>Definisi:</strong> "<?= htmlspecialchars($kuis_soal['definition']) ?>"</p>
                    <p>Manakah istilah yang tepat?</p>
                    <form action="controllers/cek_kuis.php" method="POST">
                        <?php foreach ($kuis_pilihan as $p): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="jawaban_user" id="kuis_<?= htmlspecialchars($p) ?>" value="<?= htmlspecialchars($p) ?>" required>
                            <label class="form-check-label" for="kuis_<?= htmlspecialchars($p) ?>">
                                <?= htmlspecialchars($p) ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                        <button type="submit" class="btn btn-primary btn-sm mt-3">Jawab</button>
                    </form>
                <?php else: ?>
                    <p class="text-muted">Kamu sudah menjawab kuis hari ini. Terima kasih!</p>
                    <a href="controllers/reset_kuis.php" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-clockwise"></i> Coba Kuis Lagi (Reset)
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header"><strong><i class="bi bi-star-fill text-warning"></i> Favorit Saya</strong></div>
            <ul class="list-group list-group-flush" id="favorites-list">
                <li class="list-group-item text-muted small">Memuat favorit...</li>
            </ul>
        </div>
    </div>
</div>

<?php require 'partials/footer.php'; ?>
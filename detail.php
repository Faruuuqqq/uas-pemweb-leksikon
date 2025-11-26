<?php
require "partials/header.php";

// Mengambil ID entri dari parameter URL (?id=...)
$id = $_GET["id"] ?? null;
// Validasi ID: harus ada dan harus numerik
if (!$id || !is_numeric($id)) {
  // Jika tidak valid, tampilkan pesan error dan hentikan script
  echo "<div class='alert alert-danger'>ID tidak ditemukan atau tidak valid.</div>";
  require "partials/footer.php"; // Sertakan footer sebelum exit
  exit();
}
// Konversi ID ke integer (meskipun PDO bisa menangani string, ini praktik yang baik)
$id = (int) $id;

// --- Mengambil Data Entri dari Database ---
// Menyiapkan query SELECT:
// Mengambil semua kolom dari 'entri' (e.*)
// Mengambil 'nama_sumber' dan 'deskripsi' (di-alias sebagai 'deskripsi_sumber') dari tabel 'sumber' (s).
// LEFT JOIN menggabungkan 'entri' dengan 'sumber' berdasarkan 'sumber_id'.
// WHERE e.id = ? memfilter berdasarkan ID yang diberikan.
$stmt = $pdo->prepare("SELECT e.*, s.nama_sumber, s.deskripsi AS deskripsi_sumber
                       FROM entri e
                       LEFT JOIN sumber s ON e.sumber_id = s.id
                       WHERE e.id = ?");
// Menjalankan query dengan ID sebagai parameter
$stmt->execute([$id]);
// Mengambil satu baris hasil query sebagai array asosiatif
$entri = $stmt->fetch();

if (!$entri) {
  // Tampilkan pesan error dan hentikan script
  echo "<div class='alert alert-danger'>Entri dengan ID $id tidak ditemukan.</div>";
  require "partials/footer.php";
  exit();
}

// --- Mengambil Contoh Penggunaan (jika ada) ---
// Menyiapkan query SELECT untuk mengambil semua contoh penggunaan yang terkait dengan entri_id
$stmt_contoh = $pdo->prepare(
  "SELECT * FROM contoh_penggunaan WHERE entri_id = ?",
);
$stmt_contoh->execute([$id]);
$contoh = $stmt_contoh->fetchAll();
?>

<!-- Layout Halaman Detail (Grid Bootstrap) -->
<div class="row">
    <!-- Kolom Konten Utama (8/12) -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <!-- Bagian Header Konten -->
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <!-- Menampilkan Term (Judul Utama) -->
                        <h1 class="card-title"><?= htmlspecialchars(
                          $entri["term"],
                        ) ?></h1>
                    </div>
                    <!-- Tombol/Ikon Favorit -->
                    <!-- class="favorite-btn" akan dideteksi oleh JavaScript -->
                    <!-- data-id="<?= $entri[
                      "id"
                    ] ?>" menyimpan ID entri untuk JavaScript -->
                    <i class="bi bi-star favorite-btn" data-id="<?= $entri[
                      "id"
                    ] ?>" style="font-size: 2rem; cursor: pointer;" title="Tambah ke Favorit"></i>
                </div>

                <!-- Menampilkan Nama Sumber (jika ada) -->
                <!-- '?? Umum' adalah null coalescing: jika nama_sumber null, tampilkan 'Umum' -->
                <span class="badge bg-primary mb-3"><?= htmlspecialchars(
                  $entri["nama_sumber"] ?? "Umum",
                ) ?></span>

                <!-- Menampilkan Definisi -->
                <h4 class="mt-4">Definition (Arti)</h4>
                <!-- style="white-space: pre-wrap;" agar format teks (newline) dari database tetap terjaga -->
                <p style="white-space: pre-wrap;"><?= htmlspecialchars(
                  $entri["definition"],
                ) ?></p>

                <!-- Menampilkan Deskripsi Sumber -->
                <h5 class="mt-4 text-muted">Source (Sumber)</h5>
                <!-- '?? Tidak diketahui' jika deskripsi_sumber null -->
                <p><em><?= htmlspecialchars(
                  $entri["deskripsi_sumber"] ?? "Tidak diketahui",
                ) ?></em></p>

                <!-- Menampilkan Contoh Penggunaan (jika ada) -->
                <?php if ($contoh): ?>
                <h4 class="mt-4">Contoh Penggunaan</h4>
                <?php foreach ($contoh as $c): ?>
                    <figure class="mt-3">
                      <blockquote class="blockquote">
                        <p><?= htmlspecialchars($c["contoh_teks"]) ?></p>
                      </blockquote>
                      <?php if ($c["terjemahan"]): ?>
                      <figcaption class="blockquote-footer">
                        <?= htmlspecialchars($c["terjemahan"]) ?>
                      </figcaption>
                      <?php endif; ?>
                    </figure>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Sidebar Kanan (4/12) -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><strong>Navigasi</strong></div>
            <div class="card-body">
                <a href="index.php" class="btn btn-outline-primary w-100">Kembali ke Pencarian</a>
            </div>
        </div>
    </div>
</div>

<?php require "partials/footer.php";
?>

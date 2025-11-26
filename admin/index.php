<?php
require '../partials/header.php';

// --- LOGIKA PAGINASI ---
$batas = 20;
// Mengambil nomor halaman saat ini dari URL (?halaman=...), default ke 1 jika tidak ada
$halaman_sekarang = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
if ($halaman_sekarang < 1) $halaman_sekarang = 1;

// Menghitung total entri di database
$total_entri = $pdo->query("SELECT COUNT(*) FROM entri")->fetchColumn();
// hitung ini untuk total halamannya (pembulatan ke atas)
$total_halaman = ceil($total_entri / $batas);
// Jika halaman saat ini melebihi total halaman (dan ada data), set ke halaman terakhir
if ($halaman_sekarang > $total_halaman && $total_halaman > 0) $halaman_sekarang = $total_halaman;

// Menghitung offset (mulai dari baris ke berapa) untuk query LIMIT
$offset = ($halaman_sekarang - 1) * $batas;

// --- Mengambil Data Entri untuk Halaman Saat Ini ---
// Menyiapkan query SELECT:
// Mengambil semua kolom dari 'entri' (e.*) dan 'nama_sumber' dari tabel 'sumber' (s).
// LEFT JOIN menggabungkan 'entri' dengan 'sumber' berdasarkan 'sumber_id'.
// ORDER BY e.term ASC mengurutkan berdasarkan term secara alfabetis.
// LIMIT :batas OFFSET :offset membatasi hasil sesuai paginasi.
$stmt_entri = $pdo->prepare("SELECT e.*, s.nama_sumber FROM entri e
                    LEFT JOIN sumber s ON e.sumber_id = s.id
                    ORDER BY e.id ASC 
                    LIMIT :batas OFFSET :offset");

// Mengikat nilai variabel $batas dan $offset ke placeholder :batas dan :offset
$stmt_entri->bindParam(':batas', $batas, PDO::PARAM_INT);
$stmt_entri->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt_entri->execute();
$entri = $stmt_entri->fetchAll();
?>

<!-- Bagian Header Halaman CRUD -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Manajemen Entri (CRUD)</h3>
    <a href="tambah.php" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Tambah Entri Baru
    </a>
</div>

<!-- Menampilkan Notifikasi Sukses (jika ada parameter status di URL) -->
<?php if (isset($_GET['status'])): ?>
    <?php if ($_GET['status'] == 'sukses'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          Data berhasil disimpan!
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($_GET['status'] == 'sukses_hapus'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          Data berhasil dihapus!
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Tabel untuk Menampilkan Data Entri -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Term</th>
                        <th>Sumber</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- ini buat menampilkan dataa -->
                    <?php if ($entri): ?>
                        <?php foreach ($entri as $e):?>
                        <tr>
                            <td><?= $e['id'] ?></td>
                            <td><?= htmlspecialchars($e['term']) ?></td>
                            <td>
                                <span class="badge bg-secondary"><?= htmlspecialchars($e['nama_sumber'] ?? 'N/A') ?></span>
                            </td>
                            <td class="text-nowrap">
                                <!-- Tombol Lihat (ke halaman detail.php) -->
                                <a href="../detail.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-info" title="Lihat">
                                    <i class="bi bi-eye"></i> <!-- Ikon mata -->
                                </a>
                                <!-- Tombol Edit (ke halaman edit.php) -->
                                <a href="edit.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <!-- Tombol Hapus (ke halaman hapus.php) -->
                                <a href="hapus.php?id=<?= $e['id'] ?>" class="btn btn-sm btn-danger"
                                   onclick="return confirm('Yakin ingin menghapus \'<?= htmlspecialchars(addslashes($e['term'])) ?>\'?')">
                                    <!-- onclick menampilkan konfirmasi JavaScript sebelum menghapus -->
                                    <!-- addslashes() dibutuhkan di dalam string JS confirm() jika term mengandung kutip -->
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">Belum ada data entri.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- --- PAGINASI  --- -->
        <?php if ($total_halaman > 1): // tampil jika lebih dari 1 halaman ?>
        <nav aria-label="Navigasi Halaman Admin" class="mt-3">
            <ul class="pagination justify-content-center"> <!-- Paginasi di tengah -->
                <!-- Tombol Previous -->
                <li class="page-item <?= ($halaman_sekarang <= 1) ? 'disabled' : '' // Disable jika di halaman 1 ?>">
                    <a class="page-link" href="?halaman=<?= $halaman_sekarang - 1 ?>">Previous</a>
                </li>

                <!-- Tampilan Halaman X dari Y -->
                <li class="page-item active" aria-current="page">
                    <span class="page-link">Halaman <?= $halaman_sekarang ?> dari <?= $total_halaman ?></span>
                </li>

                <!-- Tombol Next -->
                <li class="page-item <?= ($halaman_sekarang >= $total_halaman) ? 'disabled' : '' // Disable jika di halaman terakhir ?>">
                    <a class="page-link" href="?halaman=<?= $halaman_sekarang + 1 ?>">Next</a>
                </li>
            </ul>
        </nav>
        <?php endif; ?>
        <!
    </div>
</div>

<?php
require '../partials/footer.php';
?>

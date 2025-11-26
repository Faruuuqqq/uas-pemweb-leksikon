<?php
require "../partials/header.php";

// Cek ini methodnya post atau bukan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // ini ngambil data dari form yang disubmit melalui array $_POST
  $term = $_POST["term"];
  $definition = $_POST["definition"];
  $sumber_id = $_POST["sumber_id"]; // ID dari dropdown sumber

  $sumber_id = !empty($sumber_id) ? $sumber_id : null;

  $stmt = $pdo->prepare(
    "INSERT INTO entri (term, definition, sumber_id) VALUES (?, ?, ?)",
  );
  $stmt->execute([$term, $definition, $sumber_id]);

  // kalo berhasil langsung redirect dan return parameter sukses
  header("Location: index.php?status=sukses");
  exit();
}

// Mengambil semua data dari tabel 'sumber' untuk mengisi pilihan dropdown
$sumber_list = $pdo
  ->query("SELECT * FROM sumber ORDER BY nama_sumber ASC")
  ->fetchAll();
?>

<!-- Judul halaman -->
<h3>Tambah Entri Baru</h3>

<!-- Form untuk menambah data baru -->
<div class="card">
    <div class="card-body">
        <form method="POST"> <!-- Method POST akan mengirim data ke halaman ini sendiri -->
            <!-- Input untuk Term -->
            <div class="mb-3">
                <label for="term" class="form-label">Term (Kata)</label>
                <input type="text" class="form-control" id="term" name="term" required>
            </div>

            <!-- Dropdown (select) untuk Sumber -->
            <div class="mb-3">
                <label for="sumber_id" class="form-label">Sumber</label>
                <select class="form-select" id="sumber_id" name="sumber_id">
                    <option value="">-- Pilih Sumber --</option>
                    <?php foreach ($sumber_list as $s): ?>
                      // Loop melalui daftar sumber
                      ?>
                        <option value="<?= $s["id"]
                      // Nilai dari value itu ID sumber
                      ?>">
                            <?= htmlspecialchars($s["nama_sumber"]) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Textarea untuk Definition -->
            <div class="mb-3">
                <label for="definition" class="form-label">Definition (Arti)</label>
                <textarea class="form-control" id="definition" name="definition" rows="5" required></textarea>
            </div>

            <!-- Tombol Submit dan Batal -->
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<?php require "../partials/footer.php";
?>

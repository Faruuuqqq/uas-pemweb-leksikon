<?php
require "../partials/header.php";

// Mengambil ID entri dari parameter URL (?id=...)
$id = $_GET["id"] ?? null; // default null jika tidak ada

// Validasi ID: harus ada dan harus numerik
if (!$id || !is_numeric($id)) {
  header("Location: index.php"); // kalo tidak ada redirect lagi
  exit();
}

// Cek apakah methodnya POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Mengambil data dari form yang disubmit
  $term = $_POST["term"];
  $definition = $_POST["definition"];
  $sumber_id = $_POST["sumber_id"];

  // Mengubah sumber_id menjadi null jika string kosong
  $sumber_id = !empty($sumber_id) ? $sumber_id : null;

  // Menyiapkan query UPDATE untuk tabel entri
  $stmt = $pdo->prepare(
    "UPDATE entri SET term = ?, definition = ?, sumber_id = ? WHERE id = ?",
  );

  // Menjalankan query dengan data dari form sebagai parameter (sesuai urutan placeholder '?')
  $stmt->execute([$term, $definition, $sumber_id, $id]);

  header("Location: index.php?status=sukses");
  exit();
}

// Menyiapkan query SELECT untuk mengambil data entri yang akan diedit berdasarkan ID
$stmt = $pdo->prepare("SELECT * FROM entri WHERE id = ?");
// Menjalankan query dengan ID dari URL
$stmt->execute([$id]);
// Mengambil satu baris data entri
$entri = $stmt->fetch();

// Jika entri dengan ID tersebut tidak ditemukan di database
if (!$entri) {
  echo "<div class='alert alert-danger'>Entri tidak ditemukan.</div>";
  require "../partials/footer.php";
  exit();
}

// Mengambil semua data dari tabel 'sumber' untuk mengisi pilihan dropdown
$sumber_list = $pdo
  ->query("SELECT * FROM sumber ORDER BY nama_sumber ASC")
  ->fetchAll();
?>

<h3>Edit Entri: <?= htmlspecialchars($entri["term"]) ?></h3>

<!-- Form untuk mengedit data -->
<div class="card">
    <div class="card-body">
        <form method="POST"> <!-- Method POST akan mengirim data ke halaman ini sendiri -->
            <!-- Input untuk Term -->
            <div class="mb-3">
                <label for="term" class="form-label">Term (Kata)</label>
                <input type="text" class="form-control" id="term" name="term" value="<?= htmlspecialchars(
                  $entri["term"],
                ) ?>" required>
            </div>

            <!-- Dropdown untuk Sumber -->
            <div class="mb-3">
                <label for="sumber_id" class="form-label">Sumber</label>
                <select class="form-select" id="sumber_id" name="sumber_id">
                    <option value="">-- Pilih Sumber --</option>
                    <?php foreach ($sumber_list as $s): ?>
                        <option value="<?= $s["id"] ?>" <?= $s["id"] ==
$entri["sumber_id"]
  ? "selected"
  : "" ?>>
                            <?= htmlspecialchars($s["nama_sumber"]) ?>
                        </option>
                        <!-- Logika 'selected': jika ID sumber di list sama dengan sumber_id entri, tambahkan atribut 'selected' -->
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Textarea untuk Definition -->
            <div class="mb-3">
                <label for="definition" class="form-label">Definition (Arti)</label>

                <textarea class="form-control" id="definition" name="definition" rows="5" required><?= htmlspecialchars(
                  $entri["definition"],
                ) ?></textarea>
            </div>

            <!-- Tombol Submit dan Batal -->
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="index.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<?php require "../partials/footer.php";
?>

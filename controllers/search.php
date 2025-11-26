<?php
require_once __DIR__ . '/../config/database.php';

header("Content-Type: application/json");
// Mengambil query pencarian dari parameter GET 'q', default string kosong ''
$q = $_GET["q"] ?? "";

// Memeriksa apakah panjang query kurang dari 1 karakter
if (strlen($q) < 1) {
  // Ubah ke 1 agar bisa cari 'a'
  // Jika terlalu pendek, kirim array JSON kosong dan hentikan script
  echo json_encode([]);
  exit();
}

// Menyiapkan query SQL untuk mencari entri
// Mencari di kolom 'term' ATAU 'definition' yang dimulai dengan query ($q%)
// LIMIT 5 membatasi hasil maksimal 5 entri
$stmt = $pdo->prepare("SELECT id, term, definition FROM entri
                        WHERE term LIKE ? OR definition LIKE ?
                        LIMIT 5");
// Menjalankan query dengan query pencarian (ditambah wildcard '%') sebagai parameter untuk kedua placeholder
$stmt->execute(["$q%", "$q%"]);

// Mengambil semua hasil query
$hasil = $stmt->fetchAll();

echo json_encode($hasil);
?>

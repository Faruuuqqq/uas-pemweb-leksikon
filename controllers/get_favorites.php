<?php
require_once __DIR__ . '/../config/database.php';

// Menetapkan header respons sebagai JSON
header("Content-Type: application/json");

// Mengambil string JSON berisi array ID dari parameter GET 'ids', defaultnya '[]' jika tidak ada
$ids_json = $_GET["ids"] ?? "[]";
// Mendecode string JSON menjadi array PHP
$ids = json_decode($ids_json, true);

// Memeriksa apakah array $ids kosong atau bukan array
if (empty($ids) || !is_array($ids)) {
  // Jika kosong atau tidak valid, kirim respons error JSON dan hentikan script
  echo json_encode(["error" => "Tidak ada ID favorit"]);
  exit();
}

// Membuat placeholder '?' sejumlah ID yang ada untuk query SQL 'IN'
$placeholders = implode(",", array_fill(0, count($ids), "?"));
// Memastikan semua elemen dalam array $ids adalah integer untuk keamanan
$safe_ids = array_map("intval", $ids);

// Menyiapkan query SQL untuk mengambil id dan term dari tabel entri
// WHERE id IN (...) akan mencari entri yang ID-nya ada dalam daftar yang diberikan
$stmt = $pdo->prepare("SELECT id, term FROM entri WHERE id IN ($placeholders)");
// Menjalankan query dengan array ID yang sudah disanitasi sebagai parameter
$stmt->execute($safe_ids);
// Mengambil semua hasil query sebagai array asosiatif
$hasil = $stmt->fetchAll();

// Mengirimkan hasil query dalam format JSON ke browser
echo json_encode($hasil);
?>

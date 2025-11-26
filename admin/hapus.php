<?php
require '../config/database.php';

// Mengambil ID entri dari parameter URL (?id=...)
$id = $_GET['id'] ?? null;

// Memeriksa apakah ID ada
if ($id) {
    // Menyiapkan query DELETE untuk tabel entri berdasarkan ID
    $stmt = $pdo->prepare("DELETE FROM entri WHERE id = ?");
    $stmt->execute([$id]);
}

// Redirect kembali ke halaman CRUD dengan status sukses hapus
header('Location: index.php?status=sukses_hapus');
exit;
?>

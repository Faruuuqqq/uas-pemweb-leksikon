<?php
require_once __DIR__ . '/../config/database.php';

// Menghapus semua variabel session yang terkait dengan kuis
unset($_SESSION['kuis_selesai']);       // Hapus status selesai
unset($_SESSION['notif_kuis']);         // Hapus notifikasi sebelumnya
unset($_SESSION['kuis_jawaban']);       // Hapus jawaban benar
unset($_SESSION['kuis_pilihan']);       // Hapus pilihan jawaban
unset($_SESSION['kuis_soal_definisi']); // Hapus definisi soal

// Menghapus variabel session untuk Kata Hari Ini (WOTD)
// Agar WOTD juga ikut di-random ulang saat kuis di-reset
unset($_SESSION['wotd_id']);

header('Location: ../index.php');
exit;
?>

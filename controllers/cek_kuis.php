<?php
require_once __DIR__ . '/../config/database.php';

// Mengambil jawaban pengguna dari data POST, default string kosong jika tidak ada
$jawaban_user = $_POST['jawaban_user'] ?? '';
// Mengambil jawaban benar yang disimpan di session, default string kosong jika tidak ada
$jawaban_benar = $_SESSION['kuis_jawaban'] ?? '';

// Memeriksa apakah jawaban user dan jawaban benar keduanya tidak kosong
if (!empty($jawaban_user) && !empty($jawaban_benar)) {
    if ($jawaban_user === $jawaban_benar) {
        // Jika benar, simpan notifikasi sukses ke session
        $_SESSION['notif_kuis'] = ['tipe' => 'success', 'pesan' => 'Jawaban kuis benar!'];
    } else {
        // Jika salah, simpan notifikasi error ke session, tampilkan jawaban benar
        $_SESSION['notif_kuis'] = ['tipe' => 'danger', 'pesan' => "Jawaban salah. Yang benar: " . htmlspecialchars($jawaban_benar)];
    }
}

// Menandai bahwa kuis untuk sesi ini sudah selesai/dikerjakan
$_SESSION['kuis_selesai'] = true;

// Membersihkan data kuis dari session agar tidak muncul lagi atau terpakai ulang
unset($_SESSION['kuis_jawaban']);       // Hapus jawaban benar
unset($_SESSION['kuis_pilihan']);       // Hapus pilihan jawaban
unset($_SESSION['kuis_soal_definisi']); // Hapus definisi soal

header('Location: ../index.php');
exit;
?>

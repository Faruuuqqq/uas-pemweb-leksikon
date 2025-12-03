1. SweetAlert2 (Gantikan alert() Biasa) Daripada pakai confirm('Yakin hapus?') bawaan browser yang jelek, pakai library SweetAlert2.

Tambahkan di Layout Header:

HTML

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
Ubah tombol hapus di View Admin:

JavaScript

// Ganti onclick="return confirm..." jadi:
onclick="Swal.fire({
  title: 'Yakin hapus?',
  text: 'Data tidak bisa kembali!',
  icon: 'warning',
  showCancelButton: true,
  confirmButtonColor: '#d33',
  confirmButtonText: 'Ya, Hapus!'
}).then((result) => {
  if (result.isConfirmed) {
    this.closest('form').submit();
  }
}); return false;"
2. Flash Message Otomatis Hilang Pesan "Data berhasil disimpan" sebaiknya hilang sendiri setelah 3 detik.

Tambahkan script di layout/main.php bagian bawah:

JavaScript

setTimeout(function() {
    let alert = document.querySelector('.alert');
    if(alert) {
        let bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    }
}, 3000);
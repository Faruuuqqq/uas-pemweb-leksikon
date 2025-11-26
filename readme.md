# Prototype Sastra.org/leksikon oleh Achmad Faruq Mahdison 140810240080

---

## Fitur Utama
* **Pencarian cepat (AJAX)** – mencari kata tanpa reload halaman.
* **CRUD entri** – tambah, lihat, edit, dan hapus data kamus.
* **Kuis harian** – tebak kata berdasarkan definisi (pakai PHP session).
* **Kata hari ini** – menampilkan kata acak setiap sesi baru.
* **Favorit** – menandai kata favorit, disimpan di cookie browser.

---

## Teknologi yang Digunakan
* PHP
* MySQL
* HTML5, Bootstrap 5
* JavaScript (Fetch API, DOM, Cookies)

---

## Struktur Folder

```
/
├── controllers/     # Penanganan request (API & form)
├── assets/          # File CSS & JS
├── config/          # Koneksi database
├── admin/           # Halaman CRUD
├── partials/        # Template (header, footer)
├── database/        # Skrip database & data awal (seed)
│   ├── database.sql
│   └── seed/
│
├── index.php        # Halaman utama
└── detail.php       # Detail entri
```

---

## Cara Menjalankan
### 1. Siapkan Database

* Buat database baru (misal `leksikon_jawa`).
* Impor file `database.sql` untuk membuat tabel.

### 2. Konfigurasi Koneksi

* Buka `config/database.php`.
* Ubah nilai `$host`, `$dbname`, `$username`, dan `$password` sesuai MySQL lokal.
* Sesuaikan `BASE_PATH` dengan nama folder proyek di `htdocs`.

### 3. Isi Data Awal

* Untuk sementara Jalankan `seed/insert_data.sql` melalui phpMyAdmin untuk mengisi tabel `sumber` dan `entri`.
<!-- * Buka `http://localhost/NAMA_FOLDER_PROYEK/seed/import_data.php` untuk mengimpor data entri dari `dataEntri.json`. -->

### 4. Jalankan Proyek

* Pastikan server lokal aktif.
* Buka di browser: `http://localhost/NAMA_FOLDER_PROYEK/`

---

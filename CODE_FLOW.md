# Alur Kode Proyek Leksikon

Dokumen ini menjelaskan alur kerja dan struktur kode dari proyek Leksikon, sebuah aplikasi web berbasis PHP untuk menampilkan dan mengelola sebuah kamus atau leksikon.

### Daftar Isi
1.  [Gambaran Umum Proyek](#1-gambaran-umum-proyek)
2.  [Skema Database](#2-skema-database)
3.  [Struktur File](#3-struktur-file)
4.  [Alur Aplikasi](#4-alur-aplikasi)
5.  [Hubungan Antar Tabel (Database)](#hubungan-antar-tabel-database)
6.  [Logika Live Search (AJAX)](#logika-live-search-ajax)
7.  [Penggunaan Session dan Cookies](#penggunaan-session-dan-cookies)
8.  [Logika Detail Sisi Klien (script.js)](#logika-detail-sisi-klien-assetsjsscriptjs)

---

## 1. Gambaran Umum Proyek

Proyek ini adalah aplikasi web yang memungkinkan pengguna untuk:
- Menjelajahi dan mencari entri leksikon.
- Melihat detail dari setiap entri, termasuk definisi dan contoh penggunaan.
- Mendapatkan "Kata Hari Ini" (Word of the Day).
- Mengikuti kuis harian.
- Menandai entri sebagai favorit.
- Mengelola entri melalui antarmuka CRUD (Create, Read, Update, Delete).

Aplikasi ini menggunakan PHP sebagai bahasa backend, MariaDB/MySQL sebagai database, dan sedikit JavaScript untuk fungsionalitas di sisi klien (pencarian, favorit).

## 2. Skema Database

Database `leksikon` terdiri dari tiga tabel utama yang didefinisikan dalam `database.sql`:

- **`sumber`**: Menyimpan sumber dari entri leksikon (misalnya, "Bausastra Jawa").
  - `id`: Primary Key
  - `nama_sumber`: Nama dari sumber.
  - `deskripsi`: Deskripsi singkat tentang sumber.

- **`entri`**: Tabel utama yang berisi istilah dan definisi.
  - `id`: Primary Key
  - `term`: Istilah atau kata kunci.
  - `definition`: Definisi dari istilah tersebut.
  - `sumber_id`: Foreign Key yang merujuk ke `sumber.id`.

- **`contoh_penggunaan`**: Menyimpan kalimat contoh untuk setiap entri.
  - `id`: Primary Key
  - `entri_id`: Foreign Key yang merujuk ke `entri.id`.
  - `contoh_teks`: Kalimat contoh.
  - `terjemahan`: Terjemahan dari kalimat contoh.

## 3. Struktur File

Berikut adalah rincian file dan direktori penting:

- **`index.php`**: Halaman utama yang menampilkan pencarian, daftar entri, Kata Hari Ini, dan kuis.
- **`detail.php`**: Menampilkan detail lengkap dari satu entri.
- **`config/database.php`**: Skrip koneksi database menggunakan PDO.
- **`partials/header.php` & `partials/footer.php`**: Template untuk bagian header dan footer halaman.
- **`assets/`**: Berisi file statis seperti CSS (`style.css`) dan JavaScript (`script.js`).
- **`controllers/`**: Berisi semua skrip yang menangani request dari pengguna.
  - `search.php`: Menangani permintaan pencarian (API).
  - `get_favorites.php`: Mengambil data favorit (API).
  - `cek_kuis.php`: Memeriksa jawaban kuis dari form.
  - `reset_kuis.php`: Mereset status kuis di session.
- **`admin/`**: Direktori untuk manajemen data (Create, Read, Update, Delete).
  - `index.php`: Halaman utama CRUD.
  - `tambah.php`: Form untuk menambah entri baru.
  - `edit.php`: Form untuk mengedit entri.
  - `hapus.php`: Skrip untuk menghapus entri.
- **`database/`**: Berisi file terkait database.
  - `database.sql`: Skrip SQL untuk membuat struktur database.
  - `seed/`: Skrip dan data untuk mengisi database awal.

## 4. Alur Aplikasi

### Halaman Utama (`index.php`)

1.  **Inisialisasi**: `includes/header.php` disertakan, yang juga memuat koneksi database dari `config/database.php` dan memulai session.
2.  **Kata Hari Ini**: Skrip memeriksa apakah `$_SESSION['wotd_id']` sudah ada. Jika tidak, ia mengambil satu entri acak dari database dan menyimpan ID-nya di session. Jika sudah ada, ia mengambil entri berdasarkan ID yang tersimpan.
3.  **Kuis Harian**: Mirip dengan Kata Hari Ini, kuis dihasilkan sekali per sesi. Jawaban yang benar dan pilihan pengecoh disimpan dalam `$_SESSION`.
4.  **Daftar Entri**: Skrip melakukan paginasi dan mengambil daftar entri dari database untuk ditampilkan.
5.  **Render Tampilan**: Halaman dirender dengan semua data yang telah disiapkan.

### Pencarian (`assets/js/script.js` & `controllers/search.php`)

1.  Pengguna mengetik di kolom pencarian (`#search-input`).
2.  JavaScript mendeteksi input dan mengirimkan permintaan `fetch` ke `controllers/search.php` dengan query pencarian sebagai parameter URL (`?q=...`).
3.  `controllers/search.php` menerima query, mencari di database untuk `term` atau `definition` yang cocok (menggunakan `LIKE`), dan mengembalikan hasilnya sebagai JSON.
4.  JavaScript menerima respons JSON dan menampilkan hasilnya di bawah kotak pencarian.

### Halaman Detail (`detail.php`)

1.  Pengguna mengklik sebuah entri dari halaman utama atau hasil pencarian, yang mengarah ke `detail.php?id={id_entri}`.
2.  Skrip mengambil `id` dari URL.
3.  Ia melakukan query ke tabel `entri` (dengan `JOIN` ke tabel `sumber`) untuk mendapatkan semua detail entri.
4.  Ia juga melakukan query ke tabel `contoh_penggunaan` untuk mendapatkan semua contoh yang terkait.
5.  Halaman dirender untuk menampilkan semua informasi ini.

### Sistem Favorit (`assets/js/script.js` & `controllers/get_favorites.php`)

1.  **Penyimpanan**: ID entri favorit disimpan di `localStorage` browser pengguna.
2.  **Menambah/Menghapus Favorit**: Saat pengguna mengklik ikon bintang di halaman detail (`detail.php`), JavaScript menambahkan atau menghapus ID entri dari `localStorage`.
3.  **Menampilkan Favorit**:
    - Di halaman utama (`index.php`), JavaScript mengambil daftar ID dari `localStorage`.
    - Ia mengirimkan daftar ID ini ke `controllers/get_favorites.php` melalui parameter URL (`?ids=[...]`).
    - `controllers/get_favorites.php` mengambil data entri (ID dan `term`) dari database berdasarkan ID yang diterima.
    - Hasilnya dikembalikan sebagai JSON, dan JavaScript menampilkannya di daftar favorit.

### Alur CRUD (`admin/`)

1.  **Read**: `admin/index.php` menampilkan daftar semua entri dengan link untuk melihat (`detail.php`), mengedit (`edit.php`), dan menghapus (`hapus.php`).
2.  **Create**:
    - Pengguna mengklik "Tambah Entri Baru", yang mengarah ke `admin/tambah.php`.
    - `tambah.php` menampilkan form.
    - Saat form disubmit (method `POST`), data dikirim ke `tambah.php` itu sendiri, yang kemudian memproses dan menyimpan data ke database. Setelah itu, pengguna diarahkan kembali ke `admin/index.php`.
3.  **Update**:
    - Pengguna mengklik tombol edit, yang mengarah ke `admin/edit.php?id={id_entri}`.
    - `edit.php` mengambil data entri yang ada dan menampilkannya dalam form.
    - Saat form disubmit, `edit.php` memperbarui data di database dan mengarahkan pengguna kembali ke `admin/index.php`.
4.  **Delete**:
    - Pengguna mengklik tombol hapus, yang mengarah ke `admin/hapus.php?id={id_entri}` (setelah konfirmasi JavaScript).
    - `hapus.php` menghapus entri dari database dan mengarahkan pengguna kembali ke `admin/index.php`.

## Hubungan Antar Tabel (Database)

Struktur database dirancang dengan relasi antar tabel untuk menjaga integritas data. Berikut adalah penjelasannya:

1.  **Relasi `sumber` ke `entri` (Satu-ke-Banyak / One-to-Many)**
    *   Satu baris di tabel `sumber` dapat menjadi sumber bagi banyak baris di tabel `entri`.
    *   Ini diimplementasikan dengan kolom `sumber_id` di tabel `entri` yang berfungsi sebagai *Foreign Key* yang merujuk ke kolom `id` (Primary Key) di tabel `sumber`.
    *   Artinya, setiap entri kamus bisa dilacak asalnya dari sumber mana, namun satu sumber (misal: sebuah buku) bisa berisi banyak entri.

2.  **Relasi `entri` ke `contoh_penggunaan` (Satu-ke-Banyak / One-to-Many)**
    *   Satu baris di tabel `entri` dapat memiliki banyak contoh penggunaan yang tersimpan di tabel `contoh_penggunaan`.
    *   Ini diimplementasikan dengan kolom `entri_id` di tabel `contoh_penggunaan` yang berfungsi sebagai *Foreign Key* yang merujuk ke kolom `id` (Primary Key) di tabel `entri`.
    *   Ini memungkinkan sebuah kata atau istilah memiliki beberapa kalimat contoh untuk memperjelas maknanya. Jika sebuah `entri` dihapus, semua `contoh_penggunaan` yang terkait juga akan terhapus (`ON DELETE CASCADE`).

## Logika Live Search (AJAX)

Fitur pencarian "live" atau instan di halaman utama bekerja tanpa perlu me-reload seluruh halaman. Ini dicapai dengan menggunakan teknologi AJAX (Asynchronous JavaScript and XML). Berikut adalah alur logikanya secara rinci:

1.  **Pemicu di Sisi Klien (Frontend - `index.php` & `assets/js/script.js`)**
    *   Pengguna mengetik sebuah karakter di dalam kotak input dengan ID `#search-input`.
    *   Sebuah *event listener* `keyup` di file `assets/js/script.js` mendeteksi perubahan ini.

2.  **Pengiriman Permintaan Asinkron (AJAX Request)**
    *   Fungsi JavaScript mengambil teks yang sedang diketik oleh pengguna.
    *   Jika ada teks, JavaScript secara otomatis menyembunyikan daftar entri utama dan mengirimkan permintaan (`request`) HTTP `GET` ke server di belakang layar (asinkron).
    *   Target dari permintaan ini adalah file `controllers/search.php`, dengan teks pencarian dilampirkan sebagai *query parameter*. Contoh: `controllers/search.php?q=jawa`.

3.  **Pemrosesan di Sisi Server (Backend - `controllers/search.php`)**
    *   File `search.php` menerima parameter `q` dari URL.
    *   Skrip ini kemudian menjalankan query SQL ke database pada tabel `entri`.
    *   Query tersebut menggunakan klausa `LIKE` untuk mencari kecocokan teks pada kolom `term` (kata) dan `definition` (definisi). Querynya mencari kata yang *diawali* dengan input pengguna (`LIKE 'input%'`) dan dibatasi hanya 5 hasil (`LIMIT 5`) untuk efisiensi.
    *   Hasil query dari database (berupa array data) diubah formatnya menjadi **JSON** (JavaScript Object Notation) menggunakan fungsi `json_encode()`.
    *   Server kemudian mengirimkan data JSON ini sebagai respons (balasan) atas permintaan AJAX tadi.

4.  **Menampilkan Hasil (Frontend - `assets/js/script.js`)**
    *   JavaScript yang tadi mengirim permintaan, kini menerima respons JSON dari server.
    *   Data JSON tersebut di-parsing, dan untuk setiap objek di dalamnya (yang merepresentasikan satu entri), JavaScript secara dinamis membuat elemen HTML baru (elemen `<a>` di dalam sebuah `<div>`).
    *   Elemen-elemen HTML baru ini kemudian disisipkan ke dalam `<div>` kontainer hasil pencarian (`#search-results-container`) di halaman `index.php`, sehingga pengguna dapat melihat hasilnya secara langsung di bawah kotak pencarian.
    *   Jika respons dari server kosong (tidak ada hasil), JavaScript akan menampilkan pesan "Tidak ada hasil ditemukan."

Dengan alur ini, interaksi terasa lebih cepat dan responsif karena komunikasi dengan server terjadi di latar belakang tanpa mengganggu atau memuat ulang tampilan halaman yang sedang dilihat pengguna.

## Penggunaan Session dan Cookies

Aplikasi ini menggunakan Session dan Cookies untuk dua tujuan yang berbeda dalam mengelola data pengguna.

### 1. Penggunaan Session (Data Sisi Server)

Session digunakan untuk menyimpan informasi sementara yang spesifik untuk satu sesi kunjungan pengguna. Data ini disimpan di server.

- **Lokasi Utama**: `index.php`, `controllers/cek_kuis.php`, `controllers/reset_kuis.php`.
- **Kegunaan**:
    - **Kata Hari Ini**: `$_SESSION['wotd_id']` menyimpan ID dari "Kata Hari Ini" agar entri yang sama ditampilkan kepada pengguna selama mereka tidak menutup browser atau sesinya berakhir.
    - **Kuis Harian**: Aplikasi menyimpan semua status kuis di dalam session (`$_SESSION['kuis_jawaban']`, `$_SESSION['kuis_pilihan']`, `$_SESSION['kuis_selesai']`). Ini memastikan bahwa pengguna hanya bisa menjawab kuis sekali per sesi dan state kuis (pertanyaan dan pilihan) tetap konsisten saat halaman dimuat ulang.
    - **Notifikasi**: `$_SESSION['notif_kuis']` digunakan untuk menampilkan pesan (seperti "Jawaban benar!" atau "Jawaban salah") setelah pengguna menjawab kuis. Pesan ini ditampilkan sekali lalu dihapus dari session.

### 2. Penggunaan Cookies (Data Sisi Klien)

Cookies digunakan untuk menyimpan data di browser pengguna, yang dapat bertahan lebih lama dari satu sesi kunjungan.

- **Lokasi Utama**: `assets/js/script.js`
- **Kegunaan**:
    - **Sistem Favorit**: Cookie dengan nama `favorites` digunakan untuk menyimpan array dari ID entri yang telah ditandai sebagai favorit oleh pengguna.
    - **Persistensi**: Karena disimpan dalam cookie, daftar favorit pengguna akan tetap ada bahkan setelah mereka menutup browser dan membukanya kembali, memberikan pengalaman yang personal.
    - **Manajemen**: Fungsi `setCookie()` dan `getCookie()` di `script.js` bertanggung jawab untuk menulis dan membaca data favorit ini dari cookie.

---

## Logika Detail Sisi Klien (`assets/js/script.js`)

File `assets/js/script.js` adalah pusat dari semua interaktivitas di sisi klien. Seluruh skrip dibungkus dalam event listener `DOMContentLoaded`, yang memastikan kode baru berjalan setelah seluruh halaman HTML selesai dimuat.

### 1. Pencarian Instan (Live Search)

- **Event Trigger**: Kode ini memasang event listener `keyup` pada input `#search-input`. Setiap kali pengguna menekan dan melepaskan tombol keyboard, fungsi pencarian akan dijalankan.
- **Validasi & UI**: Jika input kosong, kontainer hasil pencarian (`#search-results-container`) akan dikosongkan dan daftar entri utama (`#main-entry-list`) akan ditampilkan kembali. Jika tidak, daftar utama disembunyikan untuk memberi ruang bagi hasil pencarian.
- **AJAX `fetch`**: Permintaan `fetch` dikirim ke `controllers/search.php` dengan query pengguna. Ini adalah permintaan asinkron, artinya browser tidak "freeze" saat menunggu respons server.
- **Render Hasil**: Setelah respons diterima dalam format JSON, skrip akan mengulang (loop) setiap hasil, membuat elemen HTML `<a>` untuk setiap item, dan menyisipkannya ke dalam `#search-results-container`. Jika tidak ada hasil, pesan "Tidak ada hasil ditemukan" akan ditampilkan.
- **Menyembunyikan Hasil**: Ada event listener tambahan pada `document` yang mendeteksi klik. Jika pengguna mengklik di mana pun di luar `#search-input`, hasil pencarian akan disembunyikan.

### 2. Sistem Favorit Berbasis Cookie

Sistem ini memungkinkan pengguna menyimpan entri favorit mereka di browser.

- **Fungsi Helper**:
    - `getCookie(name)`: Fungsi ini membaca semua cookie yang ada, memisahkannya, dan mencari cookie dengan nama yang diberikan. Jika ditemukan, nilai cookie (yang berupa string JSON) di-parse dan dikembalikan sebagai array.
    - `setCookie(name, value, days)`: Fungsi ini mengubah array (misalnya, array ID favorit) menjadi string JSON dan menyimpannya ke dalam `document.cookie` dengan tanggal kedaluwarsa yang ditentukan.
- **Logika Utama**:
    - `toggleFavorite(id)`: Saat tombol bintang diklik, fungsi ini mengambil array ID favorit dari cookie, lalu menambahkan ID baru jika belum ada, atau menghapus ID jika sudah ada. Setelah itu, ia menyimpan kembali array yang sudah diperbarui ke dalam cookie.
    - `updateFavoriteIcons()`: Fungsi ini memastikan ikon bintang di seluruh halaman (misalnya di daftar dan di halaman detail) secara visual mencerminkan status favorit saat ini (bintang terisi atau kosong).
    - `loadFavoritesList()`: Fungsi ini berjalan saat halaman dimuat. Ia mengambil daftar ID favorit dari cookie, lalu mengirimkannya ke `controllers/get_favorites.php` via `fetch`. Kontroler ini mengembalikan detail entri (nama term), yang kemudian ditampilkan di sidebar "Favorit Saya".
- **Event Listener Global**: Sebuah event listener dipasang di `document.body` yang "mendengarkan" klik. Jika elemen yang diklik memiliki kelas `.favorite-btn`, maka fungsi `toggleFavorite` akan dipicu. Ini adalah teknik yang efisien (disebut *event delegation*) yang memungkinkan tombol favorit yang ditambahkan secara dinamis (seperti dari hasil pencarian) tetap berfungsi.
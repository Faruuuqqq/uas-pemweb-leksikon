
# Penjelasan Proyek Leksikon

Proyek ini adalah aplikasi web Leksikon yang dibangun menggunakan framework CodeIgniter 4.

## Struktur Folder

Berikut adalah penjelasan singkat mengenai struktur folder utama dalam proyek ini:

-   `.env`: File konfigurasi environment untuk aplikasi. Di sinilah Anda mengatur koneksi database, base URL, dan variabel environment lainnya.
-   `admin/`: Folder ini tampaknya berisi file-file PHP lama untuk fungsi admin (tambah, edit, hapus) yang mungkin sudah tidak digunakan lagi karena fungsionalitasnya telah dipindahkan ke dalam arsitektur CodeIgniter (MVC).
-   `archived/`: Berisi file-file lama seperti data JSON, file impor SQL, dan skrip PHP yang digunakan untuk seeding database.
-   `assets/`: Berisi file-file statis seperti CSS dan JavaScript.
-   `codeigniter/`: Ini adalah direktori inti dari framework CodeIgniter.
    -   `app/`: Direktori utama aplikasi Anda (arsitektur MVC).
        -   `Config/`: Berisi file-file konfigurasi untuk aplikasi, rute, database, dll.
        -   `Controllers/`: Mengatur logika untuk merespons request dari pengguna.
        -   `Models/`: Berinteraksi dengan database untuk mengambil, mengubah, atau menyimpan data.
        -   `Views/`: Berisi file-file HTML/PHP yang akan ditampilkan kepada pengguna.
    -   `public/`: Merupakan *web root* dari aplikasi. Semua request browser akan masuk melalui `index.php` di sini.
    -   `writable/`: Direktori tempat CodeIgniter menyimpan file-file yang perlu ditulis, seperti cache, log, dan sesi.
-   `database/`: Berisi file SQL dump untuk struktur dan data database (`database.sql`).

## Instalasi & Setup

1.  **Clone Repository**:
    ```bash
    git clone <url_repository>
    cd uts-pemweb-leksikon
    ```

2.  **Install Dependencies**:
    Pastikan Anda memiliki [Composer](https://getcomposer.org/). Buka direktori `codeigniter` dan jalankan:
    ```bash
    cd codeigniter
    composer install
    ```

3.  **Konfigurasi Environment**:
    - Salin file `env` (tanpa titik di depan) di dalam direktori `codeigniter` dan ubah namanya menjadi `.env`.
    - Buka file `.env` dan sesuaikan konfigurasinya:
        - Atur `app.baseURL` sesuai dengan URL aplikasi Anda (contoh: `http://localhost:8080`).
        - Atur `database.default.hostname`, `database.default.database`, `database.default.username`, dan `database.default.password` sesuai dengan konfigurasi database Anda.
        - Pastikan untuk menghapus tanda `#` di depan baris-baris tersebut untuk mengaktifkannya.

4.  **Database**:
    - Buat sebuah database baru di MySQL/MariaDB.
    - Impor file `database/database.sql` ke dalam database yang baru Anda buat. Ini akan membuat tabel-tabel yang diperlukan dan mengisi data awalnya.

## Cara Menjalankan Aplikasi

Ada dua cara untuk menjalankan aplikasi ini:

### 1. Menggunakan Web Server (XAMPP, AMPPS, dll.)
   - Arahkan *Document Root* dari web server Anda ke direktori `public` di dalam folder `codeigniter`.
   - Buka browser dan akses URL yang telah Anda konfigurasi (misalnya: `http://localhost/uts-pemweb-leksikon/codeigniter/public`).

### 2. Menggunakan Server Bawaan CodeIgniter (Development)
   - Buka terminal atau command prompt.
   - Masuk ke direktori `codeigniter`:
     ```bash
     cd codeigniter
     ```
   - Jalankan perintah berikut untuk memulai server development:
     ```bash
     php spark serve
     ```
   - Aplikasi akan berjalan secara default di `http://localhost:8080`.

Dengan mengikuti langkah-langkah di atas, aplikasi Leksikon seharusnya sudah bisa berjalan di lingkungan lokal Anda.

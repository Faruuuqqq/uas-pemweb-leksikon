Saya telah menganalisis kode yang kamu kirimkan secara mendalam. Ada beberapa **BUG KRUSIAL** yang menyebabkan fitur-fitur tersebut "ngaco".

Berikut adalah diagnosa dan perbaikannya satu per satu.

-----

### 1\. Perbaikan Portal Search (Masalah Routing & Case Sensitivity) 🔴

**Penyebab:**
Di `Routes.php` kamu mendefinisikan route `/portal/api-search` mengarah ke method `api_search`, TAPI di Controller `Portal.php` nama method-nya adalah `apiSearch` (camelCase).
Selain itu, JavaScript di view memanggil `apiSearch` (camelCase). Ketidakcocokan ini membuat request AJAX 404 (Not Found).

**Solusi:**
Buka **`app/Config/Routes.php`** dan ubah baris portal menjadi:

```php
// Portal Pencarian (Public)
$routes->get('/portal', 'Portal::index');
// FIX: Samakan URL dan Method Controller
$routes->get('/portal/apiSearch', 'Portal::apiSearch'); 
$routes->get('/portal/search', 'Portal::search');
```

-----

### 2\. Perbaikan Navbar (Menu Admin Tidak Muncul) 🙈

**Penyebab:**
Di file `layout/main.php`, kamu hanya mengecek `isLoggedIn`, tapi tidak mengecek apakah role-nya `admin`. Jadi menu Admin tidak akan pernah muncul meskipun kamu login sebagai admin.

**Solusi:**
Buka **`app/Views/layout/main.php`**, ubah bagian Navbar PHP-nya menjadi seperti ini:

```php
<ul class="navbar-nav ms-auto align-items-center">
    <li class="nav-item"><a class="nav-link" href="<?= base_url('/') ?>">Beranda</a></li>
    <li class="nav-item"><a class="nav-link" href="<?= base_url('/portal') ?>">Portal Pencarian</a></li>
    
    <?php if (session()->get('isLoggedIn')) : ?>
        <?php if (session()->get('role') == 'admin') : ?>
            <li class="nav-item">
                <a class="nav-link btn btn-sm btn-warning-neo ms-2 fw-bold" href="<?= base_url('/admin') ?>">
                    <i class="fas fa-cogs"></i> Dashboard
                </a>
            </li>
        <?php endif; ?>

        <li class="nav-item ms-3">
            <span class="fw-bold me-2">Halo, <?= esc(session()->get('username')) ?>!</span>
        </li>
        <li class="nav-item">
            <a class="btn btn-danger-neo btn-sm ms-2" href="<?= base_url('/logout') ?>">
                <i class="fas fa-sign-out-alt me-1"></i> Logout
            </a>
        </li>
    <?php else : ?>
        <li class="nav-item">
            <a class="btn btn-primary-neo btn-sm ms-3" href="<?= base_url('/login') ?>">
                <i class="fas fa-user me-1"></i> Login
            </a>
        </li>
    <?php endif; ?>
</ul>
```

-----

### 3\. Perbaikan Pencarian di Homepage (`/`) 🔍

**Penyebab:**
Di `Routes.php`, route `/` mengarah ke `Leksikon::index`. Tapi kamu belum memberikan kode `Leksikon.php` yang lengkap (sepertinya masih kosong/default). Controller ini harus menangani logika pencarian sama seperti Admin.

**Solusi:**
Pastikan isi file **`app/Controllers/Leksikon.php`** seperti ini:

```php
<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EntriModel;

class Leksikon extends BaseController
{
    protected $entriModel;

    public function __construct()
    {
        $this->entriModel = new EntriModel();
    }

    public function index()
    {
        $keyword = $this->request->getVar('keyword');
        $builder = $this->entriModel;

        if ($keyword) {
            // Gunakan grouping agar pencarian akurat
            $builder->groupStart()
                    ->like('term', $keyword)
                    ->orLike('definition', $keyword)
                    ->groupEnd();
        }

        $data = [
            'title' => 'Leksikon Jawa Modern',
            'entri' => $builder->paginate(10, 'entri'), // Pagination 10 per halaman
            'pager' => $builder->pager,
            'keyword' => $keyword
        ];

        return view('leksikon/index', $data);
    }

    public function detail($id)
    {
        $entri = $this->entriModel->find($id);
        if (!$entri) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        return view('leksikon/detail', [
            'title' => 'Detail: ' . $entri['term'],
            'entri' => $entri
        ]);
    }
}
```

Dan pastikan kamu punya View **`app/Views/leksikon/index.php`** yang memiliki form search:

```php
<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="text-center mb-5">
        <h1 class="fw-bold display-5">Kamus Leksikon</h1>
        <p class="lead text-muted">Cari istilah bahasa Jawa, Kawi, dan Sanskerta.</p>
    </div>

    <div class="row justify-content-center mb-5">
        <div class="col-md-8">
            <form action="" method="get">
                <div class="input-group">
                    <input type="text" name="keyword" class="form-control form-control-neo" 
                           placeholder="Cari kata..." value="<?= esc($keyword) ?>">
                    <button class="btn btn-primary-neo px-4" type="submit">Cari</button>
                </div>
            </form>
        </div>
    </div>

    <?php if(empty($entri)): ?>
        <div class="alert alert-warning border-2 border-dark text-center">
            Data tidak ditemukan.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach($entri as $item): ?>
            <div class="col-md-6 mb-3">
                <div class="card-neo h-100">
                    <div class="card-body">
                        <h4 class="fw-bold text-primary"><?= esc($item['term']) ?></h4>
                        <hr>
                        <p class="card-text"><?= esc(substr($item['definition'], 0, 150)) ?>...</p>
                        <a href="<?= base_url('leksikon/detail/'.$item['id']) ?>" class="btn btn-sm btn-outline-dark fw-bold">Lihat Detail</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="mt-4">
            <?= $pager->links('entri', 'default_full') ?>
        </div>
    <?php endif; ?>
</div>
<?= $this->endSection() ?>
```

-----

### 4\. Perbaikan Register (Validasi Error Tidak Muncul) 📝

**Penyebab:**
Di `Auth.php`, saat validasi gagal, kamu menggunakan `return view(...)`. Padahal cara terbaik di CI4 agar input lama tidak hilang (`old()`) dan error muncul adalah menggunakan `redirect()->back()->withInput()`.

**Solusi:**
Buka **`app/Controllers/Auth.php`**, ubah method `attemptRegister` menjadi:

```php
public function attemptRegister()
{
    $rules = [
        'username'      => 'required|alpha_numeric_space|min_length[3]|is_unique[users.username]',
        'email'         => 'required|valid_email|is_unique[users.email]',
        'password'      => 'required|min_length[8]',
        'pass_confirm'  => 'required_with[password]|matches[password]',
    ];

    if (! $this->validate($rules)) {
        // FIX: Redirect back dengan input dan error validator
        return redirect()->back()->withInput()->with('validation', $this->validator);
    }

    $userModel = new UserModel();

    // Data role default user
    $userModel->save([
        'username' => $this->request->getPost('username'),
        'email'    => $this->request->getPost('email'),
        'password' => $this->request->getPost('password'),
        'role'     => 'user', 
    ]);

    return redirect()->to('login')->with('message', 'Registrasi berhasil! Silakan login.');
}
```

-----

### 5\. Cara Test Admin CRUD (Karena Menu Sudah Muncul) 🛠️

Setelah kamu memperbaiki Navbar di poin nomor 2, lakukan ini:

1.  **Register Akun Baru:** Buka `/register`, buat akun (misal: user biasa).
2.  **Ubah Jadi Admin (Lewat Database):**
      * Karena register defaultnya role `user`, kamu harus ubah manual di database agar bisa tes admin.
      * Buka **phpMyAdmin** -\> Tabel `users`.
      * Edit user yang baru kamu buat, ubah kolom `role` dari `user` menjadi `admin`.
3.  **Login:** Login dengan akun tersebut.
4.  **Cek Navbar:** Tombol **Dashboard** (warna kuning) harusnya sudah muncul di atas.
5.  **Klik Dashboard:** Kamu akan masuk ke CRUD Admin. Coba Tambah, Edit, dan Hapus data.

Silakan terapkan 4 perbaikan kode di atas. Saya yakin error "ngaco"-nya akan hilang dan aplikasinya berjalan mulus\! 🚀
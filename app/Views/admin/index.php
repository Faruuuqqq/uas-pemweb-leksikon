<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Dashboard Admin</h2>
        <a href="<?= base_url('admin/entri/create') ?>" class="btn btn-primary-neo btn-neo">
            <i class="fas fa-plus"></i> Tambah Data
        </a>
    </div>

    <!-- STATISTIK -->
    <div class="row mb-4">
        <!-- Card 1: Total Istilah -->
        <div class="col-md-3">
            <div class="card card-neo border-0 bg-primary text-white mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1" style="font-size: 0.8rem; opacity: 0.8;">Total Istilah</h6>
                            <h2 class="fw-bold mb-0"><?= number_format($stats['total_entri']) ?></h2>
                        </div>
                        <i class="fas fa-book fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Total Sumber -->
        <div class="col-md-3">
            <div class="card card-neo border-0 bg-success text-white mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1" style="font-size: 0.8rem; opacity: 0.8;">Total Sumber</h6>
                            <h2 class="fw-bold mb-0"><?= number_format($stats['total_sumber']) ?></h2>
                        </div>
                        <i class="fas fa-database fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: User Terdaftar -->
        <div class="col-md-3">
            <div class="card card-neo border-0 bg-warning text-dark mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1" style="font-size: 0.8rem; opacity: 0.8;">User Terdaftar</h6>
                            <h2 class="fw-bold mb-0"><?= number_format($stats['total_user']) ?></h2>
                        </div>
                        <i class="fas fa-users fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Total Disukai -->
        <div class="col-md-3">
            <div class="card card-neo border-0 bg-danger text-white mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1" style="font-size: 0.8rem; opacity: 0.8;">Total Disukai</h6>
                            <h2 class="fw-bold mb-0"><?= number_format($stats['total_fav']) ?></h2>
                        </div>
                        <i class="fas fa-heart fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTER & PENCARIAN -->
    <div class="card-neo mb-4 p-4 bg-light">
        <form action="" method="get" class="row g-3">
            
            <!-- Pencarian Keyword -->
            <div class="col-md-4">
                <label class="form-label fw-bold small">Cari Kata/Definisi</label>
                <div class="input-group">
                    <input type="text" class="form-control form-control-neo" placeholder="Ketik sesuatu..." name="keyword" value="<?= esc($keyword) ?>">
                    <button class="btn btn-primary-neo btn-neo" type="submit"><i class="fas fa-search"></i></button>
                </div>
            </div>

            <!-- Filter Sumber -->
            <div class="col-md-3">
                <label class="form-label fw-bold small">Filter Sumber</label>
                <select class="form-select form-control-neo" name="sumber" onchange="this.form.submit()">
                    <option value="">-- Semua Sumber --</option>
                    <?php foreach($sumber_list as $src): ?>
                        <option value="<?= $src['id'] ?>" <?= ($selected_sumber == $src['id']) ? 'selected' : '' ?>>
                            <?= esc($src['nama_sumber']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Sorting -->
            <div class="col-md-3">
                <label class="form-label fw-bold small">Urutkan Berdasarkan</label>
                <select class="form-select form-control-neo" name="sort_by" onchange="this.form.submit()">
                    <option value="id" <?= ($sort_by == 'id') ? 'selected' : '' ?>>ID (Default)</option>
                    <option value="term" <?= ($sort_by == 'term') ? 'selected' : '' ?>>Abjad Istilah</option>
                    <option value="created_at" <?= ($sort_by == 'created_at') ? 'selected' : '' ?>>Tanggal Dibuat</option>
                    <option value="updated_at" <?= ($sort_by == 'updated_at') ? 'selected' : '' ?>>Tanggal Update</option>
                </select>
            </div>

            <!-- Sort Order -->
            <div class="col-md-2">
                <label class="form-label fw-bold small">Urutan</label>
                <select class="form-select form-control-neo" name="sort_order" onchange="this.form.submit()">
                    <option value="ASC" <?= ($sort_order == 'ASC') ? 'selected' : '' ?>>A-Z (Naik)</option>
                    <option value="DESC" <?= ($sort_order == 'DESC') ? 'selected' : '' ?>>Z-A (Turun)</option>
                </select>
            </div>

        </form>
    </div>

    <?php if (session()->getFlashdata('message')): ?>
        <div class="alert alert-success border-2 border-dark fw-bold">
            <?= session()->getFlashdata('message') ?>
        </div>
    <?php endif; ?>

    <div class="card-neo p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-dark text-white">
                    <tr>
                        <th class="p-3">Istilah</th>
                        <th class="p-3">Definisi</th>
                        <th class="p-3">Sumber</th>
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($entri)): ?>
                        <tr>
                            <td colspan="4" class="text-center p-5 text-muted">Tidak ada data ditemukan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($entri as $item): ?>
                        <tr>
                            <td class="p-3 fw-bold"><?= esc($item['term']) ?></td>
                            <td class="p-3"><?= substr(esc($item['definition']), 0, 80) ?>...</td>
                            <td class="p-3">
                                <span class="badge bg-light text-dark border border-dark">
                                    <?= esc($item['nama_sumber'] ?? '-') ?>
                                </span>
                            </td>
                            <td class="p-3">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="<?= base_url('admin/entri/edit/'.$item['id']) ?>" 
                                    class="btn btn-sm btn-warning-neo btn-neo" 
                                    title="Edit Data">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form action="<?= base_url('admin/entri/delete/'.$item['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Yakin hapus?');">
                                        <?= csrf_field() ?> 
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-sm btn-danger-neo btn-neo" title="Hapus Data">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4 d-flex justify-content-center">
        <?= $pager->links('default', 'default_full') ?>
    </div>

</div>
<?= $this->endSection() ?>
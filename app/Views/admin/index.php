<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Dashboard Admin</h2>
        <a href="<?= base_url('admin/entri/create') ?>" class="btn btn-primary-neo btn-neo">
            <i class="fas fa-plus"></i> Tambah Data
        </a>
    </div>

    <div class="row mb-4">
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
    <div class="card-neo mb-4 p-3 bg-light">
        <form action="" method="get">
            <div class="input-group">
                <input type="text" class="form-control form-control-neo" placeholder="Cari kata..." name="keyword" value="<?= esc($keyword) ?>">
                <button class="btn btn-primary-neo btn-neo" type="submit">Cari</button>
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
                        <th class="p-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($entri as $item): ?>
                    <tr>
                        <td class="p-3 fw-bold"><?= esc($item['term']) ?></td>
                        <td class="p-3"><?= substr(esc($item['definition']), 0, 100) ?>...</td>
                        <td class="p-3 text-center" style="width: 150px;">
                            <a href="<?= base_url('admin/entri/edit/'.$item['id']) ?>" class="btn btn-sm btn-warning-neo btn-neo me-1">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <form action="<?= base_url('admin/entri/delete/'.$item['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Yakin hapus?');">
                                <?= csrf_field() ?> <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="btn btn-sm btn-danger-neo btn-neo">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-4">
        <?= $pager->links('entri', 'default_full') ?>
    </div>

</div>
<?= $this->endSection() ?>
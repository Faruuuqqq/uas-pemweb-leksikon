<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Manajemen Data Leksikon</h2>
        <a href="<?= base_url('admin/entri/create') ?>" class="btn btn-primary-neo btn-neo">
            <i class="fas fa-plus"></i> Tambah Data
        </a>
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
                <thead class="bg-primary text-white">
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
                            
                            <form action="<?= base_url('admin/entri/delete/'.$item['id']) ?>" method="post" class="d-inline" onsubmit="Swal.fire({
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
}); return false;">
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
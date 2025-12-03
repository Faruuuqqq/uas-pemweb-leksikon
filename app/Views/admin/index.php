<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="row mb-5 align-items-center">
        <div class="col-md-8">
            <h1 class="display-5 fw-bold" style="color: var(--primary-color);">Dashboard Admin</h1>
            <p class="lead text-muted">Manajemen Data dan Statistik Leksikon.</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="<?= base_url('/admin/new') ?>" class="btn btn-neo btn-primary-neo">
                <i class="fas fa-plus-circle me-2"></i> Tambah Data Baru
            </a>
        </div>
    </div>

    <?php if (session()->getFlashdata('message')) : ?>
        <div class="alert alert-success border-2 border-dark shadow-sm fw-bold mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?= session()->getFlashdata('message') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger border-2 border-dark shadow-sm fw-bold mb-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i> <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <!-- Insights Section -->
    <div class="row mb-5">
        <div class="col-md-4 mb-4">
            <div class="card-neo text-center p-4 h-100 bg-primary-neo text-white">
                <h3 class="fw-bold">Total Entri</h3>
                <p class="display-4 fw-bolder"><?= $totalEntries ?></p>
                <p class="mb-0">Data leksikon terdaftar</p>
            </div>
        </div>
        <div class="col-md-8 mb-4">
            <div class="card-neo p-4 h-100">
                <h3 class="fw-bold text-primary">Entri per Sumber</h3>
                <canvas id="entriesPerSourceChart" style="max-height: 250px;"></canvas>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold">Manajemen Entri</h2>
        
        <form action="" method="get" class="d-flex gap-2">
            <input type="text" class="form-control border-2 border-dark" 
                   placeholder="Cari data..." name="keyword" 
                   value="<?= esc($keyword ?? '') ?>" style="width: 250px;">
            <button class="btn btn-warning border-2 border-dark fw-bold" type="submit">
                Cari
            </button>
            <?php if ($keyword) : ?>
                <a href="<?= base_url('/admin') ?>" class="btn btn-secondary border-2 border-dark">Reset</a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Entri Table -->
    <div class="card-neo">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" style="border-color: var(--border-color);">
                    <thead style="background-color: #f1f1f1; border-bottom: 3px solid var(--border-color);">
                        <tr>
                            <th class="p-4">Istilah (Term)</th>
                            <th class="p-4">Definisi</th>
                            <th class="p-4">Sumber</th>
                            <th class="p-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($entri)) : ?>
                            <?php foreach ($entri as $item) : ?>
                            <tr style="border-bottom: 1px solid #ddd;">
                                <td class="p-4 fw-bold" style="color: var(--primary-color);"><?= esc($item['term']) ?></td>
                                <td class="p-4 text-muted"><?= esc(substr($item['definition'], 0, 100)) ?>...</td>
                                <td class="p-4">
                                    <span class="badge badge-neo bg-warning text-dark">
                                        <?= esc($item['nama_sumber'] ?? 'Umum') ?>
                                    </span>
                                </td>
                                <td class="p-4 text-center">
                                    <a href="<?= base_url('leksikon/detail/' . $item['id']) ?>" class="btn btn-sm btn-neo btn-warning-neo me-1" title="Detail"><i class="fas fa-eye"></i></a>
                                    <a href="<?= base_url('admin/edit/' . $item['id']) ?>" class="btn btn-sm btn-neo btn-primary-neo me-1" title="Edit"><i class="fas fa-edit"></i></a>
                                    <form action="<?= base_url('admin/delete/' . $item['id']) ?>" method="post" class="d-inline">
                                        <input type="hidden" name="_method" value="DELETE">
                                        <button type="submit" class="btn btn-sm btn-neo btn-danger-neo" onclick="return confirm('Yakin hapus?')" title="Hapus"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="4" class="text-center p-5">
                                    <h4 class="text-muted">Data tidak ditemukan.</h4>
                                    <p>Coba kata kunci lain atau tambahkan data baru.</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if (isset($pager) && $pager->getPageCount() > 1) : ?>
            <div class="p-4 border-top border-2 border-dark bg-light">
                 <?= $pager->links() ?>
            </div>
        <?php endif; ?>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Chart for Entries per Source
    const ctx = document.getElementById('entriesPerSourceChart');
    if (ctx) {
        const entriesPerSource = <?= json_encode($entriesPerSource) ?>;
        const labels = entriesPerSource.map(item => item.nama_sumber || 'Tidak Ada Sumber');
        const data = entriesPerSource.map(item => item.total_entri_per_sumber);

        new Chart(ctx, {
            type: 'bar', // Can be 'pie', 'doughnut', 'bar'
            data: {
                labels: labels,
                datasets: [{
                    label: 'Jumlah Entri',
                    data: data,
                    backgroundColor: [
                        'rgba(67, 97, 238, 0.8)', // Primary Blue
                        'rgba(255, 202, 58, 0.8)', // Secondary Yellow
                        'rgba(114, 9, 183, 0.8)', // Accent Purple
                        'rgba(239, 71, 111, 0.8)', // Danger Red
                        'rgba(6, 214, 160, 0.8)', // Success Green
                        'rgba(173, 181, 189, 0.8)', // Grey
                    ],
                    borderColor: 'var(--border-color)',
                    borderWidth: 2,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: false,
                        text: 'Distribusi Entri per Sumber'
                    }
                }
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
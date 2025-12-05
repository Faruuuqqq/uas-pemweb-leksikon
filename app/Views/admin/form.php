<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card-neo">
                <div class="card-header-neo">
                    <h4 class="mb-0"><?= $title ?></h4>
                </div>
                <div class="card-body p-4">
                    
                    <?php 
                        $urlAction = isset($entri) ? 'admin/entri/update/'.$entri['id'] : 'admin/entri/store';
                    ?>

                    <form action="<?= base_url($urlAction) ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label class="fw-bold mb-1">Istilah (Term)</label>
                            <input type="text" name="term" class="form-control form-control-neo" 
                                   value="<?= old('term', $entri['term'] ?? '') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold mb-1">Sumber</label>
                            <select name="sumber_id" class="form-select form-control-neo">
                                <?php foreach($sumber as $s): ?>
                                    <option value="<?= $s['id'] ?>" <?= (old('sumber_id', $entri['sumber_id'] ?? '') == $s['id']) ? 'selected' : '' ?>>
                                        <?= $s['nama_sumber'] ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="fw-bold mb-1">Definisi</label>
                            <textarea name="definition" class="form-control form-control-neo" rows="5" required><?= esc(old('definition', $entri['definition'] ?? '')) ?></textarea>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('admin') ?>" class="btn btn-outline-dark fw-bold">Kembali</a>
                            <button type="submit" class="btn btn-primary-neo btn-neo">
                                <i class="fas fa-save me-2"></i> Simpan Data
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
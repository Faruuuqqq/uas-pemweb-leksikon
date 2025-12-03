<?= $this->extend('layout') ?>

<?= $this->section('title') ?>
<?= isset($entri) ? 'Edit Entri' : 'Tambah Entri Baru' ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h3><?= isset($entri) ? 'Edit Entri' : 'Tambah Entri Baru' ?></h3>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?= isset($entri) ? site_url('admin/edit/' . $entri['id']) : site_url('admin/new') ?>">
            <?= csrf_field() ?>
            <!-- Input untuk Term -->
            <div class="mb-3">
                <label for="term" class="form-label">Term (Kata)</label>
                <input type="text" class="form-control" id="term" name="term" required value="<?= old('term', $entri['term'] ?? '') ?>">
                <?php if (session('validation') && session('validation')->hasError('term')): ?>
                    <div class="invalid-feedback d-block">
                        <?= session('validation')->getError('term') ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Dropdown (select) untuk Sumber -->
            <div class="mb-3">
                <label for="sumber_id" class="form-label">Sumber</label>
                <select class="form-select" id="sumber_id" name="sumber_id">
                    <option value="">-- Pilih Sumber --</option>
                    <?php foreach ($sumber_list as $s): ?>
                        <option value="<?= $s['id'] ?>" <?= old('sumber_id', $entri['sumber_id'] ?? '') == $s['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['nama_sumber']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if (session('validation') && session('validation')->hasError('sumber_id')): ?>
                    <div class="invalid-feedback d-block">
                        <?= session('validation')->getError('sumber_id') ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Textarea untuk Definition -->
            <div class="mb-3">
                <label for="definition" class="form-label">Definition (Arti)</label>
                <textarea class="form-control" id="definition" name="definition" rows="5" required><?= old('definition', $entri['definition'] ?? '') ?></textarea>
                <?php if (session('validation') && session('validation')->hasError('definition')): ?>
                    <div class="invalid-feedback d-block">
                        <?= session('validation')->getError('definition') ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Tombol Submit dan Batal -->
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= site_url('admin') ?>" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

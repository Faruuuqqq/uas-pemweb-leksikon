<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container">
    
    <div class="text-center mb-5 py-5" style="background: var(--white); border: 3px solid var(--border-color); border-radius: 20px; box-shadow: var(--shadow-hard);">
        <h1 class="display-4 fw-800 mb-3">Portal Leksikon <span style="color: var(--primary-color);">Dunia</span></h1>
        <p class="lead text-muted mb-4" style="max-width: 600px; margin: 0 auto;">
            Satu pintu untuk mencari istilah Sanskerta, Jawa Kuno, dan Kawi dari 5 sumber terpercaya.
        </p>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <form id="portal-search-form" action="<?= base_url('/portal/search') ?>" method="get">
                    <div class="d-flex gap-2">
                        <select name="source" id="source-select" class="form-select form-control-neo" style="width: 35%;">
                            <option value="all">Semua Sumber</option>
                            <option value="sastra" <?= (isset($source) && $source == 'sastra') ? 'selected' : '' ?>>Sastra.org</option>
                            <option value="ojed" <?= (isset($source) && $source == 'ojed') ? 'selected' : '' ?>>SEAlang OJED</option>
                            <option value="sanskrit" <?= (isset($source) && $source == 'sanskrit') ? 'selected' : '' ?>>Sanskrit Cologne</option>
                            <option value="learnsanskrit" <?= (isset($source) && $source == 'learnsanskrit') ? 'selected' : '' ?>>LearnSanskrit.cc</option>
                            <option value="sealang_lib" <?= (isset($source) && $source == 'sealang_lib') ? 'selected' : '' ?>>SEAlang Library</option>
                        </select>
                        <input type="text" name="keyword" id="keyword-input" class="form-control form-control-neo" placeholder="Ketik kata kunci (misal: raja)..." value="<?= $keyword ?? '' ?>" required>
                        <button type="submit" class="btn btn-neo btn-primary-neo px-4">
                            <i class="fas fa-search me-2"></i>Cari
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- This area will be populated by AJAX -->
    <div id="results-container">
        <!-- Results will be injected here -->
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const keyword = "<?= esc($keyword ?? '') ?>";
    // Daftar sumber yang mau ditembak
    const sources = ['sastra', 'sanskrit', 'learnsanskrit', 'ojed', 'sealang_lib'];
    const container = document.getElementById('results-container');

    if(keyword) {
        sources.forEach(source => {
            // 1. Buat Placeholder Loading untuk tiap sumber
            const placeholder = document.createElement('div');
            placeholder.id = `card-${source}`;
            placeholder.className = 'col-12 mb-3';
            placeholder.innerHTML = `
                <div class="card-neo p-3">
                    <div class="d-flex align-items-center">
                        <div class="spinner-border text-primary me-3" role="status"></div>
                        <h5 class="mb-0">Sedang mengambil data dari <span class="text-uppercase fw-bold">${source}</span>...</h5>
                    </div>
                </div>`;
            container.appendChild(placeholder);

            // 2. Fetch Data via AJAX
            fetch(`<?= base_url('portal/apiSearch') ?>?keyword=${keyword}&source=${source}`, {
                headers: { "X-Requested-With": "XMLHttpRequest" }
            })
            .then(response => response.json())
            .then(data => {
                // 3. Ganti Loading dengan Hasil
                let htmlContent = '';
                if(data.length > 0) {
                    data.forEach(item => {
                        htmlContent += `
                        <div class="col-md-6 mb-4">
                            <div class="card-neo h-100">
                                <div class="card-header-neo d-flex justify-content-between">
                                    <span>${item.term}</span>
                                    <span class="badge bg-warning text-dark border border-dark">${item.source}</span>
                                </div>
                                <div class="card-body">
                                    <p class="card-text">${item.definition}</p>
                                </div>
                            </div>
                        </div>`;
                    });
                } else {
                    htmlContent = `
                    <div class="col-12 mb-3">
                        <div class="alert alert-secondary border-2 border-dark">
                            <i class="fas fa-info-circle me-2"></i>Tidak ditemukan hasil di <b>${source.toUpperCase()}</b>
                        </div>
                    </div>`;
                }
                // Update HTML, hapus placeholder loading
                document.getElementById(`card-${source}`).outerHTML = htmlContent;
            })
            .catch(error => {
                document.getElementById(`card-${source}`).innerHTML = `<div class="alert alert-danger">Gagal memuat ${source}</div>`;
            });
        });
    }
});
</script>
<?= $this->endSection() ?>

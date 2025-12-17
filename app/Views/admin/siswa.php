<?= $this->extend('layout/app') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Siswa</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola data siswa berdasarkan kelas</p>
        </div>
        <div class="flex gap-2">
            <button type="button" id="btnTambah" class="btn btn-primary" disabled>
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah Siswa
            </button>
            <button type="button" id="btnImport" class="btn btn-success">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                </svg>
                Import CSV
            </button>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Siswa</h3>
        </div>
        <div class="card-body">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Pilih Kelas -->
                <div class="form-group">
                    <label for="filter_kelas" class="form-label">Pilih Kelas (Tahun Ajaran Aktif)</label>
                    <select class="form-input" id="filter_kelas" name="id_kelas">
                        <option value="">-- Tampilkan Siswa Berdasarkan Kelas --</option>
                        <?php foreach ($kelas_list as $kelas) : ?>
                            <option value="<?= $kelas['id_kelas'] ?>">
                                <?= esc($kelas['nama_kelas']) ?> (<?= esc($kelas['kode_kelas']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Search -->
                <div class="form-group">
                    <label for="search_keyword" class="form-label">Cari Nama / NIS</label>
                    <div class="relative">
                        <input type="text" 
                               class="form-input pl-10" 
                               id="search_keyword" 
                               name="keyword" 
                               placeholder="Masukkan nama atau NIS...">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table-modern" id="dataTable">
                    <thead>
                        <tr>
                            <th class="w-16">No</th>
                            <th>Nama Siswa</th>
                            <th>NIS</th>
                            <th>Kelas (Tahun Ajaran)</th>
                            <th class="w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="siswa-table-body">
                        <tr>
                            <td colspan="5" class="text-center text-gray-500 py-12">
                                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <p class="font-medium text-gray-600">Silakan pilih kelas terlebih dahulu</p>
                                <p class="text-sm">untuk menampilkan data siswa</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div id="pagination-links" class="flex justify-center py-4 border-t border-gray-200"></div>
        </div>
    </div>
</div>

<!-- Modal CRUD -->
<div id="modalSiswa" class="modal" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-backdrop"></div>
    <div class="modal-container">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalLabel">Form Siswa</h3>
                <button type="button" class="modal-close" data-modal-close aria-label="Close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="formSiswa">
                <div class="modal-body">
                    <div class="alert alert-danger hidden" id="error-alert-manual"></div>

                    <input type="hidden" name="id_siswa" id="id_siswa">
                    
                    <div class="form-group">
                        <label for="id_kelas" class="form-label">
                            Kelas (Tahun Ajaran Aktif) <span class="text-red-500">*</span>
                        </label>
                        <select class="form-input" id="id_kelas" name="id_kelas" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($kelas_aktif as $kelas) : ?>
                                <option value="<?= $kelas['id_kelas'] ?>">
                                    <?= esc($kelas['nama_kelas']) ?> (<?= esc($kelas['kode_kelas']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="nis" class="form-label">NIS (Nomor Induk Siswa)</label>
                        <input type="text" 
                               class="form-input" 
                               id="nis" 
                               name="nis" 
                               placeholder="Kosongkan jika siswa baru">
                        <p class="form-hint">NIS harus unik jika diisi</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="nama_siswa" class="form-label">
                            Nama Siswa <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="form-input" 
                               id="nama_siswa" 
                               name="nama_siswa" 
                               required>
                        <p class="form-hint">Nama akan otomatis diubah ke huruf besar</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanManual">
                        <svg class="w-4 h-4 mr-2 hidden animate-spin" id="btnSpinner" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span id="btnText">Simpan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import CSV -->
<div id="modalImport" class="modal" role="dialog" aria-labelledby="modalImportLabel" aria-hidden="true">
    <div class="modal-backdrop"></div>
    <div class="modal-container">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalImportLabel">Import Siswa dari CSV</h3>
                <button type="button" class="modal-close" data-modal-close aria-label="Close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="formImport">
                <div class="modal-body">
                    <div class="alert alert-danger hidden" id="error-alert-import"></div>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h4 class="font-semibold text-blue-800 mb-2">Petunjuk:</h4>
                                <ol class="list-decimal list-inside text-sm text-blue-700 space-y-1">
                                    <li>
                                        <a href="<?= base_url('template-siswa.csv') ?>" class="font-semibold underline hover:text-blue-900">
                                            Download template CSV
                                        </a>
                                    </li>
                                    <li>Isi data sesuai format: <strong>Nama Siswa, NIS, Kode Kelas</strong></li>
                                    <li>Kolom NIS boleh dikosongkan atau diisi tanda strip (-)</li>
                                    <li>Kode Kelas harus sesuai dengan yang ada di menu "Kelas"</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="file_csv" class="form-label">
                            Upload File CSV <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="file" 
                                   class="form-input file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" 
                                   id="file_csv" 
                                   name="file_csv" 
                                   accept=".csv" 
                                   required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
                    <button type="submit" class="btn btn-success" id="btnUploadImport">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        <span id="btnImportText">Upload dan Import</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Confirm Delete Modal -->
<div id="modalConfirmDelete" class="modal" role="dialog" aria-hidden="true">
    <div class="modal-backdrop"></div>
    <div class="modal-container">
        <div class="modal-content max-w-md">
            <div class="modal-body text-center py-8">
                <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Siswa?</h3>
                <p class="text-gray-600 mb-6">Data siswa (termasuk nilai & absensi) akan dihapus permanen!</p>
                <input type="hidden" id="deleteId">
                <div class="flex items-center justify-center gap-3">
                    <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
                    <button type="button" class="btn btn-danger" id="btnConfirmDelete">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Import Modal -->
<div id="modalImportSuccess" class="modal" role="dialog" aria-hidden="true">
    <div class="modal-backdrop"></div>
    <div class="modal-container">
        <div class="modal-content max-w-md">
            <div class="modal-body text-center py-8">
                <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Import Berhasil!</h3>
                <p class="text-gray-600 mb-6" id="importSuccessMessage"></p>
                <button type="button" class="btn btn-primary" data-modal-close>OK</button>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script type="module">
    // Configuration
    const csrfName = document.querySelector('meta[name="csrf-name"]').content;
    let csrfHash = document.querySelector('meta[name="csrf-token"]').content;
    const baseUrl = document.querySelector('meta[name="base-url"]').content;
    
    // Helper to update CSRF token
    function updateCsrf(newHash) {
        if (newHash) {
            csrfHash = newHash;
            document.querySelector('meta[name="csrf-token"]').content = newHash;
        }
    }
    
    // State
    let searchTimer = null;
    let currentRequest = null;
    
    // Elements
    const btnTambah = document.getElementById('btnTambah');
    const btnImport = document.getElementById('btnImport');
    const filterKelas = document.getElementById('filter_kelas');
    const searchKeyword = document.getElementById('search_keyword');
    const siswaTableBody = document.getElementById('siswa-table-body');
    const paginationLinks = document.getElementById('pagination-links');
    
    const modalSiswa = document.getElementById('modalSiswa');
    const formSiswa = document.getElementById('formSiswa');
    const modalLabel = document.getElementById('modalLabel');
    const errorAlertManual = document.getElementById('error-alert-manual');
    
    const modalImport = document.getElementById('modalImport');
    const formImport = document.getElementById('formImport');
    const errorAlertImport = document.getElementById('error-alert-import');
    
    const modalConfirmDelete = document.getElementById('modalConfirmDelete');
    const deleteIdInput = document.getElementById('deleteId');
    
    const modalImportSuccess = document.getElementById('modalImportSuccess');
    
    // Toast helper
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-[100] px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        toast.innerHTML = `
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    ${type === 'success' 
                        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
                        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
                    }
                </svg>
                <span class="font-medium">${message}</span>
            </div>
        `;
        document.body.appendChild(toast);
        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-full');
            toast.classList.add('translate-x-0');
        });
        setTimeout(() => {
            toast.classList.remove('translate-x-0');
            toast.classList.add('translate-x-full');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    
    // Modal helpers
    function openModal(modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    function closeModal(modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    // Setup modal close handlers
    document.querySelectorAll('.modal').forEach(modal => {
        modal.querySelector('.modal-backdrop')?.addEventListener('click', () => closeModal(modal));
        modal.querySelectorAll('[data-modal-close]').forEach(btn => {
            btn.addEventListener('click', () => closeModal(modal));
        });
    });
    
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal.active').forEach(modal => closeModal(modal));
        }
    });
    
    // Loading rows template
    function getLoadingRow() {
        return `
            <tr>
                <td colspan="5" class="text-center py-12">
                    <svg class="w-8 h-8 mx-auto text-blue-500 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p class="mt-3 text-gray-500">Memuat data...</p>
                </td>
            </tr>
        `;
    }
    
    // Load Siswa via AJAX
    async function loadSiswa(page = 1) {
        const id_kelas = filterKelas.value;
        const keyword = searchKeyword.value;
        
        // Toggle add button
        btnTambah.disabled = !id_kelas;
        
        // Show loading
        siswaTableBody.innerHTML = getLoadingRow();
        paginationLinks.innerHTML = '';
        
        try {
            const params = new URLSearchParams({
                page_siswa: page,
                id_kelas: id_kelas,
                keyword: keyword
            });
            
            const response = await fetch(`${baseUrl}admin/siswa/fetch-by-kelas?${params}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            
            const data = await response.json();
            
            siswaTableBody.innerHTML = data.rows || `
                <tr>
                    <td colspan="5" class="text-center text-gray-500 py-12">
                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <p class="font-medium">Tidak ada data siswa</p>
                    </td>
                </tr>
            `;
            paginationLinks.innerHTML = data.pager || '';
            
            // Re-attach event listeners for new buttons
            attachRowEventListeners();
            
        } catch (error) {
            console.error(error);
            siswaTableBody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-red-500 py-12">
                        <svg class="w-12 h-12 mx-auto text-red-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <p class="font-medium">Gagal memuat data</p>
                        <p class="text-sm">Silakan coba lagi</p>
                    </td>
                </tr>
            `;
        }
    }
    
    // Attach event listeners to dynamically loaded rows
    function attachRowEventListeners() {
        // Edit buttons
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', async () => {
                const id = btn.dataset.id;
                modalLabel.textContent = 'Edit Siswa';
                formSiswa.reset();
                errorAlertManual.classList.add('hidden');
                
                try {
                    const response = await fetch(`${baseUrl}admin/siswa/fetch`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: new URLSearchParams({ id_siswa: id, [csrfName]: csrfHash })
                    });
                    
                    const data = await response.json();
                    updateCsrf(data.csrf_hash);
                    
                    if (data.status === 'success') {
                        document.getElementById('id_siswa').value = data.data.id_siswa;
                        document.getElementById('id_kelas').value = data.data.id_kelas;
                        document.getElementById('nis').value = data.data.nis || '';
                        document.getElementById('nama_siswa').value = data.data.nama_siswa;
                        openModal(modalSiswa);
                    } else {
                        showToast(data.message || 'Gagal mengambil data', 'error');
                    }
                } catch (error) {
                    showToast('Terjadi kesalahan', 'error');
                }
            });
        });
        
        // Delete buttons
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', () => {
                deleteIdInput.value = btn.dataset.id;
                openModal(modalConfirmDelete);
            });
        });
        
        // Pagination links
        paginationLinks.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const url = new URL(link.href);
                const page = url.searchParams.get('page_siswa') || 1;
                loadSiswa(page);
            });
        });
    }
    
    // Filter change
    filterKelas.addEventListener('change', () => loadSiswa(1));
    
    // Search with debounce
    searchKeyword.addEventListener('keyup', () => {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => loadSiswa(1), 500);
    });
    
    // Add Button
    btnTambah.addEventListener('click', () => {
        if (btnTambah.disabled) return;
        
        modalLabel.textContent = 'Tambah Siswa';
        formSiswa.reset();
        document.getElementById('id_siswa').value = '';
        errorAlertManual.classList.add('hidden');
        
        // Pre-select current filter kelas
        if (filterKelas.value) {
            document.getElementById('id_kelas').value = filterKelas.value;
        }
        
        openModal(modalSiswa);
    });
    
    // Form Submit
    formSiswa.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const btnSpinner = document.getElementById('btnSpinner');
        const btnText = document.getElementById('btnText');
        const btnSimpan = document.getElementById('btnSimpanManual');
        
        btnSimpan.disabled = true;
        btnSpinner.classList.remove('hidden');
        btnText.textContent = 'Menyimpan...';
        errorAlertManual.classList.add('hidden');
        
        const formData = new FormData(formSiswa);
        formData.append(csrfName, csrfHash);
        
        try {
            const response = await fetch(`${baseUrl}admin/siswa/save`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            
            const data = await response.json();
            updateCsrf(data.csrf_hash);
            
            if (data.status === 'success') {
                closeModal(modalSiswa);
                showToast(data.message, 'success');
                loadSiswa();
            } else if (data.errors) {
                let errors = '<ul class="list-disc list-inside">';
                for (const key in data.errors) {
                    errors += `<li>${data.errors[key]}</li>`;
                }
                errors += '</ul>';
                errorAlertManual.innerHTML = errors;
                errorAlertManual.classList.remove('hidden');
            } else {
                showToast(data.message || 'Gagal menyimpan', 'error');
            }
        } catch (error) {
            showToast('Terjadi kesalahan', 'error');
        } finally {
            btnSimpan.disabled = false;
            btnSpinner.classList.add('hidden');
            btnText.textContent = 'Simpan';
        }
    });
    
    // Confirm Delete
    document.getElementById('btnConfirmDelete').addEventListener('click', async () => {
        const id = deleteIdInput.value;
        
        try {
            const response = await fetch(`${baseUrl}admin/siswa/delete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({ id_siswa: id, [csrfName]: csrfHash })
            });
            
            const data = await response.json();
            updateCsrf(data.csrf_hash);
            
            closeModal(modalConfirmDelete);
            showToast(data.message, data.status);
            
            if (data.status === 'success') {
                loadSiswa();
            }
        } catch (error) {
            showToast('Terjadi kesalahan', 'error');
        }
    });
    
    // Import Button
    btnImport.addEventListener('click', () => {
        formImport.reset();
        errorAlertImport.classList.add('hidden');
        openModal(modalImport);
    });
    
    // Import Form Submit
    formImport.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const btnUpload = document.getElementById('btnUploadImport');
        const btnImportText = document.getElementById('btnImportText');
        
        btnUpload.disabled = true;
        btnImportText.textContent = 'Mengimport...';
        errorAlertImport.classList.add('hidden');
        
        const formData = new FormData(formImport);
        formData.append(csrfName, csrfHash);
        
        try {
            const response = await fetch(`${baseUrl}admin/siswa/import-csv`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            
            const data = await response.json();
            updateCsrf(data.csrf_hash);
            
            if (data.status === 'success') {
                closeModal(modalImport);
                document.getElementById('importSuccessMessage').textContent = data.message;
                openModal(modalImportSuccess);
                loadSiswa();
            } else {
                errorAlertImport.innerHTML = data.message;
                errorAlertImport.classList.remove('hidden');
            }
        } catch (error) {
            errorAlertImport.innerHTML = 'Terjadi kesalahan server. Silakan coba lagi.';
            errorAlertImport.classList.remove('hidden');
        } finally {
            btnUpload.disabled = false;
            btnImportText.textContent = 'Upload dan Import';
        }
    });
</script>
<?= $this->endSection() ?>

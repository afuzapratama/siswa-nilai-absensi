<?= $this->extend('layout/app') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelas</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola data kelas berdasarkan tahun ajaran</p>
        </div>
        <button type="button" id="btnTambah" class="btn btn-primary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Tambah Kelas
        </button>
    </div>

    <!-- Alert Placeholder -->
    <div id="alert-placeholder"></div>

    <!-- Table Card -->
    <div class="card">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table-modern" id="dataTable">
                    <thead>
                        <tr>
                            <th class="w-16">No</th>
                            <th>Kode Kelas</th>
                            <th>Nama Kelas</th>
                            <th>Tahun Ajaran</th>
                            <th class="w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php if (empty($kelas)) : ?>
                            <tr>
                                <td colspan="5" class="text-center text-gray-500 py-8">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                    <p class="font-medium">Belum ada data kelas</p>
                                    <p class="text-sm">Klik tombol "Tambah Kelas" untuk menambah data baru</p>
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($kelas as $k) : ?>
                                <tr>
                                    <td class="text-center font-medium"><?= $no++ ?></td>
                                    <td>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-gray-100 text-gray-800 font-mono text-sm">
                                            <?= esc($k['kode_kelas']) ?>
                                        </span>
                                    </td>
                                    <td class="font-semibold text-gray-900"><?= esc($k['nama_kelas']) ?></td>
                                    <td>
                                        <span class="inline-flex items-center gap-1 text-gray-700">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <?= esc($k['tahun_ajaran']) ?> (<?= esc($k['semester']) ?>)
                                        </span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm btn-delete" 
                                                data-id="<?= $k['id_kelas'] ?>" 
                                                title="Hapus">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal CRUD -->
<div id="modalKelas" class="modal" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-backdrop"></div>
    <div class="modal-container">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="modalLabel">Form Kelas</h3>
                <button type="button" class="modal-close" data-modal-close aria-label="Close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="formKelas">
                <div class="modal-body">
                    <!-- Error Alert -->
                    <div class="alert alert-danger hidden" id="error-alert"></div>

                    <input type="hidden" name="id_kelas" id="id_kelas">
                    
                    <div class="form-group">
                        <label for="id_tahun_ajaran" class="form-label">
                            Tahun Ajaran <span class="text-red-500">*</span>
                        </label>
                        <select class="form-input" id="id_tahun_ajaran" name="id_tahun_ajaran" required>
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            <?php foreach ($tahun_ajaran as $ta) : ?>
                                <option value="<?= $ta['id_tahun_ajaran'] ?>" <?= ($ta['status'] == 'aktif') ? 'selected' : '' ?>>
                                    <?= esc($ta['tahun_ajaran']) ?> (<?= esc($ta['semester']) ?>)
                                    <?= ($ta['status'] == 'aktif') ? '✓ Aktif' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="kode_kelas" class="form-label">Kode Kelas</label>
                        <input type="text" 
                               class="form-input" 
                               id="kode_kelas" 
                               name="kode_kelas" 
                               placeholder="Kosongkan untuk auto-generate">
                        <p class="form-hint">Jika dikosongkan, kode akan digenerate otomatis</p>
                    </div>
                    
                    <div class="form-group">
                        <label for="nama_kelas" class="form-label">
                            Nama Kelas <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               class="form-input" 
                               id="nama_kelas" 
                               name="nama_kelas" 
                               placeholder="Contoh: XI TKJ 1" 
                               required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-modal-close>Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpan">
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

<!-- Confirm Delete Modal -->
<div id="modalConfirmDelete" class="modal" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-backdrop"></div>
    <div class="modal-container">
        <div class="modal-content max-w-md">
            <div class="modal-body text-center py-8">
                <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2" id="confirmDeleteLabel">Hapus Kelas?</h3>
                <p class="text-gray-600 mb-6">Data yang dihapus tidak dapat dikembalikan. Siswa dan nilai yang terkait dengan kelas ini juga akan terhapus.</p>
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
    
    // Elements
    const btnTambah = document.getElementById('btnTambah');
    const modalKelas = document.getElementById('modalKelas');
    const formKelas = document.getElementById('formKelas');
    const modalLabel = document.getElementById('modalLabel');
    const errorAlert = document.getElementById('error-alert');
    const btnSimpan = document.getElementById('btnSimpan');
    const btnSpinner = document.getElementById('btnSpinner');
    const btnText = document.getElementById('btnText');
    
    const modalConfirmDelete = document.getElementById('modalConfirmDelete');
    const deleteIdInput = document.getElementById('deleteId');
    const btnConfirmDelete = document.getElementById('btnConfirmDelete');
    
    // Toast notification helper
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-[100] px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full ${
            type === 'success' ? 'bg-green-500 text-white' : 
            type === 'error' ? 'bg-red-500 text-white' : 
            'bg-blue-500 text-white'
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
    
    // Close modal on backdrop click or close button
    document.querySelectorAll('.modal').forEach(modal => {
        modal.querySelector('.modal-backdrop')?.addEventListener('click', () => closeModal(modal));
        modal.querySelectorAll('[data-modal-close]').forEach(btn => {
            btn.addEventListener('click', () => closeModal(modal));
        });
    });
    
    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal.active').forEach(modal => closeModal(modal));
        }
    });
    
    // Button loading state
    function setLoading(isLoading) {
        btnSimpan.disabled = isLoading;
        btnSpinner.classList.toggle('hidden', !isLoading);
        btnText.textContent = isLoading ? 'Menyimpan...' : 'Simpan';
    }
    
    // 1. Add Button - Open modal for create
    btnTambah.addEventListener('click', () => {
        modalLabel.textContent = 'Tambah Kelas';
        formKelas.reset();
        errorAlert.classList.add('hidden');
        errorAlert.innerHTML = '';
        
        // Select active tahun ajaran
        const activeOption = document.querySelector('#id_tahun_ajaran option[selected]');
        if (activeOption) {
            document.getElementById('id_tahun_ajaran').value = activeOption.value;
        }
        
        openModal(modalKelas);
    });
    
    // 2. Form Submit - Save
    formKelas.addEventListener('submit', async (e) => {
        e.preventDefault();
        setLoading(true);
        errorAlert.classList.add('hidden');
        
        const formData = new FormData(formKelas);
        formData.append(csrfName, csrfHash);
        
        try {
            const response = await fetch(`${baseUrl}admin/kelas/save`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });
            
            const data = await response.json();
            updateCsrf(data.csrf_hash);
            
            if (data.status === 'success') {
                closeModal(modalKelas);
                showToast(data.message, 'success');
                setTimeout(() => location.reload(), 1500);
            } else if (data.errors) {
                let errors = '<ul class="list-disc list-inside">';
                for (const key in data.errors) {
                    errors += `<li>${data.errors[key]}</li>`;
                }
                errors += '</ul>';
                errorAlert.innerHTML = errors;
                errorAlert.classList.remove('hidden');
            } else {
                showToast(data.message || 'Gagal menyimpan data', 'error');
            }
        } catch (error) {
            showToast('Terjadi kesalahan saat menyimpan data', 'error');
            console.error(error);
        } finally {
            setLoading(false);
        }
    });
    
    // 3. Delete Button - Open confirm modal
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.id;
            deleteIdInput.value = id;
            openModal(modalConfirmDelete);
        });
    });
    
    // Confirm Delete
    btnConfirmDelete.addEventListener('click', async () => {
        const id = deleteIdInput.value;
        
        try {
            const response = await fetch(`${baseUrl}admin/kelas/delete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    id: id,
                    [csrfName]: csrfHash
                })
            });
            
            const data = await response.json();
            updateCsrf(data.csrf_hash);
            
            closeModal(modalConfirmDelete);
            showToast(data.message, data.status);
            
            if (data.status === 'success') {
                setTimeout(() => location.reload(), 1500);
            }
        } catch (error) {
            showToast('Terjadi kesalahan saat menghapus data', 'error');
            console.error(error);
        }
    });
</script>
<?= $this->endSection() ?>

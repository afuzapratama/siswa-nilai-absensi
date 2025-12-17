<?= $this->extend('layout/app') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Mata Pelajaran</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola data mata pelajaran dan hubungan ke kelas</p>
        </div>
        <button type="button" id="btnTambah" class="btn btn-primary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Tambah Mata Pelajaran
        </button>
    </div>

    <!-- Table Card -->
    <div class="card">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table-modern" id="dataTable">
                    <thead>
                        <tr>
                            <th class="w-16">No</th>
                            <th>Kode Mapel</th>
                            <th>Nama Mata Pelajaran</th>
                            <th>Kelas Terhubung (Tahun Aktif)</th>
                            <th class="w-40">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php if (empty($mapel)) : ?>
                            <tr>
                                <td colspan="5" class="text-center text-gray-500 py-8">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    <p class="font-medium">Belum ada data mata pelajaran</p>
                                    <p class="text-sm">Klik tombol "Tambah Mata Pelajaran" untuk menambah data baru</p>
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php foreach ($mapel as $m) : ?>
                                <tr>
                                    <td class="text-center font-medium"><?= $no++ ?></td>
                                    <td>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-purple-100 text-purple-800 font-mono text-sm">
                                            <?= esc($m['kode_mapel']) ?>
                                        </span>
                                    </td>
                                    <td class="font-semibold text-gray-900"><?= esc($m['nama_mapel']) ?></td>
                                    <td>
                                        <?php if (empty($m['kelas_terhubung'])) : ?>
                                            <span class="badge badge-secondary">Belum terhubung</span>
                                        <?php else : ?>
                                            <div class="flex flex-wrap gap-1">
                                                <?php foreach ($m['kelas_terhubung'] as $k) : ?>
                                                    <span class="badge badge-info">
                                                        <?= esc($k['nama_kelas']) ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <button type="button" class="btn btn-warning btn-sm btn-edit" 
                                                    data-id="<?= $m['id_mapel'] ?>" 
                                                    title="Edit">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Edit
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm btn-delete" 
                                                    data-id="<?= $m['id_mapel'] ?>" 
                                                    title="Hapus">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Hapus
                                            </button>
                                        </div>
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

<!-- Modal CRUD Mata Pelajaran -->
<div id="modalMapel" class="modal" role="dialog" aria-labelledby="modalLabelMapel" aria-hidden="true">
    <div class="modal-backdrop"></div>
    <div class="modal-container">
        <div class="modal-content max-w-2xl">
            <div class="modal-header">
                <h3 class="modal-title" id="modalLabelMapel">Form Mata Pelajaran</h3>
                <button type="button" class="modal-close" data-modal-close aria-label="Close">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="formMapel">
                <div class="modal-body">
                    <div class="alert alert-danger hidden" id="error-alert"></div>

                    <input type="hidden" name="id_mapel" id="id_mapel">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="form-group">
                            <label for="kode_mapel" class="form-label">
                                Kode Mapel <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="form-input" 
                                   id="kode_mapel" 
                                   name="kode_mapel" 
                                   placeholder="Contoh: MTK-01" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label for="nama_mapel" class="form-label">
                                Nama Mata Pelajaran <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   class="form-input" 
                                   id="nama_mapel" 
                                   name="nama_mapel" 
                                   placeholder="Contoh: Matematika Wajib" 
                                   required>
                        </div>
                    </div>
                    
                    <hr class="my-4 border-gray-200">
                    
                    <div class="form-group">
                        <label class="form-label">Hubungkan ke Kelas (Tahun Ajaran Aktif)</label>
                        <p class="text-sm text-gray-500 mb-3">Pilih kelas yang akan diajarkan mata pelajaran ini</p>
                        
                        <div class="max-h-48 overflow-y-auto border border-gray-200 rounded-lg p-3 space-y-2" id="kelasCheckboxes">
                            <?php if (empty($kelas_aktif)) : ?>
                                <p class="text-gray-500 text-sm text-center py-4">Belum ada kelas untuk tahun ajaran aktif</p>
                            <?php else : ?>
                                <?php foreach ($kelas_aktif as $k) : ?>
                                    <label class="flex items-center gap-3 p-2 hover:bg-gray-50 rounded-lg cursor-pointer transition-colors">
                                        <input type="checkbox" 
                                               name="id_kelas[]" 
                                               value="<?= $k['id_kelas'] ?>" 
                                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="text-gray-900"><?= esc($k['nama_kelas']) ?></span>
                                        <span class="text-gray-500 text-sm">(<?= esc($k['kode_kelas']) ?>)</span>
                                    </label>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
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
                <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Mata Pelajaran?</h3>
                <p class="text-gray-600 mb-6">Menghapus mapel akan menghapus relasinya ke semua kelas dan data nilai terkait.</p>
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
    const modalMapel = document.getElementById('modalMapel');
    const formMapel = document.getElementById('formMapel');
    const modalLabel = document.getElementById('modalLabelMapel');
    const errorAlert = document.getElementById('error-alert');
    const btnSimpan = document.getElementById('btnSimpan');
    const btnSpinner = document.getElementById('btnSpinner');
    const btnText = document.getElementById('btnText');
    
    const modalConfirmDelete = document.getElementById('modalConfirmDelete');
    const deleteIdInput = document.getElementById('deleteId');
    
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
    
    // Loading state
    function setLoading(isLoading) {
        btnSimpan.disabled = isLoading;
        btnSpinner.classList.toggle('hidden', !isLoading);
        btnText.textContent = isLoading ? 'Menyimpan...' : 'Simpan';
    }
    
    // Reset checkboxes
    function resetKelasCheckboxes() {
        document.querySelectorAll('#kelasCheckboxes input[type="checkbox"]').forEach(cb => {
            cb.checked = false;
        });
    }
    
    // Set checkboxes by values
    function setKelasCheckboxes(kelasIds) {
        resetKelasCheckboxes();
        kelasIds.forEach(id => {
            const cb = document.querySelector(`#kelasCheckboxes input[value="${id}"]`);
            if (cb) cb.checked = true;
        });
    }
    
    // 1. Add Button
    btnTambah.addEventListener('click', () => {
        modalLabel.textContent = 'Tambah Mata Pelajaran';
        formMapel.reset();
        document.getElementById('id_mapel').value = '';
        errorAlert.classList.add('hidden');
        resetKelasCheckboxes();
        openModal(modalMapel);
    });
    
    // 2. Edit Button
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            modalLabel.textContent = 'Edit Mata Pelajaran';
            errorAlert.classList.add('hidden');
            
            try {
                const response = await fetch(`${baseUrl}admin/mata-pelajaran/fetch`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams({ id: id, [csrfName]: csrfHash })
                });
                
                const data = await response.json();
                updateCsrf(data.csrf_hash);
                
                if (data.status === 'success') {
                    document.getElementById('id_mapel').value = data.data_mapel.id_mapel;
                    document.getElementById('kode_mapel').value = data.data_mapel.kode_mapel;
                    document.getElementById('nama_mapel').value = data.data_mapel.nama_mapel;
                    setKelasCheckboxes(data.kelas_ids || []);
                    openModal(modalMapel);
                } else {
                    showToast(data.message || 'Gagal mengambil data', 'error');
                }
            } catch (error) {
                showToast('Terjadi kesalahan', 'error');
            }
        });
    });
    
    // 3. Form Submit
    formMapel.addEventListener('submit', async (e) => {
        e.preventDefault();
        setLoading(true);
        errorAlert.classList.add('hidden');
        
        const formData = new FormData(formMapel);
        formData.append(csrfName, csrfHash);
        
        try {
            const response = await fetch(`${baseUrl}admin/mata-pelajaran/save`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            
            const data = await response.json();
            updateCsrf(data.csrf_hash);
            
            if (data.status === 'success') {
                closeModal(modalMapel);
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
                showToast(data.message || 'Gagal menyimpan', 'error');
            }
        } catch (error) {
            showToast('Terjadi kesalahan', 'error');
        } finally {
            setLoading(false);
        }
    });
    
    // 4. Delete Button
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', () => {
            deleteIdInput.value = btn.dataset.id;
            openModal(modalConfirmDelete);
        });
    });
    
    // Confirm Delete
    document.getElementById('btnConfirmDelete').addEventListener('click', async () => {
        const id = deleteIdInput.value;
        
        try {
            const response = await fetch(`${baseUrl}admin/mata-pelajaran/delete`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({ id: id, [csrfName]: csrfHash })
            });
            
            const data = await response.json();
            updateCsrf(data.csrf_hash);
            
            closeModal(modalConfirmDelete);
            showToast(data.message, data.status);
            
            if (data.status === 'success') {
                setTimeout(() => location.reload(), 1500);
            }
        } catch (error) {
            showToast('Terjadi kesalahan', 'error');
        }
    });
</script>
<?= $this->endSection() ?>

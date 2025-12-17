<?= $this->extend('layout/app') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Form Penilaian</h1>
            <p class="mt-1 text-sm text-gray-500">Kelola form penilaian siswa berdasarkan kelas dan mata pelajaran</p>
        </div>
        <a href="<?= site_url('admin/penilaian/create') ?>" class="btn btn-primary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Buat Form Penilaian Baru
        </a>
    </div>

    <!-- Table Card -->
    <div class="card">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table-modern" id="dataTable">
                    <thead>
                        <tr>
                            <th class="w-16">No</th>
                            <th>Judul Penilaian</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Tgl Dibuat</th>
                            <th class="w-48">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($forms)) : ?>
                            <tr>
                                <td colspan="6" class="text-center text-gray-500 py-12">
                                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                    </svg>
                                    <p class="font-medium text-gray-600">Belum ada form penilaian</p>
                                    <p class="text-sm mb-4">Klik tombol "Buat Form Penilaian Baru" untuk memulai</p>
                                    <a href="<?= site_url('admin/penilaian/create') ?>" class="btn btn-primary btn-sm">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Buat Form Baru
                                    </a>
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php $no = 1; ?>
                            <?php foreach ($forms as $form) : ?>
                                <tr>
                                    <td class="text-center font-medium"><?= $no++ ?></td>
                                    <td>
                                        <a href="<?= site_url('admin/penilaian/form/' . $form['id_header']) ?>" class="font-semibold text-blue-600 hover:text-blue-800 hover:underline">
                                            <?= esc($form['judul_penilaian']) ?>
                                        </a>
                                    </td>
                                    <td>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-blue-100 text-blue-800 text-sm font-medium">
                                            <?= esc($form['nama_kelas']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-purple-100 text-purple-800 text-sm font-medium">
                                            <?= esc($form['nama_mapel']) ?>
                                        </span>
                                    </td>
                                    <td class="text-gray-600 text-sm">
                                        <?= esc(\CodeIgniter\I18n\Time::parse($form['created_at'] ?? 'now')
                                                ->setTimezone('Asia/Jakarta')->format('d M Y, H:i')) ?>
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <a href="<?= site_url('admin/penilaian/form/' . $form['id_header']) ?>" class="btn btn-info btn-sm" title="Input Nilai">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                Input Nilai
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm btn-delete-form" 
                                                    data-id="<?= $form['id_header'] ?>" 
                                                    data-judul="<?= esc($form['judul_penilaian']) ?>"
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
                <h3 class="text-xl font-bold text-gray-900 mb-2">Hapus Form Penilaian?</h3>
                <p class="text-gray-600 mb-2">Anda yakin ingin menghapus form:</p>
                <p class="font-semibold text-gray-900 mb-4" id="deleteFormTitle"></p>
                <p class="text-red-600 text-sm mb-6">⚠️ SEMUA NILAI di dalamnya akan hilang permanen!</p>
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
    const csrfName = document.querySelector('meta[name="csrf-name"]').content;
    let csrfHash = document.querySelector('meta[name="csrf-token"]').content;
    const baseUrl = document.querySelector('meta[name="base-url"]').content;
    
    const modalConfirmDelete = document.getElementById('modalConfirmDelete');
    const deleteIdInput = document.getElementById('deleteId');
    const deleteFormTitle = document.getElementById('deleteFormTitle');
    
    function updateCsrf(newHash) {
        if (newHash) {
            csrfHash = newHash;
            document.querySelector('meta[name="csrf-token"]').content = newHash;
        }
    }
    
    function showToast(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-[100] px-6 py-4 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        toast.innerHTML = `<div class="flex items-center gap-3"><span class="font-medium">${message}</span></div>`;
        document.body.appendChild(toast);
        requestAnimationFrame(() => toast.classList.replace('translate-x-full', 'translate-x-0'));
        setTimeout(() => {
            toast.classList.replace('translate-x-0', 'translate-x-full');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    
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
    
    // Delete button
    document.querySelectorAll('.btn-delete-form').forEach(btn => {
        btn.addEventListener('click', () => {
            deleteIdInput.value = btn.dataset.id;
            deleteFormTitle.textContent = `"${btn.dataset.judul}"`;
            openModal(modalConfirmDelete);
        });
    });
    
    // Confirm delete
    document.getElementById('btnConfirmDelete').addEventListener('click', async () => {
        const id = deleteIdInput.value;
        
        try {
            const response = await fetch(`${baseUrl}admin/penilaian/delete-form`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({ id_header: id, [csrfName]: csrfHash })
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

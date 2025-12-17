<?= $this->extend('layout/app') ?>

<?= $this->section('css') ?>
<style>
    /* Custom styles for input nilai */
    .nilai-input {
        width: 4.5rem;
        text-align: center;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.375rem 0.5rem;
        font-size: 0.875rem;
        line-height: 1.25rem;
        transition: all 0.15s ease;
    }
    .nilai-input:focus {
        outline: none;
        ring: 2px;
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
    }
    .nilai-input.saving {
        background-color: #fefce8;
        border-color: #facc15;
    }
    .nilai-input.saved {
        background-color: #f0fdf4;
        border-color: #4ade80;
    }
    .nilai-input.error {
        background-color: #fef2f2;
        border-color: #f87171;
    }
    .kolom-header {
        background-color: #f9fafb;
        min-width: 5rem;
    }
    .nilai-rata {
        font-weight: 600;
        color: #2563eb;
    }
    /* Hide number input spinners */
    .nilai-input::-webkit-outer-spin-button,
    .nilai-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .nilai-input[type=number] {
        -moz-appearance: textfield;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900"><?= esc($header['judul_penilaian']) ?></h1>
            <p class="mt-1 text-sm text-gray-500">
                <span class="inline-flex items-center gap-1.5">
                    <span class="px-2 py-0.5 rounded bg-blue-100 text-blue-700 text-xs font-medium"><?= esc($header['nama_kelas']) ?></span>
                    <span class="text-gray-400">•</span>
                    <span class="px-2 py-0.5 rounded bg-purple-100 text-purple-700 text-xs font-medium"><?= esc($header['nama_mapel']) ?></span>
                    <span class="text-gray-400">•</span>
                    <span class="text-gray-500"><?= date('d M Y', strtotime($header['created_at'])) ?></span>
                </span>
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="<?= site_url('admin/penilaian') ?>" class="btn btn-secondary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Kembali ke Daftar
            </a>
        </div>
    </div>

    <!-- Info & Actions Card -->
    <div class="card">
        <div class="card-body">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="bg-green-50 border border-green-200 rounded-lg px-4 py-2">
                        <p class="text-xs text-green-600 font-medium">Jumlah Siswa</p>
                        <p class="text-xl font-bold text-green-700"><?= count($siswa_list) ?></p>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-2">
                        <p class="text-xs text-blue-600 font-medium">Kolom Nilai</p>
                        <p class="text-xl font-bold text-blue-700" id="kolomCount"><?= count($kolom_list) ?></p>
                    </div>
                    <div class="text-sm text-gray-500 flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                        <span>Auto-save aktif</span>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <button type="button" id="btnAddKolom" class="btn btn-primary btn-sm">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Tambah Kolom Nilai
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Nilai Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="overflow-x-auto">
                <table class="table-auto w-full min-w-max" id="nilaiTable">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">No</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">NIS</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-48">Nama Siswa</th>
                            <?php foreach ($kolom_list as $kolom) : ?>
                                <th class="kolom-header px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider" data-kolom-id="<?= $kolom['id_kolom'] ?>">
                                    <div class="flex flex-col items-center gap-1">
                                        <input type="text" 
                                               class="w-16 text-center border-0 border-b border-gray-300 bg-transparent px-1 py-0.5 text-xs font-semibold focus:outline-none focus:border-blue-500"
                                               value="<?= esc($kolom['nama_kolom']) ?>"
                                               data-kolom-id="<?= $kolom['id_kolom'] ?>"
                                               onchange="updateNamaKolom(this)">
                                        <button type="button" 
                                                class="text-red-400 hover:text-red-600 transition-colors"
                                                onclick="deleteKolom(<?= $kolom['id_kolom'] ?>)"
                                                title="Hapus Kolom">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </th>
                            <?php endforeach; ?>
                            <th class="kolom-header px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-blue-50">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200" id="nilaiTbody">
                        <?php $no = 1; ?>
                        <?php foreach ($siswa_list as $siswa) : ?>
                            <tr class="hover:bg-gray-50" data-siswa-id="<?= $siswa['id_siswa'] ?>">
                                <td class="px-4 py-3 text-sm text-gray-500"><?= $no++ ?></td>
                                <td class="px-4 py-3 text-sm font-mono text-gray-600"><?= esc($siswa['nis'] ?? '-') ?></td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900"><?= esc($siswa['nama_siswa']) ?></td>
                                <?php foreach ($kolom_list as $kolom) : ?>
                                    <?php 
                                        $nilai_existing = '';
                                        foreach ($nilai_list as $n) {
                                            if ($n['id_siswa'] == $siswa['id_siswa'] && $n['id_kolom'] == $kolom['id_kolom']) {
                                                // Format nilai: hilangkan .00 atau .0 di belakang
                                                $val = (float)$n['nilai'];
                                                $nilai_existing = ($val == (int)$val) ? (int)$val : $val;
                                                break;
                                            }
                                        }
                                    ?>
                                    <td class="px-2 py-2 text-center" data-kolom-id="<?= $kolom['id_kolom'] ?>">
                                        <input type="number" 
                                               step="any" 
                                               min="0" 
                                               max="100"
                                               class="nilai-input"
                                               value="<?= $nilai_existing ?>"
                                               data-id-siswa="<?= $siswa['id_siswa'] ?>"
                                               data-id-kolom="<?= $kolom['id_kolom'] ?>"
                                               onchange="saveNilai(this)"
                                               onkeydown="handleKeyNav(event, this)">
                                    </td>
                                <?php endforeach; ?>
                                <?php 
                                    // Format rata-rata tanpa .00
                                    $avg = $rata_rata[$siswa['id_siswa']] ?? null;
                                    $avg_display = '-';
                                    if ($avg !== null) {
                                        $avg_display = ($avg == (int)$avg) ? (int)$avg : number_format($avg, 2);
                                    }
                                ?>
                                <td class="px-4 py-2 text-center nilai-rata" data-siswa-id="<?= $siswa['id_siswa'] ?>">
                                    <?= $avg_display ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($siswa_list)) : ?>
                            <tr>
                                <td colspan="100" class="px-4 py-8 text-center text-gray-500">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                                    </svg>
                                    Tidak ada siswa di kelas ini
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="bg-gray-50 rounded-lg p-4">
        <p class="text-xs font-medium text-gray-700 mb-2">Keterangan:</p>
        <div class="flex flex-wrap items-center gap-4 text-xs text-gray-600">
            <span class="flex items-center gap-1.5">
                <span class="w-4 h-4 bg-yellow-50 border border-yellow-400 rounded"></span>
                Menyimpan...
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-4 h-4 bg-green-50 border border-green-400 rounded"></span>
                Tersimpan
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-4 h-4 bg-red-50 border border-red-400 rounded"></span>
                Gagal simpan
            </span>
            <span class="text-gray-400">|</span>
            <span>Gunakan <kbd class="px-1.5 py-0.5 bg-gray-200 rounded text-xs">Tab</kbd> atau <kbd class="px-1.5 py-0.5 bg-gray-200 rounded text-xs">Enter</kbd> untuk pindah ke kolom berikutnya</span>
        </div>
    </div>
</div>

<!-- Add Kolom Modal -->
<div class="modal" id="modalAddKolom">
    <div class="modal-backdrop"></div>
    <div class="modal-container">
        <div class="modal-content max-w-md">
            <div class="modal-header">
                <h3 class="modal-title">Tambah Kolom Nilai</h3>
                <button type="button" class="btn-close" data-dismiss="modal">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="formAddKolom">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="namaKolomBaru" class="form-label">Nama Kolom <span class="text-red-500">*</span></label>
                        <input type="text" class="form-input" id="namaKolomBaru" placeholder="Contoh: N1, Praktik, Teori" required>
                        <p class="form-hint">Nama kolom akan ditampilkan sebagai header di tabel nilai</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Tambah Kolom</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Kolom Modal -->
<div class="modal" id="modalDeleteKolom">
    <div class="modal-backdrop"></div>
    <div class="modal-container">
        <div class="modal-content max-w-md">
            <div class="modal-header">
                <h3 class="modal-title text-red-600">Hapus Kolom Nilai</h3>
                <button type="button" class="btn-close" data-dismiss="modal">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="modal-body">
                <div class="flex items-center gap-4 p-4 bg-red-50 rounded-lg border border-red-200">
                    <svg class="w-10 h-10 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-red-800">Apakah Anda yakin ingin menghapus kolom ini?</p>
                        <p class="text-xs text-red-600 mt-1">Semua nilai siswa untuk kolom ini akan terhapus secara permanen.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" id="btnConfirmDeleteKolom" class="btn btn-danger">Hapus Kolom</button>
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
    const id_header = <?= $header['id_header'] ?>;
    
    let deleteKolomId = null;

    // Toast function
    function showToast(message, type = 'info') {
        const bgColors = { success: 'bg-green-500', error: 'bg-red-500', info: 'bg-blue-500', warning: 'bg-yellow-500' };
        const toast = document.createElement('div');
        toast.className = `fixed bottom-4 right-4 ${bgColors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-up`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    // Modal functions
    function openModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    // Close modal on backdrop/button click
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal-backdrop') || e.target.closest('[data-dismiss="modal"]')) {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });

    // Save nilai (auto-save on change)
    window.saveNilai = async function(input) {
        const id_siswa = input.dataset.idSiswa;
        const id_kolom = input.dataset.idKolom;
        const nilai = input.value;
        
        input.classList.remove('saved', 'error');
        input.classList.add('saving');
        
        try {
            const response = await fetch(`${baseUrl}admin/penilaian/save-nilai`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    id_header: id_header,
                    id_siswa: id_siswa,
                    id_kolom: id_kolom,
                    nilai: nilai,
                    [csrfName]: csrfHash
                })
            });
            
            const data = await response.json();
            
            // Update CSRF token
            if (data.csrf_hash) {
                csrfHash = data.csrf_hash;
            }
            
            if (data.status === 'success') {
                input.classList.remove('saving');
                input.classList.add('saved');
                
                // Update rata-rata
                if (data.rata_rata !== undefined) {
                    const rataCell = document.querySelector(`td.nilai-rata[data-siswa-id="${id_siswa}"]`);
                    if (rataCell) {
                        // Format: hilangkan .00 jika bilangan bulat
                        const avg = parseFloat(data.rata_rata);
                        rataCell.textContent = data.rata_rata ? (avg === Math.floor(avg) ? Math.floor(avg) : avg.toFixed(2)) : '-';
                    }
                }
                
                setTimeout(() => input.classList.remove('saved'), 1500);
            } else {
                throw new Error(data.message || 'Gagal menyimpan');
            }
        } catch (error) {
            input.classList.remove('saving');
            input.classList.add('error');
            showToast('Gagal menyimpan nilai: ' + error.message, 'error');
        }
    };

    // Handle keyboard navigation
    window.handleKeyNav = function(e, input) {
        if (e.key === 'Enter' || e.key === 'Tab') {
            e.preventDefault();
            const allInputs = Array.from(document.querySelectorAll('.nilai-input'));
            const currentIndex = allInputs.indexOf(input);
            const nextIndex = e.shiftKey ? currentIndex - 1 : currentIndex + 1;
            
            if (nextIndex >= 0 && nextIndex < allInputs.length) {
                allInputs[nextIndex].focus();
                allInputs[nextIndex].select();
            }
        }
    };

    // Update nama kolom
    window.updateNamaKolom = async function(input) {
        const id_kolom = input.dataset.kolomId;
        const nama_kolom = input.value.trim();
        
        if (!nama_kolom) {
            showToast('Nama kolom tidak boleh kosong', 'warning');
            return;
        }
        
        try {
            const response = await fetch(`${baseUrl}admin/penilaian/update-kolom`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    id_kolom: id_kolom,
                    nama_kolom: nama_kolom,
                    [csrfName]: csrfHash
                })
            });
            
            const data = await response.json();
            
            if (data.csrf_hash) {
                csrfHash = data.csrf_hash;
            }
            
            if (data.status === 'success') {
                showToast('Nama kolom diperbarui', 'success');
            } else {
                throw new Error(data.message || 'Gagal memperbarui');
            }
        } catch (error) {
            showToast('Gagal memperbarui nama kolom', 'error');
        }
    };

    // Delete kolom
    window.deleteKolom = function(id_kolom) {
        deleteKolomId = id_kolom;
        openModal('modalDeleteKolom');
    };

    document.getElementById('btnConfirmDeleteKolom').addEventListener('click', async function() {
        if (!deleteKolomId) return;
        
        this.disabled = true;
        this.textContent = 'Menghapus...';
        
        try {
            const response = await fetch(`${baseUrl}admin/penilaian/delete-kolom`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    id_kolom: deleteKolomId,
                    [csrfName]: csrfHash
                })
            });
            
            const data = await response.json();
            
            if (data.csrf_hash) {
                csrfHash = data.csrf_hash;
            }
            
            if (data.status === 'success') {
                // Remove kolom from table
                document.querySelectorAll(`[data-kolom-id="${deleteKolomId}"]`).forEach(el => el.remove());
                
                // Update kolom count
                const countEl = document.getElementById('kolomCount');
                countEl.textContent = parseInt(countEl.textContent) - 1;
                
                closeModal('modalDeleteKolom');
                showToast('Kolom berhasil dihapus', 'success');
                
                // Recalculate rata-rata - reload page for simplicity
                setTimeout(() => location.reload(), 1000);
            } else {
                throw new Error(data.message || 'Gagal menghapus');
            }
        } catch (error) {
            showToast('Gagal menghapus kolom: ' + error.message, 'error');
        } finally {
            this.disabled = false;
            this.textContent = 'Hapus Kolom';
        }
    });

    // Add kolom
    document.getElementById('btnAddKolom').addEventListener('click', () => {
        document.getElementById('namaKolomBaru').value = '';
        openModal('modalAddKolom');
    });

    document.getElementById('formAddKolom').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const namaKolom = document.getElementById('namaKolomBaru').value.trim();
        if (!namaKolom) {
            showToast('Nama kolom tidak boleh kosong', 'warning');
            return;
        }
        
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Menambah...';
        
        try {
            const response = await fetch(`${baseUrl}admin/penilaian/add-kolom`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    id_header: id_header,
                    nama_kolom: namaKolom,
                    [csrfName]: csrfHash
                })
            });
            
            const data = await response.json();
            
            if (data.csrf_hash) {
                csrfHash = data.csrf_hash;
            }
            
            if (data.status === 'success') {
                closeModal('modalAddKolom');
                showToast('Kolom berhasil ditambahkan', 'success');
                setTimeout(() => location.reload(), 500);
            } else {
                throw new Error(data.message || 'Gagal menambahkan');
            }
        } catch (error) {
            showToast('Gagal menambahkan kolom: ' + error.message, 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Tambah Kolom';
        }
    });

    // ESC key to close modals
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal.active').forEach(modal => {
                modal.classList.remove('active');
                document.body.style.overflow = '';
            });
        }
    });
</script>
<?= $this->endSection() ?>

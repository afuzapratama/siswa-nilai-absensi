<?= $this->extend('layout/app') ?>

<?= $this->section('css') ?>
<style>
    /* Row done indicator */
    .row-done {
        @apply bg-emerald-50;
    }
    .row-done td:first-child {
        box-shadow: inset 3px 0 0 #10B981;
    }
    /* Checkbox absensi styling */
    .check-absen {
        @apply w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500 cursor-pointer;
    }
    .check-absen:checked {
        @apply bg-blue-600;
    }
    /* Status specific colors */
    .check-absen[value="H"]:checked {
        @apply bg-green-600;
    }
    .check-absen[value="I"]:checked {
        @apply bg-yellow-500;
    }
    .check-absen[value="S"]:checked {
        @apply bg-orange-500;
    }
    .check-absen[value="A"]:checked {
        @apply bg-red-600;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900"><?= $title ?></h1>
        <p class="mt-1 text-sm text-gray-500">Pilih kelas, mata pelajaran dan tanggal untuk input absensi siswa</p>
    </div>

    <!-- Filter Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Input Absensi</h3>
        </div>
        <div class="card-body">
            <form id="filterAbsensi" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Tanggal -->
                    <div class="form-group">
                        <label for="tanggal" class="form-label">Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" 
                               class="form-input" 
                               id="tanggal" 
                               name="tanggal" 
                               value="<?= date('Y-m-d') ?>" 
                               required>
                    </div>
                    
                    <!-- Kelas -->
                    <div class="form-group">
                        <label for="id_kelas" class="form-label">Pilih Kelas <span class="text-red-500">*</span></label>
                        <select class="form-input" id="id_kelas" name="id_kelas" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($kelas_list as $kelas) : ?>
                                <option value="<?= $kelas['id_kelas'] ?>">
                                    <?= esc($kelas['nama_kelas']) ?> (<?= esc($kelas['kode_kelas']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Mapel -->
                    <div class="form-group">
                        <label for="id_mapel" class="form-label">Pilih Mata Pelajaran <span class="text-red-500">*</span></label>
                        <select class="form-input" id="id_mapel" name="id_mapel" required disabled>
                            <option value="">-- Pilih Kelas Dulu --</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary" id="btnGo">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                    Tampilkan
                </button>
            </form>
        </div>
    </div>

    <!-- Hasil Absensi Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Daftar Absensi Siswa</h3>
        </div>
        <div class="card-body p-0">
            <div id="tabelAbsensiContainer" class="p-6">
                <div class="text-center py-12 text-gray-500">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <p>Silakan pilih filter di atas dan klik "Tampilkan" untuk menampilkan data siswa.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="bg-gray-50 rounded-lg p-4">
        <p class="text-xs font-medium text-gray-700 mb-2">Keterangan Status:</p>
        <div class="flex flex-wrap items-center gap-4 text-sm">
            <span class="flex items-center gap-2">
                <span class="w-4 h-4 bg-green-600 rounded"></span>
                <span>H = Hadir</span>
            </span>
            <span class="flex items-center gap-2">
                <span class="w-4 h-4 bg-yellow-500 rounded"></span>
                <span>I = Izin</span>
            </span>
            <span class="flex items-center gap-2">
                <span class="w-4 h-4 bg-orange-500 rounded"></span>
                <span>S = Sakit</span>
            </span>
            <span class="flex items-center gap-2">
                <span class="w-4 h-4 bg-red-600 rounded"></span>
                <span>A = Alpa</span>
            </span>
        </div>
    </div>
</div>

<!-- Modal Izin/Sakit -->
<div class="modal" id="modalIzinSakit">
    <div class="modal-backdrop"></div>
    <div class="modal-container">
        <div class="modal-content max-w-2xl">
            <div class="modal-header">
                <h3 class="modal-title">Catatan & Bukti (Izin/Sakit)</h3>
                <button type="button" class="btn-close" data-dismiss="modal">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form id="formIzinSakit" enctype="multipart/form-data">
                <div class="modal-body">
                    <!-- Hidden fields -->
                    <input type="hidden" name="id_ta">
                    <input type="hidden" name="id_kelas">
                    <input type="hidden" name="id_mapel">
                    <input type="hidden" name="tanggal">
                    <input type="hidden" name="id_siswa">
                    <input type="hidden" name="status">
                    <input type="hidden" name="hapus_bukti" value="0">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Catatan -->
                        <div class="form-group md:col-span-1">
                            <label for="catatan" class="form-label">Catatan <span class="text-red-500">*</span></label>
                            <textarea id="catatan" 
                                      name="catatan" 
                                      class="form-input" 
                                      rows="5" 
                                      required 
                                      placeholder="Tulis alasan izin/sakit di sini..."></textarea>
                            <p class="form-hint">Tulis ringkas, jelas, dan sopan</p>
                        </div>

                        <!-- Bukti -->
                        <div class="form-group md:col-span-1">
                            <label for="bukti" class="form-label">Bukti (opsional)</label>
                            <div class="relative">
                                <input type="file" 
                                       id="bukti" 
                                       name="bukti" 
                                       class="form-input pt-1.5"
                                       accept="image/jpeg,image/png,image/webp">
                            </div>
                            <p class="form-hint">Format: JPG/PNG/WEBP, maks 4MB</p>

                            <!-- Preview -->
                            <div id="previewWrap" class="border border-gray-200 rounded-lg p-3 mt-3 hidden">
                                <div class="flex items-start gap-3">
                                    <img id="imgPreview" src="" alt="Preview" class="w-24 h-24 object-cover rounded border">
                                    <div class="flex-1">
                                        <a href="#" target="_blank" id="linkBukti" class="text-sm text-blue-600 hover:underline block mb-2">
                                            Lihat ukuran penuh
                                        </a>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm"
                                                id="btnHapusBukti">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script type="module">
    const csrfName = document.querySelector('meta[name="csrf-name"]').content;
    let csrfHash = document.querySelector('meta[name="csrf-token"]').content;
    const baseUrl = document.querySelector('meta[name="base-url"]').content;

    const ENDPOINT = {
        MAPEL: `${baseUrl}admin/absensi/ajax-get-mapel`,
        TABEL: `${baseUrl}admin/absensi/get-siswa`,
        DETAIL: `${baseUrl}admin/absensi/detail`,
        SAVE_ABSEN: `${baseUrl}admin/absensi/save-absen`,
        SAVE_IS: `${baseUrl}admin/absensi/save-izin-sakit`
    };

    // Elements
    const formFilter = document.getElementById('filterAbsensi');
    const selectKelas = document.getElementById('id_kelas');
    const selectMapel = document.getElementById('id_mapel');
    const btnGo = document.getElementById('btnGo');
    const container = document.getElementById('tabelAbsensiContainer');
    
    const modal = document.getElementById('modalIzinSakit');
    const formIS = document.getElementById('formIzinSakit');
    const fileInput = document.getElementById('bukti');
    const previewWrap = document.getElementById('previewWrap');
    const imgPreview = document.getElementById('imgPreview');
    const linkBukti = document.getElementById('linkBukti');
    const hapusBuktiInput = formIS.querySelector('[name="hapus_bukti"]');

    let objectUrl = null;

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
    function openModal() {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        modal.classList.remove('active');
        document.body.style.overflow = '';
    }

    // Close modal on backdrop/button click
    modal.addEventListener('click', (e) => {
        if (e.target.classList.contains('modal-backdrop') || e.target.closest('[data-dismiss="modal"]')) {
            closeModal();
        }
    });

    // ESC to close modal
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('active')) {
            closeModal();
        }
    });

    // Paint row done
    function paintRow(tr) {
        const anyChecked = tr.querySelector('.check-absen:checked');
        const hasBukti = tr.querySelector('[data-has-bukti="1"]');
        if (anyChecked || hasBukti) {
            tr.classList.add('row-done');
        } else {
            tr.classList.remove('row-done');
        }
    }

    function paintAllRows() {
        container.querySelectorAll('tbody tr').forEach(tr => paintRow(tr));
    }

    // Get Mapel by Kelas
    selectKelas.addEventListener('change', async function() {
        const id_kelas = this.value;
        selectMapel.innerHTML = '<option value="">Loading...</option>';
        selectMapel.disabled = true;

        if (!id_kelas) {
            selectMapel.innerHTML = '<option value="">-- Pilih Kelas Dulu --</option>';
            return;
        }

        try {
            const response = await fetch(ENDPOINT.MAPEL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({ id_kelas, [csrfName]: csrfHash })
            });
            const data = await response.json();

            if (data.status === 'success' && data.mapel?.length) {
                selectMapel.innerHTML = '<option value="">-- Pilih Mata Pelajaran --</option>';
                data.mapel.forEach(m => {
                    selectMapel.innerHTML += `<option value="${m.id_mapel}">${m.nama_mapel}</option>`;
                });
                selectMapel.disabled = false;
            } else {
                selectMapel.innerHTML = '<option value="">-- Tidak ada mapel terhubung --</option>';
            }
        } catch (error) {
            selectMapel.innerHTML = '<option value="">Gagal memuat mapel</option>';
        }
    });

    // Load table
    async function loadTable(showSpinner = true) {
        const formData = new FormData(formFilter);
        formData.append(csrfName, csrfHash);

        if (!formData.get('tanggal') || !formData.get('id_kelas') || !formData.get('id_mapel')) {
            return;
        }

        if (showSpinner) {
            container.innerHTML = `
                <div class="text-center py-12 text-gray-500">
                    <svg class="w-8 h-8 mx-auto text-gray-400 animate-spin mb-2" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <p>Memuat data...</p>
                </div>
            `;
        }

        try {
            const response = await fetch(ENDPOINT.TABEL, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            const html = await response.text();
            container.innerHTML = html;
            paintAllRows();
        } catch (error) {
            container.innerHTML = '<div class="p-6 text-center text-red-500">Gagal memuat data. Silakan coba lagi.</div>';
        }
    }

    // Submit filter form
    formFilter.addEventListener('submit', async function(e) {
        e.preventDefault();
        btnGo.disabled = true;
        btnGo.innerHTML = `
            <svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Loading...
        `;
        await loadTable(true);
        btnGo.disabled = false;
        btnGo.innerHTML = `
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
            </svg>
            Tampilkan
        `;
    });

    // Handle checkbox change (delegated)
    container.addEventListener('change', async function(e) {
        if (!e.target.classList.contains('check-absen')) return;

        const cb = e.target;
        const id_siswa = cb.dataset.id_siswa;
        const kriteria = JSON.parse(cb.dataset.kriteria || '{}');
        const status = cb.value;
        const tr = cb.closest('tr');

        // Uncheck others in same group
        document.querySelectorAll(`input[name="absen_${id_siswa}"]`).forEach(other => {
            if (other !== cb) other.checked = false;
        });

        const baseData = {
            id_ta: kriteria.id_tahun_ajaran,
            id_kelas: kriteria.id_kelas,
            id_mapel: kriteria.id_mapel,
            tanggal: kriteria.tanggal,
            id_siswa
        };

        // If I or S, open modal
        if (status === 'I' || status === 'S') {
            cb.checked = false;
            prefillModal(baseData, status);
            return;
        }

        // Save H or A
        try {
            const response = await fetch(ENDPOINT.SAVE_ABSEN, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({ ...baseData, status, [csrfName]: csrfHash })
            });
            const data = await response.json();

            if (data.csrf_hash) csrfHash = data.csrf_hash;

            if (data.status === 'success') {
                showToast(data.message || 'Tersimpan', 'success');
                paintRow(tr);
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            showToast('Gagal menyimpan', 'error');
            cb.checked = !cb.checked;
        }
    });

    // Handle catatan button click (delegated)
    container.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-catatan');
        if (!btn) return;

        const id_siswa = btn.dataset.id_siswa;
        const kriteria = JSON.parse(btn.dataset.kriteria || '{}');
        const baseData = {
            id_ta: kriteria.id_tahun_ajaran,
            id_kelas: kriteria.id_kelas,
            id_mapel: kriteria.id_mapel,
            tanggal: kriteria.tanggal,
            id_siswa
        };
        prefillModal(baseData, 'I');
    });

    // Modal functions
    function resetModalUI() {
        formIS.reset();
        hidePreview();
        hapusBuktiInput.value = '0';
    }

    function hidePreview() {
        if (objectUrl) {
            URL.revokeObjectURL(objectUrl);
            objectUrl = null;
        }
        previewWrap.classList.add('hidden');
        imgPreview.src = '';
        linkBukti.href = '#';
    }

    async function prefillModal(baseData, statusDefault) {
        resetModalUI();
        formIS.querySelector('[name="id_ta"]').value = baseData.id_ta;
        formIS.querySelector('[name="id_kelas"]').value = baseData.id_kelas;
        formIS.querySelector('[name="id_mapel"]').value = baseData.id_mapel;
        formIS.querySelector('[name="tanggal"]').value = baseData.tanggal;
        formIS.querySelector('[name="id_siswa"]').value = baseData.id_siswa;
        formIS.querySelector('[name="status"]').value = statusDefault;

        try {
            const response = await fetch(ENDPOINT.DETAIL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({ ...baseData, [csrfName]: csrfHash })
            });
            const data = await response.json();

            if (data.status === 'success' && data.data) {
                const d = data.data;
                if (d.status === 'I' || d.status === 'S') {
                    formIS.querySelector('[name="status"]').value = d.status;
                }
                if (d.catatan) {
                    formIS.querySelector('[name="catatan"]').value = d.catatan;
                }
                if (d.bukti) {
                    linkBukti.href = d.bukti;
                    imgPreview.src = d.bukti;
                    previewWrap.classList.remove('hidden');
                }
            }
        } catch (error) {
            console.error('Error fetching detail:', error);
        }

        openModal();
    }

    // File input change
    fileInput.addEventListener('change', function() {
        const file = this.files?.[0];
        hapusBuktiInput.value = '0';

        if (!file) {
            hidePreview();
            return;
        }

        const validTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!validTypes.includes(file.type) || file.size > 4 * 1024 * 1024) {
            showToast('File tidak valid. Hanya JPG/PNG/WEBP, maks 4MB.', 'error');
            this.value = '';
            hidePreview();
            return;
        }

        if (objectUrl) URL.revokeObjectURL(objectUrl);
        objectUrl = URL.createObjectURL(file);
        imgPreview.src = objectUrl;
        linkBukti.href = objectUrl;
        previewWrap.classList.remove('hidden');
    });

    // Hapus bukti button
    document.getElementById('btnHapusBukti').addEventListener('click', function() {
        hapusBuktiInput.value = '1';
        fileInput.value = '';
        hidePreview();
    });

    // Submit form izin/sakit
    formIS.addEventListener('submit', async function(e) {
        e.preventDefault();

        const catatan = formIS.querySelector('[name="catatan"]').value.trim();
        if (!catatan) {
            showToast('Catatan wajib diisi', 'warning');
            return;
        }

        const fd = new FormData(this);
        fd.append(csrfName, csrfHash);

        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = `
            <svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Menyimpan...
        `;

        try {
            const response = await fetch(ENDPOINT.SAVE_IS, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: fd
            });
            const data = await response.json();

            if (data.csrf_hash) csrfHash = data.csrf_hash;

            if (data.status === 'success') {
                closeModal();
                showToast('Tersimpan', 'success');

                // Update checkbox
                const id_siswa = formIS.querySelector('[name="id_siswa"]').value;
                const status = formIS.querySelector('[name="status"]').value;
                document.querySelectorAll(`input[name="absen_${id_siswa}"]`).forEach(cb => {
                    cb.checked = cb.value === status;
                });

                // Reload table
                await loadTable(false);
            } else {
                throw new Error(data.message || 'Gagal menyimpan');
            }
        } catch (error) {
            showToast(error.message || 'Terjadi kesalahan', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = `
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Simpan
            `;
        }
    });
</script>
<?= $this->endSection() ?>

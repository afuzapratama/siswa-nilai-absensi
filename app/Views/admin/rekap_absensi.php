<?= $this->extend('layout/app') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-2xl font-bold text-gray-900"><?= $title ?></h1>
        <p class="mt-1 text-sm text-gray-500">Lihat rekap kehadiran siswa berdasarkan periode, kelas dan mata pelajaran</p>
    </div>

    <!-- Filter Card -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Laporan Kehadiran</h3>
        </div>
        <div class="card-body">
            <form id="filterRekapAbsensi" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Tanggal Mulai -->
                    <div class="form-group">
                        <label for="tanggal_mulai" class="form-label">Dari Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" 
                               class="form-input" 
                               id="tanggal_mulai" 
                               name="tanggal_mulai" 
                               value="<?= date('Y-m-01') ?>" 
                               required>
                    </div>

                    <!-- Tanggal Selesai -->
                    <div class="form-group">
                        <label for="tanggal_selesai" class="form-label">Sampai Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" 
                               class="form-input" 
                               id="tanggal_selesai" 
                               name="tanggal_selesai" 
                               value="<?= date('Y-m-t') ?>" 
                               required>
                    </div>

                    <!-- Pilih Kelas -->
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

                    <!-- Pilih Mata Pelajaran -->
                    <div class="form-group">
                        <label for="id_mapel" class="form-label">Pilih Mata Pelajaran <span class="text-red-500">*</span></label>
                        <select class="form-input" id="id_mapel" name="id_mapel" required disabled>
                            <option value="">-- Pilih Kelas Dulu --</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" id="btnTerapkan">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Terapkan
                </button>
            </form>
        </div>
    </div>

    <!-- Hasil Rekap Card -->
    <div class="card">
        <div class="card-header flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <h3 class="card-title">Hasil Rekap Kehadiran</h3>
            <div class="flex items-center gap-2">
                <button type="button" class="btn btn-success btn-sm" id="btnExportCSV" disabled>
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export CSV
                </button>
                <button type="button" class="btn btn-danger btn-sm" id="btnExportPDF" disabled>
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Export PDF
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div id="tabelRekapContainer" class="p-6">
                <div class="text-center py-12 text-gray-500">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <p>Silakan pilih filter di atas dan klik "Terapkan" untuk menampilkan rekap.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Legend -->
    <div class="bg-gray-50 rounded-lg p-4">
        <p class="text-xs font-medium text-gray-700 mb-2">Keterangan Bobot Kehadiran:</p>
        <div class="flex flex-wrap items-center gap-4 text-sm">
            <span class="flex items-center gap-2">
                <span class="w-4 h-4 bg-green-600 rounded"></span>
                <span>Hadir (H) = 100%</span>
            </span>
            <span class="flex items-center gap-2">
                <span class="w-4 h-4 bg-orange-500 rounded"></span>
                <span>Sakit (S) = 90%</span>
            </span>
            <span class="flex items-center gap-2">
                <span class="w-4 h-4 bg-yellow-500 rounded"></span>
                <span>Izin (I) = 70%</span>
            </span>
            <span class="flex items-center gap-2">
                <span class="w-4 h-4 bg-red-600 rounded"></span>
                <span>Alpa (A) = 0%</span>
            </span>
        </div>
        <p class="text-xs text-gray-500 mt-2">* Persentase kehadiran dihitung berdasarkan total poin dibagi jumlah pertemuan</p>
    </div>
</div>

<!-- Loading Modal -->
<div class="modal" id="loadingExport">
    <div class="modal-backdrop"></div>
    <div class="modal-container">
        <div class="modal-content max-w-xs">
            <div class="p-8 text-center">
                <svg class="w-10 h-10 mx-auto text-blue-500 animate-spin mb-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p class="text-gray-700 font-medium">Menyiapkan PDF...</p>
                <p class="text-sm text-gray-500 mt-1">Mohon tunggu sebentar</p>
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

    // Elements
    const formFilter = document.getElementById('filterRekapAbsensi');
    const selectKelas = document.getElementById('id_kelas');
    const selectMapel = document.getElementById('id_mapel');
    const btnTerapkan = document.getElementById('btnTerapkan');
    const container = document.getElementById('tabelRekapContainer');
    const btnExportCSV = document.getElementById('btnExportCSV');
    const btnExportPDF = document.getElementById('btnExportPDF');
    const loadingModal = document.getElementById('loadingExport');

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
            const response = await fetch(`${baseUrl}admin/absensi/ajax-get-mapel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({ id_kelas, [csrfName]: csrfHash })
            });
            const data = await response.json();

            if (data.csrf_hash) csrfHash = data.csrf_hash;

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

    // Submit filter - load rekap data
    formFilter.addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        formData.append(csrfName, csrfHash);

        // Disable buttons & show loading
        btnTerapkan.disabled = true;
        btnTerapkan.innerHTML = `
            <svg class="w-4 h-4 mr-2 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Menerapkan...
        `;
        btnExportCSV.disabled = true;
        btnExportPDF.disabled = true;

        container.innerHTML = `
            <div class="text-center py-12 text-gray-500">
                <svg class="w-8 h-8 mx-auto text-gray-400 animate-spin mb-2" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <p>Memuat data...</p>
            </div>
        `;

        try {
            const response = await fetch(`${baseUrl}admin/rekap-absensi/tampilkan-data`, {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            });
            const html = await response.text();
            container.innerHTML = html;
            
            // Enable export buttons
            btnExportCSV.disabled = false;
            btnExportPDF.disabled = false;
        } catch (error) {
            container.innerHTML = `
                <div class="p-6 text-center">
                    <div class="alert alert-danger">Gagal memuat data. Silakan coba lagi.</div>
                </div>
            `;
        } finally {
            btnTerapkan.disabled = false;
            btnTerapkan.innerHTML = `
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Terapkan
            `;
        }
    });

    // Helper to get export URL
    function getExportUrl(format) {
        const params = new URLSearchParams({
            tanggal_mulai: document.getElementById('tanggal_mulai').value,
            tanggal_selesai: document.getElementById('tanggal_selesai').value,
            id_kelas: selectKelas.value,
            id_mapel: selectMapel.value
        });
        return `${baseUrl}admin/rekap-absensi/${format}?${params}`;
    }

    // Export CSV
    btnExportCSV.addEventListener('click', function() {
        window.location.href = getExportUrl('export-csv');
    });

    // Export PDF with loading indicator
    btnExportPDF.addEventListener('click', function() {
        const originalHtml = this.innerHTML;
        this.disabled = true;
        this.innerHTML = `
            <svg class="w-4 h-4 mr-1.5 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Mengunduh...
        `;

        // Show loading modal
        loadingModal.classList.add('active');
        document.body.style.overflow = 'hidden';

        // Create hidden iframe to trigger download
        const token = 'dl_' + Date.now() + '_' + Math.random().toString(36).slice(2);
        const url = getExportUrl('export-pdf') + '&dl_token=' + token;
        
        const iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = url;
        document.body.appendChild(iframe);

        // Close loading after timeout (fallback for download managers)
        setTimeout(() => {
            loadingModal.classList.remove('active');
            document.body.style.overflow = '';
            this.disabled = false;
            this.innerHTML = originalHtml;
            iframe.remove();
            showToast('Download dimulai', 'info');
        }, 5000);
    });
</script>
<?= $this->endSection() ?>

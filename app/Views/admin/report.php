<?= $this->extend('layout/app') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-2xl font-semibold text-gray-900"><?= esc($title) ?></h1>
        <p class="text-sm text-gray-500 mt-1">Lihat dan export laporan nilai siswa berdasarkan penilaian yang dipilih</p>
    </div>

    <!-- Card Filter -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Filter Laporan Penilaian</h3>
        </div>
        <div class="card-body">
            <form id="filterLaporanNilai">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrf_field">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Pilih Kelas -->
                    <div class="form-group">
                        <label for="id_kelas" class="form-label">Pilih Kelas</label>
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
                        <label for="id_mapel" class="form-label">Pilih Mata Pelajaran</label>
                        <select class="form-input" id="id_mapel" name="id_mapel" required>
                            <option value="">-- Pilih Kelas Dulu --</option>
                        </select>
                    </div>

                    <!-- Pilih Judul Penilaian (Custom Multi-select) -->
                    <div class="form-group">
                        <label class="form-label">Pilih Judul Penilaian</label>
                        <div class="relative">
                            <button type="button" id="headerSelectBtn" 
                                class="form-input w-full text-left flex items-center justify-between disabled:bg-gray-100 disabled:cursor-not-allowed" disabled>
                                <span id="headerSelectLabel" class="truncate text-gray-500">-- Pilih Mapel Dulu --</span>
                                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <!-- Dropdown Menu -->
                            <div id="headerDropdown" class="hidden absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                <div class="sticky top-0 bg-white border-b p-2">
                                    <div class="flex gap-2">
                                        <button type="button" id="selectAllHeaders" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Pilih Semua</button>
                                        <span class="text-gray-300">|</span>
                                        <button type="button" id="deselectAllHeaders" class="text-xs text-gray-600 hover:text-gray-800 font-medium">Batal Semua</button>
                                    </div>
                                </div>
                                <div id="headerOptions" class="p-2 space-y-1">
                                    <!-- Options will be loaded via AJAX -->
                                </div>
                            </div>
                        </div>
                        <p class="form-hint" id="headerHint">Pilih satu atau lebih judul penilaian</p>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary" id="btnTampilkan">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Tampilkan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Card Hasil Laporan -->
    <div class="card">
        <div class="card-header flex items-center justify-between">
            <h3 class="card-title">Hasil Laporan Penilaian</h3>
            <div class="flex gap-2">
                <button type="button" class="btn btn-success btn-sm" id="btnExportCSV" disabled>
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export CSV
                </button>
                <button type="button" class="btn btn-danger btn-sm" id="btnExportPDF" disabled>
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    Export PDF
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Tempat tabel AJAX di-load -->
            <div id="tabelLaporanContainer">
                <div class="text-center text-gray-500 py-12">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    <p>Silakan pilih filter di atas dan klik "Tampilkan" untuk melihat laporan.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Loading PDF -->
    <div class="modal" id="loadingExport">
        <div class="modal-backdrop"></div>
        <div class="modal-container">
            <div class="modal-content max-w-xs">
                <div class="modal-body text-center py-8">
                    <div class="animate-spin rounded-full h-10 w-10 border-4 border-blue-500 border-t-transparent mx-auto mb-4"></div>
                    <p class="text-gray-600">Menyiapkan PDF...</p>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script type="module">
    // CSRF & Base URL
    const csrfName = document.querySelector('meta[name="csrf-name"]').content;
    let csrfHash = document.querySelector('meta[name="csrf-token"]').content;
    const baseUrl = document.querySelector('meta[name="base-url"]').content;

    // DOM Elements
    const selectKelas = document.getElementById('id_kelas');
    const selectMapel = document.getElementById('id_mapel');
    const headerSelectBtn = document.getElementById('headerSelectBtn');
    const headerDropdown = document.getElementById('headerDropdown');
    const headerOptions = document.getElementById('headerOptions');
    const headerSelectLabel = document.getElementById('headerSelectLabel');
    const filterForm = document.getElementById('filterLaporanNilai');
    const btnTampilkan = document.getElementById('btnTampilkan');
    const tabelContainer = document.getElementById('tabelLaporanContainer');
    const btnExportCSV = document.getElementById('btnExportCSV');
    const btnExportPDF = document.getElementById('btnExportPDF');
    
    // Selected headers storage
    let selectedHeaders = [];

    // Toggle dropdown
    headerSelectBtn.addEventListener('click', () => {
        if (!headerSelectBtn.disabled) {
            headerDropdown.classList.toggle('hidden');
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', (e) => {
        if (!headerSelectBtn.contains(e.target) && !headerDropdown.contains(e.target)) {
            headerDropdown.classList.add('hidden');
        }
    });

    // Select All / Deselect All
    document.getElementById('selectAllHeaders').addEventListener('click', () => {
        headerOptions.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = true);
        updateSelectedHeaders();
    });
    
    document.getElementById('deselectAllHeaders').addEventListener('click', () => {
        headerOptions.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);
        updateSelectedHeaders();
    });

    // Update selected headers label
    function updateSelectedHeaders() {
        const checked = headerOptions.querySelectorAll('input[type="checkbox"]:checked');
        selectedHeaders = Array.from(checked).map(cb => cb.value);
        
        if (selectedHeaders.length === 0) {
            headerSelectLabel.textContent = '-- Pilih Judul Penilaian --';
            headerSelectLabel.classList.add('text-gray-500');
            headerSelectLabel.classList.remove('text-gray-900');
        } else {
            headerSelectLabel.textContent = `${selectedHeaders.length} judul dipilih`;
            headerSelectLabel.classList.remove('text-gray-500');
            headerSelectLabel.classList.add('text-gray-900');
        }
    }

    // 1. AJAX: Ambil Mata Pelajaran saat Kelas dipilih
    selectKelas.addEventListener('change', async function() {
        const id_kelas = this.value;
        selectMapel.innerHTML = '<option value="">Loading...</option>';
        selectMapel.disabled = true;
        headerSelectBtn.disabled = true;
        headerSelectLabel.textContent = '-- Pilih Mapel Dulu --';
        headerSelectLabel.classList.add('text-gray-500');
        headerOptions.innerHTML = '';
        selectedHeaders = [];

        if (!id_kelas) {
            selectMapel.innerHTML = '<option value="">-- Pilih Kelas Dulu --</option>';
            selectMapel.disabled = false;
            return;
        }

        try {
            const formData = new FormData();
            formData.append('id_kelas', id_kelas);
            formData.append(csrfName, csrfHash);

            const response = await fetch(`${baseUrl}admin/report/ajax-get-mapel`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await response.json();
            
            if (data.csrf_hash) csrfHash = data.csrf_hash;
            
            selectMapel.innerHTML = '<option value="">-- Pilih Mata Pelajaran --</option>';
            selectMapel.disabled = false;
            
            if (data.status === 'success' && data.mapel) {
                data.mapel.forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m.id_mapel;
                    opt.textContent = m.nama_mapel;
                    selectMapel.appendChild(opt);
                });
            }
        } catch (err) {
            selectMapel.innerHTML = '<option value="">Gagal memuat mapel</option>';
            selectMapel.disabled = false;
        }
    });

    // 2. AJAX: Ambil Judul Penilaian saat Mapel dipilih
    selectMapel.addEventListener('change', async function() {
        const id_kelas = selectKelas.value;
        const id_mapel = this.value;
        
        headerOptions.innerHTML = '';
        headerSelectBtn.disabled = true;
        headerSelectLabel.textContent = '-- Pilih Mapel Dulu --';
        headerSelectLabel.classList.add('text-gray-500');
        selectedHeaders = [];

        if (!id_mapel || !id_kelas) {
            return;
        }

        try {
            const formData = new FormData();
            formData.append('id_kelas', id_kelas);
            formData.append('id_mapel', id_mapel);
            formData.append(csrfName, csrfHash);

            const response = await fetch(`${baseUrl}admin/report/get-judul`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const result = await response.json();
            
            if (result.csrf_hash) csrfHash = result.csrf_hash;

            const data = result.data || result; // Support both formats
            if (data && data.length > 0) {
                data.forEach(h => {
                    const div = document.createElement('div');
                    div.className = 'flex items-center';
                    div.innerHTML = `
                        <label class="flex items-center w-full p-2 hover:bg-gray-50 rounded cursor-pointer">
                            <input type="checkbox" name="id_header[]" value="${h.id_header}" 
                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">${escapeHtml(h.judul_penilaian)}</span>
                        </label>
                    `;
                    div.querySelector('input').addEventListener('change', updateSelectedHeaders);
                    headerOptions.appendChild(div);
                });
                
                headerSelectBtn.disabled = false;
                headerSelectLabel.textContent = '-- Pilih Judul Penilaian --';
            } else {
                headerOptions.innerHTML = '<div class="p-3 text-center text-gray-500 text-sm">Tidak ada data penilaian</div>';
                headerSelectLabel.textContent = '-- Tidak ada data --';
            }
        } catch (err) {
            headerOptions.innerHTML = '<div class="p-3 text-center text-red-500 text-sm">Gagal memuat data</div>';
            headerSelectLabel.textContent = '-- Gagal memuat --';
        }
    });

    // 3. Submit Form: Tampilkan Data
    filterForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (selectedHeaders.length === 0) {
            showToast('Pilih minimal satu judul penilaian!', 'warning');
            return;
        }

        const originalBtnText = btnTampilkan.innerHTML;
        btnTampilkan.disabled = true;
        btnTampilkan.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span> Menampilkan...';
        
        tabelContainer.innerHTML = `
            <div class="text-center text-gray-500 py-12">
                <div class="animate-spin rounded-full h-10 w-10 border-4 border-blue-500 border-t-transparent mx-auto mb-4"></div>
                <p>Memuat data...</p>
            </div>
        `;
        
        btnExportCSV.disabled = true;
        btnExportPDF.disabled = true;

        try {
            const formData = new FormData();
            formData.append('id_kelas', selectKelas.value);
            formData.append(csrfName, csrfHash);
            selectedHeaders.forEach(h => formData.append('id_header[]', h));

            const response = await fetch(`${baseUrl}admin/report/tampilkan-data`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const html = await response.text();
            
            // Update CSRF from response header if available
            const newCsrf = response.headers.get('X-CSRF-Hash');
            if (newCsrf) csrfHash = newCsrf;
            
            tabelContainer.innerHTML = html;
            btnExportCSV.disabled = false;
            btnExportPDF.disabled = false;
            
            // Reinitialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        } catch (err) {
            tabelContainer.innerHTML = '<div class="alert alert-danger">Gagal memuat data. Silakan coba lagi.</div>';
        } finally {
            btnTampilkan.disabled = false;
            btnTampilkan.innerHTML = originalBtnText;
            // Reinitialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
    });

    // Export URL builder
    function getExportUrl(format) {
        const params = new URLSearchParams();
        params.append('id_kelas', selectKelas.value);
        params.append('id_header', selectedHeaders.join(','));
        return `${baseUrl}admin/report/${format}?${params.toString()}`;
    }

    // 4. Export CSV
    btnExportCSV.addEventListener('click', () => {
        window.location.href = getExportUrl('export-csv');
    });

    // 5. Export PDF with loading modal
    btnExportPDF.addEventListener('click', function() {
        const modal = document.getElementById('loadingExport');
        const originalHtml = this.innerHTML;
        const btn = this;
        
        // UI: disable button + show modal
        btn.disabled = true;
        btn.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-1"></span> Mengunduh...';
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';

        const baseUrl = getExportUrl('export-pdf');
        const token = 'dl_' + Date.now() + '_' + Math.random().toString(36).slice(2);
        const url = addQuery(baseUrl, 'dl_token', token);

        // Clear old cookies and create hidden iframe
        clearDownloadCookie();
        const iframe = document.createElement('iframe');
        iframe.src = url;
        iframe.style.display = 'none';
        iframe.id = 'dlFrameTmp';
        document.body.appendChild(iframe);

        const start = Date.now();
        const hardTimeout = 120000; // 2 minutes
        const softCloseAt = 5000;   // 5 seconds fallback

        const timer = setInterval(() => {
            const elapsed = Date.now() - start;

            // Success: cookie detected
            if (hasDownloadCookie(token)) {
                clearInterval(timer);
                clearDownloadCookie();
                try { iframe.remove(); } catch (e) {}
                closeModal();
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                if (typeof lucide !== 'undefined') lucide.createIcons();
                showToast('PDF sedang diunduh.', 'success');
                return;
            }

            // Fallback: close loader after X seconds
            if (elapsed >= softCloseAt) {
                clearInterval(timer);
                try { iframe.remove(); } catch (e) {}
                closeModal();
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                if (typeof lucide !== 'undefined') lucide.createIcons();
                showToast('Download dimulai.', 'info');
                return;
            }

            // Hard timeout
            if (elapsed >= hardTimeout) {
                clearInterval(timer);
                try { iframe.remove(); } catch (e) {}
                closeModal();
                btn.disabled = false;
                btn.innerHTML = originalHtml;
                if (typeof lucide !== 'undefined') lucide.createIcons();
                showToast('Gagal mengunduh (timeout).', 'error');
            }
        }, 300);

        function closeModal() {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    });

    // Helper functions
    function addQuery(url, key, value) {
        const u = new URL(url, window.location.origin);
        u.searchParams.set(key, value);
        return u.toString();
    }

    function hasDownloadCookie(token) {
        const all = document.cookie ? document.cookie.split(';') : [];
        for (const pair of all) {
            const idx = pair.indexOf('=');
            const name = (idx > -1 ? pair.slice(0, idx) : pair).trim();
            const val = (idx > -1 ? pair.slice(idx + 1) : '').trim();
            if (!name) continue;
            if (name === 'dl_token' || name.endsWith('dl_token')) {
                try {
                    if (decodeURIComponent(val) === token) return true;
                } catch (_) {
                    if (val === token) return true;
                }
            }
        }
        return false;
    }

    function clearDownloadCookie() {
        const all = document.cookie ? document.cookie.split(';') : [];
        for (const pair of all) {
            const idx = pair.indexOf('=');
            const name = (idx > -1 ? pair.slice(0, idx) : pair).trim();
            if (name === 'dl_token' || name.endsWith('dl_token')) {
                document.cookie = name + '=; Max-Age=0; path=/';
            }
        }
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function showToast(message, type = 'info') {
        // Simple toast implementation
        const toast = document.createElement('div');
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        };
        toast.className = `fixed bottom-4 right-4 ${colors[type]} text-white px-4 py-2 rounded-lg shadow-lg z-50 transition-opacity duration-300`;
        toast.textContent = message;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
</script>
<?= $this->endSection() ?>

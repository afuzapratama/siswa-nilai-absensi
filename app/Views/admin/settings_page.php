<?= $this->extend('layout/app') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-2xl font-semibold text-gray-900"><?= esc($title) ?></h1>
        <p class="text-sm text-gray-500 mt-1">Konfigurasi umum dan bobot kehadiran untuk rekap absensi</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Settings Card -->
        <div class="lg:col-span-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Pengaturan Aplikasi</h3>
                </div>
                <div class="card-body">
                    <form id="formSettings" enctype="multipart/form-data">
                        <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>" id="csrf_field">

                        <!-- Informasi Sekolah -->
                        <div class="space-y-4">
                            <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Informasi Sekolah</h4>
                            
                            <div class="form-group">
                                <label for="nama_sekolah" class="form-label">Nama Sekolah</label>
                                <input type="text" class="form-input" id="nama_sekolah" name="nama_sekolah" 
                                       value="<?= esc($settings['nama_sekolah'] ?? 'SMK') ?>" 
                                       placeholder="Masukkan nama sekolah">
                                <p class="form-hint">Nama sekolah yang akan ditampilkan pada laporan</p>
                            </div>

                            <div class="form-group">
                                <label for="format_tanggal" class="form-label">Format Tanggal</label>
                                <select class="form-input" id="format_tanggal" name="format_tanggal">
                                    <option value="YYYY-MM-DD" <?= ($settings['format_tanggal'] ?? 'YYYY-MM-DD') == 'YYYY-MM-DD' ? 'selected' : '' ?>>
                                        YYYY-MM-DD (Contoh: 2025-10-31)
                                    </option>
                                    <option value="DD-MM-YYYY" <?= ($settings['format_tanggal'] ?? '') == 'DD-MM-YYYY' ? 'selected' : '' ?>>
                                        DD-MM-YYYY (Contoh: 31-10-2025)
                                    </option>
                                </select>
                                <p class="form-hint">Format tanggal yang digunakan dalam laporan</p>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="border-t border-gray-200 my-6"></div>

                        <!-- Bobot Kehadiran -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h4 class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Bobot Kehadiran</h4>
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">Untuk perhitungan rekap absensi</span>
                            </div>
                            
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="form-group">
                                    <label for="bobot_hadir" class="form-label flex items-center">
                                        <span class="w-6 h-6 rounded bg-green-100 text-green-700 text-xs font-bold flex items-center justify-center mr-2">H</span>
                                        Hadir
                                    </label>
                                    <input type="number" step="0.1" min="0" max="1" class="form-input text-center" 
                                           id="bobot_hadir" name="bobot_hadir" 
                                           value="<?= esc($settings['bobot_hadir'] ?? '1.0') ?>">
                                    <p class="form-hint text-center"><?= number_format((float)($settings['bobot_hadir'] ?? 1.0) * 100, 0) ?>%</p>
                                </div>
                                
                                <div class="form-group">
                                    <label for="bobot_sakit" class="form-label flex items-center">
                                        <span class="w-6 h-6 rounded bg-yellow-100 text-yellow-700 text-xs font-bold flex items-center justify-center mr-2">S</span>
                                        Sakit
                                    </label>
                                    <input type="number" step="0.1" min="0" max="1" class="form-input text-center" 
                                           id="bobot_sakit" name="bobot_sakit" 
                                           value="<?= esc($settings['bobot_sakit'] ?? '0.9') ?>">
                                    <p class="form-hint text-center"><?= number_format((float)($settings['bobot_sakit'] ?? 0.9) * 100, 0) ?>%</p>
                                </div>
                                
                                <div class="form-group">
                                    <label for="bobot_izin" class="form-label flex items-center">
                                        <span class="w-6 h-6 rounded bg-blue-100 text-blue-700 text-xs font-bold flex items-center justify-center mr-2">I</span>
                                        Izin
                                    </label>
                                    <input type="number" step="0.1" min="0" max="1" class="form-input text-center" 
                                           id="bobot_izin" name="bobot_izin" 
                                           value="<?= esc($settings['bobot_izin'] ?? '0.7') ?>">
                                    <p class="form-hint text-center"><?= number_format((float)($settings['bobot_izin'] ?? 0.7) * 100, 0) ?>%</p>
                                </div>
                                
                                <div class="form-group">
                                    <label for="bobot_alpa" class="form-label flex items-center">
                                        <span class="w-6 h-6 rounded bg-red-100 text-red-700 text-xs font-bold flex items-center justify-center mr-2">A</span>
                                        Alpa
                                    </label>
                                    <input type="number" step="0.1" min="0" max="1" class="form-input text-center" 
                                           id="bobot_alpa" name="bobot_alpa" 
                                           value="<?= esc($settings['bobot_alpa'] ?? '0.0') ?>">
                                    <p class="form-hint text-center"><?= number_format((float)($settings['bobot_alpa'] ?? 0.0) * 100, 0) ?>%</p>
                                </div>
                            </div>
                        </div>

                        <!-- Divider -->
                        <div class="border-t border-gray-200 my-6"></div>

                        <!-- Submit Button -->
                        <div class="flex justify-end">
                            <button type="submit" class="btn btn-primary" id="btnSimpan">
                                <i data-lucide="save" class="w-4 h-4 mr-2"></i>
                                Simpan Pengaturan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Info Sidebar -->
        <div class="space-y-6">
            <!-- Info Bobot -->
            <div class="card">
                <div class="card-header bg-blue-50">
                    <h3 class="card-title text-blue-700">
                        <i data-lucide="info" class="w-4 h-4 mr-2 inline"></i>
                        Tentang Bobot Kehadiran
                    </h3>
                </div>
                <div class="card-body text-sm text-gray-600 space-y-3">
                    <p>Bobot kehadiran digunakan untuk menghitung persentase kehadiran siswa pada rekap absensi.</p>
                    <p><strong>Rumus:</strong></p>
                    <code class="block bg-gray-100 p-2 rounded text-xs">
                        Poin = (H × bobot_H) + (S × bobot_S) + (I × bobot_I) + (A × bobot_A)
                    </code>
                    <p class="mt-2"><strong>Contoh perhitungan:</strong></p>
                    <ul class="list-disc list-inside text-xs space-y-1">
                        <li>5 Hadir × 1.0 = 5.0</li>
                        <li>2 Sakit × 0.9 = 1.8</li>
                        <li>1 Izin × 0.7 = 0.7</li>
                        <li>0 Alpa × 0.0 = 0.0</li>
                        <li><strong>Total:</strong> 7.5 / 8 hari = 93.75%</li>
                    </ul>
                </div>
            </div>

            <!-- Quick Tips -->
            <div class="card">
                <div class="card-header bg-amber-50">
                    <h3 class="card-title text-amber-700">
                        <i data-lucide="lightbulb" class="w-4 h-4 mr-2 inline"></i>
                        Tips
                    </h3>
                </div>
                <div class="card-body text-sm text-gray-600 space-y-2">
                    <p>• Bobot berkisar antara <strong>0.0</strong> hingga <strong>1.0</strong></p>
                    <p>• Nilai 1.0 berarti 100% dihitung sebagai hadir</p>
                    <p>• Nilai 0.0 berarti tidak dihitung sama sekali</p>
                    <p>• Sesuaikan bobot sesuai kebijakan sekolah</p>
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

    const form = document.getElementById('formSettings');
    const btnSimpan = document.getElementById('btnSimpan');

    // Update percentage hints when bobot changes
    ['bobot_hadir', 'bobot_sakit', 'bobot_izin', 'bobot_alpa'].forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener('input', function() {
                const hint = this.parentElement.querySelector('.form-hint');
                if (hint) {
                    const val = parseFloat(this.value) || 0;
                    hint.textContent = (val * 100).toFixed(0) + '%';
                }
            });
        }
    });

    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        formData.set(csrfName, csrfHash);
        
        const originalBtnText = btnSimpan.innerHTML;
        btnSimpan.disabled = true;
        btnSimpan.innerHTML = '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span> Menyimpan...';

        try {
            const response = await fetch(`${baseUrl}admin/settings/save`, {
                method: 'POST',
                body: formData
            });
            const data = await response.json();
            
            if (data.csrf_hash) csrfHash = data.csrf_hash;
            
            if (data.status === 'success') {
                showToast(data.message || 'Pengaturan berhasil disimpan.', 'success');
            } else {
                showToast(data.message || 'Gagal menyimpan data.', 'error');
            }
        } catch (err) {
            showToast('Terjadi kesalahan server.', 'error');
        } finally {
            btnSimpan.disabled = false;
            btnSimpan.innerHTML = originalBtnText;
            // Reinitialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
    });

    function showToast(message, type = 'info') {
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

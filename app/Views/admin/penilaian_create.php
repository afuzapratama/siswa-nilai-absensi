<?= $this->extend('layout/app') ?>

<?= $this->section('content') ?>
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Buat Form Penilaian Baru</h1>
            <p class="mt-1 text-sm text-gray-500">Pilih kelas dan mata pelajaran untuk membuat form penilaian</p>
        </div>
        <a href="<?= site_url('admin/penilaian') ?>" class="btn btn-secondary">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Kembali
        </a>
    </div>

    <!-- Form Card -->
    <div class="card max-w-2xl">
        <div class="card-body">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="text-sm text-blue-700">
                        <p class="font-medium mb-1">Petunjuk:</p>
                        <p>Pilih kelas, mata pelajaran, dan beri judul untuk form penilaian. Setelah disimpan, Anda akan diarahkan ke halaman input nilai.</p>
                    </div>
                </div>
            </div>

            <form action="<?= site_url('admin/penilaian/save-header') ?>" method="POST" class="space-y-6">
                <?= csrf_field() ?>

                <!-- 1. Pilih Kelas -->
                <div class="form-group">
                    <label for="id_kelas" class="form-label">
                        <span class="flex items-center gap-2">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-600 text-xs font-bold">1</span>
                            Pilih Kelas (Tahun Aktif) <span class="text-red-500">*</span>
                        </span>
                    </label>
                    <select class="form-input" id="id_kelas" name="id_kelas" required>
                        <option value="">-- Pilih Kelas --</option>
                        <?php foreach ($kelas_list as $kelas) : ?>
                            <option value="<?= $kelas['id_kelas'] ?>" <?= (old('id_kelas') == $kelas['id_kelas']) ? 'selected' : '' ?>>
                                <?= esc($kelas['nama_kelas']) ?> (<?= esc($kelas['kode_kelas']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- 2. Pilih Mata Pelajaran -->
                <div class="form-group">
                    <label for="id_mapel" class="form-label">
                        <span class="flex items-center gap-2">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-600 text-xs font-bold">2</span>
                            Pilih Mata Pelajaran <span class="text-red-500">*</span>
                        </span>
                    </label>
                    <select class="form-input" id="id_mapel" name="id_mapel" required disabled>
                        <option value="">-- Pilih Kelas Dulu --</option>
                    </select>
                    <p class="form-hint" id="mapelHint">Pilih kelas terlebih dahulu untuk melihat daftar mata pelajaran</p>
                </div>

                <!-- 3. Judul Penilaian -->
                <div class="form-group">
                    <label for="judul_penilaian" class="form-label">
                        <span class="flex items-center gap-2">
                            <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-100 text-blue-600 text-xs font-bold">3</span>
                            Judul Penilaian <span class="text-red-500">*</span>
                        </span>
                    </label>
                    <input type="text" 
                           class="form-input" 
                           id="judul_penilaian" 
                           name="judul_penilaian" 
                           value="<?= old('judul_penilaian') ?>" 
                           placeholder="Contoh: Ujian Praktek Instalasi Jaringan" 
                           required>
                    <p class="form-hint">Beri nama yang jelas dan deskriptif untuk form penilaian ini</p>
                </div>

                <?php if (session()->has('errors')) : ?>
                    <div class="alert alert-danger">
                        <ul class="list-disc list-inside">
                            <?php foreach (session('errors') as $error) : ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                <?php endif ?>

                <hr class="border-gray-200">

                <div class="flex items-center justify-end gap-3">
                    <a href="<?= site_url('admin/penilaian') ?>" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Simpan dan Lanjut Input Nilai
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
    
    const selectKelas = document.getElementById('id_kelas');
    const selectMapel = document.getElementById('id_mapel');
    const mapelHint = document.getElementById('mapelHint');
    
    function updateCsrf(newHash) {
        if (newHash) {
            csrfHash = newHash;
            document.querySelector('meta[name="csrf-token"]').content = newHash;
        }
    }
    
    selectKelas.addEventListener('change', async function() {
        const id_kelas = this.value;
        
        // Reset mapel dropdown
        selectMapel.innerHTML = '<option value="">Loading...</option>';
        selectMapel.disabled = true;
        
        if (!id_kelas) {
            selectMapel.innerHTML = '<option value="">-- Pilih Kelas Dulu --</option>';
            mapelHint.textContent = 'Pilih kelas terlebih dahulu untuk melihat daftar mata pelajaran';
            return;
        }
        
        try {
            const response = await fetch(`${baseUrl}admin/penilaian/get-mapel`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({ id_kelas: id_kelas, [csrfName]: csrfHash })
            });
            
            const data = await response.json();
            updateCsrf(data.csrf_hash);
            
            if (data.status === 'success' && data.mapel.length > 0) {
                selectMapel.innerHTML = '<option value="">-- Pilih Mata Pelajaran --</option>';
                data.mapel.forEach(m => {
                    selectMapel.innerHTML += `<option value="${m.id_mapel}">${m.nama_mapel}</option>`;
                });
                selectMapel.disabled = false;
                mapelHint.textContent = `${data.mapel.length} mata pelajaran tersedia untuk kelas ini`;
            } else {
                selectMapel.innerHTML = '<option value="">-- Tidak ada mapel terhubung --</option>';
                mapelHint.textContent = 'Silakan hubungkan mata pelajaran ke kelas ini di menu Mata Pelajaran';
            }
        } catch (error) {
            selectMapel.innerHTML = '<option value="">Gagal memuat mapel</option>';
            mapelHint.textContent = 'Terjadi kesalahan saat memuat data';
        }
    });
</script>
<?= $this->endSection() ?>

<?= $this->extend('layout/app') ?>

<?= $this->section('content') ?>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <!-- Total Siswa -->
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <div>
                <p class="stat-card-label">Total Siswa (Aktif)</p>
                <p class="stat-card-value"><?= esc($stats['total_siswa']) ?></p>
            </div>
            <div class="stat-card-icon bg-blue-100">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
        </div>
    </div>
    
    <!-- Total Kelas -->
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <div>
                <p class="stat-card-label">Total Kelas (Aktif)</p>
                <p class="stat-card-value"><?= esc($stats['total_kelas']) ?></p>
            </div>
            <div class="stat-card-icon bg-emerald-100">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
        </div>
    </div>
    
    <!-- Total Mapel -->
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <div>
                <p class="stat-card-label">Total Mapel (Aktif)</p>
                <p class="stat-card-value"><?= esc($stats['total_mapel']) ?></p>
            </div>
            <div class="stat-card-icon bg-violet-100">
                <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
            </div>
        </div>
    </div>
    
    <!-- Tahun Ajaran Aktif -->
    <div class="stat-card">
        <div class="flex items-center justify-between">
            <div>
                <p class="stat-card-label">Tahun Ajaran Aktif</p>
                <p class="stat-card-value text-lg sm:text-xl"><?= esc($stats['ta_aktif']) ?></p>
            </div>
            <div class="stat-card-icon bg-amber-100">
                <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Data Tables -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Top 5 Nilai Terbaik -->
    <div class="card">
        <div class="card-header">
            <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                </svg>
                Top 5 Nilai Terbaik
            </h2>
        </div>
        <div class="card-body p-0">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-12">No</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th class="text-center">Rata-rata</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($top_nilai)) : ?>
                            <tr>
                                <td colspan="4" class="text-center text-gray-500 py-8">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Belum ada data nilai
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php $no = 1; ?>
                            <?php foreach ($top_nilai as $siswa) : ?>
                                <tr>
                                    <td class="text-center">
                                        <?php if ($no <= 3): ?>
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold <?= $no == 1 ? 'bg-amber-100 text-amber-700' : ($no == 2 ? 'bg-gray-200 text-gray-700' : 'bg-orange-100 text-orange-700') ?>">
                                                <?= $no ?>
                                            </span>
                                        <?php else: ?>
                                            <?= $no ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="font-medium text-gray-900"><?= esc($siswa['nama_siswa']) ?></td>
                                    <td class="text-gray-500"><?= esc($siswa['nama_kelas']) ?></td>
                                    <td class="text-center">
                                        <span class="badge-success"><?= number_format($siswa['rata_rata_semua_nilai'], 1) ?></span>
                                    </td>
                                </tr>
                            <?php $no++; endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Top 5 Kehadiran -->
    <div class="card">
        <div class="card-header">
            <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Top 5 Kehadiran
            </h2>
        </div>
        <div class="card-body p-0">
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="w-12">No</th>
                            <th>Nama Siswa</th>
                            <th>Kelas</th>
                            <th class="text-center">Kehadiran</th>
                            <th class="text-center">Alpa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($top_absensi)) : ?>
                            <tr>
                                <td colspan="5" class="text-center text-gray-500 py-8">
                                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                    Belum ada data absensi
                                </td>
                            </tr>
                        <?php else : ?>
                            <?php $no = 1; ?>
                            <?php foreach ($top_absensi as $siswa) : ?>
                                <tr>
                                    <td class="text-center">
                                        <?php if ($no <= 3): ?>
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-xs font-bold <?= $no == 1 ? 'bg-amber-100 text-amber-700' : ($no == 2 ? 'bg-gray-200 text-gray-700' : 'bg-orange-100 text-orange-700') ?>">
                                                <?= $no ?>
                                            </span>
                                        <?php else: ?>
                                            <?= $no ?>
                                        <?php endif; ?>
                                    </td>
                                    <td class="font-medium text-gray-900"><?= esc($siswa['nama_siswa']) ?></td>
                                    <td class="text-gray-500"><?= esc($siswa['nama_kelas']) ?></td>
                                    <td class="text-center">
                                        <span class="badge-success"><?= number_format($siswa['persentase_kehadiran'], 1) ?>%</span>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($siswa['total_alpa'] > 0): ?>
                                            <span class="badge-danger"><?= esc($siswa['total_alpa']) ?></span>
                                        <?php else: ?>
                                            <span class="badge-gray">0</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php $no++; endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="mt-6">
    <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Aksi Cepat</h3>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <a href="<?= site_url('admin/penilaian/create') ?>" class="card p-4 hover:shadow-md transition-shadow group">
            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-3 group-hover:bg-blue-200 transition-colors">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700">Buat Penilaian</span>
            </div>
        </a>
        
        <a href="<?= site_url('admin/absensi') ?>" class="card p-4 hover:shadow-md transition-shadow group">
            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center mb-3 group-hover:bg-emerald-200 transition-colors">
                    <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700">Input Absensi</span>
            </div>
        </a>
        
        <a href="<?= site_url('admin/siswa') ?>" class="card p-4 hover:shadow-md transition-shadow group">
            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 bg-violet-100 rounded-xl flex items-center justify-center mb-3 group-hover:bg-violet-200 transition-colors">
                    <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700">Tambah Siswa</span>
            </div>
        </a>
        
        <a href="<?= site_url('admin/report') ?>" class="card p-4 hover:shadow-md transition-shadow group">
            <div class="flex flex-col items-center text-center">
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center mb-3 group-hover:bg-amber-200 transition-colors">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <span class="text-sm font-medium text-gray-700">Lihat Laporan</span>
            </div>
        </a>
    </div>
</div>

<?= $this->endSection() ?>

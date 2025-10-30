<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800"><?= $title ?></h1>
    </div>

    <!-- Content Row (Kartu Statistik) -->
    <div class="row">

        <!-- Total Siswa (Tahun Ajaran Aktif) -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Siswa (Aktif)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= esc($stats['total_siswa']) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Kelas (Tahun Ajaran Aktif) -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total Kelas (Aktif)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= esc($stats['total_kelas']) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-school fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Mata Pelajaran (Tahun Ajaran Aktif) -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Mapel (Aktif)
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= esc($stats['total_mapel']) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tahun Ajaran Aktif -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Tahun Ajaran Aktif</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800" style="font-size: 1.1rem;"><?= esc($stats['ta_aktif']) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row (Statistik) -->
    <div class="row">

        <!-- Top 5 Nilai Terbaik -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top 5 Nilai Terbaik (Tahun Ajaran Aktif)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" width="100%" cellspacing="0" style="font-size: 0.9rem;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th class="text-center">Rata-rata Nilai</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($top_nilai)) : ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Belum ada data nilai.</td>
                                    </tr>
                                <?php else : ?>
                                    <?php $no = 1; ?>
                                    <?php foreach ($top_nilai as $siswa) : ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= esc($siswa['nama_siswa']) ?></td>
                                            <td><?= esc($siswa['nama_kelas']) ?></td>
                                            <td class="text-center font-weight-bold text-success"><?= number_format($siswa['rata_rata_semua_nilai'], 1) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top 5 Kehadiran (Paling Sedikit Alpa) -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Top 5 Kehadiran (Tahun Ajaran Aktif)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" width="100%" cellspacing="0" style="font-size: 0.9rem;">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Siswa</th>
                                    <th>Kelas</th>
                                    <th class="text-center">Persentase</th>
                                    <th class="text-center">Alpa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($top_absensi)) : ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Belum ada data absensi.</td>
                                    </tr>
                                <?php else : ?>
                                    <?php $no = 1; ?>
                                    <?php foreach ($top_absensi as $siswa) : ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><?= esc($siswa['nama_siswa']) ?></td>
                                            <td><?= esc($siswa['nama_kelas']) ?></td>
                                            <td class="text-center font-weight-bold text-success"><?= number_format($siswa['persentase_kehadiran'], 1) ?>%</td>
                                            <td class="text-center text-danger"><?= esc($siswa['total_alpa']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>


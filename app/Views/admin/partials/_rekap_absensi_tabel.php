
<?php if (empty($rekap_data)) : ?>
    <div class="alert alert-warning">Tidak ada data absensi yang ditemukan untuk filter ini.</div>
<?php else : ?>
    <div class="table-responsive">
        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
            <thead>
                <tr class="text-center">
                    <th>No</th>
                    <th>Nama Siswa</th>
                    <th>NIS</th>
                    <th>H (Hadir)</th>
                    <th>I (Izin)</th>
                    <th>S (Sakit)</th>
                    <th>A (Alpa)</th>
                    <th>Total Poin</th>
                    <th>Persentase (%)</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; ?>
                <?php foreach ($rekap_data as $siswa) : ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= esc($siswa['nama_siswa']) ?></td>
                        <td><?= esc($siswa['nis']) ?></td>
                        <td class="text-center"><?= esc($siswa['H']) ?></td>
                        <td class="text-center"><?= esc($siswa['I']) ?></td>
                        <td class="text-center"><?= esc($siswa['S']) ?></td>
                        <td class="text-center"><?= esc($siswa['A']) ?></td>
                        <td class="text-center"><?= esc(number_format($siswa['total_poin'], 1)) ?></td>
                        <td class="text-center 
                            <?php if ($siswa['persentase'] < 75) echo 'text-danger font-weight-bold'; ?>
                            <?php if ($siswa['persentase'] >= 90) echo 'text-success'; ?>
                        ">
                            <?= esc(number_format($siswa['persentase'], 1)) ?>%
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>


<?php
// File ini (app/Views/admin/partials/report_table.php)
// adalah "view" kecil yang dipanggil oleh AJAX untuk merender tabel
?>

<div class="table-responsive">
    <table class="table table-bordered table-hover" style="font-size: 0.9rem;">
        <thead class="thead-light text-center">
            <tr>
                <!-- Kolom Statis -->
                <th rowspan="2" class="align-middle">No</th>
                <th rowspan="2" class="align-middle text-left" style="min-width: 200px;">Nama Siswa</th>
                <th rowspan="2" class="align-middle">NIS</th>

                <!-- Kolom Dinamis (Judul Penilaian) -->
                <?php foreach ($headers as $header) : ?>
                    <?php $kolomCount = count($kolom_map[$header['id_header']]); ?>
                    <th colspan="<?= $kolomCount + 1 // +1 untuk Rata-rata header ?>">
                        <?= esc($header['judul_penilaian']) ?> (<?= esc($header['nama_mapel']) ?>)
                    </th>
                <?php endforeach; ?>

                <!-- Kolom Total Rata-rata -->
                <th rowspan="2" class="align-middle" style="min-width: 80px;">Rata-rata Total</th>
            </tr>
            <tr>
                <!-- Kolom Dinamis (N1, N2, Rata-rata per Header) -->
                <?php foreach ($headers as $header) : ?>
                    <?php foreach ($kolom_map[$header['id_header']] as $kolom) : ?>
                        <th class="align-middle"><?= esc($kolom['nama_kolom']) ?></th>
                    <?php endforeach; ?>
                    <th class="align-middle table-info" style="min-width: 80px;">Rata-rata</th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach ($siswa as $s) : ?>
                <?php $total_nilai_siswa = 0;
                $total_pembagi_siswa = 0; ?>
                <tr>
                    <!-- Info Siswa -->
                    <td class="text-center"><?= $no++ ?></td>
                    <td class="text-left"><?= esc($s['nama_siswa']) ?></td>
                    <td class="text-center"><?= esc($s['nis']) ?></td>

                    <!-- Nilai Dinamis -->
                    <?php foreach ($headers as $header) : ?>
                        <?php $total_nilai_header = 0;
                        $total_pembagi_header = 0; ?>

                        <!-- Nilai N1, N2, ... -->
                        <?php foreach ($kolom_map[$header['id_header']] as $kolom) : ?>
                            <?php
                            $id_h = $header['id_header'];
                            $id_k = $kolom['id_kolom'];
                            $id_s = $s['id_siswa'];
                            
                            // Ambil nilai dari map
                            $nilai = $nilai_map[$id_s][$id_h][$id_k] ?? null;
                            
                            if (is_numeric($nilai)) {
                                $total_nilai_header += $nilai;
                                $total_pembagi_header++;
                            }
                            ?>
                            <td class="text-center"><?= esc($nilai) ?></td>
                        <?php endforeach; ?>

                        <!-- Rata-rata per Header -->
                        <td class="text-center table-info font-weight-bold">
                            <?php
                            if ($total_pembagi_header > 0) {
                                $rata_rata_header = $total_nilai_header / $total_pembagi_header;
                                echo number_format($rata_rata_header, 1);
                                
                                // Tambahkan ke total siswa
                                $total_nilai_siswa += $rata_rata_header;
                                $total_pembagi_siswa++;
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                    <?php endforeach; ?>

                    <!-- Rata-rata Total Siswa -->
                    <td class="text-center table-primary font-weight-bold">
                        <?php
                        if ($total_pembagi_siswa > 0) {
                            $rata_rata_total = $total_nilai_siswa / $total_pembagi_siswa;
                            echo number_format($rata_rata_total, 1);
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>

            <?php if (empty($siswa)) : ?>
                <tr>
                    <td colspan="<?= $total_kolom_dinamis + 4 ?>" class="text-center">
                        Tidak ada data siswa di kelas ini.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

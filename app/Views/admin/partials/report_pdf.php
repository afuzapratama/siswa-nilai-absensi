<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Penilaian Siswa</title>
    <style>
        /* [WAJIB] Atur halaman PDF jadi Landscape */
        @page {
            size: A4 landscape;
            margin: 20px;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px; /* Ukuran font lebih kecil untuk PDF */
        }

        h1 {
            text-align: center;
            margin-bottom: 5px;
            font-size: 16px;
        }
        
        h2 {
            text-align: center;
            margin-top: 0;
            margin-bottom: 20px;
            font-size: 14px;
            font-weight: normal;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px; /* Font di tabel lebih kecil lagi */
        }

        th, td {
            border: 1px solid #333;
            padding: 4px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
            white-space: nowrap;
        }
        
        td.text-left {
            text-align: left;
            white-space: nowrap; /* Agar nama tidak terpotong */
        }

        .align-middle {
            vertical-align: middle;
        }

        /* Warna highlight (opsional, tapi bagus) */
        .highlight-header {
            background-color: #e0e0e0;
            font-weight: bold;
        }
        
        .highlight-total {
            background-color: #cce5ff;
            font-weight: bold;
        }

    </style>
</head>
<body>

    <h1>Laporan Penilaian Siswa</h1>
    <h2>Kelas: <?= esc($kelasInfo['nama_kelas']) ?> (Tahun Ajaran: <?= esc($tahun_ajaran) ?>)</h2>

    <table>
        <thead>
            <tr>
                <!-- Kolom Statis -->
                <th rowspan="2" class="align-middle">No</th>
                <th rowspan="2" class="align-middle text-left">Nama Siswa</th>
                <th rowspan="2" class="align-middle">NIS</th>

                <!-- Kolom Dinamis (Judul Penilaian) -->
                <?php foreach ($headers as $header) : ?>
                    <?php $kolomCount = count($kolom_map[$header['id_header']]); ?>
                    <th colspan="<?= $kolomCount + 1 // +1 untuk Rata-rata header ?>">
                        <?= esc($header['judul_penilaian']) ?> (<?= esc($header['nama_mapel']) ?>)
                    </th>
                <?php endforeach; ?>

                <!-- Kolom Total Rata-rata -->
                <th rowspan="2" class="align-middle">Rata-rata Total</th>
            </tr>
            <tr>
                <!-- Kolom Dinamis (N1, N2, Rata-rata per Header) -->
                <?php foreach ($headers as $header) : ?>
                    <?php foreach ($kolom_map[$header['id_header']] as $kolom) : ?>
                        <th class="align-middle"><?= esc($kolom['nama_kolom']) ?></th>
                    <?php endforeach; ?>
                    <th class="align-middle highlight-header">Rata-rata</th>
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
                    <td><?= $no++ ?></td>
                    <td class="text-left"><?= esc($s['nama_siswa']) ?></td>
                    <td><?= esc($s['nis']) ?></td>

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
                            
                            $nilai = $nilai_map[$id_s][$id_h][$id_k] ?? null;
                            
                            if (is_numeric($nilai)) {
                                $total_nilai_header += $nilai;
                                $total_pembagi_header++;
                            }
                            ?>
                            <td><?= esc($nilai) ?></td>
                        <?php endforeach; ?>

                        <!-- Rata-rata per Header -->
                        <td class="highlight-header">
                            <?php
                            if ($total_pembagi_header > 0) {
                                $rata_rata_header = $total_nilai_header / $total_pembagi_header;
                                echo number_format($rata_rata_header, 1);
                                
                                $total_nilai_siswa += $rata_rata_header;
                                $total_pembagi_siswa++;
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                    <?php endforeach; ?>

                    <!-- Rata-rata Total Siswa -->
                    <td class="highlight-total">
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
                    <td colspan="100%" class="text-center">
                        Tidak ada data siswa di kelas ini.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>


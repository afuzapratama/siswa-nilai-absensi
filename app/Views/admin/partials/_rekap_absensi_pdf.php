<!-- 
File ini adalah PARTIAL VIEW.
Hanya berisi template HTML sederhana untuk di-render oleh DomPDF.
-->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Kehadiran</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 10px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .text-danger { color: #dc3545; }
        .font-weight-bold { font-weight: bold; }
        
        h1, h2, h3, h4, h5 {
            margin: 5px 0;
        }

        h2 { font-size: 16px; }
        h3 { font-size: 14px; }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #666;
            padding: 5px;
            vertical-align: top;
        }
        thead th {
            background-color: #f2f2f2;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="text-center">
        <h2>REKAP KEHADIRAN SISWA</h2>
        <h3><?= esc($info['nama_sekolah'] ?? 'NAMA SEKOLAH') ?></h3>
    </div>

    <hr>

    <table>
        <tr>
            <td style="width: 15%; border: 0;"><strong>Kelas</strong></td>
            <td style="width: 35%; border: 0;">: <?= esc($info['kelas_info']['nama_kelas'] ?? 'N/A') ?></td>
            <td style="width: 15%; border: 0;"><strong>Periode</strong></td>
            <td style="width: 35%; border: 0;">: <?= esc($info['tgl_mulai']) ?> s/d <?= esc($info['tgl_selesai']) ?></td>
        </tr>
        <tr>
            <td style="border: 0;"><strong>Mata Pelajaran</strong></td>
            <td style="border: 0;">: <?= esc($info['mapel_info']['nama_mapel'] ?? 'N/A') ?></td>
            <td style="border: 0;"><strong>Tahun Ajaran</strong></td>
            <td style="border: 0;">: <?= esc($info['ta_aktif']['tahun_ajaran'] ?? 'N/A') ?> (<?= esc($info['ta_aktif']['semester'] ?? 'N/A') ?>)</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
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
            <?php if (empty($rekap_data)) : ?>
                <tr>
                    <td colspan="9" class="text-center">Tidak ada data absensi yang ditemukan.</td>
                </tr>
            <?php else : ?>
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
                        ">
                            <?= esc(number_format($siswa['persentase'], 1)) ?>%
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>


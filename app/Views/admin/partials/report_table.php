<?php
/**
 * Partial view untuk tabel laporan nilai (Tailwind CSS version)
 * Dipanggil via AJAX dari report_new.php
 * 
 * Data yang diterima:
 * - $headers: array judul penilaian
 * - $kolom_map: array kolom per header [id_header => [kolom...]]
 * - $nilai_map: array nilai [id_siswa => [id_header => [id_kolom => nilai]]]
 * - $siswa: array siswa
 * - $total_kolom_dinamis: total kolom untuk colspan
 */
?>

<?php if (empty($siswa)) : ?>
    <div class="text-center text-gray-500 py-12">
        <i data-lucide="users" class="w-12 h-12 mx-auto mb-3 text-gray-300"></i>
        <p>Tidak ada data siswa di kelas ini.</p>
    </div>
<?php else : ?>
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead>
            <!-- Baris Header 1: Judul Penilaian -->
            <tr class="bg-gray-50">
                <!-- Kolom Statis -->
                <th rowspan="2" class="px-3 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider border-r align-middle">No</th>
                <th rowspan="2" class="px-3 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-r align-middle min-w-[200px]">Nama Siswa</th>

                <!-- Kolom Dinamis (Judul Penilaian) -->
                <?php foreach ($headers as $header) : ?>
                    <?php $kolomCount = count($kolom_map[$header['id_header']]); ?>
                    <th colspan="<?= $kolomCount + 1 ?>" class="px-3 py-2 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider border-r bg-blue-50">
                        <?= esc($header['judul_penilaian']) ?>
                        <span class="block text-[10px] font-normal text-gray-500 normal-case"><?= esc($header['nama_mapel']) ?></span>
                    </th>
                <?php endforeach; ?>

                <!-- Kolom Total Rata-rata -->
                <th rowspan="2" class="px-3 py-3 text-center text-xs font-semibold text-white uppercase tracking-wider align-middle bg-blue-600 min-w-[80px]">
                    Rata-rata<br>Total
                </th>
            </tr>
            
            <!-- Baris Header 2: N1, N2, ..., Rata-rata -->
            <tr class="bg-gray-50">
                <?php foreach ($headers as $header) : ?>
                    <?php foreach ($kolom_map[$header['id_header']] as $kolom) : ?>
                        <th class="px-2 py-2 text-center text-xs font-medium text-gray-600 border-r whitespace-nowrap">
                            <?= esc($kolom['nama_kolom']) ?>
                        </th>
                    <?php endforeach; ?>
                    <th class="px-2 py-2 text-center text-xs font-semibold text-blue-700 border-r bg-blue-100 whitespace-nowrap">
                        Rata-rata
                    </th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            <?php $no = 1; ?>
            <?php 
            // Untuk statistik ringkasan
            $total_all_rata = 0;
            $count_rata = 0;
            $count_atas_90 = 0;
            $count_bawah_75 = 0;
            ?>
            <?php foreach ($siswa as $s) : ?>
                <?php 
                $total_nilai_siswa = 0;
                $total_pembagi_siswa = 0; 
                ?>
                <tr class="hover:bg-gray-50">
                    <!-- Info Siswa -->
                    <td class="px-3 py-2 text-center text-gray-700 border-r"><?= $no++ ?></td>
                    <td class="px-3 py-2 text-left text-gray-900 border-r font-medium"><?= esc($s['nama_siswa']) ?></td>

                    <!-- Nilai Dinamis -->
                    <?php foreach ($headers as $header) : ?>
                        <?php 
                        $total_nilai_header = 0;
                        $total_pembagi_header = 0; 
                        ?>

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
                            
                            // Color coding for nilai
                            $nilaiClass = 'text-gray-600';
                            if (is_numeric($nilai)) {
                                if ($nilai >= 90) $nilaiClass = 'text-green-600 font-medium';
                                elseif ($nilai < 75) $nilaiClass = 'text-red-600';
                            }
                            ?>
                            <td class="px-2 py-2 text-center border-r <?= $nilaiClass ?>">
                                <?= $nilai !== null ? esc($nilai) : '-' ?>
                            </td>
                        <?php endforeach; ?>

                        <!-- Rata-rata per Header -->
                        <td class="px-2 py-2 text-center border-r bg-blue-50 font-semibold">
                            <?php
                            if ($total_pembagi_header > 0) {
                                $rata_rata_header = $total_nilai_header / $total_pembagi_header;
                                
                                // Color coding
                                $rataClass = 'text-gray-700';
                                if ($rata_rata_header >= 90) $rataClass = 'text-green-600';
                                elseif ($rata_rata_header < 75) $rataClass = 'text-red-600';
                                
                                echo '<span class="' . $rataClass . '">' . number_format($rata_rata_header, 1) . '</span>';
                                
                                // Tambahkan ke total siswa
                                $total_nilai_siswa += $rata_rata_header;
                                $total_pembagi_siswa++;
                            } else {
                                echo '<span class="text-gray-400">-</span>';
                            }
                            ?>
                        </td>
                    <?php endforeach; ?>

                    <!-- Rata-rata Total Siswa -->
                    <td class="px-3 py-2 text-center bg-blue-600 font-bold">
                        <?php
                        if ($total_pembagi_siswa > 0) {
                            $rata_rata_total = $total_nilai_siswa / $total_pembagi_siswa;
                            
                            // Color coding for total average
                            $totalClass = 'text-white';
                            if ($rata_rata_total >= 90) {
                                $totalClass = 'text-green-300';
                                $count_atas_90++;
                            } elseif ($rata_rata_total < 75) {
                                $totalClass = 'text-red-300';
                                $count_bawah_75++;
                            }
                            
                            echo '<span class="' . $totalClass . '">' . number_format($rata_rata_total, 1) . '</span>';
                            
                            $total_all_rata += $rata_rata_total;
                            $count_rata++;
                        } else {
                            echo '<span class="text-gray-300">-</span>';
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Statistik Ringkasan -->
<div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
    <div class="bg-gray-50 rounded-lg p-4 text-center">
        <p class="text-sm text-gray-500">Total Siswa</p>
        <p class="text-2xl font-bold text-gray-700"><?= count($siswa) ?></p>
    </div>
    <div class="bg-blue-50 rounded-lg p-4 text-center">
        <p class="text-sm text-blue-600">Rata-rata Kelas</p>
        <p class="text-2xl font-bold text-blue-700">
            <?= $count_rata > 0 ? number_format($total_all_rata / $count_rata, 1) : '-' ?>
        </p>
    </div>
    <div class="bg-green-50 rounded-lg p-4 text-center">
        <p class="text-sm text-green-600">Nilai ≥ 90</p>
        <p class="text-2xl font-bold text-green-700"><?= $count_atas_90 ?></p>
        <p class="text-xs text-green-500"><?= $count_rata > 0 ? number_format(($count_atas_90 / $count_rata) * 100, 1) : 0 ?>%</p>
    </div>
    <div class="bg-red-50 rounded-lg p-4 text-center">
        <p class="text-sm text-red-600">Nilai < 75</p>
        <p class="text-2xl font-bold text-red-700"><?= $count_bawah_75 ?></p>
        <p class="text-xs text-red-500"><?= $count_rata > 0 ? number_format(($count_bawah_75 / $count_rata) * 100, 1) : 0 ?>%</p>
    </div>
</div>

<!-- Legend -->
<div class="mt-4 p-3 bg-gray-50 rounded-lg">
    <p class="text-xs text-gray-500">
        <strong>Keterangan:</strong>
        <span class="inline-flex items-center ml-2"><span class="w-3 h-3 bg-green-500 rounded mr-1"></span> ≥ 90 (Sangat Baik)</span>
        <span class="inline-flex items-center ml-3"><span class="w-3 h-3 bg-gray-400 rounded mr-1"></span> 75-89 (Baik)</span>
        <span class="inline-flex items-center ml-3"><span class="w-3 h-3 bg-red-500 rounded mr-1"></span> < 75 (Perlu Perbaikan)</span>
    </p>
</div>
<?php endif; ?>

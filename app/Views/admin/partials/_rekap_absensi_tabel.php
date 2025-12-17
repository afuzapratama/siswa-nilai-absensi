<?php if (empty($rekap_data)) : ?>
    <div class="p-6 text-center">
        <svg class="w-12 h-12 mx-auto text-yellow-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <p class="text-gray-600">Tidak ada data absensi yang ditemukan untuk filter ini.</p>
        <p class="text-sm text-gray-500 mt-1">Pastikan sudah ada data absensi dalam rentang tanggal yang dipilih.</p>
    </div>
<?php else : ?>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-48">Nama Siswa</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">NIS</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-green-100 text-green-700 rounded-full font-bold">H</span>
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-yellow-100 text-yellow-700 rounded-full font-bold">I</span>
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-orange-100 text-orange-700 rounded-full font-bold">S</span>
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16">
                        <span class="inline-flex items-center justify-center w-8 h-8 bg-red-100 text-red-700 rounded-full font-bold">A</span>
                    </th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Total Poin</th>
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-28">Persentase</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php $no = 1; ?>
                <?php foreach ($rekap_data as $siswa) : ?>
                    <?php 
                        $persen = $siswa['persentase'];
                        $persenClass = 'text-gray-700';
                        $persenBgClass = '';
                        if ($persen >= 90) {
                            $persenClass = 'text-green-700';
                            $persenBgClass = 'bg-green-50';
                        } elseif ($persen >= 75) {
                            $persenClass = 'text-blue-700';
                            $persenBgClass = 'bg-blue-50';
                        } elseif ($persen < 75) {
                            $persenClass = 'text-red-700 font-bold';
                            $persenBgClass = 'bg-red-50';
                        }
                    ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-sm text-gray-500"><?= $no++ ?></td>
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-gray-900"><?= esc($siswa['nama_siswa']) ?></div>
                        </td>
                        <td class="px-4 py-3 text-sm font-mono text-gray-600"><?= esc($siswa['nis'] ?? '-') ?></td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center justify-center min-w-8 px-2 py-1 text-sm font-medium text-green-700 bg-green-100 rounded-full">
                                <?= esc($siswa['H']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center justify-center min-w-8 px-2 py-1 text-sm font-medium text-yellow-700 bg-yellow-100 rounded-full">
                                <?= esc($siswa['I']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center justify-center min-w-8 px-2 py-1 text-sm font-medium text-orange-700 bg-orange-100 rounded-full">
                                <?= esc($siswa['S']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center justify-center min-w-8 px-2 py-1 text-sm font-medium text-red-700 bg-red-100 rounded-full">
                                <?= esc($siswa['A']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-sm font-medium text-gray-700">
                            <?= esc(number_format($siswa['total_poin'], 1)) ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-sm font-semibold <?= $persenClass ?> <?= $persenBgClass ?>">
                                <?= esc(number_format($siswa['persentase'], 1)) ?>%
                                <?php if ($persen < 75) : ?>
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                <?php endif; ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Summary Stats -->
    <div class="px-4 py-4 bg-gray-50 border-t border-gray-200">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <?php
                $totalSiswa = count($rekap_data);
                $avgPersen = $totalSiswa > 0 ? array_sum(array_column($rekap_data, 'persentase')) / $totalSiswa : 0;
                $below75 = count(array_filter($rekap_data, fn($s) => $s['persentase'] < 75));
                $above90 = count(array_filter($rekap_data, fn($s) => $s['persentase'] >= 90));
            ?>
            <div class="text-center">
                <p class="text-sm text-gray-500">Total Siswa</p>
                <p class="text-xl font-bold text-gray-900"><?= $totalSiswa ?></p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-500">Rata-rata Kehadiran</p>
                <p class="text-xl font-bold text-blue-600"><?= number_format($avgPersen, 1) ?>%</p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-500">Kehadiran ≥90%</p>
                <p class="text-xl font-bold text-green-600"><?= $above90 ?> <span class="text-sm font-normal text-gray-500">siswa</span></p>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-500">Kehadiran &lt;75%</p>
                <p class="text-xl font-bold text-red-600"><?= $below75 ?> <span class="text-sm font-normal text-gray-500">siswa</span></p>
            </div>
        </div>
    </div>
<?php endif; ?>

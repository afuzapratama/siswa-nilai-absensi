<?php if (empty($siswa_list)) : ?>
    <div class="p-6 text-center">
        <svg class="w-12 h-12 mx-auto text-yellow-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
        </svg>
        <p class="text-gray-600">Tidak ada data siswa di kelas ini.</p>
        <p class="text-sm text-gray-500 mt-1">Silakan tambahkan siswa di menu 'Data Master → Siswa'.</p>
    </div>
<?php else : ?>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">No</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider min-w-48">Nama Siswa</th>
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
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Catatan/Bukti</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php $no = 1; foreach ($siswa_list as $siswa) :
                    $id_s   = $siswa['id_siswa'];
                    $absen  = $absen_map[$id_s] ?? null;
                    $krit   = isset($kriteria) ? htmlspecialchars(json_encode($kriteria), ENT_QUOTES, 'UTF-8') : '{}';
                    $hasBukti = !empty($absen['bukti']);
                ?>
                    <tr class="hover:bg-gray-50 transition-colors <?= ($absen) ? 'row-done' : '' ?>" data-id_siswa="<?= $id_s ?>">
                        <td class="px-4 py-3 text-sm text-gray-500"><?= $no++ ?></td>
                        <td class="px-4 py-3">
                            <div class="text-sm font-medium text-gray-900"><?= esc($siswa['nama_siswa']) ?></div>
                            <?php if (!empty($siswa['nis'])) : ?>
                                <div class="text-xs text-gray-500 font-mono"><?= esc($siswa['nis']) ?></div>
                            <?php endif; ?>
                        </td>

                        <!-- Checkbox H -->
                        <td class="px-4 py-3 text-center">
                            <input type="checkbox"
                                   class="check-absen w-5 h-5 text-green-600 border-gray-300 rounded focus:ring-green-500"
                                   name="absen_<?= $id_s ?>"
                                   value="H"
                                   data-id_siswa="<?= $id_s ?>"
                                   data-kriteria='<?= $krit ?>'
                                   <?= ($absen && $absen['status'] === 'H') ? 'checked' : '' ?>>
                        </td>

                        <!-- Checkbox I -->
                        <td class="px-4 py-3 text-center">
                            <input type="checkbox"
                                   class="check-absen w-5 h-5 text-yellow-500 border-gray-300 rounded focus:ring-yellow-500"
                                   name="absen_<?= $id_s ?>"
                                   value="I"
                                   data-id_siswa="<?= $id_s ?>"
                                   data-kriteria='<?= $krit ?>'
                                   <?= ($absen && $absen['status'] === 'I') ? 'checked' : '' ?>>
                        </td>

                        <!-- Checkbox S -->
                        <td class="px-4 py-3 text-center">
                            <input type="checkbox"
                                   class="check-absen w-5 h-5 text-orange-500 border-gray-300 rounded focus:ring-orange-500"
                                   name="absen_<?= $id_s ?>"
                                   value="S"
                                   data-id_siswa="<?= $id_s ?>"
                                   data-kriteria='<?= $krit ?>'
                                   <?= ($absen && $absen['status'] === 'S') ? 'checked' : '' ?>>
                        </td>

                        <!-- Checkbox A -->
                        <td class="px-4 py-3 text-center">
                            <input type="checkbox"
                                   class="check-absen w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500"
                                   name="absen_<?= $id_s ?>"
                                   value="A"
                                   data-id_siswa="<?= $id_s ?>"
                                   data-kriteria='<?= $krit ?>'
                                   <?= ($absen && $absen['status'] === 'A') ? 'checked' : '' ?>>
                        </td>

                        <!-- Catatan/Bukti -->
                        <td class="px-4 py-3 text-center">
                            <button type="button"
                                    class="btn-catatan inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-medium rounded-md border transition-colors
                                           <?= ($absen && ($absen['status'] === 'I' || $absen['status'] === 'S')) 
                                               ? 'border-blue-300 bg-blue-50 text-blue-700 hover:bg-blue-100' 
                                               : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50' ?>"
                                    data-id_siswa="<?= $id_s ?>"
                                    data-kriteria='<?= $krit ?>'
                                    data-has-bukti="<?= $hasBukti ? '1' : '0' ?>">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Catatan
                            </button>

                            <?php if ($hasBukti) : ?>
                                <a href="<?= base_url($absen['bukti']) ?>" 
                                   target="_blank" 
                                   class="inline-flex items-center ml-1 text-blue-600 hover:text-blue-800"
                                   title="Lihat bukti">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                    </svg>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Summary -->
    <div class="px-4 py-3 bg-gray-50 border-t border-gray-200">
        <div class="flex flex-wrap items-center gap-4 text-sm">
            <?php
                $total = count($siswa_list);
                $hadir = 0; $izin = 0; $sakit = 0; $alpa = 0; $belum = 0;
                foreach ($siswa_list as $s) {
                    $ab = $absen_map[$s['id_siswa']] ?? null;
                    if (!$ab) { $belum++; continue; }
                    switch ($ab['status']) {
                        case 'H': $hadir++; break;
                        case 'I': $izin++; break;
                        case 'S': $sakit++; break;
                        case 'A': $alpa++; break;
                        default: $belum++;
                    }
                }
            ?>
            <span class="text-gray-600">Total: <strong><?= $total ?></strong></span>
            <span class="text-green-600">Hadir: <strong><?= $hadir ?></strong></span>
            <span class="text-yellow-600">Izin: <strong><?= $izin ?></strong></span>
            <span class="text-orange-600">Sakit: <strong><?= $sakit ?></strong></span>
            <span class="text-red-600">Alpa: <strong><?= $alpa ?></strong></span>
            <span class="text-gray-500">Belum Diisi: <strong><?= $belum ?></strong></span>
        </div>
    </div>
<?php endif; ?>

<?php if (empty($siswa)) : ?>
<tr>
  <td colspan="5" class="text-center text-gray-500 py-12">
    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
    </svg>
    <p class="font-medium"><?= empty($id_kelas) ? 'Silakan pilih kelas terlebih dahulu' : 'Tidak ada data siswa yang ditemukan' ?></p>
    <p class="text-sm"><?= empty($id_kelas) ? 'untuk menampilkan data siswa' : 'untuk filter ini' ?></p>
  </td>
</tr>
<?php else : ?>
  <?php
    $cur = isset($pager) && $pager ? (int)$pager->getCurrentPage('siswa') : 1;
    $per = isset($pager) && $pager ? (int)$pager->getPerPage() : 10;
    $no  = ($cur - 1) * $per + 1;
  ?>
  <?php foreach ($siswa as $s) : ?>
  <tr class="hover:bg-gray-50 transition-colors">
    <td class="text-center font-medium"><?= $no++ ?></td>
    <td class="font-semibold text-gray-900"><?= esc($s['nama_siswa']) ?></td>
    <td>
      <?php if ($s['nis']) : ?>
        <span class="inline-flex items-center px-2.5 py-1 rounded-md bg-gray-100 text-gray-800 font-mono text-sm">
          <?= esc($s['nis']) ?>
        </span>
      <?php else : ?>
        <span class="text-gray-400">-</span>
      <?php endif; ?>
    </td>
    <td>
      <div class="flex flex-col">
        <span class="font-medium text-gray-900"><?= esc($s['nama_kelas']) ?> (<?= esc($s['kode_kelas']) ?>)</span>
        <span class="text-sm text-gray-500"><?= esc($s['tahun_ajaran']) ?> (<?= esc($s['semester']) ?>)</span>
      </div>
    </td>
    <td>
      <div class="flex items-center gap-2">
        <button type="button" class="btn btn-warning btn-sm btn-edit" data-id="<?= $s['id_siswa'] ?>" title="Edit">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
          </svg>
          <span class="hidden sm:inline">Edit</span>
        </button>
        <button type="button" class="btn btn-danger btn-sm btn-delete" data-id="<?= $s['id_siswa'] ?>" title="Hapus">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
          </svg>
          <span class="hidden sm:inline">Hapus</span>
        </button>
      </div>
    </td>
  </tr>
  <?php endforeach; ?>
<?php endif; ?>

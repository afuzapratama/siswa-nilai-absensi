<?php if (empty($siswa)) : ?>
<tr>
  <td colspan="5" class="text-center text-muted">
    <?= empty($id_kelas) ? 'Silakan pilih kelas terlebih dahulu.' : 'Tidak ada data siswa yang ditemukan untuk filter ini.' ?>
  </td>
</tr>
<?php else : ?>
  <?php
    $cur = isset($pager) && $pager ? (int)$pager->getCurrentPage('siswa') : 1;
    $per = isset($pager) && $pager ? (int)$pager->getPerPage() : 10;
    $no  = ($cur - 1) * $per + 1;
  ?>
  <?php foreach ($siswa as $s) : ?>
  <tr>
    <td><?= $no++ ?></td>
    <td><?= esc($s['nama_siswa']) ?></td>
    <td><?= $s['nis'] ? esc($s['nis']) : '-' ?></td>
    <td>
      <?= esc($s['nama_kelas']) ?> (<?= esc($s['kode_kelas']) ?>)<br>
      <small class="text-muted"><?= esc($s['tahun_ajaran']) ?> (<?= esc($s['semester']) ?>)</small>
    </td>
    <td>
      <button class="btn btn-sm btn-warning btn-edit" data-id="<?= $s['id_siswa'] ?>"><i class="fas fa-edit"></i> Edit</button>
      <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $s['id_siswa'] ?>"><i class="fas fa-trash"></i> Hapus</button>
    </td>
  </tr>
  <?php endforeach; ?>
<?php endif; ?>

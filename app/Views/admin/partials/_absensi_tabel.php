<?php if (empty($siswa_list)) : ?>
  <div class="alert alert-warning text-center">
    Tidak ada data siswa di kelas ini. Silakan tambahkan siswa di menu 'Data Master -> Siswa'.
  </div>
<?php else : ?>
  <div class="table-responsive">
    <table class="table table-bordered table-hover" id="tabelAbsensi" width="100%" cellspacing="0">
      <thead>
        <tr class="text-center">
          <th class="align-middle" style="width:5%;">No</th>
          <th class="align-middle text-left" style="min-width:200px;">Nama Siswa</th>
          <th class="align-middle" style="width:10%;">H</th>
          <th class="align-middle" style="width:10%;">I</th>
          <th class="align-middle" style="width:10%;">S</th>
          <th class="align-middle" style="width:10%;">A</th>
          <th class="align-middle" style="width:15%;">Catatan/Bukti</th>
        </tr>
      </thead>
      <tbody>
        <?php $no = 1; foreach ($siswa_list as $siswa) :
          $id_s   = $siswa['id_siswa'];
          $absen  = $absen_map[$id_s] ?? null;
          $krit   = isset($kriteria) ? htmlspecialchars(json_encode($kriteria), ENT_QUOTES, 'UTF-8') : '{}';
        ?>
          <tr data-id_siswa="<?= $id_s ?>">
            <td class="text-center"><?= $no++ ?></td>
            <td><?= esc($siswa['nama_siswa']) ?></td>

            <!-- Checkbox H -->
            <td class="text-center">
              <input type="checkbox"
                     class="form-check-input check-absen"
                     name="absen_<?= $id_s ?>"
                     value="H"
                     data-id_siswa="<?= $id_s ?>"
                     data-kriteria='<?= $krit ?>'
                     <?= ($absen && $absen['status'] === 'H') ? 'checked' : '' ?>>
            </td>

            <!-- Checkbox I -->
            <td class="text-center">
              <input type="checkbox"
                     class="form-check-input check-absen"
                     name="absen_<?= $id_s ?>"
                     value="I"
                     data-id_siswa="<?= $id_s ?>"
                     data-kriteria='<?= $krit ?>'
                     <?= ($absen && $absen['status'] === 'I') ? 'checked' : '' ?>>
            </td>

            <!-- Checkbox S -->
            <td class="text-center">
              <input type="checkbox"
                    class="form-check-input check-absen"
                     name="absen_<?= $id_s ?>"
                     value="S"
                     data-id_siswa="<?= $id_s ?>"
                     data-kriteria='<?= $krit ?>'
                     <?= ($absen && $absen['status'] === 'S') ? 'checked' : '' ?>>
            </td>

            <!-- Checkbox A -->
            <td class="text-center">
              <input type="checkbox"
                     class="form-check-input check-absen"
                     name="absen_<?= $id_s ?>"
                     value="A"
                     data-id_siswa="<?= $id_s ?>"
                     data-kriteria='<?= $krit ?>'
                     <?= ($absen && $absen['status'] === 'A') ? 'checked' : '' ?>>
            </td>

            <!-- Catatan/Bukti: tombol buka modal + indikator bukti -->
            <td class="text-center">
              <button type="button"
                      class="btn btn-xs btn-outline-primary btn-catatan py-0 px-1"
                      data-id_siswa="<?= $id_s ?>"
                      data-kriteria='<?= $krit ?>'>
                <i class="fas fa-pencil-alt"></i> Catatan/Bukti
              </button>

              <?php if (!empty($absen['bukti'])): ?>
                <a href="<?= base_url($absen['bukti']) ?>" target="_blank" class="ml-1" title="Lihat bukti">
                  <i class="fas fa-paperclip"></i>
                </a>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Tidak ada <script> di partial ini.
       Event handler untuk .check-absen & .btn-catatan ditangani JS global di absensi_input.php
       (yang tadi sudah aku kasih: buka modal untuk I/S, autosave untuk H/A). -->
<?php endif; ?>

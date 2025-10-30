<?= $this->section('css') ?>
<style>
  /* hijau lembut + aksen sisi kiri */
  #tabelAbsensiContainer tbody tr.row-done {
    background-color: #F0FDF4;               /* emerald-50 */
    transition: background-color .25s ease;
  }
  #tabelAbsensiContainer tbody tr.row-done td {
    box-shadow: inset 3px 0 0 #10B981;       /* emerald-500 */
  }
  #tabelAbsensiContainer tbody tr.row-done:hover {
    background-color: #ECFDF5;               /* sedikit lebih terang saat hover */
  }
</style>
 <style>
    /* Sentuhan kecil biar rapih */
    #modalIzinSakit .custom-file-label::after { content: attr(data-browse); } /* ganti "Browse" jadi "Pilih" */
    #modalIzinSakit .form-control, 
    #modalIzinSakit .custom-file-input { transition: box-shadow .15s ease; }
    #modalIzinSakit .form-control:focus, 
    #modalIzinSakit .custom-file-input:focus ~ .custom-file-label {
        box-shadow: 0 0 0 .2rem rgba(0,123,255,.25);
    }

    .form-check-input.check-absen {
  /* Gunakan 'em' agar skalanya proporsional */
    width: 1.3em;
    height: 1.3em;

    /* Atau gunakan 'px' jika ingin ukuran pasti */
    /* width: 30px; */
    /* height: 30px; */
    }

/* Jika Anda menggunakan .form-switch, 
Anda mungkin perlu sedikit penyesuaian lagi agar label tidak terlalu dekat 
*/
    .form-switch .form-check-input.check-absen {
    width: 3em; /* Switch biasanya lebih lebar */
    }
    </style>
<?= $this->endSection() ?>
<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title ?></h1>

    <!-- Card Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Input Absensi</h6>
        </div>
        <div class="card-body">
            <form id="filterAbsensi">
                <?= csrf_field() ?>
                <div class="row">
                    <!-- Tanggal -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="tanggal">Tanggal</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" value="<?= date('Y-m-d') ?>" required>
                        </div>
                    </div>
                    <!-- Kelas -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="id_kelas">Pilih Kelas</label>
                            <select class="form-control" id="id_kelas" name="id_kelas" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach ($kelas_list as $kelas) : ?>
                                    <option value="<?= $kelas['id_kelas'] ?>">
                                        <?= esc($kelas['nama_kelas']) ?> (<?= esc($kelas['kode_kelas']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <!-- Mapel -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="id_mapel">Pilih Mata Pelajaran</label>
                            <select class="form-control" id="id_mapel" name="id_mapel" required>
                                <option value="">-- Pilih Kelas Dulu --</option>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary" id="btnGo">
                    <i class="fas fa-arrow-right"></i> Go
                </button>
            </form>
        </div>
    </div>

    <!-- Card Hasil Absensi -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Hasil Absensi</h6>
        </div>
        <div class="card-body">
            <!-- Tempat tabel AJAX di-load -->
            <div id="tabelAbsensiContainer">
                <div class="text-center text-muted">
                    Silakan pilih filter di atas dan klik "Go" untuk menampilkan data siswa.
                </div>
            </div>
        </div>
    </div>
<!-- Modal Izin/Sakit -->
<div class="modal fade" id="modalIzinSakit" tabindex="-1" role="dialog" aria-labelledby="modalIzinSakitLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <form id="formIzinSakit" class="w-100 needs-validation" novalidate enctype="multipart/form-data">
      <?= csrf_field() ?>
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalIzinSakitLabel">Catatan & Bukti (Izin/Sakit)</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <!-- Hidden -->
          <input type="hidden" name="id_ta">
          <input type="hidden" name="id_kelas">
          <input type="hidden" name="id_mapel">
          <input type="hidden" name="tanggal">
          <input type="hidden" name="id_siswa">
          <input type="hidden" name="status"> <!-- I atau S -->
          <input type="hidden" name="hapus_bukti" value="0">

          <div class="form-row">
            <!-- Kolom Catatan -->
            <div class="col-md-7 mb-3">
              <label for="catatan">Catatan <span class="text-danger">*</span></label>
              <textarea id="catatan" name="catatan" class="form-control" rows="5" required placeholder="Tulis alasan izin/sakit di sini..."></textarea>
              <div class="invalid-feedback">Catatan wajib diisi.</div>
              <small class="text-muted d-block mt-2">Tulis ringkas, jelas, dan sopan.</small>
            </div>

            <!-- Kolom Bukti -->
            <div class="col-md-5 mb-3">
              <label for="bukti">Bukti (opsional)</label>
              <div class="custom-file">
                <input type="file" id="bukti" name="bukti" class="custom-file-input" accept="image/jpeg,image/png,image/webp">
                <label class="custom-file-label" for="bukti" data-browse="Pilih">Pilih file…</label>
                <div class="invalid-feedback" id="buktiInvalid">File tidak valid. Hanya JPG/PNG/WEBP, maks 4MB.</div>
              </div>

              <!-- Preview -->
              <div id="previewWrap" class="border rounded p-2 mt-3 d-none">
                <div class="d-flex align-items-center">
                  <img id="imgPreview" src="" alt="Preview bukti" class="img-fluid img-thumbnail mr-2" style="max-height:120px;">
                  <div class="flex-fill">
                    <a href="#" target="_blank" id="linkBukti" class="d-inline-block mb-2">Lihat ukuran penuh</a><br>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="btnHapusBukti">
                      <i class="fas fa-trash-alt"></i> Hapus bukti
                    </button>
                  </div>
                </div>
              </div>

              <small class="text-muted d-block mt-2">Format: JPG/PNG/WEBP, ukuran maks 4MB.</small>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Simpan
          </button>
        </div>
      </div>
    </form>
  </div>
</div>
</div>



<?= $this->endSection() ?>

<?= $this->section('js') ?>

<script>
(function () {
  // =========================
  // Konstanta & util
  // =========================
  const ENDPOINT = {
    MAPEL: '<?= site_url('admin/absensi/ajax-get-mapel') ?>',
    TABEL: '<?= site_url('admin/absensi/get-siswa') ?>',
    DETAIL: '<?= site_url('admin/absensi/detail') ?>',
    SAVE_ABSEN: '<?= site_url('admin/absensi/save-absen') ?>',
    SAVE_IS: '<?= site_url('admin/absensi/save-izin-sakit') ?>'
  };

  const CSRF = {'<?= csrf_token() ?>': '<?= csrf_hash() ?>'};

  const $formFilter   = $('#filterAbsensi');
  const $kelas        = $('#id_kelas');
  const $mapel        = $('#id_mapel');
  const $btnGo        = $('#btnGo');
  const $container    = $('#tabelAbsensiContainer');

  const $modal        = $('#modalIzinSakit');
  const $formIS       = $('#formIzinSakit');
  const $file         = $('#bukti');
  const $fileLabel    = $file.next('.custom-file-label');
  const $previewWrap  = $('#previewWrap');
  const $imgPreview   = $('#imgPreview');
  const $linkBukti    = $('#linkBukti');
  const $hapusBukti   = $formIS.find('[name="hapus_bukti"]');

  let tabelXhr = null;              // guard: request reload tabel yang berjalan
  let lastFilterSig = null;         // tanda filter terakhir
  let objectUrl = null;

  // SweetAlert Toast
  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 2500,
    timerProgressBar: true,
    didOpen: t => {
      t.addEventListener('mouseenter', Swal.stopTimer);
      t.addEventListener('mouseleave', Swal.resumeTimer);
    }
  });

  // =========================
  // HIGHLIGHT ROW helpers
  // =========================
  function isRowFilled($tr) {
    const anyChecked   = $tr.find('.check-absen:checked').length > 0;
    const hasNoteAttr  = $tr.find('.btn-catatan[data-has-catatan="1"], .btn-catatan[data-has-bukti="1"]').length > 0;
    const hasPaperclip = $tr.find('.btn-catatan .fa-paperclip').length > 0;
    return anyChecked || hasNoteAttr || hasPaperclip;
  }
  function paintRow($tr) { if (isRowFilled($tr)) $tr.addClass('row-done'); else $tr.removeClass('row-done'); }
  function paintRows()   { $('#tabelAbsensiContainer tbody tr').each(function(){ paintRow($(this)); }); }

  // =========================
  // Helper: signature filter & reload tabel parsial
  // =========================
  function filterSignature() { return $formFilter.serialize(); }

  function reloadTabelPartial(opts = { spinner: false, preserveScroll: true }) {
    const sig = filterSignature();
    const hasRequired = $formFilter.find('[name="tanggal"]').val() && $kelas.val() && $mapel.val();
    if (!hasRequired) return;

    if (tabelXhr && tabelXhr.readyState !== 4) tabelXhr.abort();

    const prevScroll = opts.preserveScroll ? $container.scrollTop() : 0;

    if (opts.spinner) {
      $container.html('<div class="text-center text-muted py-5"><i class="fas fa-spin fa-spinner fa-2x"></i><br>Memuat…</div>');
    }

    tabelXhr = $.ajax({
      url: ENDPOINT.TABEL,
      method: 'POST',
      data: $formFilter.serialize(),
      success: function (html) {
        $container.html(html);
        if (opts.preserveScroll) $container.scrollTop(prevScroll);
        lastFilterSig = sig;
        paintRows(); // <- warnai semua row setelah partial reload
      },
      error: function (xhr, s) {
        if (s === 'abort') return;
        $container.html('<div class="alert alert-danger text-center">Gagal memuat data.</div>');
      }
    });
  }

  // =========================
  // MAPEL by Kelas
  // =========================
  $kelas.off('change.absensi').on('change.absensi', function () {
    const id_kelas = $(this).val();
    $mapel.html('<option value="">Loading...</option>');

    if (!id_kelas) { $mapel.html('<option value="">-- Pilih Kelas Dulu --</option>'); return; }

    $.ajax({
      url: ENDPOINT.MAPEL,
      method: 'POST',
      data: Object.assign({ id_kelas }, CSRF),
      dataType: 'json'
    }).done(resp => {
      $mapel.html('<option value="">-- Pilih Mata Pelajaran --</option>');
      if (resp.status === 'success' && resp.mapel?.length) {
        resp.mapel.forEach(m => $mapel.append(`<option value="${m.id_mapel}">${m.nama_mapel}</option>`));
      } else {
        $mapel.html('<option value="">-- Tidak ada mapel terhubung --</option>');
      }
    }).fail(() => {
      $mapel.html('<option value="">Gagal memuat mapel</option>');
    });
  });

  // =========================
  // Tampilkan Tabel (Go)
  // =========================
  $formFilter.off('submit.absensi').on('submit.absensi', function (e) {
    e.preventDefault();
    $btnGo.prop('disabled', true).html('<i class="fas fa-spin fa-spinner"></i>');
    reloadTabelPartial({ spinner: true, preserveScroll: false });
    $(document).one('ajaxStop', function () {
      $btnGo.prop('disabled', false).html('<i class="fas fa-arrow-right"></i> Go');
    });
  });

  // =========================
  // Checkbox H/A/I/S (TANPA reload tabel)
  // =========================
  $container.off('change.absensi', '.check-absen')
    .on('change.absensi', '.check-absen', function () {
      const $cb = $(this);
      const id_siswa = $cb.data('id_siswa');
      let kriteria = $cb.data('kriteria');
      if (typeof kriteria === 'string') { try { kriteria = JSON.parse(kriteria); } catch (e) { kriteria = {}; } }
      const status = $cb.val();

      // uncheck yang lain dalam grup siswa ini
      $(`input[name="absen_${id_siswa}"]`).not(this).prop('checked', false);

      const baseData = Object.assign({
        id_ta: kriteria.id_tahun_ajaran,
        id_kelas: kriteria.id_kelas,
        id_mapel: kriteria.id_mapel,
        tanggal: kriteria.tanggal,
        id_siswa
      }, CSRF);

      if (status === 'I' || status === 'S') {
        // buka modal, tidak autosave & tidak reload tabel
        $cb.prop('checked', false);
        prefillModal(baseData, status);
        return;
      }

      // H / A → autosave ringan, TIDAK reload tabel
      $.ajax({
        url: ENDPOINT.SAVE_ABSEN,
        method: 'POST',
        data: Object.assign({}, baseData, { status }),
        dataType: 'json'
      }).done(resp => {
        Toast.fire({ icon: resp.status === 'success' ? 'success' : 'error', title: resp.message || 'Tersimpan' });
        // <<< PATCH PENTING: warna-ulang baris ini saja (live, tanpa reload)
        paintRow($cb.closest('tr'));
      }).fail(() => {
        Toast.fire({ icon: 'error', title: 'Gagal menyimpan' });
        $cb.prop('checked', !$cb.prop('checked'));
      });
    });

  // =========================
  // Tombol Catatan/Bukti → buka modal (TANPA reload tabel)
  // =========================
  $container.off('click.absensi', '.btn-catatan')
    .on('click.absensi', '.btn-catatan', function () {
      const id_siswa = $(this).data('id_siswa');
      let kriteria = $(this).data('kriteria');
      if (typeof kriteria === 'string') { try { kriteria = JSON.parse(kriteria); } catch (e) { kriteria = {}; } }
      const baseData = Object.assign({
        id_ta: kriteria.id_tahun_ajaran,
        id_kelas: kriteria.id_kelas,
        id_mapel: kriteria.id_mapel,
        tanggal: kriteria.tanggal,
        id_siswa
      }, CSRF);
      prefillModal(baseData, 'I'); // default I, nanti ditimpa dari DETAIL
    });

  // =========================
  // Modal IS: Prefill + Preview
  // =========================
  function resetModalUI() {
    $formIS.removeClass('was-validated')[0].reset();
    $fileLabel.text('Pilih file…');
    hidePreview();
    $hapusBukti.val('0');
  }

  function prefillModal(baseData, statusDefault) {
    resetModalUI();
    $formIS.find('[name="id_ta"]').val(baseData.id_ta);
    $formIS.find('[name="id_kelas"]').val(baseData.id_kelas);
    $formIS.find('[name="id_mapel"]').val(baseData.id_mapel);
    $formIS.find('[name="tanggal"]').val(baseData.tanggal);
    $formIS.find('[name="id_siswa"]').val(baseData.id_siswa);
    $formIS.find('[name="status"]').val(statusDefault);

    $.post(ENDPOINT.DETAIL, baseData, function (resp) {
      if (resp.status === 'success' && resp.data) {
        const d = resp.data;
        if (d.status === 'I' || d.status === 'S') $formIS.find('[name="status"]').val(d.status);
        if (d.catatan) $formIS.find('[name="catatan"]').val(d.catatan);
        if (d.bukti) {
          $linkBukti.attr('href', d.bukti);
          $imgPreview.attr('src', d.bukti);
          $previewWrap.removeClass('d-none');
        }
      }
      $modal.modal('show');
    }, 'json').fail(() => $modal.modal('show'));
  }

  function hidePreview() {
    if (objectUrl) { URL.revokeObjectURL(objectUrl); objectUrl = null; }
    $previewWrap.addClass('d-none');
    $imgPreview.attr('src', '');
    $linkBukti.attr('href', '#');
  }

  $modal.off('show.bs.modal.absensi').on('show.bs.modal.absensi', function () {
    $formIS.removeClass('was-validated');
  });

  $file.off('change.absensi').on('change.absensi', function () {
    const file = this.files && this.files[0] ? this.files[0] : null;
    $hapusBukti.val('0');
    if (!file) { $fileLabel.text('Pilih file…'); hidePreview(); return; }

    $fileLabel.text(file.name);
    const validTypes = ['image/jpeg','image/png','image/webp'];
    const tooBig = file.size > 4 * 1024 * 1024;
    const badType = validTypes.indexOf(file.type) === -1;

    if (tooBig || badType) { $(this).addClass('is-invalid'); hidePreview(); return; }
    $(this).removeClass('is-invalid');

    if (objectUrl) URL.revokeObjectURL(objectUrl);
    objectUrl = URL.createObjectURL(file);
    $imgPreview.attr('src', objectUrl);
    $linkBukti.attr('href', objectUrl);
    $previewWrap.removeClass('d-none');
  });

  $('#btnHapusBukti').off('click.absensi').on('click.absensi', function () {
    $hapusBukti.val('1');
    $file.val('');
    $fileLabel.text('Pilih file…');
    hidePreview();
  });

  // =========================
  // Submit Modal Izin/Sakit
  // (di sini barulah partial reload)
  // =========================
  $formIS.off('submit.absensi').on('submit.absensi', function (e) {
    e.preventDefault();
    if (this.checkValidity() === false) { $(this).addClass('was-validated'); return; }

    const fd = new FormData(this);
    for (const k in CSRF) fd.append(k, CSRF[k]);

    $.ajax({
      url: ENDPOINT.SAVE_IS,
      method: 'POST',
      data: fd,
      processData: false,
      contentType: false,
      dataType: 'json'
    }).done(resp => {
      if (resp.status === 'success') {
        $modal.modal('hide');
        Toast.fire({ icon: 'success', title: 'Tersimpan' });

        const id_siswa = $formIS.find('[name="id_siswa"]').val();
        const status   = $formIS.find('[name="status"]').val();
        const name     = 'absen_' + id_siswa;
        $(`input[name="${name}"]`).prop('checked', false);
        $(`input[name="${name}"][value="${status}"]`).prop('checked', true);

        // reload partial + repaint
        reloadTabelPartial({ spinner: false, preserveScroll: true });
      } else {
        Toast.fire({ icon: 'error', title: resp.message || 'Gagal menyimpan' });
      }
    }).fail(() => {
      Toast.fire({ icon: 'error', title: 'Terjadi kesalahan' });
    });
  });

})();
</script>

<?= $this->endSection() ?>
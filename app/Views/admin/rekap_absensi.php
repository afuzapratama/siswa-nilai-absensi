<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title ?></h1>

    <!-- Card Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Laporan Kehadiran</h6>
        </div>
        <div class="card-body">
            <form id="filterRekapAbsensi">
                <?= csrf_field() ?>
                <div class="row">
                    <!-- Tanggal Mulai -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tanggal_mulai">Dari Tanggal</label>
                            <input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai" value="<?= date('Y-m-01') ?>" required>
                        </div>
                    </div>

                    <!-- Tanggal Selesai -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="tanggal_selesai">Sampai Tanggal</label>
                            <input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai" value="<?= date('Y-m-t') ?>" required>
                        </div>
                    </div>

                    <!-- Pilih Kelas -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="id_kelas_absensi">Pilih Kelas</label> <!-- ID unik -->
                            <select class="form-control" id="id_kelas_absensi" name="id_kelas" required>
                                <option value="">-- Pilih Kelas --</option>
                                <?php foreach ($kelas_list as $kelas) : ?>
                                    <option value="<?= $kelas['id_kelas'] ?>">
                                        <?= esc($kelas['nama_kelas']) ?> (<?= esc($kelas['kode_kelas']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Pilih Mata Pelajaran -->
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="id_mapel_absensi">Pilih Mata Pelajaran</label> <!-- ID unik -->
                            <select class="form-control" id="id_mapel_absensi" name="id_mapel" required>
                                <option value="">-- Pilih Kelas Dulu --</option>
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" id="btnTerapkan">
                    <i class="fas fa-search"></i> Terapkan
                </button>
            </form>
        </div>
    </div>

    <!-- Card Hasil Laporan -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Hasil Rekap Kehadiran</h6>
            <div>
                <button type="button" class="btn btn-success" id="btnExportCSV" disabled>
                    <i class="fas fa-file-csv"></i> Export CSV
                </button>
                <button type="button" class="btn btn-danger" id="btnExportPDF" disabled>
                    <i class="fas fa-file-pdf"></i> Export PDF
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Tempat tabel AJAX di-load -->
            <div id="tabelRekapContainer">
                <div class="text-center text-muted">
                    Silakan pilih filter di atas dan klik "Terapkan" untuk menampilkan rekap.
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Loading Bootstrap 4 -->
    <div class="modal fade" id="loadingExport" tabindex="-1" data-backdrop="static" data-keyboard="false" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content text-center p-4">
                <div class="spinner-border mb-2" role="status" aria-hidden="true"></div>
                <div>Menyiapkan PDF...</div>
            </div>
        </div>
    </div>


</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<!-- Script khusus untuk Halaman Rekap Absensi -->
<script>
    $(document).ready(function() {

        // 1. AJAX: Ambil Mata Pelajaran saat Kelas dipilih
        $('#id_kelas_absensi').on('change', function() { // Target ID unik
            let id_kelas = $(this).val();
            let selectMapel = $('#id_mapel_absensi'); // Target ID unik

            selectMapel.html('<option value="">Loading...</option>');

            if (!id_kelas) {
                selectMapel.html('<option value="">-- Pilih Kelas Dulu --</option>');
                return;
            }

            $.ajax({
                url: '<?= site_url('admin/absensi/ajax-get-mapel') ?>', // [FIX] Rute yang benar
                type: 'POST',
                data: {
                    id_kelas: id_kelas,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: 'JSON',
                success: function(response) {
                    selectMapel.html('<option value="">-- Pilih Mata Pelajaran --</option>');
                    if (response.status === 'success') {
                        $.each(response.mapel, function(key, value) {
                            selectMapel.append('<option value="' + value.id_mapel + '">' + value.nama_mapel + '</option>');
                        });
                    }
                },
                error: function() {
                    selectMapel.html('<option value="">Gagal memuat mapel</option>');
                }
            });
        });

        // 2. AJAX: Tombol "Terapkan"
        $('#filterRekapAbsensi').on('submit', function(e) {
            e.preventDefault();
            let btn = $('#btnTerapkan');
            let container = $('#tabelRekapContainer');
            let formData = $(this).serialize();

            $.ajax({
                url: '<?= site_url('admin/rekap-absensi/tampilkan-data') ?>', // [FIX] Rute yang benar (tanpa typo '1')
                type: 'POST',
                data: formData,
                beforeSend: function() {
                    btn.prop('disabled', true).html('<i class="fas fa-spin fa-spinner"></i> Menerapkan...');
                    container.html('<div class="text-center text-muted py-5"><i class="fas fa-spin fa-spinner fa-2x"></i><br>Memuat data...</div>');
                    $('#btnExportCSV').prop('disabled', true);
                    $('#btnExportPDF').prop('disabled', true);
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-search"></i> Terapkan');
                },
                success: function(response) {
                    container.html(response);
                    $('#btnExportCSV').prop('disabled', false);
                    $('#btnExportPDF').prop('disabled', false);
                },
                error: function() {
                    container.html('<div class="alert alert-danger text-center">Gagal memuat data. Silakan coba lagi.</div>');
                }
            });
        });

        // Helper untuk Export
        function getExportUrl(format) {
            let params = {
                tanggal_mulai: $('#tanggal_mulai').val(),
                tanggal_selesai: $('#tanggal_selesai').val(),
                id_kelas: $('#id_kelas_absensi').val(), // Target ID unik
                id_mapel: $('#id_mapel_absensi').val() // Target ID unik
            };
            let baseUrl = '<?= site_url('admin/rekap-absensi/export-csv') ?>'.replace('export-csv', format);
            return baseUrl + '?' + $.param(params);
        }

        // 3. Tombol Export CSV
        $('#btnExportCSV').on('click', function() {
            window.location.href = getExportUrl('export-csv');
        });

      // Tambah query param
function addQuery(url, key, value) {
  const u = new URL(url, window.location.origin);
  u.searchParams.set(key, value);
  return u.toString();
}

// Cari cookie yang namanya 'dl_token' ATAU berakhiran 'dl_token' (kalau ada prefix)
function hasDownloadCookie(token) {
  const all = document.cookie ? document.cookie.split(';') : [];
  for (const pair of all) {
    const idx = pair.indexOf('=');
    const name = (idx > -1 ? pair.slice(0, idx) : pair).trim();
    const val  = (idx > -1 ? pair.slice(idx + 1) : '').trim();
    if (!name) continue;
    if (name === 'dl_token' || name.endsWith('dl_token')) {
      try {
        if (decodeURIComponent(val) === token) return true;
      } catch (_) {
        if (val === token) return true;
      }
    }
  }
  return false;
}

// Hapus semua varian cookie dl_token (dengan/ tanpa prefix)
function clearDownloadCookie() {
  const all = document.cookie ? document.cookie.split(';') : [];
  for (const pair of all) {
    const idx = pair.indexOf('=');
    const name = (idx > -1 ? pair.slice(0, idx) : pair).trim();
    if (name === 'dl_token' || name.endsWith('dl_token')) {
      document.cookie = name + '=; Max-Age=0; path=/';
    }
  }
}

// Handler tombol
$('#btnExportPDF').off('click').on('click', function () {
  const $btn = $(this);
  const baseUrl = getExportUrl('export-pdf'); // punyamu
  const token   = 'dl_' + Date.now() + '_' + Math.random().toString(36).slice(2);
  const url     = addQuery(baseUrl, 'dl_token', token);

  // UI: tombol + modal
  const originalHtml = $btn.html();
  $btn.prop('disabled', true).html(
    '<span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>Mengunduh...'
  );
  $('#loadingExport').modal('show');

  // Buat iframe tersembunyi untuk trigger download
  clearDownloadCookie(); // bersihkan sisa cookie lama
  const $iframe = $('<iframe>', { src: url, style: 'display:none', id: 'dlFrameTmp' });
  $('body').append($iframe);

  const start = Date.now();
  const hardTimeout = 120000; // 2 menit (sesuaikan)
  const softCloseAt = 5000;   // 5 detik -> tutup loader walau cookie belum muncul (fallback utk IDM)

  const timer = setInterval(function () {
    const elapsed = Date.now() - start;

    // 1) sukses: cookie terdeteksi
    if (hasDownloadCookie(token)) {
      clearInterval(timer);
      clearDownloadCookie();
      try { $iframe.remove(); } catch (e) {}
      $('#loadingExport').modal('hide');
      $btn.prop('disabled', false).html(originalHtml);
      if (typeof Toast !== 'undefined') Toast.fire({ icon: 'success', title: 'PDF sedang diunduh.' });
      return;
    }

    // 2) fallback: setelah X detik, tutup loader agar tidak menggantung (IDM sering blok Set-Cookie)
    if (elapsed >= softCloseAt) {
      clearInterval(timer);
      try { $iframe.remove(); } catch (e) {}
      $('#loadingExport').modal('hide');
      $btn.prop('disabled', false).html(originalHtml);
      // opsional: kasih info ringan saja, jangan error
      if (typeof Toast !== 'undefined') Toast.fire({ icon: 'info', title: 'Download dimulai.' });
      return;
    }

    // 3) benar-benar timeout (server gagal)
    if (elapsed >= hardTimeout) {
      clearInterval(timer);
      try { $iframe.remove(); } catch (e) {}
      $('#loadingExport').modal('hide');
      $btn.prop('disabled', false).html(originalHtml);
      if (typeof Toast !== 'undefined') Toast.fire({ icon: 'error', title: 'Gagal mengunduh (timeout).' });
      else alert('Gagal mengunduh (timeout).');
    }
  }, 300);
});


    });
</script>
<?= $this->endSection() ?>
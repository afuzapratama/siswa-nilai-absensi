<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title ?></h1>

    <!-- Card Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Laporan Penilaian</h6>
        </div>
        <div class="card-body">
            <form id="filterLaporanNilai">
                <?= csrf_field() ?>
                <div class="row">
                    <!-- Pilih Kelas -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="id_kelas">Pilih Kelas</label>
                            <!-- [FIX] Menggunakan $kelas_list, bukan $kelas_filter -->
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

                    <!-- Pilih Mata Pelajaran -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="id_mapel">Pilih Mata Pelajaran</label>
                            <select class="form-control" id="id_mapel" name="id_mapel" required>
                                <option value="">-- Pilih Kelas Dulu --</option>
                            </select>
                        </div>
                    </div>

                    <!-- Filter Judul Penilaian (Multi-select) -->
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="id_header">Pilih Judul Penilaian</label>
                            <select class="form-control selectpicker" id="id_header" name="id_header[]" multiple data-live-search="true" title="-- Pilih Mapel Dulu --" data-actions-box="true" data-select-all-text="Pilih Semua" data-deselect-all-text="Batal" required>
                                <!-- Opsi di-load oleh AJAX -->
                            </select>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" id="btnTampilkan">
                    <i class="fas fa-search"></i> Tampilkan
                </button>
            </form>
        </div>
    </div>

    <!-- Card Hasil Laporan -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Hasil Laporan Penilaian</h6>
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
            <div id="tabelLaporanContainer">
                <div class="text-center text-muted">
                    Silakan pilih filter di atas dan klik "Tampilkan" untuk melihat laporan.
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
<!-- Script khusus untuk Halaman Laporan Nilai -->
<script>
    $(document).ready(function() {
        let selectKelas = $('#id_kelas');
        let selectMapel = $('#id_mapel');
        let selectHeader = $('#id_header');

        // 1. AJAX: Ambil Mata Pelajaran saat Kelas dipilih
        selectKelas.on('change', function() {
            let id_kelas = $(this).val();
            selectMapel.html('<option value="">Loading...</option>').prop('disabled', true);
            selectHeader.html('').selectpicker('refresh').prop('disabled', true);

            if (!id_kelas) {
                selectMapel.html('<option value="">-- Pilih Kelas Dulu --</option>').prop('disabled', false);
                return;
            }

            $.ajax({
                url: '<?= site_url('admin/report/ajax-get-mapel') ?>', // [FIX] Rute yang benar
                type: 'POST',
                data: {
                    id_kelas: id_kelas,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: 'JSON',
                success: function(response) {
                    selectMapel.html('<option value="">-- Pilih Mata Pelajaran --</option>').prop('disabled', false);
                    if (response.status === 'success') {
                        $.each(response.mapel, function(key, value) {
                            selectMapel.append('<option value="' + value.id_mapel + '">' + value.nama_mapel + '</option>');
                        });
                    }
                },
                error: function() {
                    selectMapel.html('<option value="">Gagal memuat mapel</option>').prop('disabled', false);
                }
            });
        });

        // 2. AJAX: Ambil Judul Penilaian saat Mapel dipilih
        selectMapel.on('change', function() {
            let id_kelas = selectKelas.val();
            let id_mapel = $(this).val();
            selectHeader.html('').prop('disabled', true);

            if (!id_mapel || !id_kelas) {
                selectHeader.selectpicker('refresh');
                return;
            }

            $.ajax({
                url: '<?= site_url('admin/report/get-judul') ?>',
                type: 'POST',
                data: {
                    id_kelas: id_kelas,
                    id_mapel: id_mapel,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: 'JSON',
                success: function(response) {
                    if (response.length > 0) {
                        $.each(response, function(key, value) {
                            selectHeader.append('<option value="' + value.id_header + '">' + value.judul_penilaian + '</option>');
                        });
                        selectHeader.prop('disabled', false).selectpicker('refresh');
                    } else {
                        selectHeader.attr('title', '-- Tidak ada data --').selectpicker('refresh');
                    }
                },
                error: function() {
                    selectHeader.attr('title', '-- Gagal memuat --').selectpicker('refresh');
                }
            });
        });

        // 3. AJAX: Tombol "Tampilkan"
        $('#filterLaporanNilai').on('submit', function(e) {
            e.preventDefault();
            let btn = $('#btnTampilkan');
            let container = $('#tabelLaporanContainer');
            let formData = $(this).serialize();

            $.ajax({
                url: '<?= site_url('admin/report/tampilkan-data') ?>', // [FIX] Rute yang benar
                type: 'POST',
                data: formData,
                beforeSend: function() {
                    btn.prop('disabled', true).html('<i class="fas fa-spin fa-spinner"></i> Menampilkan...');
                    container.html('<div class="text-center text-muted py-5"><i class="fas fa-spin fa-spinner fa-2x"></i><br>Memuat data...</div>');
                    $('#btnExportCSV').prop('disabled', true);
                    $('#btnExportPDF').prop('disabled', true);
                },
                complete: function() {
                    btn.prop('disabled', false).html('<i class="fas fa-search"></i> Tampilkan');
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
                id_kelas: selectKelas.val(),
                id_header: selectHeader.val().join(',') // Ubah array jadi string dipisah koma
            };
            let baseUrl = '<?= site_url('admin/report/export-csv') ?>'.replace('export-csv', format);
            return baseUrl + '?' + $.param(params);
        }

        // 4. Tombol Export CSV
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


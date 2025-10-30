<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title ?></h1>

    <!-- Card Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter Siswa</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <!-- Pilih Kelas (Filter Utama) -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="filter_kelas">Pilih Kelas (Tahun Ajaran Aktif)</label>
                        <select class="form-control" id="filter_kelas" name="id_kelas">
                            <option value="">-- Tampilkan Siswa Berdasarkan Kelas --</option>
                            <?php foreach ($kelas_list as $kelas) : ?>
                                <option value="<?= $kelas['id_kelas'] ?>">
                                    <?= esc($kelas['nama_kelas']) ?> (<?= esc($kelas['kode_kelas']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Cari Nama / NIS -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="search_keyword">Cari Nama / NIS</label>
                        <input type="text" class="form-control" id="search_keyword" name="keyword" placeholder="Masukkan nama atau NIS...">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Daftar Siswa -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Siswa</h6>
            <div>
                <button class="btn btn-primary" id="btnTambah">
                    <i class="fas fa-plus"></i> Tambah Manual
                </button>
                <button class="btn btn-success" id="btnImport">
                    <i class="fas fa-file-csv"></i> Import CSV
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Siswa</th>
                            <th>NIS</th>
                            <th>Kelas (Tahun Ajaran)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="siswa-table-body">
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                Silakan pilih kelas terlebih dahulu untuk menampilkan data siswa.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Tempat untuk Pager (Link Paginasi) -->
            <div id="pagination-links" class="d-flex justify-content-center">
                <!-- Link paginasi akan di-load di sini oleh AJAX -->
            </div>

        </div>
    </div>
</div>

<!-- Modal CRUD (Tambah/Edit Manual) -->
<div class="modal fade" id="modalSiswa" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Form Siswa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formSiswa">
                <?= csrf_field() ?>
                <input type="hidden" name="id_siswa" id="id_siswa">
                <div class="modal-body">
                    <div class="alert alert-danger d-none" id="error-alert-manual"></div>

                    <div class="form-group">
                        <label for="id_kelas">Kelas (Tahun Ajaran Aktif)</label>
                        <select class="form-control" id="id_kelas" name="id_kelas" required>
                            <option value="">-- Pilih Kelas --</option>
                            <?php foreach ($kelas_aktif as $kelas) : ?>
                                <option value="<?= $kelas['id_kelas'] ?>">
                                    <?= esc($kelas['nama_kelas']) ?> (<?= esc($kelas['kode_kelas']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nis">NIS (Nomor Induk Siswa)</label>
                        <input type="text" class="form-control" id="nis" name="nis" placeholder="Kosongkan jika siswa baru">
                        <small class="text-muted">NIS harus unik jika diisi.</small>
                    </div>
                    <div class="form-group">
                        <label for="nama_siswa">Nama Siswa</label>
                        <input type="text" class="form-control" id="nama_siswa" name="nama_siswa" required>
                        <small class="text-muted">Nama akan otomatis diubah ke huruf besar.</small>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanManual">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Import CSV -->
<div class="modal fade" id="modalImport" tabindex="-1" role="dialog" aria-labelledby="modalImportLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalImportLabel">Import Siswa dari CSV</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formImport" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="alert alert-danger d-none" id="error-alert-import"></div>
                    <div class="alert alert-info">
                        <strong>Petunjuk:</strong>
                        <ol class="mb-0 pl-3">
                            <li><a href="<?= base_url('template-siswa.csv') ?>" class="text-primary font-weight-bold">Download template CSV</a>.</li>
                            <li>Isi data sesuai format: <strong>Nama Siswa, NIS, Kode Kelas</strong>.</li>
                            <li>Kolom NIS boleh dikosongkan atau diisi tanda strip (-).</li>
                            <li>Kode Kelas harus sesuai dengan yang ada di menu "Kelas" (untuk tahun ajaran aktif).</li>
                        </ol>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="file_csv">Upload File CSV</label>
                        <input type="file" class="form-control-file" id="file_csv" name="file_csv" accept=".csv" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success" id="btnUploadImport">
                        <i class="fas fa-upload"></i> Upload dan Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
$(function () {
    const Toast = Swal.mixin({ toast:true, position:'top-end', showConfirmButton:false, timer:3000, timerProgressBar:true,
    didOpen:t=>{t.addEventListener('mouseenter',Swal.stopTimer);t.addEventListener('mouseleave',Swal.resumeTimer);} });

  let searchTimer, currentRequest=null;

  function loadSiswa(page=1){
    const id_kelas = $('#filter_kelas').val();
    const keyword  = $('#search_keyword').val();

    if (currentRequest) currentRequest.abort();

    if (id_kelas) $('#btnTambah').prop('disabled',false).removeClass('btn-secondary').addClass('btn-primary');
    else          $('#btnTambah').prop('disabled',true).removeClass('btn-primary').addClass('btn-secondary');

    currentRequest = $.ajax({
      url: '<?= site_url('admin/siswa/fetch-by-kelas') ?>',
      type: 'GET',
      dataType: 'json',                                   // <-- JSON sekarang
      data: { page_siswa: page, id_kelas, keyword },      // <-- penting: page_siswa
      beforeSend: function(){
        $('#siswa-table-body').html('<tr><td colspan="5" class="text-center"><i class="fas fa-spin fa-spinner fa-2x"></i><br>Memuat data...</td></tr>');
        $('#pagination-links').empty();
      },
      success: function(res){
        $('#siswa-table-body').html(res.rows || '');
        $('#pagination-links').html(res.pager || '');
      },
      error: function(_, textStatus){
        if (textStatus!=='abort'){
          $('#siswa-table-body').html('<tr><td colspan="5" class="text-center text-danger">Gagal memuat data. Silakan coba lagi.</td></tr>');
          $('#pagination-links').empty();
        }
      },
      complete: function(){ currentRequest=null; }
    });
  }

  // Filter & cari
  $('#filter_kelas').on('change', ()=> loadSiswa(1));
  $('#search_keyword').on('keyup', function(){
    clearTimeout(searchTimer);
    searchTimer = setTimeout(()=> loadSiswa(1), 500);
  });

  // Klik pager — template-agnostic (default CI4 tidak selalu beri .page-link)
    $(document).on('click', '#pagination-links a', function (e) {
      e.preventDefault();
      const href = $(this).attr('href') || '';
      const url  = new URL(href, window.location.origin);
      const page = url.searchParams.get('page_siswa') || 1; // grup 'siswa'
      loadSiswa(page);
    });
  // default state
  $('#btnTambah').prop('disabled', true).removeClass('btn-primary').addClass('btn-secondary');

  // ===== CRUD & IMPORT tetap sama punyamu =====
  $('#btnTambah').on('click', function () {
    if ($(this).prop('disabled')) return;
    $('#modalLabel').text('Tambah Siswa');
    $('#formSiswa')[0].reset();
    $('#id_siswa').val('');
    $('#error-alert-manual').addClass('d-none').html('');
    const k = $('#filter_kelas').val();
    if (k) $('#id_kelas').val(k);
    $('#modalSiswa').modal('show');
  });

  $(document).on('click', '.btn-edit', function () {
    const id = $(this).data('id');
    $('#modalLabel').text('Edit Siswa');
    $('#formSiswa')[0].reset();
    $('#error-alert-manual').addClass('d-none').html('');
    $.post('<?= site_url('admin/siswa/fetch') ?>', { id_siswa: id }, function (r) {
      if (r.status === 'success') {
        $('#id_siswa').val(r.data.id_siswa);
        $('#id_kelas').val(r.data.id_kelas);
        $('#nis').val(r.data.nis);
        $('#nama_siswa').val(r.data.nama_siswa);
        $('#modalSiswa').modal('show');
      } else {
        Toast.fire({ icon: 'error', title: r.message });
      }
    }, 'json');
  });

  $('#formSiswa').on('submit', function (e) {
    e.preventDefault();
    $.ajax({
      url: '<?= site_url('admin/siswa/save') ?>',
      type: 'POST',
      dataType: 'json',
      data: $(this).serialize(),
      beforeSend: () => $('#btnSimpanManual').prop('disabled', true).html('<i class="fas fa-spin fa-spinner"></i>'),
      complete: () => $('#btnSimpanManual').prop('disabled', false).html('Simpan'),
      success: function (r) {
        if (r.status === 'success') {
          $('#modalSiswa').modal('hide');
          Toast.fire({ icon: 'success', title: r.message });
          loadSiswa();
        } else {
          let errors = '<ul>';
          $.each(r.errors, (k, v) => errors += `<li>${v}</li>`);
          errors += '</ul>';
          $('#error-alert-manual').html(errors).removeClass('d-none');
        }
      }
    });
  });

  $(document).on('click', '.btn-delete', function () {
    const id = $(this).data('id');
    Swal.fire({
      title: 'Apakah Anda yakin?',
      text: 'Data siswa (termasuk nilai & absensi) akan dihapus permanen!',
      icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#3085d6',
      confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal'
    }).then((res) => {
      if (res.isConfirmed) {
        $.post('<?= site_url('admin/siswa/delete') ?>', { id_siswa: id }, function (r) {
          Toast.fire({ icon: r.status, title: r.message });
          loadSiswa();
        }, 'json');
      }
    });
  });

  $('#btnImport').on('click', function () {
    $('#formImport')[0].reset();
    $('#error-alert-import').addClass('d-none').html('');
    $('#modalImport').modal('show');
  });

  $('#formImport').on('submit', function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    $.ajax({
      url: '<?= site_url('admin/siswa/import-csv') ?>',
      type: 'POST', data: formData, processData: false, contentType: false, dataType: 'json',
      beforeSend: () => $('#btnUploadImport').prop('disabled', true).html('<i class="fas fa-spin fa-spinner"></i> Mengimport...'),
      complete:   () => $('#btnUploadImport').prop('disabled', false).html('<i class="fas fa-upload"></i> Upload dan Import'),
      success: function (r) {
        if (r.status === 'success') {
          $('#modalImport').modal('hide');
          Swal.fire({ title: 'Import Selesai', text: r.message, icon: 'success' });
          loadSiswa();
        } else {
          $('#error-alert-import').html(r.message).removeClass('d-none');
        }
      },
      error: function () {
        $('#error-alert-import').html('Terjadi kesalahan server. Silakan coba lagi.').removeClass('d-none');
      }
    });
  });
});
</script>



<?= $this->endSection() ?>


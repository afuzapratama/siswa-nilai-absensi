<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Tombol Tambah -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <button class="btn btn-primary" id="btnTambah">
                <i class="fas fa-plus"></i> Tambah Mata Pelajaran
            </button>
        </div>
        <div class="card-body">
            <!-- Tabel Data -->
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Mapel</th>
                            <th>Nama Mata Pelajaran</th>
                            <th>Kelas Terhubung (Tahun Aktif)</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($mapel as $m) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($m['kode_mapel']) ?></td>
                                <td><?= esc($m['nama_mapel']) ?></td>
                                <td>
                                    <?php if (empty($m['kelas_terhubung'])) : ?>
                                        <span class="badge badge-secondary">Belum terhubung</span>
                                    <?php else : ?>
                                        <?php foreach ($m['kelas_terhubung'] as $k) : ?>
                                            <span class="badge badge-info m-1">
                                                <?= esc($k['nama_kelas']) ?> (<?= esc($k['kode_kelas']) ?>)
                                            </span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning btn-edit" data-id="<?= $m['id_mapel'] ?>">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $m['id_mapel'] ?>">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- Modal CRUD Mata Pelajaran -->
<div class="modal fade" id="modalMapel" tabindex="-1" role="dialog" aria-labelledby="modalLabelMapel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> <!-- Buat modal lebih besar (modal-lg) -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabelMapel">Form Mata Pelajaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formMapel">
                <?= csrf_field() ?>
                <input type="hidden" name="id_mapel" id="id_mapel">
                <div class="modal-body">
                    <!-- Error Alert -->
                    <div class="alert alert-danger d-none" id="error-alert"></div>

                    <div class="form-group">
                        <label for="kode_mapel">Kode Mapel</label>
                        <input type="text" class="form-control" id="kode_mapel" name="kode_mapel" placeholder="Contoh: MTK-01" required>
                    </div>
                    <div class="form-group">
                        <label for="nama_mapel">Nama Mata Pelajaran</label>
                        <input type="text" class="form-control" id="nama_mapel" name="nama_mapel" placeholder="Contoh: Matematika Wajib" required>
                    </div>
                    <hr>
                    <div class="form-group">
                        <label for="id_kelas">Hubungkan ke Kelas (Tahun Ajaran Aktif)</label>
                        <select class="form-control selectpicker" id="id_kelas" name="id_kelas[]" multiple data-live-search="true" title="-- Pilih Kelas --">
                            <?php foreach ($kelas_aktif as $k) : ?>
                                <option value="<?= $k['id_kelas'] ?>">
                                    <?= esc($k['nama_kelas']) ?> (<?= esc($k['kode_kelas']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="form-text text-muted">Anda bisa memilih lebih dari satu kelas.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpan">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    $(document).ready(function() {
        // Inisialisasi Bootstrap-Select
        // Perlu event 'shown.bs.modal' agar dropdown render dengan benar di dalam modal
        $('#modalMapel').on('shown.bs.modal', function () {
            $('.selectpicker').selectpicker('refresh');
        });

        // Konfigurasi SweetAlert Toast
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // 1. Tombol Tambah
        $('#btnTambah').on('click', function() {
            $('#modalLabelMapel').text('Tambah Mata Pelajaran');
            $('#formMapel')[0].reset();
            $('#id_mapel').val('');
            $('#error-alert').addClass('d-none').html('');
            
            // Reset bootstrap-select
            $('#id_kelas').selectpicker('val', []); 
            $('#id_kelas').selectpicker('refresh');

            $('#modalMapel').modal('show');
        });

        // 2. Tombol Edit
        $('.btn-edit').on('click', function() {
            let id = $(this).data('id');
            $('#modalLabelMapel').text('Edit Mata Pelajaran');
            $('#error-alert').addClass('d-none').html('');

            $.ajax({
                url: '<?= site_url('admin/mata-pelajaran/fetch') ?>',
                type: 'POST',
                data: { id: id },
                dataType: 'JSON',
                success: function(response) {
                    if (response.status === 'success') {
                        // Isi data mapel
                        $('#id_mapel').val(response.data_mapel.id_mapel);
                        $('#kode_mapel').val(response.data_mapel.kode_mapel);
                        $('#nama_mapel').val(response.data_mapel.nama_mapel);

                        // Atur kelas yang ter-select di multi-select
                        $('#id_kelas').selectpicker('val', response.kelas_ids);
                        $('#id_kelas').selectpicker('refresh');

                        $('#modalMapel').modal('show');
                    } else {
                        Toast.fire({ icon: 'error', title: response.message });
                    }
                }
            });
        });

        // 3. Submit Form (Save / Update)
        $('#formMapel').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: '<?= site_url('admin/mata-pelajaran/save') ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'JSON',
                beforeSend: function() {
                    $('#btnSimpan').prop('disabled', true).html('<i class="fas fa-spin fa-spinner"></i>');
                },
                complete: function() {
                    $('#btnSimpan').prop('disabled', false).html('Simpan');
                },
                success: function(response) {
                    if (response.status === 'success') {
                        $('#modalMapel').modal('hide');
                        Toast.fire({ icon: 'success', title: response.message })
                        .then(() => { location.reload(); });
                    } else {
                        // Tampilkan error validasi
                        let errors = '<ul>';
                        $.each(response.errors, function(key, value) {
                            errors += '<li>' + value + '</li>';
                        });
                        errors += '</ul>';
                        $('#error-alert').html(errors).removeClass('d-none');
                    }
                }
            });
        });

        // 4. Tombol Delete
        $('.btn-delete').on('click', function() {
            let id = $(this).data('id');
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Menghapus mapel akan menghapus relasinya ke semua kelas.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= site_url('admin/mata-pelajaran/delete') ?>',
                        type: 'POST',
                        data: { id: id },
                        dataType: 'JSON',
                        success: function(response) {
                            Toast.fire({ icon: response.status, title: response.message })
                            .then(() => { location.reload(); });
                        }
                    });
                }
            });
        });

    });
</script>
<?= $this->endSection() ?>

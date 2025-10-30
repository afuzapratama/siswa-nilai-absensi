<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">


    <!-- Tombol Tambah -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <button class="btn btn-primary" id="btnTambah">
                <i class="fas fa-plus"></i> Tambah Tahun Ajaran
            </button>
        </div>
        <div class="card-body">
            <!-- Tempat Alert -->
            <div id="alert-placeholder"></div>

            <!-- Tabel Data -->
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tahun Ajaran</th>
                            <th>Semester</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($tahun_ajaran as $ta) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($ta['tahun_ajaran']) ?></td>
                                <td><?= esc($ta['semester']) ?></td>
                                <td>
                                    <?php if ($ta['status'] == 'aktif') : ?>
                                        <span class="badge badge-success">Aktif</span>
                                    <?php else : ?>
                                        <span class="badge badge-secondary">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($ta['status'] == 'nonaktif') : ?>
                                        <button class="btn btn-sm btn-info btn-set-status" data-id="<?= $ta['id_tahun_ajaran'] ?>">
                                            <i class="fas fa-check"></i> Aktifkan
                                        </button>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-warning btn-edit" data-id="<?= $ta['id_tahun_ajaran'] ?>">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $ta['id_tahun_ajaran'] ?>">
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

<!-- Modal CRUD -->
<div class="modal fade" id="modalTahunAjaran" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Form Tahun Ajaran</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formTahunAjaran">
                <?= csrf_field() ?>
                <input type="hidden" name="id_tahun_ajaran" id="id_tahun_ajaran">
                <div class="modal-body">
                    <!-- Error Alert -->
                    <div class="alert alert-danger d-none" id="error-alert"></div>

                    <div class="form-group">
                        <label for="tahun_ajaran">Tahun Ajaran</label>
                        <input type="text" class="form-control" id="tahun_ajaran" name="tahun_ajaran" placeholder="Contoh: 2024/2025" required>
                    </div>
                    <div class="form-group">
                        <label for="semester">Semester</label>
                        <select class="form-control" id="semester" name="semester" required>
                            <option value="">-- Pilih Semester --</option>
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                        </select>
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
            $('#modalLabel').text('Tambah Tahun Ajaran');
            $('#formTahunAjaran')[0].reset();
            $('#id_tahun_ajaran').val('');
            $('#error-alert').addClass('d-none').html('');
            $('#modalTahunAjaran').modal('show');
        });

        // 2. Tombol Edit
        $('.btn-edit').on('click', function() {
            let id = $(this).data('id');
            $('#modalLabel').text('Edit Tahun Ajaran');
            $('#error-alert').addClass('d-none').html('');

            $.ajax({
                url: '<?= site_url('admin/tahun-ajaran/fetch') ?>',
                type: 'POST',
                data: {
                    id: id,
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                dataType: 'JSON',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#id_tahun_ajaran').val(response.data.id_tahun_ajaran);
                        $('#tahun_ajaran').val(response.data.tahun_ajaran);
                        $('#semester').val(response.data.semester);
                        $('#modalTahunAjaran').modal('show');
                    } else {
                        Toast.fire({
                            icon: 'error',
                            title: response.message
                        });
                    }
                }
            });
        });

        // 3. Submit Form (Save / Update)
        $('#formTahunAjaran').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: '<?= site_url('admin/tahun-ajaran/save') ?>',
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
                        $('#modalTahunAjaran').modal('hide');
                        Toast.fire({
                            icon: 'success',
                            title: response.message
                        }).then(() => {
                            location.reload(); // Refresh halaman
                        });
                    } else {
                        // Tampilkan error validasi
                        let errors = '<ul>';
                        $.each(response.errors, function(key, value) {
                            errors += '<li>' + value + '</li>';
                        });
                        errors += '</ul>';
                        $('#error-alert').html(errors).removeClass('d-none');
                    }
                },
                error: function() {
                     Toast.fire({
                        icon: 'error',
                        title: 'Gagal menyimpan data.'
                    });
                }
            });
        });

        // 4. Tombol Delete
        $('.btn-delete').on('click', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= site_url('admin/tahun-ajaran/delete') ?>',
                        type: 'POST',
                        data: {
                            id: id,
                            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                        },
                        dataType: 'JSON',
                        success: function(response) {
                            Toast.fire({
                                icon: response.status,
                                title: response.message
                            }).then(() => {
                                location.reload();
                            });
                        }
                    });
                }
            });
        });

        // 5. Tombol Set Status Aktif
        $('.btn-set-status').on('click', function() {
            let id = $(this).data('id');

            Swal.fire({
                title: 'Aktifkan Tahun Ajaran?',
                text: "Tahun ajaran lain yang aktif akan dinonaktifkan.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Aktifkan!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= site_url('admin/tahun-ajaran/set-status') ?>',
                        type: 'POST',
                        data: {
                            id: id,
                            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                        },
                        dataType: 'JSON',
                        success: function(response) {
                            Toast.fire({
                                icon: response.status,
                                title: response.message
                            }).then(() => {
                                location.reload();
                            });
                        }
                    });
                }
            });
        });

    });
</script>
<?= $this->endSection() ?>

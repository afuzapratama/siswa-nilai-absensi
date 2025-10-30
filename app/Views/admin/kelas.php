<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Tombol Tambah -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <button class="btn btn-primary" id="btnTambah">
                <i class="fas fa-plus"></i> Tambah Kelas
            </button>
        </div>
        <div class="card-body">
            <!-- Tabel Data -->
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Kelas</th>
                            <th>Nama Kelas</th>
                            <th>Tahun Ajaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($kelas as $k) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($k['kode_kelas']) ?></td>
                                <td><?= esc($k['nama_kelas']) ?></td>
                                <td><?= esc($k['tahun_ajaran']) ?> (<?= esc($k['semester']) ?>)</td>
                                <td>
                                    <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $k['id_kelas'] ?>">
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
<div class="modal fade" id="modalKelas" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Form Kelas</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formKelas">
                <?= csrf_field() ?>
                <input type="hidden" name="id_kelas" id="id_kelas">
                <div class="modal-body">
                    <!-- Error Alert -->
                    <div class="alert alert-danger d-none" id="error-alert"></div>

                    <div class="form-group">
                        <label for="id_tahun_ajaran">Tahun Ajaran</label>
                        <select class="form-control" id="id_tahun_ajaran" name="id_tahun_ajaran" required>
                            <option value="">-- Pilih Tahun Ajaran --</option>
                            <?php foreach ($tahun_ajaran as $ta) : ?>
                                <option value="<?= $ta['id_tahun_ajaran'] ?>" <?= ($ta['status'] == 'aktif') ? 'selected' : '' ?>>
                                    <?= esc($ta['tahun_ajaran']) ?> (<?= esc($ta['semester']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="kode_kelas">Kode Kelas</label>
                       <input type="text" class="form-control" id="kode_kelas" name="kode_kelas" placeholder="Kosongkan untuk auto-generate">
                    </div>
                    <div class="form-group">
                        <label for="nama_kelas">Nama Kelas</label>
                        <input type="text" class="form-control" id="nama_kelas" name="nama_kelas" placeholder="Contoh: XI TKJ 1" required>
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
            $('#modalLabel').text('Tambah Kelas');
            $('#formKelas')[0].reset();
            // Baris 'id_kelas' DIHAPUS
            $('#error-alert').addClass('d-none').html('');
            $('#modalKelas').modal('show');
            // Otomatis pilih tahun ajaran yg aktif
            $('#id_tahun_ajaran option[selected]').prop('selected', true);
        });

        // 2. Tombol Edit (DIHAPUS)
        // $('.btn-edit').on('click', function() { ... });


        // 3. Submit Form (HANYA Save)
        $('#formKelas').on('submit', function(e) {
            e.preventDefault();

            $.ajax({
                url: '<?= site_url('admin/kelas/save') ?>',
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
                        $('#modalKelas').modal('hide');
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

        // 4. Tombol Delete (Tetap sama)
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
                        url: '<?= site_url('admin/kelas/delete') ?>',
                        type: 'POST',
                        data: { id: id },
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

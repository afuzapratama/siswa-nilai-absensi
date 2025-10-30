<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <!-- Tombol Tambah -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Manajemen User</h6>
        </div>
        <div class="card-body">
            <button class="btn btn-primary mb-3" id="btnTambahUser">
                <i class="fas fa-plus"></i> Tambah User
            </button>

            <!-- Tabel Data -->
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($users as $user) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= esc($user['username']) ?></td>
                                <td><?= esc($user['nama_lengkap']) ?></td>
                                <td>
                                    <button class="btn btn-sm btn-warning btn-edit" data-id="<?= $user['id'] ?>">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-info btn-ganti-pass" data-id="<?= $user['id'] ?>" data-username="<?= esc($user['username']) ?>">
                                        <i class="fas fa-key"></i> Ganti Password
                                    </button>
                                    <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $user['id'] ?>">
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

<!-- Modal CRUD User (Tambah/Edit) -->
<div class="modal fade" id="modalUser" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Form User</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formUser">
                <?= csrf_field() ?>
                <input type="hidden" name="id_user" id="id_user">
                <div class="modal-body">
                    <!-- Error Alert -->
                    <div class="alert alert-danger d-none" id="error-alert"></div>

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="nama_lengkap">Nama Lengkap</label>
                        <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                    </div>
                    
                    <!-- Grup Password (hanya tampil saat Tambah Baru) -->
                    <div id="grup-password">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Min. 6 karakter">
                        </div>
                        <div class="form-group">
                            <label for="password_confirm">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm">
                        </div>
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

<!-- Modal Ganti Password -->
<div class="modal fade" id="modalPassword" tabindex="-1" role="dialog" aria-labelledby="modalPassLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalPassLabel">Ganti Password untuk <strong id="username-pass"></strong></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formPassword">
                <?= csrf_field() ?>
                <input type="hidden" name="id_user_pass" id="id_user_pass">
                <div class="modal-body">
                    <!-- Error Alert -->
                    <div class="alert alert-danger d-none" id="error-alert-pass"></div>
                    
                    <div class="form-group">
                        <label for="new_password">Password Baru</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" placeholder="Min. 6 karakter" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password_confirm">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="new_password_confirm" name="new_password_confirm" required>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="btnSimpanPassword">Simpan Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    $(document).ready(function() {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });

        // 1. Tombol Tambah User
        $('#btnTambahUser').on('click', function() {
            $('#modalLabel').text('Tambah User Baru');
            $('#formUser')[0].reset();
            $('#id_user').val('');
            $('#error-alert').addClass('d-none').html('');
            $('#grup-password').show(); // Tampilkan field password
            $('#modalUser').modal('show');
        });

        // 2. Tombol Edit
        $('.btn-edit').on('click', function() {
            let id = $(this).data('id');
            $('#modalLabel').text('Edit User');
            $('#error-alert').addClass('d-none').html('');
            $('#grup-password').hide(); // Sembunyikan field password

            $.ajax({
                url: '<?= site_url('admin/users/fetch') ?>',
                type: 'POST',
                data: { id: id },
                dataType: 'JSON',
                success: function(response) {
                    if (response.status === 'success') {
                        $('#id_user').val(response.data.id);
                        $('#username').val(response.data.username);
                        $('#nama_lengkap').val(response.data.nama_lengkap);
                        $('#modalUser').modal('show');
                    } else {
                        Toast.fire({ icon: 'error', title: response.message });
                    }
                }
            });
        });

        // 3. Submit Form User (Tambah/Edit)
        $('#formUser').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: '<?= site_url('admin/users/save') ?>',
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
                        $('#modalUser').modal('hide');
                        Toast.fire({ icon: 'success', title: response.message })
                            .then(() => location.reload());
                    } else {
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
                text: "User yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= site_url('admin/users/delete') ?>',
                        type: 'POST',
                        data: { id: id },
                        dataType: 'JSON',
                        success: function(response) {
                            Toast.fire({ icon: response.status, title: response.message })
                                .then(() => location.reload());
                        }
                    });
                }
            });
        });

        // 5. Tombol Ganti Password (Membuka modal)
        $('.btn-ganti-pass').on('click', function() {
            let id = $(this).data('id');
            let username = $(this).data('username');
            
            $('#formPassword')[0].reset();
            $('#id_user_pass').val(id);
            $('#username-pass').text(username);
            $('#error-alert-pass').addClass('d-none').html('');
            $('#modalPassword').modal('show');
        });

        // 6. Submit Form Ganti Password
        $('#formPassword').on('submit', function(e) {
            e.preventDefault();
            $.ajax({
                url: '<?= site_url('admin/users/change-password') ?>',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'JSON',
                beforeSend: function() {
                    $('#btnSimpanPassword').prop('disabled', true).html('<i class="fas fa-spin fa-spinner"></i>');
                },
                complete: function() {
                    $('#btnSimpanPassword').prop('disabled', false).html('Simpan Password');
                },
                success: function(response) {
                    if (response.status === 'success') {
                        $('#modalPassword').modal('hide');
                        Toast.fire({ icon: 'success', title: response.message });
                    } else {
                         let errors = '<ul>';
                        $.each(response.errors, function(key, value) {
                            errors += '<li>' + value + '</li>';
                        });
                        errors += '</ul>';
                        $('#error-alert-pass').html(errors).removeClass('d-none');
                    }
                }
            });
        });

    });
</script>
<?= $this->endSection() ?>

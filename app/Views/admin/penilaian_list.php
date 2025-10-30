<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800"><?= $title ?></h1>

    <!-- Tombol Tambah -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <a href="<?= site_url('admin/penilaian/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Buat Form Penilaian Baru
            </a>
        </div>
        <div class="card-body">

            <?php if (session()->getFlashdata('success')) : ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <!-- Tabel Data -->
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Judul Penilaian</th>
                            <th>Kelas</th>
                            <th>Mata Pelajaran</th>
                            <th>Tgl Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <!-- [FIX] Ganti $headers jadi $forms, dan $s jadi $form -->
                        <?php foreach ($forms as $form) : ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td>
                                    <a href="<?= site_url('admin/penilaian/form/' . $form['id_header']) ?>">
                                        <?= esc($form['judul_penilaian']) ?>
                                    </a>
                                </td>
                                <td><?= esc($form['nama_kelas']) ?></td>
                                <td><?= esc($form['nama_mapel']) ?></td>
                                <td><?= esc(date('d-m-Y H:i', strtotime($form['created_at'] ?? time()))) ?></td>
                                <td>
                                    <a href="<?= site_url('admin/penilaian/form/' . $form['id_header']) ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-edit"></i> Input Nilai
                                    </a>
                                    <button class="btn btn-sm btn-danger btn-delete-form" data-id="<?= $form['id_header'] ?>" data-judul="<?= esc($form['judul_penilaian']) ?>">
                                        <i class="fas fa-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (empty($forms)) : ?>
                            <tr>
                                <td colspan="6" class="text-center">Belum ada form penilaian yang dibuat.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
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
            timerProgressBar: true
        });

        // Tombol Hapus Form
        $('.btn-delete-form').on('click', function() {
            let id_header = $(this).data('id');
            let judul = $(this).data('judul');

            Swal.fire({
                title: 'Hapus Form Penilaian?',
                text: "Anda yakin ingin menghapus form '" + judul + "'? SEMUA NILAI di dalamnya akan hilang permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= site_url('admin/penilaian/delete-form') ?>',
                        type: 'POST',
                        data: {
                            id_header: id_header,
                            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                        },
                        dataType: 'JSON',
                        success: function(response) {
                            if (response.status === 'success') {
                                Toast.fire({
                                    icon: 'success',
                                    title: response.message
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Toast.fire({
                                    icon: 'error',
                                    title: 'Gagal menghapus form.'
                                });
                            }
                        }
                    });
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>


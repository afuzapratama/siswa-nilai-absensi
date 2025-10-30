<?= $this->extend('layout/template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800"><?= $title ?></h1>
    <h5 class="mb-4">
        Kelas: <span class="text-primary"><?= esc($header['nama_kelas']) ?></span> |
        Mapel: <span class="text-primary"><?= esc($header['nama_mapel']) ?></span> |
        TA: <span class="text-primary"><?= esc($header['tahun_ajaran']) ?> (<?= esc($header['semester']) ?>)</span>
    </h5>

    <!-- Tombol Kembali -->
    <a href="<?= site_url('admin/penilaian') ?>" class="btn btn-secondary btn-sm mb-3">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar Form
    </a>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Daftar Siswa dan Nilai</h6>
            <button class="btn btn-success btn-sm" id="btnTambahKolom">
                <i class="fas fa-plus"></i> Tambah Kolom Penilaian (N)
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tabelNilai" width="100%" cellspacing="0" style="min-width: 700px;">
                    <thead>
                        <tr>
                            <th class="text-center align-middle" style="width: 5%;">No</th>
                            <th class="align-middle" style="min-width: 100px;">Nama Siswa</th>
                            <!-- Kolom Nilai Dinamis -->
                            <?php foreach ($kolom_list as $kolom) : ?>
                                <th class="text-center align-middle" data-id_kolom="<?= $kolom['id_kolom'] ?>" style="min-width: 80px;">
                                    <?= esc($kolom['nama_kolom']) ?>
                                    <button class="btn btn-danger btn-xs py-0 px-1 btn-hapus-kolom" data-id_kolom="<?= $kolom['id_kolom'] ?>" data-nama_kolom="<?= esc($kolom['nama_kolom']) ?>" title="Hapus kolom <?= esc($kolom['nama_kolom']) ?>">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($siswa_list as $siswa) : ?>
                            <tr data-id_siswa="<?= $siswa['id_siswa'] ?>">
                                <td class="text-center"><?= $no++ ?></td>
                                <td><?= esc($siswa['nama_siswa']) ?></td>

                                <!-- Input Nilai Dinamis -->
                                <?php foreach ($kolom_list as $kolom) : ?>
                                    <td class="text-center">
                                        <!-- [FIX] Tambahkan style width & margin auto agar rapi -->
                                        <input type="number" class="form-control form-control-sm input-nilai" style="width: 80px; margin: 0 auto;" min="0" max="100" step="0.5" data-id_kolom="<?= $kolom['id_kolom'] ?>" data-id_siswa="<?= $siswa['id_siswa'] ?>" value="<?= $nilai_map[$siswa['id_siswa']][$kolom['id_kolom']] ?? '' ?>">
                                    </td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($siswa_list)) : ?>
                            <tr>
                                <td colspan="<?= count($kolom_list) + 2 ?>" class="text-center">
                                    Belum ada siswa di kelas ini. Silakan tambahkan siswa di menu 'Data Master -> Siswa'.
                                </td>
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
            timer: 2000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // ===========================================
        // FITUR INTI 1: AUTO-SAVE NILAI
        // ===========================================
        let saveTimer; // Timer untuk delay
        $('#tabelNilai').on('input', '.input-nilai', function() {
            clearTimeout(saveTimer); // Hapus timer sebelumnya

            let input = $(this);
            let id_header = <?= $header['id_header'] ?>;
            let id_kolom = input.data('id_kolom');
            let id_siswa = input.data('id_siswa');
            let nilai = input.val();

            // Tampilkan ikon loading di input
            input.css('background-image', 'url(data:image/gif;base64,R0lGODlhEAAQAPIAAP///wAAAMLCwkJCQgAAAGJiYoKCgpKSkiH/C05FVFNDQVBFMi4wAwEAAAAh/hpDcmVhdGVkIHdpdGggYWpheGxvYWQuaW5mbwAh+QQJCgAAACwAAAAAEAAQAAADMwi63P4wyklrE2MIOggZnAdOmGYJRbExwroUmcG2LmDEwnHQLVsYOd2mBzkYDAdKa+dIAAAh+QQJCgAAACwAAAAAEAAQAAADNAi63P5OjCEgG4QMu7DmikRxQlFUYDEZIGBMRVsaqHwctXXf7WEYB4Ag1xjihkMZsiUkKhIAIfkECQoAAAAsAAAAABAAEAAAAzYIujIjK8pByJDMlFYvBoVjHA70GU7xSUJhmKtwHPAKzLO9HMaoKwJZ7Rf8AYPDDzKpZBqfvwQAIfkECQoAAAAsAAAAABAAEAAAAzMIumIlK8oyhpHsnFZvxvoY3NtPHUroMoJyMfAGLgLts9rCTWEA3SPlN7ky2KSgCAACH5BAkKAAAALAAAAAAQABAAAAMyCLrc/jDKSatlQtScKdceCAjDII7HcQ4EMTCpyrCuUBjCYRgHVtqlAiB1YhiCnlsRkAAAIfkECQoAAAAsAAAAABAAEAAAAzYIujIjK8pByJDMlFYvBoVjHA70GU7xSUJhmKtwHPAKzLO9HMaoKwJZ7Rf8AYPDDzKpZBqfvwQAIfkECQoAAAAsAAAAABAAEAAAAzMIumIlK8oyhpHsnFZvxvoY3NtPHUroMoJyMfAGLgLts9rCTWEA3SPlN7ky2KSgCAACH5BAkKAAAALAAAAAAQABAAAAMyCLrc/jDKSatlQtScKdceCAjDII7HcQ4EMTCpyrCuUBjCYRgHVtqlAiB1YhiCnlsRkAAAOwAAAAAAAAAAAA==)');
            input.css('background-repeat', 'no-repeat');
            input.css('background-position', 'center right 5px');

            saveTimer = setTimeout(() => {
                $.ajax({
                    url: "<?= site_url('admin/penilaian/save-nilai') ?>",
                    type: "POST",
                    data: {
                        id_header: id_header,
                        id_kolom: id_kolom,
                        id_siswa: id_siswa,
                        nilai: nilai,
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    dataType: 'JSON',
                    success: function(response) {
                        if (response.status === 'error') {
                            Toast.fire({
                                icon: 'error',
                                title: response.message
                            });
                        } else {
                            Toast.fire({
                                icon: 'success',
                                title: 'Tersimpan.'
                            });
                        }
                    },
                    error: function() {
                        Toast.fire({
                            icon: 'error',
                            title: 'Koneksi error.'
                        });
                    },
                    complete: function() {
                        // Hilangkan ikon loading
                        input.css('background-image', 'none');
                    }
                });
            }, 750); // Delay 750ms
        });

        // ===========================================
        // FITUR INTI 2: TAMBAH KOLOM
        // ===========================================
        $('#btnTambahKolom').on('click', function() {
            Swal.fire({
                title: 'Tambah Kolom Penilaian Baru',
                input: 'text',
                inputLabel: 'Nama Kolom (Contoh: N2, N3, Tugas 1, Praktek)',
                
                // [FIX] Menggunakan $kolom_list (dari Controller) bukan $kolom (null)
                inputValue: 'N' + (<?= count($kolom_list) ?> + 1),

                inputPlaceholder: 'Masukkan nama kolom',
                showCancelButton: true,
                confirmButtonText: 'Simpan & Tambah',
                cancelButtonText: 'Batal',
                inputValidator: (value) => {
                    if (!value) {
                        return 'Nama kolom tidak boleh kosong!'
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    let nama_kolom = result.value;

                    // Tampilkan loading global
                    Swal.fire({
                        title: 'Menyimpan...',
                        text: 'Menambahkan kolom baru ke database.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });

                    $.ajax({
                        url: "<?= site_url('admin/penilaian/add-kolom') ?>",
                        type: "POST",
                        data: {
                            id_header: <?= $header['id_header'] ?>,
                            nama_kolom: nama_kolom, // Kirim nama kolom baru
                            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                        },
                        dataType: 'JSON',
                        success: function(response) {
                            if (response.status === 'success') {
                                Toast.fire({
                                    icon: 'success',
                                    title: 'Kolom berhasil ditambahkan.'
                                }).then(() => {
                                    location.reload(); // Reload halaman untuk memuat kolom baru
                                });
                            } else {
                                Swal.fire('Gagal', 'Gagal menambah kolom.', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Terjadi kesalahan AJAX.', 'error');
                        }
                    });
                }
            });
        });

        // ===========================================
        // FITUR INTI 3: HAPUS KOLOM
        // ===========================================
        $('#tabelNilai').on('click', '.btn-hapus-kolom', function() {
            let id_kolom = $(this).data('id_kolom');
            let nama_kolom = $(this).data('nama_kolom');

            Swal.fire({
                title: 'Hapus Kolom ' + nama_kolom + '?',
                text: "Anda yakin? Semua nilai siswa di kolom " + nama_kolom + " ini akan dihapus permanen.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Tampilkan loading global
                    Swal.fire({
                        title: 'Menghapus...',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading()
                        }
                    });

                    $.ajax({
                        url: "<?= site_url('admin/penilaian/delete-kolom') ?>",
                        type: "POST",
                        data: {
                            id_kolom: id_kolom,
                            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                        },
                        dataType: 'JSON',
                        success: function(response) {
                            if (response.status === 'success') {
                                Toast.fire({
                                    icon: 'success',
                                    title: 'Kolom berhasil dihapus.'
                                }).then(() => {
                                    location.reload(); // Reload halaman
                                });
                            } else {
                                Swal.fire('Gagal', 'Gagal menghapus kolom.', 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error', 'Terjadi kesalahan AJAX.', 'error');
                        }
                    });
                }
            });
        });

    });
</script>
<?= $this->endSection() ?>


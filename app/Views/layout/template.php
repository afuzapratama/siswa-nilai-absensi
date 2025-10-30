<?php
// Ambil URL dasar dari .env atau set default
$app_baseURL = config('App')->baseURL;
?>
<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Sistem Penilaian Siswa">
    <meta name="author" content="Afuza Pratama (Developer)">

    <title><?= esc($title) ?> | Penilaian Siswa</title>

    <!-- Font Kustom (FontAwesome CDN) -->
    <!-- Custom fonts for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/css/sb-admin-2.min.css" rel="stylesheet">

    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- CSS BARU UNTUK BOOTSTRAP-SELECT -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/css/bootstrap-select.min.css">


    <!-- CSS Kustom (Permintaan Anda) -->
    <style>
        .sidebar-heading {
            padding: 0 1rem;
            margin-top: 1rem;
            font-size: .75rem;
            color: rgba(255, 255, 255, .4);
            text-transform: uppercase;
            letter-spacing: .05rem;
            font-weight: 700;
        }

        .sidebar-divider {
            margin: 0 1rem 1rem;
            border-top: 1px solid rgba(255, 255, 255, .15);
        }
    </style>
    <!-- Render CSS tambahan per halaman -->
    <?= $this->renderSection('css') ?>
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
</head>

<body id="page-top">

    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= site_url('admin/dashboard') ?>">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Nilai Siswa</div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item <?= (current_url() == site_url('admin/dashboard')) ? 'active' : '' ?>">
                <a class="nav-link" href="<?= site_url('admin/dashboard') ?>">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Data Master
            </div>

            <!-- Nav Item - Tahun Ajaran -->
            <li class="nav-item <?= (strpos(current_url(), 'admin/tahun-ajaran') !== false) ? 'active' : '' ?>">
                <a class="nav-link" href="<?= site_url('admin/tahun-ajaran') ?>">
                    <i class="fas fa-fw fa-calendar-alt"></i>
                    <span>Tahun Ajaran</span></a>
            </li>
            <!-- NAV ITEM BARU UNTUK KELAS -->
            <li class="nav-item <?= (strpos(current_url(), 'admin/kelas') !== false) ? 'active' : '' ?>">
                <a class="nav-link" href="<?= site_url('admin/kelas') ?>">
                    <i class="fas fa-fw fa-school"></i>
                    <span>Kelas</span></a>
            </li>

            <!-- NAV ITEM BARU UNTUK SISWA -->
            <li class="nav-item <?= (strpos(current_url(), 'admin/siswa') !== false) ? 'active' : '' ?>">
                <a class="nav-link" href="<?= site_url('admin/siswa') ?>">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Siswa</span></a>
            </li>

            <!-- NAV ITEM BARU UNTUK MAPEL -->
            <li class="nav-item <?= (strpos(current_url(), 'admin/mata-pelajaran') !== false) ? 'active' : '' ?>">
                <a class="nav-link" href="<?= site_url('admin/mata-pelajaran') ?>">
                    <i class="fas fa-fw fa-book"></i>
                    <span>Mata Pelajaran</span></a>
            </li>
            <!-- NAV ITEM BARU UNTUK PENILAIAN -->
            <li class="nav-item <?= (strpos(current_url(), 'admin/penilaian') !== false) ? 'active' : '' ?>">
                <a class="nav-link" href="<?= site_url('admin/penilaian') ?>">
                    <i class="fas fa-fw fa-edit"></i>
                    <span>Input Penilaian</span>
                </a>
            </li>

            <!-- Nav Item - Laporan (BARU) -->
            <li class="nav-item <?= (strpos(current_url(), 'admin/report') !== false) ? 'active' : '' ?>">
                <a class="nav-link" href="<?= site_url('admin/report') ?>">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Laporan Nilai</span></a>
            </li>
             <!-- [BARU] Heading Absensi -->
            <hr class="sidebar-divider">

            <div class="sidebar-heading">
                Sistem Absensi
            </div>

            <!-- [BARU] Nav Item - Absensi -->
            <li class="nav-item <?= (strpos(current_url(), 'admin/absensi') !== false) ? 'active' : '' ?>">
                <a class="nav-link" href="<?= site_url('admin/absensi') ?>">
                    <i class="fas fa-fw fa-user-check"></i>
                    <span>Input Absensi</span></a>
            </li>
            
            <!-- [BARU] Nav Item - Rekap Absensi -->
            <li class="nav-item <?= (strpos(current_url(), 'admin/rekap-absensi') !== false) ? 'active' : '' ?>">
                <a class="nav-link" href="<?= site_url('admin/rekap-absensi') ?>">
                    <i class="fas fa-fw fa-chart-pie"></i>
                    <span>Rekap Absensi</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Pengaturan
            </div>

            <!-- Nav Item - Manajemen User [BARU] -->
            <li class="nav-item <?= (strpos(current_url(), 'admin/users') !== false) ? 'active' : '' ?>">
                <a class="nav-link" href="<?= site_url('admin/users') ?>">
                    <i class="fas fa-fw fa-users-cog"></i>
                    <span>Manajemen User</span></a>
            </li>
            <!-- [BARU] Nav Item - Settings Aplikasi -->
            <li class="nav-item <?= (strpos(current_url(), 'admin/settings') !== false) ? 'active' : '' ?>">
                <a class="nav-link" href="<?= site_url('admin/settings') ?>">
                    <i class="fas fa-fw fa-cog"></i>
                    <span>Settings Aplikasi</span></a>
            </li>
            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <div class="topbar-divider d-none d-sm-block"></div>

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small"><?= session()->get('username') ?? 'User' ?></span>
                                <img class="img-profile rounded-circle" src="https://ui-avatars.com/api/?name=<?= session()->get('username') ?? 'User' ?>&background=4e73df&color=ffffff">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
                                <a class="dropdown-item" href="#">
                                    <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Profile
                                </a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <?= $this->renderSection('content') ?>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Sistem Penilaian Siswa <?= date('Y') ?></span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Yakin ingin Logout?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Pilih "Logout" di bawah jika Anda siap untuk mengakhiri sesi Anda saat ini.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Batal</button>
                    <a class="btn btn-primary" href="<?= site_url('logout') ?>">Logout</a>
                </div>
            </div>
        </div>
    </div>

    <!-- INI BAGIAN YANG HILANG SEBELUMNYA (CDN) -->

    <!-- Bootstrap core JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/js/sb-admin-2.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- JS BARU UNTUK BOOTSTRAP-SELECT -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.13.18/js/bootstrap-select.min.js"></script>
    <!-- Script AJAX CSRF Global -->
    <script>
        // Set base_url dan csrf_token untuk digunakan di script JS lain
        const BASE_URL = '<?= $app_baseURL ?>';
        let csrf_token = '<?= csrf_hash() ?>';

        // Auto-update CSRF token di setiap request AJAX
        $(document).ajaxSend(function(e, xhr, options) {
            // Cek jika request adalah 'POST' dan internal (bukan ke domain lain)
            if (options.type === 'POST' && !options.crossDomain) {
                // Set header X-CSRF-TOKEN
                // CI4 versi baru mungkin lebih suka 'X-CSRF-TOKEN'
                // xhr.setRequestHeader('X-CSRF-TOKEN', csrf_token);

                // Jika data adalah FormData (untuk upload file), kita harus append
                if (options.data instanceof FormData) {
                    // Pastikan token belum ada
                    if (!options.data.has('<?= csrf_token() ?>')) {
                        options.data.append('<?= csrf_token() ?>', csrf_token);
                    }
                } else {
                    // Jika data adalah string (serialize)
                    // Perlu cek apakah token sudah ada
                    if (options.data && options.data.indexOf('<?= csrf_token() ?>') === -1) {
                        options.data += '&<?= csrf_token() ?>=' + csrf_token;
                    } else if (!options.data) {
                        options.data = '<?= csrf_token() ?>=' + csrf_token;
                    }
                }
            }
        });

        // Auto-refresh token setelah AJAX sukses (jika header dikirim oleh CI)
        $(document).ajaxComplete(function(e, xhr) {
            // Cek jika header dikirim (CI4 Auto-refresh CSRF)
            var new_csrf_hash = xhr.getResponseHeader('X-CSRF-TOKEN-Response');
            if (new_csrf_hash) {
                csrf_token = new_csrf_hash;
                // Update juga semua input hidden di form (BUG FIX DI SINI)
                $('input[name="<?= csrf_token() ?>"]').val(new_csrf_hash);
            }
        });
    </script>

    <!-- Render script tambahan per halaman -->
    <?= $this->renderSection('js') ?>

</body>

</html>
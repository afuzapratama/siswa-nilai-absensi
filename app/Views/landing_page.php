<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= esc($title ?? 'Selamat Datang') ?></title>

    <!-- Bootstrap 4 CSS (Sesuai dengan template admin kita) -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    
    <!-- Font Awesome (Untuk Ikon) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, .05);
        }
        .hero {
            background: #ffffff;
            border-radius: 0.5rem;
            padding: 4rem 2rem;
            margin-top: 3rem;
            text-align: center;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, .05);
        }
        .hero h1 {
            font-size: 3rem;
            font-weight: 700;
            color: #333;
        }
        .hero .lead {
            font-size: 1.25rem;
            color: #555;
            margin-bottom: 2rem;
        }
        .hero .btn-login {
            font-size: 1.2rem;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
        }
        .features {
            margin-top: 4rem;
        }
        .feature-item {
            text-align: center;
            padding: 1.5rem;
        }
        .feature-item .icon {
            font-size: 3rem;
            color: #007bff;
        }
        .feature-item h5 {
            font-weight: 600;
            margin-top: 1rem;
        }
        .footer {
            padding: 2rem 0;
            margin-top: 4rem;
            background-color: #343a40;
            color: #c2c7d0;
        }
    </style>
</head>

<body>

    <!-- Navigasi -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= site_url('/') ?>">
                <i class="fas fa-graduation-cap"></i>
                Sistem Penilaian Siswa
            </a>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="btn btn-primary btn-sm" href="<?= site_url('login') ?>">
                        <i class="fas fa-sign-in-alt"></i> Login Admin
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Konten Utama -->
    <div class="container">

        <!-- Hero Section -->
        <div class="hero">
            <h1>Selamat Datang di Sistem Penilaian</h1>
            <p class="lead">Solusi digital terintegrasi untuk manajemen nilai dan absensi siswa secara modern, cepat, dan akurat.</p>
            <a href="<?= site_url('login') ?>" class="btn btn-primary btn-lg btn-login">
                <i class="fas fa-sign-in-alt"></i> Masuk ke Sistem
            </a>
        </div>

        <!-- Fitur -->
        <div class="features">
            <div class="row">
                <div class="col-md-4">
                    <div class="feature-item">
                        <div class="icon"><i class="fas fa-tasks"></i></div>
                        <h5>Manajemen Nilai</h5>
                        <p>Kelola nilai harian, tugas, praktek, dan ujian dengan mudah dalam satu tempat.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-item">
                        <div class="icon"><i class="fas fa-calendar-check"></i></div>
                        <h5>Manajemen Absensi</h5>
                        <p>Catat kehadiran siswa (Hadir, Izin, Sakit, Alpa) per mata pelajaran secara real-time.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-item">
                        <div class="icon"><i class="fas fa-chart-bar"></i></div>
                        <h5>Laporan Cepat</h5>
                        <p>Hasilkan laporan rekap nilai dan absensi secara instan, siap ekspor ke CSV atau PDF.</p>
                    </div>
                </div>
            </div>
        </div>

    </div> <!-- /container -->

    <!-- Footer -->
    <footer class="footer text-center">
        <div class="container">
            <p>&copy; <?= date('Y') ?> Sistem Penilaian Siswa. Dibuat dengan CodeIgniter 4.</p>
        </div>
    </footer>

    <!-- Bootstrap 4 JS (Harus ada JQuery dulu) -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

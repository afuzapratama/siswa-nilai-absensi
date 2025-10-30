<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

// Rute Autentikasi
$routes->get('login', 'Auth\AuthController::index');
$routes->post('login/process', 'Auth\AuthController::processLogin');
$routes->get('logout', 'Auth\AuthController::logout');

// Grup Rute Admin (Diproteksi oleh Filter Auth)
$routes->group('admin', ['filter' => 'auth'], static function ($routes) {
    $routes->get('dashboard', 'Admin\Dashboard::index');

    // Tahun Ajaran
    $routes->group('tahun-ajaran', static function ($routes) {
        $routes->get('/', 'Admin\TahunAjaran::index');
        $routes->post('save', 'Admin\TahunAjaran::save');
        $routes->post('fetch', 'Admin\TahunAjaran::fetch');
        $routes->post('delete', 'Admin\TahunAjaran::delete');
        $routes->post('set-status', 'Admin\TahunAjaran::setStatus');
    });

    // Kelas
    $routes->group('kelas', static function ($routes) {
        $routes->get('/', 'Admin\Kelas::index');
        $routes->post('save', 'Admin\Kelas::save');
        $routes->post('delete', 'Admin\Kelas::delete');
    });

    // Siswa
    $routes->group('siswa', static function ($routes) {
        $routes->get('/', 'Admin\Siswa::index');
        $routes->post('save', 'Admin\Siswa::save');
        $routes->post('fetch', 'Admin\Siswa::fetch');
        $routes->post('delete', 'Admin\Siswa::delete');
        $routes->post('import-csv', 'Admin\Siswa::importCsv');
        // [INI PERBAIKANNYA] Rute untuk AJAX Paginasi
        $routes->get('fetch-by-kelas', 'Admin\Siswa::fetchByKelas');
    });

    // Mata Pelajaran
    $routes->group('mata-pelajaran', static function ($routes) {
        $routes->get('/', 'Admin\MataPelajaran::index');
        $routes->post('save', 'Admin\MataPelajaran::save');
        $routes->post('fetch', 'Admin\MataPelajaran::fetch');
        $routes->post('delete', 'Admin\MataPelajaran::delete');
    });

    // Penilaian
    $routes->group('penilaian', static function ($routes) {
        $routes->get('/', 'Admin\Penilaian::index');
        $routes->get('create', 'Admin\Penilaian::create');
        $routes->post('save-header', 'Admin\Penilaian::saveHeader');
        $routes->get('form/(:num)', 'Admin\Penilaian::form/$1');
        // Rute AJAX
        $routes->post('get-mapel', 'Admin\Penilaian::ajaxGetMapel');
        $routes->post('add-kolom', 'Admin\Penilaian::addKolom');
        $routes->post('delete-kolom', 'Admin\Penilaian::deleteKolom');
        $routes->post('save-nilai', 'Admin\Penilaian::saveNilai');
    });

    // Laporan (Nilai)
    $routes->group('report', static function ($routes) {
        $routes->get('/', 'Admin\Report::index');
        $routes->post('ajax-get-mapel', 'Admin\Report::ajaxGetMapel');
        $routes->post('get-judul', 'Admin\Report::getJudulPenilaian');
        $routes->post('tampilkan-data', 'Admin\Report::tampilkanDataNilai');
        $routes->get('export-csv', 'Admin\Report::exportCsvNilai');
        $routes->get('export-pdf', 'Admin\Report::exportPdfNilai');
    });

    // Manajemen User (Settings)
    $routes->group('users', static function ($routes) {
        $routes->get('/', 'Admin\Users::index');
        $routes->post('save', 'Admin\Users::save');
        $routes->post('fetch', 'Admin\Users::fetch');
        $routes->post('delete', 'Admin\Users::delete');
        $routes->post('change-password', 'Admin\Users::changePassword');
    });

    // Settings Aplikasi (Bobot, dll)
    $routes->group('settings', static function ($routes) {
        $routes->get('/', 'Admin\Settings::index');
        $routes->post('save', 'Admin\Settings::save');
    });

    // Input Absensi
    $routes->group('absensi', static function ($routes) {
        $routes->get('/', 'Admin\AbsensiController::index');
        $routes->post('ajax-get-mapel', 'Admin\AbsensiController::ajaxGetMapel');
        $routes->post('get-siswa', 'Admin\AbsensiController::getSiswaAbsensi');

        // FIX nama route agar sesuai JS
        $routes->post('save-absen', 'Admin\AbsensiController::saveAbsen');

        // Baru: detail & save izin/sakit
        $routes->post('detail', 'Admin\AbsensiController::detail');
        $routes->post('save-izin-sakit', 'Admin\AbsensiController::saveIzinSakit');
    });


    // Rekap Absensi
    $routes->group('rekap-absensi', static function ($routes) {
        $routes->get('/', 'Admin\Report::rekapAbsensi');
        $routes->post('tampilkan-data', 'Admin\Report::tampilkanDataAbsensi');
        $routes->get('export-csv', 'Admin\Report::exportCsvAbsensi');
        $routes->get('export-pdf', 'Admin\Report::exportPdfAbsensi');
    });
});

// Rute API
$routes->group('api', static function ($routes) {

    $routes->get('total-nilai', 'Api\NilaiController::getTotalNilaiSiswa');

});



/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}


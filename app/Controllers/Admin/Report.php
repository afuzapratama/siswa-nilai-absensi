<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\MapelModel;
use App\Models\MapelKelasModel; // [PENTING] Load model pivot
use App\Models\PenilaianHeaderModel;
use App\Models\SettingsModel;
use App\Models\AbsensiModel;
use App\Models\TahunAjaranModel;
use Dompdf\Dompdf;
use Dompdf\Options;

class Report extends BaseController
{
    protected $penilaianHeaderModel;
    protected $kelasModel;
    protected $mapelModel;
    protected $mapelKelasModel; // [PENTING]
    protected $settingsModel;
    protected $absensiModel;
    protected $taModel;

    public function __construct()
    {
        helper('url');
        $this->penilaianHeaderModel = new PenilaianHeaderModel();
        $this->kelasModel           = new KelasModel();
        $this->mapelModel           = new MapelModel();
        $this->mapelKelasModel      = new MapelKelasModel(); // [PENTING]
        $this->settingsModel        = new SettingsModel();
        $this->absensiModel         = new AbsensiModel();
        $this->taModel              = new TahunAjaranModel();
    }

    //--------------------------------------------------------------------
    // HALAMAN LAPORAN NILAI
    //--------------------------------------------------------------------

    public function index()
    {
        $ta_aktif = $this->taModel->getTahunAjaranAktif();
        if (!$ta_aktif) {
            session()->setFlashdata('error', 'Tidak ada Tahun Ajaran yang aktif. Silakan aktifkan satu di menu Tahun Ajaran.');
            return redirect()->to('admin/tahun-ajaran');
        }

        $data = [
            'title'      => 'Laporan Penilaian Siswa',
            'kelas_list' => $this->kelasModel->getKelasByTahunAjaran($ta_aktif['id_tahun_ajaran']),
        ];
        return view('admin/report', $data); // View: report_new.php (Tailwind)
    }

    /**
     * [AJAX - UNTUK LAPORAN NILAI]
     * Ambil Mata Pelajaran berdasarkan Kelas
     */
    public function ajaxGetMapel()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $id_kelas = $this->request->getPost('id_kelas');
        if (!$id_kelas) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID Kelas tidak ada.', 'csrf_hash' => csrf_hash()]);
        }

        // Gunakan model pivot untuk mengambil mapel
        $mapel = $this->mapelKelasModel->getMapelByKelas($id_kelas);

        return $this->response->setJSON(['status' => 'success', 'mapel' => $mapel, 'csrf_hash' => csrf_hash()]);
    }


    /**
     * [AJAX - UNTUK LAPORAN NILAI]
     * Ambil Judul Penilaian berdasarkan Kelas & Mapel
     */
    public function getJudulPenilaian()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $id_kelas = $this->request->getPost('id_kelas');
        $id_mapel = $this->request->getPost('id_mapel');
        $ta_aktif = $this->taModel->getTahunAjaranAktif();

        $data = $this->penilaianHeaderModel->getJudulByKelas($id_kelas, $id_mapel, $ta_aktif['id_tahun_ajaran']);

        return $this->response->setJSON(['status' => 'success', 'data' => $data, 'csrf_hash' => csrf_hash()]);
    }

    /**
     * [AJAX - UNTUK LAPORAN NILAI]
     * Tampilkan data Laporan Nilai
     */
    public function tampilkanDataNilai()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $id_kelas        = $this->request->getPost('id_kelas');
        $id_header_array = $this->request->getPost('id_header'); // Ini adalah array

        if (empty($id_header_array)) {
            return '<div class="alert alert-danger">Silakan pilih minimal satu judul penilaian.</div>';
        }

        $data = $this->penilaianHeaderModel->getReportData($id_kelas, $id_header_array);

        // Add CSRF hash to response header for HTML response
        return $this->response
            ->setHeader('X-CSRF-Hash', csrf_hash())
            ->setBody(view('admin/partials/report_table', $data));
    }

    /**
     * Export CSV Laporan Nilai
     */
    public function exportCsvNilai()
    {
        $id_kelas        = $this->request->getGet('id_kelas');
        $id_header_param = $this->request->getGet('id_header'); // Ini string "1,2,3"

        $id_header_array = explode(',', $id_header_param);

        if (empty($id_header_array) || empty($id_kelas)) {
            session()->setFlashdata('error', 'Filter tidak lengkap untuk export CSV.');
            return redirect()->back();
        }

        $reportData = $this->penilaianHeaderModel->getReportData($id_kelas, $id_header_array);

        $kelasInfo = $this->kelasModel->getKelasWithTahunAjaran($id_kelas);
        $nama_kelas_safe = str_replace(' ', '_', $kelasInfo['nama_kelas'] ?? 'kelas');
        $filename = 'Laporan_Nilai_' . $nama_kelas_safe . '_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Header CSV - Baris 1 (Judul Penilaian)
        $header1 = ['No', 'Nama Siswa', 'NIS'];
        foreach ($reportData['headers'] as $judul) {
            $header1[] = esc($judul['judul_penilaian']);
            $kolomCount = count($reportData['kolom_map'][$judul['id_header']]);
            for ($i = 0; $i < $kolomCount; $i++) {
                $header1[] = '';
            }
        }
        $header1[] = 'Rata-rata Total';
        fputcsv($output, $header1);

        // Header CSV - Baris 2 (N1, N2, Rata-rata)
        $header2 = ['', '', ''];
        foreach ($reportData['headers'] as $judul) {
            foreach ($reportData['kolom_map'][$judul['id_header']] as $kolom) {
                $header2[] = esc($kolom['nama_kolom']);
            }
            $header2[] = 'Rata-rata';
        }
        $header2[] = '';
        fputcsv($output, $header2);

        // Data Siswa
        $no = 1;
        foreach ($reportData['siswa'] as $s) {
            $id_s = $s['id_siswa'];
            $row = [
                $no++,
                $s['nama_siswa'],
                $s['nis'] ?? '-',
            ];

            $total_nilai_siswa = 0;
            $total_pembagi_siswa = 0;

            foreach ($reportData['headers'] as $judul) {
                $id_header = $judul['id_header'];
                $total_nilai_header = 0;
                $total_pembagi_header = 0;

                foreach ($reportData['kolom_map'][$id_header] as $kolom) {
                    $id_kolom = $kolom['id_kolom'];
                    $nilai = $reportData['nilai_map'][$id_s][$id_header][$id_kolom] ?? null;
                    $row[] = $nilai;
                    if (is_numeric($nilai)) {
                        $total_nilai_header += $nilai;
                        $total_pembagi_header++;
                    }
                }
                $rata_rata_header = ($total_pembagi_header > 0) ? ($total_nilai_header / $total_pembagi_header) : 0;
                $row[] = number_format($rata_rata_header, 1);

                if ($rata_rata_header > 0) {
                    $total_nilai_siswa += $rata_rata_header;
                    $total_pembagi_siswa++;
                }
            }
            $rata_rata_total = ($total_pembagi_siswa > 0) ? ($total_nilai_siswa / $total_pembagi_siswa) : 0;
            $row[] = number_format($rata_rata_total, 1);
            fputcsv($output, $row);
        }
        fclose($output);
        exit();
    }

    /**
     * Export PDF Laporan Nilai
     */
   public function exportPdfNilai()
    {
        // 0) Tahun ajaran aktif
        $ta_aktif = $this->taModel->getTahunAjaranAktif();
        if (!$ta_aktif) {
            session()->setFlashdata('error', 'Tidak ada Tahun Ajaran yang aktif. Silakan aktifkan satu di menu Tahun Ajaran.');
            return redirect()->to('admin/tahun-ajaran');
        }

        // 1) Ambil & validasi parameter
        $id_kelas        = $this->request->getGet('id_kelas');
        $id_header_param = (string) $this->request->getGet('id_header'); // contoh: "1,2,3"
        $id_header_array = array_values(array_filter(array_map('intval', explode(',', $id_header_param))));

        if (empty($id_kelas) || empty($id_header_array)) {
            session()->setFlashdata('error', 'Filter tidak lengkap untuk export PDF.');
            return redirect()->back();
        }

        // 2) Ambil data laporan
        $data = $this->penilaianHeaderModel->getReportData($id_kelas, $id_header_array);
        $data['kelasInfo']    = $this->kelasModel->getKelasWithTahunAjaran($id_kelas);
        $data['namaSekolah']  = $this->settingsModel->getSetting('nama_sekolah') ?? 'NAMA SEKOLAH';
        $data['tahun_ajaran'] = $ta_aktif['tahun_ajaran'];

        // 3) Render HTML -> PDF (Dompdf)
        // (opsional) naikkan batasan
        // @ini_set('memory_limit', '512M');
        // @set_time_limit(120);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->setDefaultFont('Helvetica');

        $dompdf = new Dompdf($options);

        $html = view('admin/partials/report_pdf', $data);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $pdf = $dompdf->output();

        // 4) Bersihkan output buffer agar header/length rapi
        if (ob_get_length()) { @ob_end_clean(); }

        // 5) Kirim cookie download-token supaya front-end bisa menutup loader
        $token = $this->request->getGet('dl_token');
        if (!empty($token)) {
            // expire 60 detik, path '/', HttpOnly=false agar bisa dibaca JS
            $this->response->setCookie('dl_token', $token, 60, '', '/', '', false, false, 'Lax');
        }

        // 6) Nama file aman + download
        $nama_kelas_safe = preg_replace('/[^\w\-]+/u', '_', $data['kelasInfo']['nama_kelas'] ?? 'kelas');
        $filename = 'Laporan_Nilai_' . $nama_kelas_safe . '_' . date('Y-m-d') . '.pdf';

        return $this->response->download($filename, $pdf, true);
    }



    //--------------------------------------------------------------------
    // HALAMAN REKAP ABSENSI
    //--------------------------------------------------------------------

    /**
     * Halaman utama Rekap Absensi
     */
    public function rekapAbsensi()
    {
        $ta_aktif = $this->taModel->getTahunAjaranAktif();
        if (!$ta_aktif) {
            session()->setFlashdata('error', 'Tidak ada Tahun Ajaran yang aktif.');
            return redirect()->to('admin/tahun-ajaran');
        }

        $data = [
            'title'      => 'Rekap Kehadiran Siswa',
            'kelas_list' => $this->kelasModel->getKelasByTahunAjaran($ta_aktif['id_tahun_ajaran']),
        ];
        return view('admin/rekap_absensi', $data);
    }

    /**
     * [AJAX - UNTUK REKAP ABSENSI]
     * Tampilkan data Rekap Absensi
     */
    public function tampilkanDataAbsensi()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $tgl_mulai   = $this->request->getPost('tanggal_mulai');
        $tgl_selesai = $this->request->getPost('tanggal_selesai');
        $id_kelas    = $this->request->getPost('id_kelas');
        $id_mapel    = $this->request->getPost('id_mapel');
        $ta_aktif    = $this->taModel->getTahunAjaranAktif();

        $bobot = [
            'H' => (float)($this->settingsModel->getSetting('bobot_hadir') ?? 1.0),
            'I' => (float)($this->settingsModel->getSetting('bobot_izin') ?? 0.7),
            'S' => (float)($this->settingsModel->getSetting('bobot_sakit') ?? 0.9),
            'A' => (float)($this->settingsModel->getSetting('bobot_alpa') ?? 0.0),
        ];

        $data['rekap_data'] = $this->absensiModel->getRekapAbsensi(
            $ta_aktif['id_tahun_ajaran'],
            $id_kelas,
            $id_mapel,
            $tgl_mulai,
            $tgl_selesai,
            $bobot
        );

        return view('admin/partials/_rekap_absensi_tabel', $data);
    }

    /**
     * Export CSV Rekap Absensi
     */
    public function exportCsvAbsensi()
    {
        $tgl_mulai   = $this->request->getGet('tanggal_mulai');
        $tgl_selesai = $this->request->getGet('tanggal_selesai');
        $id_kelas    = $this->request->getGet('id_kelas');
        $id_mapel    = $this->request->getGet('id_mapel');
        $ta_aktif    = $this->taModel->getTahunAjaranAktif();

        if (empty($tgl_mulai) || empty($tgl_selesai) || empty($id_kelas) || empty($id_mapel)) {
            session()->setFlashdata('error', 'Filter tidak lengkap untuk export CSV.');
            return redirect()->back();
        }

        $bobot = [
            'H' => (float)($this->settingsModel->getSetting('bobot_hadir') ?? 1.0),
            'I' => (float)($this->settingsModel->getSetting('bobot_izin') ?? 0.7),
            'S' => (float)($this->settingsModel->getSetting('bobot_sakit') ?? 0.9),
            'A' => (float)($this->settingsModel->getSetting('bobot_alpa') ?? 0.0),
        ];

        $rekap_data = $this->absensiModel->getRekapAbsensi(
            $ta_aktif['id_tahun_ajaran'],
            $id_kelas,
            $id_mapel,
            $tgl_mulai,
            $tgl_selesai,
            $bobot
        );

        $kelasInfo = $this->kelasModel->getKelasWithTahunAjaran($id_kelas);
        $nama_kelas_safe = str_replace(' ', '_', $kelasInfo['nama_kelas'] ?? 'kelas');
        $filename = 'Rekap_Absensi_' . $nama_kelas_safe . '_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['No', 'Nama Siswa', 'NIS', 'Hadir (H)', 'Izin (I)', 'Sakit (S)', 'Alpa (A)', 'Total Poin', 'Persentase (%)']);

        $no = 1;
        foreach ($rekap_data as $siswa) {
            $row = [
                $no++,
                $siswa['nama_siswa'],
                $siswa['nis'] ?? '-',
                $siswa['H'],
                $siswa['I'],
                $siswa['S'],
                $siswa['A'],
                number_format($siswa['total_poin'], 1),
                number_format($siswa['persentase'], 1) . '%'
            ];
            fputcsv($output, $row);
        }
        fclose($output);
        exit();
    }

    /**
     * Export PDF Rekap Absensi
     */
    public function exportPdfAbsensi()
    {
        $tgl_mulai   = $this->request->getGet('tanggal_mulai');
        $tgl_selesai = $this->request->getGet('tanggal_selesai');
        $id_kelas    = $this->request->getGet('id_kelas');
        $id_mapel    = $this->request->getGet('id_mapel');
        $ta_aktif    = $this->taModel->getTahunAjaranAktif();

        if (empty($tgl_mulai) || empty($tgl_selesai) || empty($id_kelas) || empty($id_mapel) || !$ta_aktif) {
            session()->setFlashdata('error', 'Filter tidak lengkap untuk export PDF.');
            return redirect()->back();
        }

        $bobot = [
            'H' => (float)($this->settingsModel->getSetting('bobot_hadir') ?? 1.0),
            'I' => (float)($this->settingsModel->getSetting('bobot_izin') ?? 0.7),
            'S' => (float)($this->settingsModel->getSetting('bobot_sakit') ?? 0.9),
            'A' => (float)($this->settingsModel->getSetting('bobot_alpa') ?? 0.0),
        ];

        $data['rekap_data'] = $this->absensiModel->getRekapAbsensi(
            $ta_aktif['id_tahun_ajaran'],
            $id_kelas,
            $id_mapel,
            $tgl_mulai,
            $tgl_selesai,
            $bobot
        );

        $data['info'] = [
            'nama_sekolah' => $this->settingsModel->getSetting('nama_sekolah') ?? 'NAMA SEKOLAH',
            'kelas_info'   => $this->kelasModel->getKelasWithTahunAjaran($id_kelas),
            'mapel_info'   => $this->mapelModel->find($id_mapel),
            'ta_aktif'     => $ta_aktif,
            'tgl_mulai'    => $tgl_mulai,
            'tgl_selesai'  => $tgl_selesai
        ];

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->setDefaultFont('Helvetica');
        $dompdf = new Dompdf($options);

        $html = view('admin/partials/_rekap_absensi_pdf', $data);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $pdf = $dompdf->output();

        if (ob_get_length()) {
            @ob_end_clean();
        }

        // ——— DOWNLOAD TOKEN: kirim balik cookie agar JS tahu “download dimulai”
        $token = $this->request->getGet('dl_token');
        if (!empty($token)) {
            // expire 60 detik, path '/', HttpOnly=false agar bisa dibaca JS
            $this->response->setCookie('dl_token', $token, 60, '', '/', '', false, false, 'Lax');
        }

       $nama_kelas_safe = preg_replace('/\s+/', '_', $data['info']['kelas_info']['nama_kelas'] ?? 'kelas');
        $filename = 'Rekap_Absensi_' . $nama_kelas_safe . '_' . date('Y-m-d') . '.pdf';

        return $this->response->download($filename, $pdf, true);
    }
}

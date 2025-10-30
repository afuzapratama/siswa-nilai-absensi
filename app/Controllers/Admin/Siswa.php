<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\SiswaModel;
use App\Models\TahunAjaranModel;

class Siswa extends BaseController
{
    protected $kelasModel;
    protected $siswaModel;
    protected $taModel;

    public function __construct()
    {
        $this->kelasModel = new KelasModel();
        $this->siswaModel = new SiswaModel();
        $this->taModel = new TahunAjaranModel();
        helper('text'); // [PENTING] Untuk random_string
    }

    /**
     * [REVISI] Halaman index sekarang HANYA menampilkan filter.
     * Data siswa di-load via AJAX (Default Kosong).
     */
    public function index()
    {
        $ta_aktif = $this->taModel->getTahunAjaranAktif();
        if (!$ta_aktif) {
            session()->setFlashdata('error', 'Tidak ada Tahun Ajaran yang aktif.');
            return redirect()->to('admin/tahun-ajaran');
        }

        $data = [
            'title'        => 'Manajemen Siswa',
            // Untuk dropdown filter
            'kelas_list'   => $this->kelasModel->getKelasByTahunAjaran($ta_aktif['id_tahun_ajaran']),
            // Untuk dropdown di modal tambah/edit
            'kelas_aktif'  => $this->kelasModel->getKelasByTahunAjaran($ta_aktif['id_tahun_ajaran']),
        ];

        // Data siswa tidak di-load di sini lagi
        return view('admin/siswa', $data);
    }

    /**
     * [REVISI] Fungsi AJAX baru untuk mengambil data siswa dengan paginasi dan filter
     */
    public function fetchByKelas()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $id_kelas = $this->request->getGet('id_kelas');
        $keyword  = $this->request->getGet('keyword');

        // Jika belum pilih kelas: kirimkan baris placeholder + pager kosong
        if (empty($id_kelas)) {
            $rows = view('admin/partials/_siswa_rows', [
                'siswa'    => [],
                'pager'    => null,
                'id_kelas' => null,
            ]);
            return $this->response->setJSON(['rows' => $rows, 'pager' => '']);
        }

        // Ambil data + pager (grup 'siswa' => query param page_siswa)
        $siswa = $this->siswaModel->getPaginatedSiswa($id_kelas, $keyword);
        $pager = $this->siswaModel->pager;

        $rows  = view('admin/partials/_siswa_rows', [
            'siswa'    => $siswa,
            'pager'    => $pager,
            'id_kelas' => $id_kelas,
        ]);

        $links = $pager ? $pager->links('siswa', 'bootstrap4_full') : '';

        return $this->response->setJSON([
            'rows'  => $rows,
            'pager' => $links, // pakai template default bawaan CI4
        ]);
    }



    /**
     * [AJAX] Simpan data siswa (Create / Update)
     */
    public function save()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $data = [
            'id_siswa'   => $this->request->getPost('id_siswa'),
            'id_kelas'   => $this->request->getPost('id_kelas'),
            'nis'        => $this->request->getPost('nis'),
            'nama_siswa' => mb_strtoupper($this->request->getPost('nama_siswa'), 'UTF-8'),
        ];

        // Ubah NIS kosong menjadi NULL
        if (empty($data['nis']) || $data['nis'] == '-') {
            $data['nis'] = null;
        }

        if ($this->siswaModel->save($data) === false) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->siswaModel->errors()
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data siswa berhasil disimpan!'
        ]);
    }

    /**
     * [AJAX] Ambil 1 data siswa untuk form edit
     */
    public function fetch()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $id_siswa = $this->request->getPost('id_siswa');
        $data = $this->siswaModel->find($id_siswa);

        if (!$data) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data siswa tidak ditemukan.']);
        }

        return $this->response->setJSON(['status' => 'success', 'data' => $data]);
    }

    /**
     * [AJAX] Hapus data siswa
     */
    public function delete()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $id_siswa = $this->request->getPost('id_siswa');

        if ($this->siswaModel->delete($id_siswa)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Data siswa berhasil dihapus.']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menghapus data siswa.']);
    }

    /**
     * [AJAX] Import data siswa dari CSV
     */
    public function importCsv()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $file = $this->request->getFile('file_csv');

        if (!$file || !$file->isValid() || $file->getMimeType() !== 'text/csv') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'File tidak valid. Harap upload file .csv']);
        }

        // Ambil data kelas aktif untuk pemetaan
        $ta_aktif = $this->taModel->getTahunAjaranAktif();
        $kelas_list = $this->kelasModel->getKelasByTahunAjaran($ta_aktif['id_tahun_ajaran']);
        $kelas_map = [];
        foreach ($kelas_list as $kelas) {
            $kelas_map[strtoupper($kelas['kode_kelas'])] = $kelas['id_kelas'];
        }

        // Buka file CSV
        $fileHandle = $file->openFile('r');
        $fileHandle->setFlags(\SplFileObject::READ_CSV);

        $data_to_insert = [];
        $errors = [];
        $sukses_count = 0;
        $gagal_count = 0;
        $row_number = 0;

        foreach ($fileHandle as $row) {
            $row_number++;
            if ($row_number == 1) continue; // Lewati header

            // [FIX] Tangani baris kosong/invalid (count(false))
            if (empty($row) || !isset($row[0])) {
                continue;
            }
            if (count($row) < 3) {
                $errors[] = "Baris $row_number: Format data tidak lengkap (Nama, NIS, Kode Kelas).";
                $gagal_count++;
                continue;
            }

            $nama_siswa = mb_strtoupper(trim($row[0]), 'UTF-8');
            $nis = trim($row[1]);
            $kode_kelas = strtoupper(trim($row[2]));

            // Validasi 1: Cek nama
            if (empty($nama_siswa)) {
                $errors[] = "Baris $row_number: Nama siswa tidak boleh kosong.";
                $gagal_count++;
                continue;
            }

            // Validasi 2: Cek kode kelas
            if (!isset($kelas_map[$kode_kelas])) {
                $errors[] = "Baris $row_number: Kode Kelas '$kode_kelas' tidak ditemukan di tahun ajaran aktif.";
                $gagal_count++;
                continue;
            }

            // [REVISI] Ubah NIS kosong atau '-' menjadi NULL
            if (empty($nis) || $nis == '-') {
                $nis = null;
            }

            $data_to_insert[] = [
                'nama_siswa' => $nama_siswa,
                'nis' => $nis,
                'id_kelas' => $kelas_map[$kode_kelas],
            ];
            $sukses_count++;
        }

        // [FIX] Tutup file SplFileObject
        $fileHandle = null;

        // Simpan ke database
        if (!empty($data_to_insert)) {
            try {
                $this->siswaModel->insertBatch($data_to_insert);
            } catch (\Exception $e) {
                // Tangani error (misal: duplicate entry)
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Terjadi error saat menyimpan data: ' . $e->getMessage()
                ]);
            }
        }

        $message = "Import Selesai. $sukses_count data berhasil disiapkan, $gagal_count data gagal.";
        if (!empty($errors)) {
            $message .= " Detail Kegagalan: " . implode(" | ", array_slice($errors, 0, 5)); // Tampilkan 5 error pertama
        }

        return $this->response->setJSON([
            'status' => 'success',
            'message' => $message
        ]);
    }
}


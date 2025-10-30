<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\SiswaModel;
use CodeIgniter\API\ResponseTrait;

/**
 * Controller ini KHUSUS untuk menyediakan API JSON
 * datanya bisa diambil oleh aplikasi lain (misal: aplikasi mobile)
 */
class NilaiController extends BaseController
{
    use ResponseTrait; // Menggunakan ResponseTrait CI4 untuk respon JSON

    protected $siswaModel;

    public function __construct()
    {
        $this->siswaModel = new SiswaModel();
    }

    /**
     * Endpoint API Utama
     * Mengembalikan total nilai (bukan rata-rata) dari semua siswa
     * di tahun ajaran yang sedang aktif.
     */
    public function getTotalNilaiSiswa()
    {
        try {
            // Panggil fungsi baru yang akan kita buat di SiswaModel
            $dataSiswa = $this->siswaModel->getApiTotalNilai();

            if (empty($dataSiswa)) {
                return $this->respond([
                    'status'  => 'success',
                    'message' => 'Tidak ada data nilai di tahun ajaran aktif saat ini.',
                    'data'    => []
                ], 200);
            }

            // Struktur data sesuai permintaan Anda
            $result = [];
            foreach ($dataSiswa as $siswa) {
                $result[] = [
                    'nama'        => $siswa['nama_siswa'],
                    'kelas'       => $siswa['nama_kelas'],
                    'total_nilai' => (int) $siswa['total_nilai'] // Pastikan jadi angka
                ];
            }

            return $this->respond([
                'status' => 'success',
                'data'   => $result
            ], 200);
        } catch (\Exception $e) {
            return $this->respond([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}


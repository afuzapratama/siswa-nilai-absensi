<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MapelModel;
use App\Models\MapelKelasModel;
use App\Models\KelasModel;

class MataPelajaran extends BaseController
{
    protected $mapelModel;
    protected $mapelKelasModel;
    protected $kelasModel;
    protected $db;

    public function __construct()
    {
        $this->mapelModel = new MapelModel();
        $this->mapelKelasModel = new MapelKelasModel();
        $this->kelasModel = new KelasModel();
        $this->db = \Config\Database::connect(); // Load database untuk transaction
    }

    public function index()
    {
        // Ambil kelas yang HANYA dari tahun ajaran aktif
        $kelas_aktif = $this->kelasModel
            ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = kelas.id_tahun_ajaran')
            ->where('tahun_ajaran.status', 'aktif')
            ->orderBy('kelas.nama_kelas', 'ASC')
            ->findAll();

        $data = [
            'title' => 'Manajemen Mata Pelajaran',
            'mapel' => $this->mapelModel->getAllMapelWithKelas(),
            'kelas_aktif' => $kelas_aktif, // Untuk dropdown multi-select
        ];
        return view('admin/mata_pelajaran', $data);
    }

    /**
     * Method untuk menyimpan atau memperbarui data
     * Ini lebih kompleks karena menyimpan ke 2 tabel
     */
    public function save()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Forbidden');
        }

        $id_mapel = $this->request->getPost('id_mapel');

        // 1. Data untuk tabel 'mapel'
        $dataMapel = [
            'kode_mapel' => $this->request->getPost('kode_mapel'),
            'nama_mapel' => $this->request->getPost('nama_mapel'),
        ];
        
        // 2. Data untuk tabel 'mapel_kelas' (dari multi-select)
        $id_kelas_array = $this->request->getPost('id_kelas') ?? [];

        // Tambahkan id_mapel jika ini adalah update
        if ($id_mapel) {
            $dataMapel['id_mapel'] = $id_mapel;
        }

        // 3. Validasi data 'mapel' dulu
        if ($this->mapelModel->save($dataMapel) === false) {
            // Gagal validasi
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->mapelModel->errors(),
            ]);
        }

        // 4. Dapatkan ID mapel (jika baru, ambil ID terakhir)
        if (!$id_mapel) {
            $id_mapel = $this->mapelModel->getInsertID();
        }

        // 5. Gunakan Transaction untuk menyimpan relasi kelas
        $this->db->transStart();

        // Hapus dulu semua relasi lama untuk mapel ini
        $this->mapelKelasModel->where('id_mapel', $id_mapel)->delete();

        // Jika ada kelas yang dipilih, masukkan relasi baru
        if (!empty($id_kelas_array)) {
            $dataBatch = [];
            foreach ($id_kelas_array as $id_kelas) {
                $dataBatch[] = [
                    'id_mapel' => $id_mapel,
                    'id_kelas' => $id_kelas
                ];
            }
            // Insert batch agar lebih cepat
            $this->mapelKelasModel->insertBatch($dataBatch);
        }

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
             return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal menyimpan relasi kelas.'
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => 'Data mata pelajaran berhasil disimpan.'
        ]);
    }

    /**
     * Method untuk mengambil 1 data (untuk edit)
     */
    public function fetch()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Forbidden');
        }

        $id_mapel = $this->request->getPost('id');
        
        // 1. Ambil data mapel
        $dataMapel = $this->mapelModel->find($id_mapel);
        if (!$dataMapel) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
        }

        // 2. Ambil data kelas yang terhubung (hanya ID-nya)
        $kelas_terhubung = $this->mapelKelasModel
            ->where('id_mapel', $id_mapel)
            ->findColumn('id_kelas'); // ['1', '3', '5']

        return $this->response->setJSON([
            'status' => 'success', 
            'data_mapel' => $dataMapel,
            'kelas_ids' => $kelas_terhubung ?? [] // Kirim array ID kelas
        ]);
    }

    /**
     * Method untuk menghapus data
     */
    public function delete()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Forbidden');
        }

        $id = $this->request->getPost('id');

        try {
            // Hapus dari tabel 'mapel'
            // Relasi di 'mapel_kelas' akan terhapus otomatis
            // berkat ON DELETE CASCADE di database migration
            $this->mapelModel->delete($id);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Data mapel berhasil dihapus.']);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal menghapus data. Mapel ini mungkin sudah memiliki data nilai.'
            ]);
        }
    }
}

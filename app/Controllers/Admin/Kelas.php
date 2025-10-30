<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KelasModel;
use App\Models\TahunAjaranModel; // [FIX] Tambahkan TahunAjaranModel

class Kelas extends BaseController
{
    protected $kelasModel;
    protected $taModel; // [FIX] Tambahkan taModel

    public function __construct()
    {
        $this->kelasModel = new KelasModel();
        $this->taModel    = new TahunAjaranModel(); // [FIX] Load model
    }

    public function index()
    {
        // [FIX] Ambil TA Aktif DULU
        $ta_aktif = $this->taModel->getTahunAjaranAktif();
        if (!$ta_aktif) {
            session()->setFlashdata('error', 'Tidak ada Tahun Ajaran yang aktif.');
            return redirect()->to('admin/tahun-ajaran');
        }

        $data = [
            'title'        => 'Manajemen Kelas',
            // [FIX] Kirim $ta_aktif['id_tahun_ajaran'] ke model
            'kelas'        => $this->kelasModel->getAllKelasWithTahunAjaran($ta_aktif['id_tahun_ajaran']),
            'tahun_ajaran' => $this->taModel->findAll()
        ];
        return view('admin/kelas', $data);
    }

    public function save()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }
        helper('text');

        // Ambil data dari POST
        $data = [
            'id_tahun_ajaran' => $this->request->getPost('id_tahun_ajaran'),
            'nama_kelas'      => $this->request->getPost('nama_kelas'),
            'kode_kelas'      => $this->request->getPost('kode_kelas'),
        ];

        // FITUR BARU: Auto-generate kode_kelas
        if (empty($data['kode_kelas'])) {
            // Buat slug dari nama_kelas, misal: "XI TKJ I" -> "xi-tkj-i"
            $slug_nama_kelas = url_title($data['nama_kelas'], '-', false);

            // Buat 4 karakter acak
            $random_string = strtoupper(random_string('alpha', 4));
            // Gabungkan: XI-TKJ-I-ABCD
            $data['kode_kelas'] = $slug_nama_kelas . '-' . $random_string;
        }

        // Validasi dan simpan
        if ($this->kelasModel->save($data) === false) {
            // Gagal validasi
            return $this->response->setJSON([
                'status'  => 'error',
                'errors'  => $this->kelasModel->errors()
            ]);
        } else {
            // Sukses
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data kelas berhasil ditambahkan.'
            ]);
        }
    }

    // Fungsi delete (tidak ada perubahan)
    public function delete()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $id = $this->request->getPost('id');
        if ($this->kelasModel->delete($id)) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data kelas berhasil dihapus.'
            ]);
        } else {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal menghapus data.'
            ]);
        }
    }
}


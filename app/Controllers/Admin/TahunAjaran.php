<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TahunAjaranModel;

class TahunAjaran extends BaseController
{
    protected $TahunAjaranModel;

    public function __construct()
    {
        $this->TahunAjaranModel = new TahunAjaranModel();
    }

    public function index()
    {
        $data = [
            'title'       => 'Data Tahun Ajaran',
            'tahun_ajaran' => $this->TahunAjaranModel->findAll()
        ];
        return view('admin/tahun_ajaran', $data);
    }

    /**
     * Menyimpan data (Create / Update)
     */
    public function save()
    {
        // Cek jika ini adalah AJAX request
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Forbidden');
        }

        $id = $this->request->getPost('id_tahun_ajaran');

        // Aturan validasi
        $rules = [
            'tahun_ajaran' => 'required',
            'semester'     => 'required|in_list[Ganjil,Genap]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'tahun_ajaran' => $this->request->getPost('tahun_ajaran'),
            'semester'     => $this->request->getPost('semester'),
        ];

        try {
            if (empty($id)) {
                // Buat data baru
                $this->TahunAjaranModel->insert($data);
                $message = 'Data tahun ajaran berhasil ditambahkan.';
            } else {
                // Update data
                $this->TahunAjaranModel->update($id, $data);
                $message = 'Data tahun ajaran berhasil diperbarui.';
            }

            return $this->response->setJSON(['status' => 'success', 'message' => $message]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Mengambil data untuk diedit
     */
    public function fetch()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Forbidden');
        }

        $id = $this->request->getPost('id');
        $data = $this->TahunAjaranModel->find($id);

        if ($data) {
            return $this->response->setJSON(['status' => 'success', 'data' => $data]);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Data tidak ditemukan.']);
        }
    }

    /**
     * Menghapus data
     */
    public function delete()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Forbidden');
        }

        $id = $this->request->getPost('id');

        try {
            $this->TahunAjaranModel->delete($id);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Data berhasil dihapus.']);
        } catch (\Exception $e) {
            // Tangani jika ada foreign key constraint
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal menghapus data. Data mungkin sedang digunakan di tabel lain.']);
        }
    }

    /**
     * Mengatur status Aktif
     */
    public function setStatus()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403, 'Forbidden');
        }

        $id = $this->request->getPost('id');

        if ($this->TahunAjaranModel->setAktif($id)) {
            return $this->response->setJSON(['status' => 'success', 'message' => 'Status tahun ajaran berhasil diaktifkan.']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Gagal memperbarui status.']);
        }
    }
}

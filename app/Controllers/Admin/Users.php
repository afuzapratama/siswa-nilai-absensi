<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Users extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * Halaman utama Manajemen User
     */
    public function index()
    {
        $data = [
            'title' => 'Manajemen User',
            'users' => $this->userModel->findAll()
        ];
        return view('admin/users', $data); // Tailwind version
    }

    /**
     * [AJAX] Menyimpan data (Tambah baru atau Update)
     */
    public function save()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $id_user = $this->request->getPost('id_user');
        $password = $this->request->getPost('password');

        // Aturan validasi dasar
        $rules = [
            'username' => [
                'rules' => 'required|max_length[100]|is_unique[users.username,id,' . $id_user . ']',
                'errors' => ['is_unique' => 'Username ini sudah digunakan.']
            ],
            'nama_lengkap' => 'required|max_length[255]',
        ];

        // Jika ini adalah user BARU (id kosong), password WAJIB diisi
        if (empty($id_user)) {
            $rules['password'] = 'required|min_length[6]';
            $rules['password_confirm'] = 'matches[password]';
        }

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        // Siapkan data
        $data = [
            'id'           => $id_user,
            'username'     => $this->request->getPost('username'),
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
        ];

        // Hanya hash dan simpan password jika diisi (untuk user baru)
        if (!empty($password)) {
            $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        try {
            $this->userModel->save($data);
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Data user berhasil disimpan.'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal menyimpan data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * [AJAX] Mengambil data user untuk form edit
     */
    public function fetch()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }
        $id_user = $this->request->getPost('id');
        $data = $this->userModel->find($id_user);
        if ($data) {
            return $this->response->setJSON(['status' => 'success', 'data' => $data]);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => 'User tidak ditemukan.']);
    }

    /**
     * [AJAX] Menghapus user
     */
    public function delete()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $id_user = $this->request->getPost('id');

        // Fitur keamanan: Jangan biarkan user menghapus dirinya sendiri
        if ($id_user == session('user_id')) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Anda tidak dapat menghapus akun Anda sendiri.'
            ]);
        }

        try {
            $this->userModel->delete($id_user);
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'User berhasil dihapus.'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal menghapus data.'
            ]);
        }
    }

    /**
     * [AJAX] Mengganti password user
     */
    public function changePassword()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $id_user = $this->request->getPost('id_user_pass');
        
        $rules = [
            'new_password'         => 'required|min_length[6]',
            'new_password_confirm' => 'matches[new_password]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors()
            ]);
        }

        $hash = password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT);
        
        try {
            $this->userModel->update($id_user, ['password_hash' => $hash]);
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Password berhasil diperbarui.'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Gagal memperbarui password.'
            ]);
        }
    }
}

<?php

namespace App\Controllers\Auth;

use App\Controllers\BaseController;
use App\Models\UserModel; // Kita panggil UserModel

class AuthController extends BaseController
{
    public function __construct()
    {
        // Helper untuk form dan URL
        helper(['form', 'url']);
    }

    public function login()
    {
        // Jika sudah login, tendang ke dashboard
        if (session()->get('isLoggedIn')) {
            return redirect()->to('admin/dashboard');
        }

        // Tampilkan halaman login
        return view('auth/login');
    }

    public function processLogin()
    {
        $session = session();
        $userModel = new UserModel();

        $username = $this->request->getVar('username');
        $password = $this->request->getVar('password');

        // Validasi input
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            $session->setFlashdata('error', 'Username dan Password wajib diisi.');
            return redirect()->to('login');
        }

        // Cari user
        $user = $userModel->where('username', $username)->first();

        if ($user) {
            // User ditemukan, verifikasi password
            if (password_verify($password, $user['password_hash'])) {
                // Password benar, buat session
                $sessionData = [
                    'user_id'    => $user['id'],
                    'username'   => $user['username'],
                    'nama_lengkap' => $user['nama_lengkap'],
                    'isLoggedIn' => true,
                ];
                $session->set($sessionData);
                
                return redirect()->to('admin/dashboard');
            } else {
                // Password salah
                $session->setFlashdata('error', 'Password salah.');
                return redirect()->to('login');
            }
        } else {
            // User tidak ditemukan
            $session->setFlashdata('error', 'Username tidak ditemukan.');
            return redirect()->to('login');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('login');
    }
}

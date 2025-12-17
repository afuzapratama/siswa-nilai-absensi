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

        // Tampilkan halaman login (new Tailwind UI)
        return view('auth/login');
    }

    public function processLogin()
{
    $session   = session();
    $userModel = new UserModel();

    $username = trim((string) $this->request->getVar('username'));
    $password = (string) $this->request->getVar('password');

    // Validasi input sederhana
    $rules = [
        'username' => 'required',
        'password' => 'required',
    ];
    if (! $this->validate($rules)) {
        $session->setFlashdata('error', 'Username dan Password wajib diisi.');
        return redirect()->to('login');
    }

    // Cari user
    $user = $userModel->where('username', $username)->first();

    // Jika user ada, cek lockout
    if ($user && !empty($user['lock_until'])) {
        $now = new \CodeIgniter\I18n\Time('now', 'UTC');
        $lockUntil = new \CodeIgniter\I18n\Time($user['lock_until'], 'UTC');

        if ($lockUntil->isAfter($now)) {
            $mins = max(1, (int) ceil(($lockUntil->getTimestamp() - $now->getTimestamp()) / 60));
            $session->setFlashdata('error', "Akun dikunci sementara. Coba lagi dalam ~{$mins} menit.");
            return redirect()->to('login');
        }
    }

    // Verifikasi kredensial
    if ($user && password_verify($password, $user['password_hash'])) {

        // Sukses login: reset counter gagal & lock
        if (($user['failed_attempts'] ?? 0) > 0 || !empty($user['lock_until'])) {
            $userModel->update($user['id'], [
                'failed_attempts' => 0,
                'lock_until'      => null,
            ]);
        }

        // (Opsional) Rehash jika algoritma berubah
        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            $userModel->update($user['id'], [
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            ]);
        }

        // Set session login
        $session->set([
            'user_id'      => $user['id'],
            'username'     => $user['username'],
            'nama_lengkap' => $user['nama_lengkap'],
            'isLoggedIn'   => true,
        ]);

        return redirect()->to('admin/dashboard');
    }

    // Gagal (user tak ada atau password salah):
    // Tambahkan delay (300–800ms) untuk memperlambat brute-force
    usleep(random_int(300_000, 800_000));

    if ($user) {
        $attempts = (int) ($user['failed_attempts'] ?? 0) + 1;
        $lock     = null;

        // Lock setelah 5 kali gagal: 15 menit
        if ($attempts >= 5) {
            $lock = (new \CodeIgniter\I18n\Time('now', 'UTC'))->addMinutes(15)->toDateTimeString();
            $attempts = 0; // reset counter setelah dikunci
        }

        $userModel->update($user['id'], [
            'failed_attempts' => $attempts,
            'lock_until'      => $lock,
        ]);
    }

    // Pesan generik (jangan bocorkan mana yg salah)
    $session->setFlashdata('error', 'Username atau password salah.');
    return redirect()->to('login');
}

    public function logout()
    {
        session()->destroy();
        return redirect()->to('login');
    }
}

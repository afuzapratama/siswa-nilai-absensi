<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingsModel;

class Settings extends BaseController
{
    protected $settingsModel;

    public function __construct()
    {
        $this->settingsModel = new SettingsModel();
    }

    /**
     * Halaman utama Settings
     */
    public function index()
    {
        $data = [
            'title'    => 'Settings Aplikasi',
            'settings' => $this->settingsModel->getSettings() // Menggunakan fungsi 'getSettings' (plural)
        ];
        return view('admin/settings_page', $data); // Tailwind version
    }

    /**
     * [FIX] Fungsi Save sekarang mengembalikan JSON untuk AJAX
     */
    public function save()
    {
        if (!$this->request->is('post')) {
            return $this->response->setStatusCode(405, 'Method Not Allowed');
        }

        $data = $this->request->getPost();

        // Hapus token dari data yang akan disimpan
        unset($data[csrf_token()]);

        try {
            if ($this->settingsModel->saveSettings($data)) {
                // Sukses
                return $this->response->setJSON([
                    'status'  => 'success',
                    'message' => 'Pengaturan berhasil disimpan.'
                ]);
            } else {
                // Gagal (dari model)
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Gagal menyimpan pengaturan.'
                ]);
            }
        } catch (\Exception $e) {
            // Error database atau lainnya
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
            ]);
        }
    }
}


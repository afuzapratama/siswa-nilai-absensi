<?php

namespace App\Controllers;

class Home extends BaseController
{
    /**
     * Halaman Landing Page Publik
     */
    public function index()
    {
        // Ambil info nama sekolah dari settings jika ada
        $settingsModel = new \App\Models\SettingsModel();
        $nama_sekolah = $settingsModel->getSetting('nama_sekolah') ?? 'Sistem Penilaian Siswa';

        $data = [
            'title' => 'Selamat Datang - ' . esc($nama_sekolah)
        ];

        return view('landing_page', $data);
    }
}

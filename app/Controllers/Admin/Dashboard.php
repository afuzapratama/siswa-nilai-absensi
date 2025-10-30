<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TahunAjaranModel;
use App\Models\SiswaModel;
use App\Models\KelasModel;
use App\Models\MapelModel;
use App\Models\AbsensiModel;
use App\Models\PenilaianDetailModel;

class Dashboard extends BaseController
{
    protected $taModel;
    protected $siswaModel;
    protected $kelasModel;
    protected $mapelModel;
    protected $absensiModel;
    protected $penilaianDetailModel;

    public function __construct()
    {
        $this->taModel              = new TahunAjaranModel();
        $this->siswaModel           = new SiswaModel();
        $this->kelasModel           = new KelasModel();
        $this->mapelModel           = new MapelModel();
        $this->absensiModel         = new AbsensiModel();
        $this->penilaianDetailModel = new PenilaianDetailModel();
    }

    public function index()
    {
        $ta_aktif = $this->taModel->getTahunAjaranAktif();
        $id_ta_aktif = $ta_aktif ? $ta_aktif['id_tahun_ajaran'] : null;

        // 1. Data untuk Kartu Statistik
        $stats = [
            'total_siswa' => $this->siswaModel->countSiswa($id_ta_aktif),
            'total_kelas' => $this->kelasModel->countKelas($id_ta_aktif),
            'total_mapel' => $this->mapelModel->countMapel($id_ta_aktif),
            'ta_aktif'    => $ta_aktif ? $ta_aktif['tahun_ajaran'] . ' (' . $ta_aktif['semester'] . ')' : 'Belum Diatur',
        ];

        // 2. Data untuk Top 5 Nilai (berdasarkan $id_ta_aktif)
        $top_nilai = $this->penilaianDetailModel->getTopNilai($id_ta_aktif, 5);

        // 3. Data untuk Top 5 Absensi (berdasarkan $id_ta_aktif)
        $top_absensi = $this->absensiModel->getTopAbsensi($id_ta_aktif, 5);

        $data = [
            'title'       => 'Dashboard',
            'stats'       => $stats,
            'top_nilai'   => $top_nilai,
            'top_absensi' => $top_absensi,
        ];

        return view('admin/dashboard', $data);
    }
}


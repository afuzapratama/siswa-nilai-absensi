<?php

namespace App\Models;

use CodeIgniter\Model;

class MapelModel extends Model
{
    protected $table            = 'mapel';
    protected $primaryKey       = 'id_mapel';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['kode_mapel', 'nama_mapel'];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validasi
    protected $validationRules = [
        'id_mapel'   => 'permit_empty|integer', // TAMBAHKAN BARIS INI
        'kode_mapel' => 'required|max_length[20]|is_unique[mapel.kode_mapel,id_mapel,{id_mapel}]',
        'nama_mapel' => 'required|max_length[100]',
    ];
    protected $validationMessages = [
        'kode_mapel' => [
            'required'  => 'Kode Mata Pelajaran wajib diisi.',
            'is_unique' => 'Kode Mata Pelajaran ini sudah terdaftar.',
        ],
        'nama_mapel' => [
            'required' => 'Nama Mata Pelajaran wajib diisi.',
        ],
    ];

    /**
     * Mengambil semua mapel dan daftar kelas yang terhubung
     */
    public function getAllMapelWithKelas()
    {
        $mapel = $this->orderBy('nama_mapel', 'ASC')->findAll();

        // Ambil data kelas untuk setiap mapel
        $mapelKelasModel = new MapelKelasModel();
        
        foreach ($mapel as $key => $m) {
            $kelas = $mapelKelasModel
                ->select('kelas.nama_kelas, kelas.kode_kelas')
                ->join('kelas', 'kelas.id_kelas = mapel_kelas.id_kelas')
                ->where('mapel_kelas.id_mapel', $m['id_mapel'])
                ->findAll();
            
            $mapel[$key]['kelas_terhubung'] = $kelas;
        }

        return $mapel;
    }

     /**
     * [DASHBOARD] Menghitung total mapel yang terhubung ke TA Aktif
     */
    public function countMapel($id_ta_aktif)
    {
        if (!$id_ta_aktif) {
            return 0;
        }

        // Hitung mapel unik yang terhubung ke kelas di TA aktif
        return $this->distinct()
            ->select('mapel.id_mapel')
            ->join('mapel_kelas', 'mapel_kelas.id_mapel = mapel.id_mapel')
            ->join('kelas', 'kelas.id_kelas = mapel_kelas.id_kelas')
            ->where('kelas.id_tahun_ajaran', $id_ta_aktif)
            ->countAllResults();
    }
}

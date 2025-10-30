<?php

namespace App\Models;

use CodeIgniter\Model;

class SiswaModel extends Model
{
    protected $table            = 'siswa';
    protected $primaryKey       = 'id_siswa';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['id_kelas', 'nis', 'nama_siswa'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules    = [
        // [FIX] Tambahkan aturan untuk id_siswa (untuk placeholder)
        'id_siswa'  => 'permit_empty|integer',
        'id_kelas'  => 'required|integer',

        // [FIX] Ubah nis menjadi permit_empty, tapi jika diisi, harus unik
        'nis'       => 'permit_empty|max_length[50]|is_unique[siswa.nis,id_siswa,{id_siswa}]',
        'nama_siswa' => 'required|max_length[255]',
    ];
    protected $validationMessages   = [
        'id_kelas' => [
            'required' => 'Kelas wajib dipilih.',
        ],
        'nis' => [
            'is_unique' => 'NIS ini sudah terdaftar. Gunakan NIS lain.',
        ],
        'nama_siswa' => [
            'required' => 'Nama siswa wajib diisi.',
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
   
    /**
     * [REVISI] Mengambil data siswa dengan paginasi, join, dan search
     * Ini menggantikan getAllSiswaDetail()
     */
    public function getPaginatedSiswa($id_kelas, $keyword = null)
    {
        $this->select('siswa.*, kelas.nama_kelas, kelas.kode_kelas, tahun_ajaran.tahun_ajaran, tahun_ajaran.semester')
            ->join('kelas', 'kelas.id_kelas = siswa.id_kelas')
            ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = kelas.id_tahun_ajaran')
            ->where('siswa.id_kelas', $id_kelas);

        if (!empty($keyword)) {
            $this->groupStart()
                ->like('siswa.nama_siswa', $keyword)
                ->orLike('siswa.nis', $keyword)
                ->groupEnd();
        }

        return $this->orderBy('siswa.nama_siswa', 'ASC')
                    ->paginate(10, 'siswa'); // gunakan grup 'siswa'
    }


    /**
     * Mengambil semua siswa dengan detail kelas dan TA
     * Digunakan di halaman Siswa (dengan filter)
     */
    public function getAllSiswaDetail($id_kelas, $keyword = null)
    {
        $builder = $this->db->table('siswa');
        $builder->select('siswa.*, kelas.nama_kelas, kelas.kode_kelas, tahun_ajaran.tahun_ajaran, tahun_ajaran.semester');
        $builder->join('kelas', 'kelas.id_kelas = siswa.id_kelas');
        $builder->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = kelas.id_tahun_ajaran');
        
        $builder->where('siswa.id_kelas', $id_kelas);

        if ($keyword) {
            $builder->groupStart();
            $builder->like('siswa.nama_siswa', $keyword);
            $builder->orLike('siswa.nis', $keyword);
            $builder->groupEnd();
        }

        $builder->orderBy('siswa.nama_siswa', 'ASC');
        return $builder->get()->getResultArray();
    }

    /**
     * Mengambil siswa di satu kelas (untuk Halaman Input Nilai & Absen)
     */
    public function getSiswaByKelas($id_kelas)
    {
        return $this->where('id_kelas', $id_kelas)
            ->orderBy('nama_siswa', 'ASC')
            ->findAll();
    }

    /**
     * [API] Mengambil total nilai siswa
     */
    public function getApiTotalNilai()
    {
        // 1. Cari TA Aktif
        $ta_aktif_id = $this->db->table('tahun_ajaran')->select('id_tahun_ajaran')->where('status', 'aktif')->get()->getRow('id_tahun_ajaran');
        if (!$ta_aktif_id) {
            return []; // Tidak ada TA aktif
        }

        // 2. Query utama
        $builder = $this->db->table('siswa');
        $builder->select([
            'siswa.nama_siswa',
            'kelas.nama_kelas',
            'SUM(penilaian_detail.nilai) as total_nilai' // Total akumulasi nilai
        ]);
        $builder->join('kelas', 'kelas.id_kelas = siswa.id_kelas');
        $builder->join('penilaian_detail', 'penilaian_detail.id_siswa = siswa.id_siswa', 'left');
        $builder->join('penilaian_header', 'penilaian_header.id_header = penilaian_detail.id_header', 'left');

        // Filter hanya untuk TA aktif
        $builder->where('kelas.id_tahun_ajaran', $ta_aktif_id);
        $builder->where('penilaian_header.id_tahun_ajaran', $ta_aktif_id);

        $builder->groupBy('siswa.id_siswa, siswa.nama_siswa, kelas.nama_kelas');
        $builder->orderBy('kelas.nama_kelas', 'ASC');
        $builder->orderBy('siswa.nama_siswa', 'ASC');

        return $builder->get()->getResultArray();
    }


    /**
     * [DASHBOARD] Menghitung total siswa di TA Aktif
     */
    public function countSiswa($id_ta_aktif)
    {
        if (!$id_ta_aktif) {
            return 0;
        }

        // Hitung siswa yang tergabung di kelas yang TA-nya aktif
        return $this->join('kelas', 'kelas.id_kelas = siswa.id_kelas')
            ->where('kelas.id_tahun_ajaran', $id_ta_aktif)
            ->countAllResults();
    }
}


<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;

class KelasModel extends Model
{
    protected $table            = 'kelas';
    protected $primaryKey       = 'id_kelas';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['id_tahun_ajaran', 'kode_kelas', 'nama_kelas'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'id_tahun_ajaran' => 'required|integer',
        'nama_kelas'      => 'required|max_length[100]',
        'kode_kelas'      => 'required|max_length[50]|is_unique[kelas.kode_kelas,id_kelas,{id_kelas}]',
    ];
    protected $validationMessages   = [
        'kode_kelas' => [
            'is_unique' => 'Kode Kelas sudah ada. Harap gunakan kode lain.'
        ]
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Ambil semua kelas dengan info tahun ajaran
     */
    public function getAllKelasWithTahunAjaran($id_ta_aktif)
    {
        return $this->db->table('kelas')
            ->select('kelas.*, tahun_ajaran.tahun_ajaran, tahun_ajaran.semester')
            ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = kelas.id_tahun_ajaran')
            ->where('kelas.id_tahun_ajaran', $id_ta_aktif)
            ->orderBy('kelas.nama_kelas', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * [FUNGSI PENTING CONTROLLER]
     * Ambil semua kelas di tahun ajaran aktif
     */
    public function getKelasByTahunAjaran($id_ta_aktif)
    {
        return $this->where('id_tahun_ajaran', $id_ta_aktif)
            ->orderBy('nama_kelas', 'ASC')
            ->findAll();
    }

    /**
     * [FUNGSI PENTING CONTROLLER]
     * Ambil info 1 kelas dengan join tahun ajaran
     */
    public function getKelasWithTahunAjaran($id_kelas)
    {
        return $this->db->table('kelas')
            ->select('kelas.*, tahun_ajaran.tahun_ajaran, tahun_ajaran.semester')
            ->join('tahun_ajaran', 'tahun_ajaran.id_tahun_ajaran = kelas.id_tahun_ajaran')
            ->where('kelas.id_kelas', $id_kelas)
            ->get()
            ->getRowArray();
    }

     /**
     * [DASHBOARD] Menghitung total kelas di TA Aktif
     */
    public function countKelas($id_ta_aktif)
    {
        if (!$id_ta_aktif) {
            return 0;
        }
        return $this->where('id_tahun_ajaran', $id_ta_aktif)->countAllResults();
    }
}


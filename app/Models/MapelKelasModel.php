<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model ini HANYA untuk tabel pivot mapel_kelas
 */
class MapelKelasModel extends Model
{
    protected $table            = 'mapel_kelas';
    protected $primaryKey       = 'id_mapel_kelas';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_mapel', 'id_kelas'];

    // Tidak perlu timestamps untuk tabel pivot ini
    protected $useTimestamps = false;

    /**
     * [FUNGSI PENTING AJAX]
     * Ambil semua mapel yang terhubung ke satu kelas
     */
    public function getMapelByKelas($id_kelas)
    {
        return $this->db->table('mapel_kelas')
            ->join('mapel', 'mapel.id_mapel = mapel_kelas.id_mapel')
            ->where('mapel_kelas.id_kelas', $id_kelas)
            ->get()
            ->getResultArray();
    }
}


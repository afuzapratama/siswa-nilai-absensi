<?php

namespace App\Models;

use CodeIgniter\Model;
use CodeIgniter\Database\Exceptions\DatabaseException;

class TahunAjaranModel extends Model
{
    protected $table            = 'tahun_ajaran';
    protected $primaryKey       = 'id_tahun_ajaran';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = ['tahun_ajaran', 'semester', 'status'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'tahun_ajaran' => 'required|max_length[10]',
        'semester'     => 'required|in_list[Ganjil,Genap]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    /**
     * Set 1 tahun ajaran sebagai 'aktif' dan nonaktifkan yang lain.
     * Menggunakan Transaksi DB agar aman.
     */
    public function setAktif($id)
    {
        $this->db->transStart();

        // 1. Nonaktifkan semua
        $this->db->table($this->table)->update(['status' => 'nonaktif']);

        // 2. Aktifkan 1
        $this->db->table($this->table)->where('id_tahun_ajaran', $id)->update(['status' => 'aktif']);

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return false;
        } else {
            $this->db->transCommit();
            return true;
        }
    }

    /**
     * [FUNGSI PENTING CONTROLLER]
     * Ambil data tahun ajaran yang sedang aktif
     */
    public function getTahunAjaranAktif()
    {
        return $this->where('status', 'aktif')->first();
    }
}


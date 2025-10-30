<?php

namespace App\Models;

use CodeIgniter\Model;

class PenilaianKolomModel extends Model
{
    protected $table            = 'penilaian_kolom';
    protected $primaryKey       = 'id_kolom';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    // TAMBAHKAN 'urutan' DI SINI
    protected $allowedFields    = ['id_header', 'nama_kolom', 'urutan'];

    protected $useTimestamps = false;
}

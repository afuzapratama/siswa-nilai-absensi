<?php

namespace App\Models;

use CodeIgniter\Model;

class PenilaianDetailModel extends Model
{
    protected $table            = 'penilaian_detail';
    protected $primaryKey       = 'id_detail';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_header', 'id_kolom', 'id_siswa', 'nilai'];

    // Tidak perlu timestamps
    protected $useTimestamps = false;

    // Tambahkan unique key agar tidak ada data ganda
    // protected $uniqueKeys = ['id_header', 'id_kolom', 'id_siswa'];

    /**
     * Fungsi Auto-Save (Upsert: Update or Insert)
     */
    public function upsert($data)
    {
        $builder = $this->db->table($this->table);

        // Cek dulu apakah data sudah ada
        $builder->where('id_header', $data['id_header']);
        $builder->where('id_kolom', $data['id_kolom']);
        $builder->where('id_siswa', $data['id_siswa']);
        $query = $builder->get();

        if ($query->getNumRows() > 0) {
            // Jika ada, UPDATE
            $builder->where('id_header', $data['id_header']);
            $builder->where('id_kolom', $data['id_kolom']);
            $builder->where('id_siswa', $data['id_siswa']);
            return $builder->update(['nilai' => $data['nilai']]);
        } else {
            // Jika tidak ada, INSERT
            return $builder->insert($data);
        }
    }


    /**
     * [DASHBOARD] Mengambil 5 siswa dengan nilai rata-rata terbaik
     * di tahun ajaran aktif.
     */
     /**
     * [DASHBOARD] Mengambil 5 siswa dengan nilai rata-rata terbaik
     * di tahun ajaran aktif.
     */
    public function getTopNilai($id_ta_aktif, $limit = 5)
    {
        if (!$id_ta_aktif) {
            return [];
        }

        /**
         * Ini adalah query yang sangat kompleks. Kita perlu:
         * 1. (SUB-QUERY 1) Hitung rata-rata nilai per (siswa, id_header) -> (AVG(nilai))
         * 2. (SUB-QUERY 2) Hitung rata-rata dari rata-rata di #1 per siswa -> (AVG(rata_rata_header))
         * 3. JOIN dengan siswa dan kelas untuk dapat nama.
         * 4. Filter berdasarkan tahun ajaran aktif.
         */

        // 1. Sub-query (rata_rata_per_header)
        // Menghitung rata-rata nilai untuk setiap "Judul Penilaian" (header) per siswa
        $subQueryAvgPerHeader = $this->db->table('penilaian_detail')
            ->select('id_siswa, id_header, AVG(nilai) as rata_rata_header')
            ->groupBy(['id_siswa', 'id_header']);

        // 2. Main Query (rata_rata_total)
        // Menghitung rata-rata dari SEMUA rata-rata header di sub-query #1

        // [FIX] Mengganti $this->db->table(...) dengan $this->db->newQuery()->fromSubquery()
        // Ini adalah cara yang lebih aman untuk membuat subquery di CodeIgniter 4
        $builder = $this->db->newQuery()->fromSubquery($subQueryAvgPerHeader, 'RataRataHeader');
        
        $builder->select('
                RataRataHeader.id_siswa, 
                AVG(RataRataHeader.rata_rata_header) as rata_rata_semua_nilai,
                siswa.nama_siswa, 
                kelas.nama_kelas
            ');
        $builder->join('siswa', 'siswa.id_siswa = RataRataHeader.id_siswa');
        $builder->join('penilaian_header', 'penilaian_header.id_header = RataRataHeader.id_header');
        $builder->join('kelas', 'kelas.id_kelas = siswa.id_kelas');
        
        // Filter berdasarkan Tahun Ajaran Aktif
        $builder->where('penilaian_header.id_tahun_ajaran', $id_ta_aktif);
        
        $builder->groupBy('RataRataHeader.id_siswa, siswa.nama_siswa, kelas.nama_kelas');
        $builder->orderBy('rata_rata_semua_nilai', 'DESC');
        $builder->limit($limit);

        return $builder->get()->getResultArray();
    }
}

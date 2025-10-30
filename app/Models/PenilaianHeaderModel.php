<?php

namespace App\Models;

use CodeIgniter\Model;

class PenilaianHeaderModel extends Model
{
    protected $table            = 'penilaian_header';
    protected $primaryKey       = 'id_header';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['id_tahun_ajaran', 'id_kelas', 'id_mapel', 'judul_penilaian'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [
        'id_tahun_ajaran' => 'required|integer',
        'id_kelas'        => 'required|integer',
        'id_mapel'        => 'required|integer',
        'judul_penilaian' => 'required|max_length[255]',
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;

    /**
     * [FIX - FUNGSI HILANG]
     * Ambil semua form penilaian dengan info join
     */
    public function getAllForm($id_ta_aktif)
    {
        return $this->db->table('penilaian_header as ph')
            ->select('ph.id_header, ph.judul_penilaian, k.nama_kelas, m.nama_mapel')
            ->join('kelas as k', 'k.id_kelas = ph.id_kelas')
            ->join('mapel as m', 'm.id_mapel = ph.id_mapel')
            ->where('ph.id_tahun_ajaran', $id_ta_aktif)
            ->orderBy('ph.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * Ambil detail 1 form penilaian
     */
    public function getDetailForm($id_header)
    {
        return $this->db->table('penilaian_header as ph')
            ->select('ph.*, k.nama_kelas, m.nama_mapel, ta.tahun_ajaran, ta.semester')
            ->join('kelas as k', 'k.id_kelas = ph.id_kelas')
            ->join('mapel as m', 'm.id_mapel = ph.id_mapel')
            ->join('tahun_ajaran as ta', 'ta.id_tahun_ajaran = ph.id_tahun_ajaran')
            ->where('ph.id_header', $id_header)
            ->get()
            ->getRowArray();
    }

    /**
     * Ambil judul penilaian (untuk Laporan Nilai)
     */
    public function getJudulByKelas($id_kelas, $id_mapel, $id_ta)
    {
        return $this->where('id_kelas', $id_kelas)
            ->where('id_mapel', $id_mapel)
            ->where('id_tahun_ajaran', $id_ta)
            ->findAll();
    }

    /**
     * Query utama untuk Laporan Nilai
     */
    public function getReportData($id_kelas, $id_header_array)
    {
        $siswaModel = new SiswaModel();
        $siswa = $siswaModel->getSiswaByKelas($id_kelas);

        $headers = $this->db->table('penilaian_header as ph')
            ->select('ph.id_header, ph.judul_penilaian, m.nama_mapel')
            ->join('mapel as m', 'm.id_mapel = ph.id_mapel')
            ->whereIn('ph.id_header', $id_header_array)
            ->orderBy('ph.judul_penilaian', 'ASC')
            ->get()
            ->getResultArray();

        $kolom_map = [];
        $kolom_all = $this->db->table('penilaian_kolom')
            ->whereIn('id_header', $id_header_array)
            ->orderBy('urutan', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($kolom_all as $k) {
            $kolom_map[$k['id_header']][] = $k;
        }

        $nilai_all = $this->db->table('penilaian_detail')
            ->whereIn('id_header', $id_header_array)
            ->get()
            ->getResultArray();

        $nilai_map = [];
        foreach ($nilai_all as $n) {
            $nilai_map[$n['id_siswa']][$n['id_header']][$n['id_kolom']] = $n['nilai'];
        }

        return [
            'siswa'     => $siswa,
            'headers'   => $headers,
            'kolom_map' => $kolom_map,
            'nilai_map' => $nilai_map,
        ];
    }
}


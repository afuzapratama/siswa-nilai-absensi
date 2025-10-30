<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiModel extends Model
{
    protected $table            = 'absensi';
    protected $primaryKey       = 'id_absensi';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id_tahun_ajaran',
        'id_kelas',
        'id_mapel',
        'id_siswa',
        'tanggal',
        'status',
        'catatan',
        'bukti'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil data absensi yang sudah ada berdasarkan kriteria
     */
    public function getAbsenByKriteria($id_ta, $id_kelas, $id_mapel, $tanggal)
    {
        return $this->where('id_tahun_ajaran', $id_ta)
            ->where('id_kelas', $id_kelas)
            ->where('id_mapel', $id_mapel)
            ->where('tanggal', $tanggal)
            ->findAll();
    }

    /**
     * [FUNGSI PENTING REKAP]
     * Query kompleks untuk menghitung rekap absensi
     */
    public function getRekapAbsensi($id_ta, $id_kelas, $id_mapel, $tgl_mulai, $tgl_selesai, $bobot)
    {
        // 1. Ambil semua siswa di kelas tersebut
        $siswaModel = new SiswaModel();
        $siswa_list = $siswaModel->getSiswaByKelas($id_kelas);

        if (empty($siswa_list)) {
            return [];
        }

        // 2. Ambil semua data absensi dalam rentang tanggal
        $absensi_data = $this->db->table('absensi')
            ->where('id_tahun_ajaran', $id_ta)
            ->where('id_kelas', $id_kelas)
            ->where('id_mapel', $id_mapel)
            ->where('tanggal >=', $tgl_mulai)
            ->where('tanggal <=', $tgl_selesai)
            ->get()
            ->getResultArray();

        // 3. Ambil jumlah hari (pertemuan) unik
        $pertemuan_unik = $this->db->table('absensi')
            ->select('tanggal')
            ->where('id_tahun_ajaran', $id_ta)
            ->where('id_kelas', $id_kelas)
            ->where('id_mapel', $id_mapel)
            ->where('tanggal >=', $tgl_mulai)
            ->where('tanggal <=', $tgl_selesai)
            ->groupBy('tanggal')
            ->get()
            ->getNumRows();

        $total_pertemuan = $pertemuan_unik > 0 ? $pertemuan_unik : 1; // Hindari pembagian dengan nol

        // 4. Proses data di PHP
        $rekap = [];
        foreach ($siswa_list as $siswa) {
            $id_s = $siswa['id_siswa'];

            // Inisialisasi
            $rekap[$id_s] = [
                'id_siswa'   => $id_s,
                'nama_siswa' => $siswa['nama_siswa'],
                'nis'        => $siswa['nis'],
                'H'          => 0,
                'I'          => 0,
                'S'          => 0,
                'A'          => 0,
                'total_poin' => 0,
                'persentase' => 0,
            ];
        }

        // 5. Hitung absensi
        foreach ($absensi_data as $absen) {
            $id_s = $absen['id_siswa'];
            $status = $absen['status'];

            if (isset($rekap[$id_s]) && isset($bobot[$status])) {
                $rekap[$id_s][$status]++;
                $rekap[$id_s]['total_poin'] += $bobot[$status];
            }
        }

        // 6. Hitung persentase
        foreach ($rekap as $id_s => $data) {
            // Persentase = (Total Poin didapat) / (Total Poin Maksimal)
            // Total Poin Maksimal = Total Pertemuan * Bobot Hadir
            $total_poin_maksimal = $total_pertemuan * $bobot['H'];
            
            if ($total_poin_maksimal > 0) {
                 $rekap[$id_s]['persentase'] = ($data['total_poin'] / $total_poin_maksimal) * 100;
            } else {
                 $rekap[$id_s]['persentase'] = 0; // Jika tidak ada pertemuan
            }
           
        }

        return array_values($rekap); // Kembalikan sebagai array biasa
    }

     /**
     * [DASHBOARD] Mengambil 5 siswa dengan kehadiran terbaik (Alpa Paling Sedikit)
     * di tahun ajaran aktif.
     */
    public function getTopAbsensi($id_ta_aktif, $limit = 5)
    {
        if (!$id_ta_aktif) {
            return [];
        }

        // 1. Dapatkan semua siswa di TA Aktif
        $siswa_list = $this->db->table('siswa')
            ->select('siswa.id_siswa, siswa.nama_siswa, kelas.nama_kelas')
            ->join('kelas', 'kelas.id_kelas = siswa.id_kelas')
            ->where('kelas.id_tahun_ajaran', $id_ta_aktif)
            ->get()->getResultArray();

        if (empty($siswa_list)) {
            return [];
        }

        // 2. Hitung total pertemuan UNTUK SETIAP MAPEL di TA Aktif
        // Ini adalah query yang kompleks
        // Kita hitung (id_mapel, id_kelas, COUNT(DISTINCT tanggal))
        $total_pertemuan_raw = $this->db->table('absensi')
            ->select('id_mapel, id_kelas, COUNT(DISTINCT tanggal) as total_pertemuan')
            ->where('id_tahun_ajaran', $id_ta_aktif)
            ->groupBy(['id_mapel', 'id_kelas'])
            ->get()->getResultArray();

        // Buat map agar mudah dicari: $map[id_kelas][id_mapel] = total
        $total_pertemuan_map = [];
        foreach ($total_pertemuan_raw as $row) {
            $total_pertemuan_map[$row['id_kelas']][$row['id_mapel']] = (int)$row['total_pertemuan'];
        }

        // 3. Hitung total absensi (H, I, S, A) per siswa
        $absensi_data_raw = $this->db->table('absensi')
            ->select('id_siswa, id_kelas, id_mapel, status, COUNT(id_absensi) as jumlah')
            ->where('id_tahun_ajaran', $id_ta_aktif)
            ->whereIn('status', ['H', 'I', 'S', 'A'])
            ->groupBy(['id_siswa', 'id_kelas', 'id_mapel', 'status'])
            ->get()->getResultArray();

        // Buat map: $map[id_siswa][id_kelas][id_mapel][status] = jumlah
        $absensi_map = [];
        foreach ($absensi_data_raw as $row) {
            $absensi_map[$row['id_siswa']][$row['id_kelas']][$row['id_mapel']][$row['status']] = (int)$row['jumlah'];
        }

        // 4. Gabungkan semua data
        $rekap = [];
        foreach ($siswa_list as $siswa) {
            $id_s = $siswa['id_siswa'];

            $total_poin_siswa = 0;
            $total_max_poin_siswa = 0;
            $total_alpa_siswa = 0;

            // Iterasi semua map absensi siswa
            if (isset($absensi_map[$id_s])) {
                foreach ($absensi_map[$id_s] as $id_kelas => $mapel_data) {
                    foreach ($mapel_data as $id_mapel => $status_data) {
                        
                        $H = $status_data['H'] ?? 0;
                        $I = $status_data['I'] ?? 0;
                        $S = $status_data['S'] ?? 0;
                        $A = $status_data['A'] ?? 0;
                        $total_alpa_siswa += $A;

                        // (Bobot hardcode 1,0,0,0 untuk persentase kehadiran sederhana)
                        $total_poin_siswa += $H; 

                        // Ambil total pertemuan
                        $total_pertemuan = $total_pertemuan_map[$id_kelas][$id_mapel] ?? 0;
                        $total_max_poin_siswa += $total_pertemuan;
                    }
                }
            }

            $persentase = ($total_max_poin_siswa > 0) ? ($total_poin_siswa / $total_max_poin_siswa) * 100 : 0;

            $rekap[] = [
                'nama_siswa' => $siswa['nama_siswa'],
                'nama_kelas' => $siswa['nama_kelas'],
                'total_alpa' => $total_alpa_siswa,
                'persentase_kehadiran' => $persentase,
            ];
        }

        // 5. Urutkan berdasarkan Alpa (ASC) dan Persentase (DESC)
        array_multisort(
            array_column($rekap, 'total_alpa'), SORT_ASC,
            array_column($rekap, 'persentase_kehadiran'), SORT_DESC,
            $rekap
        );

        return array_slice($rekap, 0, $limit);
    }
}


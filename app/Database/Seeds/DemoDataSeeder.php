<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run()
    {
        // 1. Tahun Ajaran
        $tahunAjaran = [
            [
                'tahun_ajaran' => '2024/2025',
                'semester'     => 'Ganjil',
                'status'       => 'aktif',
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'tahun_ajaran' => '2023/2024',
                'semester'     => 'Genap',
                'status'       => 'tidak_aktif',
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ],
        ];
        $this->db->table('tahun_ajaran')->insertBatch($tahunAjaran);
        echo "✓ Tahun Ajaran seeded\n";

        // Get active tahun ajaran ID
        $taAktif = $this->db->table('tahun_ajaran')->where('status', 'aktif')->get()->getRow();
        $idTaAktif = $taAktif->id_tahun_ajaran;

        // 2. Kelas
        $kelas = [
            ['kode_kelas' => 'X-IPA-1', 'nama_kelas' => 'X IPA 1', 'id_tahun_ajaran' => $idTaAktif, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['kode_kelas' => 'X-IPA-2', 'nama_kelas' => 'X IPA 2', 'id_tahun_ajaran' => $idTaAktif, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['kode_kelas' => 'X-IPS-1', 'nama_kelas' => 'X IPS 1', 'id_tahun_ajaran' => $idTaAktif, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['kode_kelas' => 'XI-IPA-1', 'nama_kelas' => 'XI IPA 1', 'id_tahun_ajaran' => $idTaAktif, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['kode_kelas' => 'XI-IPS-1', 'nama_kelas' => 'XI IPS 1', 'id_tahun_ajaran' => $idTaAktif, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];
        $this->db->table('kelas')->insertBatch($kelas);
        echo "✓ Kelas seeded\n";

        // Get kelas IDs
        $kelasData = $this->db->table('kelas')->where('id_tahun_ajaran', $idTaAktif)->get()->getResult();

        // 3. Mata Pelajaran
        $mapel = [
            ['kode_mapel' => 'MTK', 'nama_mapel' => 'Matematika', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['kode_mapel' => 'BIN', 'nama_mapel' => 'Bahasa Indonesia', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['kode_mapel' => 'BIG', 'nama_mapel' => 'Bahasa Inggris', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['kode_mapel' => 'FIS', 'nama_mapel' => 'Fisika', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['kode_mapel' => 'KIM', 'nama_mapel' => 'Kimia', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['kode_mapel' => 'BIO', 'nama_mapel' => 'Biologi', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['kode_mapel' => 'SEJ', 'nama_mapel' => 'Sejarah', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ['kode_mapel' => 'EKO', 'nama_mapel' => 'Ekonomi', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
        ];
        $this->db->table('mapel')->insertBatch($mapel);
        echo "✓ Mata Pelajaran seeded\n";

        // Get mapel IDs
        $mapelData = $this->db->table('mapel')->get()->getResult();

        // 4. Mapel-Kelas (pivot) - Assign mapel ke kelas
        $mapelKelas = [];
        foreach ($kelasData as $k) {
            // IPA classes get science subjects
            if (strpos($k->nama_kelas, 'IPA') !== false) {
                foreach ($mapelData as $m) {
                    if (in_array($m->kode_mapel, ['MTK', 'BIN', 'BIG', 'FIS', 'KIM', 'BIO'])) {
                        $mapelKelas[] = [
                            'id_mapel' => $m->id_mapel,
                            'id_kelas' => $k->id_kelas,
                        ];
                    }
                }
            }
            // IPS classes get social subjects
            if (strpos($k->nama_kelas, 'IPS') !== false) {
                foreach ($mapelData as $m) {
                    if (in_array($m->kode_mapel, ['MTK', 'BIN', 'BIG', 'SEJ', 'EKO'])) {
                        $mapelKelas[] = [
                            'id_mapel' => $m->id_mapel,
                            'id_kelas' => $k->id_kelas,
                        ];
                    }
                }
            }
        }
        $this->db->table('mapel_kelas')->insertBatch($mapelKelas);
        echo "✓ Mapel-Kelas relations seeded\n";

        // 5. Siswa - Generate demo students
        $namaSiswa = [
            'Ahmad Fauzi', 'Budi Santoso', 'Citra Dewi', 'Dina Permata', 'Eko Prasetyo',
            'Fitri Handayani', 'Gilang Ramadhan', 'Hana Safira', 'Irfan Maulana', 'Jasmine Putri',
            'Kevin Wijaya', 'Lisa Andriani', 'Muhammad Rizki', 'Nadia Salsabila', 'Omar Farhan',
            'Putri Ayu', 'Qori Ramadhani', 'Reza Pratama', 'Siti Nurhaliza', 'Taufik Hidayat',
            'Umi Kalsum', 'Vina Melati', 'Wahyu Nugroho', 'Xena Anggraini', 'Yusuf Hakim',
            'Zahra Amelia', 'Arief Budiman', 'Bella Oktavia', 'Cahyo Wibowo', 'Desi Ratnasari',
        ];

        $siswaData = [];
        $nisCounter = 1001;
        foreach ($kelasData as $kelas) {
            // 6 siswa per kelas
            for ($i = 0; $i < 6; $i++) {
                $namaIndex = array_rand($namaSiswa);
                $siswaData[] = [
                    'id_kelas'    => $kelas->id_kelas,
                    'nis'         => 'NIS' . $nisCounter++,
                    'nama_siswa'  => $namaSiswa[$namaIndex],
                    'created_at'  => date('Y-m-d H:i:s'),
                    'updated_at'  => date('Y-m-d H:i:s'),
                ];
            }
        }
        $this->db->table('siswa')->insertBatch($siswaData);
        echo "✓ Siswa seeded (" . count($siswaData) . " students)\n";

        // 6. Settings
        $settings = [
            ['setting_key' => 'bobot_hadir', 'setting_value' => '100'],
            ['setting_key' => 'bobot_izin', 'setting_value' => '70'],
            ['setting_key' => 'bobot_sakit', 'setting_value' => '70'],
            ['setting_key' => 'bobot_alpa', 'setting_value' => '0'],
        ];
        $this->db->table('settings')->insertBatch($settings);
        echo "✓ Settings seeded\n";

        echo "\n✅ Demo data seeding complete!\n";
        echo "Login dengan: admin / Sayang@123\n";
    }
}

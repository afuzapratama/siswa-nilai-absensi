<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBuktiToAbsensi extends Migration
{
    public function up()
    {
        // Tambah kolom bukti (setelah catatan)
        $this->forge->addColumn('absensi', [
            'bukti' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'catatan',
            ],
        ]);

        // (Opsional tapi direkomendasikan) Unique constraint:
        // pastikan tidak ada duplikat record untuk kombinasi ini
        $this->db->query("
            ALTER TABLE `absensi`
            ADD UNIQUE KEY `uniq_absen_harian`
            (`id_tahun_ajaran`,`id_kelas`,`id_mapel`,`id_siswa`,`tanggal`);
        ");
    }

    public function down()
    {
        // Hapus unique key (jika kamu tambahkan di up)
        $this->db->query("ALTER TABLE `absensi` DROP INDEX `uniq_absen_harian`;");

        // Hapus kolom bukti
        $this->forge->dropColumn('absensi', 'bukti');
    }
}

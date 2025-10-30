<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAbsensiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_absensi' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_tahun_ajaran' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_kelas' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_mapel' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_siswa' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['H', 'I', 'S', 'A'], // Hadir, Izin, Sakit, Alpa
                'null'       => false,
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id_absensi', true);
        // Menambahkan foreign key
        $this->forge->addForeignKey('id_tahun_ajaran', 'tahun_ajaran', 'id_tahun_ajaran', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_kelas', 'kelas', 'id_kelas', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_mapel', 'mapel', 'id_mapel', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_siswa', 'siswa', 'id_siswa', 'CASCADE', 'CASCADE');
        
        // Index untuk mempercepat query pencarian absen
        $this->forge->addKey(['tanggal', 'id_kelas', 'id_mapel']);

        $this->forge->createTable('absensi');
    }

    public function down()
    {
        $this->forge->dropTable('absensi', true);
    }
}

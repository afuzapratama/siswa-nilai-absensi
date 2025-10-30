<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePenilaianHeaderTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_header' => [ // Diubah
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_tahun_ajaran' => [ // Diubah
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_kelas' => [ // Diubah
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_mapel' => [ // Diubah
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'judul_penilaian' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'tanggal' => [
                'type' => 'DATE',
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
        $this->forge->addKey('id_header', true); // Diubah

        // DIPERBAIKI: Semua Foreign Key
        $this->forge->addForeignKey('id_tahun_ajaran', 'tahun_ajaran', 'id_tahun_ajaran', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_kelas', 'kelas', 'id_kelas', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_mapel', 'mapel', 'id_mapel', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('penilaian_header');
    }

    public function down()
    {
        $this->forge->dropTable('penilaian_header', true);
    }
}


<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

// Ini adalah tabel pivot
class CreateMapelKelasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_mapel_kelas' => [ // Diubah
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_mapel' => [ // Diubah
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_kelas' => [ // Diubah
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
        ]);
        $this->forge->addKey('id_mapel_kelas', true); // Diubah

        // DIPERBAIKI: Referensi ke id_mapel dan id_kelas
        $this->forge->addForeignKey('id_mapel', 'mapel', 'id_mapel', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_kelas', 'kelas', 'id_kelas', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('mapel_kelas');
    }

    public function down()
    {
        $this->forge->dropTable('mapel_kelas', true);
    }
}


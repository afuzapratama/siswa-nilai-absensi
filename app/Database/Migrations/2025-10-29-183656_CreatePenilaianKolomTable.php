<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePenilaianKolomTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_kolom' => [ // Diubah
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_header' => [ // Diubah
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nama_kolom' => [
                'type'       => 'VARCHAR',
                'constraint' => '50', // cth: "N1", "N2", "Tugas 1"
            ],
            'urutan' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 0,
            ],
        ]);
        $this->forge->addKey('id_kolom', true); // Diubah

        // DIPERBAIKI: Foreign Key
        $this->forge->addForeignKey('id_header', 'penilaian_header', 'id_header', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('penilaian_kolom');
    }

    public function down()
    {
        $this->forge->dropTable('penilaian_kolom', true);
    }
}


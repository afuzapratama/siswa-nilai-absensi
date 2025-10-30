<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePenilaianDetailTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_detail' => [ // Diubah
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
            'id_kolom' => [ // Diubah
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'id_siswa' => [ // Diubah
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nilai' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2', // cth: 100.00
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id_detail', true); // Diubah

        // DIPERBAIKI: Semua Foreign Key
        $this->forge->addForeignKey('id_header', 'penilaian_header', 'id_header', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_kolom', 'penilaian_kolom', 'id_kolom', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('id_siswa', 'siswa', 'id_siswa', 'CASCADE', 'CASCADE');

        $this->forge->createTable('penilaian_detail');
    }

    public function down()
    {
        $this->forge->dropTable('penilaian_detail', true);
    }
}


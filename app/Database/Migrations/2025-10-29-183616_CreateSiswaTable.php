<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSiswaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_siswa' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'id_kelas' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'nis' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
                'null'       => true, // [FIX] Diubah ke true (boleh null)
            ],
            'nama_siswa' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
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
        $this->forge->addKey('id_siswa', true);
        $this->forge->addForeignKey('id_kelas', 'kelas', 'id_kelas', 'CASCADE', 'CASCADE');
        $this->forge->createTable('siswa');
    }

    public function down()
    {
        $this->forge->dropTable('siswa', true);
    }
}


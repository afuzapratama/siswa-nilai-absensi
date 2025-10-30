<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKelasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_kelas' => [
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
            'kode_kelas' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'nama_kelas' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
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
        $this->forge->addKey('id_kelas', true);

        // DIPERBAIKI: Referensi ke id_tahun_ajaran
        $this->forge->addForeignKey('id_tahun_ajaran', 'tahun_ajaran', 'id_tahun_ajaran', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('kelas');
    }

    public function down()
    {
        $this->forge->dropTable('kelas', true);
    }
}


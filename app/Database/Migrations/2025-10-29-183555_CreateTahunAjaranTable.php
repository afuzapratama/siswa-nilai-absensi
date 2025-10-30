<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTahunAjaranTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_tahun_ajaran' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tahun_ajaran' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            // KOLOM BARU YANG DITAMBAHKAN
            'semester' => [
                'type'       => 'ENUM',
                'constraint' => ['Ganjil', 'Genap'],
                'null'       => false,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['aktif', 'nonaktif'],
                'default'    => 'nonaktif',
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
        $this->forge->addKey('id_tahun_ajaran', true);
        $this->forge->createTable('tahun_ajaran');
    }

    public function down()
    {
        // Ditambahkan 'true' agar aman saat refresh
        $this->forge->dropTable('tahun_ajaran', true);
    }
}


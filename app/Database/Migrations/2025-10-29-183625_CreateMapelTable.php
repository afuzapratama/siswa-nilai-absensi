<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMapelTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id_mapel' => [ // Diubah
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'kode_mapel' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'unique'     => true,
            ],
            'nama_mapel' => [
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
        $this->forge->addKey('id_mapel', true); // Diubah
        $this->forge->createTable('mapel');
    }

    public function down()
    {
        $this->forge->dropTable('mapel', true);
    }
}


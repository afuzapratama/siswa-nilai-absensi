<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        // Tabel untuk auth sederhana
        $this->forge->addField([
            'id' => [ // Diubah kembali ke 'id'
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'username' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true,
            ],
            'password_hash' => [ // Diubah kembali ke 'password_hash'
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'nama_lengkap' => [
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
        $this->forge->addKey('id', true); // Diubah kembali ke 'id'
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users', true); // Ditambahkan true
    }
}


<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLoginLockColumns extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'failed_attempts' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'after'      => 'nama_lengkap',
            ],
            'lock_until' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'failed_attempts',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['failed_attempts', 'lock_until']);
    }
}

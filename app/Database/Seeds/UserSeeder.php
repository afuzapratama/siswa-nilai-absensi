<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Data user yang akan kita masukkan
        $data = [
            'username'     => 'admin',
            'password_hash' => password_hash('Sayang@123', PASSWORD_DEFAULT), 
            'nama_lengkap' => 'Administrator Utama',
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ];

        // Masukkan data ke tabel 'users'
        $this->db->table('users')->insert($data);

        echo "User 'admin' berhasil dibuat dengan password '123456'.\n";
    }
}

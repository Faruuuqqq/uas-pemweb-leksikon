<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'nama' => 'Administrator',
                'username' => 'admin',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'admin',
            ],
            [
                'nama' => 'User Biasa',
                'username' => 'user',
                'password' => password_hash('user123', PASSWORD_DEFAULT),
                'role' => 'user',
            ],
        ];

        // Using Query Builder
        $this->db->table('users')->insertBatch($data);
    }
}
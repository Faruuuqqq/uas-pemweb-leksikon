<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Clear existing data to prevent duplicates on re-runs
        $this->db->table('users')->emptyTable();

        $this->call('UserSeeder');
    }
}

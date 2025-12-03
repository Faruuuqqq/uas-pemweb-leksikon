<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EntriSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'term' => 'Absorpsi',
                'definition' => 'Proses penyerapan suatu zat oleh zat lain.',
            ],
            [
                'term' => 'Algoritma',
                'definition' => 'Urutan langkah-langkah logis untuk menyelesaikan masalah.',
            ],
            [
                'term' => 'Biodiversitas',
                'definition' => 'Keanekaragaman hayati di suatu lingkungan.',
            ],
        ];

        $this->db->table('entri')->insertBatch($data);
    }
}
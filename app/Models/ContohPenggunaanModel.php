<?php

namespace App\Models;

use CodeIgniter\Model;

class ContohPenggunaanModel extends Model
{
    protected $table            = 'contoh_penggunaan';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['entri_id', 'contoh_teks', 'terjemahan'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}

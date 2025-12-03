<?php

namespace App\Models;

use CodeIgniter\Model;

class EntriModel extends Model
{
    protected $table            = 'entri';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['term', 'definition', 'sumber_id'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function searchAndPaginate($keyword, $perPage = 20)
    {
        $builder = $this->builder(); // Get query builder instance

        $builder->select('entri.*, sumber.nama_sumber')
                ->join('sumber', 'sumber.id = entri.sumber_id', 'left');

        if ($keyword) {
            $escapedKeyword = $this->db->escapeString($keyword);
            $builder->select("entri.*, sumber.nama_sumber, MATCH(term, definition) AGAINST('{$escapedKeyword}') as score");
            $builder->where("MATCH(term, definition) AGAINST('{$escapedKeyword}' IN NATURAL LANGUAGE MODE)");
            $builder->orderBy('score', 'DESC');
        } else {
            $builder->orderBy('entri.id', 'DESC');
        }

        return [
            'entri' => $builder->paginate($perPage, 'default'),
            'pager' => $this->pager,
        ];
    }

    public function search($keyword)
    {
        // Pakai Match Against (Lebih cepat untuk data besar)
        return $this->select('*')
                    ->where("MATCH(term, definition) AGAINST ('$keyword' IN NATURAL LANGUAGE MODE)")
                    ->findAll();
    }
}

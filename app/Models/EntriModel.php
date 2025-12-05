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

    /**
     * Mencari data dengan filter, sorting, dan pagination
     */
    public function searchAndPaginate($keyword = null, $sumberId = null, $sortBy = 'term', $sortOrder = 'ASC', $perPage = 10)
    {
        // 1. Base Query (Join dengan Sumber)
        $this->select('entri.*, sumber.nama_sumber');
        $this->join('sumber', 'sumber.id = entri.sumber_id', 'left');

        // 2. Filter Keyword (Pencarian)
        if ($keyword) {
            $escapedKeyword = $this->db->escapeString($keyword);
            // Tambahkan skor relevansi untuk sorting default pencarian
            $this->select("MATCH(term, definition) AGAINST('{$escapedKeyword}') as score");
            $this->where("MATCH(term, definition) AGAINST('{$escapedKeyword}' IN NATURAL LANGUAGE MODE)");
        }

        // 3. Filter Sumber (Jika ada yang dipilih)
        if ($sumberId) {
            $this->where('entri.sumber_id', $sumberId);
        }

        // 4. Sorting Logic
        $allowedSortColumns = ['term', 'id', 'created_at', 'updated_at', 'score'];
        
        // Validasi kolom sorting
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = ($keyword) ? 'score' : 'term'; // Default: Score jika cari, Term jika tidak
        }
        
        // Validasi urutan (ASC/DESC)
        $sortOrder = strtoupper($sortOrder);
        if (!in_array($sortOrder, ['ASC', 'DESC'])) {
            $sortOrder = ($keyword && $sortBy == 'score') ? 'DESC' : 'ASC';
        }

        $this->orderBy($sortBy, $sortOrder);

        // 5. Return Data & Pager
        return [
            'entri' => $this->paginate($perPage, 'default'),
            'pager' => $this->pager,
        ];
    }

    public function search($keyword)
    {
        return $this->select('*')
                    ->where("MATCH(term, definition) AGAINST ('$keyword' IN NATURAL LANGUAGE MODE)")
                    ->findAll();
    }
}
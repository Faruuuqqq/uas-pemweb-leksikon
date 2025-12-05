<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EntriModel;
use App\Models\SumberModel;
use App\Models\UserModel;
use App\Models\UserFavoriteModel;

class Admin extends BaseController
{
    protected $entriModel;
    protected $sumberModel;
    protected $userModel;
    protected $favModel;

    public function __construct()
    {
        $this->entriModel = new EntriModel();
        $this->sumberModel = new SumberModel();
        $this->userModel = new UserModel();
        $this->favModel = new UserFavoriteModel();
    }

    public function index()
    {
        // 1. Ambil Parameter Request
        $keyword = $this->request->getVar('keyword');
        $sumberId = $this->request->getVar('sumber'); // Tambahan Filter
        $sortBy = $this->request->getVar('sort_by') ?? 'id'; // Default sort Admin: ID terbaru
        $sortOrder = $this->request->getVar('sort_order') ?? 'DESC';

        // 2. Gunakan searchAndPaginate (Sama seperti Leksikon Front-end)
        // Ini memastikan search fulltext dan sorting bekerja konsisten
        $dataResult = $this->entriModel->searchAndPaginate($keyword, $sumberId, $sortBy, $sortOrder, 10);

        // 3. Statistik Dashboard
        $stats = [
            'total_entri' => $this->entriModel->countAllResults(),
            'total_sumber' => $this->sumberModel->countAllResults(),
            'total_user' => $this->userModel->countAllResults(),
            'total_fav' => $this->favModel->countAllResults(), 
        ];

        $data = [
            'title' => 'Dashboard Admin',
            'entri' => $dataResult['entri'], // Data hasil query model
            'pager' => $dataResult['pager'], // Pagination object
            'keyword' => $keyword,
            'stats' => $stats,
            
            // Data untuk Dropdown & Filter di View
            'sumber_list' => $this->sumberModel->orderBy('nama_sumber', 'ASC')->findAll(),
            'selected_sumber' => $sumberId,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder
        ];

        return view('admin/index', $data);
    }
    
    // ... (Method CRUD lainnya: create, store, edit, update, delete TETAP SAMA) ...
    
    public function create()
    {
        $data = [
            'title' => 'Tambah Entri Baru',
            'sumber' => $this->sumberModel->findAll(),
            'validation' => \Config\Services::validation()
        ];
        return view('admin/form', $data);
    }

    public function store()
    {
        if (!$this->validate([
            'term' => 'required|min_length[2]',
            'definition' => 'required',
            'sumber_id' => 'required'
        ])) {
            return redirect()->to('/admin/entri/create')->withInput();
        }

        $this->entriModel->save([
            'term' => $this->request->getPost('term'),
            'definition' => $this->request->getPost('definition'),
            'sumber_id' => $this->request->getPost('sumber_id'),
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/admin')->with('message', 'Data berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $data = [
            'title' => 'Edit Entri',
            'entri' => $this->entriModel->find($id),
            'sumber' => $this->sumberModel->findAll(),
            'validation' => \Config\Services::validation()
        ];
        return view('admin/form', $data);
    }

    public function update($id)
    {
        $this->entriModel->update($id, [
            'term' => $this->request->getPost('term'),
            'definition' => $this->request->getPost('definition'),
            'sumber_id' => $this->request->getPost('sumber_id'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        return redirect()->to('/admin')->with('message', 'Data berhasil diupdate!');
    }

    public function delete($id)
    {
        $this->entriModel->delete($id);
        return redirect()->to('/admin')->with('message', 'Data berhasil dihapus!');
    }
}
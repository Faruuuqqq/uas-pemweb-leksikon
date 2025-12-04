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
        $keyword = $this->request->getVar('keyword');
        $builder = $this->entriModel;

        if ($keyword) {
            $builder->groupStart()
                    ->like('term', $keyword)
                    ->orLike('definition', $keyword)
                    ->groupEnd();
        }


        $stats = [
            'total_entri' => $this->entriModel->countAllResults(),
            'total_sumber' => $this->sumberModel->countAllResults(),
            'total_user' => $this->userModel->countAllResults(),
            'total_fav' => $this->favModel->countAllResults(), 
        ];

        $data = [
            'title' => 'Dashboard Admin',
            'entri' => $builder->paginate(10, 'entri'),
            'pager' => $builder->pager,
            'keyword' => $keyword,
            'stats' => $stats
        ];

        return view('admin/index', $data);
    }
    
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
            'term' => 'required|min_length(2)',
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
<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EntriModel;
use App\Models\SumberModel;

class Admin extends BaseController
{
    protected $entriModel;
    protected $sumberModel;

    public function __construct()
    {
        $this->entriModel = new EntriModel();
        $this->sumberModel = new SumberModel();
    }

    // 1. Dashboard & List Data (Read)
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

        $data = [
            'title' => 'Dashboard Admin',
            'entri' => $builder->paginate(10, 'entri'),
            'pager' => $builder->pager,
            'keyword' => $keyword
        ];

        return view('admin/index', $data);
    }

    // 2. Form Tambah (Create View)
    public function create()
    {
        $data = [
            'title' => 'Tambah Entri Baru',
            'sumber' => $this->sumberModel->findAll(),
            'validation' => \Config\Services::validation()
        ];
        return view('admin/form', $data);
    }

    // 3. Proses Simpan (Create Action)
    public function store()
    {
        // Validasi
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

    // 4. Form Edit (Update View)
    public function edit($id)
    {
        $data = [
            'title' => 'Edit Entri',
            'entri' => $this->entriModel->find($id),
            'sumber' => $this->sumberModel->findAll(),
            'validation' => \Config\Services::validation()
        ];
        return view('admin/form', $data); // Kita pakai view form yang sama utk Create/Edit
    }

    // 5. Proses Update (Update Action)
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

    // 6. Hapus (Delete Action)
    public function delete($id)
    {
        $this->entriModel->delete($id);
        return redirect()->to('/admin')->with('message', 'Data berhasil dihapus!');
    }
}

<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EntriModel;
use App\Models\SumberModel;

class Admin extends BaseController
{
    public function index()
    {
        $entriModel = new EntriModel();
        $keyword = $this->request->getVar('keyword');

        // All logic is now in the model for the main table data
        $searchResult = $entriModel->searchAndPaginate($keyword);

        // --- Insights Data ---
        $totalEntries = $entriModel->countAllResults(); // Get total entries
        $entriesPerSource = $entriModel->select('sumber.nama_sumber, COUNT(entri.id) as total_entri_per_sumber')
                                  ->join('sumber', 'sumber.id = entri.sumber_id', 'left')
                                  ->groupBy('sumber.nama_sumber')
                                  ->findAll();

        $data = [
            'entri' => $searchResult['entri'],
            'pager' => $searchResult['pager'],
            'keyword' => $keyword,
            'totalEntries' => $totalEntries, // New insight data
            'entriesPerSource' => $entriesPerSource, // New insight data
        ];

        return view('admin/index', $data);
    }

    public function create()
    {
        $sumberModel = new SumberModel();
        $sumber_list = $sumberModel->orderBy('nama_sumber', 'ASC')->findAll();

        $data = [
            'sumber_list' => $sumber_list,
            'validation' => \Config\Services::validation(), // For form validation errors
        ];

        return view('admin/form', $data);
    }

    public function store()
    {
        $rules = [
            'term' => 'required|min_length[3]',
            'definition' => 'required',
            'sumber_id' => 'permit_empty|is_natural_no_zero', // Optional, but if present must be natural number
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $entriModel = new EntriModel();
        $sumber_id = $this->request->getPost('sumber_id');
        $sumber_id = !empty($sumber_id) ? $sumber_id : null;

        $entriModel->save([
            'term' => $this->request->getPost('term'),
            'definition' => $this->request->getPost('definition'),
            'sumber_id' => $sumber_id,
        ]);

        return redirect()->to(site_url('admin'))->with('message', 'Data berhasil disimpan!');
    }

    public function edit($id)
    {
        $entriModel = new EntriModel();
        $sumberModel = new SumberModel();

        if (!is_numeric($id) || $id < 1) {
            return redirect()->to(site_url('admin'))->with('error', 'ID entri tidak valid.');
        }

        $entri = $entriModel->find($id);

        if (!$entri) {
            return redirect()->to(site_url('admin'))->with('error', "Entri dengan ID {$id} tidak ditemukan.");
        }

        $sumber_list = $sumberModel->orderBy('nama_sumber', 'ASC')->findAll();

        $data = [
            'entri' => $entri,
            'sumber_list' => $sumber_list,
            'validation' => \Config\Services::validation(),
        ];

        return view('admin/form', $data);
    }

    public function update($id)
    {
        $rules = [
            'term' => 'required|min_length[3]',
            'definition' => 'required',
            'sumber_id' => 'permit_empty|is_natural_no_zero',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $entriModel = new EntriModel();
        $sumber_id = $this->request->getPost('sumber_id');
        $sumber_id = !empty($sumber_id) ? $sumber_id : null;

        $entriModel->update($id, [
            'term' => $this->request->getPost('term'),
            'definition' => $this->request->getPost('definition'),
            'sumber_id' => $sumber_id,
        ]);

        return redirect()->to(site_url('admin'))->with('message', 'Data berhasil diperbarui!');
    }

    public function delete($id)
    {
        $entriModel = new EntriModel();

        if (!is_numeric($id) || $id < 1) {
            return redirect()->to(site_url('admin'))->with('error', 'ID entri tidak valid.');
        }

        if ($entriModel->delete($id)) {
            return redirect()->to(site_url('admin'))->with('message', 'Data berhasil dihapus!');
        } else {
            return redirect()->to(site_url('admin'))->with('error', "Gagal menghapus entri dengan ID {$id}.");
        }
    }
}

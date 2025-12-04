<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EntriModel;
use App\Models\SumberModel;
use App\Models\UserFavoriteModel;
use App\Models\ContohPenggunaanModel;

class Leksikon extends BaseController
{
    protected $entriModel;
    protected $sumberModel;

    public function __construct()
    {
        $this->entriModel = new EntriModel();
        $this->sumberModel = new SumberModel();
    }

    public function index()
    {
        helper('leksikon');
        $session = session();

        $kata_hari_ini = null;
        if ($session->has('wotd_id')) {
            $kata_hari_ini = $this->entriModel->find($session->get('wotd_id'));
        }
        if (!$kata_hari_ini) {
            $kata_hari_ini = $this->entriModel->orderBy('RAND()')->first();
            if ($kata_hari_ini) {
                $session->set('wotd_id', $kata_hari_ini['id']);
            }
        }

        $keyword = $this->request->getVar('keyword');
        $sumberId = $this->request->getVar('sumber');
        $sortBy = $this->request->getVar('sort_by') ?? 'term';
        $sortOrder = $this->request->getVar('sort_order') ?? 'ASC';

        $dataResult = $this->entriModel->searchAndPaginate($keyword, $sumberId, $sortBy, $sortOrder, 10);
        
        $daftar_entri = $dataResult['entri'];
        $pager = $dataResult['pager'];

        if ($session->get('isLoggedIn')) {
            $userFavoriteModel = new UserFavoriteModel();
            $favoritedEntries = $userFavoriteModel->where('user_id', $session->get('user_id'))->findAll();
            $favoritedEntriIds = array_column($favoritedEntries, 'entri_id');

            foreach ($daftar_entri as &$entry) {
                $entry['isFavorited'] = in_array($entry['id'], $favoritedEntriIds);
            }
        } else {
            foreach ($daftar_entri as &$entry) {
                $entry['isFavorited'] = false;
            }
        }

        $data = [
            'title' => 'Leksikon Daring',
            'kata_hari_ini' => $kata_hari_ini,
            'daftar_entri' => $daftar_entri,
            'pager' => $pager,
            'keyword' => $keyword,
            'sumber_list' => $this->sumberModel->orderBy('nama_sumber', 'ASC')->findAll(),
            'selected_sumber' => $sumberId,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'kuis_soal' => null, 
            'kuis_pilihan' => [],
            'notif_kuis' => null
        ];

        return view('leksikon/index', $data);
    }

    public function detail($id)
    {
        $entriModel = new EntriModel();
        $contohPenggunaanModel = new ContohPenggunaanModel();
        $userFavoriteModel = new UserFavoriteModel();

        if (!is_numeric($id) || $id < 1) {
             return redirect()->to('/')->with('error', 'ID tidak valid.');
        }

        $entri = $entriModel->select('entri.*, sumber.nama_sumber, sumber.deskripsi as deskripsi_sumber')
                            ->join('sumber', 'sumber.id = entri.sumber_id', 'left')
                            ->find($id);

        if (!$entri) {
            return redirect()->to('/')->with('error', 'Data tidak ditemukan');
        }

        $isFavorited = false;
        if (session()->get('isLoggedIn')) {
            $fav = $userFavoriteModel->where('user_id', session()->get('user_id'))
                                     ->where('entri_id', $id)->first();
            $isFavorited = (bool) $fav;
        }

        $data = [
            'entri' => $entri,
            'contoh' => $contohPenggunaanModel->where('entri_id', $id)->findAll(),
            'isFavorited' => $isFavorited
        ];

        return view('leksikon/detail', $data);
    }

    public function search()
    {
        $q = $this->request->getVar('q');

        if (strlen($q) < 1) {
            return $this->response->setJSON([]);
        }

        $entriModel = new EntriModel();
        $results = $entriModel->select('id, term, definition')
            ->where("MATCH(term, definition) AGAINST('{$q}' IN NATURAL LANGUAGE MODE)", null, false)
            ->limit(5)
            ->findAll();

        return $this->response->setJSON($results);
    }

    public function checkQuiz()
    {
        $entriId = $this->request->getPost('entri_id');
        $userAnswer = trim($this->request->getPost('answer'));

        $entriModel = new EntriModel();
        $entri = $entriModel->find($entriId);

        if (!$entri) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Soal tidak ditemukan']);
        }

        $isCorrect = strcasecmp($userAnswer, $entri['term']) === 0;

        return $this->response->setJSON([
            'status' => 'success',
            'is_correct' => $isCorrect,
            'correct_answer' => $entri['term']
        ]);
    }

    public function resetQuiz()
    {
        $session = session();
        $session->remove(['kuis_selesai', 'notif_kuis', 'kuis_jawaban', 'kuis_pilihan', 'kuis_soal_definisi', 'wotd_id']);
        return redirect()->to(site_url('/'));
    }

    public function getFavorites()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Anda harus login.']);
        }

        $userId = $session->get('user_id');
        $userFavoriteModel = new UserFavoriteModel();
        $entriModel = new EntriModel();

        $favoritedEntries = $userFavoriteModel->where('user_id', $userId)->findAll();
        $favoritedEntriIds = array_column($favoritedEntries, 'entri_id');

        if (empty($favoritedEntriIds)) {
            return $this->response->setJSON(['status' => 'success', 'data' => []]);
        }

        $results = $entriModel->select('id, term, definition')->whereIn('id', $favoritedEntriIds)->findAll();

        return $this->response->setJSON(['status' => 'success', 'data' => $results]);
    }

    public function toggleFavorite($id)
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Anda harus login.']);
        }

        $userId = $session->get('user_id');
        $entriId = (int) $id;

        $userFavoriteModel = new UserFavoriteModel();

        $existingFavorite = $userFavoriteModel->where('user_id', $userId)
                                              ->where('entri_id', $entriId)
                                              ->first();

        if ($existingFavorite) {
            $userFavoriteModel->delete($existingFavorite['id']);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Favorit dihapus.', 'action' => 'removed']);
        } else {
            $userFavoriteModel->insert([
                'user_id' => $userId,
                'entri_id' => $entriId,
            ]);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Favorit ditambahkan.', 'action' => 'added']);
        }
    }

    public function displayExternalSearch()
    {
        return view('leksikon/external_search');
    }

    public function queryExternalSearch()
    {
        $query = $this->request->getVar('q');
        $allResults = [];

        if (empty($query)) {
            return $this->response->setJSON($allResults);
        }

        $koelnLexiconAPI = new \App\Libraries\KoelnLexiconAPI();
        $allResults['koeln'] = $koelnLexiconAPI->search($query);

        $learnSanskritScraper = new \App\Libraries\LearnSanskritScraper();
        $allResults['learnsanskrit'] = $learnSanskritScraper->search($query);

        $sealangScraper = new \App\Libraries\SealangScraper();
        $allResults['sealangLibrary'] = $sealangScraper->searchLibrary($query);
        $allResults['sealangOjed'] = $sealangScraper->searchOjed($query);

        $sastraScraper = new \App\Libraries\SastraScraper();
        $allResults['sastra'] = $sastraScraper->search($query);

        return $this->response->setJSON($allResults);
    }
}
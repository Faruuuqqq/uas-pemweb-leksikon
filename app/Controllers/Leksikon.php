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

        // --- 1. WORD OF THE DAY ---
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

        // --- 2. LOGIKA KUIS ---
        $kuis_soal = null;
        $kuis_pilihan = [];
        
        if (!$session->has('kuis_selesai')) {
            if (!$session->has('kuis_jawaban')) {
                // Generate soal baru
                $candidates = $this->entriModel->select('id, term, definition')->orderBy('RAND()')->limit(4)->find();
                
                if (count($candidates) >= 4) {
                    $correctEntry = $candidates[0];
                    $choices = array_column($candidates, 'term');
                    shuffle($choices);
                    
                    $session->set('kuis_jawaban', $correctEntry['term']);
                    $session->set('kuis_pilihan', $choices);
                    $session->set('kuis_soal_definisi', $correctEntry['definition']);
                    
                    $kuis_soal = ['definition' => $correctEntry['definition']];
                    $kuis_pilihan = $choices;
                }
            } else {
                // Ambil soal dari sesi
                $kuis_soal = ['definition' => $session->get('kuis_soal_definisi')];
                $kuis_pilihan = $session->get('kuis_pilihan');
            }
        }

        // --- 3. PENCARIAN & DATA UTAMA ---
        $keyword = $this->request->getVar('keyword');
        $sumberId = $this->request->getVar('sumber');
        $sortBy = $this->request->getVar('sort_by') ?? 'term';
        $sortOrder = $this->request->getVar('sort_order') ?? 'ASC';

        $dataResult = $this->entriModel->searchAndPaginate($keyword, $sumberId, $sortBy, $sortOrder, 10);
        
        $daftar_entri = $dataResult['entri'];
        $pager = $dataResult['pager'];

        // --- 4. CEK FAVORIT ---
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
            'kuis_soal' => $kuis_soal, 
            'kuis_pilihan' => $kuis_pilihan,
            'notif_kuis' => $session->getFlashdata('notif_kuis')
        ];

        return view('leksikon/index', $data);
    }

    public function checkQuiz()
    {
        // PERBAIKAN 1: Tangkap input 'jawaban_user' (sesuai view), bukan 'answer'
        $userAnswer = $this->request->getPost('jawaban_user');
        $session = session();
        
        // Validasi Sesi
        if (!$session->has('kuis_jawaban')) {
            return redirect()->to('/')->with('notif_kuis', [
                'tipe' => 'warning', 
                'pesan' => 'Sesi kuis tidak valid atau sudah berakhir.'
            ]);
        }

        // Validasi Input Kosong
        if (empty($userAnswer)) {
            return redirect()->to('/')->with('notif_kuis', [
                'tipe' => 'warning', 
                'pesan' => 'Silakan pilih jawaban terlebih dahulu.'
            ]);
        }

        $correctAnswer = $session->get('kuis_jawaban');
        $isCorrect = strcasecmp(trim($userAnswer), $correctAnswer) === 0;

        // PERBAIKAN 2: Set Flashdata & Redirect (Bukan Return JSON)
        if ($isCorrect) {
            $session->setFlashdata('notif_kuis', [
                'tipe' => 'success', 
                'pesan' => 'Selamat! Jawaban Anda <strong>BENAR</strong>.'
            ]);
            $session->set('kuis_selesai', true);
        } else {
            $session->setFlashdata('notif_kuis', [
                'tipe' => 'danger', 
                'pesan' => 'Jawaban SALAH. Yang benar adalah: <strong>' . esc($correctAnswer) . '</strong>'
            ]);
            // Reset soal agar user bisa coba lagi besok atau refresh soal
            $session->remove(['kuis_jawaban', 'kuis_pilihan', 'kuis_soal_definisi']);
        }

        return redirect()->to('/#kuis-section'); // Redirect balik ke home (bisa tambah anchor ID jika ada)
    }

    public function resetQuiz()
    {
        $session = session();
        $session->remove(['kuis_selesai', 'notif_kuis', 'kuis_jawaban', 'kuis_pilihan', 'kuis_soal_definisi']);
        return redirect()->to(site_url('/'));
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

    // Method AJAX untuk search autocomplete (tetap JSON karena dipanggil via JS)
    public function search()
    {
        $q = $this->request->getVar('q');
        if (strlen($q) < 1) return $this->response->setJSON([]);
        $entriModel = new EntriModel();
        $results = $entriModel->select('id, term, definition')
            ->where("MATCH(term, definition) AGAINST('{$q}' IN NATURAL LANGUAGE MODE)", null, false)
            ->limit(5)->findAll();
        return $this->response->setJSON($results);
    }

    // Method AJAX Favorit (tetap JSON)
    public function getFavorites()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) return $this->response->setJSON(['status' => 'error', 'message' => 'Anda harus login.']);
        $userId = $session->get('user_id');
        $userFavoriteModel = new UserFavoriteModel();
        $entriModel = new EntriModel();
        $favoritedEntries = $userFavoriteModel->where('user_id', $userId)->findAll();
        $favoritedEntriIds = array_column($favoritedEntries, 'entri_id');
        if (empty($favoritedEntriIds)) return $this->response->setJSON(['status' => 'success', 'data' => []]);
        $results = $entriModel->select('id, term, definition')->whereIn('id', $favoritedEntriIds)->findAll();
        return $this->response->setJSON(['status' => 'success', 'data' => $results]);
    }

    public function toggleFavorite($id)
    {
        $session = session();
        if (! $session->get('isLoggedIn')) return $this->response->setJSON(['status' => 'error', 'message' => 'Anda harus login.']);
        $userId = $session->get('user_id');
        $entriId = (int) $id;
        $userFavoriteModel = new UserFavoriteModel();
        $existingFavorite = $userFavoriteModel->where('user_id', $userId)->where('entri_id', $entriId)->first();
        if ($existingFavorite) {
            $userFavoriteModel->delete($existingFavorite['id']);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Favorit dihapus.', 'action' => 'removed']);
        } else {
            $userFavoriteModel->insert(['user_id' => $userId, 'entri_id' => $entriId]);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Favorit ditambahkan.', 'action' => 'added']);
        }
    }

    public function displayExternalSearch() { return view('leksikon/external_search'); }
    
    public function queryExternalSearch()
    {
        $query = $this->request->getVar('q');
        $allResults = [];
        if (empty($query)) return $this->response->setJSON($allResults);
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
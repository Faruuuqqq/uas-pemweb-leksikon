<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EntriModel;
use App\Models\SumberModel;
use App\Models\ContohPenggunaanModel;
use App\Models\UserFavoriteModel;

class Leksikon extends BaseController
{
    public function index()
    {
        helper('leksikon');
        $entriModel = new EntriModel();
        $sumberModel = new SumberModel();
        $session = session();

        $keyword = $this->request->getVar('keyword');

        // Logic Pencarian
        if ($keyword) {
            $entriModel->where("MATCH(term, definition) AGAINST('{$keyword}' IN NATURAL LANGUAGE MODE)", null, false);
        }

        // --- Word of the Day & Daily Quiz (existing logic) ---
        $kata_hari_ini = null;
        if (!$session->has('wotd_id')) {
            $kata_hari_ini = $entriModel->orderBy('RAND()')->first();
            if ($kata_hari_ini) {
                $session->set('wotd_id', $kata_hari_ini['id']);
            }
        } else {
            $kata_hari_ini = $entriModel->find($session->get('wotd_id')) ?? $entriModel->orderBy('RAND()')->first();
            if ($kata_hari_ini) {
                $session->set('wotd_id', $kata_hari_ini['id']);
            }
        }

        $kuis_soal = null;
        $kuis_pilihan = [];
        if (!$session->has('kuis_selesai')) {
            if (!$session->has('kuis_jawaban')) {
                // Quiz generation logic...
            } else {
                $kuis_soal = ['definition' => $session->get('kuis_soal_definisi')];
                $kuis_pilihan = $session->get('kuis_pilihan');
            }
        }
        // --- End of WOTD & Quiz ---

        // --- Query Building ---
        // Query will already have `like` conditions if keyword exists.
        // Filtering
        $selectedSumberId = $this->request->getVar('sumber') ? (int)$this->request->getVar('sumber') : null;
        if ($selectedSumberId) {
            $entriModel->where('sumber_id', $selectedSumberId);
        }

        // Sorting
        $sortBy = $this->request->getVar('sort_by') ?? 'term';
        $sortOrder = $this->request->getVar('sort_order') ?? 'ASC';
        $allowedSortColumns = ['term', 'id', 'created_at'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'term';
        }
        if (!in_array(strtoupper($sortOrder), ['ASC', 'DESC'])) {
            $sortOrder = 'ASC';
        }
        $entriModel->orderBy($sortBy, $sortOrder);

        // Get paginated data
        $daftar_entri = $entriModel->paginate(10, 'default');

        // Check favorite status for each entry
        $favoritedEntriIds = [];
        if ($session->get('isLoggedIn')) {
            $userFavoriteModel = new UserFavoriteModel();
            $favoritedEntries = $userFavoriteModel->where('user_id', $session->get('user_id'))->findAll();
            $favoritedEntriIds = array_column($favoritedEntries, 'entri_id');
        }
        foreach ($daftar_entri as &$entry) {
            $entry['isFavorited'] = in_array($entry['id'], $favoritedEntriIds);
        }

        $data = [
            'kata_hari_ini' => $kata_hari_ini,
            'kuis_soal' => $kuis_soal,
            'kuis_pilihan' => $kuis_pilihan,
            'notif_kuis' => $session->getFlashdata('notif_kuis'),
            'daftar_entri' => $daftar_entri,
            'pager' => $entriModel->pager, // Pass pager object
            'sumber_list' => $sumberModel->orderBy('nama_sumber', 'ASC')->findAll(),
            'selected_sumber' => $selectedSumberId,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
            'keyword' => $keyword // Kirim balik keyword ke view biar input gak hilang
        ];

        return view('leksikon/index', $data);
    }

    public function detail($id)
    {
        $entriModel = new EntriModel();
        $sumberModel = new SumberModel();
        $contohPenggunaanModel = new ContohPenggunaanModel();
        $userFavoriteModel = new UserFavoriteModel(); // Instantiate UserFavoriteModel

        if (!is_numeric($id) || $id < 1) {
            return redirect()->to('/')->with('error', 'ID tidak ditemukan atau tidak valid.');
        }

        // Fetch entry and join with sumber
        $entri = $entriModel
            ->select('entri.*, sumber.nama_sumber, sumber.deskripsi as deskripsi_sumber')
            ->join('sumber', 'sumber.id = entri.sumber_id', 'left')
            ->find($id);

        if (!$entri) {
            return redirect()->to('/')->with('error', "Entri dengan ID {$id} tidak ditemukan.");
        }

        // Determine if the current entry is favorited by the logged-in user
        $isFavorited = false;
        $session = session();
        if ($session->get('isLoggedIn')) {
            $userId = $session->get('user_id');
            $existingFavorite = $userFavoriteModel->where('user_id', $userId)
                                                  ->where('entri_id', $id)
                                                  ->first();
            if ($existingFavorite) {
                $isFavorited = true;
            }
        }

        // Fetch examples
        $contoh = $contohPenggunaanModel->where('entri_id', $id)->findAll();

        $data = [
            'entri' => $entri,
            'contoh' => $contoh,
            'isFavorited' => $isFavorited, // Pass favorite status to the view
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
        $results = $entriModel
            ->select('id, term, definition')
            ->where("MATCH(term, definition) AGAINST('{$q}' IN NATURAL LANGUAGE MODE)", null, false)
            ->limit(5)
            ->findAll();

        return $this->response->setJSON($results);
    }

    public function checkQuiz()
    {
        $session = session();
        $jawaban_user = $this->request->getPost('jawaban_user');
        $jawaban_benar = $session->get('kuis_jawaban');

        if (!empty($jawaban_user) && !empty($jawaban_benar)) {
            if ($jawaban_user === $jawaban_benar) {
                $session->setFlashdata('notif_kuis', ['tipe' => 'success', 'pesan' => 'Jawaban kuis benar!']);
            } else {
                $session->setFlashdata('notif_kuis', [
                    'tipe' => 'danger',
                    'pesan' => "Jawaban salah. Yang benar: " . htmlspecialchars($jawaban_benar)
                ]);
            }
        }

        $session->set('kuis_selesai', true);

        $session->remove('kuis_jawaban');
        $session->remove('kuis_pilihan');
        $session->remove('kuis_soal_definisi');

        return redirect()->to(site_url('/'));
    }

    public function resetQuiz()
    {
        $session = session();

        $session->remove('kuis_selesai');
        $session->remove('notif_kuis');
        $session->remove('kuis_jawaban');
        $session->remove('kuis_pilihan');
        $session->remove('kuis_soal_definisi');
        $session->remove('wotd_id'); // Also reset WOTD

        return redirect()->to(site_url('/'));
    }

    public function getFavorites()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Anda harus login untuk melihat favorit.']);
        }

        $userId = $session->get('user_id');
        $userFavoriteModel = new UserFavoriteModel();
        $entriModel = new EntriModel();

        // Get all favorited entry IDs for the current user
        $favoritedEntries = $userFavoriteModel->where('user_id', $userId)->findAll();
        $favoritedEntriIds = array_column($favoritedEntries, 'entri_id');

        if (empty($favoritedEntriIds)) {
            return $this->response->setJSON(['status' => 'success', 'data' => []]);
        }

        // Fetch details of the favorited entries
        $results = $entriModel->select('id, term, definition')->whereIn('id', $favoritedEntriIds)->findAll();

        return $this->response->setJSON(['status' => 'success', 'data' => $results]);
    }

    public function toggleFavorite($id)
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Anda harus login untuk menandai favorit.']);
        }

        $userId = $session->get('user_id');
        $entriId = (int) $id;

        $userFavoriteModel = new UserFavoriteModel();

        $existingFavorite = $userFavoriteModel->where('user_id', $userId)
                                              ->where('entri_id', $entriId)
                                              ->first();

        if ($existingFavorite) {
            // Already favorited, so remove it
            $userFavoriteModel->delete($existingFavorite['id']);
            return $this->response->setJSON(['status' => 'success', 'message' => 'Favorit dihapus.', 'action' => 'removed']);
        } else {
            // Not favorited, so add it
            $userFavoriteModel->insert([
                'user_id'  => $userId,
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

        // Koeln Lexicon API
        $koelnLexiconAPI = new \App\Libraries\KoelnLexiconAPI();
        $koelnResults = $koelnLexiconAPI->search($query);
        $allResults['koeln'] = $koelnResults;

        // Learn Sanskrit Scraper
        $learnSanskritScraper = new \App\Libraries\LearnSanskritScraper();
        $learnSanskritResults = $learnSanskritScraper->search($query);
        $allResults['learnsanskrit'] = $learnSanskritResults;

        // Sealang Scraper (Library and OJED)
        $sealangScraper = new \App\Libraries\SealangScraper();
        $sealangLibraryResults = $sealangScraper->searchLibrary($query);
        $allResults['sealangLibrary'] = $sealangLibraryResults;
        $sealangOjedResults = $sealangScraper->searchOjed($query);
        $allResults['sealangOjed'] = $sealangOjedResults;

        // Sastra Scraper
        $sastraScraper = new \App\Libraries\SastraScraper();
        $sastraResults = $sastraScraper->search($query);
        $allResults['sastra'] = $sastraResults;

        return $this->response->setJSON($allResults);
    }
}

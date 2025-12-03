<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Libraries\PortalScraper;

class Portal extends BaseController
{
    public function index()
    {
        return view('portal/index', [
            'title' => 'Portal Pencarian Leksikon',
            'results' => null
        ]);
    }

    public function search()
    {
        $keyword = $this->request->getVar('keyword');
        $source = $this->request->getVar('source');
        
        $scraper = new PortalScraper();
        $results = [];

        // Logic Pemilihan Sumber
        switch ($source) {
            case 'sastra':
                $results = $scraper->searchSastraOrg($keyword);
                break;
            case 'sanskrit': // Cologne
                $results = $scraper->searchSanskritCologne($keyword);
                break;
            case 'learnsanskrit':
                $results = $scraper->searchLearnSanskrit($keyword);
                break;
            case 'ojed': // Old Javanese
                $results = $scraper->searchSealangOJED($keyword);
                break;
            case 'sealang_lib': // Library
                $results = $scraper->searchSealangLibrary($keyword);
                break;
            case 'all':
                // For non-AJAX, we just show the view and let AJAX handle the fetching.
                break;
            default:
                $results = [];
                break;
        }

        return view('portal/index', [
            'title' => 'Hasil Pencarian: ' . $keyword,
            'results' => $results, // This will be empty for 'all'
            'keyword' => $keyword,
            'source' => $source
        ]);
    }

    public function api_search()
    {
        // Hanya menerima request AJAX
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(404);
        }

        $keyword = $this->request->getVar('keyword');
        $source  = $this->request->getVar('source');
        
        if (empty($keyword) || empty($source)) {
            return $this->response->setJSON(['error' => 'Keyword and source are required.'])->setStatusCode(400);
        }

        $scraper = new PortalScraper();
        $data = [];

        try {
            switch ($source) {
                case 'sastra':
                    $data = $scraper->searchSastraOrg($keyword);
                    break;
                case 'sanskrit':
                    $data = $scraper->searchSanskritCologne($keyword);
                    break;
                case 'learnsanskrit':
                    $data = $scraper->searchLearnSanskrit($keyword);
                    break;
                case 'ojed':
                    $data = $scraper->searchSealangOJED($keyword);
                    break;
                case 'sealang_lib':
                    $data = $scraper->searchSealangLibrary($keyword);
                    break;
                default:
                    return $this->response->setJSON(['error' => 'Invalid source.'])->setStatusCode(400);
            }
            return $this->response->setJSON($data);
        } catch (\Exception $e) {
            return $this->response->setJSON(['error' => 'Scraper failed: ' . $e->getMessage()])->setStatusCode(500);
        }
    }
}
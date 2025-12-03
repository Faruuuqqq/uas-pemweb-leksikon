<?php 
namespace App\Libraries;

use CodeIgniter\HTTP\CURLRequest;
use Config\Services;

class PortalScraper {

    protected $client;

    public function __construct() {
        // Setup Client: Timeout agak lama (15s) karena koneksi ke luar negeri bisa lambat
        $this->client = Services::curlrequest([
            'timeout' => 15,
            'verify' => false, // Bypass SSL (Penting buat web jadul/kampus)
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
        ]);
    }

    // --- 1. SASTRA.ORG (SUDAH ADA SEBELUMNYA) ---
    public function searchSastraOrg($keyword) {
        $url = "https://www.sastra.org/sastra/leksikon/leksikon.inx.php";
        
        // Buat ID Palsu
        $fake_ui = bin2hex(random_bytes(6)) . 'e'; 

        // Payload JSON yang sudah kita pelajari
        $payload = [
            "sn" => "leksikon",
            "ui" => $fake_ui,
            "us" => 0,
            "leksikon" => [
                "cs" => "adns",
                "fs" => 0,
                "nr" => 20, // Ambil 20 hasil
                "ps" => 20,
                "sk" => $keyword, // <--- Kata kunci user masuk sini
                "sl" => 2,
                "su" => 0,
                "st" => 0,
                "el" => "entri"
            ]
        ];

        try {
            $response = $this->client->get($url, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                    'X-Requested-With' => 'XMLHttpRequest',
                    // Gunakan cookie yang masih valid atau ambil baru via cURL browser
                    'Cookie' => '1847b700c692c351b59c099e6c59aab5=4emol43r1o92pancvaaqggmm9k'
                ],
                'query' => ['param' => json_encode($payload)]
            ]);

            $html = $response->getBody();
            
            // Parsing HTML response sederhana (tanpa DOMDocument biar cepet)
            // Kita cari pola <tr>...</tr>
            $results = [];
            
            // Gunakan regex untuk mengambil teks di dalam span ysl-txt-bld (Kata)
            // dan teks sisanya (Arti)
            // Ini regex sederhana, untuk produksi sebaiknya pakai DOMDocument
            if (preg_match_all('/<tr.*?<span class="ysl-txt-bld">(.*?)<\/span>(.*?)<\/div>/s', $html, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $kata = strip_tags($match[1]); // Kata kunci
                    $arti = strip_tags($match[2]); // Definisi
                    $arti = trim(str_replace(['"', "'", ':'], '', $arti));
                    
                    $results[] = [
                        'term' => $kata,
                        'definition' => $arti,
                        'source' => 'Sastra.org'
                    ];
                }
            }

            return $results;

        } catch (\Exception $e) {
            return [['term' => 'Error', 'definition' => 'Gagal mengambil data: ' . $e->getMessage()]];
        }
    }

    // --- 2. SANSKRIT LEXICON (COLOGNE) ---
    public function searchSanskritCologne($keyword) {
        // Kita pakai kamus Monier-Williams (MW)
        $url = "https://www.sanskrit-lexicon.uni-koeln.de/scans/MWScan/2020/web/webtc/getword.php";
        
        try {
            $response = $this->client->get($url, [
                'query' => [
                    'key' => $keyword,
                    'filter' => 'devanagari', // Output Devanagari
                    'noLit' => 'off',
                    'transLit' => 'HK' // Input format (Harvard-Kyoto biasanya aman untuk huruf latin)
                ]
            ]);
            
            $html = $response->getBody();
            $results = [];

            // Parsing Hasil (Mereka mereturn HTML Fragment)
            // Polanya biasanya ada di dalam <div class="card"> atau text biasa
            if (preg_match_all('/<span class="red">(.*?)<\/span>.*?<body>(.*?)<\/body>/s', $html, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $results[] = [
                        'term' => strip_tags($match[1]), // Kata Asli
                        'definition' => strip_tags($match[2]), // Arti
                        'source' => 'Sanskrit Cologne (MW)'
                    ];
                }
            }
            
            // Fallback kalau regex gagal tapi ada data
            if (empty($results) && strlen($html) > 50 && strpos($html, 'not found') === false) {
                 $results[] = [
                    'term' => $keyword,
                    'definition' => strip_tags($html), // Ambil semua teks bersih
                    'source' => 'Sanskrit Cologne (MW)'
                ];
            }

            return $results;
        } catch (\Exception $e) {
            return [['term' => 'Error', 'definition' => 'Gagal mengambil data: ' . $e->getMessage()]];
        }
    }

    // --- 3. LEARN SANSKRIT.CC ---
    public function searchLearnSanskrit($keyword) {
        $url = "https://www.learnsanskrit.cc/translate";
        
        try {
            $response = $this->client->get($url, [
                'query' => [
                    'search' => $keyword,
                    'dir' => 'au' // Auto Detect direction
                ]
            ]);
            
            $html = $response->getBody();
            $results = [];

            // Web ini pakai tabel class "table"
            // Kita cari baris <tr> yang punya class "success" atau biasa
            if (preg_match_all('/<tr>(.*?)<\/tr>/s', $html, $rows)) {
                foreach ($rows[1] as $rowContent) {
                    // Ambil kolom <td>
                    if (preg_match_all('/<td>(.*?)<\/td>/s', $rowContent, $cols)) {
                        if (count($cols[1]) >= 2) {
                            $term = strip_tags($cols[1][0]);
                            $def = strip_tags($cols[1][1]);
                            
                            // Bersihkan whitespace
                            $term = trim(preg_replace('/
+/', ' ', $term));
                            $def = trim(preg_replace('/
+/', ' ', $def));

                            if (!empty($term) && !empty($def)) {
                                $results[] = [
                                    'term' => $term,
                                    'definition' => $def,
                                    'source' => 'LearnSanskrit.cc'
                                ];
                            }
                        }
                    }
                }
            }
            return $results;
        } catch (\Exception $e) {
            return [['term' => 'Error', 'definition' => 'Gagal mengambil data: ' . $e->getMessage()]];
        }
    }

    // --- 4. SEALANG OJED (OLD JAVANESE) ---
    public function searchSealangOJED($keyword) {
        $url = "http://sealang.net/ojed/search.pl";
        
        try {
            $response = $this->client->get($url, [
                'query' => [
                    'service' => 'dictionary',
                    'query' => $keyword
                ]
            ]);
            
            $html = $response->getBody();
            $results = [];

            // OJED biasanya menampilkan hasil dalam <span class='head'>kata</span> dan isinya
            // Ini parsing kasar karena HTML SEALang sangat berantakan (old school)
            
            // Kita coba ambil blok teks yang relevan
            if (preg_match_all('/<span class=["']?head["']?>(.*?)<\/span>(.*?)<br>/s', $html, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $head = strip_tags($match[1]);
                    $body = strip_tags($match[2]);
                    
                    $results[] = [
                        'term' => trim($head),
                        'definition' => trim($body),
                        'source' => 'SEAlang OJED'
                    ];
                }
            }

            return $results;
        } catch (\Exception $e) {
            return [['term' => 'Error', 'definition' => 'Gagal mengambil data: ' . $e->getMessage()]];
        }
    }

    // --- 5. SEALANG LIBRARY (OPTIONAL/KATALOG) ---
    public function searchSealangLibrary($keyword) {
        // Ini sebenarnya untuk mencari buku, bukan kamus kata per kata
        // Tapi kita buatkan saja sesuai permintaan
        $url = "http://sealang.net/library/search.pl";
        
        try {
            $response = $this->client->get($url, [
                'query' => [
                    'query' => $keyword
                ]
            ]);
            
            $html = $response->getBody();
            $results = [];

            // Parsing Judul Buku/Artikel
            if (preg_match_all('/<b>(.*?)<\/b>.*?<i>(.*?)<\/i>/s', $html, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $author = strip_tags($match[1]);
                    $title = strip_tags($match[2]);
                    
                    $results[] = [
                        'term' => $title, // Kita anggap Judul Buku sebagai Term
                        'definition' => "Penulis: $author (Katalog Perpustakaan)",
                        'source' => 'SEAlang Library'
                    ];
                }
            }
            return $results;
        } catch (\Exception $e) {
            return [['term' => 'Error', 'definition' => 'Gagal mengambil data: ' . $e->getMessage()]];
        }
    }
}

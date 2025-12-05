<?php 
namespace App\Libraries;

use CodeIgniter\HTTP\CURLRequest;
use Config\Services;

class PortalScraper {

    protected $client;

    public function __construct() {
        // Setup Client dengan Header 'Penyamaran' agar tidak diblokir
        $this->client = Services::curlrequest([
            'timeout' => 15,
            'verify' => false, // Abaikan SSL error
            'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36'
        ]);
    }

    // --- 1. SASTRA.ORG (Jawa) ---
    public function searchSastraOrg($keyword) {
        $url = "https://www.sastra.org/sastra/leksikon/leksikon.inx.php";
        
        // Generate UI ID Random (Hex 13 chars)
        $fake_ui = bin2hex(random_bytes(6)) . 'e'; 

        $payload = [
            "sn" => "leksikon",
            "ui" => $fake_ui,
            "us" => 0,
            "leksikon" => [
                "cs" => "adns",
                "fs" => 0,
                "nr" => 20,
                "ps" => 20,
                "sk" => $keyword,
                "sl" => 2,
                "su" => 0,
                "st" => 0,
                "el" => "entri"
            ]
        ];

        try {
            // Header dan Cookie Khusus Sastra.org
            $response = $this->client->get($url, [
                'headers' => [
                    'X-Requested-With' => 'XMLHttpRequest',
                    'Cookie' => '1847b700c692c351b59c099e6c59aab5=4emol43r1o92pancvaaqggmm9k' // Pastikan cookie ini update
                ],
                'query' => ['param' => json_encode($payload)]
            ]);

            // Parsing Sederhana
            $html = $response->getBody();
            $results = [];
            
            // Regex untuk ambil kata (bold) dan arti (sisanya)
            // Pola: <span class="ysl-txt-bld">KATA</span> ARTI...
            if (preg_match_all('/<span class="ysl-txt-bld">(.*?)<\/span>(.*?)<\/div>/s', $html, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $term = strip_tags($match[1]);
                    $def = strip_tags($match[2]);
                    $def = trim(str_replace(['"', "'", ':'], '', $def)); // Bersihkan simbol
                    
                    $results[] = [
                        'term' => $term,
                        'definition' => $def,
                        'source' => 'Sastra.org'
                    ];
                }
            }
            return $results;

        } catch (\Exception $e) {
            return [['term' => 'Error', 'definition' => 'Koneksi Sastra.org gagal.', 'source' => 'System']];
        }
    }

    // --- 2. SANSKRIT LEXICON (Cologne) ---
    // Update sesuai temuan kamu: filter=roman, accent=no
    public function searchSanskritCologne($keyword) {
        $url = "https://www.sanskrit-lexicon.uni-koeln.de/scans/MWScan/2020/web/webtc/getword.php";
        
        try {
            $response = $this->client->get($url, [
                'query' => [
                    'key' => $keyword,
                    'filter' => 'roman', // Output huruf latin
                    'accent' => 'no',    // Tanpa aksen aneh
                    'transLit' => 'hk'   // Input Harvard-Kyoto
                ]
            ]);
            
            $html = $response->getBody();
            $results = [];

            // Parsing HTML Cologne
            // Biasanya formatnya: <span class="red">KATA</span> ...body...
            if (preg_match_all('/<span class="red">(.*?)<\/span>(.*?)<br>/s', $html, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $results[] = [
                        'term' => strip_tags($match[1]),
                        'definition' => strip_tags($match[2]),
                        'source' => 'Sanskrit Cologne'
                    ];
                }
            }
            
            // Fallback jika regex gagal tapi ada hasil
            if (empty($results) && strpos($html, 'not found') === false && strlen($html) > 50) {
                 $results[] = [
                    'term' => $keyword,
                    'definition' => strip_tags($html),
                    'source' => 'Sanskrit Cologne'
                ];
            }

            return $results;
        } catch (\Exception $e) {
            return [];
        }
    }

    // --- 3. LEARN SANSKRIT.CC ---
    // Update: Pakai API JSON yang kamu temukan!
    public function searchLearnSanskrit($keyword) {
        $url = "https://www.learnsanskrit.cc/getdata/word/gettranslation";
        
        try {
            $response = $this->client->get($url, [
                'query' => [
                    'word' => $keyword,
                    'direction' => 'au',
                    'count' => 0,
                    'exact' => 'false'
                ]
            ]);
            
            // Karena ini API JSON, kita decode langsung
            $jsonData = json_decode($response->getBody(), true);
            $results = [];

            // Cek struktur JSON (biasanya ada array 'result' atau langsung array)
            if (is_array($jsonData)) {
                foreach ($jsonData as $item) {
                    // Sesuaikan key JSON-nya (misal: 'key' dan 'value')
                    // Kita asumsi ada field 'word' dan 'meaning' atau sejenisnya
                    // Kalau struktur pastinya belum tau, kita dump dulu biasanya.
                    // Tapi untuk aman, kita coba ambil field umum.
                    $term = $item['word'] ?? $item['key'] ?? $keyword;
                    $def = $item['translation'] ?? $item['value'] ?? json_encode($item);

                    $results[] = [
                        'term' => $term,
                        'definition' => $def,
                        'source' => 'LearnSanskrit.cc'
                    ];
                }
            }
            return $results;

        } catch (\Exception $e) {
            return [];
        }
    }

    // --- 4. SEALANG OJED (Jawa Kuno) ---
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

            // Parsing OJED (Old School HTML)
            // Cari pola: <span class='head'>kata</span> ...definisi...
            if (preg_match_all('/<span class=[\'"]?head[\'"]?>(.*?)<\/span>(.*?)<br>/s', $html, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $results[] = [
                        'term' => strip_tags($match[1]),
                        'definition' => strip_tags($match[2]),
                        'source' => 'SEAlang OJED'
                    ];
                }
            }
            return $results;
        } catch (\Exception $e) {
            return [];
        }
    }

    // --- 5. SEALANG LIBRARY ---
    public function searchSealangLibrary($keyword) {
        $url = "http://sealang.net/library/search.pl";
        
        try {
            $response = $this->client->get($url, [
                'query' => [
                    'query' => $keyword
                ]
            ]);
            
            $html = $response->getBody();
            $results = [];

            // Parsing Katalog Buku
            // Pola: <b>Penulis</b> ... <i>Judul</i>
            if (preg_match_all('/<b>(.*?)<\/b>.*?<i>(.*?)<\/i>/s', $html, $matches, PREG_SET_ORDER)) {
                foreach ($matches as $match) {
                    $author = strip_tags($match[1]);
                    $title = strip_tags($match[2]);
                    
                    $results[] = [
                        'term' => $title,
                        'definition' => "Penulis: $author (Referensi Buku)",
                        'source' => 'SEAlang Library'
                    ];
                }
            }
            return $results;
        } catch (\Exception $e) {
            return [];
        }
    }
}
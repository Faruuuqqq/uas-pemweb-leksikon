<?php

namespace App\Libraries;

use CodeIgniter\HTTP\CURLRequest;

class SealangScraper
{
    private $client;

    public function __construct()
    {
        $this->client = \Config\Services::curlrequest([
            'timeout' => 10, // 10 seconds timeout
        ]);
    }

    /**
     * Scrapes sealang.net/library/ for the given search term.
     *
     * @param string $term The search term.
     * @return array An array of search results, or an empty array on failure.
     */
    public function searchLibrary(string $term): array
    {
        $results = [];
        $baseUrl = 'http://sealang.net/library/dictionaries/sanskrit.htm'; // Base URL to try
        $searchUrl = $baseUrl . '?search=' . urlencode($term);

        try {
            $response = $this->client->get($searchUrl);

            if ($response->getStatusCode() === 200) {
                $html = $response->getBody();
                // This is a placeholder for actual HTML parsing.
                // Without knowing the HTML structure of a successful search,
                // this will just return a placeholder result.
                $results[] = [
                    'word'       => $term,
                    'definition' => 'Search functionality for sealang.net/library/ is complex. '
                                  . 'Results may vary or require specific dictionary page navigation.',
                    'source'     => 'sealang.net/library/',
                    'link'       => $searchUrl,
                ];
                // In a real scenario, deeper HTML parsing would go here.
                // For now, we return a simple indicator.
            } else {
                log_message('info', 'SealangScraper Library search for "' . $term . '" failed with status ' . $response->getStatusCode());
            }
        } catch (\Exception $e) {
            log_message('error', 'SealangScraper Library search error: ' . $e->getMessage());
        }

        return $results;
    }

    /**
     * Scrapes sealang.net/ojed/ for the given search term.
     *
     * @param string $term The search term.
     * @return array An array of search results, or an empty array on failure.
     */
    public function searchOjed(string $term): array
    {
        $results = [];
        $baseUrl = 'http://sealang.net/ojed/';
        $searchUrl = $baseUrl . 'search.htm?q=' . urlencode($term);

        try {
            $response = $this->client->get($searchUrl);

            if ($response->getStatusCode() === 200) {
                $html = $response->getBody();
                // This is a placeholder for actual HTML parsing.
                // Without knowing the HTML structure of a successful search,
                // this will just return a placeholder result.
                $results[] = [
                    'word'       => $term,
                    'definition' => 'Search functionality for sealang.net/ojed/ is complex. '
                                  . 'Results may vary or require specific dictionary page navigation.',
                    'source'     => 'sealang.net/ojed/',
                    'link'       => $searchUrl,
                ];
                // In a real scenario, deeper HTML parsing would go here.
                // For now, we return a simple indicator.
            } else {
                log_message('info', 'SealangScraper OJED search for "' . $term . '" failed with status ' . $response->getStatusCode());
            }
        } catch (\Exception $e) {
            log_message('error', 'SealangScraper OJED search error: ' . $e->getMessage());
        }

        return $results;
    }
}

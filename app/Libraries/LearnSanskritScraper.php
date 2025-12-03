<?php namespace App\Libraries;

use CodeIgniter\HTTP\CURLRequest;

class LearnSanskritScraper
{
    private $baseUrl = 'https://www.learnsanskrit.cc/translate';
    private $client;

    public function __construct()
    {
        $this->client = \Config\Services::curlrequest([
            'baseURI' => $this->baseUrl,
            'timeout' => 10, // 10 seconds timeout
        ]);
    }

    /**
     * Scrapes learnsanskrit.cc for the given search term.
     *
     * @param string $term The search term.
     * @return array An array of search results, or an empty array on failure.
     */
    public function search(string $term): array
    {
        $results = [];
        try {
            $response = $this->client->get('', [
                'query' => [
                    'search' => $term,
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                $html = $response->getBody();
                // Find the table with id="results"
                if (preg_match('/<table class="table0" id="results">(.*?)<\/table>/s', $html, $tableMatch)) {
                    $tableContent = $tableMatch[1];

                    // Find all table rows within the results table
                    if (preg_match_all('/<tr.*?>(.*?)<\/tr>/s', $tableContent, $rowMatches)) {
                        foreach ($rowMatches[1] as $rowHtml) {
                            // Find all table data cells within each row
                            if (preg_match_all('/<td.*?>(.*?)<\/td>/s', $rowHtml, $cellMatches)) {
                                $cells = array_map('strip_tags', $cellMatches[1]); // Remove HTML tags from cell content
                                $cells = array_map('trim', $cells); // Trim whitespace

                                // Assuming the structure: first cell is word, second is definition
                                if (count($cells) >= 2) {
                                    $word       = !empty($cells[0]) ? $cells[0] : 'N/A';
                                    $definition = !empty($cells[1]) ? $cells[1] : 'No definition provided.';

                                    $results[] = [
                                        'word'       => $word,
                                        'definition' => $definition,
                                        'source'     => 'learnsanskrit.cc',
                                        'link'       => $this->baseUrl . '?search=' . urlencode($term), // Direct link to search result
                                    ];
                                }
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'LearnSanskritScraper search error: ' . $e->getMessage());
        }

        return $results;
    }
}

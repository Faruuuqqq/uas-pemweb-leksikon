<?php namespace App\Libraries;

use CodeIgniter\HTTP\CURLRequest;

class KoelnLexiconAPI
{
    private $baseUrl = 'https://api.c-salt.uni-koeln.de/dicts/mw/restful';
    private $client;

    public function __construct()
    {
        $this->client = \Config\Services::curlrequest([
            'baseURI' => $this->baseUrl,
            'timeout' => 5, // 5 seconds timeout
        ]);
    }

    /**
     * Searches the Monier Williams dictionary API.
     *
     * @param string $term The search term.
     * @param int $limit The maximum number of results to return.
     * @param int $offset The starting point for the results (for pagination).
     * @return array An array of search results, or an empty array on failure.
     */
    public function search(string $term, int $limit = 10, int $offset = 0): array
    {
        try {
            $response = $this->client->get('entries', [
                'query' => [
                    'q'      => $term,
                    'limit'  => $limit,
                    'offset' => $offset,
                ],
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                $body = $response->getBody();
                $data = json_decode($body, true);

                // Assuming the API returns an array of entries directly
                // or an object with an 'entries' key.
                if (is_array($data)) {
                    return $this->formatResults($data);
                } elseif (isset($data['entries']) && is_array($data['entries'])) {
                    return $this->formatResults($data['entries']);
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'KoelnLexiconAPI search error: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Formats the raw API results into a consistent structure.
     *
     * @param array $rawResults The raw results from the API.
     * @return array Formatted results.
     */
    private function formatResults(array $rawResults): array
    {
        $formatted = [];
        foreach ($rawResults as $item) {
            // This is a placeholder. The actual structure of the API response
            // needs to be confirmed by making a test call and inspecting the response.
            // For now, assuming 'headword' and 'meaning' are common fields.
            $formatted[] = [
                'word'       => $item['headword'] ?? 'N/A',
                'definition' => $item['meaning'] ?? 'No definition provided.',
                'source'     => 'sanskrit-lexicon.uni-koeln.de',
                'link'       => $this->getEntryLink($item['id'] ?? null), // Assuming 'id' can construct a direct link
            ];
        }
        return $formatted;
    }

    /**
     * Attempts to construct a direct link to an entry if an ID is available.
     * This is highly speculative without knowing the actual website structure for direct links.
     *
     * @param string|null $id The entry ID.
     * @return string|null A potential link to the entry.
     */
    private function getEntryLink(?string $id): ?string
    {
        if ($id) {
            // This is a placeholder. A real direct link would likely look different.
            // Example based on the previous Leksikon::externalSearch which had:
            // 'https://sanskrit-lexicon.uni-koeln.de/scans/SCHResult/index.php?filter=PED_hc&input=' . $encoded_query . '&view=3',
            // It might require a term directly or a specific entry ID path.
            return "https://sanskrit-lexicon.uni-koeln.de/scans/MWScan/2020/web/webtc/servepdf.php?key=$id";
        }
        return null;
    }
}

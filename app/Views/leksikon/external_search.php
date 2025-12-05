<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>External Lexicon Search Portal</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .search-section {
            margin-bottom: 20px;
            text-align: center;
        }
        .search-section input[type="text"] {
            width: 70%;
            padding: 10px;
            margin-right: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .search-section button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .search-section button:hover {
            background-color: #0056b3;
        }
        .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .result-card {
            background-color: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .result-card h3 {
            margin-top: 0;
            color: #0056b3;
        }
        .result-card .loading-indicator {
            text-align: center;
            font-style: italic;
            color: #666;
        }
        .error-message {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>External Lexicon Search Portal</h1>

        <div class="search-section">
            <input type="text" id="searchInput" placeholder="Enter Sanskrit word...">
            <button id="searchButton">Search</button>
        </div>

        <div id="loading" class="loading-indicator" style="display: none;">Searching...</div>
        <div id="errorMessage" class="error-message" style="display: none;"></div>

        <div class="results-grid">
            <div class="result-card">
                <h3>sanskrit-lexicon.uni-koeln.de</h3>
                <div id="koelnResults">
                    <p class="loading-indicator">Results will appear here.</p>
                </div>
            </div>

            <div class="result-card">
                <h3>learnsanskrit.cc</h3>
                <div id="learnsanskritResults">
                    <p class="loading-indicator">Results will appear here.</p>
                </div>
            </div>

            <div class="result-card">
                <h3>sealang.net/library/</h3>
                <div id="sealangLibraryResults">
                    <p class="loading-indicator">Results will appear here.</p>
                </div>
            </div>

            <div class="result-card">
                <h3>sealang.net/ojed/</h3>
                <div id="sealangOjedResults">
                    <p class="loading-indicator">Results will appear here.</p>
                </div>
            </div>

            <div class="result-card">
                <h3>sastra.org/leksikon</h3>
                <div id="sastraResults">
                    <p class="loading-indicator">Results will appear here.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="/assets/js/script.js"></script>
    <script>
        // Placeholder for future JavaScript for this page
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const searchButton = document.getElementById('searchButton');
            const loadingIndicator = document.getElementById('loading');
            const errorMessage = document.getElementById('errorMessage');

            // Result containers
            const koelnResults = document.getElementById('koelnResults');
            const learnsanskritResults = document.getElementById('learnsanskritResults');
            const sealangLibraryResults = document.getElementById('sealangLibraryResults');
            const sealangOjedResults = document.getElementById('sealangOjedResults');
            const sastraResults = document.getElementById('sastraResults');

            searchButton.addEventListener('click', performSearch);
            searchInput.addEventListener('keypress', function(event) {
                if (event.key === 'Enter') {
                    performSearch();
                }
            });

            async function performSearch() {
                const query = searchInput.value.trim();
                if (!query) {
                    alert('Please enter a search term.');
                    return;
                }

                // Clear previous results and show loading indicator
                koelnResults.innerHTML = '<p class="loading-indicator">Searching...</p>';
                learnsanskritResults.innerHTML = '<p class="loading-indicator">Searching...</p>';
                sealangLibraryResults.innerHTML = '<p class="loading-indicator">Searching...</p>';
                sealangOjedResults.innerHTML = '<p class="loading-indicator">Searching...</p>';
                sastraResults.innerHTML = '<p class="loading-indicator">Searching...</p>';
                loadingIndicator.style.display = 'block';
                errorMessage.style.display = 'none';

                // Disable search button and show loading state
                searchButton.disabled = true;
                searchButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Searching...';

                try {
                    const response = await fetch(`/leksikon/externalSearch/query?q=${encodeURIComponent(query)}`);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    const data = await response.json();

                    loadingIndicator.style.display = 'none';
                    displayResults(data);

                } catch (error) {
                    console.error('Search failed:', error);
                    loadingIndicator.style.display = 'none';
                    errorMessage.textContent = 'Failed to fetch search results. Please try again.';
                    errorMessage.style.display = 'block';
                    // Also update individual result cards to show error
                    koelnResults.innerHTML = '<p class="error-message">Error loading results.</p>';
                    learnsanskritResults.innerHTML = '<p class="error-message">Error loading results.</p>';
                    sealangLibraryResults.innerHTML = '<p class="error-message">Error loading results.</p>';
                    sealangOjedResults.innerHTML = '<p class="error-message">Error loading results.</p>';
                    sastraResults.innerHTML = '<p class="error-message">Error loading results.</p>';
                } finally {
                    // Re-enable search button
                    searchButton.disabled = false;
                    searchButton.innerHTML = 'Search'; // Restore original text
                }
            }

            function displayResults(data) {
                // Example: populate results. This will need to be refined based on actual backend response structure
                // Assuming data is an object with keys like 'koeln', 'learnsanskrit', etc., each containing an array of results or a status message.

                function renderSiteResults(container, siteData, siteName) {
                    container.innerHTML = ''; // Clear existing content
                    if (siteData && siteData.length > 0) {
                        siteData.forEach(item => {
                            const resultDiv = document.createElement('div');
                            resultDiv.classList.add('search-result-item'); // Add a class for potential styling
                            // This structure needs to be adapted based on the actual result format from each site
                            resultDiv.innerHTML = `
                                <h4>${item.word || 'N/A'}</h4>
                                <p>${item.definition || 'No definition available.'}</p>
                                ${item.link ? `<p><a href="${item.link}" target="_blank">View on ${siteName}</a></p>` : ''}
                            `;
                            container.appendChild(resultDiv);
                        });
                    } else {
                        container.innerHTML = '<p>No results found.</p>';
                    }
                }

                if (data.koeln) renderSiteResults(koelnResults, data.koeln, 'sanskrit-lexicon.uni-koeln.de');
                if (data.learnsanskrit) renderSiteResults(learnsanskritResults, data.learnsanskrit, 'learnsanskrit.cc');
                if (data.sealangLibrary) renderSiteResults(sealangLibraryResults, data.sealangLibrary, 'sealang.net/library/');
                if (data.sealangOjed) renderSiteResults(sealangOjedResults, data.sealangOjed, 'sealang.net/ojed/');
                if (data.sastra) renderSiteResults(sastraResults, data.sastra, 'sastra.org/leksikon');
            }
        });
    </script>
</body>
</html>
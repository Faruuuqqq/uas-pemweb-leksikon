document.addEventListener("DOMContentLoaded", () => {

    // --- PENCARIAN INSTAN ---
    const searchInput = document.getElementById("search-input");
    const resultsContainer = document.getElementById("search-results-container");
    const mainList = document.getElementById("main-entry-list"); // Used for hiding/showing main list

    if (searchInput) {
        searchInput.addEventListener("keyup", async (e) => {
            const query = e.target.value.trim();

            if (query.length < 1) {
                resultsContainer.innerHTML = "";
                if (mainList) mainList.style.display = "block";
                return;
            }

            if (mainList) mainList.style.display = "none";
            
            try {
                // Adjust URL for CodeIgniter's Leksikon::search() method
                const response = await fetch(`${CI_SITE_URL}leksikon/search?q=${query}`);
                const results = await response.json();

                resultsContainer.innerHTML = ""; // Kosongkan

                if (results.length > 0 && !results.error) {
                    results.forEach(item => {
                        resultsContainer.innerHTML += `
                            <a href="${CI_SITE_URL}leksikon/detail/${item.id}" class="list-group-item list-group-item-action">
                                <strong>${item.term}</strong><br>
                                <small class="text-muted">${item.definition.substring(0, 75)}...</small>
                            </a>
                        `;
                    });
                } else {
                    resultsContainer.innerHTML = `<span class="list-group-item text-center p-3">Tidak ada hasil ditemukan.</span>`;
                }
            } catch (err) {
                console.error("Error fetching search:", err);
                resultsContainer.innerHTML = `<span class="list-group-item text-danger">Gagal memuat hasil.</span>`;
            }
        });

        // Sembunyikan hasil jika klik di luar
        document.addEventListener('click', (e) => {
            if (e.target.id !== 'search-input' && !searchInput.contains(e.target)) {
                resultsContainer.innerHTML = '';
                if (mainList) mainList.style.display = "block";
            }
        });
    }

    // --- SISTEM FAVORIT (SERVER-SIDE) ---

    // Function to handle toggling favorite status via AJAX
    const toggleFavoriteServerSide = async (entriId, favoriteBtnElement) => {
        try {
            const response = await fetch(`${CI_SITE_URL}leksikon/toggle_favorite/${entriId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest', // Important for CI4's isAJAX()
                    [CI_CSRF_HEADER]: CI_CSRF_TOKEN // Send CSRF token in header
                },
                body: JSON.stringify({}) // Empty body might be needed for POST
            });
            const result = await response.json();

            if (result.status === 'success') {
                if (result.action === 'added') {
                    favoriteBtnElement.classList.remove('bi-star');
                    favoriteBtnElement.classList.add('bi-star-fill', 'text-warning');
                    favoriteBtnElement.setAttribute('title', 'Hapus dari Favorit');
                } else {
                    favoriteBtnElement.classList.remove('bi-star-fill', 'text-warning');
                    favoriteBtnElement.classList.add('bi-star');
                    favoriteBtnElement.setAttribute('title', 'Tambah ke Favorit');
                }
                // Optional: show a toast/alert message
                console.log(result.message);
                loadFavoritesListServerSide(); // Reload sidebar favorites
            } else {
                alert(result.message); // Show error message (e.g., "Anda harus login")
                if (result.message.includes('login')) { // Simple check for login requirement
                    window.location.href = `${CI_SITE_URL}login`;
                }
            }
        } catch (error) {
            console.error("Error toggling favorite:", error);
            alert("Gagal mengubah status favorit. Silakan coba lagi.");
        }
    };

    // Function to load and display server-side favorites in the sidebar
    const loadFavoritesListServerSide = async () => {
        const listEl = document.getElementById("favorites-list");
        if (!listEl) return;
        
        // No need to pass IDs, server will get user ID from session
        try {
            const response = await fetch(`${CI_SITE_URL}leksikon/get_favorites`);
            const result = await response.json();

            if (result.status === 'success') {
                const items = result.data; // Server now returns data within 'data' key

                listEl.innerHTML = ""; // Clear existing list

                if (items.length > 0) {
                    items.forEach(item => {
                        listEl.innerHTML += `
                            <a href="${CI_SITE_URL}leksikon/detail/${item.id}" class="list-group-item list-group-item-action py-2">
                                ${item.term}
                            </a>
                        `;
                    });
                } else {
                    listEl.innerHTML = `<li class="list-group-item text-muted small">Belum ada favorit.</li>`;
                }
            } else {
                 listEl.innerHTML = `<li class="list-group-item text-danger small">${result.message}</li>`;
            }
        } catch (e) {
            console.error("Gagal memuat favorit:", e);
            listEl.innerHTML = `<li class="list-group-item text-danger small">Gagal memuat.</li>`;
        }
    };

    // Event listener for favorite buttons (both detail and list pages)
    document.body.addEventListener('click', (e) => {
        // Target buttons with either 'favorite-btn' (detail page) or 'favorite-btn-list' (index page)
        if (e.target.classList.contains('favorite-btn') || e.target.classList.contains('favorite-btn-list')) {
            e.preventDefault(); // Prevent default link behavior if applicable
            const id = e.target.dataset.id;
            if (id) {
                toggleFavoriteServerSide(id, e.target);
            }
        }
    });

    // Initial load for sidebar favorites
    loadFavoritesListServerSide();

});
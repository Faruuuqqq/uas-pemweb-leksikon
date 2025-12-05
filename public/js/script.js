document.addEventListener("DOMContentLoaded", () => {

    // --- PENCARIAN INSTAN ---
    const searchInput = document.getElementById("search-input");
    const resultsContainer = document.getElementById("search-results-container");
    const mainList = document.getElementById("main-entry-list");
    
    // Get base URL from a meta tag or a global JS variable if needed, for now we assume relative paths work
    const siteUrl = (path) => {
        // In a real CI setup, you might pass the base URL from PHP
        // For example: <script>const BASE_URL = '<?= site_url() ?>';</script>
        // For now, we'll just use relative paths.
        const baseUrl = window.location.origin;
        return `${baseUrl}${path}`;
    };


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
                // Use CodeIgniter route, e.g., /search
                const response = await fetch(siteUrl(`/leksikon/search?q=${query}`));
                const results = await response.json();

                resultsContainer.innerHTML = ""; // Kosongkan

                if (results.length > 0 && !results.error) {
                    results.forEach(item => {
                        resultsContainer.innerHTML += `
                            <a href="${siteUrl(`/leksikon/detail/${item.id}`)}" class="list-group-item list-group-item-action">
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

        document.addEventListener('click', (e) => {
            if (e.target.id !== 'search-input') {
                resultsContainer.innerHTML = '';
                if (mainList) mainList.style.display = "block";
            }
        });
    }

    // --- SISTEM FAVORIT (COOKIE) ---
    
    const setCookie = (name, value, days = 30) => {
        let expires = "";
        if (days) {
            const date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            expires = "; expires=" + date.toUTCString();
        }
        document.cookie = name + "=" + (JSON.stringify(value) || "") + expires + "; path=/; SameSite=Lax";
    };

    const getCookie = (name) => {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) {
                try {
                    return JSON.parse(c.substring(nameEQ.length, c.length));
                } catch (e) {
                    return [];
                }
            }
        }
        return [];
    };

    const toggleFavorite = (id) => {
        let favorites = getCookie("favorites");
        if (!Array.isArray(favorites)) favorites = [];
        const idStr = String(id);
        if (favorites.includes(idStr)) {
            favorites = favorites.filter(fId => fId !== idStr);
        } else {
            favorites.push(idStr);
        }
        setCookie("favorites", favorites);
    };

    const updateFavoriteIcons = () => {
        const favorites = getCookie("favorites");
        if (!Array.isArray(favorites)) return;
        
        document.querySelectorAll('.favorite-btn').forEach(btn => {
            const id = btn.dataset.id;
            if (favorites.includes(id)) {
                btn.classList.remove('bi-star');
                btn.classList.add('bi-star-fill', 'text-warning');
            } else {
                btn.classList.remove('bi-star-fill', 'text-warning');
                btn.classList.add('bi-star');
            }
        });
    };

    const loadFavoritesList = async () => {
        const listEl = document.getElementById("favorites-list");
        if (!listEl) return;
        
        const favorites = getCookie("favorites");
        if (!Array.isArray(favorites) || favorites.length === 0) {
            listEl.innerHTML = `<li class="list-group-item text-muted small">Belum ada favorit.</li>`;
            return;
        }

        try {
            // Use CodeIgniter route, e.g., /favorites
            const response = await fetch(siteUrl(`/leksikon/get_favorites?ids=${JSON.stringify(favorites)}`));
            const items = await response.json();

            if (items.length > 0) {
                listEl.innerHTML = "";
                items.forEach(item => {
                    listEl.innerHTML += `
                        <a href="${siteUrl(`/leksikon/detail/${item.id}`)}" class="list-group-item list-group-item-action py-2">
                            ${item.term}
                        </a>
                    `;
                });
            } else {
                listEl.innerHTML = `<li class="list-group-item text-muted small">Belum ada favorit.</li>`;
            }
        } catch (e) {
            console.error("Gagal memuat favorit:", e);
            listEl.innerHTML = `<li class="list-group-item text-danger small">Gagal memuat.</li>`;
        }
    };

    document.body.addEventListener('click', (e) => {
        if (e.target.classList.contains('favorite-btn')) {
            const id = e.target.dataset.id;
            toggleFavorite(id);
            updateFavoriteIcons();
            loadFavoritesList(); 
        }
    });

    updateFavoriteIcons();
    loadFavoritesList();
});

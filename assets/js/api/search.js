document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('desktop-search');
    const searchResults = document.getElementById('desktop-search-results');
    let timeout = null;

    if (!searchInput || !searchResults) return;

    searchInput.addEventListener('input', function() {
        const query = this.value; // Don't trim immediately to allow space typing

        clearTimeout(timeout);
        
        if (query.trim().length === 0) {
            searchResults.classList.add('hidden');
            searchResults.innerHTML = '';
            return;
        }

        timeout = setTimeout(() => {
            fetch(`/api/search.php?q=${encodeURIComponent(query.trim())}`)
                .then(response => response.json())
                .then(data => {
                    searchResults.innerHTML = '';
                    if (data.length > 0) {
                        data.forEach(book => {
                            const item = document.createElement('a');
                            item.href = `/page/user/detail.php?id=${book.id}`;
                            item.className = 'flex items-center gap-3 p-3 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors border-b last:border-0 border-gray-100 dark:border-gray-700';
                            
                            // Image
                            const imgContainer = document.createElement('div');
                            imgContainer.className = 'w-10 h-14 bg-gray-200 rounded overflow-hidden flex-shrink-0';
                            if(book.cover) {
                                imgContainer.innerHTML = `<img src="${book.cover}" class="w-full h-full object-cover">`;
                            } else {
                                imgContainer.className += ' flex items-center justify-center text-gray-400';
                                imgContainer.innerHTML = '<i class="fa-solid fa-book"></i>';
                            }

                            // Info
                            const info = document.createElement('div');
                            info.innerHTML = `
                                <div class="text-sm font-semibold text-gray-900 dark:text-gray-100 line-clamp-1">${book.title}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">oleh ${book.author} <span class="mx-1">•</span> ${book.genre}</div>
                            `;

                            item.appendChild(imgContainer);
                            item.appendChild(info);
                            searchResults.appendChild(item);
                        });
                        searchResults.classList.remove('hidden');
                    } else {
                        searchResults.innerHTML = `
                            <div class="p-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                Tidak ditemukan hasil untuk "${query}"
                            </div>
                        `;
                        searchResults.classList.remove('hidden');
                    }
                })
                .catch(err => {
                    console.error('Search error:', err);
                });
        }, 300); // Debounce 300ms
    });

    // Close on click outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });

    // Re-open if clicking input with value
    searchInput.addEventListener('focus', function() {
        if (this.value.trim().length > 0 && searchResults.children.length > 0) {
            searchResults.classList.remove('hidden');
        }
    });
});

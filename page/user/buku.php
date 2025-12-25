<?php
session_start();

require_once '../../database/koneksi.php';

// Pagination Setup
$per_page = 15; // Set items per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page > 1) ? ($page * $per_page) - $per_page : 0;

// Build Query Conditions
$conditions = ["books.status = 'published'"];

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($koneksi, $_GET['search']);
    $conditions[] = "(books.title LIKE '%$search%' OR users.name LIKE '%$search%' OR books.genre LIKE '%$search%')";
}

if (isset($_GET['genre']) && !empty($_GET['genre'])) {
    $genre_filter = mysqli_real_escape_string($koneksi, $_GET['genre']);
    $conditions[] = "books.genre LIKE '%$genre_filter%'";
}

$where_clause = implode(' AND ', $conditions);

// Get Total Count
$sql_count = "SELECT COUNT(*) as total FROM books 
              JOIN users ON books.user_id = users.id 
              WHERE $where_clause";
$result_count = mysqli_query($koneksi, $sql_count);
$total_rows = mysqli_fetch_assoc($result_count)['total'];
$total_pages = ceil($total_rows / $per_page);

// Get Data with Limit
$sql_query = "SELECT books.*, users.name as author_name 
            FROM books 
            JOIN users ON books.user_id = users.id 
            WHERE $where_clause 
            ORDER BY books.created_at DESC 
            LIMIT $start, $per_page";

$result = mysqli_query($koneksi, $sql_query);

if (isset($_GET['ajax']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')) {
    include 'partials/book_grid.php';
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jelajahi Buku - WebBuku</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 font-sans transition-colors duration-300">

    <?php include '../../layouts/navbar.php'; ?>

    <!-- Header Section -->
    <div class="pt-32 pb-12 bg-emerald-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl md:text-5xl font-bold mb-4">Pustaka Cerita</h1>
            <p class="text-emerald-100 max-w-2xl mx-auto">Temukan ribuan cerita menarik dari penulis berbakat di seluruh Indonesia.</p>
            
            <!-- Search Bar -->
            <form action="" method="GET" class="mt-8 max-w-xl mx-auto relative">
                <?php if(isset($_GET['genre'])): ?>
                    <input type="hidden" name="genre" value="<?php echo htmlspecialchars($_GET['genre']); ?>">
                <?php endif; ?>
                <input type="text" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" placeholder="Cari judul, penulis, atau genre..." class="w-full py-4 pl-6 pr-14 rounded-full text-gray-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/30 shadow-lg">
                <button type="submit" class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-emerald-600 p-2.5 rounded-full text-white hover:bg-emerald-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </form>
            
            <!-- Filters -->
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="buku.php<?php echo isset($_GET['search']) ? '?search='.urlencode($_GET['search']) : ''; ?>" class="px-4 py-2 <?php echo !isset($_GET['genre']) || $_GET['genre'] == '' ? 'bg-white text-emerald-800 shadow-md dark:bg-emerald-600 dark:text-white' : 'bg-emerald-800/50 hover:bg-emerald-700 text-white border border-emerald-700'; ?> rounded-full text-sm font-semibold transition-colors">Semua</a>
                
                <?php 
                $genres = ['Fantasi', 'Romantis', 'Horror', 'Sci-Fi', 'Misteri', 'Drama', 'Komedi', 'Petualangan', 'Sejarah', 'Thriller'];
                foreach ($genres as $genre) {
                    $isActive = isset($_GET['genre']) && $_GET['genre'] == $genre;
                    $searchParam = isset($_GET['search']) ? '&search='.urlencode($_GET['search']) : '';
                    $class = $isActive ? 'bg-white text-emerald-800 shadow-md dark:bg-emerald-600 dark:text-white' : 'bg-emerald-800/50 hover:bg-emerald-700 text-white border border-emerald-700';
                    echo '<a href="buku.php?genre='.$genre.$searchParam.'" class="px-4 py-2 '.$class.' rounded-full text-sm font-medium transition-colors">'.$genre.'</a>';
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Gallery Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-16">
            <!-- Book Items (From Database) -->
            
            <div id="book-grid-container">
                <?php include 'partials/book_grid.php'; ?>
            </div>
            
    </div>

    <script>
        // Skeleton Template
        const skeletonHTML = `
        <div class="grid grid-cols-3 lg:grid-cols-5 gap-3 md:gap-8 min-h-[400px]">
            ${Array(10).fill(0).map(() => `
            <div class="bg-white dark:bg-gray-800 rounded-lg md:rounded-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
                <div class="relative h-36 md:h-72 bg-gray-200 dark:bg-gray-700 animate-pulse"></div>
                <div class="p-2.5 md:p-5 space-y-2">
                    <div class="h-4 bg-gray-200 dark:bg-gray-700 rounded w-3/4 animate-pulse"></div>
                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-1/2 animate-pulse"></div>
                    <div class="pt-2 md:pt-4 border-t border-gray-100 dark:border-gray-700 flex justify-between">
                        <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-8 animate-pulse"></div>
                        <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-12 animate-pulse"></div>
                    </div>
                </div>
            </div>
            `).join('')}
        </div>
        `;

        const container = document.getElementById('book-grid-container');
        const form = document.querySelector('form');
        
        // Helper to update active states for genre
        function updateActiveGenre(url) {
            const params = new URLSearchParams(url.split('?')[1]);
            const currentGenre = params.get('genre') || 'Semua';
            
            // This is a simplified approach, ideally we select specific elements
            document.querySelectorAll('.flex-wrap a').forEach(el => {
                const href = el.getAttribute('href');
                const urlParams = new URLSearchParams(href.split('?')[1]);
                const elGenre = urlParams.get('genre') || 'Semua'; // Default to 'Semua' if no genre param

                // Check if this link corresponds to current genre
                // "Semua" link usually has no genre param or empty
                const isSemua = !params.has('genre') || params.get('genre') === '';
                const elIsSemua = !urlParams.has('genre') || urlParams.get('genre') === '';
                
                let matches = false;
                if(isSemua && elIsSemua) matches = true;
                else if (params.get('genre') === urlParams.get('genre')) matches = true;
                
                if (matches) {
                    el.className = 'px-4 py-2 bg-white text-emerald-800 shadow-md dark:bg-emerald-600 dark:text-white rounded-full text-sm font-semibold transition-colors';
                } else {
                    el.className = 'px-4 py-2 bg-emerald-800/50 hover:bg-emerald-700 text-white border border-emerald-700 rounded-full text-sm font-medium transition-colors';
                }
            });
        }

        async function fetchBooks(url, pushState = true) {
            // Show Skeleton
            container.innerHTML = skeletonHTML;
            
            try {
                // Ensure we add ajax=1
                const fetchUrl = new URL(url, document.baseURI);
                fetchUrl.searchParams.set('ajax', '1');

                const response = await fetch(fetchUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const html = await response.text();
                
                container.innerHTML = html;
                
                if (pushState) {
                    window.history.pushState({}, '', url);
                }
                
                updateActiveGenre(url);
                attachPaginationListeners();

            } catch (error) {
                console.error('Error fetching books:', error);
                container.innerHTML = '<div class="text-center py-8 text-red-500">Terjadi kesalahan saat memuat data.</div>';
            }
        }

        // Handle Search Submit
        if(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                const params = new URLSearchParams(formData);
                // Keep genre if exists in current URL but not in form (though form has hidden input usually)
                // Actually form has hidden genre input if set, so formData should have it.
                
                const url = 'buku.php?' + params.toString();
                fetchBooks(url);
            });
        }

        // Handle Genre Clicks
        document.querySelectorAll('.flex-wrap.justify-center.gap-3 a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                fetchBooks(this.href);
            });
        });

        // Handle Pagination Clicks (Dynamic content needs re-attaching or delegation)
        function attachPaginationListeners() {
            document.querySelectorAll('.pagination-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    fetchBooks(this.href);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                });
            });
        }

        // Initial Attach
        attachPaginationListeners();

        // Handle Back/Forward Browser Buttons
        window.addEventListener('popstate', function() {
            fetchBooks(window.location.href, false);
        });

    </script>


    <?php include '../../layouts/footer.php'; ?>

</body>
</html>

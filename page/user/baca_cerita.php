<?php
session_start();
require_once '../../database/koneksi.php';

if (!isset($_GET['chapter_id']) || !isset($_GET['book_id'])) {
    header('Location: buku.php');
    exit();
}

$book_id = mysqli_real_escape_string($koneksi, $_GET['book_id']);
$chapter_id = mysqli_real_escape_string($koneksi, $_GET['chapter_id']);

// Get Book Info
$query_book = "SELECT * FROM books WHERE id = '$book_id' AND status = 'published'";
$result_book = mysqli_query($koneksi, $query_book);
$book = mysqli_fetch_assoc($result_book);

if (!$book) {
    echo "Buku tidak ditemukan.";
    exit();
}

// Get Current Chapter
$query_chap = "SELECT * FROM chapters WHERE id = '$chapter_id' AND book_id = '$book_id' AND status = 'published'";
$result_chap = mysqli_query($koneksi, $query_chap);
$chapter = mysqli_fetch_assoc($result_chap);

if (!$chapter) {
    echo "Bab tidak ditemukan.";
    exit();
}

// Update View Count (Optional: Implement robust logic later)
mysqli_query($koneksi, "UPDATE books SET views = views + 1 WHERE id = '$book_id'");

// Update Reading History
if (isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $query_hist = "INSERT INTO reading_history (user_id, book_id, last_chapter_id) VALUES ('$uid', '$book_id', '$chapter_id')
                   ON DUPLICATE KEY UPDATE last_chapter_id = '$chapter_id', updated_at = NOW()";
    mysqli_query($koneksi, $query_hist);
}

// Get Navigation (Prev/Next)
$query_all = "SELECT id FROM chapters WHERE book_id = '$book_id' AND status = 'published' ORDER BY created_at ASC";
$result_all = mysqli_query($koneksi, $query_all);
$all_chapters = [];
while ($row = mysqli_fetch_assoc($result_all)) {
    $all_chapters[] = $row['id'];
}

$current_index = array_search($chapter_id, $all_chapters);
$prev_id = ($current_index > 0) ? $all_chapters[$current_index - 1] : null;
$next_id = ($current_index < count($all_chapters) - 1) ? $all_chapters[$current_index + 1] : null;

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($chapter['title']); ?> - <?php echo htmlspecialchars($book['title']); ?></title>
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        serif: ['"Merriweather"', 'serif'],
                    },
                    colors: {
                        emerald: {
                            600: '#059669',
                            700: '#047857',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .prose p { margin-bottom: 1.5em; line-height: 1.8; transition: font-size 0.3s ease; text-align: justify; }
        .theme-sepia body { background-color: #f4ecd8; color: #5b4636; }
        .theme-sepia header { background-color: rgba(244, 236, 216, 0.95); border-color: #eaddc5; }
        .theme-dark body { background-color: #111827; color: #d1d5db; }
        .theme-dark header { background-color: rgba(17, 24, 39, 0.95); border-color: #374151; }
        .theme-dark .text-gray-900 { color: #f3f4f6; }
        .theme-dark .bg-white { background-color: #1f2937; border-color: #374151; }
        /* Override prose colors for specific themes if needed */
        .theme-sepia .prose { color: #433422; }
        .theme-dark .prose { color: #d1d5db; }
        .theme-dark .prose h2, .theme-dark .prose strong { color: #f3f4f6; }
    </style>
</head>
<body class="bg-[#f9f9f9] text-gray-800 font-sans antialiased">

    <!-- Sticky Header -->
    <header class="sticky top-0 bg-white/95 backdrop-blur-sm border-b border-gray-200 z-50 shadow-sm transition-all duration-300" id="header">
        <div class="max-w-4xl mx-auto px-4 h-16 flex items-center justify-between">
            <a href="detail.php?id=<?php echo $book_id; ?>" class="flex items-center text-gray-500 hover:text-emerald-600 transition-colors">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                <span class="hidden sm:inline font-medium">Daftar Bab</span>
            </a>
            
            <div class="text-center">
                <h1 class="text-sm font-bold text-gray-900 line-clamp-1"><?php echo htmlspecialchars($book['title']); ?></h1>
                <p class="text-xs text-gray-500"><?php echo htmlspecialchars($chapter['title']); ?></p>
            </div>

            <div class="flex items-center gap-2">
            <div class="flex items-center gap-2 relative">
                 <!-- Settings Toggle -->
                <button id="settings-btn" class="p-2 text-gray-500 hover:text-emerald-600 transition-colors rounded-lg hover:bg-gray-100">
                    <i class="fa-solid fa-sliders text-lg"></i>
                </button>
                
                <!-- Settings Dropdown -->
                <div id="settings-dropdown" class="absolute top-full right-0 mt-2 w-64 bg-white rounded-xl shadow-xl border border-gray-100 p-4 hidden transform transition-all origin-top-right z-50">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Tampilan Baca</h3>
                    
                    <!-- Font Size -->
                    <div class="mb-4">
                        <label class="block text-xs text-gray-500 mb-2">Ukuran Font</label>
                        <div class="flex bg-gray-100 rounded-lg p-1">
                            <button onclick="changeFontSize('small')" class="flex-1 py-1 text-sm rounded-md hover:bg-white hover:shadow-sm transition-all">Aa</button>
                            <button onclick="changeFontSize('medium')" class="flex-1 py-1 text-base font-medium rounded-md hover:bg-white hover:shadow-sm transition-all">Aa</button>
                            <button onclick="changeFontSize('large')" class="flex-1 py-1 text-lg font-bold rounded-md hover:bg-white hover:shadow-sm transition-all">Aa</button>
                        </div>
                    </div>
                    
                    <!-- Theme -->
                    <div>
                        <label class="block text-xs text-gray-500 mb-2">Tema</label>
                        <div class="flex gap-2">
                            <button onclick="changeTheme('light')" class="flex-1 h-8 rounded-full bg-white border border-gray-200 shadow-sm" title="Terang"></button>
                            <button onclick="changeTheme('sepia')" class="flex-1 h-8 rounded-full bg-[#f4ecd8] border border-[#eaddc5]" title="Sepia"></button>
                            <button onclick="changeTheme('dark')" class="flex-1 h-8 rounded-full bg-gray-900 border border-gray-700" title="Gelap"></button>
                        </div>
                    </div>
                </div>
            </div>
                <!-- Bookmark Manual Toggle -->
                <button id="bookmark-btn" class="p-2 text-gray-400 hover:text-emerald-600 transition-colors rounded-lg hover:bg-gray-100 relative group" title="Tandai Bab Ini">
                    <i class="fa-regular fa-bookmark text-lg" id="bookmark-icon"></i>
                    <span class="absolute top-full right-0 mt-2 bg-gray-800 text-white text-[10px] px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap pointer-events-none">Tandai Bab</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Reading Area -->
    <main class="max-w-3xl mx-auto px-6 py-12 md:py-16">
        <div class="prose mx-auto font-serif text-gray-800">
            <h2 class="text-3xl md:text-4xl font-bold text-center mb-12 font-sans text-gray-900"><?php echo htmlspecialchars($chapter['title']); ?></h2>
            
            <div class="content-body">
                <?php echo $chapter['content']; ?>
            </div>
        </div>
    </main>

    <!-- Navigation Footer -->
    <footer class="max-w-4xl mx-auto px-6 py-12 border-t border-gray-200">
        <div class="flex justify-between items-center">
            <?php if ($prev_id): ?>
            <a href="baca_cerita.php?book_id=<?php echo $book_id; ?>&chapter_id=<?php echo $prev_id; ?>" class="flex items-center px-6 py-3 bg-white border border-gray-200 rounded-full hover:bg-emerald-50 hover:border-emerald-200 hover:text-emerald-700 transition-all font-medium shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Bab Sebelumnya
            </a>
            <?php else: ?>
            <div></div> <!-- Spacer -->
            <?php endif; ?>

            <?php if ($next_id): ?>
            <a href="baca_cerita.php?book_id=<?php echo $book_id; ?>&chapter_id=<?php echo $next_id; ?>" class="flex items-center px-6 py-3 bg-emerald-600 text-white rounded-full hover:bg-emerald-700 hover:shadow-lg transition-all font-bold shadow-md">
                Bab Berikutnya
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
            </a>
            <?php else: ?>
            <a href="detail.php?id=<?php echo $book_id; ?>" class="flex items-center px-6 py-3 bg-gray-900 text-white rounded-full hover:bg-gray-800 transition-all font-bold shadow-md">
                Selesai Membaca
                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </a>
            <?php endif; ?>
        </div>
    </footer>

    <script>
        const settingsBtn = document.getElementById('settings-btn');
        const settingsDropdown = document.getElementById('settings-dropdown');
        const contentBody = document.querySelector('.prose');

        // Toggle Dropdown
        settingsBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            settingsDropdown.classList.toggle('hidden');
        });

        // Close when clicking outside
        document.addEventListener('click', (e) => {
            if (!settingsDropdown.contains(e.target) && !settingsBtn.contains(e.target)) {
                settingsDropdown.classList.add('hidden');
            }
        });

        // Change Font Size
        function changeFontSize(size) {
            contentBody.classList.remove('prose-sm', 'prose-base', 'prose-lg', 'prose-xl', 'prose-2xl');
            if (size === 'small') contentBody.classList.add('prose-base');
            if (size === 'medium') contentBody.classList.add('prose-lg');
            if (size === 'large') contentBody.classList.add('prose-xl');
            localStorage.setItem('reader-font-size', size);
        }

        // Change Theme
        function changeTheme(theme) {
            document.documentElement.classList.remove('theme-light', 'theme-sepia', 'theme-dark');
            document.documentElement.classList.add('theme-' + theme);
            
            // Toggle prose-invert for dark mode
            if(theme === 'dark') {
                contentBody.classList.add('prose-invert');
            } else {
                contentBody.classList.remove('prose-invert');
            }

            localStorage.setItem('reader-theme', theme);
        }

        // Manual Bookmark Logic
        const bookmarkBtn = document.getElementById('bookmark-btn');
        const bookmarkIcon = document.getElementById('bookmark-icon');
        const bookId = <?php echo $book_id; ?>;
        const chapterId = <?php echo $chapter_id; ?>;

        async function checkBookmarkStatus() {
            try {
                const res = await fetch('../../api/toggle_bookmark_chapter.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({book_id: bookId, chapter_id: chapterId, action: 'check'})
                });
                const data = await res.json();
                if(data.state === 'exists') {
                    setBookmarkActive(true);
                }
            } catch(e) { console.error(e); }
        }

        function setBookmarkActive(isActive) {
            if(isActive) {
                bookmarkIcon.classList.remove('fa-regular', 'text-gray-400');
                bookmarkIcon.classList.add('fa-solid', 'text-emerald-600');
                bookmarkBtn.classList.add('bg-emerald-50');
            } else {
                bookmarkIcon.classList.remove('fa-solid', 'text-emerald-600');
                bookmarkIcon.classList.add('fa-regular', 'text-gray-400');
                bookmarkBtn.classList.remove('bg-emerald-50');
            }
        }

        bookmarkBtn.addEventListener('click', async () => {
            const isActive = bookmarkIcon.classList.contains('fa-solid');
            const action = isActive ? 'remove' : 'add';
            
            try {
                const res = await fetch('../../api/toggle_bookmark_chapter.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({book_id: bookId, chapter_id: chapterId, action: action})
                });
                const data = await res.json();
                if(data.status === 'success') {
                    setBookmarkActive(action === 'add');
                }
            } catch(e) { console.error(e); }
        });

        // Load Preferences & Bookmark Status
        document.addEventListener('DOMContentLoaded', () => {
            const savedFont = localStorage.getItem('reader-font-size') || 'medium';
            const savedTheme = localStorage.getItem('reader-theme') || 'light';
            changeFontSize(savedFont);
            changeTheme(savedTheme);
            checkBookmarkStatus();
        });
    </script>

</body>
</html>

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
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
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
        .prose p { margin-bottom: 1.5em; line-height: 1.8; }
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
                 <!-- Font Size Toggle (Simulated) -->
                <button class="p-2 text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                </button>
            </div>
        </div>
    </header>

    <!-- Reading Area -->
    <main class="max-w-3xl mx-auto px-6 py-12 md:py-16">
        <div class="prose prose-lg md:prose-xl mx-auto font-serif text-gray-800">
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

</body>
</html>

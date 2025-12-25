<?php
session_start();
require_once '../../database/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/auth_google.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch Bookmarks
$query = "SELECT books.*, users.name as author_name, bookmarks.created_at as bookmarked_at, 
          reading_history.last_chapter_id, chapters.title as last_chapter_title,
          cb.chapter_id as manual_chapter_id, cbm.title as manual_chapter_title
          FROM bookmarks 
          JOIN books ON bookmarks.book_id = books.id 
          JOIN users ON books.user_id = users.id 
          LEFT JOIN reading_history ON books.id = reading_history.book_id AND reading_history.user_id = $user_id
          LEFT JOIN chapters ON reading_history.last_chapter_id = chapters.id
          LEFT JOIN chapter_bookmarks cb ON books.id = cb.book_id AND cb.user_id = $user_id
          LEFT JOIN chapters cbm ON cb.chapter_id = cbm.id
          WHERE bookmarks.user_id = $user_id 
          ORDER BY bookmarks.created_at DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pustaka Saya - WebBuku</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: { bg: '#0f172a', card: '#1e293b', text: '#f1f5f9' }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 dark:bg-dark-bg text-gray-800 dark:text-dark-text font-[Poppins]">
    <?php include '../../layouts/navbar.php'; ?>

    <main class="pt-24 pb-12 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h1 class="text-2xl font-bold dark:text-white">Pustaka Saya <i class="fa-solid fa-bookmark text-emerald-600 ml-2"></i></h1>
                <span class="text-sm text-gray-500 dark:text-gray-400"><?php echo mysqli_num_rows($result); ?> Buku Disimpan</span>
            </div>

            <?php if (mysqli_num_rows($result) > 0): ?>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
                    <?php while($book = mysqli_fetch_assoc($result)): ?>
                    <div class="bg-white dark:bg-dark-card rounded-xl shadow-sm hover:shadow-lg transition-all border border-gray-100 dark:border-gray-700 overflow-hidden group">
                        <div class="relative h-48 sm:h-64 bg-gray-200 overflow-hidden">
                            <a href="detail.php?id=<?php echo $book['id']; ?>">
                                <?php if($book['cover_image']): ?>
                                <img src="<?php echo (strpos($book['cover_image'], 'http') === 0) ? $book['cover_image'] : '/' . ltrim($book['cover_image'], '/'); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <?php else: ?>
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <i class="fa-solid fa-book text-4xl"></i>
                                </div>
                                <?php endif; ?>
                            </a>
                            <?php if($book['manual_chapter_title']): ?>
                            <div class="absolute bottom-0 left-0 right-0 bg-emerald-900/80 backdrop-blur-sm p-2 border-t border-emerald-500/30">
                                <p class="text-[10px] text-white font-medium truncate">
                                    <i class="fa-solid fa-bookmark text-emerald-400 mr-1"></i> 
                                    Ditandai: <?php echo htmlspecialchars($book['manual_chapter_title']); ?>
                                </p>
                            </div>
                            <?php elseif($book['last_chapter_title']): ?>
                            <div class="absolute bottom-0 left-0 right-0 bg-black/60 backdrop-blur-sm p-2">
                                <p class="text-[10px] text-white font-medium truncate">
                                    <i class="fa-regular fa-clock text-gray-300 mr-1"></i> 
                                    Terakhir: <?php echo htmlspecialchars($book['last_chapter_title']); ?>
                                </p>
                            </div>
                            <?php endif; ?>
                            <button onclick="removeBookmark(event, <?php echo $book['id']; ?>, this)" class="absolute top-2 right-2 bg-red-500 text-white p-2 rounded-full shadow-lg hover:bg-red-600 transition-colors z-10" title="Hapus dari Pustaka">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </button>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-gray-900 dark:text-white mb-1 line-clamp-1 text-sm md:text-base">
                                <a href="detail.php?id=<?php echo $book['id']; ?>" class="hover:text-emerald-600 transition-colors">
                                    <?php echo htmlspecialchars($book['title']); ?>
                                </a>
                            </h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">oleh <?php echo htmlspecialchars($book['author_name']); ?></p>
                            <a href="detail.php?id=<?php echo $book['id']; ?>" class="block w-full text-center py-2 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-600 dark:text-emerald-400 rounded-lg text-xs font-semibold hover:bg-emerald-600 hover:text-white dark:hover:bg-emerald-600 dark:hover:text-white transition-all">
                                Baca Sekarang
                            </a>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-20 bg-white dark:bg-dark-card rounded-2xl border border-gray-100 dark:border-gray-700">
                    <div class="mb-4 text-emerald-100 dark:text-emerald-900/30">
                        <i class="fa-solid fa-bookmark text-6xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Pustaka Masih Kosong</h2>
                    <p class="text-gray-500 dark:text-gray-400 mb-6">Simpan cerita menarik yang kamu temukan untuk dibaca nanti.</p>
                    <a href="buku.php" class="inline-block px-6 py-3 bg-emerald-600 text-white rounded-xl font-medium hover:bg-emerald-700 transition-colors shadow-lg shadow-emerald-600/20">
                        Jelajahi Buku
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../../layouts/footer.php'; ?>

    <script>
        function removeBookmark(e, bookId, btn) {
            e.preventDefault();
            if(!confirm('Hapus buku ini dari Pustaka Saya?')) return;

            fetch('/api/bookmark.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({book_id: bookId})
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    // Remove card element
                    const card = btn.closest('.group');
                    card.classList.add('opacity-0', 'scale-95');
                    setTimeout(() => {
                        card.remove();
                        // Reload if empty to show empty state
                        if(document.querySelectorAll('.group').length === 0) location.reload();
                    }, 300);
                } else {
                    alert('Gagal menghapus bookmark');
                }
            });
        }
    </script>
</body>
</html>

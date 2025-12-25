<?php
session_start();

require_once '../../database/koneksi.php';

if (!isset($_GET['id'])) {
    header('Location: buku.php');
    exit();
}

$book_id = mysqli_real_escape_string($koneksi, $_GET['id']);

// Get Book Details
$query = "SELECT books.*, users.name as author_name, users.bio as author_bio 
          FROM books 
          JOIN users ON books.user_id = users.id 
          WHERE books.id = '$book_id' AND books.status = 'published'";
$result = mysqli_query($koneksi, $query);
$book = mysqli_fetch_assoc($result);

if (!$book) {
    echo "Buku tidak ditemukan atau belum dipublikasikan.";
    exit();
}

// Get Chapters
$query_chapters = "SELECT * FROM chapters WHERE book_id = '$book_id' AND status = 'published' ORDER BY created_at ASC";
$result_chapters = mysqli_query($koneksi, $query_chapters);
$chapters = [];
while ($row = mysqli_fetch_assoc($result_chapters)) {
    $chapters[] = $row;
}

// Get First Chapter ID for "Start Reading"
$first_chapter_id = !empty($chapters) ? $chapters[0]['id'] : null;

// Check Follow Status
$is_following = false;
$is_own_book = false;
if (isset($_SESSION['user_id'])) {
    $current_user_id = $_SESSION['user_id'];
    $author_id = $book['user_id'];
    
    if ($current_user_id == $author_id) {
        $is_own_book = true;
    } else {
        $check_follow = mysqli_query($koneksi, "SELECT id FROM follows WHERE follower_id = '$current_user_id' AND following_id = '$author_id'");
        if (mysqli_num_rows($check_follow) > 0) {
            $is_following = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($book['title']); ?> - WebBuku</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        emerald: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .book-cover-shadow {
            box-shadow: 0 20px 40px -8px rgba(0, 0, 0, 0.4);
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans">

    <?php include '../../layouts/navbar.php'; ?>

    <!-- Hero Section with Blurred Background -->
    <div class="relative w-full bg-gray-900 overflow-hidden">
        <!-- Blurred Background -->
        <?php if($book['cover_image']): ?>
        <div class="absolute inset-0 bg-cover bg-center blur-lg scale-110 opacity-90" style="background-image: url('../../<?php echo htmlspecialchars($book['cover_image']); ?>');"></div>
        <?php else: ?>
        <div class="absolute inset-0 bg-emerald-900 opacity-40"></div>
        <?php endif; ?>
        <!-- Simple dark overlay -->
        <div class="absolute inset-0 bg-gray-900/60"></div>
        
        <!-- Content -->
        <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 pt-24 pb-8 sm:pt-28 sm:pb-12">
            
            <!-- Mobile: Centered Stacked / Desktop: Side by Side -->
            <div class="flex flex-col sm:flex-row gap-6 sm:gap-8 items-center sm:items-end">
                
                <!-- Book Cover - Centered on mobile -->
                <div class="flex-shrink-0">
                    <div class="w-36 sm:w-44 lg:w-52 aspect-[4/5] rounded-lg overflow-hidden bg-gray-700 book-cover-shadow">
                        <?php if($book['cover_image']): ?>
                            <img src="../../<?php echo htmlspecialchars($book['cover_image']); ?>" alt="Cover" class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-10 h-10 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                <span class="text-xs">No Cover</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Book Info - Centered text on mobile, left on desktop -->
                <div class="flex-1 text-center sm:text-left pb-2">
                    <!-- Genre Badge -->
                    <span class="inline-block bg-emerald-600 text-white text-xs px-2.5 py-1 rounded font-semibold uppercase tracking-wide mb-3"><?php echo htmlspecialchars($book['genre']); ?></span>
                    
                    <!-- Title -->
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-2 leading-tight"><?php echo htmlspecialchars($book['title']); ?></h1>
                    
                    <!-- Author -->
                        oleh <a href="profile.php?id=<?php echo $book['user_id']; ?>" class="text-white font-medium hover:text-emerald-400 transition-colors"><?php echo htmlspecialchars($book['author_name']); ?></a>
                    </p>
                    
                    <!-- Stats - Simple inline -->
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-x-4 gap-y-1 text-sm text-gray-400 mb-5">
                        <span><?php echo number_format($book['views']); ?> dibaca</span>
                        <span class="hidden sm:inline">•</span>
                        <span><?php echo count($chapters); ?> bab</span>
                        <span class="hidden sm:inline">•</span>
                        <span><?php echo date('d M Y', strtotime($book['created_at'])); ?></span>
                    </div>
                    
                    <!-- Action Button -->
                    <?php if($first_chapter_id): ?>
                    <a href="baca_cerita.php?book_id=<?php echo $book_id; ?>&chapter_id=<?php echo $first_chapter_id; ?>" class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-5 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"></path></svg>
                        Mulai Baca
                    </a>
                    <?php else: ?>
                    <button disabled class="inline-flex items-center justify-center gap-2 bg-gray-600 text-gray-400 font-semibold py-2.5 px-5 rounded-lg cursor-not-allowed">
                        Belum Ada Bab
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
        
        <!-- Back Button -->
        <div class="mb-6">
            <a href="buku.php" class="inline-flex items-center text-gray-500 hover:text-emerald-600 text-sm font-medium transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                Kembali ke Daftar Buku
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Main Content -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Synopsis -->
                <section>
                    <h2 class="text-lg font-bold text-gray-900 mb-3">Sinopsis</h2>
                    <p class="text-gray-600 leading-relaxed whitespace-pre-line"><?php echo htmlspecialchars($book['synopsis']); ?></p>
                </section>

                <!-- Chapters List -->
                <section>
                    <h2 class="text-lg font-bold text-gray-900 mb-3">
                        Daftar Bab
                        <span class="text-sm font-normal text-gray-400 ml-1">(<?php echo count($chapters); ?>)</span>
                    </h2>
                    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                        <?php if(count($chapters) > 0): ?>
                            <div class="divide-y divide-gray-100">
                            <?php $i = 1; foreach($chapters as $chap): ?>
                            <a href="baca_cerita.php?book_id=<?php echo $book_id; ?>&chapter_id=<?php echo $chap['id']; ?>" class="flex items-center gap-3 p-4 hover:bg-gray-50 transition-colors group">
                                <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-gray-100 text-gray-500 font-medium text-sm flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors"><?php echo $i++; ?></span>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-gray-800 group-hover:text-emerald-600 truncate transition-colors"><?php echo htmlspecialchars($chap['title']); ?></h4>
                                    <p class="text-xs text-gray-400 mt-0.5"><?php echo date('d M Y', strtotime($chap['created_at'])); ?></p>
                                </div>
                                <svg class="w-4 h-4 text-gray-300 group-hover:text-emerald-600 transition-colors flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                            <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="p-8 text-center text-gray-400">
                                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p class="text-sm">Belum ada bab yang dirilis</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                
                <!-- Author Card -->
                <div class="bg-white rounded-xl border border-gray-200 p-5 text-center">
                    <a href="profile.php?id=<?php echo $book['user_id']; ?>" class="block hover:opacity-80 transition-opacity">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($book['author_name']); ?>&background=059669&color=fff&size=80" alt="Author" class="w-16 h-16 rounded-full mx-auto mb-3">
                        <h4 class="font-bold text-gray-900"><?php echo htmlspecialchars($book['author_name']); ?></h4>
                    </a>
                    <p class="text-sm text-gray-500 mb-2">Penulis</p>
                    <?php if (!empty($book['author_bio'])): ?>
                        <p class="text-xs text-gray-600 mb-4 px-2 italic line-clamp-3">"<?php echo htmlspecialchars($book['author_bio']); ?>"</p>
                    <?php else: ?>
                        <p class="text-xs text-gray-400 mb-4 italic">Belum ada bio</p>
                    <?php endif; ?>
                    
                    <?php if (!$is_own_book): ?>
                    <button id="followBtn" data-author-id="<?php echo $book['user_id']; ?>" 
                        class="w-full py-2 rounded-lg border-2 transition-colors font-semibold text-sm 
                        <?php echo $is_following 
                            ? 'bg-gray-100 border-gray-100 text-gray-600 hover:bg-gray-200 hover:border-gray-200' 
                            : 'border-emerald-600 text-emerald-600 hover:bg-emerald-600 hover:text-white'; ?>">
                        <?php echo $is_following ? 'Mengikuti' : 'Ikuti Penulis'; ?>
                    </button>
                    <?php else: ?>
                    <a href="profile.php?id=<?php echo $book['user_id']; ?>" class="block w-full py-2 rounded-lg bg-gray-100 text-gray-600 font-semibold text-sm hover:bg-gray-200 transition-colors">
                        Lihat Profil
                    </a>
                    <?php endif; ?>
                </div>

                <!-- Share Card -->
                <div class="bg-white rounded-xl border border-gray-200 p-5">
                    <h4 class="font-bold text-gray-900 mb-3 text-sm">Bagikan</h4>
                    <button onclick="navigator.clipboard.writeText(window.location.href); alert('Link berhasil disalin!');" class="w-full py-2 rounded-lg bg-gray-100 text-gray-600 text-sm font-medium hover:bg-gray-200 transition-colors flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        Salin Link
                    </button>
                </div>
            </div>
        </div>
    </main>

    <?php include '../../layouts/footer.php'; ?>

    <script>
        const followBtn = document.getElementById('followBtn');
        if (followBtn) {
            followBtn.addEventListener('click', function() {
                const authorId = this.getAttribute('data-author-id');
                
                // Optimistic UI Update (optional, but good for UX)
                // For now, let's wait for server response to be sure
                
                const formData = new FormData();
                formData.append('following_id', authorId);

                fetch('process_follow.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (data.is_following) {
                            // Changed to Following
                            followBtn.textContent = 'Mengikuti';
                            followBtn.className = 'w-full py-2 rounded-lg border-2 transition-colors font-semibold text-sm bg-gray-100 border-gray-100 text-gray-600 hover:bg-gray-200 hover:border-gray-200';
                        } else {
                            // Changed to Not Following
                            followBtn.textContent = 'Ikuti Penulis';
                            followBtn.className = 'w-full py-2 rounded-lg border-2 transition-colors font-semibold text-sm border-emerald-600 text-emerald-600 hover:bg-emerald-600 hover:text-white';
                        }
                    } else {
                        if (data.message.includes('login')) {
                            alert('Silakan login untuk mengikuti penulis.');
                            window.location.href = '../../auth/login.php'; 
                        } else {
                            alert(data.message);
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan. Silakan coba lagi.');
                });
            });
        }
    </script>

</body>
</html>

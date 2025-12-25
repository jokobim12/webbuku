<?php
session_start();

require_once '../../database/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../auth/login.php');
    exit();
}

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
// Check Follow Status, Likes, Bookmarks
$is_following = false;
$is_own_book = false;
$is_liked = false;
$is_bookmarked = false;
$like_count = 0;

// Get Like Count
$like_query = mysqli_query($koneksi, "SELECT COUNT(*) as c FROM likes WHERE book_id = '$book_id'");
$like_count = mysqli_fetch_assoc($like_query)['c'];

// Get Comments
$comments_query = "SELECT comments.*, users.name as user_name, users.avatar as user_avatar 
                   FROM comments 
                   JOIN users ON comments.user_id = users.id 
                   WHERE comments.book_id = '$book_id' 
                   ORDER BY comments.created_at ASC"; // Order ASC for chronological conversation
$result_comments = mysqli_query($koneksi, $comments_query);

$comments_by_parent = [];
$total_comments = 0;
while ($row = mysqli_fetch_assoc($result_comments)) {
    $pid = $row['parent_id'] ? $row['parent_id'] : 0;
    $comments_by_parent[$pid][] = $row;
    $total_comments++;
}

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

    // Check Like
    $check_like = mysqli_query($koneksi, "SELECT id FROM likes WHERE user_id = '$current_user_id' AND book_id = '$book_id'");
    if (mysqli_num_rows($check_like) > 0) $is_liked = true;

    // Check Bookmark
    $check_bookmark = mysqli_query($koneksi, "SELECT id FROM bookmarks WHERE user_id = '$current_user_id' AND book_id = '$book_id'");
    if (mysqli_num_rows($check_bookmark) > 0) $is_bookmarked = true;
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script>
        tailwind.config = {
            darkMode: 'class',
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
<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-100 font-sans transition-colors duration-300">

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
                    <div id="coverContainer" class="w-36 sm:w-44 lg:w-52 aspect-[4/5] rounded-lg overflow-hidden bg-gray-700 book-cover-shadow animate-pulse relative">
                        <?php if($book['cover_image']): ?>
                            <img src="../../<?php echo htmlspecialchars($book['cover_image']); ?>" 
                                 alt="Cover" 
                                 class="w-full h-full object-cover opacity-0 transition-opacity duration-700"
                                 onload="this.classList.remove('opacity-0'); document.getElementById('coverContainer').classList.remove('animate-pulse');"
                                 onerror="this.style.display='none'; document.getElementById('coverContainer').classList.remove('animate-pulse');">
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
                    <div class="flex flex-wrap gap-2 mb-3 justify-center sm:justify-start">
                        <?php 
                        $genres = explode(',', $book['genre']);
                        foreach($genres as $g): 
                            $g = trim($g);
                            if(empty($g)) continue;
                        ?>
                        <span class="inline-block bg-emerald-600/90 backdrop-blur-sm text-white text-xs px-2.5 py-1 rounded font-semibold uppercase tracking-wide"><?php echo htmlspecialchars($g); ?></span>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Title & Like -->
                    <div class="flex items-start justify-center sm:justify-start gap-3 mb-2">
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white leading-tight"><?php echo htmlspecialchars($book['title']); ?></h1>
                    </div>
                    
                    <!-- Author -->
                    <a href="profile.php?id=<?php echo $book['user_id']; ?>" class="text-white font-medium hover:text-emerald-400 transition-colors"><?php echo htmlspecialchars($book['author_name']); ?></a>
                    
                    <!-- Stats - Simple inline -->
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-x-4 gap-y-1 text-sm text-gray-400 mb-5">
                        <span class="flex items-center gap-1"><i class="fa-regular fa-eye"></i> <?php echo number_format($book['views']); ?></span>
                        <span class="hidden sm:inline">•</span>
                        <span class="flex items-center gap-1"><i class="fa-solid fa-list-ol"></i> <?php echo count($chapters); ?> Bab</span>
                        <span class="hidden sm:inline">•</span>
                        <span><?php echo date('d M Y', strtotime($book['created_at'])); ?></span>
                    </div>
                    
                    <!-- Action Button -->
                    <?php if($first_chapter_id): ?>
                    <div class="flex flex-wrap gap-3 justify-center sm:justify-start">
                        <a href="baca_cerita.php?book_id=<?php echo $book_id; ?>&chapter_id=<?php echo $first_chapter_id; ?>" class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-2.5 px-6 rounded-lg transition-colors shadow-lg shadow-emerald-600/20">
                            <i class="fa-solid fa-book-open"></i> Mulai Baca
                        </a>
                        <button id="bookmarkBtn" data-book-id="<?php echo $book_id; ?>" class="inline-flex items-center justify-center gap-2 bg-gray-800/50 backdrop-blur-sm border border-gray-600 hover:bg-gray-700 text-white font-semibold py-2.5 px-4 rounded-lg transition-all">
                            <i class="<?php echo $is_bookmarked ? 'fa-solid text-emerald-400' : 'fa-regular'; ?> fa-bookmark"></i>
                            <span><?php echo $is_bookmarked ? 'Disimpan' : 'Simpan'; ?></span>
                        </button>
                        <button id="likeBtn" data-book-id="<?php echo $book_id; ?>" class="inline-flex items-center justify-center gap-2 bg-gray-800/50 backdrop-blur-sm border border-gray-600 hover:bg-gray-700 text-white font-semibold py-2.5 px-4 rounded-lg transition-all group min-w-[3.5rem]">
                            <i class="<?php echo $is_liked ? 'fa-solid text-red-500' : 'fa-regular'; ?> fa-heart text-xl group-hover:scale-110 transition-transform"></i>
                            <span id="likeCount" class="text-sm"><?php echo number_format($like_count); ?></span>
                        </button>
                    </div>
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
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-3">Sinopsis</h2>
                    <p class="text-gray-600 dark:text-gray-300 leading-relaxed whitespace-pre-line"><?php echo htmlspecialchars($book['synopsis']); ?></p>
                </section>

                <!-- Chapters List -->
                <section>
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-3">
                        Daftar Bab
                        <span class="text-sm font-normal text-gray-400 ml-1">(<?php echo count($chapters); ?>)</span>
                    </h2>
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <?php if(count($chapters) > 0): ?>
                            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                            <?php $i = 1; foreach($chapters as $chap): ?>
                            <a href="baca_cerita.php?book_id=<?php echo $book_id; ?>&chapter_id=<?php echo $chap['id']; ?>" class="flex items-center gap-3 p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors group">
                                <span class="flex-shrink-0 w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 font-medium text-sm flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors"><?php echo $i++; ?></span>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-medium text-gray-800 dark:text-gray-200 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 truncate transition-colors"><?php echo htmlspecialchars($chap['title']); ?></h4>
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
                
                <!-- Comments Section -->
                <section>
                    <h2 class="text-lg font-bold text-gray-900 mb-4">
                        Komentar
                        <span class="text-sm font-normal text-gray-400 ml-1">(<?php echo $total_comments; ?>)</span>
                    </h2>

                    <!-- Comment Form -->
                    <?php if(isset($_SESSION['user_id'])): ?>
                    <form id="commentForm" data-book-id="<?php echo $book_id; ?>" class="mb-8">
                        <div class="flex gap-4">
                            <?php 
                                $avatar = !empty($_SESSION['avatar']) ? (strpos($_SESSION['avatar'], 'http') === 0 ? $_SESSION['avatar'] : '/' . ltrim($_SESSION['avatar'], '/')) : 'https://ui-avatars.com/api/?name='.urlencode($_SESSION['name']).'&background=random';
                            ?>
                            <img src="<?php echo $avatar; ?>" alt="Avatar" class="w-10 h-10 rounded-full border border-gray-100 object-cover flex-shrink-0">
                            <div class="flex-1">
                                <textarea class="w-full bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 text-sm text-gray-900 dark:text-gray-100 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all resize-none" rows="3" placeholder="Tulis komentar anda..."></textarea>
                                <div class="flex justify-end mt-2">
                                    <button type="submit" class="bg-emerald-600 text-white px-5 py-2 rounded-lg text-sm font-semibold hover:bg-emerald-700 transition-all shadow-md hover:shadow-lg">Kirim</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <?php else: ?>
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6 text-center mb-8 border border-dashed border-gray-300 dark:border-gray-700">
                        <p class="text-gray-500 dark:text-gray-400 mb-3">Login untuk ikut berdiskusi</p>
                        <a href="../../auth/auth_google.php" class="inline-block bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 px-4 py-2 rounded-lg text-sm font-semibold hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">masuk dengan Google</a>
                    </div>
                    <?php endif; ?>

                    <!-- Comments List -->
                    <div id="commentsList" class="space-y-6">
                        <?php 
                        if (!empty($comments_by_parent[0])): 
                            foreach ($comments_by_parent[0] as $parent): 
                        ?>
                            <!-- Parent Comment -->
                            <div class="group" id="comment-<?php echo $parent['id']; ?>">
                                <div class="flex gap-4">
                                    <img src="<?php echo !empty($parent['user_avatar']) ? (strpos($parent['user_avatar'], 'http') === 0 ? $parent['user_avatar'] : '/' . ltrim($parent['user_avatar'], '/')) : 'https://ui-avatars.com/api/?name='.urlencode($parent['user_name']).'&background=random'; ?>" class="w-10 h-10 rounded-full object-cover border border-gray-100 dark:border-gray-700 flex-shrink-0">
                                    <div class="flex-1">
                                        <div class="bg-white dark:bg-gray-800 rounded-2xl rounded-tl-none p-4 border border-gray-100 dark:border-gray-700 shadow-sm relative group-hover:border-emerald-100 dark:group-hover:border-emerald-900 transition-colors">
                                            
                                            <div class="flex items-start justify-between mb-2">
                                                <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2">
                                                    <span class="font-bold text-sm text-gray-900 dark:text-gray-100"><?php echo htmlspecialchars($parent['user_name']); ?></span>
                                                    <span class="text-[10px] sm:text-xs text-gray-400"><?php echo date('d M, H:i', strtotime($parent['created_at'])); ?></span>
                                                </div>

                                                <!-- Actions (Edit/Delete) -->
                                                <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                                    <?php if(isset($_SESSION['user_id'])): ?>
                                                        <?php if($_SESSION['user_id'] == $parent['user_id']): ?>
                                                        <button onclick="toggleEdit(<?php echo $parent['id']; ?>)" class="text-gray-400 hover:text-emerald-600 p-1 transition-colors" title="Edit">
                                                            <i class="fa-solid fa-pen text-xs"></i>
                                                        </button>
                                                        <button onclick="deleteComment(<?php echo $parent['id']; ?>)" class="text-gray-400 hover:text-red-500 p-1 transition-colors" title="Hapus">
                                                            <i class="fa-solid fa-trash text-xs"></i>
                                                        </button>
                                                        <?php endif; ?>
                                                        <button onclick="toggleReply(<?php echo $parent['id']; ?>)" class="text-emerald-600 hover:text-emerald-700 font-medium text-xs bg-emerald-50 hover:bg-emerald-100 px-2 py-1 rounded transition-colors">
                                                            Balas
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            
                                            <!-- Content Display -->
                                            <p id="content-<?php echo $parent['id']; ?>" class="text-gray-700 dark:text-gray-300 text-sm whitespace-pre-wrap leading-relaxed"><?php echo htmlspecialchars($parent['content']); ?></p>
                                            
                                            <!-- Edit Form (Hidden) -->
                                            <div id="edit-form-<?php echo $parent['id']; ?>" class="hidden mt-2">
                                                <textarea class="w-full text-sm bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-600 dark:text-gray-200 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 p-2" rows="2"><?php echo htmlspecialchars($parent['content']); ?></textarea>
                                                <div class="flex justify-end gap-2 mt-2">
                                                    <button onclick="cancelEdit(<?php echo $parent['id']; ?>)" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">Batal</button>
                                                    <button onclick="saveEdit(<?php echo $parent['id']; ?>)" class="text-xs bg-emerald-600 text-white px-3 py-1 rounded hover:bg-emerald-700">Simpan</button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Reply Form (Hidden) -->
                                        <div id="reply-form-<?php echo $parent['id']; ?>" class="hidden mt-3 ml-2">
                                            <div class="flex gap-3">
                                                <div class="flex-1">
                                                    <textarea class="w-full text-sm bg-gray-50 dark:bg-gray-800 border-gray-200 dark:border-gray-700 dark:text-gray-200 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 p-3" rows="2" placeholder="Tulis balasan..."></textarea>
                                                    <div class="flex justify-end gap-2 mt-2">
                                                        <button onclick="toggleReply(<?php echo $parent['id']; ?>)" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">Batal</button>
                                                        <button onclick="submitReply(<?php echo $parent['id']; ?>)" class="text-xs bg-emerald-600 text-white px-3 py-1 rounded hover:bg-emerald-700 shadow-sm">Kirim Balasan</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Child Comments (Replies) -->
                                        <?php if (isset($comments_by_parent[$parent['id']])): ?>
                                        <div class="mt-4 space-y-4 pl-4 border-l-2 border-gray-100 dark:border-gray-700 ml-4">
                                            <?php foreach ($comments_by_parent[$parent['id']] as $child): ?>
                                            <div class="group/child" id="comment-<?php echo $child['id']; ?>">
                                                <div class="flex gap-3">
                                                    <img src="<?php echo !empty($child['user_avatar']) ? (strpos($child['user_avatar'], 'http') === 0 ? $child['user_avatar'] : '/' . ltrim($child['user_avatar'], '/')) : 'https://ui-avatars.com/api/?name='.urlencode($child['user_name']).'&background=random'; ?>" class="w-8 h-8 rounded-full object-cover border border-gray-100 dark:border-gray-700 flex-shrink-0">
                                                    <div class="flex-1">
                                                        <div class="bg-gray-50 dark:bg-gray-900/50 rounded-2xl rounded-tl-none p-3 border border-gray-100 dark:border-gray-700 relative group-hover/child:bg-white dark:group-hover/child:bg-gray-800 group-hover/child:shadow-sm transition-all">
                                                            
                                                            <div class="flex items-start justify-between mb-1">
                                                                <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-2">
                                                                    <span class="font-bold text-xs text-gray-900 dark:text-gray-200"><?php echo htmlspecialchars($child['user_name']); ?></span>
                                                                    <span class="text-[10px] text-gray-400"><?php echo date('d M, H:i', strtotime($child['created_at'])); ?></span>
                                                                </div>

                                                                <!-- Child Actions -->
                                                                <div class="flex gap-2 opacity-0 group-hover/child:opacity-100 transition-opacity">
                                                                    <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $child['user_id']): ?>
                                                                    <button onclick="toggleEdit(<?php echo $child['id']; ?>)" class="text-gray-400 hover:text-emerald-600 p-1 transition-colors" title="Edit">
                                                                        <i class="fa-solid fa-pen text-[10px]"></i>
                                                                    </button>
                                                                    <button onclick="deleteComment(<?php echo $child['id']; ?>)" class="text-gray-400 hover:text-red-500 p-1 transition-colors" title="Hapus">
                                                                        <i class="fa-solid fa-trash text-[10px]"></i>
                                                                    </button>
                                                                    <?php endif; ?>
                                                                </div>
                                                            </div>
                                                            
                                                            <p id="content-<?php echo $child['id']; ?>" class="text-gray-600 dark:text-gray-300 text-sm whitespace-pre-wrap"><?php echo htmlspecialchars($child['content']); ?></p>
                                                            
                                                            <!-- Child Edit Form -->
                                                            <div id="edit-form-<?php echo $child['id']; ?>" class="hidden mt-2">
                                                                <textarea class="w-full text-sm bg-gray-50 dark:bg-gray-800 border-gray-300 dark:border-gray-600 dark:text-gray-200 rounded-lg focus:ring-emerald-500 focus:border-emerald-500 p-2" rows="2"><?php echo htmlspecialchars($child['content']); ?></textarea>
                                                                <div class="flex justify-end gap-2 mt-2">
                                                                    <button onclick="cancelEdit(<?php echo $child['id']; ?>)" class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">Batal</button>
                                                                    <button onclick="saveEdit(<?php echo $child['id']; ?>)" class="text-xs bg-emerald-600 text-white px-3 py-1 rounded hover:bg-emerald-700">Simpan</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <?php endif; ?>

                                    </div>
                                </div>
                            </div>
                        <?php 
                            endforeach;
                        else: 
                        ?>
                            <div class="text-center py-8">
                                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 text-gray-400 mb-3">
                                    <i class="fa-regular fa-comments text-xl"></i>
                                </div>
                                <p class="text-gray-500 text-sm">Belum ada komentar. Jadilah yang pertama!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </section>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                
                <!-- Author Card -->
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5 text-center">
                    <a href="profile.php?id=<?php echo $book['user_id']; ?>" class="block hover:opacity-80 transition-opacity">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($book['author_name']); ?>&background=059669&color=fff&size=80" alt="Author" class="w-16 h-16 rounded-full mx-auto mb-3 border-4 border-emerald-50 dark:border-emerald-900">
                        <h4 class="font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($book['author_name']); ?></h4>
                    </a>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Penulis</p>
                    <?php if (!empty($book['author_bio'])): ?>
                        <p class="text-xs text-gray-600 dark:text-gray-400 mb-4 px-2 italic line-clamp-3">"<?php echo htmlspecialchars($book['author_bio']); ?>"</p>
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
                <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-5">
                    <h4 class="font-bold text-gray-900 dark:text-white mb-3 text-sm">Bagikan</h4>
                    <button onclick="navigator.clipboard.writeText(window.location.href); alert('Link berhasil disalin!');" class="w-full py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors flex items-center justify-center gap-1.5">
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
                const text = this;
                
                fetch('../../api/follow.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({following_id: authorId})
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        if (data.is_following) {
                            // Changed to Following
                            text.textContent = 'Mengikuti';
                            text.className = 'w-full py-2 rounded-lg border-2 transition-colors font-semibold text-sm bg-gray-100 border-gray-100 text-gray-600 hover:bg-gray-200 hover:border-gray-200';
                        } else {
                            // Changed to Not Following
                            text.textContent = 'Ikuti Penulis';
                            text.className = 'w-full py-2 rounded-lg border-2 transition-colors font-semibold text-sm border-emerald-600 text-emerald-600 hover:bg-emerald-600 hover:text-white';
                        }
                    } else {
                        if (data.message.includes('login') || data.message === 'Unauthorized') {
                            alert('Silakan login untuk mengikuti penulis.');
                            window.location.href = '../../auth/auth_google.php'; 
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

        // Bookmark Logic
        const bookmarkBtn = document.getElementById('bookmarkBtn');
        if(bookmarkBtn) {
            bookmarkBtn.addEventListener('click', function() {
                const bookId = this.getAttribute('data-book-id');
                const icon = this.querySelector('i');
                const text = this.querySelector('span');

                fetch('../../api/bookmark.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({book_id: bookId})
                })
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') {
                        if(data.action === 'added') {
                            icon.classList.remove('fa-regular');
                            icon.classList.add('fa-solid', 'text-emerald-400');
                            text.textContent = 'Disimpan';
                        } else {
                            icon.classList.remove('fa-solid', 'text-emerald-400');
                            icon.classList.add('fa-regular');
                            text.textContent = 'Simpan';
                        }
                    } else if(data.message === 'Unauthorized') {
                        window.location.href = '../../auth/auth_google.php';
                    }
                });
            });
        }

        // Like Logic
        const likeBtn = document.getElementById('likeBtn');
        const likeCount = document.getElementById('likeCount');
        if(likeBtn) {
            likeBtn.addEventListener('click', function() {
                const bookId = this.getAttribute('data-book-id');
                const icon = this.querySelector('i');
                // Optimistic UI
                const isLiked = icon.classList.contains('fa-solid');
                if(isLiked) {
                    icon.classList.remove('fa-solid', 'text-red-500');
                    icon.classList.add('fa-regular');
                } else {
                    icon.classList.remove('fa-regular');
                    icon.classList.add('fa-solid', 'text-red-500');
                }

                fetch('../../api/like.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({book_id: bookId})
                })
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') {
                        if(likeCount) likeCount.textContent = data.likes;
                        
                        // Sync UI with actual server state
                        if(data.action === 'liked') {
                            icon.classList.remove('fa-regular');
                            icon.classList.add('fa-solid', 'text-red-500');
                        } else {
                             icon.classList.remove('fa-solid', 'text-red-500');
                             icon.classList.add('fa-regular');
                        }
                    } else if(data.message === 'Unauthorized') {
                         window.location.href = '../../auth/auth_google.php';
                    }
                });
            });
        }

        // Comment Logic
        const commentForm = document.getElementById('commentForm');
        const commentsList = document.getElementById('commentsList');

        if(commentForm) {
            commentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const content = this.querySelector('textarea').value;
                const bookId = this.getAttribute('data-book-id');
                
                if(!content.trim()) return;

                fetch('../../api/comment.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({book_id: bookId, content: content})
                })
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') {
                        location.reload(); // Reload to show new comment in correct threaded order (simplest for now)
                    } else if(data.message === 'Unauthorized') {
                        window.location.href = '../../auth/auth_google.php';
                    }
                });
            });
        }

        // Toggle Reply Form
        function toggleReply(id) {
            const form = document.getElementById(`reply-form-${id}`);
            if(form) form.classList.toggle('hidden');
        }

        // Toggle Edit Form
        function toggleEdit(id) {
            const content = document.getElementById(`content-${id}`);
            const form = document.getElementById(`edit-form-${id}`);
            
            if (form && content) {
                if (form.classList.contains('hidden')) {
                    form.classList.remove('hidden');
                    content.classList.add('hidden');
                } else {
                    form.classList.add('hidden');
                    content.classList.remove('hidden');
                }
            }
        }
        
        function cancelEdit(id) {
             const content = document.getElementById(`content-${id}`);
             const form = document.getElementById(`edit-form-${id}`);
             if(form && content) {
                 form.classList.add('hidden');
                 content.classList.remove('hidden');
             }
        }

        // Submit Reply
        function submitReply(parentId) {
            const formContainer = document.getElementById(`reply-form-${parentId}`);
            const textarea = formContainer.querySelector('textarea');
            const content = textarea.value;
            const bookId = <?php echo $book_id; ?>;

            if(!content.trim()) return;

            fetch('../../api/comment.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    book_id: bookId, 
                    content: content, 
                    parent_id: parentId
                })
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    location.reload(); 
                } else {
                    alert(data.message || 'Gagal membalas komentar');
                }
            });
        }

        // Save Edit
        function saveEdit(commentId) {
            const formContainer = document.getElementById(`edit-form-${commentId}`);
            const textarea = formContainer.querySelector('textarea');
            const content = textarea.value;

            if(!content.trim()) return;

            fetch('../../api/comment.php', {
                method: 'PUT',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    comment_id: commentId, 
                    content: content
                })
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    const contentP = document.getElementById(`content-${commentId}`);
                    contentP.textContent = content;
                    toggleEdit(commentId);
                } else {
                     alert(data.message || 'Gagal mengupdate komentar');
                }
            });
        }

        // Delete Comment
        function deleteComment(id) {
            if(!confirm('Yakin ingin menghapus komentar ini?')) return;

            fetch('../../api/comment.php', {
                method: 'DELETE',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({comment_id: id})
            })
            .then(res => res.json())
            .then(data => {
                 if(data.status === 'success') {
                     const el = document.getElementById(`comment-${id}`);
                     if(el) el.remove();
                     location.reload(); // Reload to handle removed children if any
                 } else {
                     alert(data.message || 'Gagal menghapus komentar');
                 }
            });
        }

    </script>
</body>
</html>

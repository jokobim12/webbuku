<?php
session_start();
require_once '../../database/koneksi.php';

if (!isset($_GET['id'])) {
    header('Location: ../../index.php');
    exit();
}

$user_id = mysqli_real_escape_string($koneksi, $_GET['id']);

// Get User Details
$query_user = "SELECT * FROM users WHERE id = '$user_id'";
$result_user = mysqli_query($koneksi, $query_user);
$profile_user = mysqli_fetch_assoc($result_user);

if (!$profile_user) {
    echo "Pengguna tidak ditemukan.";
    exit();
}

// Get Stats
// 1. Total Works
$query_works = "SELECT COUNT(*) as total FROM books WHERE user_id = '$user_id' AND status = 'published'";
$result_works = mysqli_query($koneksi, $query_works);
$total_works = mysqli_fetch_assoc($result_works)['total'];

// 2. Total Views
$query_views = "SELECT SUM(views) as total FROM books WHERE user_id = '$user_id' AND status = 'published'";
$result_views = mysqli_query($koneksi, $query_views);
$total_views = mysqli_fetch_assoc($result_views)['total'];
$total_views = $total_views ? $total_views : 0;

// 3. Followers
$query_followers = "SELECT COUNT(*) as total FROM follows WHERE following_id = '$user_id'";
$result_followers = mysqli_query($koneksi, $query_followers);
$total_followers = mysqli_fetch_assoc($result_followers)['total'];

// 4. Following
$query_following = "SELECT COUNT(*) as total FROM follows WHERE follower_id = '$user_id'";
$result_following = mysqli_query($koneksi, $query_following);
$total_following = mysqli_fetch_assoc($result_following)['total'];

// Get Published Books
$query_books = "SELECT * FROM books WHERE user_id = '$user_id' AND status = 'published' ORDER BY created_at DESC";
$result_books = mysqli_query($koneksi, $query_books);
$books = [];
while ($row = mysqli_fetch_assoc($result_books)) {
    $books[] = $row;
}

// Check Follow Status (if logged in)
$is_following = false;
$is_self = false;
if (isset($_SESSION['user_id'])) {
    $current_user_id = $_SESSION['user_id'];
    if ($current_user_id == $user_id) {
        $is_self = true;
    } else {
        $check_follow = mysqli_query($koneksi, "SELECT id FROM follows WHERE follower_id = '$current_user_id' AND following_id = '$user_id'");
        $is_following = mysqli_num_rows($check_follow) > 0;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil <?php echo htmlspecialchars($profile_user['name']); ?> - WebBuku</title>
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
</head>
<body class="bg-gray-50 text-gray-800 font-sans">

    <?php include '../../layouts/navbar.php'; ?>

    <!-- Header / Profile Cover -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 pt-24 pb-12"> <!-- Added pt-24 -->
            <!-- Back Button -->
            <div class="mb-8">
                <button onclick="history.back()" class="inline-flex items-center text-gray-500 hover:text-emerald-600 font-medium transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali
                </button>
            </div>

            <div class="flex flex-col md:flex-row items-center gap-8">
                <!-- Avatar -->
                <div class="flex-shrink-0">
                    <?php 
                        $avatar = trim($profile_user['avatar']);
                        if (stripos($avatar, 'http') !== 0) {
                            $avatar = '../../' . $avatar;
                        }
                    ?>
                    <img src="<?php echo htmlspecialchars($avatar); ?>" alt="Avatar" referrerpolicy="no-referrer" class="w-32 h-32 md:w-40 md:h-40 rounded-full object-cover border-4 border-emerald-50 shadow-lg">
                </div>

                <!-- Info -->
                <div class="flex-1 text-center md:text-left">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($profile_user['name']); ?></h1>
                    
                    <?php if (!empty($profile_user['bio'])): ?>
                        <p class="text-gray-600 mb-6 max-w-2xl mx-auto md:mx-0 leading-relaxed"><?php echo nl2br(htmlspecialchars($profile_user['bio'])); ?></p>
                    <?php else: ?>
                        <p class="text-gray-400 mb-6 italic">Tidak ada bio.</p>
                    <?php endif; ?>

                    <!-- Social Media (Added) -->
                    <?php if (!empty($profile_user['social_twitter']) || !empty($profile_user['social_instagram']) || !empty($profile_user['social_website'])): ?>
                        <div class="flex gap-4 justify-center md:justify-start mb-6">
                            <?php if (!empty($profile_user['social_twitter'])): ?>
                                <a href="https://twitter.com/<?php echo htmlspecialchars($profile_user['social_twitter']); ?>" target="_blank" class="text-gray-400 hover:text-blue-400 transition-colors">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.84 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($profile_user['social_instagram'])): ?>
                                <a href="https://instagram.com/<?php echo htmlspecialchars($profile_user['social_instagram']); ?>" target="_blank" class="text-gray-400 hover:text-pink-500 transition-colors">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                                </a>
                            <?php endif; ?>
                            <?php if (!empty($profile_user['social_website'])): ?>
                                <a href="<?php echo htmlspecialchars($profile_user['social_website']); ?>" target="_blank" class="text-gray-400 hover:text-emerald-600 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <div class="flex flex-wrap justify-center md:justify-start gap-6 text-sm mb-6">
                        <div class="flex flex-col items-center md:items-start">
                            <span class="font-bold text-gray-900 text-lg"><?php echo number_format($total_works); ?></span>
                            <span class="text-gray-500">Karya</span>
                        </div>
                        <div class="flex flex-col items-center md:items-start">
                            <span class="font-bold text-gray-900 text-lg"><?php echo number_format($total_views); ?></span>
                            <span class="text-gray-500">Dibaca</span>
                        </div>
                        <div class="flex flex-col items-center md:items-start">
                            <span class="font-bold text-gray-900 text-lg"><?php echo number_format($total_followers); ?></span>
                            <span class="text-gray-500">Pengikut</span>
                        </div>
                        <div class="flex flex-col items-center md:items-start">
                            <span class="font-bold text-gray-900 text-lg"><?php echo number_format($total_following); ?></span>
                            <span class="text-gray-500">Mengikuti</span>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <?php if ($is_self): ?>
                        <a href="pengaturan.php" class="inline-flex items-center px-6 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                            Edit Profil
                        </a>
                    <?php else: ?>
                        <button id="followBtn" data-author-id="<?php echo $user_id; ?>" 
                            class="inline-flex items-center px-8 py-2 rounded-lg font-medium transition-colors border-2
                            <?php echo $is_following 
                                ? 'bg-gray-100 border-gray-100 text-gray-600 hover:bg-gray-200 hover:border-gray-200' 
                                : 'bg-emerald-600 border-emerald-600 text-white hover:bg-emerald-700 hover:border-emerald-700'; ?>">
                            <?php echo $is_following ? 'Mengikuti' : 'Ikuti'; ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Books Section -->
    <main class="max-w-5xl mx-auto px-4 sm:px-6 py-12">
        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            Karya Dipublikasikan
        </h2>

        <?php if (count($books) > 0): ?>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php foreach ($books as $buku): ?>
                <a href="detail.php?id=<?php echo $buku['id']; ?>" class="group block bg-white rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden border border-gray-100">
                    <div class="aspect-[3/4] overflow-hidden bg-gray-100 relative">
                        <?php if ($buku['cover_image']): ?>
                            <img src="../../<?php echo htmlspecialchars($buku['cover_image']); ?>" alt="<?php echo htmlspecialchars($buku['title']); ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        <?php endif; ?>
                        <div class="absolute top-2 right-2 bg-black/50 backdrop-blur-sm text-white text-xs px-2 py-1 rounded-full flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <?php echo number_format($buku['views']); ?>
                        </div>
                    </div>
                    <div class="p-4">
                        <span class="text-xs font-semibold text-emerald-600 mb-1 block"><?php echo htmlspecialchars($buku['genre']); ?></span>
                        <h3 class="font-bold text-gray-900 mb-1 line-clamp-1 group-hover:text-emerald-600 transition-colors"><?php echo htmlspecialchars($buku['title']); ?></h3>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-xl border border-gray-200 p-12 text-center text-gray-500">
                <p>Belum ada karya yang dipublikasikan.</p>
            </div>
        <?php endif; ?>
    </main>

    <?php include '../../layouts/footer.php'; ?>

    <script>
        const followBtn = document.getElementById('followBtn');
        if (followBtn) {
            followBtn.addEventListener('click', function() {
                const authorId = this.getAttribute('data-author-id');
                const formData = new FormData();
                formData.append('following_id', authorId);

                // Optimistic Update
                const isFollowing = this.textContent.trim() === 'Mengikuti';
                if (isFollowing) {
                    this.textContent = 'Ikuti';
                    this.className = 'inline-flex items-center px-8 py-2 rounded-lg font-medium transition-colors border-2 bg-emerald-600 border-emerald-600 text-white hover:bg-emerald-700 hover:border-emerald-700';
                } else {
                    this.textContent = 'Mengikuti';
                    this.className = 'inline-flex items-center px-8 py-2 rounded-lg font-medium transition-colors border-2 bg-gray-100 border-gray-100 text-gray-600 hover:bg-gray-200 hover:border-gray-200';
                }

                fetch('../../api/follow.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        if (data.message.includes('login')) {
                            alert('Silakan login untuk mengikuti penulis.');
                            window.location.href = '../../auth/login.php'; 
                        } else {
                            alert(data.message);
                            location.reload(); // Revert on error
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    location.reload();
                });
            });
        }
    </script>

</body>
</html>

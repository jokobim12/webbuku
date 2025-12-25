<?php
session_start();
require_once '../../database/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../auth/login.php');
    exit();
}

$current_user_id = $_SESSION['user_id'];
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'followers'; // Default to followers

$users = [];
if ($tab == 'following') {
    $query = "SELECT users.id, users.name, users.avatar 
              FROM follows 
              JOIN users ON follows.following_id = users.id 
              WHERE follows.follower_id = '$current_user_id'";
} else {
    // Followers
    $query = "SELECT users.id, users.name, users.avatar 
              FROM follows 
              JOIN users ON follows.follower_id = users.id 
              WHERE follows.following_id = '$current_user_id'";
}

$result = mysqli_query($koneksi, $query);
while ($row = mysqli_fetch_assoc($result)) {
    // Check if I follow them (for the 'Followers' tab, we might want to follow back)
    // For 'Following' tab, we obviously follow them.
    $check_query = "SELECT id FROM follows WHERE follower_id = '$current_user_id' AND following_id = '" . $row['id'] . "'";
    $check_res = mysqli_query($koneksi, $check_query);
    $row['is_following'] = mysqli_num_rows($check_res) > 0;
    
    $users[] = $row;
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo ($tab == 'following') ? 'Mengikuti' : 'Pengikut'; ?> - WebBuku</title>
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
                            600: '#059669',
                            700: '#047857',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased font-sans">
    
    <!-- Sidebar (Fixed) -->
    <aside class="fixed inset-y-0 left-0 w-64 bg-white border-r border-gray-200 z-30 transition-transform duration-300 transform -translate-x-full md:translate-x-0" id="sidebar">
        <!-- Logo -->
        <div class="h-16 flex items-center px-6 border-b border-gray-100">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-emerald-600 rounded-lg flex items-center justify-center text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <span class="text-xl font-bold text-gray-900 tracking-tight">WebBuku</span>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="p-4 space-y-1 overflow-y-auto h-[calc(100vh-4rem)]">
            <a href="../../dashboard.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-lg font-medium transition-colors">
                <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                Dashboard
            </a>
            
            <a href="tulis_cerita.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-lg font-medium transition-colors">
                 <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Tulis Cerita
            </a>

            <a href="karyaku.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-lg font-medium transition-colors">
                <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                Karyaku
            </a>
            
            <div class="pt-4 mt-4 border-t border-gray-100">
                <a href="buku.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    Jelajahi
                </a>
                
                <a href="../../index.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Ke Beranda
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="md:ml-64 min-h-screen flex flex-col">
        
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8">
            <button class="md:hidden p-2 text-gray-600 rounded-lg hover:bg-gray-100" onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>

            <div class="flex-1 px-4">
               <h1 class="text-lg font-semibold text-gray-800">Jaringan Pertemanan</h1>
            </div>

            <!-- Profile Dropdown -->
            <div class="relative group">
                <button class="flex items-center gap-3 focus:outline-none">
                    <span class="hidden md:block text-sm font-medium text-gray-700"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                    <?php 
                        $sess_avatar = trim($_SESSION['avatar']);
                        if (stripos($sess_avatar, 'http') !== 0) {
                            $sess_avatar = '../../' . $sess_avatar;
                        }
                    ?>
                    <img src="<?php echo htmlspecialchars($sess_avatar); ?>" alt="Avatar" referrerpolicy="no-referrer" class="w-9 h-9 rounded-full border border-gray-200 object-cover">
                </button>
                <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 hidden group-hover:block z-50">
                    <a href="../../auth/logout.php" onclick="confirmLogout(event, this.href)" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 first:rounded-t-lg last:rounded-b-lg">
                        Keluar
                    </a>
                </div>
            </div>
        </header>

        <!-- Content Body -->
        <main class="flex-1 p-6 lg:p-8">
        
            <!-- Back Button -->
            <div class="mb-6">
                <a href="../../dashboard.php" class="inline-flex items-center text-gray-500 hover:text-emerald-600 text-sm font-medium transition-colors">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Kembali ke Dashboard
                </a>
            </div>

            <!-- Tabs -->
            <div class="flex border-b border-gray-200 mb-6">
                <a href="?tab=followers" class="px-6 py-3 text-sm font-medium <?php echo $tab == 'followers' ? 'border-b-2 border-emerald-600 text-emerald-600' : 'text-gray-500 hover:text-gray-700'; ?>">
                    Pengikut
                </a>
                <a href="?tab=following" class="px-6 py-3 text-sm font-medium <?php echo $tab == 'following' ? 'border-b-2 border-emerald-600 text-emerald-600' : 'text-gray-500 hover:text-gray-700'; ?>">
                    Mengikuti
                </a>
            </div>

            <!-- User List -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <?php if (count($users) > 0): ?>
                    <div class="divide-y divide-gray-100">
                        <?php foreach ($users as $u): ?>
                        <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                            <a href="profile.php?id=<?php echo $u['id']; ?>" class="flex items-center gap-4 group">
                                <?php 
                                    $u_avatar = trim($u['avatar']);
                                    if (stripos($u_avatar, 'http') !== 0) {
                                        $u_avatar = '../../' . $u_avatar;
                                    }
                                ?>
                                <img src="<?php echo htmlspecialchars($u_avatar); ?>" alt="Avatar" referrerpolicy="no-referrer" class="w-12 h-12 rounded-full object-cover border border-gray-200 group-hover:border-emerald-500 transition-colors">
                                <div>
                                    <h4 class="font-bold text-gray-900 group-hover:text-emerald-600 transition-colors"><?php echo htmlspecialchars($u['name']); ?></h4>
                                </div>
                            </a>
                            
                            <?php if ($u['id'] != $current_user_id): ?>
                                <button onclick="toggleFollow(this, <?php echo $u['id']; ?>)" 
                                    class="px-4 py-1.5 rounded-lg text-sm font-medium transition-colors border-2
                                    <?php echo $u['is_following'] 
                                        ? 'bg-gray-100 border-gray-100 text-gray-600 hover:bg-gray-200 hover:border-gray-200' 
                                        : 'border-emerald-600 text-emerald-600 hover:bg-emerald-600 hover:text-white'; ?>">
                                    <?php echo $u['is_following'] ? 'Mengikuti' : 'Ikuti'; ?>
                                </button>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="p-12 text-center text-gray-400">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <p class="text-lg font-medium text-gray-900">Belum ada data</p>
                        <p class="text-sm">Daftar <?php echo ($tab == 'following') ? 'orang yang Anda ikuti' : 'pengikut Anda'; ?> kosong.</p>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <script>
        function toggleFollow(btn, userId) {
            // Optimistic UI
            const isFollowing = btn.textContent.trim() === 'Mengikuti';
            const newText = isFollowing ? 'Ikuti' : 'Mengikuti';
            const newClass = isFollowing 
                ? 'border-emerald-600 text-emerald-600 hover:bg-emerald-600 hover:text-white' // Green
                : 'bg-gray-100 border-gray-100 text-gray-600 hover:bg-gray-200 hover:border-gray-200'; // Gray
            
            // Apply new state
            btn.textContent = newText;
            btn.className = `px-4 py-1.5 rounded-lg text-sm font-medium transition-colors border-2 ${newClass}`;

            const formData = new FormData();
            formData.append('following_id', userId);

            fetch('process_follow.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    // Revert if failed
                    alert(data.message);
                    location.reload(); 
                }
            })
            .catch(error => {
                console.error('Error:', error);
                // Revert
                location.reload();
            });
        }
    </script>
    <?php include '../../layouts/confirmation_modal.php'; ?>
</body>
</html>

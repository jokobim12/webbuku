<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}
require_once 'database/koneksi.php';

$user_id = $_SESSION['user_id'];

// Get Total Works
$query_works = "SELECT COUNT(*) as total FROM books WHERE user_id = '$user_id'";
$result_works = mysqli_query($koneksi, $query_works);
$total_works = mysqli_fetch_assoc($result_works)['total'];

// Get Total Views
$query_views = "SELECT SUM(views) as total FROM books WHERE user_id = '$user_id'";
$result_views = mysqli_query($koneksi, $query_views);
$total_views = mysqli_fetch_assoc($result_views)['total'];
$total_views = $total_views ? $total_views : 0;

// Get Total Followers
$query_followers = "SELECT COUNT(*) as total FROM follows WHERE following_id = '$user_id'";
$result_followers = mysqli_query($koneksi, $query_followers);
$total_followers = mysqli_fetch_assoc($result_followers)['total'];

// Get Total Following
$query_following = "SELECT COUNT(*) as total FROM follows WHERE follower_id = '$user_id'";
$result_following = mysqli_query($koneksi, $query_following);
$total_following = mysqli_fetch_assoc($result_following)['total'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - WebBuku</title>
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
            <a href="#" class="flex items-center px-4 py-3 bg-emerald-50 text-emerald-700 rounded-lg font-medium transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                Dashboard
            </a>
            
            <a href="page/user/tulis_cerita.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-lg font-medium transition-colors">
                 <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Tulis Cerita
            </a>
            
            <a href="page/user/pengaturan.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-lg font-medium transition-colors">
                <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Pengaturan
            </a>
            <a href="page/user/karyaku.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-lg font-medium transition-colors">
                <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                Karyaku
            </a>
            
            <div class="pt-4 mt-4 border-t border-gray-100">
                <a href="page/user/buku.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    Jelajahi
                </a>
                
                <a href="index.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-lg font-medium transition-colors">
                    <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Ke Beranda
                </a>
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <div class="md:ml-64 min-h-screen flex flex-col">
        
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8">
            <button class="md:hidden p-2 text-gray-600 rounded-lg hover:bg-gray-100" onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>

            <div class="flex-1 px-4">
               <h1 class="text-lg font-semibold text-gray-800">Overview</h1>
            </div>

            <!-- Profile Dropdown -->
            <div class="relative group">
                <button class="flex items-center gap-3 focus:outline-none">
                    <span class="hidden md:block text-sm font-medium text-gray-700"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                    <img src="<?php echo htmlspecialchars($_SESSION['avatar']); ?>" alt="Avatar" class="w-9 h-9 rounded-full border border-gray-200 object-cover">
                </button>
                <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 hidden group-hover:block z-50">
                    <a href="page/user/pengaturan.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 first:rounded-t-lg">
                        Edit Profil
                    </a>
                    <a href="auth/logout.php" onclick="confirmLogout(event, this.href)" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 last:rounded-b-lg">
                        Keluar
                    </a>
                </div>
            </div>
        </header>

        <!-- Content Body -->
        <main class="flex-1 p-6 lg:p-8">
            <!-- Stats Grid - Mobile Optimized (2 cols) -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-8">
                <!-- Stat Card 1 -->
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Total Karyamu</p>
                        <h3 class="text-3xl font-bold text-gray-900"><?php echo number_format($total_works); ?></h3>
                    </div>
                    <div class="p-3 bg-indigo-50 rounded-lg text-indigo-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                </div>

                <!-- Stat Card 2 -->
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Total Dibaca</p>
                        <h3 class="text-3xl font-bold text-gray-900"><?php echo number_format($total_views); ?></h3>
                    </div>
                    <div class="p-3 bg-emerald-50 rounded-lg text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </div>
                </div>

                <!-- Stat Card 3 (Followers) -->
                <a href="page/user/relasi.php?tab=followers" class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-start justify-between hover:shadow-md transition-shadow">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Total Pengikut</p>
                        <h3 class="text-3xl font-bold text-gray-900"><?php echo number_format($total_followers); ?></h3>
                    </div>
                    <div class="p-3 bg-purple-50 rounded-lg text-purple-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                </a>

                <!-- Stat Card 4 (Following) -->
                <a href="page/user/relasi.php?tab=following" class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex items-start justify-between hover:shadow-md transition-shadow">
                    <div>
                        <p class="text-sm font-medium text-gray-500 mb-1">Total Mengikuti</p>
                        <h3 class="text-3xl font-bold text-gray-900"><?php echo number_format($total_following); ?></h3>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-lg text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    </div>
                </a>
            </div>

            <!-- CTA Banner -->
            <div class="bg-emerald-700 rounded-2xl p-8 md:p-10 text-white relative overflow-hidden shadow-lg">
                <!-- Subtle pattern instead of loud gradient blobs -->
                <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -translate-y-1/2 translate-x-1/2"></div>
                
                <div class="relative z-10 max-w-2xl">
                    <h2 class="text-3xl font-bold mb-4">Mulai Petualangan Barumu</h2>
                    <p class="text-emerald-100 text-lg mb-8">Punya ide cerita yang menarik? Tuangkan imajinasimu sekarang dan bagikan kepada dunia.</p>
                   <a href="page/user/tulis_cerita.php"> <button class="bg-white text-emerald-700 hover:bg-gray-100 px-6 py-3 rounded-lg font-bold shadow-sm transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                        Buat Cerita Baru
                    </button></a>
                </div>
            </div>

            <footer class="mt-12 text-center text-gray-400 text-sm">
                &copy; 2025 WebBuku. All rights reserved.
            </footer>
        </main>
    </div>

    <?php include 'layouts/confirmation_modal.php'; ?>
</body>
</html>

<?php 
session_start(); 
require_once 'database/koneksi.php';

// Fetch Popular Books (Limit 4)
$query_popular = "SELECT books.*, users.name as author_name FROM books 
                  JOIN users ON books.user_id = users.id 
                  WHERE books.status='published' 
                  ORDER BY books.views DESC LIMIT 4";
$result_popular = mysqli_query($koneksi, $query_popular);

// Fetch One Featured Book (Random)
$query_featured = "SELECT books.*, users.name as author_name FROM books 
                   JOIN users ON books.user_id = users.id 
                   WHERE books.status='published' 
                   ORDER BY RAND() LIMIT 1";
$result_featured = mysqli_query($koneksi, $query_featured);
$featured_book = mysqli_fetch_assoc($result_featured);

// Fetch Stats
$count_users = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as c FROM users"))['c'];
$count_books = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as c FROM books WHERE status='published'"))['c'];
$sum_views = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT SUM(views) as s FROM books WHERE status='published'"))['s'];
$sum_views = $sum_views ? $sum_views : 0;

// Reading History removed as requested
$continue_reading = null;
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebBuku - Buat Cerita Impianmu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            bg: '#0f172a',
                            card: '#1e293b',
                            text: '#f1f5f9'
                        }
                    }
                }
            }
        }
    </script>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .hero-pattern {
            background-color: #f3f4f6;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23e5e7eb' fill-opacity='0.4'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

   <?php include 'layouts/navbar.php'; ?>

    <!-- Continue Reading Banner -->
    <!-- Continue Reading Banner removed -->


    <!-- Hero Section -->
    <section class="relative pt-28 pb-16 lg:pt-48 lg:pb-32 overflow-hidden hero-pattern">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-4xl mx-auto">
                <span class="inline-block py-1 px-3 rounded-full bg-emerald-100 text-emerald-700 text-xs md:text-sm font-semibold mb-4 md:mb-6 animate-fade-in-up">Platform Penulis Masa Depan</span>
                <h1 class="text-2xl md:text-5xl font-extrabold text-gray-900 tracking-tight mb-6 md:mb-8 leading-tight">
                    <?php if($featured_book): ?>
                        Baca <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 to-teal-600"><?php echo htmlspecialchars($featured_book['title']); ?></span><br>
                        karya <?php echo htmlspecialchars($featured_book['author_name']); ?>
                    <?php else: ?>
                        Tuangkan Imajinasi,<br>
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 to-teal-600">Terbitkan Karyamu</span>
                    <?php endif; ?>
                </h1>
                <p class="mt-4 max-w-2xl mx-auto text-base md:text-xl text-gray-500 mb-8 md:mb-10 leading-relaxed px-4">
                    <?php if($featured_book): ?>
                        Satu dari sekian banyak cerita menarik di WebBuku. Mulai petualangan membacamu atau tulis ceritamu sendiri hari ini.
                    <?php else: ?>
                        WebBuku adalah tempat di mana setiap kata bermakna. Mulai tulis novel atau cerita pendekmu hari ini dengan alat penulisan canggih dan komunitas yang mendukung.
                    <?php endif; ?>
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4 px-4">
                    <?php if($featured_book): ?>
                        <a href="page/user/detail.php?id=<?php echo $featured_book['id']; ?>" class="bg-gray-900 text-white px-8 py-3 md:py-4 rounded-xl font-medium text-base md:text-lg hover:bg-gray-800 transition-all shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
                            Baca Sekarang <i class="fa-solid fa-book-open ml-2"></i>
                        </a>
                    <?php else: ?>
                        <a href="page/user/kelola_cerita.php" class="bg-gray-900 text-white px-8 py-3 md:py-4 rounded-xl font-medium text-base md:text-lg hover:bg-gray-800 transition-all shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
                            Mulai Menulis <i class="fa-solid fa-pen-nib ml-2"></i>
                        </a>
                    <?php endif; ?>
                    <a href="page/user/buku.php" class="bg-white text-gray-700 border border-gray-200 px-8 py-3 md:py-4 rounded-xl font-medium text-base md:text-lg hover:bg-gray-50 transition-all hover:border-gray-300">
                        Lihat Semua Buku
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Decorative Elements -->
        <div class="absolute top-1/4 left-0 w-64 h-64 bg-emerald-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
        <div class="absolute top-1/3 right-0 w-64 h-64 bg-teal-300 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
    </section>

    <!-- Section: Buku Terpopuler (Top 4) -->
    <section id="top-books" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-emerald-600 font-semibold tracking-wide uppercase text-sm">Sedang Hangat</span>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2">Buku Paling Populer Minggu Ini <i class="fa-solid fa-fire text-orange-500 ml-2"></i></h2>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8">
                <?php while($book = mysqli_fetch_assoc($result_popular)): ?>
                <!-- Book Card -->
                <div class="group bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                    <div class="relative h-40 md:h-64 bg-gray-200 overflow-hidden">
                        <?php if($book['cover_image']): ?>
                        <img src="<?php echo htmlspecialchars($book['cover_image']); ?>" alt="Cover" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <?php endif; ?>
                        
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                            <span class="text-white font-medium text-xs">Dibaca <?php echo number_format($book['views']); ?> kali</span>
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="flex items-center justify-between mb-2">
                             <div class="flex flex-wrap gap-1">
                                <?php 
                                $genres = explode(',', $book['genre']);
                                $first_genre = trim($genres[0]);
                                ?>
                                <span class="bg-emerald-50 text-emerald-700 text-[10px] px-2 py-0.5 rounded-full font-semibold uppercase tracking-wide truncate max-w-[100px]"><?php echo htmlspecialchars($first_genre); ?></span>
                            </div>
                        </div>
                        <h3 class="text-base font-bold text-gray-900 mb-1 line-clamp-1 group-hover:text-emerald-600 transition-colors" title="<?php echo htmlspecialchars($book['title']); ?>">
                            <?php echo htmlspecialchars($book['title']); ?>
                        </h3>
                        <p class="text-xs text-gray-500 mb-4">oleh <span class="font-medium text-gray-700"><?php echo htmlspecialchars($book['author_name']); ?></span></p>
                        <a href="page/user/detail.php?id=<?php echo $book['id']; ?>" class="block w-full text-center py-2 rounded-lg border border-emerald-600 text-emerald-600 hover:bg-emerald-600 hover:text-white font-semibold text-xs transition-all">Baca Sekarang</a>
                    </div>
                </div>
                <?php endwhile; ?>
                
                <?php if(mysqli_num_rows($result_popular) == 0): ?>
                    <div class="col-span-full text-center py-12 text-gray-500">
                        <p>Belum ada buku yang diterbitkan.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Section: Explore Categories -->
    <section id="kategori" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center mb-12">
                <div class="mb-6 md:mb-0 text-center md:text-left">
                    <h2 class="text-3xl font-bold text-gray-900">Jelajahi Genre Favoritmu <i class="fa-solid fa-magnifying-glass text-emerald-600 text-2xl ml-2"></i></h2>
                    <p class="text-gray-500 mt-2">Temukan cerita yang sesuai dengan suasana hatimu</p>
                </div>
                <!-- Fixed: Changed button to 'a' tag -->
                <a href="page/user/buku.php" class="text-emerald-600 font-semibold hover:text-emerald-700 flex items-center gap-2 transition-colors">
                    Lihat Semua Genre <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <a href="page/user/buku.php?genre=Fantasi" class="flex flex-col items-center justify-center p-6 bg-white rounded-xl shadow-sm hover:shadow-md hover:scale-105 transition-all cursor-pointer border border-transparent hover:border-emerald-200 group">
                    <i class="fa-solid fa-dragon text-4xl mb-3 text-emerald-600 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-gray-700">Fantasi</span>
                </a>
                <a href="page/user/buku.php?genre=Misteri" class="flex flex-col items-center justify-center p-6 bg-white rounded-xl shadow-sm hover:shadow-md hover:scale-105 transition-all cursor-pointer border border-transparent hover:border-purple-200 group">
                    <i class="fa-solid fa-user-secret text-4xl mb-3 text-purple-600 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-gray-700">Misteri</span>
                </a>
                <a href="page/user/buku.php?genre=Romantis" class="flex flex-col items-center justify-center p-6 bg-white rounded-xl shadow-sm hover:shadow-md hover:scale-105 transition-all cursor-pointer border border-transparent hover:border-pink-200 group">
                    <i class="fa-solid fa-heart text-4xl mb-3 text-pink-500 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-gray-700">Romantis</span>
                </a>
                <a href="page/user/buku.php?genre=Horror" class="flex flex-col items-center justify-center p-6 bg-white rounded-xl shadow-sm hover:shadow-md hover:scale-105 transition-all cursor-pointer border border-transparent hover:border-red-200 group">
                    <i class="fa-solid fa-ghost text-4xl mb-3 text-red-600 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-gray-700">Horror</span>
                </a>
                <a href="page/user/buku.php?genre=Sci-Fi" class="flex flex-col items-center justify-center p-6 bg-white rounded-xl shadow-sm hover:shadow-md hover:scale-105 transition-all cursor-pointer border border-transparent hover:border-blue-200 group">
                    <i class="fa-solid fa-rocket text-4xl mb-3 text-blue-600 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-gray-700">Sci-Fi</span>
                </a>
                <a href="page/user/buku.php?genre=Komedi" class="flex flex-col items-center justify-center p-6 bg-white rounded-xl shadow-sm hover:shadow-md hover:scale-105 transition-all cursor-pointer border border-transparent hover:border-yellow-200 group">
                    <i class="fa-solid fa-face-laugh-squint text-4xl mb-3 text-yellow-500 group-hover:scale-110 transition-transform"></i>
                    <span class="font-medium text-gray-700">Komedi</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Section: Writer's Community (CTA) -->
    <section id="komunitas" class="py-24 relative overflow-hidden">
        <div class="absolute inset-0 bg-emerald-900">
            <div class="absolute inset-0 opacity-20" style="background-image: url('data:image/svg+xml,%3Csvg width=\'100\' height=\'100\' viewBox=\'0 0 100 100\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z\' fill=\'%23ffffff\' fill-opacity=\'0.1\' fill-rule=\'evenodd\'/%3E%3C/svg%3E');"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 flex flex-col md:flex-row items-center gap-12">
            <div class="md:w-1/2 text-center md:text-left">
                <span class="inline-block py-1 px-3 rounded-full bg-emerald-800 text-emerald-200 text-xs md:text-sm font-semibold mb-6">Komunitas Penulis Bertumbuh</span>
                <h2 class="text-3xl md:text-5xl font-bold text-white mb-6 leading-tight">Bergabung dengan <?php echo number_format($count_users); ?>+ Penulis Lainnya <i class="fa-solid fa-feather-pointed ml-2"></i></h2>
                <p class="text-emerald-100 text-base md:text-lg mb-8 leading-relaxed">Jangan menulis sendirian. Gunakan alat penulisan terbaik, pantau statistik pembaca, dan terbitkan karyamu untuk dunia.</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                    <a href="dashboard.php" class="bg-white text-emerald-900 py-3 px-8 rounded-full font-bold hover:bg-emerald-50 transition-all shadow-lg text-center">Gabung Sekarang Gratis</a>
                    <a href="#top-books" class="bg-transparent border border-emerald-400 text-white py-3 px-8 rounded-full font-bold hover:bg-emerald-800 transition-all text-center">Mulai Membaca</a>
                </div>
            </div>
            <div class="md:w-1/2 relative mt-10 md:mt-0">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-yellow-400 rounded-full blur-3xl opacity-20"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-4 md:mt-8">
                        <div class="bg-white/10 backdrop-blur-sm p-4 rounded-xl border border-white/10 hover:bg-white/20 transition-colors">
                            <div class="h-10 w-10 bg-blue-500 rounded-full mb-3 flex items-center justify-center text-white text-xl">
                                <i class="fa-solid fa-wand-magic-sparkles"></i>
                            </div>
                            <h4 class="text-white font-bold text-sm md:text-base">Editor Canggih</h4>
                            <p class="text-emerald-100 text-[10px] md:text-xs">Tulis ceritamu dengan tools lengkap.</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm p-4 rounded-xl border border-white/10 hover:bg-white/20 transition-colors">
                            <div class="h-10 w-10 bg-purple-500 rounded-full mb-3 flex items-center justify-center text-white text-xl">
                                <i class="fa-solid fa-book-open-reader"></i>
                            </div>
                            <h4 class="text-white font-bold text-sm md:text-base">Baca Tanpa Batas</h4>
                            <p class="text-emerald-100 text-[10px] md:text-xs">Akses ribuan cerita gratis.</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="bg-white/10 backdrop-blur-sm p-4 rounded-xl border border-white/10 hover:bg-white/20 transition-colors">
                            <div class="h-10 w-10 bg-pink-500 rounded-full mb-3 flex items-center justify-center text-white text-xl">
                                <i class="fa-solid fa-chart-line"></i>
                            </div>
                            <h4 class="text-white font-bold text-sm md:text-base">Statistik Pembaca</h4>
                            <p class="text-emerald-100 text-[10px] md:text-xs">Pantau performa karyamu.</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm p-4 rounded-xl border border-white/10 hover:bg-white/20 transition-colors">
                            <div class="h-10 w-10 bg-orange-500 rounded-full mb-3 flex items-center justify-center text-white text-xl">
                                <i class="fa-solid fa-bullhorn"></i>
                            </div>
                            <h4 class="text-white font-bold text-sm md:text-base">Publikasi Mudah</h4>
                            <p class="text-emerald-100 text-[10px] md:text-xs">Terbitkan karyamu dalam satu klik.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section: Newsletter / Stats -->
    <section class="py-12 md:py-20 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-emerald-50 rounded-3xl p-6 md:p-12 flex flex-col md:flex-row items-center justify-between gap-8 border border-emerald-100">
                <div class="md:w-2/3 text-center md:text-left">
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 mb-4">Statistik WebBuku Saat Ini <i class="fa-solid fa-chart-pie text-emerald-600 ml-2"></i></h2>
                    <p class="text-sm md:text-base text-gray-600 mb-0">Platform kami terus tumbuh setiap harinya. Jadilah bagian dari revolusi literasi digital Indonesia.</p>
                </div>
                <div class="md:w-1/3 w-full">
                    <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-emerald-100">
                        <div class="text-center w-1/3">
                            <span class="block text-xl md:text-2xl font-bold text-gray-900"><?php echo number_format($count_users); ?></span>
                            <span class="text-[10px] md:text-xs text-gray-400 uppercase tracking-wider font-semibold">Pengguna</span>
                        </div>
                        <div class="w-px h-10 bg-gray-200"></div>
                        <div class="text-center w-1/3">
                            <span class="block text-xl md:text-2xl font-bold text-gray-900"><?php echo number_format($count_books); ?></span>
                            <span class="text-[10px] md:text-xs text-gray-400 uppercase tracking-wider font-semibold">Cerita</span>
                        </div>
                        <div class="w-px h-10 bg-gray-200"></div>
                        <div class="text-center w-1/3">
                            <span class="block text-xl md:text-2xl font-bold text-gray-900"><?php echo number_format($sum_views); ?></span>
                            <span class="text-[10px] md:text-xs text-gray-400 uppercase tracking-wider font-semibold">Dibaca</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php include 'layouts/footer.php'; ?>  
</body>
</html>

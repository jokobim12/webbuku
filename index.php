<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WebBuku - Buat Cerita Impianmu</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
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

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden hero-pattern">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-4xl mx-auto">
                <span class="inline-block py-1 px-3 rounded-full bg-emerald-100 text-emerald-700 text-sm font-semibold mb-6 animate-fade-in-up">Platform Penulis Masa Depan</span>
                <h1 class="text-5xl md:text-7xl font-extrabold text-gray-900 tracking-tight mb-8 leading-tight">
                    Tuangkan Imajinasi,<br>
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 to-teal-600">Terbitkan Karyamu</span>
                </h1>
                <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-500 mb-10 leading-relaxed">
                    WebBuku adalah tempat di mana setiap kata bermakna. Mulai tulis novel atau cerita pendekmu hari ini dengan alat penulisan canggih dan komunitas yang mendukung.
                </p>
                <div class="flex justify-center gap-4">
                    <a href="page/user/kelola_cerita.php" class="bg-gray-900 text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-gray-800 transition-all shadow-xl hover:shadow-2xl transform hover:-translate-y-1">
                        Mulai Menulis ✍️
                    </a>
                    <a href="page/user/buku.php" class="bg-white text-gray-700 border border-gray-200 px-8 py-4 rounded-xl font-bold text-lg hover:bg-gray-50 transition-all hover:border-gray-300">
                        Lihat Buku
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
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2">Buku Paling Populer Minggu Ini 📚</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Book Card 1 -->
                <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                    <div class="relative h-64 bg-gray-200 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1544947950-fa07a98d237f?auto=format&fit=crop&q=80&w=800" alt="Book Cover" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                            <span class="text-white font-medium text-sm">Dibaca 12k kali</span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="bg-emerald-100 text-emerald-800 text-xs px-2 py-1 rounded-full font-semibold">Fantasi</span>
                            <span class="text-yellow-400 text-xs flex items-center">★ 4.8</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1 line-clamp-1 group-hover:text-emerald-600 transition-colors">Menembus Langit Ke-7</h3>
                        <p class="text-gray-500 text-sm mb-4 line-clamp-2">Petualangan seorang anak desa yang menemukan pintu rahasia menuju kerajaan awan.</p>
                        <a href="page/user/buku.php" class="block w-full text-center py-2 rounded-lg border border-emerald-600 text-emerald-600 hover:bg-emerald-600 hover:text-white font-semibold text-sm transition-all">Baca Sekarang</a>
                    </div>
                </div>

                <!-- Book Card 2 -->
                <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                    <div class="relative h-64 bg-gray-200 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1543002588-6e92e7dccf8c?auto=format&fit=crop&q=80&w=800" alt="Book Cover" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full font-semibold">Misteri</span>
                            <span class="text-yellow-400 text-xs flex items-center">★ 4.9</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1 line-clamp-1 group-hover:text-emerald-600 transition-colors">Hilang di Hutan Larangan</h3>
                        <p class="text-gray-500 text-sm mb-4 line-clamp-2">Detektif muda mencoba memecahkan kasus hilangnya pendaki secara misterius.</p>
                        <a href="page/user/buku.php" class="block w-full text-center py-2 rounded-lg border border-emerald-600 text-emerald-600 hover:bg-emerald-600 hover:text-white font-semibold text-sm transition-all">Baca Sekarang</a>
                    </div>
                </div>

                <!-- Book Card 3 -->
                <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                    <div class="relative h-64 bg-gray-200 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1532012197267-da84d127e765?auto=format&fit=crop&q=80&w=800" alt="Book Cover" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="bg-pink-100 text-pink-800 text-xs px-2 py-1 rounded-full font-semibold">Romantis</span>
                            <span class="text-yellow-400 text-xs flex items-center">★ 4.7</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1 line-clamp-1 group-hover:text-emerald-600 transition-colors">Senja di Kuta</h3>
                        <p class="text-gray-500 text-sm mb-4 line-clamp-2">Kisah cinta yang tumbuh di antara deburan ombak dan pasir putih Bali.</p>
                        <a href="page/user/buku.php" class="block w-full text-center py-2 rounded-lg border border-emerald-600 text-emerald-600 hover:bg-emerald-600 hover:text-white font-semibold text-sm transition-all">Baca Sekarang</a>
                    </div>
                </div>

                <!-- Book Card 4 -->
                <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-2 overflow-hidden">
                    <div class="relative h-64 bg-gray-200 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1629198688000-71f23e745b6e?auto=format&fit=crop&q=80&w=800" alt="Book Cover" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    </div>
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full font-semibold">Sci-Fi</span>
                            <span class="text-yellow-400 text-xs flex items-center">★ 4.6</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mb-1 line-clamp-1 group-hover:text-emerald-600 transition-colors">Kode 2045</h3>
                        <p class="text-gray-500 text-sm mb-4 line-clamp-2">Dunia masa depan di mana manusia hidup berdampingan dengan AI yang memiliki perasaan.</p>
                        <a href="page/user/buku.php" class="block w-full text-center py-2 rounded-lg border border-emerald-600 text-emerald-600 hover:bg-emerald-600 hover:text-white font-semibold text-sm transition-all">Baca Sekarang</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- New Section 1: Explore Categories -->
    <section id="kategori" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center mb-12">
                <div class="mb-6 md:mb-0">
                    <h2 class="text-3xl font-bold text-gray-900">Jelajahi Genre Favoritmu 🔍</h2>
                    <p class="text-gray-500 mt-2">Temukan cerita yang sesuai dengan suasana hatimu</p>
                </div>
                <button class="text-emerald-600 font-semibold hover:text-emerald-700 flex items-center gap-2">
                    Lihat Semua Genre <span class="text-xl">→</span>
                </button>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <a href="#" class="flex flex-col items-center justify-center p-6 bg-white rounded-xl shadow-sm hover:shadow-md hover:scale-105 transition-all cursor-pointer border border-transparent hover:border-emerald-200">
                    <span class="text-4xl mb-3">🏰</span>
                    <span class="font-medium text-gray-700">Fantasi</span>
                </a>
                <a href="#" class="flex flex-col items-center justify-center p-6 bg-white rounded-xl shadow-sm hover:shadow-md hover:scale-105 transition-all cursor-pointer border border-transparent hover:border-purple-200">
                    <span class="text-4xl mb-3">🕵️‍♂️</span>
                    <span class="font-medium text-gray-700">Misteri</span>
                </a>
                <a href="#" class="flex flex-col items-center justify-center p-6 bg-white rounded-xl shadow-sm hover:shadow-md hover:scale-105 transition-all cursor-pointer border border-transparent hover:border-pink-200">
                    <span class="text-4xl mb-3">💕</span>
                    <span class="font-medium text-gray-700">Romantis</span>
                </a>
                <a href="#" class="flex flex-col items-center justify-center p-6 bg-white rounded-xl shadow-sm hover:shadow-md hover:scale-105 transition-all cursor-pointer border border-transparent hover:border-red-200">
                    <span class="text-4xl mb-3">👻</span>
                    <span class="font-medium text-gray-700">Horror</span>
                </a>
                <a href="#" class="flex flex-col items-center justify-center p-6 bg-white rounded-xl shadow-sm hover:shadow-md hover:scale-105 transition-all cursor-pointer border border-transparent hover:border-blue-200">
                    <span class="text-4xl mb-3">🚀</span>
                    <span class="font-medium text-gray-700">Sci-Fi</span>
                </a>
                <a href="#" class="flex flex-col items-center justify-center p-6 bg-white rounded-xl shadow-sm hover:shadow-md hover:scale-105 transition-all cursor-pointer border border-transparent hover:border-yellow-200">
                    <span class="text-4xl mb-3">😂</span>
                    <span class="font-medium text-gray-700">Komedi</span>
                </a>
            </div>
        </div>
    </section>

    <!-- New Section 2: Writer's Community (CTA) -->
    <section id="komunitas" class="py-24 relative overflow-hidden">
        <div class="absolute inset-0 bg-emerald-900">
            <div class="absolute inset-0 opacity-20" style="background-image: url('data:image/svg+xml,%3Csvg width=\'100\' height=\'100\' viewBox=\'0 0 100 100\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z\' fill=\'%23ffffff\' fill-opacity=\'0.1\' fill-rule=\'evenodd\'/%3E%3C/svg%3E');"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 flex flex-col md:flex-row items-center gap-12">
            <div class="md:w-1/2">
                <span class="inline-block py-1 px-3 rounded-full bg-emerald-800 text-emerald-200 text-sm font-semibold mb-6">Komunitas Penulis #1</span>
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-6 leading-tight">Bergabung dengan 10,000+ Penulis Lainnya ✍️</h2>
                <p class="text-emerald-100 text-lg mb-8 leading-relaxed">Jangan menulis sendirian. Dapatkan feedback, ikut kompetisi mingguan, dan bangun audiensmu dari nol bersama komunitas WebBuku.</p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="auth/auth_google.php" class="bg-white text-emerald-900 py-3 px-8 rounded-full font-bold hover:bg-emerald-50 transition-all shadow-lg text-center">Gabyng Sekarang Gratis</a>
                    <a href="#" class="bg-transparent border border-emerald-400 text-white py-3 px-8 rounded-full font-bold hover:bg-emerald-800 transition-all text-center">Lihat Diskusi</a>
                </div>
            </div>
            <div class="md:w-1/2 relative">
                <div class="absolute -top-10 -right-10 w-32 h-32 bg-yellow-400 rounded-full blur-3xl opacity-20"></div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-4 mt-8">
                        <div class="bg-white/10 backdrop-blur-sm p-4 rounded-xl border border-white/10">
                            <div class="h-10 w-10 bg-blue-500 rounded-full mb-3 flex items-center justify-center text-white text-xl">💡</div>
                            <h4 class="text-white font-bold">Tips Menulis</h4>
                            <p class="text-emerald-100 text-xs">Artikel harian untuk upgrade skillmu.</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm p-4 rounded-xl border border-white/10">
                            <div class="h-10 w-10 bg-purple-500 rounded-full mb-3 flex items-center justify-center text-white text-xl">🏆</div>
                            <h4 class="text-white font-bold">Kompetisi</h4>
                            <p class="text-emerald-100 text-xs">Hadiah jutaan rupiah setiap bulan.</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="bg-white/10 backdrop-blur-sm p-4 rounded-xl border border-white/10">
                            <div class="h-10 w-10 bg-pink-500 rounded-full mb-3 flex items-center justify-center text-white text-xl">❤️</div>
                            <h4 class="text-white font-bold">Dukungan</h4>
                            <p class="text-emerald-100 text-xs">Saling support antar sesama penulis.</p>
                        </div>
                        <div class="bg-white/10 backdrop-blur-sm p-4 rounded-xl border border-white/10">
                            <div class="h-10 w-10 bg-orange-500 rounded-full mb-3 flex items-center justify-center text-white text-xl">📢</div>
                            <h4 class="text-white font-bold">Promosi</h4>
                            <p class="text-emerald-100 text-xs">Fitur untuk promosikan karyamu.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- New Section 3: Newsletter / Stats -->
    <section class="py-20 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-emerald-50 rounded-3xl p-8 md:p-12 flex flex-col md:flex-row items-center justify-between gap-8 border border-emerald-100">
                <div class="md:w-2/3">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Statistik WebBuku Saat Ini 📈</h2>
                    <p class="text-gray-600 mb-0">Platform kami terus tumbuh setiap harinya. Jadilah bagian dari revolusi literasi digital Indonesia.</p>
                </div>
                <div class="md:w-1/3 w-full">
                    <div class="flex justify-between items-center bg-white p-6 rounded-2xl shadow-sm border border-emerald-100">
                        <div class="text-center">
                            <span class="block text-2xl font-bold text-gray-900">50k+</span>
                            <span class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Pengguna</span>
                        </div>
                        <div class="w-px h-10 bg-gray-200"></div>
                        <div class="text-center">
                            <span class="block text-2xl font-bold text-gray-900">12k+</span>
                            <span class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Cerita</span>
                        </div>
                        <div class="w-px h-10 bg-gray-200"></div>
                        <div class="text-center">
                            <span class="block text-2xl font-bold text-gray-900">1M+</span>
                            <span class="text-xs text-gray-400 uppercase tracking-wider font-semibold">Pembaca</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php include 'layouts/footer.php'; ?>  
</body>
</html>

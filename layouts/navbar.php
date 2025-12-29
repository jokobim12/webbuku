<?php
// Navbar Component
?>
<!-- Navbar -->
<nav class="bg-white/90 dark:bg-gray-900/90 backdrop-blur-md fixed w-full z-50 border-b border-gray-100 dark:border-gray-800 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20 items-center gap-4">
            <!-- Left: Logo -->
            <div class="flex-shrink-0 flex items-center gap-2">
                <a href="/index.php" class="flex items-center gap-2">
                    <div class="">
                        <img src="/assets/logo.jpg" alt="WebBuku Logo" class="w-10 h-10 object-cover rounded-md">
                    </div>
                    <span class="font-bold text-2xl tracking-tight text-gray-900 dark:text-white hidden sm:block">WebBuku</span>
                </a>
            </div>

            <!-- Center: Search Bar (Desktop) -->
            <div class="hidden md:flex flex-1 max-w-lg mx-4 relative">
                <div class="relative w-full">
                    <input type="text" id="desktop-search" placeholder="Cari judul, penulis, atau genre..." 
                           class="w-full bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-900 dark:text-gray-100 text-sm rounded-full focus:ring-emerald-500 focus:border-emerald-500 block pl-10 p-2.5 transition-colors"
                           autocomplete="off">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <!-- Search Dropdown Results -->
                    <div id="desktop-search-results" class="absolute z-50 w-full bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-100 dark:border-gray-700 mt-2 hidden max-h-96 overflow-y-auto">
                        <!-- Results injected here via JS -->
                    </div>
                </div>
            </div>

            <!-- Right: Menu & User -->
            <div class="flex items-center gap-2 sm:gap-4">
                <!-- Theme Toggle -->
                <button id="theme-toggle" type="button" class="text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                    <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path></svg>
                    <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>
                </button>
                <!-- Notification Bell -->
                <?php if (isset($_SESSION['user_id'])): ?>
                <div class="relative group" id="notif-dropdown-container">
                    <button id="notif-btn" class="relative text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 rounded-lg text-sm p-2.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span id="notif-badge" class="absolute top-2 right-2 flex h-2 w-2 hidden">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                        </span>
                    </button>
                    <!-- Dropdown -->
                    <div id="notif-dropdown" class="fixed sm:absolute top-20 sm:top-auto right-4 sm:right-0 left-4 sm:left-auto mt-2 sm:mt-2 w-[calc(100%-2rem)] sm:w-80 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 hidden z-[80] overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifikasi</h3>
                            <button id="mark-read-btn" class="text-xs text-emerald-600 hover:text-emerald-700 font-medium hidden">Tandai sudah dibaca</button>
                        </div>
                        <div id="notif-list" class="max-h-80 overflow-y-auto">
                            <!-- Items injected via JS -->
                            <div class="p-4 text-center text-gray-500 text-sm">Memuat...</div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <div class="hidden lg:flex items-center gap-6">
                    <a href="/index.php" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Beranda</a>
                    <a href="/page/user/buku.php" class="text-sm font-medium text-gray-700 dark:text-gray-300 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">Jelajah</a>
                </div>

                <!-- Profile/Login -->
                <?php if (isset($_SESSION['user_id'])): ?>
                <div class="relative group">
                    <button class="flex items-center gap-2 focus:outline-none">
                        <?php 
                            $avatar = !empty($_SESSION['avatar']) ? (strpos($_SESSION['avatar'], 'http') === 0 ? $_SESSION['avatar'] : '/' . ltrim($_SESSION['avatar'], '/')) : 'https://ui-avatars.com/api/?name='.urlencode($_SESSION['name']).'&background=random';
                        ?>
                        <img src="<?php echo $avatar; ?>" alt="Avatar" class="w-9 h-9 rounded-full border border-gray-200 dark:border-gray-700 object-cover">
                        <span class="hidden lg:block text-sm font-medium text-gray-700 dark:text-gray-300 max-w-[100px] truncate"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                        <svg class="w-4 h-4 text-gray-400 hidden lg:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <!-- Dropdown -->
                    <div class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-100 dark:border-gray-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform group-hover:translate-y-0 translate-y-2 z-50">
                        <div class="p-2 space-y-1">
                            <div class="px-3 py-2 border-b border-gray-100 dark:border-gray-700 lg:hidden">
                                <p class="text-sm font-semibold text-gray-900 dark:text-white truncate"><?php echo htmlspecialchars($_SESSION['name']); ?></p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate"><?php echo htmlspecialchars($_SESSION['email']); ?></p>
                            </div>
                            <a href="/dashboard.php" class="flex items-center gap-3 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 hover:text-emerald-600 dark:hover:text-emerald-400 rounded-lg transition-colors">
                                <i class="fa-solid fa-gauge-high w-5 text-center"></i> Dashboard
                            </a>
                            <a href="/page/user/pustaka.php" class="flex items-center gap-3 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 hover:text-emerald-600 dark:hover:text-emerald-400 rounded-lg transition-colors">
                                <i class="fa-solid fa-bookmark w-5 text-center"></i> Pustaka Saya
                            </a>
                            <a href="/page/user/karyaku.php" class="flex items-center gap-3 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 hover:text-emerald-600 dark:hover:text-emerald-400 rounded-lg transition-colors">
                                <i class="fa-solid fa-pen-fancy w-5 text-center"></i> Tulis Cerita
                            </a>
                            <div class="border-t border-gray-100 dark:border-gray-700 my-1"></div>
                            <a href="/auth/logout.php" onclick="confirmLogout(event, this.href)" class="flex items-center gap-3 px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors">
                                <i class="fa-solid fa-right-from-bracket w-5 text-center"></i> Keluar
                            </a>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <a href="/auth/auth_google.php" class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2 rounded-full text-sm font-medium transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5 whitespace-nowrap">
                    Masuk
                </a>
                <?php endif; ?>

                <!-- Mobile Menu Button -->
                <div class="flex items-center lg:hidden ml-2">
                    <button id="mobile-menu-btn" class="p-2 rounded-lg text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- Mobile Menu Overlay (Backdrop) -->
<div id="mobile-menu-backdrop" class="fixed inset-0 bg-black/50 z-[60] hidden transition-opacity duration-300 backdrop-blur-sm"></div>

<!-- Mobile Menu (Side Drawer) -->
<div id="mobile-menu" class="fixed inset-y-0 right-0 w-64 bg-white shadow-2xl z-[70] transform translate-x-full transition-transform duration-300 ease-in-out md:hidden flex flex-col">
    <!-- Close Button -->
    <div class="flex items-center justify-end p-4 border-b border-gray-100">
        <button id="close-menu-btn" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition-colors">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
        </button>
    </div>

    <div class="flex-1 overflow-y-auto px-4 py-4 space-y-1">
        <a href="/#" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 font-medium transition-colors">Beranda</a>
        <a href="/#top-books" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 font-medium transition-colors">Buku</a>
        <a href="/#kategori" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 font-medium transition-colors">Genre</a>
        <a href="/#komunitas" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 font-medium transition-colors">Komunitas</a>
        
        <div class="border-t border-gray-100 my-4 pt-4">
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="flex items-center gap-3 px-4 mb-4">
                    <?php 
                        $avatar = !empty($_SESSION['avatar']) ? (strpos($_SESSION['avatar'], 'http') === 0 ? $_SESSION['avatar'] : '/' . ltrim($_SESSION['avatar'], '/')) : 'https://ui-avatars.com/api/?name='.urlencode($_SESSION['name']).'&background=random';
                    ?>
                    <img src="<?php echo $avatar; ?>" alt="Avatar" class="w-10 h-10 rounded-full border border-emerald-100 object-cover">
                    <div class="overflow-hidden">
                        <div class="font-medium text-gray-900 truncate"><?php echo htmlspecialchars($_SESSION['name']); ?></div>
                        <div class="text-xs text-emerald-600">Sedang Login</div>
                    </div>
                </div>
                <a href="/dashboard.php" class="block px-4 py-3 rounded-lg text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 font-medium transition-colors">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Dashboard
                    </span>
                </a>
                <a href="/auth/logout.php" onclick="confirmLogout(event, this.href)" class="block px-4 py-3 rounded-lg text-red-600 hover:bg-red-50 font-medium transition-colors">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Keluar
                    </span>
                </a>
            <?php else: ?>
                <a href="/auth/auth_google.php" class="block w-full text-center bg-emerald-600 text-white px-4 py-3 rounded-xl font-bold hover:bg-emerald-700 transition-all transform hover:-translate-y-0.5">
                    Masuk
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btn = document.getElementById('mobile-menu-btn');
        const closeBtn = document.getElementById('close-menu-btn');
        const menu = document.getElementById('mobile-menu');
        const backdrop = document.getElementById('mobile-menu-backdrop');
        
        function openMenu() {
            menu.classList.remove('translate-x-full');
            backdrop.classList.remove('hidden');
        }

        function closeMenu() {
            menu.classList.add('translate-x-full');
            backdrop.classList.add('hidden');
        }

        if(btn) btn.addEventListener('click', openMenu);
        if(closeBtn) closeBtn.addEventListener('click', closeMenu);
        if(backdrop) backdrop.addEventListener('click', closeMenu);
    });
</script>
<script src="/assets/js/theme.js"></script>
<script src="/assets/js/api/search.js"></script>
<script src="/assets/js/api/notification.js"></script>


<?php include 'confirmation_modal.php'; ?>
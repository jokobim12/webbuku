<?php
// Navbar Component
?>
<!-- Navbar -->
<nav class="bg-white/90 backdrop-blur-md fixed w-full z-50 border-b border-gray-100 transition-all duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-20 items-center">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center gap-2">
                <div class="bg-emerald-600 p-2 rounded-lg rotate-3 hover:rotate-0 transition-transform duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <span class="font-bold text-2xl tracking-tight text-gray-900">WebBuku</span>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:flex space-x-8 items-center">
                <a href="/#" class="text-gray-500 hover:text-emerald-600 font-medium transition-colors">Beranda</a>
                <a href="/#top-books" class="text-gray-500 hover:text-emerald-600 font-medium transition-colors">Buku</a>
                <a href="/#kategori" class="text-gray-500 hover:text-emerald-600 font-medium transition-colors">Genre</a>
                <a href="/#komunitas" class="text-gray-500 hover:text-emerald-600 font-medium transition-colors">Komunitas</a>
            </div>

            <!-- Desktop Profile / Login -->
            <div class="hidden md:flex items-center">
                <?php if (isset($_SESSION['user_id'])): ?>
                <div class="relative group">
                    <button class="flex items-center gap-3 focus:outline-none">
                        <span class="hidden md:block font-medium text-gray-700"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                        <img src="<?php echo $_SESSION['avatar']; ?>" alt="Avatar" class="w-10 h-10 rounded-full border-2 border-emerald-100 object-cover">
                    </button>
                    <!-- Dropdown -->
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform group-hover:translate-y-0 translate-y-2 z-50">
                        <div class="p-2">
                            <a href="/dashboard.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-emerald-600 rounded-lg transition-colors">Dashboard</a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <a href="/auth/logout.php" onclick="confirmLogout(event, this.href)" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors">Keluar</a>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <a href="/auth/auth_google.php" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2.5 rounded-full font-medium transition-all shadow-lg hover:shadow-emerald-500/30 transform hover:-translate-y-0.5">
                    Masuk / Daftar
                </a>
                <?php endif; ?>
            </div>

            <!-- Mobile Menu Button -->
            <div class="flex items-center md:hidden">
                <button id="mobile-menu-btn" class="p-2 rounded-lg text-gray-600 hover:bg-gray-100 focus:outline-none transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
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
                    <img src="<?php echo $_SESSION['avatar']; ?>" alt="Avatar" class="w-10 h-10 rounded-full border border-emerald-100 object-cover">
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
                <a href="/auth/auth_google.php" class="block w-full text-center bg-emerald-600 text-white px-4 py-3 rounded-xl font-bold shadow-lg shadow-emerald-200 hover:bg-emerald-700 hover:shadow-emerald-300 transition-all transform hover:-translate-y-0.5">
                    Masuk / Daftar
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

<?php include 'confirmation_modal.php'; ?>
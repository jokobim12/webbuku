<?php
session_start();
require_once '../../database/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle Delete Request (Updated)
if (isset($_POST['delete_id'])) {
    $delete_id = mysqli_real_escape_string($koneksi, $_POST['delete_id']);
    // Verify ownership
    $delete_query = "DELETE FROM books WHERE id = '$delete_id' AND user_id = '$user_id'";
    
    if (mysqli_query($koneksi, $delete_query)) {
        header("Location: karyaku.php?status=deleted");
        exit();
    } else {
        echo "Error: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Karyaku - WebBuku</title>
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
    
    <!-- Sidebar -->
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

        <nav class="p-4 space-y-1 overflow-y-auto h-[calc(100vh-4rem)]">
            <a href="../../dashboard.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-lg font-medium transition-colors">
                <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                Dashboard
            </a>
            
            <a href="tulis_cerita.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-lg font-medium transition-colors">
                 <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                Tulis Cerita
            </a>
            
            <a href="pengaturan.php" class="flex items-center px-4 py-3 text-gray-600 hover:bg-gray-50 hover:text-gray-900 rounded-lg font-medium transition-colors">
                <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                Pengaturan
            </a>

            <a href="karyaku.php" class="flex items-center px-4 py-3 bg-emerald-50 text-emerald-700 rounded-lg font-medium transition-colors">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
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

    <!-- Main Content -->
    <div class="md:ml-64 min-h-screen flex flex-col">
        
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8">
            <button class="md:hidden p-2 text-gray-600 rounded-lg hover:bg-gray-100" onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>

            <div class="flex-1 px-4">
               <h1 class="text-lg font-semibold text-gray-800">Karyaku</h1>
            </div>

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
                    <a href="pengaturan.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 first:rounded-t-lg">
                        Edit Profil
                    </a>
                    <a href="../../auth/logout.php" onclick="confirmLogout(event, this.href)" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 last:rounded-b-lg">
                        Keluar
                    </a>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="flex-1 p-6 lg:p-8">
            
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-bold text-gray-900">Daftar Buku Saya</h2>
                <a href="tulis_cerita.php" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Tulis Baru
                </a>
            </div>

            <?php if (isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Berhasil</p>
                    <p>Buku berhasil dihapus.</p>
                </div>
            <?php endif; ?>

            <?php
            $query = "SELECT * FROM books WHERE user_id = '$user_id' ORDER BY created_at DESC";
            $result = mysqli_query($koneksi, $query);

            if (mysqli_num_rows($result) > 0) : ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    <?php while ($book = mysqli_fetch_assoc($result)) : ?>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow group">
                            <div class="h-48 overflow-hidden bg-gray-100 relative">
                                <?php if ($book['cover_image']) : ?>
                                    <img src="../../<?php echo htmlspecialchars($book['cover_image']); ?>" alt="Cover" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <?php else : ?>
                                    <div class="flex items-center justify-center h-full text-gray-400">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                <?php endif; ?>
                                <div class="absolute top-2 right-2 flex gap-1">
                                    <span class="bg-white/90 backdrop-blur-sm px-2 py-1 rounded text-xs font-semibold 
                                        <?php echo $book['status'] == 'published' ? 'text-emerald-600' : 'text-amber-600'; ?>">
                                        <?php echo ucfirst($book['status']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="p-4">
                                <h3 class="font-bold text-gray-900 mb-1 line-clamp-1"><?php echo htmlspecialchars($book['title']); ?></h3>
                                <p class="text-gray-500 text-sm mb-4 line-clamp-2"><?php echo htmlspecialchars($book['synopsis']); ?></p>
                                
                                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                                    <a href="kelola_cerita.php?id=<?php echo $book['id']; ?>" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium">Kelola</a>
                                    
                                    <form method="POST" class="inline delete-form-book" onsubmit="return confirmDeleteBook(event)">
                                        <input type="hidden" name="delete_id" value="<?php echo $book['id']; ?>">
                                        <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-medium">Hapus</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else : ?>
                <div class="text-center py-12 bg-white rounded-xl border border-gray-200 border-dashed">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <p class="text-gray-500 mb-4">Belum ada cerita yang ditulis.</p>
                    <a href="tulis_cerita.php" class="text-emerald-600 font-medium hover:text-emerald-700">Mulai Menulis</a>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <?php include '../../layouts/confirmation_modal.php'; ?>
    <script>
        function confirmDeleteBook(e) {
            e.preventDefault();
            const form = e.target;
            window.ConfirmModal.show(
                'Hapus Buku',
                'Apakah anda yakin ingin menghapus buku ini? Semua bab dan data terkait akan dihapus secara permanen.',
                () => {
                    form.submit();
                }
            );
            return false;
        }
    </script>
</body>
</html>

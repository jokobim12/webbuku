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
    
    <?php include '../../layouts/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="md:ml-64 min-h-screen flex flex-col">
        
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8">
            <button class="md:hidden p-2 text-gray-600 rounded-lg hover:bg-gray-100" onclick="toggleSidebar()">
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
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-20">Cover</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Info Cerita</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Dilihat</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">Status</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider w-40">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php while ($book = mysqli_fetch_assoc($result)) : ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="w-12 h-16 bg-gray-100 rounded overflow-hidden flex-shrink-0 border border-gray-200">
                                                <?php if ($book['cover_image']) : ?>
                                                    <img src="../../<?php echo htmlspecialchars($book['cover_image']); ?>" class="w-full h-full object-cover" alt="Cover">
                                                <?php else : ?>
                                                    <div class="w-full h-full flex items-center justify-center text-gray-400 text-xs font-bold">PDF</div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="max-w-md">
                                                <div class="text-sm font-bold text-gray-900 mb-1"><?php echo htmlspecialchars($book['title']); ?></div>
                                                <div class="text-xs text-gray-500 line-clamp-2"><?php echo htmlspecialchars($book['synopsis']); ?></div>
                                                <div class="mt-1 text-xs text-emerald-600 font-medium bg-emerald-50 inline-block px-2 py-0.5 rounded-full">
                                                    <?php echo htmlspecialchars($book['genre']); ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center text-gray-500 text-sm">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                <span class="font-medium"><?php echo number_format($book['views'] ?? 0); ?></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $book['status'] == 'published' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                                <?php echo ucfirst($book['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex items-center justify-end gap-3">
                                                <a href="kelola_cerita.php?id=<?php echo $book['id']; ?>" class="text-emerald-600 hover:text-emerald-900 border border-emerald-200 bg-emerald-50 hover:bg-emerald-100 px-3 py-1 rounded-lg transition-colors">
                                                    Kelola
                                                </a>
                                                <form method="POST" class="inline delete-form-book" onsubmit="return confirmDeleteBook(event)">
                                                    <input type="hidden" name="delete_id" value="<?php echo $book['id']; ?>">
                                                    <button type="submit" class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-1.5 rounded-lg transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
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

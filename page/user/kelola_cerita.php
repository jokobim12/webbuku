<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header('Location: ../../auth/login.php');
    exit();
}

require_once '../../database/koneksi.php';

$book_id = mysqli_real_escape_string($koneksi, $_GET['id']);
$user_id = $_SESSION['user_id'];

// Verify Book Ownership
$query_book = "SELECT * FROM books WHERE id = '$book_id' AND user_id = '$user_id'";
$result_book = mysqli_query($koneksi, $query_book);
$book = mysqli_fetch_assoc($result_book);

if (!$book) {
    echo "Buku tidak ditemukan atau Anda tidak memiliki akses.";
    exit();
}

// Fetch Chapters
$query_chapters = "SELECT * FROM chapters WHERE book_id = '$book_id' ORDER BY created_at ASC";
$result_chapters = mysqli_query($koneksi, $query_chapters);

// Handle Delete Chapter
if(isset($_POST['delete_chapter_id'])) {
    $chap_id = mysqli_real_escape_string($koneksi, $_POST['delete_chapter_id']);
    mysqli_query($koneksi, "DELETE FROM chapters WHERE id='$chap_id' AND book_id='$book_id'");
    header("Location: kelola_cerita.php?id=$book_id");
    exit();
}

// Handle Publish/Unpublish Book
if(isset($_POST['toggle_status'])) {
    $new_status = $book['status'] == 'draft' ? 'published' : 'draft';
    mysqli_query($koneksi, "UPDATE books SET status='$new_status' WHERE id='$book_id'");
    header("Location: kelola_cerita.php?id=$book_id");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Cerita: <?php echo htmlspecialchars($book['title']); ?></title>
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
    
    <!-- Sidebar Removed as per user request -->

    <div class="min-h-screen flex flex-col">
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8">
            <div class="flex items-center gap-4">
                <a href="karyaku.php" class="p-2 -ml-2 text-gray-600 hover:bg-gray-100 rounded-lg group transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <span class="hidden sm:inline font-medium">Kembali</span>
                </a>
                <div class="h-6 w-px bg-gray-200 mx-2"></div>
                <h1 class="text-lg font-semibold text-gray-800">Daftar Isi</h1>
                <span class="px-2 py-0.5 <?php echo $book['status'] == 'published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; ?> rounded-full text-xs font-bold uppercase tracking-wide">
                    <?php echo htmlspecialchars($book['status']); ?>
                </span>
            </div>
             <div class="flex items-center gap-3">
                <span class="hidden md:block text-sm font-medium text-gray-700"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                <img src="<?php echo htmlspecialchars($_SESSION['avatar']); ?>" class="w-9 h-9 rounded-full border border-gray-200 object-cover">
            </div>
        </header>

        <main class="flex-1 p-8">
            
            <!-- Book Info Card -->
            <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex flex-col md:flex-row items-center md:items-start gap-6 mb-8">
                <div class="w-24 h-36 bg-gray-200 rounded flex-shrink-0 overflow-hidden relative">
                     <?php if($book['cover_image']): ?>
                        <img src="../../<?php echo htmlspecialchars($book['cover_image']); ?>" class="w-full h-full object-cover">
                     <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400 font-bold">PDF</div>
                     <?php endif; ?>
                </div>
                <div class="flex-1 w-full text-center md:text-left">
                    <h2 class="text-2xl font-bold text-gray-900 mb-1"><?php echo htmlspecialchars($book['title']); ?></h2>
                    <span class="px-2 py-0.5 bg-emerald-100 text-emerald-700 text-xs rounded-full font-medium inline-block"><?php echo htmlspecialchars($book['genre']); ?></span>
                    <p class="text-gray-500 mt-3 text-sm line-clamp-2"><?php echo htmlspecialchars($book['synopsis']); ?></p>
                </div>
                <div class="flex flex-col gap-3 w-full md:w-auto">
                    <a href="tulis_cerita.php?id=<?php echo $book['id']; ?>" class="w-full md:w-auto px-6 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-lg hover:bg-gray-50 shadow-sm transition-colors inline-flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        Edit Info Cerita
                    </a>

                     <a href="tulis_bab.php?book_id=<?php echo $book['id']; ?>" class="w-full md:w-auto px-6 py-2.5 bg-emerald-600 text-white font-bold rounded-lg hover:bg-emerald-700 shadow-md transition-colors inline-flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Bagian Baru
                    </a>
                    
                    <form method="POST" class="w-full">
                        <input type="hidden" name="toggle_status" value="1">
                        <?php if($book['status'] == 'draft'): ?>
                        <button type="submit" class="w-full px-6 py-2.5 bg-gray-900 text-white font-bold rounded-lg hover:bg-gray-800 shadow-md transition-colors inline-flex items-center justify-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Publikasikan Cerita
                        </button>
                        <?php else: ?>
                        <button type="submit" class="w-full px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-50 shadow-sm transition-colors inline-flex items-center justify-center gap-2" onclick="return confirm('Kembalikan cerita ke Draft? Cerita tidak akan muncul di halaman publik.')">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Unpublish (Draft)
                        </button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>

            <!-- Chapters List -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                    <h3 class="font-bold text-gray-800">Daftar Bab / Bagian</h3>
                </div>
                
                <?php if (mysqli_num_rows($result_chapters) > 0): ?>
                <table class="w-full text-left">
                    <tbody class="divide-y divide-gray-100">
                        <?php $i=1; while($chap = mysqli_fetch_assoc($result_chapters)): ?>
                        <tr class="group hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 w-16 text-gray-400 font-mono text-sm"><?php echo $i++; ?></td>
                            <td class="px-6 py-4">
                                <a href="tulis_bab.php?book_id=<?php echo $book_id; ?>&chapter_id=<?php echo $chap['id']; ?>" class="font-medium text-gray-800 group-hover:text-emerald-600 transition-colors text-lg">
                                    <?php echo htmlspecialchars($chap['title']); ?>
                                </a>
                                <p class="text-xs text-gray-400 mt-1">Terakhir diubah: <?php echo date('d M Y H:i', strtotime($chap['updated_at'])); ?></p>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="px-2 py-1 <?php echo $chap['status'] == 'published' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'; ?> rounded-full text-xs font-medium capitalize">
                                    <?php echo htmlspecialchars($chap['status']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form method="POST" class="inline-block" onsubmit="return confirm('Hapus bab ini?')">
                                    <input type="hidden" name="delete_chapter_id" value="<?php echo $chap['id']; ?>">
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="p-12 text-center text-gray-500">
                        <p class="mb-4">Belum ada bagian cerita yang ditulis.</p>
                        <a href="tulis_bab.php?book_id=<?php echo $book['id']; ?>" class="text-emerald-600 font-bold hover:underline">Mulai Menulis Bab Pertama</a>
                    </div>
                <?php endif; ?>
            </div>

        </main>
    </div>

</body>
</html>

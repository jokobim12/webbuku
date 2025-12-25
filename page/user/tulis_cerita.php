<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../auth/login.php');
    exit();
}

require_once '../../database/koneksi.php';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($koneksi, $_POST['title']);
    $genre = mysqli_real_escape_string($koneksi, $_POST['genre']);
    $synopsis = mysqli_real_escape_string($koneksi, $_POST['synopsis']);
    $user_id = $_SESSION['user_id'];
    
    // Handle File Upload
    $cover_image = '';
    if (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../uploads/covers/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
        $unique_name = uniqid('cover_', true) . '.' . $file_ext;
        $target_file = $upload_dir . $unique_name;
        
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        
        if (in_array($file_ext, $allowed_ext)) {
            if (move_uploaded_file($_FILES['cover']['tmp_name'], $target_file)) {
                $cover_image = 'uploads/covers/' . $unique_name; 
            }
        }
    }

    // Insert as DRAFT initially
    $query = "INSERT INTO books (user_id, title, genre, synopsis, cover_image, status) 
              VALUES ('$user_id', '$title', '$genre', '$synopsis', '$cover_image', 'draft')";
              
    if (mysqli_query($koneksi, $query)) {
        $new_book_id = mysqli_insert_id($koneksi);
        // Redirect to Story Manager
        header("Location: kelola_cerita.php?id=$new_book_id");
        exit();
    } else {
        $error = "Gagal menyimpan info cerita: " . mysqli_error($koneksi);
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Cerita Baru - WebBuku</title>
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
            
            <a href="#" class="flex items-center px-4 py-3 bg-emerald-50 text-emerald-700 rounded-lg font-medium transition-colors">
                 <svg class="w-5 h-5 mr-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
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

    <!-- Main Content -->
    <div class="md:ml-64 min-h-screen flex flex-col">
        
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8">
            <button class="md:hidden p-2 text-gray-600 rounded-lg hover:bg-gray-100" onclick="document.getElementById('sidebar').classList.toggle('-translate-x-full')">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>

            <div class="flex-1 px-4">
               <h1 class="text-lg font-semibold text-gray-800">Detail Cerita</h1>
            </div>

            <!-- Profile Dropdown -->
            <div class="relative group">
                <button class="flex items-center gap-3 focus:outline-none">
                    <span class="hidden md:block text-sm font-medium text-gray-700"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                    <img src="<?php echo htmlspecialchars($_SESSION['avatar']); ?>" alt="Avatar" class="w-9 h-9 rounded-full border border-gray-200 object-cover">
                </button>
                <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 hidden group-hover:block z-50">
                    <a href="../../auth/logout.php" onclick="confirmLogout(event, this.href)" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50 first:rounded-t-lg last:rounded-b-lg">
                        Keluar
                    </a>
                </div>
            </div>
        </header>

        <!-- Content Body -->
        <main class="flex-1 p-6 lg:p-8 max-w-4xl mx-auto w-full">
            
            <div class="mb-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Langkah 1: Identitas Cerita</h2>
                <p class="text-gray-500">Isi detail dasar ceritamu sebelum mulai menulis bab.</p>
            </div>

            <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
                
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                    <!-- Title -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Judul Cerita</label>
                        <input type="text" name="title" required placeholder="Berikan judul yang menarik..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
                    </div>

                    <!-- Genre & Cover -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                             <label class="block text-sm font-medium text-gray-700 mb-2">Genre</label>
                             <select name="genre" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-white">
                                 <option value="">Pilih Genre</option>
                                 <option value="Fantasi">Fantasi</option>
                                 <option value="Romantis">Romantis</option>
                                 <option value="Horror">Horror</option>
                                 <option value="Sci-Fi">Sci-Fi</option>
                                 <option value="Misteri">Misteri</option>
                                 <option value="Drama">Drama</option>
                                 <option value="Komedi">Komedi</option>
                                 <option value="Petualangan">Petualangan</option>
                                 <option value="Sejarah">Sejarah</option>
                                 <option value="Thriller">Thriller</option>
                             </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cover Buku</label>
                            <input type="file" name="cover" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition-colors">
                        </div>
                    </div>

                    <!-- Synopsis -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sinopsis</label>
                        <textarea name="synopsis" rows="5" placeholder="Ceritakan garis besar ceritamu..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all"></textarea>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-emerald-600 text-white font-bold rounded-lg hover:bg-emerald-700 shadow-md transition-colors hover:shadow-lg flex items-center gap-2">
                        Simpan & Lanjut
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>

            </form>

            <footer class="mt-12 text-center text-gray-400 text-sm">
                &copy; 2025 WebBuku. All rights reserved.
            </footer>
        </main>
    </div>
    <?php include '../../layouts/confirmation_modal.php'; ?>
</body>
</html>

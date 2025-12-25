<?php
session_start();

// Cek Login
// if (!isset($_SESSION['user_id'])) {
//     header('Location: ../../auth/login.php');
//     exit();
// }

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jelajahi Buku - WebBuku</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 text-gray-800 font-sans">

    <?php include '../../layouts/navbar.php'; ?>

    <!-- Header Section -->
    <div class="pt-32 pb-12 bg-emerald-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-3xl md:text-5xl font-bold mb-4">Pustaka Cerita</h1>
            <p class="text-emerald-100 max-w-2xl mx-auto">Temukan ribuan cerita menarik dari penulis berbakat di seluruh Indonesia.</p>
            
            <!-- Search Bar -->
            <form action="" method="GET" class="mt-8 max-w-xl mx-auto relative">
                <?php if(isset($_GET['genre'])): ?>
                    <input type="hidden" name="genre" value="<?php echo htmlspecialchars($_GET['genre']); ?>">
                <?php endif; ?>
                <input type="text" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" placeholder="Cari judul, penulis, atau genre..." class="w-full py-4 pl-6 pr-14 rounded-full text-gray-900 focus:outline-none focus:ring-4 focus:ring-emerald-500/30 shadow-lg">
                <button type="submit" class="absolute right-3 top-3 bg-emerald-600 p-2 rounded-full text-white hover:bg-emerald-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </form>
            
            <!-- Filters -->
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <a href="buku.php<?php echo isset($_GET['search']) ? '?search='.urlencode($_GET['search']) : ''; ?>" class="px-4 py-2 <?php echo !isset($_GET['genre']) || $_GET['genre'] == '' ? 'bg-white text-emerald-800 shadow-md' : 'bg-emerald-800/50 hover:bg-emerald-700 text-white border border-emerald-700'; ?> rounded-full text-sm font-semibold transition-colors">Semua</a>
                
                <?php 
                $genres = ['Fantasi', 'Romantis', 'Horror', 'Sci-Fi', 'Misteri', 'Drama', 'Komedi', 'Petualangan', 'Sejarah', 'Thriller'];
                foreach ($genres as $genre) {
                    $isActive = isset($_GET['genre']) && $_GET['genre'] == $genre;
                    $searchParam = isset($_GET['search']) ? '&search='.urlencode($_GET['search']) : '';
                    $class = $isActive ? 'bg-white text-emerald-800 shadow-md' : 'bg-emerald-800/50 hover:bg-emerald-700 text-white border border-emerald-700';
                    echo '<a href="buku.php?genre='.$genre.$searchParam.'" class="px-4 py-2 '.$class.' rounded-full text-sm font-medium transition-colors">'.$genre.'</a>';
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Gallery Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-16">
        <div class="grid grid-cols-3 lg:grid-cols-5 gap-3 md:gap-8">
            <!-- Book Items (From Database) -->
            <?php
            require_once '../../database/koneksi.php';

            // Build Query
            $conditions = ["books.status = 'published'"];
            
            if (isset($_GET['search']) && !empty($_GET['search'])) {
                $search = mysqli_real_escape_string($koneksi, $_GET['search']);
                $conditions[] = "(books.title LIKE '%$search%' OR users.name LIKE '%$search%' OR books.genre LIKE '%$search%')";
            }

            if (isset($_GET['genre']) && !empty($_GET['genre'])) {
                $genre_filter = mysqli_real_escape_string($koneksi, $_GET['genre']);
                $conditions[] = "books.genre LIKE '%$genre_filter%'";
            }

            $sql_query = "SELECT books.*, users.name as author_name 
                      FROM books 
                      JOIN users ON books.user_id = users.id 
                      WHERE " . implode(' AND ', $conditions) . " 
                      ORDER BY books.created_at DESC";
            
            $result = mysqli_query($koneksi, $sql_query);

            if (mysqli_num_rows($result) > 0) {
                while ($book = mysqli_fetch_assoc($result)) {
            ?>
            <div class="group bg-white rounded-lg md:rounded-xl border border-gray-100 shadow-sm hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                <div class="relative h-36 md:h-72 bg-gray-200 overflow-hidden">
                    <?php if (!empty($book['cover_image'])): ?>
                        <img src="../../<?php echo htmlspecialchars($book['cover_image']); ?>" alt="Cover" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                    <?php else: ?>
                        <div class="w-full h-full flex flex-col items-center justify-center bg-emerald-50 text-emerald-600">
                             <svg class="w-8 md:w-12 h-8 md:h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                             <span class="font-bold text-[10px] md:text-base">No Cover</span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="absolute top-1.5 right-1.5 bg-white/90 backdrop-blur-sm px-1.5 py-0.5 rounded text-[10px] md:text-xs font-bold text-gray-700 shadow-sm truncate max-w-[90%]">
                        <?php echo htmlspecialchars($book['genre']); ?>
                    </div>
                </div>
                <div class="p-2.5 md:p-5">
                    <h3 class="text-[11px] md:text-lg font-bold text-gray-900 mb-0.5 line-clamp-1 group-hover:text-emerald-600 transition-colors"><?php echo htmlspecialchars($book['title']); ?></h3>
                    <p class="text-[10px] md:text-sm text-gray-500 mb-2 md:mb-4 line-clamp-1">Oleh <span class="text-gray-700 font-medium"><?php echo htmlspecialchars($book['author_name']); ?></span></p>
                    
                    <div class="flex items-center justify-between border-t border-gray-100 pt-2 md:pt-4">
                        <span class="text-[10px] md:text-xs text-gray-400 flex items-center gap-0.5">
                            <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <?php echo $book['views']; ?>
                        </span>
                        <a href="detail.php?id=<?php echo $book['id']; ?>" class="text-emerald-600 hover:text-emerald-700 font-semibold text-[10px] md:text-sm">Baca</a>
                    </div>
                </div>
            </div>
            <?php 
                } 
            } else {
                echo '<div class="col-span-1 md:col-span-2 lg:col-span-4 text-center py-16">';
                echo '<div class="text-6xl mb-4">🔍</div>';
                echo '<h3 class="text-xl font-bold text-gray-900 mb-2">Tidak ditemukan cerita</h3>';
                echo '<p class="text-gray-500">Coba kata kunci lain atau ubah filter genre.</p>';
                echo '</div>';
            }
            ?>

        </div>
        
        <!-- Pagination -->
        <div class="mt-16 flex justify-center gap-2">
            <button class="w-10 h-10 rounded-full bg-emerald-600 text-white font-bold shadow-lg">1</button>
            <button class="w-10 h-10 rounded-full bg-white text-gray-600 hover:bg-gray-50 border border-gray-200 transition-colors">2</button>
            <button class="w-10 h-10 rounded-full bg-white text-gray-600 hover:bg-gray-50 border border-gray-200 transition-colors">3</button>
            <span class="flex items-end px-2 text-gray-400">...</span>
            <button class="w-10 h-10 rounded-full bg-white text-gray-600 hover:bg-gray-50 border border-gray-200 transition-colors">→</button>
        </div>
    </div>

    <?php include '../../layouts/footer.php'; ?>

</body>
</html>

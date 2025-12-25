<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../auth/login.php');
    exit();
}

require_once '../../database/koneksi.php';

// Initialize variables
$is_edit = false;
$book_id = '';
$title = '';
$genre = '';
$synopsis = '';
$existing_cover = '';

// Check if editing
if (isset($_GET['id'])) {
    $book_id = mysqli_real_escape_string($koneksi, $_GET['id']);
    $user_id = $_SESSION['user_id'];
    
    $query_check = "SELECT * FROM books WHERE id = '$book_id' AND user_id = '$user_id'";
    $result_check = mysqli_query($koneksi, $query_check);
    
    if ($book = mysqli_fetch_assoc($result_check)) {
        $is_edit = true;
        $title = $book['title'];
        $genre = $book['genre'];
        $synopsis = $book['synopsis'];
        $existing_cover = $book['cover_image'];
    } else {
        // ID exists but not found or not owned -> redirect or error
        header('Location: karyaku.php');
        exit();
    }
}

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($koneksi, $_POST['title']);
    $genre = mysqli_real_escape_string($koneksi, $_POST['genre']);
    $synopsis = mysqli_real_escape_string($koneksi, $_POST['synopsis']);
    $user_id = $_SESSION['user_id'];
    
    // Check if we are updating an existing book
    $update_id = isset($_POST['book_id']) ? mysqli_real_escape_string($koneksi, $_POST['book_id']) : null;
    $is_update = !empty($update_id);

    // Handle File Upload or Cropped Image
    $cover_image = $is_update ? $existing_cover : ''; // Default to existing if update, empty if new
    
    // 1. Check for cropped image (Base64)
    if (!empty($_POST['cropped_cover'])) {
        $upload_dir = '../../uploads/covers/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $data = $_POST['cropped_cover'];
        
        // Extract base64 data (remove "data:image/png;base64," part)
        list($type, $data) = explode(';', $data);
        list(, $data)      = explode(',', $data);
        $data = base64_decode($data);

        $file_ext = 'png'; // Cropper.js usually exports to png
        $unique_name = uniqid('cover_', true) . '.' . $file_ext;
        $target_file = $upload_dir . $unique_name;

        if (file_put_contents($target_file, $data)) {
            $cover_image = 'uploads/covers/' . $unique_name;
        }
    } 
    // 2. Fallback to standard upload
    elseif (isset($_FILES['cover']) && $_FILES['cover']['error'] === UPLOAD_ERR_OK) {
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

    if ($is_update) {
        // UPDATE Logic
        // Only update cover_image if a new one was generated ($cover_image is updated above)
        // If $cover_image was initialized to $existing_cover and not changed, it stays same.
        // Wait, if $existing_cover is used, I don't need to change logic much, just ensure variable is right.
        
        // Actually, if $cover_image was set to $existing_cover at start, and new one not provided, it remains old.
        // But if I want to allow removing cover? (Not requested yet, assume optional).
        
        $query = "UPDATE books SET 
                  title = '$title', 
                  genre = '$genre', 
                  synopsis = '$synopsis', 
                  cover_image = '$cover_image'
                  WHERE id = '$update_id' AND user_id = '$user_id'";
                  
        if (mysqli_query($koneksi, $query)) {
            header("Location: kelola_cerita.php?id=$update_id");
            exit();
        } else {
             $error = "Gagal mengupdate info cerita: " . mysqli_error($koneksi);
        }

    } else {
        // INSERT Logic
        $query = "INSERT INTO books (user_id, title, genre, synopsis, cover_image, status) 
                  VALUES ('$user_id', '$title', '$genre', '$synopsis', '$cover_image', 'draft')";
                  
        if (mysqli_query($koneksi, $query)) {
            $new_book_id = mysqli_insert_id($koneksi);
            header("Location: kelola_cerita.php?id=$new_book_id");
            exit();
        } else {
            $error = "Gagal menyimpan info cerita: " . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_edit ? 'Edit Cerita' : 'Buat Cerita Baru'; ?> - WebBuku</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
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
               <h1 class="text-lg font-semibold text-gray-800"><?php echo $is_edit ? 'Edit Info Cerita' : 'Detail Cerita'; ?></h1>
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
                <h2 class="text-2xl font-bold text-gray-900 mb-2"><?php echo $is_edit ? 'Edit Identitas Cerita' : 'Langkah 1: Identitas Cerita'; ?></h2>
                <p class="text-gray-500"><?php echo $is_edit ? 'Perbarui detail ceritamu.' : 'Isi detail dasar ceritamu sebelum mulai menulis bab.'; ?></p>
            </div>

            <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
                <?php if ($is_edit): ?>
                    <input type="hidden" name="book_id" value="<?php echo $book_id; ?>">
                <?php endif; ?>
                
                <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
                    <!-- Title -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Judul Cerita</label>
                        <input type="text" name="title" required value="<?php echo htmlspecialchars($title); ?>" placeholder="Berikan judul yang menarik..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
                    </div>

                    <!-- Genre & Cover -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                             <label class="block text-sm font-medium text-gray-700 mb-2">Genre</label>
                             <select name="genre" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none bg-white">
                                 <option value="">Pilih Genre</option>
                                 <?php
                                 $genres = ['Fantasi', 'Romantis', 'Horror', 'Sci-Fi', 'Misteri', 'Drama', 'Komedi', 'Petualangan', 'Sejarah', 'Thriller'];
                                 foreach ($genres as $g) {
                                     $selected = ($genre == $g) ? 'selected' : '';
                                     echo "<option value=\"$g\" $selected>$g</option>";
                                 }
                                 ?>
                             </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Cover Buku</label>
                            
                            <!-- Hidden input to store cropped base64 data -->
                            <input type="hidden" name="cropped_cover" id="cropped_cover">
                            
                            <!-- File input -->
                            <input type="file" id="cover_input" name="cover" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition-colors">
                            
                            <!-- Preview Area -->
                            <div id="preview_container" class="mt-4 <?php echo ($is_edit && $existing_cover) ? '' : 'hidden'; ?>">
                                <p class="text-xs text-gray-500 mb-2">Preview Cover:</p>
                                <img id="preview_image" src="<?php echo ($is_edit && $existing_cover) ? '../../' . htmlspecialchars($existing_cover) : ''; ?>" class="w-32 h-48 object-cover rounded-lg border border-gray-200 shadow-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Synopsis -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Sinopsis</label>
                        <textarea name="synopsis" rows="5" placeholder="Ceritakan garis besar ceritamu..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all"><?php echo htmlspecialchars($synopsis); ?></textarea>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-emerald-600 text-white font-bold rounded-lg hover:bg-emerald-700 shadow-md transition-colors hover:shadow-lg flex items-center gap-2">
                        <?php echo $is_edit ? 'Simpan Perubahan' : 'Simpan & Lanjut'; ?>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>

            </form>

            <footer class="mt-12 text-center text-gray-400 text-sm">
                &copy; 2025 WebBuku. All rights reserved.
            </footer>
        </main>
    </div>
    <!-- Cropper Modal -->
    <div id="cropModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <!-- Modal panel -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white p-4 sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Crop Gambar Cover</h3>
                            <div class="mt-4 w-full h-[400px] bg-gray-100 flex items-center justify-center overflow-hidden rounded-lg">
                                <!-- Image to Crop -->
                                <img id="image_to_crop" style="max-width: 100%; max-height: 100%; display: block;">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="crop_btn" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Potong & Simpan
                    </button>
                    <button type="button" id="cancel_crop_btn" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const coverInput = document.getElementById('cover_input');
            const cropModal = document.getElementById('cropModal');
            const imageToCrop = document.getElementById('image_to_crop');
            const cropBtn = document.getElementById('crop_btn');
            const cancelBtn = document.getElementById('cancel_crop_btn');
            const previewContainer = document.getElementById('preview_container');
            const previewImage = document.getElementById('preview_image');
            const croppedCoverInput = document.getElementById('cropped_cover');
            
            let cropper;

            // Handle file selection
            coverInput.addEventListener('change', function(e) {
                const files = e.target.files;
                if (files && files.length > 0) {
                    const file = files[0];
                    
                    if (URL) {
                        // Create URL for selected file
                        imageToCrop.src = URL.createObjectURL(file);
                        
                        // Show Modal
                        cropModal.classList.remove('hidden');
                        
                        // Destroy previous cropper if exists
                        if (cropper) {
                            cropper.destroy();
                        }

                        // Initialize Cropper (wait for image load)
                        imageToCrop.onload = function() {
                            cropper = new Cropper(imageToCrop, {
                                aspectRatio: 2 / 3, // Standard book cover ratio
                                viewMode: 1,
                                autoCropArea: 0.8,
                            });
                        };
                    }
                }
            });

            // Crop Button Click
            cropBtn.addEventListener('click', function() {
                if (cropper) {
                    // Get cropped canvas
                    const canvas = cropper.getCroppedCanvas({
                        width: 400, // Reasonable max width for saving
                        height: 600
                    });

                    // Convert to base64
                    const base64Image = canvas.toDataURL('image/png');
                    
                    // Set hidden input value
                    croppedCoverInput.value = base64Image;

                    // Update Preview
                    previewImage.src = base64Image;
                    previewContainer.classList.remove('hidden');

                    // Close Modal
                    closeModal();
                }
            });

            // Cancel Button Click
            cancelBtn.addEventListener('click', function() {
                closeModal();
                coverInput.value = ''; // Reset input so same file can be selected again
            });

            function closeModal() {
                cropModal.classList.add('hidden');
                if (cropper) {
                    cropper.destroy();
                    cropper = null;
                }
            }
        });
    </script>
    <?php include '../../layouts/confirmation_modal.php'; ?>
</body>
</html>

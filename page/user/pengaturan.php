<?php
session_start();
require_once '../../database/koneksi.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../../auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($koneksi, $query);
$user = mysqli_fetch_assoc($result);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Profil - WebBuku</title>
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

    <!-- Main Content Wrapper -->
    <div class="md:ml-64 min-h-screen flex flex-col">
        
        <!-- Header -->
        <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8">
            <button class="md:hidden p-2 text-gray-600 rounded-lg hover:bg-gray-100" onclick="toggleSidebar()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>

            <div class="flex-1 px-4">
               <h1 class="text-lg font-semibold text-gray-800">Pengaturan Profil</h1>
            </div>

            <!-- Profile Dropdown -->
            <div class="relative group">
                <button class="flex items-center gap-3 focus:outline-none">
                    <span class="hidden md:block text-sm font-medium text-gray-700" id="headerName"><?php echo htmlspecialchars($_SESSION['name']); ?></span>
                    <?php 
                        $sess_avatar = trim($_SESSION['avatar']);
                        if (stripos($sess_avatar, 'http') !== 0) {
                            $sess_avatar = '../../' . $sess_avatar;
                        }
                    ?>
                    <img id="headerAvatar" src="<?php echo htmlspecialchars($sess_avatar); ?>" alt="Avatar" referrerpolicy="no-referrer" class="w-9 h-9 rounded-full border border-gray-200 object-cover">
                </button>
                <div class="absolute right-0 top-full pt-2 w-48 hidden group-hover:block z-50">
                    <div class="bg-white rounded-lg shadow-lg border border-gray-100 overflow-hidden">
                        <a href="../../auth/logout.php" onclick="confirmLogout(event, this.href)" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                            Keluar
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content Body -->
        <main class="flex-1 p-6 lg:p-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 max-w-2xl mx-auto">
                <h2 class="text-xl font-bold text-gray-900 mb-6">Edit Profil</h2>
                
                <form id="profileForm" enctype="multipart/form-data">
                    <!-- Avatar Upload -->
                    <div class="mb-6 flex items-center gap-6">
                        <div class="relative">
                            <?php 
                                $user_avatar = trim($user['avatar']);
                                if (stripos($user_avatar, 'http') !== 0) {
                                    $user_avatar = '../../' . $user_avatar;
                                }
                            ?>
                            <img id="previewAvatar" src="<?php echo htmlspecialchars($user_avatar); ?>" alt="Avatar" referrerpolicy="no-referrer" class="w-24 h-24 rounded-full object-cover border-2 border-emerald-100">
                            <label for="avatarInput" class="absolute bottom-0 right-0 bg-emerald-600 hover:bg-emerald-700 text-white p-2 rounded-full cursor-pointer transition-colors shadow-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </label>
                            <input type="file" id="avatarInput" name="avatar" accept="image/*" class="hidden">
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-900">Foto Profil</h3>
                            <p class="text-sm text-gray-500">Format: JPG, PNG, GIF. Max 2MB.</p>
                        </div>
                    </div>

                    <!-- Name Input -->
                    <div class="mb-6">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
                    </div>

                    <!-- Bio Input -->
                    <div class="mb-6">
                        <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">Bio / Tentang Saya</label>
                        <textarea id="bio" name="bio" rows="4" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all"
                            placeholder="Ceritakan sedikit tentang dirimu..."><?php echo htmlspecialchars($user['bio']); ?></textarea>
                    </div>

                    <!-- Social Media Links -->
                    <div class="mb-6 space-y-4">
                        <h3 class="text-sm font-medium text-gray-700">Tautan Sosial Media (Opsional)</h3>
                        
                        <!-- Twitter -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.84 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                            </div>
                            <input type="text" name="social_twitter" placeholder="Twitter Username" value="<?php echo htmlspecialchars($user['social_twitter'] ?? ''); ?>" 
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
                        </div>

                        <!-- Instagram -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                            </div>
                            <input type="text" name="social_instagram" placeholder="Instagram Username" value="<?php echo htmlspecialchars($user['social_instagram'] ?? ''); ?>" 
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
                        </div>

                        <!-- Website -->
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                            </div>
                            <input type="url" name="social_website" placeholder="Website / Portfolio URL" value="<?php echo htmlspecialchars($user['social_website'] ?? ''); ?>" 
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-all">
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-3">
                        <a href="../../dashboard.php" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-colors">Batal</a>
                        <button type="submit" id="saveBtn" class="px-6 py-2 bg-emerald-600 text-white rounded-lg font-medium hover:bg-emerald-700 transition-colors">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <!-- Alert Modal (Simple implementation) -->
    <div id="alertModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl p-6 max-w-sm w-full mx-4">
            <div id="alertIcon" class="mb-4 flex justify-center text-4xl"></div>
            <h3 id="alertTitle" class="text-xl font-bold text-center text-gray-900 mb-2"></h3>
            <p id="alertMessage" class="text-gray-600 text-center mb-6"></p>
            <button onclick="document.getElementById('alertModal').classList.add('hidden')" class="w-full py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium rounded-lg transition-colors">
                Tutup
            </button>
        </div>
    </div>

    <script>
        const avatarInput = document.getElementById('avatarInput');
        const previewAvatar = document.getElementById('previewAvatar');
        const profileForm = document.getElementById('profileForm');
        const saveBtn = document.getElementById('saveBtn');

        // Avatar Preview
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewAvatar.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });

        // Form Submit
        profileForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Loading state
            const originalBtnText = saveBtn.innerText;
            saveBtn.innerText = 'Menyimpan...';
            saveBtn.disabled = true;
            saveBtn.classList.add('opacity-75');

            const formData = new FormData(this);

            fetch('process_profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                showAlert(data.success, data.message);
                if (data.success) {
                    // Update header info dynamically
                    if (data.new_name) document.getElementById('headerName').innerText = data.new_name;
                    if (data.new_avatar) {
                        // Fix relative path for current view (../../ + uploads...)
                        // data.new_avatar is "uploads/avatars/..."
                        document.getElementById('headerAvatar').src = '../../' + data.new_avatar;
                    }
                }
            })
            .catch(error => {
                showAlert(false, 'Terjadi kesalahan sistem');
                console.error(error);
            })
            .finally(() => {
                saveBtn.innerText = originalBtnText;
                saveBtn.disabled = false;
                saveBtn.classList.remove('opacity-75');
            });
        });

        function showAlert(success, message) {
            const modal = document.getElementById('alertModal');
            const icon = document.getElementById('alertIcon');
            const title = document.getElementById('alertTitle');
            const msg = document.getElementById('alertMessage');

            modal.classList.remove('hidden');
            if (success) {
                icon.innerHTML = '✅';
                title.innerText = 'Berhasil';
            } else {
                icon.innerHTML = '❌';
                title.innerText = 'Gagal';
            }
            msg.innerText = message;
        }
    </script>
    <?php include '../../layouts/confirmation_modal.php'; ?>
</body>
</html>

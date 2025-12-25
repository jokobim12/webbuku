<?php
session_start();
require_once '../../database/koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$name = isset($_POST['name']) ? trim($_POST['name']) : '';
$bio = isset($_POST['bio']) ? trim($_POST['bio']) : '';

if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Nama tidak boleh kosong']);
    exit();
}

$update_query = "UPDATE users SET name = ?, bio = ? WHERE id = ?";
$types = "ssi";
$params = [$name, $bio, $user_id];

// Handle Avatar Upload
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $file_type = $_FILES['avatar']['type'];
    
    if (!in_array($file_type, $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Format file tidak diizinkan (Gunakan JPG, PNG, GIF, atau WebP)']);
        exit();
    }
    
    // Max 2MB
    if($_FILES['avatar']['size'] > 2 * 1024 * 1024) {
         echo json_encode(['success' => false, 'message' => 'Ukuran file terlalu besar (Max 2MB)']);
         exit();
    }

    $ext = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
    $filename = 'avatar_' . $user_id . '_' . time() . '.' . $ext;
    // Relative path for database
    $db_path = 'uploads/avatars/' . $filename; 
    // Absolute path for moving file (Assuming script is in page/user/)
    $target_dir = '../../uploads/avatars/';
    $target_file = $target_dir . $filename;

    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $target_file)) {
        $update_query = "UPDATE users SET name = ?, bio = ?, avatar = ? WHERE id = ?";
        $types = "sssi";
        $params = [$name, $bio, $db_path, $user_id];
        
        // Update session avatar
        $_SESSION['avatar'] = $db_path;
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mengupload gambar']);
        exit();
    }
}

$stmt = mysqli_prepare($koneksi, $update_query);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    
    if (mysqli_stmt_execute($stmt)) {
        // Update session name if changed
        $_SESSION['name'] = $name;
        
        echo json_encode([
            'success' => true, 
            'message' => 'Profil berhasil diperbarui',
            'new_name' => $name,
            'new_avatar' => isset($db_path) ? $db_path : null
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memperbarui database: ' . mysqli_error($koneksi)]);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($koneksi)]);
}
?>

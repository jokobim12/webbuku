<?php
session_start();
require_once '../../database/koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}

$follower_id = $_SESSION['user_id'];
$following_id = isset($_POST['following_id']) ? intval($_POST['following_id']) : 0;

if ($following_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid user ID.']);
    exit();
}

if ($follower_id == $following_id) {
    echo json_encode(['success' => false, 'message' => 'Anda tidak bisa mengikuti diri sendiri.']);
    exit();
}

// Check connection
if (!$koneksi) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit();
}

// Check if already following
$check_query = "SELECT id FROM follows WHERE follower_id = ? AND following_id = ?";
$stmt = mysqli_prepare($koneksi, $check_query);
mysqli_stmt_bind_param($stmt, "ii", $follower_id, $following_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
$is_following = mysqli_stmt_num_rows($stmt) > 0;
mysqli_stmt_close($stmt);

if ($is_following) {
    // Unfollow
    $delete_query = "DELETE FROM follows WHERE follower_id = ? AND following_id = ?";
    $stmt = mysqli_prepare($koneksi, $delete_query);
    mysqli_stmt_bind_param($stmt, "ii", $follower_id, $following_id);
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'is_following' => false, 'message' => 'Berhasil berhenti mengikuti.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memproses permintaan.']);
    }
} else {
    // Follow
    $insert_query = "INSERT INTO follows (follower_id, following_id) VALUES (?, ?)";
    $stmt = mysqli_prepare($koneksi, $insert_query);
    mysqli_stmt_bind_param($stmt, "ii", $follower_id, $following_id);
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['success' => true, 'is_following' => true, 'message' => 'Berhasil mengikuti.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal memproses permintaan.']);
    }
}

mysqli_stmt_close($stmt);
mysqli_close($koneksi);
?>

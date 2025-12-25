<?php
session_start();
require_once '../database/koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$book_id = isset($input['book_id']) ? intval($input['book_id']) : 0;
$user_id = $_SESSION['user_id'];

if ($book_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid book ID']);
    exit;
}

// Check if already liked
$check = mysqli_query($koneksi, "SELECT id FROM likes WHERE user_id = $user_id AND book_id = $book_id");

if (mysqli_num_rows($check) > 0) {
    // Unlike
    mysqli_query($koneksi, "DELETE FROM likes WHERE user_id = $user_id AND book_id = $book_id");
    $action = 'unliked';
} else {
    // Like
    mysqli_query($koneksi, "INSERT INTO likes (user_id, book_id) VALUES ($user_id, $book_id)");
    $action = 'liked';

    // Notification
    $book_query = mysqli_query($koneksi, "SELECT user_id FROM books WHERE id = $book_id");
    if ($book_row = mysqli_fetch_assoc($book_query)) {
        $owner_id = $book_row['user_id'];
        if ($owner_id != $user_id) {
            mysqli_query($koneksi, "INSERT INTO notifications (user_id, type, actor_id, reference_id) VALUES ($owner_id, 'like', $user_id, $book_id)");
        }
    }
}

// Get new like count
$count_query = mysqli_query($koneksi, "SELECT COUNT(*) as c FROM likes WHERE book_id = $book_id");
$count = mysqli_fetch_assoc($count_query)['c'];

echo json_encode(['status' => 'success', 'action' => $action, 'likes' => $count]);

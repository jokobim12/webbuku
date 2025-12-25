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

// Check if already bookmarked
$check = mysqli_query($koneksi, "SELECT id FROM bookmarks WHERE user_id = $user_id AND book_id = $book_id");

if (mysqli_num_rows($check) > 0) {
    // Remove bookmark
    mysqli_query($koneksi, "DELETE FROM bookmarks WHERE user_id = $user_id AND book_id = $book_id");
    echo json_encode(['status' => 'success', 'action' => 'removed']);
} else {
    // Add bookmark
    mysqli_query($koneksi, "INSERT INTO bookmarks (user_id, book_id) VALUES ($user_id, $book_id)");
    echo json_encode(['status' => 'success', 'action' => 'added']);
}

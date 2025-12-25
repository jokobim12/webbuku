<?php
session_start();
require_once '../database/koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user_id'];
$book_id = isset($data['book_id']) ? intval($data['book_id']) : 0;
$chapter_id = isset($data['chapter_id']) ? intval($data['chapter_id']) : 0;
$action = isset($data['action']) ? $data['action'] : '';

if (!$book_id || !$chapter_id) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    exit();
}

if ($action === 'add') {
    // Upsert logic: If exists, update timestamp. If distinct chapter per user/book is needed, we might want to delete old ones for this book first?
    // User request: "kita bisa ngasih tanda sendiri". Usually implies one mark per book or multiple?
    // A simplified bookmark usually means "This is where I am". Let's assume one active mark per book for simplicity, OR let them mark multiple.
    // The UNIQUE constraint is (user_id, chapter_id), so a user can mark Chapter 1 and Chapter 5.
    // However, usually "last read marker" implies a single point.
    // Let's stick to the unique constraint.
    
    // OPTIONAL: If we want only ONE manual bookmark per book, we should delete others first.
    // For now, let's allow multiple marks, but pustaka.php query might pick the latest one.
    
    // Actually, to keep it simple and consistent with "bookmarking a page", let's clear other bookmarks for this book so there's only one "Active Manual Marker".
    mysqli_query($koneksi, "DELETE FROM chapter_bookmarks WHERE user_id = $user_id AND book_id = $book_id");

    $query = "INSERT INTO chapter_bookmarks (user_id, book_id, chapter_id) VALUES ($user_id, $book_id, $chapter_id)";
    if (mysqli_query($koneksi, $query)) {
        echo json_encode(['status' => 'success', 'state' => 'added']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($koneksi)]);
    }

} elseif ($action === 'remove') {
    $query = "DELETE FROM chapter_bookmarks WHERE user_id = $user_id AND chapter_id = $chapter_id";
    if (mysqli_query($koneksi, $query)) {
        echo json_encode(['status' => 'success', 'state' => 'removed']);
    } else {
        echo json_encode(['status' => 'error', 'message' => mysqli_error($koneksi)]);
    }

} else {
    // Check status
    $query = "SELECT id FROM chapter_bookmarks WHERE user_id = $user_id AND chapter_id = $chapter_id";
    $result = mysqli_query($koneksi, $query);
    if (mysqli_num_rows($result) > 0) {
        echo json_encode(['status' => 'success', 'state' => 'exists']);
    } else {
        echo json_encode(['status' => 'success', 'state' => 'none']);
    }
}
?>

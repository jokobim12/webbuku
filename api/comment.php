<?php
session_start();
require_once '../database/koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$user_id = $_SESSION['user_id'];

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $book_id = isset($input['book_id']) ? intval($input['book_id']) : 0;
    $content = isset($input['content']) ? trim($input['content']) : '';

    if ($book_id <= 0 || empty($content)) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
        exit;
    }

    $stmt = $koneksi->prepare("INSERT INTO comments (user_id, book_id, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $book_id, $content);
    
    if($stmt->execute()) {
        $comment_id = $stmt->insert_id;
        // Return new comment data for UI appending
        $new_comment = [
            'id' => $comment_id,
            'user_name' => $_SESSION['name'], // Assuming session has name
            'avatar' => $_SESSION['avatar'], // Assuming session has avatar
            'content' => htmlspecialchars($content),
            'created_at' => 'Baru saja',
            'is_own' => true
        ];
        echo json_encode(['status' => 'success', 'comment' => $new_comment]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to post comment']);
    }

} elseif ($method === 'DELETE') {
    $input = json_decode(file_get_contents('php://input'), true);
    $comment_id = isset($input['comment_id']) ? intval($input['comment_id']) : 0;

    if ($comment_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
        exit;
    }

    // Verify ownership
    $check = mysqli_query($koneksi, "SELECT user_id FROM comments WHERE id = $comment_id");
    $comment = mysqli_fetch_assoc($check);

    if (!$comment || $comment['user_id'] != $user_id) {
        echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
        exit;
    }

    mysqli_query($koneksi, "DELETE FROM comments WHERE id = $comment_id");
    echo json_encode(['status' => 'success']);
}

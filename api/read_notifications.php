<?php
session_start();
require_once '../database/koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Mark all as read for now (simplification)
mysqli_query($koneksi, "UPDATE notifications SET is_read = 1 WHERE user_id = $user_id AND is_read = 0");

echo json_encode(['status' => 'success']);
?>

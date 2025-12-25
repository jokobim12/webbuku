<?php
session_start();
require_once '../database/koneksi.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get unread count
$count_query = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM notifications WHERE user_id = $user_id AND is_read = 0");
$unread_count = mysqli_fetch_assoc($count_query)['total'];

// Get recent notifications (limit 10)
$query = "SELECT n.*, 
          u.name as actor_name, u.avatar as actor_avatar,
          b.title as book_title
          FROM notifications n
          JOIN users u ON n.actor_id = u.id
          LEFT JOIN books b ON n.reference_id = b.id
          WHERE n.user_id = $user_id
          ORDER BY n.created_at DESC
          LIMIT 10";

$result = mysqli_query($koneksi, $query);
$notifications = [];

while ($row = mysqli_fetch_assoc($result)) {
    $notifications[] = [
        'id' => $row['id'],
        'type' => $row['type'],
        'actor_name' => $row['actor_name'],
        'actor_avatar' => $row['actor_avatar'] ? $row['actor_avatar'] : 'assets/avatar_placeholder.png', // Handle path later
        'book_title' => $row['book_title'],
        'is_read' => $row['is_read'],
        'time' => time_elapsed_string($row['created_at'])
    ];
}

echo json_encode([
    'status' => 'success',
    'unread_count' => $unread_count,
    'notifications' => $notifications
]);

function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'tahun',
        'm' => 'bulan',
        'w' => 'minggu',
        'd' => 'hari',
        'h' => 'jam',
        'i' => 'menit',
        's' => 'detik',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? '' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? implode(', ', $string) . ' yang lalu' : 'baru saja';
}
?>

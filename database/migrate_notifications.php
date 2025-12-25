<?php
require_once 'koneksi.php';

$query = "CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('like', 'comment', 'follow') NOT NULL,
    actor_id INT NOT NULL,
    reference_id INT,
    is_read TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";

if (mysqli_query($koneksi, $query)) {
    echo "Table 'notifications' created successfully or already exists.\n";
} else {
    echo "Error creating table: " . mysqli_error($koneksi) . "\n";
}
?>

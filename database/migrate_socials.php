<?php
require_once 'koneksi.php';

$alter_queries = [
    "ALTER TABLE users ADD COLUMN social_twitter VARCHAR(255) DEFAULT NULL",
    "ALTER TABLE users ADD COLUMN social_instagram VARCHAR(255) DEFAULT NULL",
    "ALTER TABLE users ADD COLUMN social_website VARCHAR(255) DEFAULT NULL"
];

foreach ($alter_queries as $query) {
    if (mysqli_query($koneksi, $query)) {
        echo "Column added successfully: $query\n";
    } else {
        // Ignore "Duplicate column" error
        if (strpos(mysqli_error($koneksi), "Duplicate column") !== false) {
             echo "Column already exists (Skipped): $query\n";
        } else {
             echo "Error adding column: " . mysqli_error($koneksi) . "\n";
        }
    }
}
?>

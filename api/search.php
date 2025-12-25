<?php
require_once '../database/koneksi.php';

header('Content-Type: application/json');

if (!isset($_GET['q']) || empty(trim($_GET['q']))) {
    echo json_encode([]);
    exit;
}

$search = mysqli_real_escape_string($koneksi, trim($_GET['q']));

// Search logic: Title, Author Name, Genre
$query = "SELECT books.id, books.title, books.cover_image, books.genre, users.name as author_name 
          FROM books 
          JOIN users ON books.user_id = users.id 
          WHERE books.status = 'published' 
          AND (books.title LIKE '%$search%' OR users.name LIKE '%$search%' OR books.genre LIKE '%$search%') 
          LIMIT 5";

$result = mysqli_query($koneksi, $query);
$books = [];

while ($row = mysqli_fetch_assoc($result)) {
    $books[] = [
        'id' => $row['id'],
        'title' => $row['title'],
        'author' => $row['author_name'],
        'cover' => $row['cover_image'] ? $row['cover_image'] : null,
        'genre' => explode(',', $row['genre'])[0] // Take first genre
    ];
}

echo json_encode($books);

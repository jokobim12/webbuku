<?php
$host = "localhost";
$user = "root";
$pass = "1234";

$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$sql = "CREATE DATABASE IF NOT EXISTS webbuku";
if ($conn->query($sql) === TRUE) {
    echo "Database created successfully";
} else {
    echo "Error creating database: " . $conn->error;
}

$conn->close();
?>

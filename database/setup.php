<?php
require_once 'koneksi.php';

$sql = file_get_contents(__DIR__ . '/database_setup.sql');

if (mysqli_multi_query($koneksi, $sql)) {
    echo "Tabel berhasil dibuat atau sudah ada.";
    do {
        if ($result = mysqli_store_result($koneksi)) {
            mysqli_free_result($result);
        }
    } while (mysqli_next_result($koneksi));
} else {
    echo "Gagal membuat tabel: " . mysqli_error($koneksi);
}

mysqli_close($koneksi);
?>

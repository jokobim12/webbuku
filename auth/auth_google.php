<?php
session_start();
require_once '../database/koneksi.php';

// KONFIGURASI GOOGLE (Load dari .env)
$envPath = __DIR__ . '/../.env';
if (file_exists($envPath)) {
    $env = parse_ini_file($envPath);
    $client_id = $env['GOOGLE_CLIENT_ID'] ?? '';
    $client_secret = $env['GOOGLE_CLIENT_SECRET'] ?? '';
    $redirect_uri = $env['GOOGLE_REDIRECT_URI'] ?? '';
} else {
    die('Error: File .env tidak ditemukan. Silakan buat file .env sesuai panduan.');
}

if (isset($_GET['code'])) {
    // 1. Dapatkan Token
    $token_url = 'https://oauth2.googleapis.com/token';
    $post_data = [
        'code' => $_GET['code'],
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'redirect_uri' => $redirect_uri,
        'grant_type' => 'authorization_code'
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $token_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $token_data = json_decode($response, true);

    if (isset($token_data['access_token'])) {
        // 2. Dapatkan Profil User
        $user_info_url = 'https://www.googleapis.com/oauth2/v2/userinfo';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $user_info_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer ' . $token_data['access_token']]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $user_info = curl_exec($ch);
        curl_close($ch);

        $google_user = json_decode($user_info, true);

        // 3. Simpan/Update ke Database
        $google_id = $google_user['id'];
        $name = $google_user['name'];
        $email = $google_user['email'];
        $avatar = $google_user['picture'];

        // Cek user ada atau tidak
        $check_sql = "SELECT * FROM users WHERE google_id = '$google_id'";
        $result = mysqli_query($koneksi, $check_sql);

        if (mysqli_num_rows($result) > 0) {
            // User lama
            $user_data = mysqli_fetch_assoc($result);
            $_SESSION['user_id'] = $user_data['id'];
            $_SESSION['role'] = $user_data['role'];
            $_SESSION['name'] = $user_data['name'];
            $_SESSION['avatar'] = $user_data['avatar'];
        } else {
            // User baru
            $insert_sql = "INSERT INTO users (google_id, name, email, avatar, role) VALUES ('$google_id', '$name', '$email', '$avatar', 'free')";
            if (mysqli_query($koneksi, $insert_sql)) {
                $_SESSION['user_id'] = mysqli_insert_id($koneksi);
                $_SESSION['role'] = 'free';
                $_SESSION['name'] = $name;
                $_SESSION['avatar'] = $avatar;
            } else {
                die("Error: " . mysqli_error($koneksi));
            }
        }

        // 4. Redirect ke Halaman Buku (Library)
        header('Location: ../page/user/buku.php');
        exit();

    } else {
        echo "Gagal login dengan Google.";
        exit();
    }
} else {
    // Redirect ke Google Login URL
    $auth_url = 'https://accounts.google.com/o/oauth2/auth?' . http_build_query([
        'response_type' => 'code',
        'client_id' => $client_id,
        'redirect_uri' => $redirect_uri,
        'scope' => 'email profile',
        'access_type' => 'offline'
    ]);
    header('Location: ' . $auth_url);
    exit();
}
?>

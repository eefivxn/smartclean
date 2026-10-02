<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'smartclean');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conn->set_charset('utf8mb4');

if ($conn->connect_error) {
    error_log("Koneksi DB gagal: " . $conn->connect_error);
    die("Terjadi kesalahan pada sistem. Silakan coba beberapa saat lagi.");
}
?>

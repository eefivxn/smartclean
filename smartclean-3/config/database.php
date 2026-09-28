<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'smartclean');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$conn->set_charset('utf8mb4');

if ($conn->connect_error) {
    die('<div style="font-family:sans-serif;color:red;padding:20px;background:#fff1f0;border:1px solid #fca5a5;margin:20px;border-radius:8px;">
        ❌ <strong>Koneksi database gagal:</strong> ' . $conn->connect_error . '<br><br>
        Pastikan MySQL sudah <strong>Running</strong> di XAMPP dan database <strong>smartclean</strong> sudah dibuat.
    </div>');
}
?>

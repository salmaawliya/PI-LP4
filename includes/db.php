<?php
$host = "localhost";
$user = "root";  // Ubah jika MySQL menggunakan user lain
$pass = "";      // Isi password MySQL kamu (jika ada)
$db   = "lp4_db";  // Ganti dengan nama database yang kamu gunakan

$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}
?>

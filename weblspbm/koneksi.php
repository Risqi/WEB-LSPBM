<?php
// 1. Konfigurasi parameter database
$host = "localhost";      // Nama host server database (default XAMPP adalah localhost)
$user = "root";           // Username default untuk masuk ke phpMyAdmin
$pass = "";               // Password default phpMyAdmin di XAMPP (biasanya dikosongkan)
$db   = "weblspbm";       // Nama database yang kita gunakan sesuai kesepakatan

// 2. Perintah untuk menghubungkan PHP ke MySQLi
$koneksi = mysqli_connect($host, $user, $pass, $db);

// 3. Validasi/Cek apakah koneksi berhasil atau gagal
if (!$koneksi) {
    // Jika gagal terhubung, hentikan proses sistem dan tampilkan pesan error-nya
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>
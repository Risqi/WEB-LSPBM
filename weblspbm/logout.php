<?php
session_start();
// Menghapus semua session yang terdaftar
session_unset();
// Menghancurkan session
session_destroy();

// Alihkan kembali ke halaman login
header("Location: login.php");
exit;
?>
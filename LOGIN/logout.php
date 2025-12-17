<?php
session_start(); // 1. Mulai session untuk mengakses data saat ini

// 2. Hapus semua data session
session_unset(); 
session_destroy(); 

// 3. Arahkan (Redirect) kembali ke file login.php
header("Location: login.php"); 
exit();
?>
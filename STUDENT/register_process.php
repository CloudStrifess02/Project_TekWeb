<?php
session_start();
require_once '../LOGIN/connection.php';

if (!isset($_SESSION['user_login']) || !isset($_GET['pos_id'])) {
    header("Location: student_dashboard.php");
    exit();
}

$pos_id = intval($_GET['pos_id']);
$email = $_SESSION['email'];

// Ambil User ID
$q = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
$u = mysqli_fetch_assoc($q);
$user_id = $u['id'];

// Lakukan Pendaftaran
$insert = "INSERT INTO registrations (user_id, position_id) VALUES ('$user_id', '$pos_id')";

if (mysqli_query($conn, $insert)) {
    echo "<script>
            alert('Berhasil Mendaftar!');
            window.location.href='student_dashboard.php';
          </script>";
} else {
    echo "<script>
            alert('Gagal! Mungkin Anda sudah mendaftar di posisi ini.');
            window.location.href='student_dashboard.php';
          </script>";
}
?>
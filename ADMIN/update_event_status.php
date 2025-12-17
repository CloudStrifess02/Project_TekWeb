<?php
session_start();
require_once '../koneksi.php';

// Cek akses admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../LOGIN/login.php");
    exit();
}

// Ambil ID dari URL
if (isset($_GET['id'])) {
    $event_id = $_GET['id'];

    // Update status menjadi published
    $query = "UPDATE events SET event_status = 'published' WHERE event_id = '$event_id'";
    
    if (mysqli_query($conn, $query)) {
        header("Location: admin_dashboard.php?msg=published");
    } else {
        echo "Gagal mengupdate status: " . mysqli_error($conn);
    }
} else {
    header("Location: admin_dashboard.php");
}
?>
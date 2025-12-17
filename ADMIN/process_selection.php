<?php
session_start();
require_once '../koneksi.php';

// Cek akses admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Unauthorized";
    exit();
}

if (isset($_GET['id']) && isset($_GET['action'])) {
    $reg_id = $_GET['id'];
    $action = $_GET['action'];

    // Update status pendaftaran
    $query = "UPDATE registrations SET status = ? WHERE registration_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "si", $action, $reg_id);

    if (mysqli_stmt_execute($stmt)) {
        // PENTING: Harus mencetak kata "Success" agar dibaca oleh JavaScript
        echo "Success"; 
    } else {
        http_response_code(500);
        echo "Database Error: " . mysqli_error($conn);
    }
} else {
    echo "Invalid Parameters";
}
?>
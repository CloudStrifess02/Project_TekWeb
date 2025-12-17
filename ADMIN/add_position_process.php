<?php
session_start();
require_once '../LOGIN/connection.php';

// 1. CEK KEAMANAN
if (!isset($_SESSION['user_login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../LOGIN/login.php");
    exit();
}

// 2. CEK APAKAH FORM DISUBMIT
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validasi: Pastikan event_id ada
    if (empty($_POST['event_id'])) {
        die("Error Fatal: ID Event tidak ditemukan. Pastikan Anda mengakses dari halaman yang benar.");
    }

    $event_id = $_POST['event_id'];
    $name     = $_POST['position_name'];
    $quota    = $_POST['quota'];
    $desc     = isset($_POST['description']) ? $_POST['description'] : ''; 

    // 3. QUERY INSERT
    $query = "INSERT INTO positions (event_id, position_name, quota, description) VALUES (?, ?, ?, ?)";
    
    if ($stmt = mysqli_prepare($conn, $query)) {
        // Bind parameter
        mysqli_stmt_bind_param($stmt, "isis", $event_id, $name, $quota, $desc);
        
        if (mysqli_stmt_execute($stmt)) {
            // BERHASIL: Balik ke halaman view_positions
            header("Location: view_positions.php?event_id=" . $event_id . "&msg=added");
            exit();
        } else {
            echo "Gagal menyimpan data: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo "Error Query: " . mysqli_error($conn);
    }

} else {
    // Jika user coba buka file ini langsung tanpa submit form
    header("Location: admin_dashboard.php");
    exit();
}
?>
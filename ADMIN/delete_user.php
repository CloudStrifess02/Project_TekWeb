<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['user_login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../LOGIN/login.php");
    exit();
}

if (isset($_GET['id'])) {
    
    $user_id = mysqli_real_escape_string($conn, $_GET['id']);

    $query_cek = "SELECT cv_file FROM registrations WHERE user_id = '$user_id'";
    $result_cek = mysqli_query($conn, $query_cek);
    $data = mysqli_fetch_assoc($result_cek);
    
    $query_delete = "DELETE FROM users WHERE id = '$user_id'";

    if (mysqli_query($conn, $query_delete)) {
        
        if ($data && !empty($data['cv_file'])) {
            $file_path = "../uploads/docs/" . $data['cv_file'];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }

        header("Location: user_view.php?msg=deleted");
        exit();
    } else {
        echo "Gagal menghapus event: " . mysqli_error($conn);
    }
} else {
    header("Location: user_view.php");
    exit();
}
?>
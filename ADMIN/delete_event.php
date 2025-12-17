<?php
session_start();
require_once '../koneksi.php';

// 1. Cek Keamanan: Pastikan yang akses adalah Admin
if (!isset($_SESSION['user_login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../LOGIN/login.php");
    exit();
}

// 2. Cek apakah ada ID yang dikirim
if (isset($_GET['id'])) {
    $event_id = mysqli_real_escape_string($conn, $_GET['id']);

    // --- STEP 1: AMBIL DATA GAMBAR DULU ---
    // Kita perlu nama file posternya supaya bisa dihapus dari folder 'uploads' agar tidak menuh-menuhin memori.
    $query_cek = "SELECT event_poster FROM events WHERE event_id = '$event_id'";
    $result_cek = mysqli_query($conn, $query_cek);
    $data = mysqli_fetch_assoc($result_cek);

    // --- STEP 2: HAPUS EVENT DARI DATABASE ---
    // Karena setting database 'ON DELETE CASCADE', maka Posisi dan Pendaftar ikut terhapus otomatis.
    $query_delete = "DELETE FROM events WHERE event_id = '$event_id'";

    if (mysqli_query($conn, $query_delete)) {
        
        // --- STEP 3: HAPUS FILE POSTER FISIK (Opsional tapi Bagus) ---
        if ($data && !empty($data['event_poster'])) {
            $file_path = "../uploads/" . $data['event_poster'];
            if (file_exists($file_path)) {
                unlink($file_path); // Perintah hapus file di PHP
            }
        }

        // Redirect kembali ke Dashboard dengan pesan sukses
        header("Location: admin_dashboard.php?msg=deleted");
        exit();
    } else {
        echo "Gagal menghapus event: " . mysqli_error($conn);
    }
} else {
    // Jika tidak ada ID, kembalikan ke dashboard
    header("Location: admin_dashboard.php");
    exit();
}
?>
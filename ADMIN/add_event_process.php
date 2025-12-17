<?php
session_start();
include '../LOGIN/connection.php'; // Pastikan path koneksi benar

// Cek apakah form disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Ambil Data Text & Amankan
    $name = $_POST['event_name'];
    $desc = $_POST['event_description'];
    $start_date = $_POST['event_date'];
    $end_date = !empty($_POST['event_end_date']) ? $_POST['event_end_date'] : NULL; // Bisa NULL
    $location = $_POST['event_location'];
    $category = $_POST['event_category'];
    $status = $_POST['event_status'];

    // --- BARU: VALIDASI TANGGAL (Server Side) ---
    // Mencegah user nakal yang mematikan JavaScript
    if ($end_date != NULL && $end_date < $start_date) {
        echo "<script>
            alert('Gagal! Tanggal selesai tidak boleh sebelum tanggal mulai.');
            window.history.back(); // Kembalikan user ke form
        </script>";
        exit(); // Stop proses disini
    }

    // 2. Proses Upload Poster
    $poster_name = null; // Default null jika tidak ada gambar
    
    if (isset($_FILES['event_poster']) && $_FILES['event_poster']['error'] == 0) {
        $target_dir = "../uploads/"; // Folder tempat simpan gambar
        
        // Buat folder jika belum ada
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Generate nama file unik biar tidak bentrok (contoh: event_12345.jpg)
        $file_extension = pathinfo($_FILES["event_poster"]["name"], PATHINFO_EXTENSION);
        $poster_name = "event_" . time() . "." . $file_extension;
        $target_file = $target_dir . $poster_name;

        // Cek tipe file (Wajib Gambar)
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($file_extension), $allowed_types)) {
            if (move_uploaded_file($_FILES["event_poster"]["tmp_name"], $target_file)) {
                // Upload Sukses
            } else {
                echo "Gagal mengupload gambar.";
                exit();
            }
        } else {
            echo "Format file tidak valid! Hanya JPG/PNG yang diperbolehkan.";
            exit();
        }
    }

    // 3. Simpan ke Database (Pakai Prepared Statement biar aman)
    $stmt = $conn->prepare("INSERT INTO events (event_name, event_description, event_date, event_end_date, event_location, event_category, event_poster, event_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    // Bind Parameter (s = string)
    $stmt->bind_param("ssssssss", $name, $desc, $start_date, $end_date, $location, $category, $poster_name, $status);

    if ($stmt->execute()) {
        // Berhasil
        header("Location: admin_dashboard.php?msg=success");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

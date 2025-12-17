<?php
session_start();
include '../koneksi.php';

// Cek Login
if (!isset($_SESSION['id'])) {
    header("Location: ../LOGIN/login.php");
    exit;
}

if (isset($_POST['create_event'])) {
    $id = $_SESSION['id'];
    $name = mysqli_real_escape_string($conn, $_POST['event_name']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $date_start = $_POST['event_date'];
    $date_end = $_POST['event_end_date'];
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $category = $_POST['category'];

    // Upload Poster
    $poster = "default_event.png";
    if (!empty($_FILES['poster']['name'])) {
        $poster = "event_" . time() . ".png";
        move_uploaded_file($_FILES['poster']['tmp_name'], "../uploads/" . $poster);
    }

    // Insert dengan created_by = id
    $query = "INSERT INTO events (event_name, event_description, event_date, event_end_date, event_location, event_category, event_poster, event_status, created_by) 
              VALUES ('$name', '$desc', '$date_start', '$date_end', '$location', '$category', '$poster', 'published', '$id')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Event berhasil dibuat! Anda sekarang adalah Ketua Panitia event ini.'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Buat Event Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5 mb-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4>Buat Event Baru</h4>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label>Nama Event</label>
                        <input type="text" name="event_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Tanggal Mulai</label>
                            <input type="date" name="event_date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Tanggal Selesai</label>
                            <input type="date" name="event_end_date" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Lokasi</label>
                            <input type="text" name="location" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Kategori</label>
                            <select name="category" class="form-select">
                                <option value="Seminar">Seminar</option>
                                <option value="Lomba">Lomba</option>
                                <option value="Open Recruitment">Open Recruitment</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label>Poster Event</label>
                        <input type="file" name="poster" class="form-control" accept="image/*">
                    </div>
                    <button type="submit" name="create_event" class="btn btn-success w-100">Publikasikan Event</button>
                    <a href="index.php" class="btn btn-secondary w-100 mt-2">Batal</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
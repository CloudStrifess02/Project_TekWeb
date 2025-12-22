<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['id'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}

if (isset($_POST['create_event'])) {

    $user_id = $_SESSION['id'];

    $event_name  = trim($_POST['event_name']);
    $description = trim($_POST['description']);
    $date_start  = $_POST['event_date'];
    $date_end    = $_POST['event_end_date'];
    $location    = trim($_POST['location']);
    $category    = $_POST['category'];


    if (
        empty($event_name) ||
        empty($description) ||
        empty($date_start) ||
        empty($date_end) ||
        empty($location)
    ) {
        $_SESSION['error'] = 'Semua field wajib diisi.';
        header("Location: create_event.php");
        exit();
    }

    if ($date_end < $date_start) {
        $_SESSION['error'] = 'Tanggal selesai tidak boleh lebih awal dari tanggal mulai.';
        header("Location: create_event.php");
        exit();
    }

    $poster = 'default_event.png';

    if (!empty($_FILES['poster']['name'])) {

        $allowed_types = ['image/jpeg', 'image/png'];
        $max_size = 2 * 1024 * 1024; // 2MB

        if (!in_array($_FILES['poster']['type'], $allowed_types)) {
            $_SESSION['error'] = 'Poster harus JPG atau PNG.';
            header("Location: create_event.php");
            exit();
        }

        if ($_FILES['poster']['size'] > $max_size) {
            $_SESSION['error'] = 'Ukuran poster maksimal 2MB.';
            header("Location: create_event.php");
            exit();
        }

        $ext = pathinfo($_FILES['poster']['name'], PATHINFO_EXTENSION);
        $poster = 'event_' . time() . '_' . rand(100,999) . '.' . $ext;

        move_uploaded_file(
            $_FILES['poster']['tmp_name'],
            '../uploads/' . $poster
        );
    }

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO events 
        (event_name, event_description, event_date, event_end_date, event_location, event_category, event_poster, event_status, created_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, 'draft', ?)"
    );

    mysqli_stmt_bind_param(
        $stmt,
        "sssssssi",
        $event_name,
        $description,
        $date_start,
        $date_end,
        $location,
        $category,
        $poster,
        $user_id
    );

    if (!mysqli_stmt_execute($stmt)) {
        $_SESSION['error'] = 'Gagal membuat event.';
        header("Location: create_event.php");
        exit();
    }

    $_SESSION['success'] = 'Event berhasil dibuat. Status masih draft.';
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buat Event Baru</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5 mb-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Buat Event Baru</h4>
        </div>

        <div class="card-body">

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">

                <div class="mb-3">
                    <label class="form-label">Nama Event</label>
                    <input type="text" name="event_name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control" rows="4" required></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" name="event_date" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" name="event_end_date" class="form-control" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Lokasi</label>
                        <input type="text" name="location" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kategori</label>
                        <select name="category" class="form-select">
                            <option value="Seminar">Seminar</option>
                            <option value="Lomba">Lomba</option>
                            <option value="Open Recruitment">Open Recruitment</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Poster Event</label>
                    <input type="file" name="poster" class="form-control" accept="image/png, image/jpeg">
                    <div class="form-text">JPG / PNG, max 2MB</div>
                </div>

                <button type="submit" name="create_event" class="btn btn-success w-100">
                    Simpan Event
                </button>

                <a href="index.php" class="btn btn-secondary w-100 mt-2">
                    Batal
                </a>

            </form>
        </div>
    </div>
</div>

</body>
</html>

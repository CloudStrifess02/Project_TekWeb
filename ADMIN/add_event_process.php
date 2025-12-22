<?php
session_start();
require_once '../koneksi.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name        = $_POST['event_name'];
    $desc        = $_POST['event_description'];
    $start_date  = $_POST['event_date'];
    $end_date    = !empty($_POST['event_end_date']) ? $_POST['event_end_date'] : null;
    $location    = $_POST['event_location'];
    $category    = $_POST['event_category'];
    $status      = $_POST['event_status'];

    if ($end_date !== null && $end_date < $start_date) {
        $_SESSION['notif'] = [
            'type' => 'error',
            'msg'  => 'Tanggal selesai tidak boleh sebelum tanggal mulai.'
        ];
        header("Location: add_event.php");
        exit;
    }

    $poster_name = null;

    if (isset($_FILES['event_poster']) && $_FILES['event_poster']['error'] === 0) {

        $target_dir = "../uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $file_extension = strtolower(pathinfo($_FILES["event_poster"]["name"], PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($file_extension, $allowed_types)) {
            $_SESSION['notif'] = [
                'type' => 'error',
                'msg'  => 'Format poster tidak valid. Gunakan JPG, PNG, atau GIF.'
            ];
            header("Location: add_event.php");
            exit;
        }

        $poster_name = "event_" . time() . "." . $file_extension;
        $target_file = $target_dir . $poster_name;

        if (!move_uploaded_file($_FILES["event_poster"]["tmp_name"], $target_file)) {
            $_SESSION['notif'] = [
                'type' => 'error',
                'msg'  => 'Gagal mengupload poster event.'
            ];
            header("Location: add_event.php");
            exit;
        }
    }

    $stmt = $conn->prepare("
        INSERT INTO events 
        (event_name, event_description, event_date, event_end_date, event_location, event_category, event_poster, event_status)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssssssss",
        $name,
        $desc,
        $start_date,
        $end_date,
        $location,
        $category,
        $poster_name,
        $status
    );

    if ($stmt->execute()) {
        $_SESSION['notif'] = [
            'type' => 'success',
            'msg'  => 'Event berhasil ditambahkan.'
        ];
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $_SESSION['notif'] = [
            'type' => 'error',
            'msg'  => 'Gagal menyimpan event.'
        ];
        header("Location: add_event.php");
        exit;
    }

    $stmt->close();
}

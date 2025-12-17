<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['user_login'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $email_sess = $_SESSION['email'];
    $query_user = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email_sess'");
    $data_user = mysqli_fetch_assoc($query_user);
    
    if (!$data_user) {
        die("Error: User tidak ditemukan di database.");
    }
    $user_id = $data_user['id']; 

    $pos1 = $_POST['pos1'];
    $pos2 = !empty($_POST['pos2']) ? $_POST['pos2'] : "NULL";
    $motivation = mysqli_real_escape_string($conn, $_POST['motivation']);
    $experience = mysqli_real_escape_string($conn, $_POST['experience']);

    $target_dir = "../uploads/docs/";
    if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);

    $cv_file = null;
    if (isset($_FILES["cv"]) && $_FILES["cv"]["error"] == 0) {
        $cv_ext = pathinfo($_FILES["cv"]["name"], PATHINFO_EXTENSION);
        $cv_file = "CV_" . time() . "_" . $user_id . "." . $cv_ext;
        move_uploaded_file($_FILES["cv"]["tmp_name"], $target_dir . $cv_file);
    }

    $port_file = null;
    if (isset($_FILES["portfolio"]) && $_FILES["portfolio"]["error"] == 0) {
        $port_ext = pathinfo($_FILES["portfolio"]["name"], PATHINFO_EXTENSION);
        $port_file = "PORT_" . time() . "_" . $user_id . "." . $port_ext;
        move_uploaded_file($_FILES["portfolio"]["tmp_name"], $target_dir . $port_file);
    }

    $interview_time = date('Y-m-d 10:00:00', strtotime('+2 days'));
    $meet_link = "https://meet.google.com/xyz-" . substr(md5(time()), 0, 8);

    $sql = "INSERT INTO registrations 
            (user_id, position_id, position_id_2, motivation, experience, cv_file, portfolio_file, interview_time, meet_link, status) 
            VALUES 
            ('$user_id', '$pos1', " . ($pos2 == "NULL" ? "NULL" : "'$pos2'") . ", '$motivation', '$experience', '$cv_file', '$port_file', '$interview_time', '$meet_link', 'pending')";

    if (mysqli_query($conn, $sql)) {
        $last_id = mysqli_insert_id($conn);
        header("Location: interview_info.php?reg_id=$last_id");
        exit();
    } else {
        echo "Error Database: " . mysqli_error($conn);
    }
}
?>
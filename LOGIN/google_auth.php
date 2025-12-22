<?php
session_start();
require_once 'config_google.php';
require_once '../koneksi.php';

if (!isset($_GET['code'])) {
    header("Location: login.php");
    exit();
}

$token = $client->fetchAccessTokenWithAuthCode($_GET['code']);

if (isset($token['error'])) {
    $_SESSION['login_error'] = 'Gagal autentikasi Google.';
    header("Location: login.php");
    exit();
}

$client->setAccessToken($token['access_token']);

$google_oauth = new Google_Service_Oauth2($client);
$google_user  = $google_oauth->userinfo->get();

$email_google = $google_user->email;
$name_google  = $google_user->name;

$domain = substr(strrchr($email_google, "@"), 1);
if ($domain !== 'john.petra.ac.id') {
    $_SESSION['login_error'] = 'Login ditolak. Gunakan email @john.petra.ac.id';
    header("Location: login.php");
    exit();
}

$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $email_google);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user   = mysqli_fetch_assoc($result);


if ($user) {

    $_SESSION['user_login'] = true;
    $_SESSION['id']    = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['name']  = $user['name'];
    $_SESSION['role']  = $user['role'];

    if ($user['role'] === 'admin') {
        header("Location: ../ADMIN/admin_dashboard.php");
    } else {
        header("Location: ../STUDENT/student_dashboard.php");
    }
    exit();
}

/* =========================
   JIKA USER BARU
========================= */
$default_role = 'student';
$dummy_password = password_hash(
    'google_' . bin2hex(random_bytes(5)),
    PASSWORD_DEFAULT
);

$insert = mysqli_prepare(
    $conn,
    "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)"
);
mysqli_stmt_bind_param(
    $insert,
    "ssss",
    $name_google,
    $email_google,
    $dummy_password,
    $default_role
);

if (!mysqli_stmt_execute($insert)) {
    $_SESSION['login_error'] = 'Gagal membuat akun baru.';
    header("Location: login.php");
    exit();
}

$_SESSION['user_login'] = true;
$_SESSION['id']    = mysqli_insert_id($conn);
$_SESSION['email'] = $email_google;
$_SESSION['name']  = $name_google;
$_SESSION['role']  = $default_role;

header("Location: ../STUDENT/student_dashboard.php");
exit();

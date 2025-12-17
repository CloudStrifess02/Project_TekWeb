<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "test2"; // ganti nama database

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>

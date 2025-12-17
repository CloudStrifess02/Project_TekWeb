<?php
// login_process.php
session_start();
require_once 'connection.php'; // Gunakan require_once agar lebih aman

// Pastikan data dikirim via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 1. PERBAIKAN NAMA TABEL: Ubah 'user' menjadi 'users'
    $query = "SELECT * FROM users WHERE email = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    // Ambil data user
    $row = mysqli_fetch_assoc($result);

    // 2. PERBAIKAN LOGIKA IF: Cek variabel $row
    if ($row) {
        // Verifikasi password HASH
        if (password_verify($password, $row['password'])) {

            // Simpan session
            $_SESSION['user_login'] = true; // Penanda sudah login
            $_SESSION['email'] = $row['email'];
            $_SESSION['role']  = $row['role'];
            $_SESSION['name']  = $row['name']; // 3. PERBAIKAN KOLOM: 'username' jadi 'name'

            // 4. PERBAIKAN ROLE & REDIRECT: 'mahasiswa' jadi 'student'
            if ($row['role'] === "admin") {
                header("Location: ../ADMIN/admin_dashboard.php");
                exit();
            } else if ($row['role'] === "student") {
                // Pastikan foldernya STUDENT (sesuai yang kita buat tadi)
                header("Location: ../STUDENT/student_dashboard.php");
                exit();
            } else {
                echo "Role tidak dikenali!";
            }

        } else {
            // Password Salah
            echo "<script>
                    alert('Login gagal! Password salah.');
                    window.location.href = 'login.php';
                  </script>";
        }
    } else {
        // Email Tidak Ditemukan
        echo "<script>
                alert('Login gagal! Email tidak terdaftar.');
                window.location.href = 'login.php';
              </script>";
    }
}
?>
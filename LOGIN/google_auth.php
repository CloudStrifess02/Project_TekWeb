<?php
// google_auth.php
session_start();
require_once 'config_google.php';
require_once '../koneksi.php'; // Wajib koneksi database

if (isset($_GET['code'])) {
    
    // 1. Tukar Kode dengan Token
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    
    if(!isset($token['error'])){
        $client->setAccessToken($token['access_token']);

        // 2. Ambil Profil Google
        $google_oauth = new Google_Service_Oauth2($client);
        $google_account_info = $google_oauth->userinfo->get();
        
        $email_google = $google_account_info->email;
        $name_google  = $google_account_info->name;
        
        // --- STEP 1: FILTER DOMAIN (SATPAM UTAMA) ---
        $domain = substr(strrchr($email_google, "@"), 1);
        if ($domain !== 'john.petra.ac.id') {
             echo "<script>
                    alert('LOGIN DITOLAK!\\nHanya email @john.petra.ac.id yang diperbolehkan.');
                    window.location.href = 'login.php';
                  </script>";
             exit();
        }

        // --- STEP 2: CEK DATABASE ---
        // Cek apakah email ini sudah ada di database kita?
        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email_google);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user) {
            // === SKENARIO A: USER LAMA (SUDAH ADA) ===
            // Cek Role dia apa (Admin / Student)
            
            $_SESSION['user_login'] = true;
            $_SESSION['email'] = $user['email'];
            $_SESSION['name']  = $user['name'];
            $_SESSION['role']  = $user['role'];
            $_SESSION['id']    = $user['id'];

            // Redirect sesuai jabatan
            if ($user['role'] == 'admin') {
                header("Location: ../ADMIN/admin_dashboard.php");
            } else {
                header("Location: ../STUDENT/student_dashboard.php");
            }
            exit();

        } else {
            // === SKENARIO B: USER BARU (AUTO-REGISTER SEBAGAI STUDENT) ===
            // Karena belum ada, kita anggap dia Mahasiswa baru.
            // Kita masukkan data dia ke database secara otomatis.

            $default_role = 'student';
            // Kita buat password acak (dummy) karena dia login pakai Google
            $dummy_password = password_hash("google_access_" . rand(1000,9999), PASSWORD_DEFAULT); 

            $insert = mysqli_prepare($conn, "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($insert, "ssss", $name_google, $email_google, $dummy_password, $default_role);
            
            if (mysqli_stmt_execute($insert)) {
                // Jika sukses disimpan, langsung set session (Auto Login)
                $_SESSION['user_login'] = true;
                $_SESSION['email'] = $email_google;
                $_SESSION['name']  = $name_google;
                $_SESSION['role']  = 'student'; // Pasti Student

                // Lempar ke Dashboard Student
                header("Location: ../STUDENT/student_dashboard.php");
                exit();
            } else {
                echo "Gagal mendaftarkan akun baru: " . mysqli_error($conn);
            }
        }
        
    } else {
        echo "Gagal login! Token error.";
    }
} else {
    header("Location: login.php");
    exit();
}
?>
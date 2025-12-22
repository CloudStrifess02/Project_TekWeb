<?php
session_start();
require_once '../koneksi.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email = ?";
    
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        if (password_verify($password, $row['password'])) {

            $_SESSION['user_login'] = true;
            $_SESSION['id'] = $row['id'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['role']  = $row['role'];
            $_SESSION['name']  = $row['name']; 

            if ($row['role'] === "admin") {
                header("Location: ../ADMIN/admin_dashboard.php");
                exit();
            } else if ($row['role'] === "student") {
                header("Location: ../STUDENT/student_dashboard.php");
                exit();
            } else {
                echo "Role tidak dikenali!";
            }

        } else {
            echo "<script>
                    window.location.href = 'login.php';
                  </script>";
        }
    } else {
        echo "<script>
                window.location.href = 'login.php';
              </script>";
    }
}
?>
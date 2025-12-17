<?php
require_once '../koneksi.php';

if (isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $domain = substr(strrchr($email, "@"), 1);
    
    if ($domain !== 'john.petra.ac.id') {
        echo "<script>
            alert('REGISTRASI DITOLAK!\\nHanya email @john.petra.ac.id yang boleh mendaftar.');
            window.location.href='register.php';
        </script>";
        exit();
    }
    $cek_email = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
    if (mysqli_num_rows($cek_email) > 0) {
        echo "<script>alert('Email ini sudah terdaftar!');</script>";
    } else {
        $query = "INSERT INTO users (name, email, password, role) VALUES ('$name', '$email', '$password', 'student')";
        
        if (mysqli_query($conn, $query)) {
            echo "<script>alert('Registrasi Berhasil! Silakan Login.'); window.location.href='login.php';</script>";
        } else {
            echo "Error: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Registrasi Khusus Petra</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .container { width: 300px; margin: auto; padding: 20px; border: 1px solid #ccc; border-radius: 5px; }
        input, select { width: 100%; margin-bottom: 10px; padding: 8px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #2196F3; color: white; border: none; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container">
        <h2 style="text-align:center;">Register<br><small style="font-size:12px">(Khusus @john.petra.ac.id)</small></h2>
        <form method="POST">
            <label>Nama Lengkap:</label>
            <input type="text" name="name" required>

            <label>Email:</label>
            <input type="email" name="email" required placeholder="user@john.petra.ac.id">

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit" name="register">Daftar Sekarang</button>
        </form>
        <p style="text-align:center;"><a href="login.php">Sudah punya akun? Login</a></p>
    </div>
</body>
</html>
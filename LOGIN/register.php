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
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register Khusus Petra</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-image: url('../bg2.jpeg'); 
      background-size: cover;
      background-position: center;
      background-attachment: fixed;
      margin: 0;
    }
    body::before {
      content: "";
      position: fixed;
      top: 0; left: 0; right: 0; bottom: 0;
      background-color: rgba(255,255,255,0.2); 
      z-index: -1;
    }

    .register-card {
      max-width: 600px;
      width: 100%;
      margin: 80px auto;
      padding: 30px;
      background: rgba(255,255,255,0.95); 
      border-radius: 20px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.2);
      color: #1e293b; 
    }

    .register-card h2 {
      font-weight: 700;
      background: linear-gradient(to right, #4d6980, #4CAF50);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 10px;
    }

    .info-text {
      font-size: 13px;
      color: #64748b;
      font-style: italic;
    }

    .form-label {
      font-weight: 600;
      color: #1e293b;
    }

    .form-control {
      border-radius: 12px;
    }

    .btn-register {
      background: linear-gradient(135deg, #4d6980 0%, #4CAF50 100%);
      color: white;
      border: none;
      padding: 10px;
      border-radius: 12px;
      font-weight: 600;
      transition: 0.3s;
    }
    .btn-register:hover {
      opacity: 0.9;
      box-shadow: 0 5px 15px rgba(77,105,128,0.4);
    }

    .login-link {
      text-align: center;
      margin-top: 15px;
      color: #334155;
    }
    .login-link a {
      color: #4CAF50;
      font-weight: 600;
      text-decoration: none;
    }
    .login-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">

  <div class="register-card">
    <div class="text-center mb-4">
      <h2>Register</h2>
      <p class="info-text">(Wajib menggunakan email @john.petra.ac.id)</p>
    </div>

    <form method="POST" class="needs-validation" novalidate>
      
      <div class="mb-3">
        <label for="name" class="form-label">Nama Lengkap</label>
        <input type="text" class="form-control" id="name" name="name" required placeholder="Nama Lengkap Anda">
        <div class="invalid-feedback">Nama wajib diisi.</div>
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email Petra</label>
        <input type="email" class="form-control" id="email" name="email" required placeholder="c142xxxxx@john.petra.ac.id">
        <div class="invalid-feedback">Masukkan email yang valid.</div>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required placeholder="Buat Password yang aman">
        <div class="invalid-feedback">Password wajib diisi.</div>
      </div>

      <button type="submit" name="register" class="btn btn-register w-100 mb-3">Daftar Sekarang</button>
    </form>

    <div class="login-link">
      Sudah punya akun? <a href="login.php">Login disini</a>
    </div>
  </div>

</html>
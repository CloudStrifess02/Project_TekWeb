<?php
// --- BAGIAN LOGIK (Dari Kode Asli) ---
// Memanggil file konfigurasi Google Client Library
require_once 'config_google.php'; 
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-image: url("../bg2.jpeg");
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

    .login-card {
      max-width: 600px;
      width: 100%;
      margin: 90px auto;
      padding: 30px;
      background: rgba(255,255,255,0.95); 
      border-radius: 20px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.2);
      color: #1e293b; 
    }

    .login-card h3 {
      font-weight: 700;
      background: linear-gradient(to right, #4d6980, #4CAF50);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      margin-bottom: 20px;
    }

    .form-label {
      font-weight: 600;
      color: #1e293b;
    }

    .form-control {
      border-radius: 12px;
    }

    .btn-login {
      background: linear-gradient(135deg, #4d6980 0%, #4CAF50 100%);
      color: white;
      border: none;
      padding: 10px;
      border-radius: 12px;
      font-weight: 600;
      transition: 0.3s;
    }
    .btn-login:hover {
      opacity: 0.9;
      box-shadow: 0 5px 15px rgba(77,105,128,0.4);
    }

    .btn-google {
      border-radius: 12px;
      border: 1px solid #cbd5e1;
      background-color: #f8fafc;
      color: #1e293b;
      font-weight: 600;
      text-decoration: none; 
    }
    .btn-google:hover {
      background-color: #e2e8f0;
      color: #1e293b;
    }

    .register-link {
      text-align: center;
      margin-top: 15px;
      color: #334155;
    }
    .register-link a {
      color: #4CAF50;
      font-weight: 600;
      text-decoration: none;
    }
    .register-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">

  <div class="login-card">
    <div class="text-center mb-4">
      <h3>Login</h3>
    </div>

    <form action="login_process.php" method="POST" class="needs-validation" novalidate>
      <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" placeholder="user@john.petra.ac.id" required>
        <div class="invalid-feedback">Masukkan email yang valid.</div>
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
        <div class="invalid-feedback">Password wajib diisi.</div>
      </div>

      <button type="submit" class="btn btn-login w-100 mb-3">Login</button>
    </form>

    <div class="d-flex align-items-center my-3">
      <hr class="flex-grow-1">
      <span class="px-2 text-muted">atau</span>
      <hr class="flex-grow-1">
    </div>

    <a href="<?php echo $client->createAuthUrl(); ?>" class="btn btn-google w-100 d-flex align-items-center justify-content-center mb-3">
      <i class="bi bi-google me-3"></i>
        Login dengan Google Account
    </a>

    <div class="register-link">
      Belum punya akun? <a href="register.php">Daftar disini</a>
    </div>
  </div>

</body>
</html>
<?php
require_once 'config_google.php'; 
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .login-container { width: 300px; margin: auto; border: 1px solid #ccc; padding: 20px; border-radius: 8px; }
        input { width: 93%; padding: 8px; margin: 5px 0; }
        button { width: 100%; padding: 10px; cursor: pointer; }
        
        .separator { text-align: center; margin: 20px 0; color: #666; position: relative; }
        .separator::before, .separator::after { content: ""; height: 1px; background: #ccc; position: absolute; top: 50%; width: 40%; }
        .separator::before { left: 0; }
        .separator::after { right: 0; }

        .google-btn {
            background-color: #dd4b39;
            color: white;
            border: none;
            text-align: center;
            text-decoration: none;
            display: block;
            font-size: 14px;
            border-radius: 4px;
            padding: 10px;
            width: 100%; 
            box-sizing: border-box; 
        }
        .google-btn:hover { background-color: #c23321; }

                .register-link {
            text-align: center; 
            margin-top: 20px; 
            font-size: 14px;
        }
        .register-link a {
            color: #2196F3; 
            text-decoration: none;
            font-weight: bold;
        }
        .register-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2 style="text-align:center;">Login</h2>

    <form action="login_process.php" method="POST">
        <label>Email:</label><br>
        <input type="email" name="email" required placeholder="user@john.petra.ac.id"><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit" style="background-color: #4CAF50; color: white; border: none;">Login</button>
    </form>

    <div class="separator">atau</div>

    <a href="<?php echo $client->createAuthUrl(); ?>" class="google-btn">
        Login dengan Google Account
    </a>

    <div class="register-link">
        Belum punya akun? <a href="register.php">Daftar disini</a>
    </div>

</div>

</body>
</html>
<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../LOGIN/login.php");
    exit;
}


if (!isset($_GET['id'])) {
    header("Location: user_view.php");
    exit;
}

$id = mysqli_real_escape_string($conn, $_GET['id']);


if (isset($_POST['update_user'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $password_input = $_POST['password'];

    $domain = substr(strrchr($email, "@"), 1);
    
    if ($domain !== 'john.petra.ac.id') {
        echo "<script>
            alert('REGISTRASI DITOLAK!\\nHanya email @john.petra.ac.id yang boleh mendaftar.');
            window.location.href='user_edit.php?id=$id';
        </script>";
        exit();
    }
    
    if (empty($name) || empty($email)) {
        $error = "Nama dan Email tidak boleh kosong!";
    } else {
        $cek_email = mysqli_query($conn, "SELECT email FROM users WHERE email = '$email'");
        if (mysqli_num_rows($cek_email) > 1) {
            echo "<script>alert('Email ini sudah terdaftar!');</script>";
        } else {
            if (!empty($password_input)) {
                $hashed_password = password_hash($password_input, PASSWORD_DEFAULT);
                $query = "UPDATE users SET name='$name', email='$email', role='$role', password='$hashed_password' WHERE id='$id'";
            } else {
                $query = "UPDATE users SET name='$name', email='$email', role='$role' WHERE id='$id'";
            }
            if (mysqli_query($conn, $query)) {
                echo "<script>
                        alert('Data user berhasil diperbarui!');
                        window.location.href = 'user_view.php';
                        </script>";
                exit;
            } else {
                $error = "Gagal mengupdate data: " . mysqli_error($conn);
            }
        }
    }
}

$query_user = "SELECT * FROM users WHERE id = '$id'";
$result_user = mysqli_query($conn, $query_user);

if (mysqli_num_rows($result_user) == 0) {
    echo "<script>alert('User tidak ditemukan!'); window.location='user_view.php';</script>";
    exit;
}

$user = mysqli_fetch_assoc($result_user);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Edit User</h5>
                        <a href="user_view.php" class="btn btn-sm btn-outline-light">Kembali</a>
                    </div>
                    <div class="card-body">
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="<?php echo htmlspecialchars($user['name']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password Baru</label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       placeholder="Kosongkan jika tidak ingin mengubah password">
                                <div class="form-text text-muted">Hanya isi jika ingin mengganti password user ini.</div>
                            </div>

                            <!-- Role (Combo Box) -->
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="student" <?php if($user['role'] == 'student') echo 'selected'; ?>>Student</option>
                                    <option value="admin" <?php if($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                                </select>
                                <div class="form-text text-muted">Hati-hati memberikan akses Admin.</div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" name="update_user" class="btn btn-primary">Simpan Perubahan</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
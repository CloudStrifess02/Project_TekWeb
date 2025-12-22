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
$error = null;

if (isset($_POST['update_user'])) {

    $name  = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role  = mysqli_real_escape_string($conn, $_POST['role']);
    $password_input = $_POST['password'];

    // VALIDASI DOMAIN EMAIL
    $domain = substr(strrchr($email, "@"), 1);
    if ($domain !== 'john.petra.ac.id') {
        $_SESSION['notif'] = [
            'type' => 'error',
            'msg'  => 'Hanya email @john.petra.ac.id yang diperbolehkan.'
        ];
        header("Location: user_edit.php?id=$id");
        exit;
    }

    if (empty($name) || empty($email)) {
        $error = "Nama dan Email tidak boleh kosong.";
    } else {

        $cek_email = mysqli_query(
            $conn,
            "SELECT id FROM users WHERE email = '$email' AND id != '$id'"
        );

        if (mysqli_num_rows($cek_email) > 0) {
            $error = "Email sudah digunakan oleh user lain.";
        } else {

            if (!empty($password_input)) {
                $hashed_password = password_hash($password_input, PASSWORD_DEFAULT);
                $query = "
                    UPDATE users 
                    SET name='$name', email='$email', role='$role', password='$hashed_password'
                    WHERE id='$id'
                ";
            } else {
                $query = "
                    UPDATE users 
                    SET name='$name', email='$email', role='$role'
                    WHERE id='$id'
                ";
            }

            if (mysqli_query($conn, $query)) {
                $_SESSION['notif'] = [
                    'type' => 'success',
                    'msg'  => 'Data user berhasil diperbarui.'
                ];
                header("Location: user_view.php");
                exit;
            } else {
                $error = "Gagal mengupdate data.";
            }
        }
    }
}

$query_user = "SELECT * FROM users WHERE id = '$id'";
$result_user = mysqli_query($conn, $query_user);

if (mysqli_num_rows($result_user) === 0) {
    $_SESSION['notif'] = [
        'type' => 'error',
        'msg'  => 'User tidak ditemukan.'
    ];
    header("Location: user_view.php");
    exit;
}

$user = mysqli_fetch_assoc($result_user);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between">
                    <h5 class="mb-0">Edit User</h5>
                    <a href="user_view.php" class="btn btn-sm btn-outline-light">Kembali</a>
                </div>

                <div class="card-body">

                    <?php if (isset($_SESSION['notif'])): ?>
                        <div class="mb-3">
                            <span class="badge 
                                <?= $_SESSION['notif']['type'] === 'success' ? 'bg-success' : 'bg-danger' ?>">
                                <?= $_SESSION['notif']['msg']; ?>
                            </span>
                        </div>
                    <?php unset($_SESSION['notif']); endif; ?>

                    <?php if ($error): ?>
                        <div class="mb-3 text-danger fw-semibold">
                            <?= $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control"
                                   value="<?= htmlspecialchars($user['name']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control"
                                   value="<?= htmlspecialchars($user['email']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control"
                                   placeholder="Kosongkan jika tidak ingin mengubah password">
                            <small class="text-muted">Isi hanya jika ingin mengganti password.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="student" <?= $user['role']=='student'?'selected':'' ?>>Student</option>
                                <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>Admin</option>
                            </select>
                        </div>

                        <div class="d-grid">
                            <button type="submit" name="update_user" class="btn btn-primary">
                                Simpan Perubahan
                            </button>
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

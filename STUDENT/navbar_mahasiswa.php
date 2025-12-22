<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_login'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .card-event { transition: transform 0.2s; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-radius: 12px; overflow: hidden; }
        .card-event:hover { transform: translateY(-5px); box-shadow: 0 8px 16px rgba(0,0,0,0.1); }
        .poster-img { height: 180px; object-fit: cover; width: 100%; }
        .badge-posisi { font-size: 0.8rem; background-color: #e3f2fd; color: #0d6efd; border: 1px solid #0d6efd; }
        .profile-header-bg {
            background: linear-gradient(to right, #4facfe 0%, #00f2fe 100%);
            height: 120px;
            border-radius: 10px 10px 0 0;
        }
        .profile-img-wrap {
            margin-top: -60px;
            text-align: center;
        }
        .profile-img {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 4px solid white;
            background-color: #fff;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-success sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold"><i class="fas fa-graduation-cap me-2"></i>Portal Mahasiswa</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <?php if($_SESSION['role'] == "admin"): ?>
                    <li class="nav-item me-3">
                         <a href="../ADMIN/admin_dashboard.php" class="nav-link text-white active fw-semibold">
                            <i class=""></i> Admin
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item me-3">
                         <a href="index.php" class="nav-link text-white active fw-semibold">
                            <i class="bi bi-person me-1"></i> Profile
                        </a>
                    </li>
                    <li class="nav-item me-3">
                         <a href="student_dashboard.php" class="nav-link text-white active fw-semibold">
                            <i class="bi bi-people-fill me-1"></i> Panitia
                        </a>
                    </li>
                    <li class="nav-item me-3">
                         <a href="my_applications.php" class="nav-link text-white active fw-semibold">
                            <i class="fas fa-file-alt me-1"></i> Lamaran Saya
                        </a>
                    </li>
                    <li class="nav-item">
                        <span class="text-white me-3 small">Halo, <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong></span>
                    </li>
                    <li class="nav-item">
                        <a href="../LOGIN/logout.php" class="btn btn-light btn-sm text-primary fw-bold">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

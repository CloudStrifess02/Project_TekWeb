<?php
    session_start();

    // 1. Cek Session: Pastikan user sudah login dan role-nya admin
    if (!isset($_SESSION['user_login']) || $_SESSION['role'] !== 'admin') {
        header("Location: ../LOGIN/login.php");
        exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .dashboard-container { margin-top: 30px; margin-bottom: 50px; }
        .table th { background-color: #343a40; color: white; vertical-align: middle; }
        .btn-sm { font-size: 0.8rem; }
        .badge-interactive { cursor: pointer; transition: 0.2s; }
        .badge-interactive:hover { opacity: 0.8; transform: scale(1.05); }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand">
                <i class="fas fa-user-shield me-2"></i>Admin Panel
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item me-3">
                         <a href="index.php" class="nav-link text-white active fw-semibold">
                            <i class="bi bi-person me-1"></i> Users
                        </a>
                    </li>
                    <li class="nav-item me-3">
                         <a href="admin_dashboard.php" class="nav-link text-white active fw-semibold">
                            <i class="bi bi-people-fill me-1"></i> Panitia
                        </a>
                    </li>
                    <li class="nav-item me-3">
                        <span class="text-white">
                            Halo, <strong><?php echo htmlspecialchars($_SESSION['name'] ?? 'Admin'); ?></strong>
                        </span>
                    </li>
                    <li class="nav-item">
                        <a href="../LOGIN/logout.php" class="btn btn-danger btn-sm">
                            <i class="fas fa-sign-out-alt me-1"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

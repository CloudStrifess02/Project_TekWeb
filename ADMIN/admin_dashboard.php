<?php
session_start();

// 1. Cek Session: Pastikan user sudah login dan role-nya admin
if (!isset($_SESSION['user_login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../LOGIN/login.php");
    exit();
}

// 2. Hubungkan ke Database
require_once '../LOGIN/connection.php';

// 3. Ambil semua event dari tabel 'events' yang baru dibuat
$query = "SELECT * FROM events ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

// Cek jika query error
if (!$result) {
    die("Error mengambil data event: " . mysqli_error($conn));
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
    
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', sans-serif; }
        .dashboard-container { margin-top: 30px; margin-bottom: 50px; }
        .table th { background-color: #343a40; color: white; vertical-align: middle; }
        .btn-sm { font-size: 0.8rem; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-user-shield me-2"></i>Admin Panel
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
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

    <div class="container dashboard-container">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-secondary"><i class="fas fa-calendar-alt me-2"></i>Daftar Event</h2>
            <a href="add_event.php" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus me-1"></i> Tambah Event Baru
            </a>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'success') { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><strong>Berhasil!</strong> Event baru telah berhasil disimpan.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0 align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th width="30%">Nama Event</th>
                                <th width="20%">Jadwal</th>
                                <th width="15%">Status</th>
                                <th class="text-center" width="30%">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            if (mysqli_num_rows($result) > 0) { 
                                $no = 1;
                                while ($e = mysqli_fetch_assoc($result)) { 
                            ?>
                                    <tr>
                                        <td class="text-center fw-bold"><?= $no++; ?></td>
                                        <td>
                                            <span class="fw-semibold text-primary"><?= htmlspecialchars($e['event_name']) ?></span>
                                            <br>
                                            <small class="text-muted"><?= htmlspecialchars($e['event_category'] ?? '-') ?></small>
                                        </td>
                                        <td>
                                            <i class="far fa-calendar text-secondary me-1"></i>
                                            <?= date('d M Y', strtotime($e['event_date'])) ?>
                                        </td>
                                        <td>
                                            <?php 
                                            // Badge Status Sederhana
                                            $status = $e['event_status'] ?? 'draft';
                                            if($status == 'published') {
                                                echo '<span class="badge bg-success">Publish</span>';
                                            } else {
                                                echo '<span class="badge bg-secondary">Draft</span>';
                                            }
                                            ?>
                                        </td>
                                        <td class="text-center">
                                            <a href="view_positions.php?event_id=<?= $e['event_id'] ?>" class="btn btn-info btn-sm text-white me-1">
                                                <i class="fas fa-users me-1"></i> Panitia
                                            </a>
                                            
                                            <a href="add_position.php?event_id=<?= $e['event_id'] ?>" class="btn btn-success btn-sm me-1">
                                                <i class="fas fa-user-plus"></i>
                                            </a>

                                            <a href="#" class="btn btn-outline-danger btn-sm" onclick="return confirm('Yakin hapus event ini?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php } 
                            } else { ?>
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-box-open fa-3x mb-3 text-secondary"></i><br>
                                        Belum ada event yang dibuat.<br>
                                        Silakan klik tombol <strong>Tambah Event Baru</strong>.
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['user_login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../LOGIN/login.php");
    exit();
}

$event_id = $_GET['event_id'];

$q_event = mysqli_query($conn, "SELECT * FROM events WHERE event_id = '$event_id'");
$event = mysqli_fetch_assoc($q_event);

$q_pos = mysqli_query($conn, "SELECT * FROM positions WHERE event_id = '$event_id' ORDER BY position_name ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Posisi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Posisi Panitia</h2>
                <h5 class="text-primary"><?php echo htmlspecialchars($event['event_name']); ?></h5>
            </div>
            <div>
                <a href="admin_dashboard.php" class="btn btn-secondary me-2"><i class="fas fa-arrow-left"></i> Dashboard</a>
                <a href="add_position.php?event_id=<?php echo $event_id; ?>" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Posisi</a>
            </div>
        </div>

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'added') { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                Posisi berhasil ditambahkan!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">No</th>
                            <th width="30%">Nama Divisi</th>
                            <th width="15%">Kuota</th>
                            <th>Deskripsi</th>
                            <th width="15%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($q_pos) > 0) {
                            $no = 1;
                            while ($p = mysqli_fetch_assoc($q_pos)) { ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td class="fw-bold"><?php echo htmlspecialchars($p['position_name']); ?></td>
                                <td><?php echo $p['quota']; ?> Orang</td>
                                <td><?php echo nl2br(htmlspecialchars($p['description'])); ?></td>
                                <td class="text-center">
                                    
                                    <a href="view_applicants.php?pos_id=<?php echo $p['position_id']; ?>" 
                                       class="btn btn-sm btn-info text-white me-1" 
                                       title="Lihat Pelamar">
                                        <i class="fas fa-users"></i>
                                    </a>

                                    <a href="#" class="btn btn-sm btn-outline-danger" title="Hapus Posisi">
                                        <i class="fas fa-trash"></i>
                                    </a>

                                </td>
                            </tr>
                        <?php } } else { ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-clipboard-list fa-2x mb-3 text-secondary"></i><br>
                                    Belum ada posisi/divisi untuk event ini.<br>
                                    Silakan klik tombol <strong>Tambah Posisi</strong>.
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
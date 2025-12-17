<?php
session_start();
require_once '../LOGIN/connection.php';

// Cek Admin
if (!isset($_SESSION['user_login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../LOGIN/login.php");
    exit();
}

$pos_id = $_GET['pos_id'];

// 1. Ambil Info Posisi & Event
$q_info = mysqli_query($conn, "SELECT p.position_name, e.event_name, e.event_id 
                               FROM positions p 
                               JOIN events e ON p.event_id = e.event_id 
                               WHERE p.position_id = '$pos_id'");
$info = mysqli_fetch_assoc($q_info);

// 2. Ambil Daftar Pelamar (JOIN tabel registrations & users)
$query = "SELECT r.*, u.name, u.email 
          FROM registrations r 
          JOIN users u ON r.user_id = u.id 
          WHERE r.position_id = '$pos_id' 
          ORDER BY r.registered_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Daftar Pelamar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-0">Pelamar: <span class="text-primary"><?php echo htmlspecialchars($info['position_name']); ?></span></h4>
                <small class="text-muted">Event: <?php echo htmlspecialchars($info['event_name']); ?></small>
            </div>
            <a href="view_positions.php?event_id=<?php echo $info['event_id']; ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Mahasiswa</th>
                            <th>Email</th>
                            <th>Tanggal Daftar</th>
                            <th>Status</th>
                            </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0) {
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)) { 
                        ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td class="fw-bold"><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo date("d M Y H:i", strtotime($row['registered_at'])); ?></td>
                                <td>
                                    <?php 
                                    if($row['status'] == 'pending') echo '<span class="badge bg-warning text-dark">Pending</span>';
                                    elseif($row['status'] == 'accepted') echo '<span class="badge bg-success">Diterima</span>';
                                    else echo '<span class="badge bg-danger">Ditolak</span>';
                                    ?>
                                </td>
                            </tr>
                        <?php } } else { ?>
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="fas fa-user-slash fa-2x mb-3"></i><br>
                                    Belum ada mahasiswa yang mendaftar di posisi ini.
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</body>
</html>
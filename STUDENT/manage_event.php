<?php
session_start();
include '../koneksi.php';

if (!isset($_SESSION['id']) || !isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_SESSION['id'];
$event_id = $_GET['id'];

// VALIDASI: Pastikan yang akses adalah pembuat event (created_by)
$check_owner = mysqli_query($conn, "SELECT * FROM events WHERE event_id='$event_id' AND created_by='$id'");
if (mysqli_num_rows($check_owner) == 0) {
    echo "<script>alert('Anda bukan panitia inti/pembuat event ini!'); window.location='index.php';</script>";
    exit;
}
$event_data = mysqli_fetch_assoc($check_owner);

// --- LOGIKA 1: TAMBAH POSISI/DIVISI ---
if (isset($_POST['add_position'])) {
    $pos_name = mysqli_real_escape_string($conn, $_POST['position_name']);
    $quota = (int) $_POST['quota'];
    mysqli_query($conn, "INSERT INTO positions (event_id, position_name, quota) VALUES ('$event_id', '$pos_name', '$quota')");
    echo "<script>alert('Divisi berhasil ditambahkan'); window.location='manage_event.php?id=$event_id';</script>";
}

// --- LOGIKA 2: TERIMA/TOLAK ANGGOTA ---
if (isset($_GET['action']) && isset($_GET['reg_id'])) {
    $reg_id = $_GET['reg_id'];
    $action = $_GET['action'];
    
    if ($action == 'accept') {
        mysqli_query($conn, "UPDATE registrations SET status='accepted' WHERE registration_id='$reg_id'");
    } elseif ($action == 'kick' || $action == 'decline') {
        // Bisa di-delete atau set status declined. Di sini kita set declined agar history tetap ada.
        mysqli_query($conn, "UPDATE registrations SET status='declined' WHERE registration_id='$reg_id'");
    }
    echo "<script>window.location='manage_event.php?id=$event_id';</script>";
}

// --- LOGIKA 3: UPDATE EVENT ---
if (isset($_POST['update_event'])) {
    $name = mysqli_real_escape_string($conn, $_POST['event_name']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    mysqli_query($conn, "UPDATE events SET event_name='$name', event_description='$desc' WHERE event_id='$event_id'");
    echo "<script>alert('Info event diupdate'); window.location='manage_event.php?id=$event_id';</script>";
}

// AMBIL DATA PENDAFTAR
$query_applicants = "
    SELECT r.*, u.name as mhs_name, u.nrp, u.email, p.position_name 
    FROM registrations r 
    JOIN users u ON r.user_id = u.id 
    JOIN positions p ON r.position_id = p.position_id 
    WHERE p.event_id = '$event_id'
    ORDER BY r.registered_at DESC
";
$applicants = mysqli_query($conn, $query_applicants);

// AMBIL DATA POSISI
$positions = mysqli_query($conn, "SELECT * FROM positions WHERE event_id='$event_id'");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Manage Event: <?php echo htmlspecialchars($event_data['event_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    
    <!-- Navbar Sederhana -->
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container">
            <span class="navbar-brand">Kelola Event: <?php echo htmlspecialchars($event_data['event_name']); ?></span>
            <a href="index.php" class="btn btn-outline-light btn-sm">Kembali ke Dashboard</a>
        </div>
    </nav>

    <div class="container">
        
        <div class="row">
            <!-- KOLOM KIRI: EDIT EVENT & TAMBAH DIVISI -->
            <div class="col-md-4">
                <!-- Card Edit Info -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">Edit Informasi Dasar</div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-2">
                                <label>Nama Event</label>
                                <input type="text" name="event_name" class="form-control" value="<?php echo htmlspecialchars($event_data['event_name']); ?>">
                            </div>
                            <div class="mb-2">
                                <label>Deskripsi</label>
                                <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($event_data['event_description']); ?></textarea>
                            </div>
                            <button type="submit" name="update_event" class="btn btn-primary btn-sm w-100">Update Info</button>
                        </form>
                    </div>
                </div>

                <!-- Card Tambah Divisi -->
                <div class="card shadow">
                    <div class="card-header bg-success text-white">Tambah Divisi / Posisi</div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="mb-2">
                                <label>Nama Divisi</label>
                                <input type="text" name="position_name" class="form-control" placeholder="Contoh: Divisi Acara" required>
                            </div>
                            <div class="mb-2">
                                <label>Kuota</label>
                                <input type="number" name="quota" class="form-control" value="5">
                            </div>
                            <button type="submit" name="add_position" class="btn btn-success btn-sm w-100">Tambah Divisi</button>
                        </form>
                        <hr>
                        <h6>Divisi Saat Ini:</h6>
                        <ul class="list-group list-group-flush small">
                            <?php while($pos = mysqli_fetch_assoc($positions)): ?>
                                <li class="list-group-item d-flex justify-content-between">
                                    <?php echo $pos['position_name']; ?>
                                    <span class="badge bg-secondary"><?php echo $pos['quota']; ?> Orang</span>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- KOLOM KANAN: MANAGE PENDAFTAR -->
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Pendaftar & Anggota</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Nama / nrp</th>
                                        <th>Divisi Dilamar</th>
                                        <th>File</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(mysqli_num_rows($applicants) > 0): ?>
                                        <?php while($row = mysqli_fetch_assoc($applicants)): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($row['mhs_name']); ?></strong><br>
                                                <small class="text-muted"><?php echo $row['nrp']; ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['position_name']); ?></td>
                                            <td>
                                                <?php if($row['cv_file']): ?>
                                                    <a href="../uploads/<?php echo $row['cv_file']; ?>" target="_blank" class="badge bg-secondary text-decoration-none">Lihat CV</a>
                                                <?php else: ?>
                                                    -
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php 
                                                if($row['status'] == 'pending') echo '<span class="badge bg-warning text-dark">Pending</span>';
                                                elseif($row['status'] == 'accepted') echo '<span class="badge bg-success">Diterima</span>';
                                                else echo '<span class="badge bg-danger">Ditolak</span>';
                                                ?>
                                            </td>
                                            <td>
                                                <?php if($row['status'] == 'pending'): ?>
                                                    <a href="manage_event.php?id=<?php echo $event_id; ?>&action=accept&reg_id=<?php echo $row['registration_id']; ?>" class="btn btn-success btn-sm" onclick="return confirm('Terima mahasiswa ini?')">Terima</a>
                                                    <a href="manage_event.php?id=<?php echo $event_id; ?>&action=decline&reg_id=<?php echo $row['registration_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tolak mahasiswa ini?')">Tolak</a>
                                                <?php elseif($row['status'] == 'accepted'): ?>
                                                    <a href="manage_event.php?id=<?php echo $event_id; ?>&action=kick&reg_id=<?php echo $row['registration_id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Keluarkan anggota ini dari panitia?')">Keluarkan</a>
                                                <?php else: ?>
                                                    <span class="text-muted small">-</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td colspan="5" class="text-center py-4">Belum ada pendaftar.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
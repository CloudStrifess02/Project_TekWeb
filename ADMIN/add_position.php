<?php
session_start();
require_once '../LOGIN/connection.php';

// Cek Admin
if (!isset($_SESSION['user_login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../LOGIN/login.php");
    exit();
}

// Ambil Event ID dari URL
if (!isset($_GET['event_id'])) {
    echo "Error: ID Event tidak ditemukan.";
    exit();
}
$event_id = $_GET['event_id'];

// Ambil Nama Event untuk Judul
$q_event = mysqli_query($conn, "SELECT event_name FROM events WHERE event_id = '$event_id'");
$d_event = mysqli_fetch_assoc($q_event);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tambah Posisi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5" style="max-width: 600px;">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Tambah Posisi: <?php echo htmlspecialchars($d_event['event_name']); ?></h5>
            </div>
            <div class="card-body">
                
                <form action="add_position_process.php" method="POST">
                    
                    <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">

                    <div class="mb-3">
                        <label>Nama Divisi / Posisi</label>
                        <input type="text" name="position_name" class="form-control" placeholder="Contoh: Divisi Acara" required>
                    </div>
                    
                    <div class="mb-3">
                        <label>Kuota (Jumlah Orang)</label>
                        <input type="number" name="quota" class="form-control" placeholder="Contoh: 5" min="1" required>
                    </div>

                    <div class="mb-3">
                        <label>Deskripsi Tugas</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Jelaskan tanggung jawab divisi ini..."></textarea>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="view_positions.php?event_id=<?php echo $event_id; ?>" class="btn btn-secondary">Batal</a>
                        <button type="submit" name="submit" class="btn btn-success">Simpan Posisi</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</body>
</html>
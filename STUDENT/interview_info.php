<?php
session_start();
require_once '../LOGIN/connection.php';

$reg_id = $_GET['reg_id'];
$query = "SELECT r.*, e.event_name 
          FROM registrations r 
          JOIN positions p ON r.position_id = p.position_id 
          JOIN events e ON p.event_id = e.event_id 
          WHERE r.registration_id = '$reg_id'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Jadwal Interview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center text-center">
            <div class="col-md-6">
                <div class="card shadow p-5 border-0">
                    <div class="mb-4">
                        <i class="fas fa-calendar-check fa-4x text-success"></i>
                    </div>
                    <h2 class="fw-bold">Pendaftaran Berhasil!</h2>
                    <p class="text-muted">Terima kasih telah mendaftar di <strong><?php echo $data['event_name']; ?></strong>.</p>
                    <hr>
                    <div class="alert alert-warning">
                        <h6 class="mb-1 fw-bold">Jadwal Interview Anda:</h6>
                        <h4 class="mb-0 text-dark"><?php echo date('d M Y, H:i', strtotime($data['interview_time'])); ?> WIB</h4>
                    </div>
                    <div class="mb-4">
                        <p class="mb-1 fw-bold">Link Google Meet:</p>
                        <a href="<?php echo $data['meet_link']; ?>" target="_blank" class="text-primary"><?php echo $data['meet_link']; ?></a>
                    </div>
                    <a href="student_dashboard.php" class="btn btn-primary">Kembali ke Dashboard</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
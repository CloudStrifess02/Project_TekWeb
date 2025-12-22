<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['id'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}

if (!isset($_GET['reg_id']) || !is_numeric($_GET['reg_id'])) {
    header("Location: student_dashboard.php");
    exit();
}

$reg_id = $_GET['reg_id'];

$sql = "
    SELECT r.interview_time, r.meet_link, e.event_name
    FROM registrations r
    JOIN positions p ON r.position_id = p.position_id
    JOIN events e ON p.event_id = e.event_id
    WHERE r.registration_id = ?
    LIMIT 1
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $reg_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    header("Location: student_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Jadwal Interview</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container mt-5">
<div class="row justify-content-center text-center">
<div class="col-md-6">

<div class="card shadow-lg p-5 border-0 rounded-4">

<div class="mb-4">
<i class="fas fa-calendar-check fa-4x text-success"></i>
</div>

<h2 class="fw-bold mb-2">Pendaftaran Berhasil!</h2>
<p class="text-muted">
Terima kasih telah mendaftar di
<strong><?= htmlspecialchars($data['event_name']); ?></strong>
</p>

<hr class="my-4">

<div class="alert alert-warning">
<h6 class="fw-bold mb-1">Jadwal Interview Anda</h6>
<h4 class="mb-0">
<?= date('d M Y, H:i', strtotime($data['interview_time'])); ?> WIB
</h4>
</div>

<div class="mb-4">
<p class="fw-bold mb-1">Link Google Meet</p>
<a href="<?= htmlspecialchars($data['meet_link']); ?>" 
   target="_blank" 
   class="text-primary text-decoration-none">
<?= htmlspecialchars($data['meet_link']); ?>
</a>
</div>

<a href="student_dashboard.php" class="btn btn-primary w-100">
Kembali ke Dashboard
</a>

</div>

</div>
</div>
</div>

</body>
</html>

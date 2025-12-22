<?php
session_start();
require_once '../koneksi.php';


if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

$user_id  = $_SESSION['id'];

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$event_id = (int) $_GET['id'];

$stmt = mysqli_prepare($conn, "
    SELECT * FROM events 
    WHERE event_id = ? AND created_by = ?
    LIMIT 1
");
mysqli_stmt_bind_param($stmt, "ii", $event_id, $user_id);
mysqli_stmt_execute($stmt);
$event_result = mysqli_stmt_get_result($stmt);
$event_data = mysqli_fetch_assoc($event_result);

if (!$event_data) {
    exit();
}

if (isset($_POST['add_position'])) {
    $pos_name = trim($_POST['position_name']);
    $quota    = (int) $_POST['quota'];

    if ($pos_name !== '' && $quota > 0) {
        $stmt = mysqli_prepare($conn, "
            INSERT INTO positions (event_id, position_name, quota)
            VALUES (?, ?, ?)
        ");
        mysqli_stmt_bind_param($stmt, "isi", $event_id, $pos_name, $quota);
        mysqli_stmt_execute($stmt);
    }

    header("Location: manage_event.php?id=$event_id");
    exit();
}

if (isset($_POST['update_event'])) {
    $name = trim($_POST['event_name']);
    $desc = trim($_POST['description']);

    $stmt = mysqli_prepare($conn, "
        UPDATE events 
        SET event_name = ?, event_description = ?
        WHERE event_id = ?
    ");
    mysqli_stmt_bind_param($stmt, "ssi", $name, $desc, $event_id);
    mysqli_stmt_execute($stmt);

    header("Location: manage_event.php?id=$event_id");
    exit();
}

if (isset($_GET['action'], $_GET['reg_id']) && is_numeric($_GET['reg_id'])) {

    $reg_id = (int) $_GET['reg_id'];
    $action = $_GET['action'];

    if (in_array($action, ['accept', 'decline', 'kick'])) {

        $status = ($action === 'accept') ? 'accepted' : 'declined';

        $stmt = mysqli_prepare($conn, "
            UPDATE registrations 
            SET status = ?
            WHERE registration_id = ?
        ");
        mysqli_stmt_bind_param($stmt, "si", $status, $reg_id);
        mysqli_stmt_execute($stmt);
    }

    header("Location: manage_event.php?id=$event_id");
    exit();
}

$stmt = mysqli_prepare($conn, "
    SELECT r.*, u.name AS mhs_name, u.nrp, u.email, p.position_name
    FROM registrations r
    JOIN users u ON r.user_id = u.id
    JOIN positions p ON r.position_id = p.position_id
    WHERE p.event_id = ?
    ORDER BY r.registered_at DESC
");
mysqli_stmt_bind_param($stmt, "i", $event_id);
mysqli_stmt_execute($stmt);
$applicants = mysqli_stmt_get_result($stmt);


$stmt = mysqli_prepare($conn, "
    SELECT * FROM positions WHERE event_id = ?
");
mysqli_stmt_bind_param($stmt, "i", $event_id);
mysqli_stmt_execute($stmt);
$positions = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Kelola Event - <?= htmlspecialchars($event_data['event_name']); ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4">
<div class="container">
<span class="navbar-brand">Kelola Event: <?= htmlspecialchars($event_data['event_name']); ?></span>
<a href="index.php" class="btn btn-outline-light btn-sm">Dashboard</a>
</div>
</nav>

<div class="container">
<div class="row">

<!-- KIRI -->
<div class="col-md-4">

<div class="card shadow mb-4">
<div class="card-header bg-primary text-white">Edit Event</div>
<div class="card-body">
<form method="POST">
<div class="mb-2">
<label>Nama Event</label>
<input type="text" name="event_name" class="form-control" value="<?= htmlspecialchars($event_data['event_name']); ?>" required>
</div>
<div class="mb-2">
<label>Deskripsi</label>
<textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($event_data['event_description']); ?></textarea>
</div>
<button name="update_event" class="btn btn-primary btn-sm w-100">Update</button>
</form>
</div>
</div>

<div class="card shadow">
<div class="card-header bg-success text-white">Tambah Divisi</div>
<div class="card-body">
<form method="POST">
<div class="mb-2">
<input type="text" name="position_name" class="form-control" placeholder="Nama Divisi" required>
</div>
<div class="mb-2">
<input type="number" name="quota" class="form-control" value="5" min="1">
</div>
<button name="add_position" class="btn btn-success btn-sm w-100">Tambah</button>
</form>

<hr>
<ul class="list-group list-group-flush small">
<?php while($p = mysqli_fetch_assoc($positions)): ?>
<li class="list-group-item d-flex justify-content-between">
<?= htmlspecialchars($p['position_name']); ?>
<span class="badge bg-secondary"><?= $p['quota']; ?></span>
</li>
<?php endwhile; ?>
</ul>

</div>
</div>

</div>

<!-- KANAN -->
<div class="col-md-8">
<div class="card shadow">
<div class="card-header">Pendaftar</div>
<div class="card-body p-0">
<table class="table table-hover mb-0">
<thead class="table-light">
<tr>
<th>Nama</th>
<th>Divisi</th>
<th>CV</th>
<th>Status</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>

<?php if(mysqli_num_rows($applicants) > 0): ?>
<?php while($a = mysqli_fetch_assoc($applicants)): ?>
<tr>
<td>
<strong><?= htmlspecialchars($a['mhs_name']); ?></strong><br>
<small><?= htmlspecialchars($a['nrp']); ?></small>
</td>
<td><?= htmlspecialchars($a['position_name']); ?></td>
<td>
<?php if($a['cv_file']): ?>
<a href="../uploads/<?= htmlspecialchars($a['cv_file']); ?>" target="_blank">Lihat</a>
<?php else: ?>-<?php endif; ?>
</td>
<td>
<span class="badge bg-<?= $a['status']=='accepted'?'success':($a['status']=='pending'?'warning':'danger'); ?>">
<?= ucfirst($a['status']); ?>
</span>
</td>
<td>
<?php if($a['status']=='pending'): ?>
<a href="?id=<?= $event_id; ?>&action=accept&reg_id=<?= $a['registration_id']; ?>" class="btn btn-success btn-sm">Terima</a>
<a href="?id=<?= $event_id; ?>&action=decline&reg_id=<?= $a['registration_id']; ?>" class="btn btn-danger btn-sm">Tolak</a>
<?php elseif($a['status']=='accepted'): ?>
<a href="?id=<?= $event_id; ?>&action=kick&reg_id=<?= $a['registration_id']; ?>" class="btn btn-outline-danger btn-sm">Kick</a>
<?php endif; ?>
</td>
</tr>
<?php endwhile; ?>
<?php else: ?>
<tr><td colspan="5" class="text-center py-4">Belum ada pendaftar</td></tr>
<?php endif; ?>

</tbody>
</table>
</div>
</div>
</div>

</div>
</div>

</body>
</html>

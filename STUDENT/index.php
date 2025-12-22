<?php
session_start();
include '../koneksi.php';
include('navbar_mahasiswa.php');

if (!isset($_SESSION['id'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}

$user_id = $_SESSION['id'];

if (isset($_POST['update_profile'])) {

    $nama    = trim($_POST['nama']);
    $nrp     = trim($_POST['nrp']);
    $biodata = trim($_POST['biodata']);

    if (empty($nama)) {
        $_SESSION['toast'] = ['type'=>'danger','msg'=>'Nama tidak boleh kosong'];
        header("Location: index.php");
        exit();
    }

    $foto_sql = "";
    if (!empty($_FILES['foto']['name'])) {

        $allowed = ['image/jpeg','image/png'];
        if (!in_array($_FILES['foto']['type'], $allowed)) {
            $_SESSION['toast'] = ['type'=>'danger','msg'=>'Foto harus JPG atau PNG'];
            header("Location: index.php");
            exit();
        }

        if ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
            $_SESSION['toast'] = ['type'=>'danger','msg'=>'Ukuran foto maksimal 2MB'];
            header("Location: index.php");
            exit();
        }

        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto_name = 'profile_' . time() . '_' . rand(100,999) . '.' . $ext;
        move_uploaded_file($_FILES['foto']['tmp_name'], "../uploads/" . $foto_name);

        $foto_sql = ", profile_picture='$foto_name'";
    }

    $query = "
        UPDATE users 
        SET name='$nama', nrp='$nrp', biodata='$biodata' $foto_sql 
        WHERE id='$user_id'
    ";

    if (mysqli_query($conn, $query)) {
        $_SESSION['toast'] = ['type'=>'success','msg'=>'Profil berhasil diperbarui'];
    } else {
        $_SESSION['toast'] = ['type'=>'danger','msg'=>'Gagal memperbarui profil'];
    }

    header("Location: index.php");
    exit();
}

$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id='$user_id'");
$user = mysqli_fetch_assoc($user_query);

$history_query = "
    SELECT e.event_name, e.event_end_date, p.position_name
    FROM registrations r
    JOIN positions p ON r.position_id = p.position_id
    JOIN events e ON p.event_id = e.event_id
    WHERE r.user_id='$user_id' AND r.status='accepted'
    ORDER BY e.event_date DESC
";
$history_result = mysqli_query($conn, $history_query);

$my_events = mysqli_query(
    $conn,
    "SELECT * FROM events WHERE created_by='$user_id' ORDER BY event_date DESC"
);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Profil Mahasiswa</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<?php if (isset($_SESSION['toast'])): ?>
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index:1055">
    <div class="toast align-items-center text-bg-<?= $_SESSION['toast']['type']; ?> show">
        <div class="d-flex">
            <div class="toast-body">
                <?= $_SESSION['toast']['msg']; ?>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<?php unset($_SESSION['toast']); endif; ?>

<div class="container mt-4 mb-5">
<div class="row">

<div class="col-lg-4 mb-4">
<div class="card shadow-sm border-0">
<div class="card-body text-center">

<?php
$foto = !empty($user['profile_picture'])
    ? "../uploads/".$user['profile_picture']
    : "https://via.placeholder.com/150";
?>

<img src="<?= $foto ?>" class="rounded-circle mb-3" width="120" height="120">

<h5 class="fw-bold"><?= htmlspecialchars($user['name']); ?></h5>
<p class="text-muted"><?= htmlspecialchars($user['email']); ?></p>
<span class="badge bg-primary">
    <?= !empty($user['nrp']) ? htmlspecialchars($user['nrp']) : 'NRP belum diisi'; ?>
</span>

<hr>
<p class="small">
<?= !empty($user['biodata']) ? nl2br(htmlspecialchars($user['biodata'])) : 'Belum ada biodata'; ?>
</p>

<button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#editProfileModal">
Edit Profil
</button>

</div>
</div>
</div>

<div class="col-lg-8">
<div class="card shadow-sm border-0 mb-4">
<div class="card-header bg-white">
<h5 class="fw-bold text-primary mb-0">History Kepanitiaan</h5>
</div>
<div class="card-body">
<table class="table table-hover">
<thead>
<tr>
<th>#</th>
<th>Event</th>
<th>Posisi</th>
<th>Status</th>
</tr>
</thead>
<tbody>
<?php if (mysqli_num_rows($history_result) > 0): $no=1; ?>
<?php while($h = mysqli_fetch_assoc($history_result)): ?>
<tr>
<td><?= $no++; ?></td>
<td><?= htmlspecialchars($h['event_name']); ?></td>
<td><span class="badge bg-info text-dark"><?= htmlspecialchars($h['position_name']); ?></span></td>
<td>
<?php if ($h['event_end_date'] < date('Y-m-d')): ?>
<span class="badge bg-success">Selesai</span>
<?php else: ?>
<span class="badge bg-warning text-dark">Aktif</span>
<?php endif; ?>
</td>
</tr>
<?php endwhile; else: ?>
<tr>
<td colspan="4" class="text-center text-muted py-4">
Belum ada riwayat kepanitiaan
</td>
</tr>
<?php endif; ?>
</tbody>
</table>
</div>
</div>
</div>

<div class="col-12">
<div class="card shadow-sm border-0">
<div class="card-header bg-white d-flex justify-content-between">
<h5 class="fw-bold text-success mb-0">Event yang Saya Kelola</h5>
<a href="create_event.php" class="btn btn-success btn-sm">+ Buat Event</a>
</div>
<div class="card-body">
<div class="row">
<?php if (mysqli_num_rows($my_events) > 0): ?>
<?php while($evt = mysqli_fetch_assoc($my_events)): ?>
<div class="col-md-4 mb-3">
<div class="card h-100">
<div class="card-body">
<h6><?= htmlspecialchars($evt['event_name']); ?></h6>
<small class="text-muted"><?= date('d M Y', strtotime($evt['event_date'])); ?></small>
</div>
<div class="card-footer bg-white">
<a href="manage_event.php?id=<?= $evt['event_id']; ?>" class="btn btn-outline-primary w-100">
Kelola Event
</a>
</div>
</div>
</div>
<?php endwhile; else: ?>
<div class="col-12 text-center text-muted py-4">
Belum membuat event
</div>
<?php endif; ?>
</div>
</div>
</div>
</div>

</div>
</div>

<div class="modal fade" id="editProfileModal">
<div class="modal-dialog">
<div class="modal-content">
<form method="POST" enctype="multipart/form-data">

<div class="modal-header">
<h5 class="modal-title">Edit Profil</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<div class="mb-3">
<label>Nama</label>
<input type="text" name="nama" class="form-control" value="<?= htmlspecialchars($user['name']); ?>" required>
</div>
<div class="mb-3">
<label>NRP</label>
<input type="text" name="nrp" class="form-control" value="<?= htmlspecialchars($user['nrp'] ?? ''); ?>">
</div>
<div class="mb-3">
<label>Biodata</label>
<textarea name="biodata" class="form-control"><?= htmlspecialchars($user['biodata'] ?? ''); ?></textarea>
</div>
<div class="mb-3">
<label>Foto Profil</label>
<input type="file" name="foto" class="form-control" accept="image/*">
<small class="text-muted">Kosongkan jika tidak diganti</small>
</div>
</div>

<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
<button type="submit" name="update_profile" class="btn btn-primary">Simpan</button>
</div>

</form>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

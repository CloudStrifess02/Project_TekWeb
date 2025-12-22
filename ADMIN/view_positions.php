<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['user_login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../LOGIN/login.php");
    exit();
}

if (!isset($_GET['event_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$event_id = mysqli_real_escape_string($conn, $_GET['event_id']);

$q_event = mysqli_query($conn, "SELECT * FROM events WHERE event_id = '$event_id'");
$event = mysqli_fetch_assoc($q_event);

$q_pos = mysqli_query($conn, "
    SELECT * FROM positions 
    WHERE event_id = '$event_id' 
    ORDER BY position_name ASC
");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Posisi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .notif {
            padding: 10px 14px;
            border-radius: 6px;
            font-size: 14px;
            display: none;
        }
        .notif.success {
            background: #dcfce7;
            color: #166534;
        }
        .notif.error {
            background: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Posisi Panitia</h2>
            <h5 class="text-primary"><?= htmlspecialchars($event['event_name']); ?></h5>
        </div>
        <div>
            <a href="admin_dashboard.php" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left"></i> Dashboard
            </a>
            <a href="add_position.php?event_id=<?= $event_id; ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Tambah Posisi
            </a>
        </div>
    </div>

    <div id="notifBox" class="notif mb-3"></div>

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
                <?php
                if (mysqli_num_rows($q_pos) > 0) {
                    $no = 1;
                    while ($p = mysqli_fetch_assoc($q_pos)) {
                ?>
                    <tr>
                        <td><?= $no++; ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($p['position_name']); ?></td>
                        <td><?= $p['quota']; ?> Orang</td>
                        <td><?= nl2br(htmlspecialchars($p['description'])); ?></td>
                        <td class="text-center">

                            <a href="view_applicants.php?pos_id=<?= $p['position_id']; ?>"
                               class="btn btn-sm btn-info text-white me-1"
                               title="Lihat Pelamar">
                                <i class="fas fa-users"></i>
                            </a>

                            <button class="btn btn-sm btn-outline-danger btn-delete"
                                    data-id="<?= $p['position_id']; ?>">
                                <i class="fas fa-trash"></i>
                            </button>

                        </td>
                    </tr>
                <?php
                    }
                } else {
                ?>
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="fas fa-clipboard-list fa-2x mb-3 text-secondary"></i><br>
                            Belum ada posisi/divisi untuk event ini.<br>
                            Silakan klik <strong>Tambah Posisi</strong>.
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
const notifBox = document.getElementById('notifBox');

function showNotif(type, message) {
    notifBox.className = 'notif ' + type;
    notifBox.textContent = message;
    notifBox.style.display = 'block';

    setTimeout(() => {
        notifBox.style.display = 'none';
    }, 3000);
}
document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.addEventListener('click', function () {

        const positionId = this.dataset.id;

        if (!confirm('Yakin ingin menghapus posisi ini?')) return;

        fetch(`process_delete_position.php?id=${positionId}`)
            .then(res => res.text())
            .then(text => {
                if (text.trim() === 'Success') {
                    showNotif('success', 'Posisi berhasil dihapus.');
                    this.closest('tr').remove();
                } else {
                    showNotif('error', 'Gagal menghapus posisi.');
                }
            })
            .catch(() => {
                showNotif('error', 'Kesalahan koneksi server.');
            });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

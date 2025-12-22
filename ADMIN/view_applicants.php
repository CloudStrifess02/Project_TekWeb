<?php
session_start();
require_once '../koneksi.php';

if (!isset($_SESSION['user_login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../LOGIN/login.php");
    exit();
}

if (!isset($_GET['pos_id'])) {
    header("Location: admin_dashboard.php");
    exit();
}

$pos_id = mysqli_real_escape_string($conn, $_GET['pos_id']);

$q_info = mysqli_query($conn, "
    SELECT p.position_name, e.event_name, e.event_id
    FROM positions p
    JOIN events e ON p.event_id = e.event_id
    WHERE p.position_id = '$pos_id'
");
$info = mysqli_fetch_assoc($q_info);

$query = "
    SELECT r.*, u.name, u.email
    FROM registrations r
    JOIN users u ON r.user_id = u.id
    WHERE r.position_id = '$pos_id'
    ORDER BY r.registered_at DESC
";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Pelamar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .status-select {
            font-weight: 600;
            cursor: pointer;
        }
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

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-0">
                Pelamar:
                <span class="text-primary">
                    <?= htmlspecialchars($info['position_name']); ?>
                </span>
            </h4>
            <small class="text-muted">
                Event: <?= htmlspecialchars($info['event_name']); ?>
            </small>
        </div>
        <a href="view_positions.php?event_id=<?= $info['event_id']; ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <div id="notifBox" class="notif mb-3"></div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th>Nama Mahasiswa</th>
                            <th>Email</th>
                            <th>Tanggal Daftar</th>
                            <th width="20%">Status Seleksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            $no = 1;
                            while ($row = mysqli_fetch_assoc($result)) {
                                $current_status = strtolower($row['status']);
                        ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td class="fw-bold"><?= htmlspecialchars($row['name']); ?></td>
                            <td><?= htmlspecialchars($row['email']); ?></td>
                            <td><?= date("d M Y H:i", strtotime($row['registered_at'])); ?></td>
                            <td>
                                <select class="form-select form-select-sm status-dropdown status-select"
                                        data-reg-id="<?= $row['registration_id']; ?>">
                                    <option value="pending"  <?= $current_status=='pending'?'selected':'' ?>>🕒 Pending</option>
                                    <option value="accepted" <?= $current_status=='accepted'?'selected':'' ?>>✅ Accepted</option>
                                    <option value="declined" <?= $current_status=='declined'?'selected':'' ?>>❌ Declined</option>
                                </select>
                            </td>
                        </tr>
                        <?php } } else { ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-user-slash fa-3x mb-3 opacity-25"></i><br>
                                Belum ada mahasiswa yang mendaftar di posisi ini.
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
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

document.querySelectorAll('.status-dropdown').forEach(dropdown => {
    dropdown.addEventListener('change', function () {

        const regId = this.dataset.regId;
        const newStatus = this.value;
        const selectEl = this;

        selectEl.disabled = true;

        fetch(`process_selection.php?id=${regId}&action=${newStatus}`)
            .then(res => res.text())
            .then(text => {
                if (text.trim() === 'Success') {
                    showNotif('success', 'Status seleksi berhasil diperbarui.');
                } else {
                    showNotif('error', 'Gagal memperbarui status.');
                }
            })
            .catch(() => {
                showNotif('error', 'Terjadi kesalahan koneksi.');
            })
            .finally(() => {
                selectEl.disabled = false;
            });
    });
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

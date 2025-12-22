<?php
session_start();
require_once '../koneksi.php';
include('navbar_mahasiswa.php');

if (!isset($_SESSION['email'])) {
    header("Location: ../LOGIN/login.php");
    exit();
}

$email = $_SESSION['email'];


$stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($res);
$my_id = $user['id'];

$sql = "
    SELECT 
        p.*, 
        e.event_name, 
        e.event_date, 
        e.event_poster, 
        e.event_category, 
        e.event_status,
        (p.quota - (
            SELECT COUNT(*) 
            FROM registrations r 
            WHERE r.position_id = p.position_id
        )) AS sisa_slot
    FROM positions p
    JOIN events e ON p.event_id = e.event_id
    WHERE e.event_status = 'published' AND e.event_date > CURDATE()
";

$params = [];
$types  = "";

if (!empty($_GET['kategori'])) {
    $sql   .= " AND e.event_category = ?";
    $params[] = $_GET['kategori'];
    $types   .= "s";
}

if (!empty($_GET['divisi'])) {
    $sql   .= " AND p.position_name LIKE ?";
    $params[] = "%".$_GET['divisi']."%";
    $types   .= "s";
}

$sort = $_GET['sort'] ?? 'date_asc';

switch ($sort) {
    case 'date_desc':
        $sql .= " ORDER BY e.event_date DESC";
        break;
    case 'slot_desc':
        $sql .= " ORDER BY sisa_slot DESC";
        break;
    case 'slot_asc':
        $sql .= " ORDER BY sisa_slot ASC";
        break;
    default:
        $sql .= " ORDER BY e.event_date ASC";
}

$stmt = mysqli_prepare($conn, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<div class="container mt-4 mb-5">


    <?php if (isset($_GET['msg'])): ?>
        <div class="alert alert-<?= $_GET['msg']=='success'?'success':($_GET['msg']=='error'?'danger':'info'); ?> alert-dismissible fade show shadow-sm">
            <?php
                if ($_GET['msg'] == 'success') echo "Pendaftaran berhasil. Silakan tunggu proses seleksi.";
                elseif ($_GET['msg'] == 'error') echo "Terjadi kesalahan. Silakan coba lagi.";
                else echo "Informasi sistem.";
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card p-4 mb-4 shadow-sm border-0 rounded-4">
        <form method="GET">
            <div class="row g-3">

                <div class="col-md-3">
                    <label class="form-label fw-bold small">Kategori</label>
                    <select name="kategori" class="form-select">
                        <option value="">Semua</option>
                        <?php foreach (['Seminar','Workshop','Lomba','Open Recruitment'] as $k): ?>
                            <option value="<?= $k; ?>" <?= ($_GET['kategori']??'')==$k?'selected':''; ?>>
                                <?= $k; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold small">Divisi</label>
                    <input type="text" name="divisi" class="form-control"
                        value="<?= htmlspecialchars($_GET['divisi'] ?? ''); ?>"
                        placeholder="Nama divisi">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-bold small">Urutkan</label>
                    <select name="sort" class="form-select">
                        <option value="date_asc" <?= $sort=='date_asc'?'selected':''; ?>>Tanggal Terdekat</option>
                        <option value="date_desc" <?= $sort=='date_desc'?'selected':''; ?>>Tanggal Terjauh</option>
                        <option value="slot_desc" <?= $sort=='slot_desc'?'selected':''; ?>>Slot Terbanyak</option>
                        <option value="slot_asc" <?= $sort=='slot_asc'?'selected':''; ?>>Slot Tersedikit</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-primary w-100">Terapkan</button>
                </div>

            </div>
        </form>
    </div>

    <div class="row">
        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while($row = mysqli_fetch_assoc($result)): ?>

            <?php
                $pos_id = $row['position_id'];

                $stmtC = mysqli_prepare($conn, "
                    SELECT 1 FROM registrations 
                    WHERE user_id = ? AND position_id = ?
                ");
                mysqli_stmt_bind_param($stmtC, "ii", $my_id, $pos_id);
                mysqli_stmt_execute($stmtC);
                $sudah_daftar = mysqli_stmt_get_result($stmtC)->num_rows > 0;

                $img = $row['event_poster']
                    ? "../uploads/".$row['event_poster']
                    : "https://via.placeholder.com/400x200?text=Event";
            ?>

            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="<?= $img; ?>" class="card-img-top">
                    <div class="card-body d-flex flex-column">
                        <span class="badge bg-light text-dark mb-1"><?= $row['event_category']; ?></span>
                        <h5><?= htmlspecialchars($row['event_name']); ?></h5>
                        <span class="badge bg-info mb-2"><?= htmlspecialchars($row['position_name']); ?></span>

                        <small class="text-muted mb-2">
                            <?= date('d M Y', strtotime($row['event_date'])); ?>
                        </small>

                        <div class="mt-auto">
                            <?php if ($sudah_daftar): ?>
                                <button class="btn btn-success w-100" disabled>Sudah Terdaftar</button>
                            <?php elseif ($row['sisa_slot'] <= 0): ?>
                                <button class="btn btn-secondary w-100" disabled>Kuota Penuh</button>
                            <?php else: ?>
                                <a href="apply.php?event_id=<?= $row['event_id']; ?>" class="btn btn-primary w-100">
                                    Daftar
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info text-center shadow-sm">
                    Tidak ada lowongan sesuai filter.
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

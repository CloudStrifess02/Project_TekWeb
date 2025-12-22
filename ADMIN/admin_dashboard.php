<?php
require_once '../koneksi.php';
include("navbar_admin.php");

$query = "SELECT * FROM events ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error mengambil data event: " . mysqli_error($conn));
}
?>

<div class="container dashboard-container">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-secondary">
            <i class="fas fa-calendar-alt me-2"></i>Daftar Event
        </h2>
        <a href="add_event.php" class="btn btn-primary shadow-sm">
            <i class="fas fa-plus me-1"></i> Tambah Event Baru
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th width="30%">Nama Event</th>
                            <th width="20%">Jadwal</th>
                            <th width="15%">Status</th>
                            <th class="text-center" width="30%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (mysqli_num_rows($result) > 0) {
                            $no = 1;
                            while ($e = mysqli_fetch_assoc($result)) {
                        ?>
                        <tr>
                            <td class="text-center fw-bold"><?= $no++; ?></td>
                            <td>
                                <span class="fw-semibold text-primary">
                                    <?= htmlspecialchars($e['event_name']) ?>
                                </span>
                                <br>
                                <small class="text-muted">
                                    <?= htmlspecialchars($e['event_category'] ?? '-') ?>
                                </small>
                            </td>
                            <td>
                                <i class="far fa-calendar text-secondary me-1"></i>
                                <?= date('d M Y', strtotime($e['event_date'])) ?>
                            </td>
                            <td>
                                <?php
                                $status = $e['event_status'] ?? 'draft';

                                if ($status === 'published') {
                                    echo '<span class="badge bg-success">Publish</span>';
                                } else {
                                    echo '
                                        <span class="badge bg-secondary me-1">Draft</span>
                                        <a href="update_event_status.php?id='.$e['event_id'].'"
                                           class="badge bg-primary text-decoration-none">
                                           Publish
                                        </a>
                                    ';
                                }
                                ?>
                            </td>
                            <td class="text-center">
                                <a href="view_positions.php?event_id=<?= $e['event_id'] ?>"
                                   class="btn btn-info btn-sm text-white me-1">
                                    <i class="fas fa-users me-1"></i> Panitia
                                </a>
                                <a href="delete_event.php?id=<?= $e['event_id'] ?>"
                                   class="btn btn-outline-danger btn-sm"
                                   onclick="return confirm('Yakin hapus event ini? Semua data di dalamnya akan ikut terhapus!');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php
                            }
                        } else {
                        ?>
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-box-open fa-3x mb-3"></i><br>
                                Belum ada event yang dibuat.<br>
                                Klik <strong>Tambah Event Baru</strong>.
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

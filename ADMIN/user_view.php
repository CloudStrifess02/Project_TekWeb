<?php
require_once '../koneksi.php';
include("navbar_admin.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo "Unauthorized";
    exit();
}
$query = "SELECT * FROM users ORDER BY id ASC";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Error mengambil data event: " . mysqli_error($conn));
}
?>



<div class="container dashboard-container">
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0 align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center" width="5%">No</th>
                            <th width="30%">Nama</th>
                            <th width="20%">Nrp</th>
                            <th width="15%">Email</th>
                            <th class="text-center" width="30%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (mysqli_num_rows($result) > 0) {
                            while ($e = mysqli_fetch_assoc($result)) { 
                        ?>

                        <tr>
                            <td class="text-center fw-bold"><?= $e['id']; ?></td>
                            <td>
                                <span class="fw-semibold text-primary"><?= htmlspecialchars($e['name']) ?></span>
                                <br>
                                <small class="text-muted"><?= htmlspecialchars($e['role'] ?? '-') ?></small>
                            </td>
                            <td>
                                <span class="fw-semibold text-primary">
                                    <?= htmlspecialchars($e['nrp'] ?? '-') ?>
                                </span>
                            </td>
                            <td>
                                <span class="fw-semibold text-primary">
                                    <?= htmlspecialchars($e['email'] ?? '-') ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="user_edit.php?event_id=<?= $e['id'] ?>" class="btn btn-info btn-sm text-white me-1" title="Lihat Panitia">
                                    <i class="fas fa-users me-1"></i> Edit
                                </a>
                                <a href="delete_user.php?id=<?= $e['id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Yakin hapus user ini?');">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                            <?php } 
                            }?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
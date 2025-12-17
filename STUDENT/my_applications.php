<?php
require_once '../LOGIN/connection.php';
include('navbar_mahasiswa.php');

$email = $_SESSION['email'];

// Ambil User ID
$q_user = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email'");
$d_user = mysqli_fetch_assoc($q_user);
$user_id = $d_user['id'];

// Ambil Data Lamaran
$query = "SELECT r.*, 
          e.event_name, e.event_date, e.event_location,
          p1.position_name as pos_utama,
          p2.position_name as pos_cadangan
          FROM registrations r
          JOIN positions p1 ON r.position_id = p1.position_id
          LEFT JOIN positions p2 ON r.position_id_2 = p2.position_id
          JOIN events e ON p1.event_id = e.event_id
          WHERE r.user_id = '$user_id'
          ORDER BY r.registered_at DESC";

$result = mysqli_query($conn, $query);
?>

<div class="container mt-4 mb-5">
    <div class="card shadow border-0 rounded-4">
        <div class="card-body p-4">
            <h4 class="mb-4 text-primary"><i class="fas fa-clipboard-list me-2"></i>Status Pendaftaran</h4>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Event & Tanggal</th>
                            <th>Pilihan Divisi</th>
                            <th>Info Interview</th>
                            <th>Status Seleksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysqli_num_rows($result) > 0) { 
                            while($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td>
                                    <strong class="text-dark"><?php echo htmlspecialchars($row['event_name']); ?></strong><br>
                                    <small class="text-muted"><i class="far fa-calendar-alt"></i> <?php echo date('d M Y', strtotime($row['event_date'])); ?></small>
                                </td>
                                <td>
                                    <div class="mb-1"><span class="badge bg-primary">1</span> <?php echo $row['pos_utama']; ?></div>
                                    <?php if($row['pos_cadangan']) { ?>
                                        <div><span class="badge bg-secondary">2</span> <?php echo $row['pos_cadangan']; ?></div>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if($row['interview_time']) { ?>
                                        <div class="fw-bold text-dark mb-1">
                                            <?php echo date('d M Y, H:i', strtotime($row['interview_time'])); ?> WIB
                                        </div>
                                        <?php if($row['meet_link']) { ?>
                                            <a href="<?php echo $row['meet_link']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-video me-1"></i> Google Meet
                                            </a>
                                        <?php } ?>
                                    <?php } else { echo "<span class='text-muted'>-</span>"; } ?>
                                </td>
                                <td>
                                    <?php 
                                    if($row['status'] == 'pending') {
                                        echo '<span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Menunggu</span>';
                                    } elseif($row['status'] == 'accepted') {
                                        echo '<span class="badge bg-success"><i class="fas fa-check-circle"></i> Diterima</span>';
                                    } else {
                                        echo '<span class="badge bg-danger"><i class="fas fa-times-circle"></i> Ditolak</span>';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php } } else { ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 opacity-25"></i><br>
                                    Belum ada lamaran yang dikirim. Yuk daftar event!
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
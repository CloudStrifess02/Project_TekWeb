<?php
session_start();
include '../koneksi.php'; // Pastikan path ke koneksi benar
include('navbar_mahasiswa.php');

$user_id = $_SESSION['id'];
// 2. Proses Update Profile
if (isset($_POST['update_profile'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $nrp = mysqli_real_escape_string($conn, $_POST['nrp']);
    $biodata = mysqli_real_escape_string($conn, $_POST['biodata']);
    
    // Logic Upload Foto
    $foto_query = "";
    if (!empty($_FILES['foto']['name'])) {
        $foto_name = time() . '_' . $_FILES['foto']['name'];
        $target = "../uploads/" . $foto_name; // Pastikan folder 'uploads' ada di luar folder student
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target)) {
            $foto_query = ", profile_picture='$foto_name'";
        }
    }

    $query = "UPDATE users SET name='$nama', nrp='$nrp', biodata='$biodata' $foto_query WHERE id='$user_id'";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Profil berhasil diupdate!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Gagal update: " . mysqli_error($conn) . "');</script>";
    }
}

// 3. Ambil Data User
$user_query = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
$user = mysqli_fetch_assoc($user_query);

// 4. Ambil History Panitia (JOIN tables: registrations -> positions -> events)
// Hanya mengambil yang statusnya 'accepted'
$history_query = "
    SELECT 
        e.event_name, 
        e.event_date,
        e.event_end_date,
        p.position_name,
        r.status
    FROM registrations r
    JOIN positions p ON r.position_id = p.position_id
    JOIN events e ON p.event_id = e.event_id
    WHERE r.user_id = '$user_id' AND r.status = 'accepted'
    ORDER BY e.event_date DESC
";
$history_result = mysqli_query($conn, $history_query);
?>
<div class="container mt-4 mb-5">
    <div class="row">
        <!-- KOLOM KIRI: PROFIL USER -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm border-0">
                <div class="profile-header-bg"></div>
                <div class="card-body pt-0">
                    <div class="profile-img-wrap">
                        <?php 
                            $foto = !empty($user['profile_picture']) ? "../uploads/".$user['profile_picture'] : "https://via.placeholder.com/150";
                        ?>
                        <img src="<?php echo $foto; ?>" alt="Foto Profil" class="profile-img">
                    </div>
                    <div class="text-center mt-3">
                        <h5 class="fw-bold"><?php echo htmlspecialchars($user['name']); ?></h5>
                        <p class="text-muted mb-1"><?php echo htmlspecialchars($user['email']); ?></p>
                        <span class="badge bg-primary"><?php echo !empty($user['nrp']) ? htmlspecialchars($user['nrp']) : 'nrp Belum Diisi'; ?></span>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted">BIODATA</label>
                        <p class="small"><?php echo !empty($user['biodata']) ? nl2br(htmlspecialchars($user['biodata'])) : 'Belum ada biodata.'; ?></p>
                    </div>
                    <button class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        Edit Profil
                    </button>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: HISTORY PANITIA -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-primary">History Kepanitiaan</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nama Event</th>
                                    <th>Jabatan (Position)</th>
                                    <th>Tanggal Selesai</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(mysqli_num_rows($history_result) > 0): ?>
                                    <?php $no=1; while($row = mysqli_fetch_assoc($history_result)): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td class="fw-bold"><?php echo htmlspecialchars($row['event_name']); ?></td>
                                        <td>
                                            <span class="badge bg-info text-dark">
                                                <?php echo htmlspecialchars($row['position_name']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php 
                                                $tgl = strtotime($row['event_end_date']); 
                                                echo $tgl ? date('d F Y', $tgl) : '-';
                                            ?>
                                        </td>
                                        <td>
                                            <?php if($row['event_end_date'] < date('Y-m-d')): ?>
                                                <span class="badge bg-success">Selesai</span>
                                            <?php else: ?>
                                                <span class="badge bg-warning text-dark">Aktif</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            Anda belum memiliki riwayat kepanitiaan (Status: Accepted).
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Profil -->
<div class="modal fade" id="editProfileModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Profil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>nrp</label>
                        <input type="text" name="nrp" class="form-control" value="<?php echo isset($user['nrp']) ? htmlspecialchars($user['nrp']) : ''; ?>" placeholder="Contoh: C14240077">
                    </div>
                    <div class="mb-3">
                        <label>Biodata</label>
                        <textarea name="biodata" class="form-control" rows="3"><?php echo isset($user['biodata']) ? htmlspecialchars($user['biodata']) : ''; ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Ganti Foto Profil</label>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                        <small class="text-muted">Biarkan kosong jika tidak ingin mengganti foto.</small>
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
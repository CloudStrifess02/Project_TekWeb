<?php
session_start();
require_once '../LOGIN/connection.php';

// 1. Cek Login Student
if (!isset($_SESSION['user_login']) || $_SESSION['role'] != 'student') {
    header("Location: ../LOGIN/login.php");
    exit();
}

$user_id = $_SESSION['user_id'] ?? 0; 
// Catatan: Pastikan saat login Anda menyimpan $_SESSION['user_id'] = $row['id'];
// Jika belum, kita ambil ID user manual lewat email:
$email_sess = $_SESSION['email'];
$q_u = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email_sess'");
$d_u = mysqli_fetch_assoc($q_u);
$my_id = $d_u['id'];


// --- LOGIKA FILTER & SORTING ---
// Filter Kategori Event
$sql = "SELECT p.*, e.event_name, e.event_date, e.event_poster, e.event_category,
        (SELECT COUNT(*) FROM registrations r WHERE r.position_id = p.position_id) as terisi
        FROM positions p 
        JOIN events e ON p.event_id = e.event_id 
        WHERE e.event_status = 'published'"; // Hanya tampilkan event yang sudah dipublish

// Jika ada filter kategori
if (isset($_GET['kategori']) && $_GET['kategori'] != '') {
    $kat = mysqli_real_escape_string($conn, $_GET['kategori']);
    $sql .= " AND e.event_category = '$kat'";
}

// Default Sorting: Tanggal Event Terdekat
$sql .= " ORDER BY e.event_date ASC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .card-event { transition: transform 0.2s; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        .card-event:hover { transform: translateY(-5px); box-shadow: 0 8px 16px rgba(0,0,0,0.1); }
        .poster-img { height: 180px; object-fit: cover; width: 100%; border-radius: 6px 6px 0 0; }
        .badge-posisi { font-size: 0.8rem; background-color: #e3f2fd; color: #0d6efd; border: 1px solid #0d6efd; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-graduation-cap me-2"></i>Portal Mahasiswa</a>
            <div class="d-flex text-white align-items-center">
                <span class="me-3">Halo, <?php echo $_SESSION['name']; ?></span>
                <a href="../LOGIN/logout.php" class="btn btn-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mb-5">
        
        <div class="card p-3 mb-4 border-0 shadow-sm">
            <form method="GET" class="row g-3 align-items-center">
                <div class="col-auto">
                    <label class="fw-bold text-secondary">Filter Kategori:</label>
                </div>
                <div class="col-auto">
                    <select name="kategori" class="form-select form-select-sm">
                        <option value="">-- Semua Kategori --</option>
                        <option value="Seminar" <?php if(isset($_GET['kategori']) && $_GET['kategori']=='Seminar') echo 'selected';?>>Seminar</option>
                        <option value="Lomba" <?php if(isset($_GET['kategori']) && $_GET['kategori']=='Lomba') echo 'selected';?>>Lomba</option>
                        <option value="Open Recruitment" <?php if(isset($_GET['kategori']) && $_GET['kategori']=='Open Recruitment') echo 'selected';?>>Open Recruitment</option>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm">Terapkan</button>
                    <a href="student_dashboard.php" class="btn btn-light btn-sm">Reset</a>
                </div>
            </form>
        </div>

        <h4 class="mb-3 text-secondary">Lowongan Tersedia</h4>
        <div class="row">
            <?php 
            if (mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    // Hitung Sisa Kuota
                    $kuota = $row['quota'];
                    $terisi = $row['terisi'];
                    $sisa = $kuota - $terisi;
                    
                    // Cek apakah user ini SUDAH daftar di posisi ini?
                    $pos_id = $row['position_id'];
                    $cek_saya = mysqli_query($conn, "SELECT * FROM registrations WHERE user_id='$my_id' AND position_id='$pos_id'");
                    $sudah_daftar = (mysqli_num_rows($cek_saya) > 0);

                    // Tentukan Gambar Poster (Jika kosong pakai gambar default)
                    $img_src = !empty($row['event_poster']) ? "../uploads/".$row['event_poster'] : "https://via.placeholder.com/400x200?text=No+Image";
            ?>
            <div class="col-md-4 mb-4">
                <div class="card card-event h-100">
                    <img src="<?php echo $img_src; ?>" class="poster-img" alt="Poster">
                    
                    <div class="card-body d-flex flex-column">
                        <div class="mb-2">
                            <span class="badge bg-secondary"><?php echo $row['event_category']; ?></span>
                            <span class="text-muted small float-end"><i class="far fa-calendar-alt"></i> <?php echo date("d M Y", strtotime($row['event_date'])); ?></span>
                        </div>
                        
                        <h5 class="card-title text-dark fw-bold mb-1"><?php echo htmlspecialchars($row['event_name']); ?></h5>
                        <div class="mb-2">
                            <span class="badge rounded-pill badge-posisi"><?php echo htmlspecialchars($row['position_name']); ?></span>
                        </div>
                        
                        <p class="card-text text-muted small flex-grow-1">
                            <?php echo substr($row['description'], 0, 80) . "..."; ?>
                        </p>
                        
                        <div class="mt-3">
                            <div class="d-flex justify-content-between small fw-bold mb-1">
                                <span>Status Kuota</span>
                                <span class="<?php echo ($sisa > 0) ? 'text-success':'text-danger'; ?>">
                                    <?php echo ($sisa > 0) ? "Sisa $sisa Slot" : "PENUH"; ?>
                                </span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <?php 
                                    $persen = ($kuota > 0) ? ($terisi / $kuota) * 100 : 100; 
                                ?>
                                <div class="progress-bar bg-success" role="progressbar" style="width: <?php echo $persen; ?>%"></div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <?php if ($sudah_daftar): ?>
                                <button class="btn btn-info w-100 text-white" disabled><i class="fas fa-check-circle"></i> Sudah Mendaftar</button>
                            <?php elseif ($sisa <= 0): ?>
                                <button class="btn btn-secondary w-100" disabled>Pendaftaran Tutup</button>
                            <?php else: ?>
                                <a href="register_process.php?pos_id=<?php echo $row['position_id']; ?>" 
                                   class="btn btn-primary w-100" 
                                   onclick="return confirm('Apakah Anda yakin ingin mendaftar sebagai <?php echo $row['position_name']; ?>?');">
                                   Daftar Sekarang
                                </a>
                            <?php endif; ?>
                        </div>

                    </div>
                </div>
            </div>
            <?php 
                } 
            } else {
                echo "<div class='col-12 text-center py-5 text-muted'><h5>Belum ada lowongan posisi yang dibuka.</h5></div>";
            }
            ?>
        </div>
    </div>

</body>
</html>
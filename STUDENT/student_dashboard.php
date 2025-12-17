<?php
session_start();
require_once '../LOGIN/connection.php';

// 1. Cek Login Student
if (!isset($_SESSION['user_login']) || $_SESSION['role'] != 'student') {
    header("Location: ../LOGIN/login.php");
    exit();
}

// Ambil ID User
$email_sess = $_SESSION['email'];
$q_u = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email_sess'");
$d_u = mysqli_fetch_assoc($q_u);
$my_id = $d_u['id'];

// --- LOGIKA FILTER & SORTING ---
// Base Query: Mengambil data posisi, event, dan menghitung sisa slot
$sql = "SELECT p.*, e.event_name, e.event_date, e.event_poster, e.event_category, e.event_status,
        (p.quota - (SELECT COUNT(*) FROM registrations r WHERE r.position_id = p.position_id)) as sisa_slot
        FROM positions p 
        JOIN events e ON p.event_id = e.event_id 
        WHERE e.event_status = 'published'";

// 1. Filter Kategori Event
if (isset($_GET['kategori']) && $_GET['kategori'] != '') {
    $kat = mysqli_real_escape_string($conn, $_GET['kategori']);
    $sql .= " AND e.event_category = '$kat'";
}

// 2. Filter Nama Divisi (Menggunakan LIKE agar fleksibel)
if (isset($_GET['divisi']) && $_GET['divisi'] != '') {
    $div = mysqli_real_escape_string($conn, $_GET['divisi']);
    $sql .= " AND p.position_name LIKE '%$div%'";
}

// 3. Logic Sorting (Urutan)
$sort_option = isset($_GET['sort']) ? $_GET['sort'] : 'date_asc';

switch ($sort_option) {
    case 'date_asc':
        $sql .= " ORDER BY e.event_date ASC"; // Tanggal Terdekat
        break;
    case 'date_desc':
        $sql .= " ORDER BY e.event_date DESC"; // Tanggal Terjauh
        break;
    case 'slot_desc':
        $sql .= " ORDER BY sisa_slot DESC"; // Slot Terbanyak
        break;
    case 'slot_asc':
        $sql .= " ORDER BY sisa_slot ASC"; // Slot Tersedikit (Mau habis)
        break;
    default:
        $sql .= " ORDER BY e.event_date ASC";
}

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        .card-event { transition: transform 0.2s; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.05); border-radius: 12px; overflow: hidden; }
        .card-event:hover { transform: translateY(-5px); box-shadow: 0 8px 16px rgba(0,0,0,0.1); }
        .poster-img { height: 180px; object-fit: cover; width: 100%; }
        .badge-posisi { font-size: 0.8rem; background-color: #e3f2fd; color: #0d6efd; border: 1px solid #0d6efd; }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#"><i class="fas fa-graduation-cap me-2"></i>Portal Mahasiswa</a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item me-3">
                         <a href="my_applications.php" class="nav-link text-white active fw-semibold">
                            <i class="fas fa-file-alt me-1"></i> Lamaran Saya
                        </a>
                    </li>
                    <li class="nav-item">
                        <span class="text-white me-3 small">Halo, <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong></span>
                    </li>
                    <li class="nav-item">
                        <a href="../LOGIN/logout.php" class="btn btn-light btn-sm text-primary fw-bold">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4 mb-5">
        
        <div class="card p-4 mb-4 border-0 shadow-sm rounded-4 bg-white">
            <form method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-secondary">Kategori Event</label>
                        <select name="kategori" class="form-select">
                            <option value="">Semua Kategori</option>
                            <option value="Seminar" <?= (isset($_GET['kategori']) && $_GET['kategori']=='Seminar') ? 'selected':''; ?>>Seminar</option>
                            <option value="Workshop" <?= (isset($_GET['kategori']) && $_GET['kategori']=='Workshop') ? 'selected':''; ?>>Workshop</option>
                            <option value="Lomba" <?= (isset($_GET['kategori']) && $_GET['kategori']=='Lomba') ? 'selected':''; ?>>Lomba</option>
                            <option value="Open Recruitment" <?= (isset($_GET['kategori']) && $_GET['kategori']=='Open Recruitment') ? 'selected':''; ?>>Open Recruitment</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-secondary">Filter Divisi</label>
                        <select name="divisi" class="form-select">
                            <option value="">Semua Divisi</option>
                            <option value="Acara" <?= (isset($_GET['divisi']) && $_GET['divisi']=='Acara') ? 'selected':''; ?>>Divisi Acara</option>
                            <option value="Keamanan" <?= (isset($_GET['divisi']) && $_GET['divisi']=='Keamanan') ? 'selected':''; ?>>Divisi Keamanan</option>
                            <option value="Perlengkapan" <?= (isset($_GET['divisi']) && $_GET['divisi']=='Perlengkapan') ? 'selected':''; ?>>Divisi Perlengkapan</option>
                            <option value="Sponsor" <?= (isset($_GET['divisi']) && $_GET['divisi']=='Sponsor') ? 'selected':''; ?>>Divisi Sponsor</option>
                            <option value="Creative" <?= (isset($_GET['divisi']) && $_GET['divisi']=='Creative') ? 'selected':''; ?>>Divisi Creative</option>
                            <option value="Humas" <?= (isset($_GET['divisi']) && $_GET['divisi']=='Humas') ? 'selected':''; ?>>Divisi Humas</option>
                            <option value="Publikasi" <?= (isset($_GET['divisi']) && $_GET['divisi']=='Publikasi') ? 'selected':''; ?>>Divisi Publikasi</option>
                            <option value="IT" <?= (isset($_GET['divisi']) && $_GET['divisi']=='IT') ? 'selected':''; ?>>Divisi IT</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-bold small text-secondary">Urutkan</label>
                        <select name="sort" class="form-select">
                            <option value="date_asc" <?= ($sort_option=='date_asc')?'selected':''; ?>>📅 Tanggal Terdekat</option>
                            <option value="date_desc" <?= ($sort_option=='date_desc')?'selected':''; ?>>📅 Tanggal Terjauh</option>
                            <option value="slot_desc" <?= ($sort_option=='slot_desc')?'selected':''; ?>>🔢 Sisa Slot Terbanyak</option>
                            <option value="slot_asc" <?= ($sort_option=='slot_asc')?'selected':''; ?>>🔢 Sisa Slot Sedikit</option>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 me-2"><i class="fas fa-filter"></i> Terapkan</button>
                        <a href="student_dashboard.php" class="btn btn-outline-secondary"><i class="fas fa-sync"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>

        <h5 class="mb-3 text-secondary border-start border-4 border-primary ps-3">Lowongan Panitia Tersedia</h5>
        <div class="row">
            <?php 
            if ($result && mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    $kuota = $row['quota'];
                    $sisa = $row['sisa_slot']; // Hasil hitungan dari Query SQL
                    
                    $pos_id = $row['position_id'];
                    $cek_saya = mysqli_query($conn, "SELECT * FROM registrations WHERE user_id='$my_id' AND position_id='$pos_id'");
                    $sudah_daftar = (mysqli_num_rows($cek_saya) > 0);

                    // Gambar
                    $img_src = !empty($row['event_poster']) ? "../uploads/".$row['event_poster'] : "https://via.placeholder.com/400x200?text=Event+Poster";
            ?>
            <div class="col-md-4 mb-4">
                <div class="card card-event h-100">
                    <img src="<?php echo $img_src; ?>" class="poster-img" alt="Poster">
                    
                    <div class="card-body d-flex flex-column">
                        <div class="mb-2 d-flex justify-content-between">
                            <span class="badge bg-light text-dark border"><?php echo $row['event_category']; ?></span>
                            <span class="text-muted small fw-bold"><i class="far fa-calendar-alt"></i> <?php echo date("d M Y", strtotime($row['event_date'])); ?></span>
                        </div>
                        
                        <h5 class="card-title text-dark fw-bold mb-1"><?php echo htmlspecialchars($row['event_name']); ?></h5>
                        <div class="mb-2">
                            <span class="badge rounded-pill badge-posisi"><?php echo htmlspecialchars($row['position_name']); ?></span>
                        </div>
                        
                        <p class="card-text text-muted small flex-grow-1">
                            <?php echo nl2br(htmlspecialchars(substr($row['description'], 0, 90))); ?>...
                        </p>
                        
                        <div class="mt-3 bg-light p-2 rounded">
                            <div class="d-flex justify-content-between small fw-bold mb-1">
                                <span>Ketersediaan Slot</span>
                                <span class="<?php echo ($sisa > 0) ? 'text-primary':'text-danger'; ?>">
                                    <?php echo ($sisa > 0) ? "$sisa Slot Tersisa" : "Full"; ?>
                                </span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <?php 
                                    $terisi = $kuota - $sisa;
                                    $persen = ($kuota > 0) ? ($terisi / $kuota) * 100 : 100; 
                                ?>
                                <div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $persen; ?>%"></div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <?php if ($sudah_daftar): ?>
                                <button class="btn btn-success w-100 disabled"><i class="fas fa-check-circle"></i> Anda Sudah Terdaftar</button>
                            <?php elseif ($sisa <= 0): ?>
                                <button class="btn btn-secondary w-100 disabled">Kuota Penuh</button>
                            <?php else: ?>
                                <a href="apply.php?event_id=<?php echo $row['event_id']; ?>" 
                                   class="btn btn-primary w-100 fw-bold shadow-sm">
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
                echo "<div class='col-12 text-center py-5'>
                        <div class='alert alert-info border-0 shadow-sm'>
                            <i class='fas fa-search fa-2x mb-2'></i><br>
                            Tidak ada lowongan yang cocok dengan filter Anda.<br>
                            <a href='student_dashboard.php' class='fw-bold'>Reset Filter</a>
                        </div>
                      </div>";
            }
            ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
session_start();
require_once '../LOGIN/connection.php';

$event_id = $_GET['event_id'];
$q_event = mysqli_query($conn, "SELECT event_name FROM events WHERE event_id = '$event_id'");
$event = mysqli_fetch_assoc($q_event);

$q_pos = mysqli_query($conn, "SELECT * FROM positions WHERE event_id = '$event_id'");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Form Pendaftaran - <?php echo $event['event_name']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light pb-5">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow border-0">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="mb-0">Pendaftaran Panitia: <?php echo $event['event_name']; ?></h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="apply_process.php" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="event_id" value="<?php echo $event_id; ?>">
                            
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Pilihan 1 (Utama)</label>
                                    <select name="pos1" class="form-select" required>
                                        <option value="">-- Pilih Divisi --</option>
                                        <?php mysqli_data_seek($q_pos, 0); while($p = mysqli_fetch_assoc($q_pos)) { ?>
                                            <option value="<?php echo $p['position_id']; ?>"><?php echo $p['position_name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Pilihan 2 (Cadangan)</label>
                                    <select name="pos2" class="form-select" required>
                                        <option value="">-- Pilih Divisi --</option>
                                        <?php mysqli_data_seek($q_pos, 0); while($p = mysqli_fetch_assoc($q_pos)) { ?>
                                            <option value="<?php echo $p['position_id']; ?>"><?php echo $p['position_name']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Motivasi Bergabung</label>
                                <textarea name="motivation" class="form-control" rows="4" placeholder="Apa alasan Anda ingin bergabung?" required></textarea>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold">Pengalaman Kepanitiaan/Organisasi</label>
                                <textarea name="experience" class="form-control" rows="4" placeholder="Sebutkan pengalaman yang relevan..." required></textarea>
                            </div>

                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Upload CV (PDF)</label>
                                    <input type="file" name="cv" class="form-control" accept=".pdf" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">Portfolio (PDF, Opsional)</label>
                                    <input type="file" name="portfolio" class="form-control" accept=".pdf">
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">Kirim Pendaftaran</button>
                                <a href="student_dashboard.php" class="btn btn-light">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
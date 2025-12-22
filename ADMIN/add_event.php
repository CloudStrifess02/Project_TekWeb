<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../LOGIN/login.php");
    exit();
}

require_once '../koneksi.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tambah Event</title>

  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f4f6f8;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 800px;
      margin: 40px auto;
      background: #ffffff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }
    h1 {
      margin-bottom: 10px;
      color: #333;
      font-size: 24px;
    }
    .required-note {
      font-size: 13px;
      color: #dc2626;
      margin-bottom: 25px;
    }
    .form-group {
      margin-bottom: 20px;
    }
    label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #444;
      font-size: 14px;
    }
    .req {
      color: #dc2626;
      margin-left: 3px;
    }
    input[type="text"],
    input[type="date"],
    input[type="file"],
    textarea,
    select {
      width: 100%;
      padding: 12px;
      border-radius: 8px;
      border: 1px solid #ddd;
      font-size: 14px;
      box-sizing: border-box;
    }
    textarea {
      resize: vertical;
      min-height: 120px;
    }
    .row {
      display: flex;
      gap: 20px;
    }
    .row .form-group {
      flex: 1;
    }
    .btn-group {
      margin-top: 30px;
      display: flex;
      justify-content: flex-end;
      gap: 15px;
    }
    button, .btn-cancel {
      padding: 12px 24px;
      border: none;
      border-radius: 8px;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      text-decoration: none;
    }
    .btn-cancel {
      background: #f1f5f9;
      color: #64748b;
    }
    .btn-submit {
      background: #2563eb;
      color: #fff;
    }

    .notif {
      margin-bottom: 20px;
      padding: 12px 16px;
      border-radius: 8px;
      font-size: 14px;
    }
    .notif.error {
      background: #fee2e2;
      color: #991b1b;
    }
    .notif.success {
      background: #dcfce7;
      color: #166534;
    }

    .inline-error {
      font-size: 12px;
      color: #dc2626;
      margin-top: 6px;
      display: none;
    }
  </style>
</head>

<body>
<div class="container">
  <h1>Tambah Event Baru</h1>
  <div class="required-note">* Wajib diisi</div>

  <?php if (isset($_SESSION['notif'])): ?>
    <div class="notif <?= $_SESSION['notif']['type']; ?>">
      <?= $_SESSION['notif']['msg']; ?>
    </div>
  <?php unset($_SESSION['notif']); endif; ?>

  <form action="add_event_process.php" method="POST" enctype="multipart/form-data">

    <div class="form-group">
      <label>Nama Event <span class="req">*</span></label>
      <input type="text" name="event_name" required />
    </div>

    <div class="form-group">
      <label>Deskripsi Event</label>
      <textarea name="event_description"></textarea>
    </div>

    <div class="row">
      <div class="form-group">
        <label>Tanggal Mulai <span class="req">*</span></label>
        <input type="date" name="event_date" required />
      </div>

      <div class="form-group">
        <label>Tanggal Selesai</label>
        <input type="date" name="event_end_date" />
        <div class="inline-error" id="dateError">
          Tanggal selesai tidak boleh lebih awal dari tanggal mulai.
        </div>
      </div>
    </div>

    <div class="row">
      <div class="form-group">
        <label>Lokasi</label>
        <input type="text" name="event_location" />
      </div>

      <div class="form-group">
        <label>Kategori <span class="req">*</span></label>
        <select name="event_category" required>
          <option value="">-- Pilih Kategori --</option>
          <option value="Seminar">Seminar</option>
          <option value="Workshop">Workshop</option>
          <option value="Open Recruitment">Open Recruitment</option>
          <option value="Lomba">Lomba</option>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label>Poster Event</label>
      <input type="file" name="event_poster" accept="image/*" />
    </div>

    <div class="form-group">
      <label>Status Publish <span class="req">*</span></label>
      <select name="event_status" required>
        <option value="draft">Draft</option>
        <option value="published">Publish</option>
      </select>
    </div>

    <div class="btn-group">
      <a href="admin_dashboard.php" class="btn-cancel">Batal</a>
      <button type="submit" class="btn-submit">Simpan Event</button>
    </div>
  </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
  const start = document.querySelector('[name="event_date"]');
  const end = document.querySelector('[name="event_end_date"]');
  const errorText = document.getElementById("dateError");

  function validateDate() {
    if (start.value && end.value && end.value < start.value) {
      errorText.style.display = "block";
      end.value = "";
    } else {
      errorText.style.display = "none";
    }
  }

  start.addEventListener("change", () => {
    end.min = start.value;
    validateDate();
  });

  end.addEventListener("change", validateDate);
});
</script>

</body>
</html>

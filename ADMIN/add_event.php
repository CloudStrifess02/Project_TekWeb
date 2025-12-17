<?php
session_start();

// 1. Cek Session: Pastikan user adalah admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../LOGIN/login.php");
    exit();
}

// 2. Hubungkan ke Database
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
      margin-bottom: 25px;
      color: #333;
      font-size: 24px;
      border-bottom: 2px solid #f0f0f0;
      padding-bottom: 15px;
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
      transition: border-color 0.3s;
    }
    input:focus, textarea:focus, select:focus {
      border-color: #2563eb;
      outline: none;
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
      display: inline-block;
      text-align: center;
    }
    .btn-cancel {
      background: #f1f5f9;
      color: #64748b;
    }
    .btn-cancel:hover {
      background: #e2e8f0;
    }
    .btn-submit {
      background: #2563eb;
      color: #fff;
    }
    .btn-submit:hover {
      background: #1d4ed8;
    }
    .note {
      font-size: 12px;
      color: #94a3b8;
      margin-top: 6px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Tambah Event Baru</h1>

    <form action="add_event_process.php" method="POST" enctype="multipart/form-data">
      
      <div class="form-group">
        <label>Nama Event <span style="color:red">*</span></label>
        <input type="text" name="event_name" placeholder="Contoh: Open Recruitment Panitia PKKMB" required />
      </div>

      <div class="form-group">
        <label>Deskripsi Event</label>
        <textarea name="event_description" placeholder="Jelaskan detail event, tujuan, dan benefit..."></textarea>
      </div>

      <div class="row">
        <div class="form-group">
          <label>Tanggal Mulai <span style="color:red">*</span></label>
          <input type="date" name="event_date" required />
        </div>

        <div class="form-group">
          <label>Tanggal Selesai</label>
          <input type="date" name="event_end_date" />
        </div>
      </div>

      <div class="row">
        <div class="form-group">
          <label>Lokasi</label>
          <input type="text" name="event_location" placeholder="Online / Gedung Q" />
        </div>
        <div class="form-group">
          <label>Kategori</label>
          <select name="event_category">
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
        <div class="note">Format JPG/PNG, maksimal 2MB</div>
      </div>

      <div class="form-group">
        <label>Status Publish</label>
        <select name="event_status">
          <option value="draft">Draft (Menunggu Persetujuan)</option>
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
    document.addEventListener("DOMContentLoaded", function() {
        const startDateInput = document.querySelector('input[name="event_date"]');
        const endDateInput = document.querySelector('input[name="event_end_date"]');
        const form = document.querySelector('form');

        // 1. Saat Tanggal Mulai dipilih, set batas minimal Tanggal Selesai
        startDateInput.addEventListener('change', function() {
            endDateInput.min = this.value; // Tanggal selesai gak boleh sebelum tanggal mulai
            
            // Jika user sebelumnya sudah pilih tanggal selesai yg salah, reset aja
            if (endDateInput.value && endDateInput.value < this.value) {
                endDateInput.value = ""; 
                alert("Tanggal selesai di-reset karena lebih awal dari tanggal mulai.");
            }
        });

        // 2. Cek Terakhir saat tombol Simpan ditekan
        form.addEventListener('submit', function(e) {
            const start = startDateInput.value;
            const end = endDateInput.value;

            if (start && end) {
                if (end < start) {
                    e.preventDefault(); // Batalkan pengiriman
                    alert('⛔ Eits! Tanggal Selesai tidak boleh lebih awal dari Tanggal Mulai.');
                    endDateInput.focus();
                }
            }
        });
    });
  </script>

</body>
</html>
<?php
session_start();
require 'functions.php';

// Cek Login & Role
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

// Ambil ID dari URL dan pastikan berupa angka untuk keamanan
$id = (int)$_GET["id"];

// Ambil data aspirasi spesifik
$result = query("SELECT aspirasi.*, siswa.nama, kategori.nama_kategori 
                 FROM aspirasi 
                 JOIN siswa ON aspirasi.nis = siswa.nis 
                 JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori 
                 WHERE id_aspirasi = $id");

if (!$result) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='admin.php';</script>";
    exit;
}
$laporan = $result[0];

// Proses Update Tanggapan
if (isset($_POST["update"])) {
    $status_baru = mysqli_real_escape_string($conn, $_POST["status"]);
    $feedback_baru = mysqli_real_escape_string($conn, $_POST["feedback"]);
    
    $query_update = "UPDATE aspirasi SET 
                     status = '$status_baru', 
                     feedback = '$feedback_baru' 
                     WHERE id_aspirasi = $id";
              
    mysqli_query($conn, $query_update);

    if (mysqli_affected_rows($conn) >= 0) {
        echo "
            <script>
                alert('Tanggapan berhasil disimpan!');
                document.location.href = 'admin.php';
            </script>
        ";
    } else {
        echo "
            <script>
                alert('Gagal memperbarui data');
            </script>
        ";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proses Laporan - Admin</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #F1E9E9;
            color: #333;
        }

        .wrapper {
            max-width: 1000px;
            margin: auto;
            padding: 20px;
        }

        /* HEADER */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .header h1 {
            color: #982598;
            font-size: 24px;
        }

        .back {
            text-decoration: none;
            background: #fff;
            color: #982598;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            border: 1px solid #982598;
            transition: 0.3s;
        }

        .back:hover {
            background: #982598;
            color: #fff;
        }

        /* GRID RESPONSIVE */
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 8px 20px rgba(0,0,0,0.05);
        }

        .card h3 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #982598;
            border-bottom: 2px solid #f5f5f5;
            padding-bottom: 10px;
        }

        /* DETAIL STYLING */
        .info-group {
            margin-bottom: 15px;
        }

        .label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #999;
            margin-bottom: 4px;
        }

        .value {
            font-size: 15px;
            font-weight: 500;
            color: #444;
            line-height: 1.5;
        }

        /* IMAGE BOX */
        .img-box {
            margin-top: 15px;
            text-align: center;
        }

        .img-box img {
            max-width: 100%;
            height: auto;
            border-radius: 12px;
            border: 1px solid #eee;
        }

        /* FORM STYLING */
        select, textarea {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid #ddd;
            margin-top: 5px;
            margin-bottom: 20px;
            font-size: 14px;
            background: #fdfdfd;
        }

        textarea:focus, select:focus {
            outline: none;
            border-color: #982598;
        }

        button {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 30px;
            background: linear-gradient(135deg, #c084fc, #982598);
            color: white;
            font-weight: 700;
            font-size: 15px;
            cursor: pointer;
            transition: 0.3s;
            box-shadow: 0 4px 15px rgba(152, 37, 152, 0.3);
        }

        button:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }

        /* STATUS BADGE */
        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .menunggu { background: #fee2e2; color: #dc2626; }
        .proses { background: #fef9c3; color: #ca8a04; }
        .selesai { background: #dcfce7; color: #16a34a; }

        /* MOBILE ADJUSTMENT */
        @media(max-width: 850px){
            .grid { grid-template-columns: 1fr; }
            .wrapper { padding: 15px; }
        }
    </style>
</head>
<body>

<div class="wrapper">

    <div class="header">
        <h1>Tanggapi Aspirasi</h1>
        <a href="admin.php" class="back">← Kembali</a>
    </div>

    <div class="grid">

        <div class="card">
            <h3>Detail Aspirasi</h3>

            <div class="info-group">
                <div class="label">Nama Pelapor</div>
                <div class="value"><?= htmlspecialchars($laporan['nama']); ?></div>
            </div>

            <div class="info-group">
                <div class="label">Kategori & Tanggal</div>
                <div class="value">
                    <b><?= $laporan['nama_kategori']; ?></b> | 
                    <small><?= date('d M Y', strtotime($laporan['tanggal'])); ?></small>
                </div>
            </div>

            <div class="info-group">
                <div class="label">Status Saat Ini</div>
                <div class="value">
                    <span class="status <?= $laporan['status']; ?>">
                        <?= $laporan['status']; ?>
                    </span>
                </div>
            </div>

            <div class="info-group">
                <div class="label">Isi Aspirasi</div>
                <div class="value" style="background: #f9f9f9; padding: 10px; border-radius: 8px; border-left: 3px solid #982598;">
                    <?= nl2br(htmlspecialchars($laporan['keterangan'])); ?>
                </div>
            </div>

            <div class="info-group">
                <div class="label">Lampiran Bukti</div>
                <div class="img-box">
                    <?php if($laporan['foto']): ?>
                        <a href="assets/img/<?= $laporan['foto']; ?>" target="_blank">
                            <img src="assets/img/<?= $laporan['foto']; ?>" alt="Bukti Foto">
                        </a>
                    <?php else: ?>
                        <p style="color: #bbb; font-style: italic;">Tidak ada foto bukti</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="card">
            <h3>Tanggapan Admin</h3>

            <form method="post">
                <div class="info-group">
                    <div class="label">Ubah Status</div>
                    <select name="status" required>
                        <option value="menunggu" <?= ($laporan['status'] == 'menunggu') ? 'selected' : ''; ?>>Menunggu</option>
                        <option value="proses" <?= ($laporan['status'] == 'proses') ? 'selected' : ''; ?>>Proses</option>
                        <option value="selesai" <?= ($laporan['status'] == 'selesai') ? 'selected' : ''; ?>>Selesai</option>
                    </select>
                </div>

                <div class="info-group">
                    <div class="label">Pesan / Tanggapan Anda</div>
                    <textarea name="feedback" rows="8" placeholder="Tulis alasan atau langkah tindak lanjut..." required><?= $laporan['feedback']; ?></textarea>
                </div>

                <button type="submit" name="update">
                    Simpan Perubahan
                </button>
            </form>
        </div>

    </div>
</div>

</body>
</html>
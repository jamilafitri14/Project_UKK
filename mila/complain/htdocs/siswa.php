<?php
session_start();
require 'functions.php';

// Proteksi Halaman
if (!isset($_SESSION['login']) || $_SESSION['role'] != 'siswa') {
    header("Location: index.php");
    exit;
}

$nis = $_SESSION['id'];

// Logika Kirim Laporan
if (isset($_POST["kirim"])) {
    $isi_laporan = htmlspecialchars($_POST["isi_laporan"]);
    $id_kategori = $_POST["id_kategori"];
    $lokasi = htmlspecialchars($_POST["lokasi"]);
    $tanggal = date("Y-m-d");
    $status = "menunggu";

    $nama_foto = $_FILES['foto']['name'];
    $sumber_foto = $_FILES['foto']['tmp_name'];
    $folder_tujuan = './assets/img/';
    
    if($nama_foto != "") {
        move_uploaded_file($sumber_foto, $folder_tujuan . $nama_foto);
    } else {
        $nama_foto = "";
    }

    $query = "INSERT INTO aspirasi 
              VALUES (NULL, '$nis', '$id_kategori', '$lokasi', '$isi_laporan', '$nama_foto', '$tanggal', '$status', '')";
    
    mysqli_query($conn, $query);

    if (mysqli_affected_rows($conn) > 0) {
        echo "<script>
                alert('Laporan Terkirim!');
                document.location.href = 'siswa.php';
              </script>";
    } else {
        echo "<script>alert('Gagal mengirim laporan!');</script>";
    }
}

// Ambil Data Kategori & Riwayat
$kategori = query("SELECT * FROM kategori");
$riwayat = query("SELECT aspirasi.*, kategori.nama_kategori 
                  FROM aspirasi 
                  JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori
                  WHERE nis = '$nis' ORDER BY id_aspirasi DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aspirasi Siswa - Panel</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fa, #e2e8f0);
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        /* TOP BAR */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
        }

        .top-bar h1 {
            font-size: 22px;
            color: #982598;
        }

        .btn-logout {
            background: #982598;
            color: white;
            padding: 8px 18px;
            border-radius: 20px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            transition: 0.3s;
        }

        /* FORM SECTION */
        h3 {
            margin-bottom: 15px;
            font-size: 18px;
            color: #444;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            border: 1.5px solid #eee;
            background: #fafafa;
            font-size: 14px;
            transition: 0.3s;
        }

        input:focus, textarea:focus {
            outline: none;
            border-color: #982598;
            background: #fff;
        }

        textarea { grid-column: span 2; resize: vertical; }

        .file-label {
            grid-column: span 2;
            font-size: 12px;
            color: #777;
            margin-bottom: -10px;
        }

        button[name="kirim"] {
            grid-column: span 2;
            padding: 15px;
            border: none;
            border-radius: 30px;
            background: linear-gradient(135deg, #c084fc, #982598);
            color: white;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(152, 37, 152, 0.2);
            margin-top: 10px;
        }

        /* RIWAYAT HEADER & FASTLINK */
        .riwayat-header {
            margin-top: 45px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .riwayat-header h3 { margin: 0; }

        .fastlink-detail {
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            color: #982598;
            background: #f3e8ff;
            padding: 8px 16px;
            border-radius: 12px;
            border: 1px solid rgba(152, 37, 152, 0.1);
            transition: 0.3s;
        }

        .fastlink-detail:hover {
            background: #982598;
            color: white;
            transform: translateX(5px);
        }

        /* TABLE (DESKTOP) */
        .table-wrapper {
            overflow-x: auto;
            border-radius: 15px;
            border: 1px solid #f0f0f0;
        }

        table { width: 100%; border-collapse: collapse; font-size: 14px; }
        thead { background: #982598; color: white; }
        th, td { padding: 15px; text-align: left; }
        tr:nth-child(even) { background: #fafafa; }

        /* BADGES */
        .status {
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: bold;
        }
        .menunggu { background: #fef3c7; color: #92400e; }
        .proses { background: #dbeafe; color: #1e40af; }
        .selesai { background: #dcfce7; color: #166534; }

        /* ACTION BUTTONS */
        .btn-action {
            text-decoration: none;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 11px;
            color: white;
            margin-right: 3px;
        }
        .btn-edit { background: #f59e0b; }
        .btn-delete { background: #ef4444; }

        /* MOBILE CARDS */
        .card-container { display: none; }
        .card {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 15px;
        }
        .card img { width: 100%; border-radius: 10px; margin: 10px 0; }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            body { padding: 10px; }
            .container { padding: 20px; }
            .form-grid { grid-template-columns: 1fr; }
            textarea, button[name="kirim"] { grid-column: span 1; }
            table { display: none; }
            .card-container { display: block; }
            .top-bar h1 { font-size: 18px; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="top-bar">
        <h1>Halo, <?= $_SESSION['nama']; ?> 👋</h1>
        <a href="logout.php" class="btn-logout">Logout</a>
    </div>

    <h3>📝 Tulis Laporan</h3>
    <form method="post" enctype="multipart/form-data" class="form-grid">
        <select name="id_kategori" required>
            <option value="" disabled selected>Pilih Kategori</option>
            <?php foreach($kategori as $k) : ?>
                <option value="<?= $k['id_kategori']; ?>"> <?= $k['nama_kategori']; ?> </option>
            <?php endforeach; ?>
        </select>

        <input type="text" name="lokasi" placeholder="Lokasi Kejadian" required>

        <textarea name="isi_laporan" rows="4" placeholder="Deskripsikan laporan Anda secara detail..." required></textarea>

        <div class="file-label">Lampirkan Foto (Opsional):</div>
        <input type="file" name="foto" accept="image/*" class="file-input">

        <button type="submit" name="kirim">Kirim Laporan</button>
    </form>

    <div class="riwayat-header">
        <h3>📂 Riwayat Laporan</h3>
        <a href="riwayat.php" class="fastlink-detail">Detail Riwayat →</a>
    </div>

    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Tgl</th>
                    <th>Kategori</th>
                    <th>Laporan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($riwayat as $row): ?>
                <tr>
                    <td><?= date('d/m/y', strtotime($row['tanggal'])); ?></td>
                    <td><strong><?= $row['nama_kategori']; ?></strong></td>
                    <td><?= substr($row['keterangan'], 0, 40); ?>...</td>
                    <td><span class="status <?= strtolower($row['status']); ?>"><?= $row['status']; ?></span></td>
                    <td>
                        <?php if($row['status']=='menunggu'): ?>
                            <a href="edit.php?id=<?= $row['id_aspirasi']; ?>" class="btn-action btn-edit">Edit</a>
                            <a href="batal.php?id=<?= $row['id_aspirasi']; ?>" class="btn-action btn-delete" onclick="return confirm('Yakin ingin membatalkan?')">Batal</a>
                        <?php else: ?>
                            <small>-</small>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card-container">
        <?php foreach($riwayat as $row): ?>
        <div class="card">
            <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                <small style="color: #999;"><?= $row['tanggal']; ?></small>
                <span class="status <?= strtolower($row['status']); ?>"><?= $row['status']; ?></span>
            </div>
            <p><strong><?= $row['nama_kategori']; ?></strong></p>
            <p style="font-size: 14px; color: #555; margin: 8px 0;"><?= $row['keterangan']; ?></p>
            
            <?php if($row['foto']): ?>
                <img src="assets/img/<?= $row['foto']; ?>" alt="Foto Laporan">
            <?php endif; ?>

            <div style="margin-top: 12px;">
                <?php if($row['status']=='menunggu'): ?>
                    <a href="edit.php?id=<?= $row['id_aspirasi']; ?>" class="btn-action btn-edit">Edit</a>
                    <a href="batal.php?id=<?= $row['id_aspirasi']; ?>" class="btn-action btn-delete" onclick="return confirm('Yakin ingin membatalkan?')">Batal</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
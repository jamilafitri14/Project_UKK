<?php
session_start();
require 'functions.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'admin') {
    header("Location: index.php");
    exit;
}

$kategori_list = query("SELECT * FROM kategori");

$query_dasar = "SELECT aspirasi.*, siswa.nama, kategori.nama_kategori 
                FROM aspirasi 
                JOIN siswa ON aspirasi.nis = siswa.nis 
                JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori 
                WHERE 1=1 "; 

if (isset($_POST['cari'])) {
    if (!empty($_POST['id_kategori'])) {
        $id_kat = $_POST['id_kategori'];
        $query_dasar .= " AND aspirasi.id_kategori = '$id_kat'";
    }
    if (!empty($_POST['status'])) {
        $stat = $_POST['status'];
        $query_dasar .= " AND aspirasi.status = '$stat'";
    }
    if (!empty($_POST['tgl_awal']) && !empty($_POST['tgl_akhir'])) {
        $awal = $_POST['tgl_awal'];
        $akhir = $_POST['tgl_akhir'];
        $query_dasar .= " AND aspirasi.tanggal BETWEEN '$awal' AND '$akhir'";
    }
}

$query_dasar .= " ORDER BY aspirasi.tanggal DESC";
$laporan = query($query_dasar);

$total_data = count($laporan);
$menunggu = count(array_filter($laporan, fn($l) => $l['status'] == 'menunggu'));
$proses = count(array_filter($laporan, fn($l) => $l['status'] == 'proses'));
$selesai = count(array_filter($laporan, fn($l) => $l['status'] == 'selesai'));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Aspirasi</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', sans-serif; }
        body { background: #f8fafc; color: #334155; padding-bottom: 50px; }
        .wrapper { max-width: 1200px; margin: 0 auto; padding: 20px; }

        /* HEADER */
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { font-size: 24px; color: #982598; }
        .logout { background: #ef4444; color: white; padding: 8px 20px; border-radius: 25px; text-decoration: none; font-size: 14px; font-weight: 600; }

        /* STATS (Tidak diubah sesuai permintaan) */
        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .card-stat {
            background: white;
            padding: 20px;
            border-radius: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            text-align: center;
            border-bottom: 4px solid transparent;
        }

        .card-stat:nth-child(1) { border-color: #982598; }
        .card-stat:nth-child(2) { border-color: #ef4444; }
        .card-stat:nth-child(3) { border-color: #f59e0b; }
        .card-stat:nth-child(4) { border-color: #10b981; }

        .card-stat h4 { font-size: 13px; color: #64748b; text-transform: uppercase; letter-spacing: 1px; }
        .card-stat p { font-size: 28px; font-weight: 800; color: #1e293b; margin-top: 5px; }

        /* FILTER SECTION - PERBAIKAN UKURAN AGAR TIDAK OVERLAY */
        .filter { 
            background: white; 
            padding: 15px 20px; 
            border-radius: 20px; 
            box-shadow: 0 4px 6px rgba(0,0,0,0.05); 
            margin-bottom: 30px; 
        }
        
        .filter form { 
            display: flex; 
            flex-wrap: wrap; 
            gap: 8px; /* Dipersempit agar muat */
            align-items: center; 
            width: 100%; 
        }

        /* Mengecilkan elemen filter */
        .filter select {
            flex: 1;
            min-width: 130px; /* Ukuran diperkecil */
            padding: 8px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            font-size: 13px;
        }

        .filter .date-group {
            display: flex;
            align-items: center;
            gap: 5px;
            flex: 1.5; 
            min-width: 240px; /* Ukuran diperkecil agar tidak dorong border */
        }

        .filter .date-group input {
            width: 100%;
            padding: 6px 8px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-size: 13px;
        }

        .filter button, .btn-reset {
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-align: center;
            border: none;
        }

        .filter button {
            background: #982598;
            color: white;
            min-width: 80px;
        }

        .btn-reset {
            background: #f1f5f9;
            color: #475569;
            text-decoration: none;
            min-width: 70px;
        }

        .print-box { 
            margin-left: auto; 
            padding-left: 10px;
        }
        
        .link-print { 
            color: #982598; 
            font-weight: bold; 
            text-decoration: none; 
            font-size: 13px; 
            white-space: nowrap; 
        }

        /* LAPORAN GRID */
        .laporan-container { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 20px; }
        .laporan-card { background: white; border-radius: 20px; padding: 20px; box-shadow: 0 10px 15px rgba(0,0,0,0.05); display: flex; flex-direction: column; }
        .laporan-header { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .status { padding: 4px 12px; border-radius: 20px; font-size: 11px; font-weight: 800; }
        .menunggu { background: #fee2e2; color: #dc2626; }
        .proses { background: #fef3c7; color: #d97706; }
        .selesai { background: #dcfce7; color: #16a34a; }
        .laporan-body { flex-grow: 1; margin: 15px 0; }
        .badge { background: #f1f5f9; padding: 3px 8px; border-radius: 6px; font-size: 12px; color: #64748b; margin-right: 5px; }

        .laporan-footer { display: flex; justify-content: space-between; align-items: center; padding-top: 15px; border-top: 1px solid #f1f5f9; }
        .btn-tanggapi { background: #982598; color: white; padding: 8px 15px; border-radius: 10px; text-decoration: none; font-size: 13px; font-weight: 600; }

        @media (max-width: 992px) {
            .stats { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 768px) {
            .filter form { flex-direction: column; align-items: stretch; }
            .filter .date-group { flex-direction: column; min-width: unset; }
            .print-box { margin: 10px auto; text-align: center; }
        }
    </style>
</head>
<body>

<div class="wrapper">
    <div class="header">
        <h1>Admin Dashboard</h1>
        <a href="logout.php" class="logout">Logout</a>
    </div>

   <div class="stats">
        <div class="card-stat"><h4>Total</h4><p><?= $total_data; ?></p></div>
        <div class="card-stat"><h4>Menunggu</h4><p><?= $menunggu; ?></p></div>
        <div class="card-stat"><h4>Proses</h4><p><?= $proses; ?></p></div>
        <div class="card-stat"><h4>Selesai</h4><p><?= $selesai; ?></p></div>
    </div>

    <div class="filter">
        <form method="post">
            <select name="id_kategori">
                <option value="">Kategori</option>
                <?php foreach($kategori_list as $k): ?>
                <option value="<?= $k['id_kategori']; ?>"><?= $k['nama_kategori']; ?></option>
                <?php endforeach; ?>
            </select>

            <select name="status">
                <option value="">Status</option>
                <option value="menunggu">Menunggu</option>
                <option value="proses">Proses</option>
                <option value="selesai">Selesai</option>
            </select>

            <div class="date-group">
                <input type="date" name="tgl_awal">
                <span style="font-size: 11px; color: #999;">-</span>
                <input type="date" name="tgl_akhir">
            </div>

            <button type="submit" name="cari">Cari</button>
            <a href="admin.php" class="btn-reset">Reset</a>

            <div class="print-box">
                <a href="print_laporan.php" target="_blank" class="link-print">Cetak 🖨️</a>
            </div>
        </form>
    </div>

    <div class="laporan-container">
        <?php foreach($laporan as $row) : ?>
        <div class="laporan-card">
            <div class="laporan-header">
                <div>
                    <h4 style="font-size: 16px;"><?= htmlspecialchars($row['nama']); ?></h4>
                    <small style="color: #94a3b8;"><?= date('d M Y', strtotime($row['tanggal'])); ?></small>
                </div>
                <span class="status <?= $row['status']; ?>"><?= $row['status']; ?></span>
            </div>

            <div class="laporan-body">
                <div style="margin-bottom: 10px;">
                    <span class="badge"><?= $row['nama_kategori']; ?></span>
                    <span class="badge">📍 <?= htmlspecialchars($row['lokasi']); ?></span>
                </div>
                <p><?= nl2br(htmlspecialchars($row['keterangan'])); ?></p>
            </div>

            <div class="laporan-footer">
                <?php if($row['foto']) : ?>
                    <a href="assets/img/<?= $row['foto']; ?>" target="_blank" style="font-size: 12px; color: #982598; text-decoration: none;">Lihat Bukti</a>
                <?php else : ?>
                    <small style="color: #cbd5e1;">Tanpa Foto</small>
                <?php endif; ?>
                <a href="proses_laporan.php?id=<?= $row['id_aspirasi']; ?>" class="btn-tanggapi">Tanggapi</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
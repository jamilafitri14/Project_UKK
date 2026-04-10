<?php
require 'functions.php';

// ambil data
$data = query("SELECT aspirasi.*, siswa.nama, kategori.nama_kategori 
               FROM aspirasi
               JOIN siswa ON aspirasi.nis = siswa.nis
               JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori
               WHERE status IN ('proses','selesai','menunggu')
               ORDER BY tanggal DESC");

// hitung statistik
$jumlah_menunggu = 0;
$jumlah_proses = 0;
$jumlah_selesai = 0;

foreach ($data as $row) {
    if ($row['status'] == 'menunggu') {
        $jumlah_menunggu++;
    } elseif ($row['status'] == 'proses') {
        $jumlah_proses++;
    } elseif ($row['status'] == 'selesai') {
        $jumlah_selesai++;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cetak Laporan</title>

    <style>
    body {
        font-family: Arial, sans-serif;
        padding: 30px;
    }

    h2 {
        text-align: center;
        margin-bottom: 5px;
    }

    .tanggal {
        text-align: center;
        margin-bottom: 20px;
        font-size: 13px;
    }

    /* SUMMARY */
    .summary-box {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }

    .summary-box div {
        border: 1px solid black;
        padding: 10px;
        text-align: center;
        width: 30%;
        font-size: 13px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        border: 1px solid black;
        padding: 8px;
        font-size: 12px;
    }

    th {
        background: #eee;
    }

    .text-center {
        text-align: center;
    }

    /* PRINT */
    @media print {
        button {
            display: none;
        }
    }

    .print-btn {
        margin-bottom: 20px;
        padding: 10px 20px;
        border: none;
        background: #7c3aed;
        color: white;
        border-radius: 10px;
        cursor: pointer;
    }
    </style>
</head>

<body>

<button onclick="window.print()" class="print-btn">🖨️ Cetak / Save PDF</button>

<h2>Laporan Aspirasi Siswa</h2>
<div class="tanggal">Tanggal Cetak: <?= date("d-m-Y"); ?></div>

<!-- RINGKASAN -->
<div class="summary-box">
    <div>
        Menunggu<br>
        <b><?= $jumlah_menunggu; ?></b>
    </div>
    <div>
        Proses<br>
        <b><?= $jumlah_proses; ?></b>
    </div>
    <div>
        Selesai<br>
        <b><?= $jumlah_selesai; ?></b>
    </div>
</div>

<table>
    <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Nama</th>
        <th>Kategori</th>
        <th>Laporan</th>
        <th>Status</th>
    </tr>

    <?php $i = 1; foreach($data as $row): ?>
    <tr>
        <td class="text-center"><?= $i++; ?></td>
        <td><?= $row['tanggal']; ?></td>
        <td><?= $row['nama']; ?></td>
        <td><?= $row['nama_kategori']; ?></td>
        <td><?= $row['keterangan']; ?></td>
        <td><?= $row['status']; ?></td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>
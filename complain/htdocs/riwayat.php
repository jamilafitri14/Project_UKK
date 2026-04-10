<?php
session_start();
require 'functions.php';

if (!isset($_SESSION['login']) || $_SESSION['role'] != 'siswa') {
    header("Location: index.php");
    exit;
}

$nis = $_SESSION['id'];

// DELETE MASSAL
if (isset($_POST['hapus_massal'])) {
    if (!empty($_POST['pilih'])) {
        foreach ($_POST['pilih'] as $id) {
            $cek = query("SELECT status FROM aspirasi WHERE id_aspirasi='$id'")[0];
            if ($cek['status'] == 'menunggu') {
                mysqli_query($conn, "DELETE FROM aspirasi WHERE id_aspirasi='$id'");
            }
        }
        echo "<script>
                alert('Laporan terpilih berhasil dihapus!');
                document.location.href='riwayat.php';
              </script>";
    }
}

// DATA
$riwayat = query("SELECT aspirasi.*, kategori.nama_kategori 
                  FROM aspirasi 
                  JOIN kategori ON aspirasi.id_kategori = kategori.id_kategori
                  WHERE nis = '$nis'
                  ORDER BY tanggal DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Laporan - Aspirasi</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Roboto, sans-serif;
        }

        body {
            background: #f4f7f6;
            padding: 20px;
            color: #333;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            background: white;
            padding: 25px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        h2 {
            color: #982598;
            margin-bottom: 20px;
            font-size: 24px;
        }

        /* TOP ACTION */
        .top-action {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            gap: 15px;
        }

        .btn-back {
            text-decoration: none;
            color: #982598;
            font-weight: 600;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-delete-massal {
            background: #ef4444;
            color: white;
            padding: 10px 18px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-size: 13px;
            font-weight: bold;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
            display: none; /* Muncul via JS */
            transition: 0.3s;
        }

        .btn-delete-massal:hover { opacity: 0.9; transform: translateY(-2px); }

        /* TABLE (DESKTOP) */
        .table-wrapper {
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid #eee;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        thead { background: #982598; color: white; }
        th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; }

        /* IMAGE */
        .img-preview {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
        }

        /* STATUS BADGE */
        .status {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .menunggu { background: #fef3c7; color: #92400e; }
        .proses { background: #dbeafe; color: #1e40af; }
        .selesai { background: #dcfce7; color: #166534; }

        /* BUTTONS */
        .btn-action {
            text-decoration: none;
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            color: white;
            display: inline-block;
            margin-bottom: 5px;
        }
        .btn-edit { background: #f59e0b; }
        .btn-batal { background: #ef4444; }

        /* MOBILE CARDS */
        .card-container { display: none; }
        .card {
            background: #fff;
            border: 1px solid #eee;
            border-radius: 15px;
            padding: 15px;
            margin-bottom: 15px;
            position: relative;
        }
        .card-checkbox {
            position: absolute;
            top: 15px;
            right: 15px;
            transform: scale(1.3);
        }
        .card p { margin-bottom: 8px; font-size: 14px; line-height: 1.4; }
        .card img { width: 100%; border-radius: 10px; margin: 10px 0; }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            body { padding: 10px; }
            .container { padding: 15px; }
            h2 { font-size: 20px; }
            .table-wrapper { display: none; }
            .card-container { display: block; }
            .top-action { flex-direction: row; }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Riwayat Laporan</h2>

    <form method="post" id="formRiwayat">
        <div class="top-action">
            <a href="siswa.php" class="btn-back">← Kembali ke Panel</a>

            <button type="submit" name="hapus_massal" id="btnAksi" class="btn-delete-massal" 
                    onclick="return confirm('Hapus semua laporan yang dipilih?')">
                Hapus Terpilih
            </button>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th><input type="checkbox" id="checkAll"></th>
                        <th>Tgl</th>
                        <th>Kategori</th>
                        <th>Laporan</th>
                        <th>Foto</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($riwayat as $row) : ?>
                    <tr>
                        <td>
                            <?php if($row['status'] == 'menunggu') : ?>
                                <input type="checkbox" name="pilih[]" value="<?= $row['id_aspirasi']; ?>" class="item-check">
                            <?php else : ?>
                                <input type="checkbox" disabled title="Hanya status menunggu yang bisa dihapus">
                            <?php endif; ?>
                        </td>
                        <td><?= date('d/m/y', strtotime($row['tanggal'])); ?></td>
                        <td><b><?= $row['nama_kategori']; ?></b></td>
                        <td style="max-width: 200px;"><?= $row['keterangan']; ?></td>
                        <td>
                            <?php if($row['foto']) : ?>
                                <img src="assets/img/<?= $row['foto']; ?>" class="img-preview">
                            <?php else : ?>
                                <small style="color: #999;">No Image</small>
                            <?php endif; ?>
                        </td>
                        <td><span class="status <?= strtolower($row['status']); ?>"><?= $row['status']; ?></span></td>
                        <td>
                            <?php if($row['status'] == 'menunggu') : ?>
                                <a href="edit.php?id=<?= $row['id_aspirasi']; ?>" class="btn-action btn-edit">Edit</a>
                                <a href="batal.php?id=<?= $row['id_aspirasi']; ?>" class="btn-action btn-batal" onclick="return confirm('Batalkan laporan?')">Batal</a>
                            <?php else : ?>
                                <small style="color: #888;">No Action</small>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="card-container">
            <?php foreach($riwayat as $row) : ?>
            <div class="card">
                <?php if($row['status'] == 'menunggu') : ?>
                    <input type="checkbox" name="pilih[]" value="<?= $row['id_aspirasi']; ?>" class="item-check card-checkbox">
                <?php endif; ?>

                <div style="margin-bottom: 10px;">
                    <span class="status <?= strtolower($row['status']); ?>"><?= $row['status']; ?></span>
                    <small style="margin-left: 10px; color: #888;"><?= $row['tanggal']; ?></small>
                </div>
                
                <p><strong>Kategori:</strong> <?= $row['nama_kategori']; ?></p>
                <p><strong>Lokasi:</strong> <?= $row['lokasi']; ?></p>
                <p><strong>Isi:</strong> <?= $row['keterangan']; ?></p>

                <?php if($row['foto']) : ?>
                    <img src="assets/img/<?= $row['foto']; ?>" alt="Laporan">
                <?php endif; ?>

                <div style="margin-top: 10px; border-top: 1px solid #eee; padding-top: 10px;">
                    <p><strong>Tanggapan:</strong> <br>
                        <small style="color: #555;"><?= $row['feedback'] ?: "Belum ada tanggapan."; ?></small>
                    </p>
                    
                    <?php if($row['status'] == 'menunggu') : ?>
                        <a href="edit.php?id=<?= $row['id_aspirasi']; ?>" class="btn-action btn-edit">Edit</a>
                        <a href="batal.php?id=<?= $row['id_aspirasi']; ?>" class="btn-action btn-batal">Batal</a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </form>
</div>

<script>
    const checkAll = document.getElementById('checkAll');
    const checkboxes = document.querySelectorAll('.item-check');
    const btnAksi = document.getElementById('btnAksi');

    // Ceklis Semua
    if(checkAll) {
        checkAll.onclick = function() {
            checkboxes.forEach(cb => {
                cb.checked = this.checked;
            });
            toggleButton();
        }
    }

    // Toggle Button Hapus Massal
    checkboxes.forEach(cb => {
        cb.addEventListener('change', toggleButton);
    });

    function toggleButton() {
        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        btnAksi.style.display = anyChecked ? 'block' : 'none';
    }
</script>

</body>
</html>
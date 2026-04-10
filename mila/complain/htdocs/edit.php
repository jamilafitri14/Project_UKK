<?php
session_start();
require 'functions.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

// ambil data berdasarkan id_aspirasi
$data = query("SELECT * FROM aspirasi WHERE id_aspirasi = '$id'")[0];

// ambil kategori
$kategori = query("SELECT * FROM kategori");

// CEGah edit kalau bukan status menunggu
if ($data['status'] != 'menunggu') {
    echo "<script>
            alert('Laporan tidak bisa diedit!');
            document.location.href='siswa.php';
          </script>";
    exit;
}

if (isset($_POST['submit'])) {

    $isi_laporan = htmlspecialchars($_POST["isi_laporan"]);
    $id_kategori = $_POST["id_kategori"];
    $lokasi = htmlspecialchars($_POST["lokasi"]);

    // upload foto baru (optional)
    $nama_foto = $_FILES['foto']['name'];
    $sumber_foto = $_FILES['foto']['tmp_name'];
    $folder_tujuan = './assets/img/';

    if ($nama_foto != "") {
        move_uploaded_file($sumber_foto, $folder_tujuan . $nama_foto);
    } else {
        $nama_foto = $data['foto']; // pakai foto lama
    }

    $query = "UPDATE aspirasi SET
                id_kategori = '$id_kategori',
                lokasi = '$lokasi',
                keterangan = '$isi_laporan',
                foto = '$nama_foto'
              WHERE id_aspirasi = '$id'
              ";

    mysqli_query($conn, $query);

    if (mysqli_affected_rows($conn) > 0) {
        echo "<script>
                alert('Laporan berhasil diupdate!');
                document.location.href='siswa.php';
              </script>";
    } else {
        echo "<script>alert('Tidak ada perubahan!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Laporan - Aspirasi</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Roboto, sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #4d0a4d, #982598);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .box {
            background: white;
            padding: 30px;
            border-radius: 20px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            /* Menarik sedikit ke atas agar proporsional */
            transform: translateY(-5%);
        }

        h2 {
            color: #982598;
            text-align: center;
            margin-bottom: 20px;
            font-size: 22px;
        }

        label {
            font-size: 13px;
            font-weight: 600;
            color: #666;
            display: block;
            margin-bottom: 5px;
            margin-left: 5px;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border-radius: 12px;
            border: 1.5px solid #eee;
            background: #f9f9f9;
            font-size: 14px;
            transition: 0.3s;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #982598;
            background: #fff;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .current-photo {
            font-size: 11px;
            color: #888;
            margin-top: -10px;
            margin-bottom: 15px;
            display: block;
        }

        button {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #c084fc, #982598);
            color: white;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
            box-shadow: 0 5px 15px rgba(152, 37, 152, 0.3);
            transition: 0.3s;
        }

        button:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        button:active {
            transform: translateY(0);
        }

        .back {
            display: block;
            margin-top: 20px;
            text-align: center;
            text-decoration: none;
            color: #982598;
            font-size: 14px;
            font-weight: 600;
        }

        .back:hover {
            text-decoration: underline;
        }

        /* Responsive HP */
        @media (max-width: 480px) {
            .box {
                padding: 20px;
                transform: translateY(-8%); /* Lebih tinggi sedikit di HP */
            }

            h2 {
                font-size: 20px;
            }

            button {
                padding: 16px;
            }
        }
    </style>
</head>

<body>

<div class="box">
    <h2>📝 Edit Laporan</h2>

    <form method="post" enctype="multipart/form-data" autocomplete="off">

        <label>Kategori</label>
        <select name="id_kategori" required>
            <?php foreach($kategori as $k) : ?>
                <option value="<?= $k['id_kategori']; ?>"
                    <?= $k['id_kategori'] == $data['id_kategori'] ? 'selected' : ''; ?>>
                    <?= $k['nama_kategori']; ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label>Lokasi Kejadian</label>
        <input type="text" name="lokasi" value="<?= $data['lokasi']; ?>" placeholder="Contoh: Depan Kantin" required>

        <label>Detail Laporan</label>
        <textarea name="isi_laporan" placeholder="Tuliskan perubahan laporan Anda..." required><?= $data['keterangan']; ?></textarea>

        <label>Ganti Foto (Opsional)</label>
        <input type="file" name="foto" accept="image/*">
        <?php if($data['foto']): ?>
            <span class="current-photo">📸 Foto lama: <?= $data['foto']; ?></span>
        <?php endif; ?>

        <button type="submit" name="submit">Simpan Perubahan</button>
    </form>

    <a href="siswa.php" class="back">← Batal dan Kembali</a>
</div>

</body>
</html>
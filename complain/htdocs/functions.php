<?php
$conn = mysqli_connect("sql208.infinityfree.com", "if0_41558678", "Drekie14", "if0_41558678_db_pengaduan_sekolah");

function query($query) {
    global $conn;
    $result = mysqli_query($conn, $query);
    $rows = [];
    while($row = mysqli_fetch_assoc($result)) { 
        $rows[] = $row;
    }
    return $rows;
}

/* =========================
   LOGIN
========================= */
function cek_login($username, $password) {
    global $conn;

    // ADMIN
    $q_admin = mysqli_query($conn, "SELECT * FROM admin WHERE username='$username' AND password='$password'");
    if (mysqli_num_rows($q_admin) > 0) {
        $data = mysqli_fetch_assoc($q_admin);

        $_SESSION['login'] = true;
        $_SESSION['role'] = 'admin';
        $_SESSION['nama'] = $data['nama_petugas'];
        $_SESSION['id'] = $data['id_admin'];
        return "admin";
    }

    // SISWA
    $q_siswa = mysqli_query($conn, "SELECT * FROM siswa WHERE nis='$username' AND password='$password'");
    if (mysqli_num_rows($q_siswa) > 0) {
        $data = mysqli_fetch_assoc($q_siswa);

        $_SESSION['login'] = true;
        $_SESSION['role'] = 'siswa';
        $_SESSION['nama'] = $data['nama'];
        $_SESSION['id'] = $data['nis'];
        return "siswa";
    }

    return false;
}

/* =========================
   REGISTER SISWA (FIX)
========================= */
function register_siswa($nama, $nis, $kelas, $password) {
    global $conn;

    $nama = mysqli_real_escape_string($conn, $nama);
    $nis = mysqli_real_escape_string($conn, $nis);
    $kelas = mysqli_real_escape_string($conn, $kelas);
    $password = mysqli_real_escape_string($conn, $password);

    // cek duplikat
    $cek = mysqli_query($conn, "SELECT * FROM siswa WHERE nis='$nis'");
    if (mysqli_num_rows($cek) > 0) {
        return "duplikat";
    }

    // INSERT YANG BENAR (pakai nama kolom)
    $query = "INSERT INTO siswa (nis, nama, kelas, password)
              VALUES ('$nis', '$nama', '$kelas', '$password')";

    if (mysqli_query($conn, $query)) {
        return true;
    } else {
        return mysqli_error($conn);
    }
}
?>
<?php
session_start();
require 'functions.php';

$error = "";

// LOGIN logic tetap sama
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = cek_login($username, $password);

    if ($role == 'admin') {
        header("Location: admin.php");
        exit;
    } elseif ($role == 'siswa') {
        header("Location: siswa.php");
        exit;
    } else {
        $error = "Username atau Password salah!";
    }
}

// REGISTER logic tetap sama
if (isset($_POST['register'])) {
    $nama     = $_POST['nama'];
    $nis      = $_POST['username_reg'];
    $kelas    = $_POST['kelas'];
    $password = $_POST['password_reg'];

    $result = register_siswa($nama, $nis, $kelas, $password);

    if ($result === true) {
        echo "<script>alert('Registrasi berhasil, silakan login!');</script>";
    } elseif ($result === "duplikat") {
        $error = "NIS sudah terdaftar!";
    } else {
        $error = "Registrasi gagal: " . $result;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Siswa</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Roboto, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: linear-gradient(135deg, #4d0a4d, #982598);
            padding: 20px;
        }

        /* LAYOUT MAIN WRAPPER */
        .wrapper {
            display: flex;
            max-width: 1000px;
            width: 100%;
            align-items: center;
            gap: 50px;
        }

        /* INFO SECTION */
        .info-section {
            flex: 1;
            color: white;
        }

        .info-section h1 {
            font-size: 3rem;
            margin-bottom: 20px;
            line-height: 1.1;
        }

        .info-section p {
            font-size: 1.1rem;
            line-height: 1.6;
            opacity: 0.9;
            margin-bottom: 30px;
            text-align: left; /* Berbeda dengan di form yang center */
        }

        .features {
            list-style: none;
        }

        .features li {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.95rem;
        }

        .features li i {
            background: rgba(255,255,255,0.2);
            padding: 5px 10px;
            border-radius: 50%;
            font-style: normal;
        }

        /* CONTAINER FORM (Sisi Kanan) */
        .container {
            width: 100%;
            max-width: 400px;
            background: #ffffff;
            padding: 35px;
            border-radius: 24px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }

        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 5px;
        }

        .subtitle {
            text-align: center;
            font-size: 14px;
            color: #777;
            margin-bottom: 25px;
        }

        /* TOGGLE */
        .toggle {
            display: flex;
            background: #f4f4f4;
            border-radius: 30px;
            padding: 5px;
            margin-bottom: 25px;
        }

        .toggle button {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            background: transparent;
            font-weight: 600;
            transition: 0.3s;
        }

        .toggle .active {
            background: linear-gradient(135deg, #c084fc, #E491C9);
            color: white;
            box-shadow: 0 4px 10px rgba(192, 132, 252, 0.3);
        }

        /* FORM STYLING */
        .form { display: none; animation: fadeIn 0.4s ease; }
        .form.active { display: block; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .input-group { margin-bottom: 18px; }
        .input-group label { display: block; font-size: 13px; margin-bottom: 8px; color: #555; font-weight: 600; }
        .input-group input {
            width: 100%;
            padding: 13px 18px;
            border-radius: 12px;
            border: 1.5px solid #eee;
            background: #fcfcfc;
            font-size: 15px;
        }

        .input-group input:focus { outline: none; border-color: #c084fc; background: #fff; }

        .submit-btn {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 30px;
            background: linear-gradient(135deg, #c084fc, #E491C9);
            color: white;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
            transition: 0.3s;
        }

        .submit-btn:hover { opacity: 0.9; transform: translateY(-2px); }

        .error {
            background: #fff1f1;
            color: #d32f2f;
            padding: 12px;
            border-radius: 12px;
            font-size: 13px;
            text-align: center;
            margin-bottom: 20px;
            border: 1px solid #ffdadb;
        }

        /* RESPONSIVE */
        @media (max-width: 900px) {
            .wrapper {
                flex-direction: column;
                text-align: center;
                gap: 30px;
            }
            .info-section h1 { font-size: 2.2rem; }
            .info-section p { text-align: center; }
            .features { display: none; } /* Sembunyikan list fitur di HP agar ringkas */
        }
    </style>
</head>
<body>

<div class="wrapper">
    <div class="info-section">
        <h1>Pusat Aspirasi Sekolah.</h1>
        <p>Sampaikan keluhan, saran, atau laporan Anda secara aman, transparan, dan terstruktur.</p>
        
        <ul class="features">
            <li><span>✔</span>Kirim pengaduan atau aspirasi dalam hitungan detik.</li>
            <li><span>✔</span>Siswa dapat melihat progres tindak lanjut laporan secara real-time.</li>
            <li><span>✔</span>Guru & Admin dapat memantau dan merespons keluhan dengan lebih efisien.</li>
        </ul>
    </div>

    <div class="container">
        <h2 id="title">Login</h2>
        <p class="subtitle">Masuk untuk melanjutkan aktivitas Anda</p>

        <div class="toggle">
            <button id="btnLogin" class="active">Login</button>
            <button id="btnRegister">Register</button>
        </div>

        <?php if ($error != ""): ?>
            <div class="error"><?= $error ?></div>
        <?php endif; ?>

        <form method="post" class="form active" id="loginForm" autocomplete="off">
            <div class="input-group">
                <label>Username / NIS</label>
                <input type="text" name="username" placeholder="Masukkan NIS" required>
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="••••••••" required>
            </div>
            <button type="submit" name="login" class="submit-btn">Masuk Sekarang</button>
        </form>

        <form method="post" class="form" id="registerForm" autocomplete="off">
            <div class="input-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama" placeholder="Nama Anda" required>
            </div>
            <div class="input-group">
                <label>Kelas</label>
                <input type="text" name="kelas" placeholder="Contoh: XII RPL 1" required>
            </div>
            <div class="input-group">
                <label>Username / NIS</label>
                <input type="text" name="username_reg" placeholder="NIS untuk login" required>
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password_reg" placeholder="Buat password" required>
            </div>
            <button type="submit" name="register" class="submit-btn">Daftar Akun</button>
        </form>
    </div>
</div>

<script>
    const btnLogin = document.getElementById("btnLogin");
    const btnRegister = document.getElementById("btnRegister");
    const loginForm = document.getElementById("loginForm");
    const registerForm = document.getElementById("registerForm");
    const title = document.getElementById("title");

    btnLogin.onclick = () => {
        loginForm.classList.add("active");
        registerForm.classList.remove("active");
        btnLogin.classList.add("active");
        btnRegister.classList.remove("active");
        title.innerText = "Login";
    };

    btnRegister.onclick = () => {
        registerForm.classList.add("active");
        loginForm.classList.remove("active");
        btnRegister.classList.add("active");
        btnLogin.classList.remove("active");
        title.innerText = "Register";
    };
</script>

</body>
</html>
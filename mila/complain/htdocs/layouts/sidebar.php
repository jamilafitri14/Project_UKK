<style>
.sidebar {
    width: 220px;
    height: 100vh;
    position: fixed;
    background: linear-gradient(180deg, #c084fc, #a78bfa);
    padding: 20px;
    color: white;
}

.sidebar h2 {
    text-align: center;
    margin-bottom: 30px;
}

.sidebar a {
    display: block;
    padding: 10px;
    margin-bottom: 10px;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    transition: 0.3s;
}

.sidebar a:hover {
    background: rgba(255,255,255,0.2);
}
</style>

<div class="sidebar">
    <h2>Menu</h2>

    <a href="dashboard.php">Dashboard</a>
    <a href="laporan.php">Laporan</a>
    <a href="aspirasi.php">Aspirasi</a>
    <a href="logout.php">Logout</a>
</div>
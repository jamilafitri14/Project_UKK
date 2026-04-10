<?php
require 'functions.php';

$id = $_GET['id'];

// ubah status jadi dibatalkan
query("UPDATE aspirasi SET status='dibatalkan' WHERE id='$id'");

echo "
<script>
    alert('Laporan berhasil dibatalkan');
    document.location.href='riwayat.php';
</script>
";
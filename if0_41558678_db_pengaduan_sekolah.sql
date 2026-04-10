-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql208.infinityfree.com
-- Waktu pembuatan: 10 Apr 2026 pada 02.30
-- Versi server: 11.4.10-MariaDB
-- Versi PHP: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_41558678_db_pengaduan_sekolah`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_petugas` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password`, `nama_petugas`) VALUES
(1, 'admin', '123', 'admin ');

-- --------------------------------------------------------

--
-- Struktur dari tabel `aspirasi`
--

CREATE TABLE `aspirasi` (
  `id_aspirasi` int(11) NOT NULL,
  `nis` char(10) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `lokasi` varchar(50) NOT NULL,
  `keterangan` text NOT NULL,
  `foto` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('menunggu','proses','selesai') NOT NULL DEFAULT 'menunggu',
  `feedback` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `aspirasi`
--

INSERT INTO `aspirasi` (`id_aspirasi`, `nis`, `id_kategori`, `lokasi`, `keterangan`, `foto`, `tanggal`, `status`, `feedback`) VALUES
(1, '2', 2, '11 RPL', 'Panas', 'stock-photo-monkey-d-luffy-of-one-piece-anime-2513202033.jpg', '2026-03-31', 'selesai', 'sudah diselesaikan yaa'),
(2, '2', 4, 'LAB TIK', 'panas', 'stock-photo-monkey-d-luffy-of-one-piece-anime-2513202033.jpg', '2026-03-31', 'selesai', 'udah'),
(5, '2001', 4, 'bhakti insani', 'labnya panas dingin', 'Screenshot (60).png', '2026-03-31', 'menunggu', ''),
(6, '1212', 3, 'toilet', 'sabun dicolong', 'Screenshot (47).png', '2026-03-31', 'proses', 'apsi emi g suka'),
(7, '005', 2, '12 TKJ', 'bau', 'Screenshot (38).png', '2026-03-31', 'menunggu', ''),
(8, '0006', 1, 'KAMAR MANDI LAKI LAKI', 'jelek, gaadil dibanding kamar mandi cewe', 'Screenshot (41).png', '2026-03-31', 'menunggu', ''),
(9, '0006', 5, 'perpustakaan', 'tempat bacanya ga nyaman', 'Screenshot (59).png', '2026-03-31', 'proses', 'nanti kita survei langsung ya'),
(10, '10010', 5, 'perpus bi', 'novelnya dikitttt', 'Screenshot (41).png', '2026-03-31', 'proses', 'gausah komplen atau gw blok.'),
(11, '0006', 2, 'semua kelas', 'prabowo', 'WhatsApp Image 2026-02-09 at 12.55.57.jpeg', '2026-03-31', 'menunggu', ''),
(12, '1', 3, 'mushola', 'ada anomali', 'WhatsApp Image 2026-03-31 at 14.50.56.jpeg', '2026-04-02', 'proses', 'wah kita cari dulu ya\r\n'),
(13, '1', 2, 'Kelas MP', 'Kelas tidak lengkap foto presiden dan Garuda ', 'Screenshot_20260331_065107_YouTube.jpg', '2026-04-01', 'menunggu', ''),
(14, '12', 2, 'Kelas MP', 'Kurang lengkap foto presiden dan burung garuda.', 'Screenshot_20260331_065107_YouTube.jpg', '2026-04-01', 'selesai', 'Sudah'),
(15, '12', 5, 'Perpus ', 'Buku novel tambah lagi', 'IMG-20260401-WA0005.jpeg', '2026-04-01', 'menunggu', ''),
(16, '2324', 4, 'lab', 'bau endog', '3d4d1df88411ce4bd012a6224f970dee.jpg', '2026-04-02', 'menunggu', ''),
(17, '232410001', 2, 'Kelas 12 PPLG', 'Ada adit', '1000239008.jpg', '2026-04-02', 'proses', 'usir aja si adit '),
(18, '22230030', 4, 'Lab pplg', 'Lantai kotor abis makan mbg', '1000239008.jpg', '2026-04-02', 'menunggu', ''),
(19, '1234', 1, '12 pplg', 'Pada kotor', '', '2026-04-02', 'menunggu', ''),
(20, '1', 3, 'Lab', 'Orang berbahaya karena terlalu imut', 'IMG_20260402_073459_164.jpg', '2026-04-02', 'menunggu', ''),
(21, '0909', 3, 'kelas 12 rpl', 'bau', '', '2026-04-02', 'menunggu', ''),
(23, '2324.10005', 2, 'kelas12rpl', 'ada hp ketinggalan', 'stock-photo-monkey-d-luffy-of-one-piece-anime-2513202033.jpg', '2026-04-07', 'selesai', 'okeee');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'kebersihan'),
(2, 'kelas'),
(3, 'keamanan'),
(4, 'laboratorium'),
(5, 'perpustakaan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `siswa`
--

CREATE TABLE `siswa` (
  `nis` char(10) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `kelas` varchar(10) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data untuk tabel `siswa`
--

INSERT INTO `siswa` (`nis`, `nama`, `kelas`, `password`) VALUES
('0006', 'UCUP CEO', 'XIII RPL', 'ucup'),
('005', 'angellia jamilah ', 'XII RPL', 'FURAP'),
('0909', 'Nadia', 'XII RPL', 'nadia1'),
('1', 'clar', 'XII RPL', 'furabtajur'),
('10010', 'caca', 'XIII RPL', 'ccc'),
('12', 'Ka Aldi', 'Lab PPLG', '1212'),
('1212', 'pai', '9 Z', '444'),
('1234', 'salsa caca', '12 pplg', 'caca'),
('2', 'enji', 'XII RPL', '12345'),
('2001', 'prikitiw', 'XII TKJT', 'pri123'),
('22230030', 'Muhammad adziqri', 'XIII PPLG', '192837465'),
('2324', 'tes', 'XII TKJ', '123'),
('2324.10005', 'Aldi Ferdiansyah', 'XII-Rpl', '25'),
('2324.20019', 'ikhsan', 'XII PM', '1919'),
('232410001', 'Ahmad Zacki', 'XII RPL', 'zaki123'),
('Salman', 'Salman rustandi', 'Xll RPL', 'Salman'),
('salsa cant', 'Salsa Nuruli Putri', 'XII RPL', 'caca');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indeks untuk tabel `aspirasi`
--
ALTER TABLE `aspirasi`
  ADD PRIMARY KEY (`id_aspirasi`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indeks untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`nis`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `aspirasi`
--
ALTER TABLE `aspirasi`
  MODIFY `id_aspirasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

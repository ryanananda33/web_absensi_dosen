-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 22 Bulan Mei 2026 pada 11.35
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `absensi_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `matakuliah` varchar(100) NOT NULL,
  `keterangan` enum('Hadir','Izin','Sakit','Alfa','Terlambat') NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `device_id` varchar(255) DEFAULT NULL,
  `waktu_absen` timestamp NOT NULL DEFAULT current_timestamp(),
  `tanggal` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jadwal`
--

CREATE TABLE `jadwal` (
  `id` int(11) NOT NULL,
  `matakuliah` varchar(100) DEFAULT NULL,
  `kelas` varchar(50) DEFAULT NULL,
  `jurusan` varchar(100) DEFAULT NULL,
  `angkatan` varchar(10) DEFAULT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu') DEFAULT NULL,
  `jam_mulai` time DEFAULT NULL,
  `ruangan` varchar(50) DEFAULT NULL,
  `tipe` enum('Teori','Praktikum') DEFAULT 'Teori'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `jadwal`
--

INSERT INTO `jadwal` (`id`, `matakuliah`, `kelas`, `jurusan`, `angkatan`, `hari`, `jam_mulai`, `ruangan`, `tipe`) VALUES
(1, 'Pemrograman Web', NULL, NULL, NULL, 'Senin', '08:00:00', 'Lab Komputer 1', 'Praktikum'),
(2, 'Basis Data', NULL, NULL, NULL, 'Selasa', '10:30:00', 'Ruang 3.2', 'Teori'),
(3, 'Mobile App Development', NULL, NULL, NULL, 'Rabu', '13:00:00', 'Lab Komputer 2', 'Praktikum');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nim_nik` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `jurusan` varchar(100) DEFAULT NULL,
  `kelas` varchar(50) DEFAULT NULL,
  `angkatan` varchar(10) DEFAULT NULL,
  `semester` varchar(20) DEFAULT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `device_id` varchar(255) NOT NULL,
  `doc_type` varchar(50) DEFAULT NULL,
  `foto_ktm` varchar(255) DEFAULT NULL,
  `foto_selfie` varchar(255) DEFAULT NULL,
  `status_akun` enum('pending','aktif') DEFAULT 'aktif',
  `role` enum('mahasiswa','dosen') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nim_nik`, `nama`, `gender`, `jurusan`, `kelas`, `angkatan`, `semester`, `tempat_lahir`, `tanggal_lahir`, `password`, `device_id`, `doc_type`, `foto_ktm`, `foto_selfie`, `status_akun`, `role`) VALUES
(1, '20240810002', 'Nabil', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '', NULL, NULL, NULL, 'aktif', 'mahasiswa');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attendance` (`user_id`,`matakuliah`,`tanggal`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_waktu` (`waktu_absen`);

--
-- Indeks untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nim_nik` (`nim_nik`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `fk_user_absensi` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

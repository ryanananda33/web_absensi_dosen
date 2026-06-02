-- Migration: Add kelas, jurusan, angkatan to jadwal table
-- Run this SQL once or use migrations/run_migrations.php

ALTER TABLE `jadwal`
  ADD COLUMN `kelas` VARCHAR(50) DEFAULT NULL AFTER `matakuliah`,
  ADD COLUMN `jurusan` VARCHAR(100) DEFAULT NULL AFTER `kelas`,
  ADD COLUMN `angkatan` VARCHAR(10) DEFAULT NULL AFTER `jurusan`;

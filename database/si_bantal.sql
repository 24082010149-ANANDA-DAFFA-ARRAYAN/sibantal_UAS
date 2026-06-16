-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 23, 2026 at 12:47 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `si_bantal`
--

-- --------------------------------------------------------

--
-- Table structure for table `history_penyaluran`
--

CREATE TABLE `history_penyaluran` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `program_id` int(11) NOT NULL,
  `tipe_program` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `history_penyaluran`
--

INSERT INTO `history_penyaluran` (`id`, `user_id`, `program_id`, `tipe_program`, `created_at`) VALUES
(8, 10, 11, 'permintaan', '2026-04-17 07:02:24'),
(9, 10, 12, 'permintaan', '2026-04-18 09:51:53'),
(10, 9, 9, 'penawaran', '2026-04-18 09:52:44');

-- --------------------------------------------------------

--
-- Table structure for table `penawaran_bantuan`
--

CREATE TABLE `penawaran_bantuan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_instansi` varchar(100) NOT NULL,
  `pj_donatur` varchar(100) NOT NULL,
  `jabatan_donatur` varchar(100) NOT NULL,
  `kontak_donatur` varchar(20) NOT NULL,
  `jenis_penawaran` varchar(50) NOT NULL,
  `detail_bantuan` text NOT NULL,
  `dokumen_donatur` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `is_funded` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penawaran_bantuan`
--

INSERT INTO `penawaran_bantuan` (`id`, `user_id`, `nama_instansi`, `pj_donatur`, `jabatan_donatur`, `kontak_donatur`, `jenis_penawaran`, `detail_bantuan`, `dokumen_donatur`, `status`, `is_funded`, `created_at`) VALUES
(9, 10, 'PT Samudra Hindia', 'Arrayan ', 'CSR Chief', '081352931980', 'Sembako', 'oijsaoijdoiwajdoiajdojwadoiajdiaw', '1776505876_WhatsApp Image 2026-03-31 at 19.29.39.jpeg', 'approved', 1, '2026-04-18 09:51:16');

-- --------------------------------------------------------

--
-- Table structure for table `permintaan_bantuan`
--

CREATE TABLE `permintaan_bantuan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_pj` varchar(100) NOT NULL,
  `jabatan` varchar(100) NOT NULL,
  `target_bantuan` enum('warga','fasilitas') NOT NULL,
  `jumlah_kk` int(11) DEFAULT NULL,
  `provinsi` varchar(50) NOT NULL,
  `kota` varchar(50) NOT NULL,
  `kecamatan` varchar(50) NOT NULL,
  `desa` varchar(50) NOT NULL,
  `alasan` text NOT NULL,
  `dokumen_desa` varchar(255) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `is_funded` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `permintaan_bantuan`
--

INSERT INTO `permintaan_bantuan` (`id`, `user_id`, `nama_pj`, `jabatan`, `target_bantuan`, `jumlah_kk`, `provinsi`, `kota`, `kecamatan`, `desa`, `alasan`, `dokumen_desa`, `status`, `is_funded`, `created_at`) VALUES
(11, 9, 'Daffa', 'RT', 'warga', 49, 'Jawa Timur', 'Surabaya', 'Rungkut', 'Desa Gununganyar', 'permintaan bantuan untuk warga terdampak', '1776409221_desa_Kelompok 1_STATKOM.pdf', 'approved', 1, '2026-04-17 07:00:21'),
(12, 9, 'Daffa', 'RT', 'warga', 50, 'Jawa Timur', 'Surabaya', 'Waru', 'Desa Gununganyar', 'KWKWKWKWWKWKWKWKWKWKWKW', '1776497612_desa_Register.png', 'approved', 1, '2026-04-18 07:33:32');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','desa','donatur') NOT NULL,
  `asal_desa` varchar(100) DEFAULT NULL,
  `nama_organisasi` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama_lengkap`, `email`, `password`, `role`, `asal_desa`, `nama_organisasi`, `created_at`) VALUES
(8, 'Administrator', 'admin@sibantal.com', '$2y$10$o9GZwwIShxpxq4KnupjEg.anApQ8Hx5T.q47jOR.6pgZfBHAvcLIu', 'admin', NULL, NULL, '2026-04-16 16:29:40'),
(9, 'Daffa', 'anandadaffaarrayan@gmail.com', '$2y$10$QY8kxfI7O264PpqocmzNzeF3TTUlCZNAmALjVeW4mm8crjGNkO/lO', 'desa', 'Desa Gununganyar', NULL, '2026-04-17 06:15:38'),
(10, 'Arrayan ', 'ptanandadaffaarrayan@gmail.com', '$2y$10$/qXuNTjOYUjq4qEDPScbhO1HagGOKw2upw6jdry7E9BpXWIZ60WIa', 'donatur', NULL, 'PT Samudra Hindia', '2026-04-17 06:15:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `history_penyaluran`
--
ALTER TABLE `history_penyaluran`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `penawaran_bantuan`
--
ALTER TABLE `penawaran_bantuan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `permintaan_bantuan`
--
ALTER TABLE `permintaan_bantuan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `history_penyaluran`
--
ALTER TABLE `history_penyaluran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `penawaran_bantuan`
--
ALTER TABLE `penawaran_bantuan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `permintaan_bantuan`
--
ALTER TABLE `permintaan_bantuan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `penawaran_bantuan`
--
ALTER TABLE `penawaran_bantuan`
  ADD CONSTRAINT `penawaran_bantuan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `permintaan_bantuan`
--
ALTER TABLE `permintaan_bantuan`
  ADD CONSTRAINT `permintaan_bantuan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

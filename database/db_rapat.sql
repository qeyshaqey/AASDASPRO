-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 10, 2025 at 04:43 PM
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
-- Database: `db_rapat`
--

-- --------------------------------------------------------

--
-- Table structure for table `agendas`
--

CREATE TABLE `agendas` (
  `id` int(11) NOT NULL,
  `judul_rapat` varchar(255) NOT NULL,
  `jurusan` varchar(100) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL,
  `lokasi` varchar(255) NOT NULL,
  `tipe_tempat` enum('offline','online') DEFAULT 'offline',
  `tempat_detail` text NOT NULL,
  `host` varchar(100) NOT NULL,
  `status` enum('akan datang','berlangsung','selesai') DEFAULT 'akan datang',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agendas`
--

INSERT INTO `agendas` (`id`, `judul_rapat`, `jurusan`, `tanggal`, `waktu`, `lokasi`, `tipe_tempat`, `tempat_detail`, `host`, `status`, `created_at`) VALUES
(90, 'Rapat AAS PBL', 'Teknik Informatika', '2025-12-24', '08:00:00', 'https://discord.com/channels/1431309060552261829/1431309061743448155', 'online', '', 'Swono Sibagariang', 'akan datang', '2025-12-10 13:16:01'),
(91, 'Rapat ATS PBL', 'Teknik Informatika', '2025-12-10', '20:20:00', 'GU 706', 'offline', '', 'Swono Sibagariang', 'selesai', '2025-12-10 13:18:16');

-- --------------------------------------------------------

--
-- Table structure for table `agenda_participants`
--

CREATE TABLE `agenda_participants` (
  `id` int(11) NOT NULL,
  `agenda_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status_kehadiran` enum('hadir','tidak_hadir','belum_dikonfirmasi') NOT NULL DEFAULT 'belum_dikonfirmasi',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agenda_participants`
--

INSERT INTO `agenda_participants` (`id`, `agenda_id`, `user_id`, `status_kehadiran`, `created_at`, `updated_at`) VALUES
(25, 90, 7, 'belum_dikonfirmasi', '2025-12-10 13:16:01', '2025-12-10 13:16:01'),
(26, 90, 8, 'belum_dikonfirmasi', '2025-12-10 13:16:01', '2025-12-10 13:16:01'),
(27, 90, 9, 'belum_dikonfirmasi', '2025-12-10 13:16:01', '2025-12-10 13:16:01'),
(28, 91, 7, 'hadir', '2025-12-10 13:18:16', '2025-12-10 13:21:31'),
(29, 91, 8, 'hadir', '2025-12-10 13:18:16', '2025-12-10 13:21:31'),
(30, 91, 9, 'hadir', '2025-12-10 13:18:16', '2025-12-10 13:21:31');

-- --------------------------------------------------------

--
-- Table structure for table `agenda_peserta`
--

CREATE TABLE `agenda_peserta` (
  `id` int(11) NOT NULL,
  `agenda_id` int(11) NOT NULL,
  `peserta_id` int(11) NOT NULL,
  `nama_peserta` varchar(100) NOT NULL,
  `status_kehadiran` enum('belum_dikirim','hadir','tidak_hadir') DEFAULT 'belum_dikirim'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `success` tinyint(1) NOT NULL,
  `attempt_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `username`, `ip_address`, `success`, `attempt_time`) VALUES
(1, 'nabila149', '::1', 1, '2025-12-01 08:36:07'),
(2, 'nabila149', '::1', 1, '2025-12-01 15:30:01'),
(3, 'nabila149', '::1', 1, '2025-12-01 15:44:29'),
(4, 'nabila149', '::1', 1, '2025-12-02 01:58:48'),
(5, 'nabila149', '::1', 1, '2025-12-02 06:51:24'),
(6, 'nabila149', '::1', 1, '2025-12-08 01:04:47'),
(7, 'nabila149', '::1', 1, '2025-12-08 01:08:49'),
(8, 'nabila149', '::1', 1, '2025-12-08 01:29:07'),
(9, 'nabila149', '::1', 1, '2025-12-08 01:33:59'),
(10, 'nabila149', '::1', 1, '2025-12-08 01:59:22'),
(11, 'nabila149', '::1', 1, '2025-12-08 02:00:34'),
(12, 'nabila149', '::1', 1, '2025-12-08 02:01:31'),
(13, 'nabila149', '::1', 1, '2025-12-08 02:02:51'),
(14, 'nabila149', '::1', 1, '2025-12-08 02:04:25'),
(15, 'nabila149', '::1', 1, '2025-12-08 02:13:02'),
(16, 'nabila149', '::1', 1, '2025-12-08 02:25:22'),
(17, 'nabila149', '::1', 1, '2025-12-08 02:58:12'),
(18, 'nabila149', '::1', 1, '2025-12-08 03:13:52'),
(19, 'nabila149', '::1', 1, '2025-12-08 07:20:58'),
(20, 'nabila149', '::1', 1, '2025-12-08 09:00:42'),
(21, 'nabila149', '::1', 1, '2025-12-08 11:53:33'),
(22, 'nabila149', '::1', 0, '2025-12-09 02:43:54'),
(23, 'nabila123', '::1', 0, '2025-12-09 02:44:05'),
(24, 'nabila123', '::1', 1, '2025-12-09 02:44:57'),
(25, 'nabila123', '::1', 1, '2025-12-09 02:50:51'),
(26, 'nabila123', '::1', 1, '2025-12-09 02:53:07');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expiry` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `email`, `token`, `expiry`, `used`, `created_at`) VALUES
(1, 7, 'nabilafatin131@gmail.com', '4bfe49f7e8ff163fd465644701ff00cbac1920267a1ac7dba83c52ddfe775d5b', '2025-12-10 15:29:16', 0, '2025-12-10 13:29:16'),
(2, 7, 'nabilafatin131@gmail.com', 'f79da8e36f0abe02d07eab3c0f4180c4d9d3c1b425d05244b73e9c0758aaacc1', '2025-12-10 15:29:42', 0, '2025-12-10 13:29:42'),
(3, 7, 'nabilafatin131@gmail.com', '51547e4f682f504a0d782e8059c0a6d15d8f8403edb305dce4537ea5be5d7492', '2025-12-10 15:40:24', 0, '2025-12-10 13:40:24'),
(4, 7, 'nabilafatin131@gmail.com', '7eafb43d1aabed053b85fb066914b2387d9aa0a8ce14282f73664f3b4fc258be', '2025-12-10 15:40:35', 0, '2025-12-10 13:40:35'),
(5, 7, 'nabilafatin131@gmail.com', '7aa266e41fd45ca8061a302b2b56fc7a13fe65c9fb9696fe2c9846607ab02721', '2025-12-10 15:48:47', 0, '2025-12-10 13:48:47'),
(6, 7, 'nabilafatin131@gmail.com', 'd675c35f624f4b514229561ef38e579358042943e924c5b30f9f127973cae1c8', '2025-12-10 15:50:26', 0, '2025-12-10 13:50:26'),
(7, 7, 'nabilafatin131@gmail.com', '8a9b0939e2f8a89d355259bcb8fae1e413c847e8174747befb77a2924f4bd77b', '2025-12-10 15:51:19', 0, '2025-12-10 13:51:19'),
(8, 7, 'nabilafatin131@gmail.com', '6b98711350ac63e6a0c8c15c448393934300e3ea594d39186b466d1a07fb720b', '2025-12-10 15:51:51', 0, '2025-12-10 13:51:51'),
(9, 7, 'nabilafatin131@gmail.com', '8fa2f4a86ec2db20219fb626c0b65567646d689ec50f8664d03a073eb9ffeb8a', '2025-12-10 15:52:15', 0, '2025-12-10 13:52:15'),
(10, 7, 'nabilafatin131@gmail.com', '1ffd064404ddd72fa9c743ca26d99f6218bd6edb5f03bc18c057d0d35037dd7d', '2025-12-10 15:56:24', 0, '2025-12-10 13:56:24'),
(11, 7, 'nabilafatin131@gmail.com', '7d56d0e06d4ca7aea6844d3819fb30876ad2621033a25754382bca1b52fe89de', '2025-12-10 15:58:55', 0, '2025-12-10 13:58:55'),
(12, 7, 'nabilafatin131@gmail.com', '0d4a94a5c81593d856a285afa1c7f600cb19433f013f76c1169e31cf822e91b5', '2025-12-10 16:03:11', 0, '2025-12-10 14:03:11'),
(13, 7, 'nabilafatin131@gmail.com', '0b63d214d3723d11a0d295794aa26b2bf116975b64b65a6c500da608f03a72b4', '2025-12-11 15:04:54', 0, '2025-12-10 14:04:54'),
(14, 7, 'nabilafatin131@gmail.com', 'b5e9f92be417f96e62cc13084145c7d9858cceea2290a2fa3ac40a7eb4457010', '2025-12-11 15:05:04', 0, '2025-12-10 14:05:04');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `jurusan` varchar(100) DEFAULT NULL,
  `prodi` varchar(100) DEFAULT NULL,
  `foto` varchar(255) DEFAULT 'default.png',
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `jurusan`, `prodi`, `foto`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', NULL, NULL, 'default.png', 'admin', '2025-11-28 10:04:47', '2025-11-28 10:04:47'),
(7, 'nabilaaa09', 'nabilafatin131@gmail.com', '$2y$10$rC9GX5hzZw7kpgZQKbk3keNn4a19kozX8L7gHIL/2AK0M4H5tC/s6', 'Nabila Fatin', 'Teknik Informatika', 'Teknik Informatika', '1765335470_webcam-toy-photo4.jpg', 'user', '2025-12-09 02:44:41', '2025-12-10 12:16:38'),
(8, 'qeyshaa', 'qeysha@gmail.com', '$2y$10$tn80FFAhkeJUf1crCBLV1uDMV2nIYWU16t0ucv.WYjIkLe2.WvYgC', 'Qeysha Nadine Handoko', 'Teknik Informatika', 'Teknik Informatika', '1765337206_WhatsApp Image 2025-10-18 at 21.43.04.jpeg', 'user', '2025-12-10 03:25:32', '2025-12-10 03:26:46'),
(9, 'rizkaa', 'rizkanur@gmail.com', '$2y$10$ozkIWCCmSsPJm9mtmwYg.uj26UJqPHOmD0cZ3/2aHAzXU6DqGwQkW', 'Rizka Nur Azizah', 'Teknik Informatika', 'Teknik Informatika', 'default.png', 'user', '2025-12-10 08:12:25', '2025-12-10 08:12:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agendas`
--
ALTER TABLE `agendas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `agenda_participants`
--
ALTER TABLE `agenda_participants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_agenda_user` (`agenda_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `agenda_peserta`
--
ALTER TABLE `agenda_peserta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `agenda_id` (`agenda_id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agendas`
--
ALTER TABLE `agendas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `agenda_participants`
--
ALTER TABLE `agenda_participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `agenda_peserta`
--
ALTER TABLE `agenda_peserta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=182;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `agenda_participants`
--
ALTER TABLE `agenda_participants`
  ADD CONSTRAINT `agenda_participants_ibfk_1` FOREIGN KEY (`agenda_id`) REFERENCES `agendas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `agenda_participants_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `agenda_peserta`
--
ALTER TABLE `agenda_peserta`
  ADD CONSTRAINT `agenda_peserta_ibfk_1` FOREIGN KEY (`agenda_id`) REFERENCES `agendas` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 17, 2025 at 07:43 AM
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
-- Database: `pdfsimba_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password_hash`, `created_at`, `updated_at`) VALUES
(1, 'PDF Simba', 'systempdfsimba@gmail.com', '$2y$10$NrGMj686ArLD5h1FXnjdOOb.1fEc0jjq80EhB/k3USLrUGKl4nYOe', '2025-08-17 02:35:27', '2025-08-17 03:51:01'),
(2, 'Gaudensia Otara', 'otaragaudensia@gmail.com', '$2y$10$9PrLo6qLEyjI9RiGbk8vi.Xnt6vmonURa9tZXlksNtPROoOGroIN.', '2025-08-17 03:21:17', '2025-08-17 03:21:17');

-- --------------------------------------------------------

--
-- Table structure for table `failed_logins`
--

CREATE TABLE `failed_logins` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempt_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `failed_logins`
--

INSERT INTO `failed_logins` (`id`, `email`, `ip_address`, `attempt_time`) VALUES
(0, 'otaragaudensia@gmail.com', '::1', '2025-08-11 11:18:27'),
(0, 'otaragaudensia@gmail.com', '::1', '2025-08-11 13:11:35'),
(0, 'otaragaudensia@gmail.com', '::1', '2025-08-11 13:14:56'),
(0, 'otaragaudensia@gmail.com', '::1', '2025-08-11 13:15:04'),
(0, 'otaragaudensia@gmail.com', '::1', '2025-08-11 13:38:08'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-11 20:44:52'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-11 20:55:12'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-11 21:29:37'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-12 20:47:09'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-12 20:54:23'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-12 21:27:10'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-12 21:27:46'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-12 21:56:24'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-12 22:25:24'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-12 23:21:40'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-12 23:31:51'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-12 23:58:02'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 11:44:18'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 12:00:12'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 12:05:43'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 13:12:31'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 13:15:28'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 13:37:54'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 13:39:42'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 13:50:57'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 14:04:16'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 14:04:34'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 14:06:19'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 14:15:47'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 14:16:49'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 14:24:23'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 14:27:53'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 14:28:51'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 14:37:47'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 14:45:21'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 14:48:03'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 14:56:35'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:01:07'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:01:10'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:01:13'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:01:16'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:01:20'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:01:23'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:01:26'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:01:29'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:01:33'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:01:37'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:01:42'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:01:46'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:01:50'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:01:54'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:01:58'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:02:02'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:02:06'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:02:10'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:02:14'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:02:18'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:02:22'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:02:26'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:02:30'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:02:34'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:02:38'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:02:42'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:02:46'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:02:50'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:02:54'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:02:58'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:03:02'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:03:06'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:03:10'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:03:14'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:03:18'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:03:22'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:03:42'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:05:46'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:08:30'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:10:19'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:13:52'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:14:04'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-13 15:22:14'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-15 15:53:20'),
(0, 'gaudenciaotara@gmail.com', '::1', '2025-08-15 15:53:22'),
(0, 'seolyoona@gmail.com', '::1', '2025-08-15 15:55:11'),
(0, 'estherotara@gmail.com', '::1', '2025-08-15 15:55:28'),
(0, 'estherotara@gmail.com', '::1', '2025-08-15 20:21:06'),
(0, 'estherotara@gmail.com', '::1', '2025-08-15 20:21:08'),
(0, 'estherotara@gmail.com', '::1', '2025-08-15 21:01:35');

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`id`, `user_id`, `ip_address`, `login_time`) VALUES
(1, 6, '95.111.247.6', '2025-08-01 09:07:42'),
(2, 1, '95.111.247.6', '2025-08-01 09:35:49'),
(4, 1, '95.111.247.6', '2025-08-01 10:48:26'),
(5, 9, '95.111.247.6', '2025-08-01 11:01:29'),
(6, 9, '95.111.247.6', '2025-08-01 11:05:11'),
(7, 9, '95.111.247.6', '2025-08-01 11:13:01'),
(0, 0, '::1', '2025-08-11 13:11:28'),
(0, 1, '::1', '2025-08-11 18:52:10'),
(0, 1, '::1', '2025-08-11 18:56:14'),
(0, 1, '::1', '2025-08-11 19:36:51'),
(0, 1, '::1', '2025-08-11 19:43:06'),
(0, 0, '::1', '2025-08-11 19:50:53'),
(0, 1, '::1', '2025-08-11 20:57:52'),
(0, 1, '::1', '2025-08-11 20:57:53'),
(0, 1, '::1', '2025-08-11 21:33:40'),
(0, 1, '::1', '2025-08-11 22:09:52'),
(0, 1, '::1', '2025-08-11 22:13:49'),
(0, 1, '::1', '2025-08-12 20:13:16'),
(0, 1, '::1', '2025-08-12 20:58:25'),
(0, 1, '::1', '2025-08-12 21:28:02'),
(0, 1, '::1', '2025-08-12 21:56:48'),
(0, 1, '::1', '2025-08-12 22:25:42'),
(0, 1, '::1', '2025-08-12 23:22:26'),
(0, 1, '::1', '2025-08-12 23:32:01'),
(0, 1, '::1', '2025-08-12 23:58:09'),
(0, 1, '::1', '2025-08-12 23:58:17'),
(0, 1, '::1', '2025-08-12 23:58:30'),
(0, 1, '::1', '2025-08-12 23:58:33'),
(0, 1, '::1', '2025-08-13 11:44:28'),
(0, 1, '::1', '2025-08-13 12:00:26'),
(0, 1, '::1', '2025-08-13 12:05:54'),
(0, 1, '::1', '2025-08-13 12:18:00'),
(0, 1, '::1', '2025-08-13 12:25:13'),
(0, 1, '::1', '2025-08-13 13:12:56'),
(0, 1, '::1', '2025-08-13 13:15:47'),
(0, 1, '::1', '2025-08-13 13:17:48'),
(0, 1, '::1', '2025-08-13 13:39:58'),
(0, 1, '::1', '2025-08-13 13:51:31'),
(0, 1, '::1', '2025-08-13 15:14:16'),
(0, 1, '::1', '2025-08-13 15:22:29'),
(0, 1, '::1', '2025-08-14 23:08:49'),
(0, 1, '::1', '2025-08-15 01:20:50'),
(0, 0, '::1', '2025-08-15 12:43:08'),
(0, 0, '::1', '2025-08-15 12:46:59'),
(0, 0, '::1', '2025-08-15 13:21:41'),
(0, 0, '::1', '2025-08-15 13:32:08'),
(0, 0, '::1', '2025-08-15 13:42:13'),
(0, 0, '::1', '2025-08-15 13:53:20'),
(0, 0, '::1', '2025-08-15 14:08:29'),
(0, 0, '::1', '2025-08-15 15:03:49'),
(0, 0, '::1', '2025-08-15 15:27:44'),
(0, 0, '::1', '2025-08-15 15:53:46'),
(0, 1, '::1', '2025-08-15 15:55:42'),
(0, 1, '::1', '2025-08-15 20:21:19'),
(0, 1, '::1', '2025-08-15 20:26:08'),
(0, 1, '::1', '2025-08-15 20:31:11'),
(0, 1, '::1', '2025-08-15 20:50:34'),
(0, 1, '::1', '2025-08-15 21:01:42'),
(0, 1, '::1', '2025-08-15 21:12:26'),
(0, 1, '::1', '2025-08-15 21:20:36'),
(0, 1, '::1', '2025-08-16 22:58:45'),
(0, 1, '::1', '2025-08-17 02:10:47'),
(0, 1, '::1', '2025-08-17 03:07:51'),
(0, 1, '::1', '2025-08-17 03:22:35'),
(0, 1, '::1', '2025-08-17 03:41:14'),
(0, 1, '::1', '2025-08-17 03:43:06');

-- --------------------------------------------------------

--
-- Table structure for table `test_users`
--

CREATE TABLE `test_users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `verification_code` varchar(10) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `test_users`
--

INSERT INTO `test_users` (`id`, `first_name`, `last_name`, `email`, `password`, `verification_code`, `is_verified`, `created_at`, `updated_at`) VALUES
(2, 'Khloe', 'Otara', 'khloeotara@gmail.com', '$2y$10$YQ4rYM88VOMEL9EB1aANgOGWs4oPTw/p0BQDWDqBJP5qrV7kNJkGq', NULL, 1, '2025-07-31 22:18:45', '2025-07-31 22:19:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `verification_code` varchar(10) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `verification_code_expiry` datetime DEFAULT NULL,
  `temp_password` tinyint(1) DEFAULT 0 COMMENT '1 if user is using a temporary password'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password`, `created_at`, `verification_code`, `is_verified`, `updated_at`, `verification_code_expiry`, `temp_password`) VALUES
(1, 'Otara', 'Gaudensia', 'otaragaudensia@gmail.com', '$2y$10$Iei3Amv5jim9oaTFCwY1auOb58T18gXeZ8ovU/Wss3ZPpQHRib4pa', '2025-07-20 21:12:23', '674510', 1, '2025-08-17 03:16:15', '2025-08-01 12:28:16', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

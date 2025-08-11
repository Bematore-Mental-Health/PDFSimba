-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 11, 2025 at 11:35 AM
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
-- Table structure for table `failed_logins`
--

CREATE TABLE `failed_logins` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `attempt_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(7, 9, '95.111.247.6', '2025-08-01 11:13:01');

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
(1, 'Otara', 'Gaudensia', 'otaragaudensia@gmail.com', '$2y$10$Iei3Amv5jim9oaTFCwY1auOb58T18gXeZ8ovU/Wss3ZPpQHRib4pa', '2025-07-21 00:12:23', '674510', 1, '2025-08-01 09:28:16', '2025-08-01 12:28:16', 0),
(2, 'Seol', 'Yoona', 'seolyoona@gmail.com', '$2y$10$fPXw6whi46MTo.LPFybQQOgwcb1ScdjIXlZD9mi0R0MQAACA9AQxm', '2025-07-21 00:15:55', NULL, 1, '2025-07-31 22:39:33', NULL, 0),
(3, 'Nswer', 'Barbie', 'nswerbarbie@gmail.com', '$2y$10$/JFdDC48mzhbggSD7.OWHu4hT/Xau1tbZ/dxSG2.evzGvGh4sWtfm', '2025-07-21 02:36:20', NULL, 1, '2025-07-31 22:39:33', NULL, 0),
(4, 'Albert', 'Owuor', 'owuoralbert83@gmail.com', '$2y$10$.h6.Qy.Vv6c55ZpHjqcHBew4R8K8f1rwO5Nz24R2a5H3XvoDf/l26', '2025-07-23 06:45:55', NULL, 1, '2025-07-31 22:39:33', NULL, 0),
(6, 'Khloe', 'Otara', 'khloeotara@gmail.com', '$2y$10$ZMWh5ku/2rG6Njeu4zFcIeivr1rxmlrYX7YzSxucm9Y2f6j.Uc/9q', '2025-08-01 08:25:07', NULL, 1, '2025-08-01 09:07:41', NULL, 1),
(9, 'Gaude', 'Otara', 'kimaniashley55@gmail.com', '$2y$10$hX2UAjzMkp8P9G4yBTpADOkIpRnSAUPgYOJBw82rEU9/cbzEJx/X6', '2025-08-01 10:04:06', NULL, 1, '2025-08-01 11:12:31', NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `failed_logins`
--
ALTER TABLE `failed_logins`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`),
  ADD KEY `attempt_time` (`attempt_time`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `test_users`
--
ALTER TABLE `test_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

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
-- AUTO_INCREMENT for table `failed_logins`
--
ALTER TABLE `failed_logins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `test_users`
--
ALTER TABLE `test_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD CONSTRAINT `login_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

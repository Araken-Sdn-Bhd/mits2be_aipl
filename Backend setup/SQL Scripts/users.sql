-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:2022
-- Generation Time: May 28, 2022 at 06:13 AM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mits`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Ranjan Gupta', 'rgupta291187@gmail.com', NULL, '$2y$10$6e912amt0Coswz8LTkk4ZOa2Xl.vK4H00X1KUMmcvaODeOTn7aYWq', 'Admin', NULL, '2022-02-27 12:17:38', '2022-02-27 12:17:38'),
(2, 'Ranjan Gupta', 'rg29111987@gmail.com', NULL, '$2y$10$BJ8GpQke0OYaP/zawYEfEukU.Vo5xhKH8WqZR5k4nCnGt6ArtyhHG', 'Staff', NULL, '2022-02-28 04:05:23', '2022-02-28 04:05:23'),
(3, 'Test User', 'test@gmail.com', NULL, '$2y$10$r6I.081bkfwSzyLqmSMsJOILaYw4zyeUe3LgG4CniGQvc21zqoDne', 'Admin', NULL, NULL, NULL),
(4, 'Staff', 'staff@gmail.com', NULL, '$2y$10$r6I.081bkfwSzyLqmSMsJOILaYw4zyeUe3LgG4CniGQvc21zqoDne', 'Staff', NULL, NULL, NULL),
(5, 'Employer', 'emp@gmail.com', NULL, '$2y$10$r6I.081bkfwSzyLqmSMsJOILaYw4zyeUe3LgG4CniGQvc21zqoDne', 'emp', NULL, NULL, NULL),
(6, 'Intervention', 'intervention@gmail.com', NULL, '$2y$10$r6I.081bkfwSzyLqmSMsJOILaYw4zyeUe3LgG4CniGQvc21zqoDne', 'Intervention', NULL, NULL, NULL),
(7, 'Von', 'von@gmail.com', NULL, '$2y$10$r6I.081bkfwSzyLqmSMsJOILaYw4zyeUe3LgG4CniGQvc21zqoDne', 'von', NULL, NULL, NULL),
(8, 'Patient', 'patient@gmail.com', NULL, '$2y$10$r6I.081bkfwSzyLqmSMsJOILaYw4zyeUe3LgG4CniGQvc21zqoDne', 'patient', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `role` (`role`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

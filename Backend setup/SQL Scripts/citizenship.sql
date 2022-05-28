-- phpMyAdmin SQL Dump
-- version 5.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:2022
-- Generation Time: May 28, 2022 at 08:37 AM
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
-- Table structure for table `citizenship`
--

CREATE TABLE `citizenship` (
  `id` int(10) UNSIGNED NOT NULL,
  `citizenship_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `citizenship_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `citizenship_status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `citizenship`
--

INSERT INTO `citizenship` (`id`, `citizenship_name`, `citizenship_code`, `citizenship_status`, `created_at`, `updated_at`) VALUES
(2, 'Malaysian', NULL, '1', NULL, NULL),
(3, 'Permanent Resident', NULL, '1', NULL, NULL),
(4, 'Foreigner', NULL, '1', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `citizenship`
--
ALTER TABLE `citizenship`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `citizenship_citizenship_name_unique` (`citizenship_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `citizenship`
--
ALTER TABLE `citizenship`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

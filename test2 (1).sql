-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 17, 2025 at 09:15 PM
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
-- Database: `test2`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(100) NOT NULL,
  `event_name` varchar(255) NOT NULL,
  `event_description` text DEFAULT NULL,
  `event_date` date NOT NULL,
  `event_end_date` date DEFAULT NULL,
  `event_location` varchar(100) DEFAULT NULL,
  `event_category` varchar(50) DEFAULT NULL,
  `event_poster` varchar(255) DEFAULT NULL,
  `event_status` enum('draft','published') DEFAULT 'draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `event_name`, `event_description`, `event_date`, `event_end_date`, `event_location`, `event_category`, `event_poster`, `event_status`, `created_at`, `created_by`) VALUES
(1, 'Career Kickstart', 'urgent', '2025-12-18', '2025-12-15', 'gedung Q', 'Open Recruitment', 'event_1765976800.png', 'published', '2025-12-17 14:40:56', NULL),
(2, 'Capital', 'DJAHDQJEAO', '2025-12-20', '2025-12-22', 'gedung Q', 'Seminar', 'event_1765981854.png', 'published', '2025-12-17 14:30:54', NULL),
(3, 'Renang', 'adsfsgd', '2025-12-18', '2025-12-23', 'gedung Q', 'Lomba', NULL, 'published', '2025-12-17 16:42:00', NULL),
(4, 'BalapKarung', 'gaega', '2025-12-18', '2025-12-31', 'Kolam Ikan', 'Lomba', 'default_event.png', 'published', '2025-12-17 18:28:02', 9);

-- --------------------------------------------------------

--
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `position_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `position_name` varchar(100) NOT NULL,
  `quota` int(11) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`position_id`, `event_id`, `position_name`, `quota`, `description`, `created_at`) VALUES
(1, 1, 'Divisi Acara', 5, 'p', '2025-12-17 13:10:20'),
(2, 3, 'Divisi Sponsor', 10, 'asf', '2025-12-17 16:42:47'),
(3, 4, 'Acara', 5, NULL, '2025-12-17 18:36:24'),
(4, 4, 'Perlengkapan', 5, NULL, '2025-12-17 18:36:31'),
(5, 4, 'Keamanan', 5, NULL, '2025-12-17 18:36:38');

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `registration_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  `position_id_2` int(11) DEFAULT NULL,
  `motivation` text DEFAULT NULL,
  `experience` text DEFAULT NULL,
  `cv_file` varchar(255) DEFAULT NULL,
  `portfolio_file` varchar(255) DEFAULT NULL,
  `interview_time` datetime DEFAULT NULL,
  `meet_link` varchar(255) DEFAULT NULL,
  `status` enum('pending','accepted','declined') DEFAULT 'pending',
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`registration_id`, `user_id`, `position_id`, `position_id_2`, `motivation`, `experience`, `cv_file`, `portfolio_file`, `interview_time`, `meet_link`, `status`, `registered_at`) VALUES
(2, 7, 1, 1, 'adesgfdghgawrr', 'wadefsgrdgwaefsd', 'CV_1765988888_7.pdf', '', '2025-12-19 10:00:00', 'https://meet.google.com/xyz-7474091d', 'pending', '2025-12-17 16:28:08'),
(3, 7, 2, 2, 'wefrgt', 'daesfrgdhf', 'CV_1765989801_7.pdf', '', '2025-12-19 10:00:00', 'https://meet.google.com/xyz-ec76d892', 'pending', '2025-12-17 16:43:21'),
(4, 9, 2, 2, 'asspaspa', 'assas', 'CV_1765996971_9.pdf', '', '2025-12-19 10:00:00', 'https://meet.google.com/xyz-e023132d', 'accepted', '2025-12-17 18:42:51');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `nrp` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `biodata` text DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `role` enum('admin','student') NOT NULL DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `nrp`, `password`, `email`, `biodata`, `profile_picture`, `role`, `created_at`) VALUES
(1, 'dummy', NULL, '$2y$10$uHO1lkz8QGZhwykuZd7rEe4dwZDUGo8V//Imx5URcBKrJ6NKB.SEu', 'dummy@john.petra.ac.id', NULL, NULL, 'student', '0000-00-00 00:00:00'),
(4, 'admin123', NULL, '$2y$10$uiZ/k.Rko4U6gmdx7f9YY.5pZi7mfqAINfu0rWOt2mCImT7eJkW5e', 'admin123@gmail.com', NULL, NULL, 'admin', '0000-00-00 00:00:00'),
(7, 'SAMUEL KENNETH', NULL, '$2y$10$eKdDjb8gWhlzUAnhrHI/2Ovad0IVEM41cwvXOvEUVE6RyidBhwwAO', 'c14240077@john.petra.ac.id', NULL, NULL, 'student', '2025-12-17 10:09:23'),
(8, 'Super Admin', NULL, '$2y$10$VNo26Z6mmzOnKE3/MrNN6e17nC2XQirxhEtczz1s5cjbUIaUeDo3a', 'admin@john.petra.ac.id', NULL, NULL, 'admin', '2025-12-17 12:42:57'),
(9, 'Sean Vandana Sanjaya', 'c14240092', '$2y$10$uZm0yMR2tzkconDheWHqH.G5pcLjBVmuZ/a6uhEkjcdNR0G1Q8F1W', 'c14240092@john.petra.ac.id', 'Mahasiswa infor angkatan 24\r\n', '1765993543_Me.jpg', 'admin', '2025-12-17 17:01:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `idx_events_created_by` (`created_by`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`position_id`),
  ADD KEY `event_id` (`event_id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`registration_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `position_id` (`position_id`),
  ADD KEY `position_id_2` (`position_id_2`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `position_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `positions`
--
ALTER TABLE `positions`
  ADD CONSTRAINT `positions_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`) ON DELETE CASCADE;

--
-- Constraints for table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `registrations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `registrations_ibfk_2` FOREIGN KEY (`position_id`) REFERENCES `positions` (`position_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `registrations_ibfk_3` FOREIGN KEY (`position_id_2`) REFERENCES `positions` (`position_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

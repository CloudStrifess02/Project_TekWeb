-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 22, 2025 at 05:59 PM
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
(3, 'Renang', 'adsfsgd', '2025-12-18', '2025-12-23', 'gedung Q', 'Lomba', NULL, 'published', '2025-12-17 16:42:00', NULL),
(4, 'LE 13', 'WOII', '2025-12-18', '2025-12-23', 'Online', 'Workshop', 'event_1766023532.png', 'published', '2025-12-18 02:05:36', NULL),
(5, 'Lomba menanam', 'Tanam', '2025-12-18', '2025-12-25', 'Online', 'Seminar', 'event_1766026009.png', 'published', '2025-12-18 02:48:14', 9),
(6, 'lomba', 'sdfg', '2025-12-19', '2026-01-02', 'loooooooooooooooooooooooooooooooool.', 'Seminar', 'event_1766026484.png', 'published', '2025-12-18 02:54:51', NULL),
(7, 'HutLk', 'afefeaf', '2025-12-17', '2025-12-31', 'Online', 'Seminar', 'event_1766027326.png', 'published', '2025-12-18 03:10:03', NULL),
(8, 'iyuhuhytuyi', 'dvwww', '2025-12-19', '2025-12-27', 'Online', 'Seminar', 'event_1766027799.png', 'published', '2025-12-18 03:17:29', 4),
(9, 'Acara Lama', 'GTAWTTAW', '2025-12-15', '2025-12-16', 'Online', 'Seminar', 'default_event.png', 'published', '2025-12-18 03:20:05', 4),
(10, 'Career Kickstart', 'dsasfdgb', '2025-12-23', '2025-12-25', 'Online', 'Seminar', NULL, 'published', '2025-12-22 16:06:51', NULL);

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
(2, 3, 'Divisi Sponsor', 10, 'asf', '2025-12-17 16:42:47'),
(3, 4, 'Divisi Acara', 3, 'RUndown', '2025-12-18 02:05:55'),
(4, 4, 'Divisi keamanan', 10, 'sadfsg', '2025-12-18 02:06:07'),
(5, 5, 'Divisi Acara', 5, NULL, '2025-12-18 02:47:53'),
(6, 5, 'Divisi Creative', 10, NULL, '2025-12-18 02:48:02'),
(7, 7, 'Divisi Acara', 10, 'gitu ae', '2025-12-18 03:09:23'),
(8, 7, 'Divisi Abc', 2, 'gtw', '2025-12-18 03:09:46'),
(9, 8, 'Divisi Sponsor', 5, NULL, '2025-12-18 03:16:56'),
(10, 9, 'Divisi Sponsor', 5, NULL, '2025-12-18 03:19:49');

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
(3, 7, 2, 2, 'wefrgt', 'daesfrgdhf', 'CV_1765989801_7.pdf', '', '2025-12-19 10:00:00', 'https://meet.google.com/xyz-ec76d892', 'pending', '2025-12-17 16:43:21'),
(4, 7, 3, 4, 'adf', 'sswadefr', 'CV_1766023625_7.pdf', '', '2025-12-20 10:00:00', 'https://meet.google.com/xyz-f00e764c', 'accepted', '2025-12-18 02:07:05'),
(5, 9, 5, 5, 'sadfd', 'asdsf', 'CV_1766026160_9.pdf', '', '2025-12-20 10:00:00', 'https://meet.google.com/xyz-a40b1caf', 'pending', '2025-12-18 02:49:20'),
(6, 4, 8, 7, 'giuhb', 'bacebwjfgewjfgj', 'CV_1766027566_4.pdf', '', '2025-12-20 10:00:00', 'https://meet.google.com/xyz-fc0d91d2', 'accepted', '2025-12-18 03:12:46'),
(7, 7, 3, 3, 'tufyguhij', 'yrdtfgh', 'CV_1766028537_7.pdf', '', '2025-12-20 10:00:00', 'https://meet.google.com/xyz-1007e2b9', 'pending', '2025-12-18 03:28:57'),
(8, 7, 3, 4, 'asdfsdbfgewaf', 'waefsrgdwf', 'CV_1766047635_7.pdf', '', '2025-12-20 10:00:00', 'https://meet.google.com/xyz-c7435d6a', 'pending', '2025-12-18 08:47:15');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `nrp` varchar(20) DEFAULT NULL,
  `role` enum('admin','student') NOT NULL DEFAULT 'student',
  `biodata` text DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `password`, `email`, `nrp`, `role`, `biodata`, `profile_picture`, `created_at`) VALUES
(1, 'dummy', 'dummy123', '', NULL, '', NULL, NULL, '0000-00-00 00:00:00'),
(4, 'admin123', '$2y$10$uiZ/k.Rko4U6gmdx7f9YY.5pZi7mfqAINfu0rWOt2mCImT7eJkW5e', 'admin123@gmail.com', 'C14240092', 'admin', 'frrr', '1766027744_WhatsApp Image 2025-12-10 at 22.27.19.jpeg', '0000-00-00 00:00:00'),
(7, 'SAMUEL KENNETH', '$2y$10$eKdDjb8gWhlzUAnhrHI/2Ovad0IVEM41cwvXOvEUVE6RyidBhwwAO', 'c14240077@john.petra.ac.id', NULL, 'student', NULL, NULL, '2025-12-17 10:09:23'),
(8, 'Super Admin', '$2y$10$VNo26Z6mmzOnKE3/MrNN6e17nC2XQirxhEtczz1s5cjbUIaUeDo3a', 'admin@john.petra.ac.id', NULL, 'admin', NULL, NULL, '2025-12-17 12:42:57'),
(9, 'Sean', '$2y$10$0WZ1SxmFeNrfGd9GmdMri.GbvQVShE65AadX8/t0dI8K4rxoufoBe', 'c14240092@john.petra.ac.id', 'C14240092', 'student', 'Bocil', '1766025967_image_2025-12-18_094537442.png', '2025-12-18 02:39:54');

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
  MODIFY `event_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `position_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `registration_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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

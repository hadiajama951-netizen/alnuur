-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 19, 2026 at 06:04 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `alnuur`
--

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `class` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `student_id`, `name`, `class`, `created_at`) VALUES
(12, '001', 'Apdifatah Nour Husein', 'Form 4A', '2026-05-19 14:51:11'),
(13, '002', 'hadiya', 'Form 4B', '2026-05-19 14:55:34'),
(14, '007', 'maxamed', 'Form 4B', '2026-05-19 15:09:41');

-- --------------------------------------------------------

--
-- Table structure for table `student_marks`
--

CREATE TABLE `student_marks` (
  `id` int(11) NOT NULL,
  `roll_no` varchar(50) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `class` varchar(50) NOT NULL,
  `math` int(11) DEFAULT 0,
  `english` int(11) DEFAULT 0,
  `science` int(11) DEFAULT 0,
  `somali` int(11) DEFAULT 0,
  `history` int(11) DEFAULT 0,
  `geography` int(11) DEFAULT 0,
  `arabic` int(11) DEFAULT 0,
  `islamic` int(11) DEFAULT 0,
  `chemistry` int(11) DEFAULT 0,
  `physics` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_marks`
--

INSERT INTO `student_marks` (`id`, `roll_no`, `full_name`, `class`, `math`, `english`, `science`, `somali`, `history`, `geography`, `arabic`, `islamic`, `chemistry`, `physics`, `created_at`) VALUES
(42, '001', 'Apdifatah Nour Husein', 'Form 4A', 100, 100, 100, 100, 100, 100, 100, 100, 0, 0, '2026-05-19 14:51:11'),
(43, '002', 'hadiya', 'Form 4B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2026-05-19 14:55:34'),
(44, '007', 'maxamed', 'Form 4B', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2026-05-19 15:09:41');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `subject_code` varchar(50) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `teacher` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin','User') DEFAULT 'User',
  `status` varchar(20) DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `username`, `email`, `password`, `role`, `status`) VALUES
(26, 'Apdifatah Nour Husein', '001', 'apdifatahnourhusein@school.com', '$2y$10$6cqe.567TJM3RPWRTE7zV.UWXmxeQNGh7lfYMPVtlBNzYyBLSkMOW', 'User', 'Active'),
(27, 'hadiya', '002', 'hadiya@school.com', '$2y$10$Ote5C3xZWZH1tHj2UeXsp.D06WL5UUgUmbf2Zh4xSJMeSv1.KO2xu', 'User', 'Active'),
(29, 'admin', 'admin', 'admin@gmail.com', '$2y$10$Fs9ZhXcq9TR70s39POXPmeIfQCfD/f24wvcYZ7l67XzwzvC.M2d6S', 'Admin', 'Active'),
(30, 'maxamed', '007', 'maxamed@school.com', '$2y$10$8qkq098TTW7tQ/Grc6cCYOoauv9NaqywFMvliEccgazyXBxnoKPuy', 'User', 'Active'),
(31, 'abdifatah', 'abdifatah', 'Apdifatahdipho@gmail.com', '$2y$10$ICnRwwPnpY81CF/T00lMQeXzEEk9uRffI0tzVlUAn9XpbBsADbosK', 'Admin', 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`);

--
-- Indexes for table `student_marks`
--
ALTER TABLE `student_marks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `student_marks`
--
ALTER TABLE `student_marks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

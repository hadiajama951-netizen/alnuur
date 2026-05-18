-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 18, 2026 at 05:51 PM
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
(1, '16977', 'CABDIFATAAX NUUR XUSEEN', 'Form 4B', '2026-05-05 21:55:12'),
(2, '16970', 'muuse', 'Form 4B', '2026-05-05 22:11:36'),
(4, '88', 'CABDIFATAAX NUUR XUSEEN', 'Form 4A', '2026-05-06 07:32:41');

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
(9, '001', 'abdinasir', 'Form 4B', 100, 100, 100, 100, 100, 100, 100, 100, 100, 100, '2026-05-18 15:15:55'),
(10, '002', 'nimco', 'Form 4B', 50, 100, 100, 90, 80, 90, 70, 100, 100, 100, '2026-05-18 15:44:55');

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
(1, 'Admin Ali', 'admin_ali', 'ali@alnuur.edu', '12345', 'Admin', 'Active'),
(2, 'Mohamed Ahmed', 'std_mohamed', 'mohamed@student.com', '12345', 'User', 'Active'),
(3, 'mubaarik abdirashiid ', 'user', 'Apdifatahdipho@gmail.com', 'user', 'User', 'Active'),
(4, 'mubaarik abdirashiid ', 'admin', 'marco@gmail.com', '1234', 'Admin', 'Active'),
(5, 'jamac', 'jamac', 'jamac@gail.com', '123', 'Admin', 'Active');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `student_marks`
--
ALTER TABLE `student_marks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

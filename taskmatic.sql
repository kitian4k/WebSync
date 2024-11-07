-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 07, 2024 at 01:35 PM
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
-- Database: `taskmatic`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance_info`
--

CREATE TABLE `attendance_info` (
  `aten_id` int(20) NOT NULL,
  `atn_user_id` int(20) NOT NULL,
  `in_time` varchar(200) DEFAULT NULL,
  `out_time` varchar(150) DEFAULT NULL,
  `total_duration` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `attendance_info`
--

INSERT INTO `attendance_info` (`aten_id`, `atn_user_id`, `in_time`, `out_time`, `total_duration`) VALUES
(38, 18, '22-03-2021 13:51:01', '09-11-2023 10:09:02', '20:18:01'),
(35, 17, '22-03-2021 11:37:44', '07-01-2024 22:55:15', '11:17:31'),
(37, 21, '22-03-2021 13:49:26', NULL, NULL),
(39, 23, '22-03-2021 13:51:51', NULL, NULL),
(40, 20, '22-03-2021 13:52:24', NULL, NULL),
(41, 25, '22-03-2021 15:09:00', NULL, NULL),
(42, 1, '22-03-2021 22:01:43', '13-01-2024 22:54:50', '00:53:07'),
(43, 17, '07-01-2024 22:56:44', '07-01-2024 22:57:16', '00:00:32'),
(44, 28, '13-01-2024 22:52:18', '13-01-2024 22:52:45', '00:00:27');

-- --------------------------------------------------------

--
-- Table structure for table `task_info`
--

CREATE TABLE `task_info` (
  `task_id` int(50) NOT NULL,
  `t_title` varchar(120) NOT NULL,
  `t_description` text DEFAULT NULL,
  `t_start_time` varchar(100) DEFAULT NULL,
  `t_end_time` varchar(100) DEFAULT NULL,
  `t_user_id` int(20) NOT NULL,
  `status` int(11) NOT NULL DEFAULT 0 COMMENT '0 = incomplete, 1 = In progress, 2 = complete'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `task_info`
--

INSERT INTO `task_info` (`task_id`, `t_title`, `t_description`, `t_start_time`, `t_end_time`, `t_user_id`, `status`) VALUES
(58, 'Survey', 'Survey', '2024-11-04 12:00', '2024-11-05 12:00', 33, 1),
(43, 'Chapter 3', '123', '2024-10-26 12:04', '2024-10-31 12:00', 29, 2),
(57, 'Video Recording', 'Website Demo', '2024-11-04 12:00', '2024-11-06 12:00', 29, 1),
(72, 'CHAPTER 5', 'FINISH ALL THE TASK', '2024-11-08 12:00', '2024-11-11 12:00', 30, 1),
(64, 'Video Recording', '123', '2024-11-06 12:00', '2024-11-08 12:00', 33, 1),
(70, 'TEST TASK FOR ADMIN', 'TEST TASK FOR ADMIN', '2024-11-08 12:00', '2024-11-11 12:00', 37, 1),
(66, 'Research Defense', 'Research Defense', '', '2024-11-15 12:00', 33, 1),
(71, 'CHAPTER 4', 'FOR DEFENSE', '2024-11-08 12:00', '2024-11-15 12:00', 1, 0),
(73, 'PRINT ALL THE CHAPTERS', 'CHAPTER 1 TO 5', '', '2024-11-11 12:00', 34, 1);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `user_id` int(20) NOT NULL,
  `fullname` varchar(120) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `temp_password` varchar(100) DEFAULT NULL,
  `user_role` int(10) NOT NULL,
  `user_course` varchar(100) NOT NULL,
  `user_group` varchar(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `tbl_admin`
--

INSERT INTO `tbl_admin` (`user_id`, `fullname`, `username`, `email`, `password`, `temp_password`, `user_role`, `user_course`, `user_group`) VALUES
(1, 'Jerald Illustrisimo', 'TUPM-20-0009', 'TEST@gmail.com', 'admin123', NULL, 1, 'BSIE-ICT', 'Group 1'),
(39, 'ACCOUNT ADMIN', 'TUPM-20-1234', 'admin123@tup.edu.ph', 'admin123', NULL, 1, 'BSIE-ICT', 'Group 1'),
(30, 'Janeane Libres', 'TUPM-20-0003', 'TEST3@gmail.com', 'Test123', NULL, 1, 'BSIE-ICT', 'Group 1'),
(34, 'Agatha Lapuz', 'TUPM-20-0007', 'Aesth@tup.edu.ph', 'admin123', NULL, 1, 'BSIE-ICT', 'Group 1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance_info`
--
ALTER TABLE `attendance_info`
  ADD PRIMARY KEY (`aten_id`);

--
-- Indexes for table `task_info`
--
ALTER TABLE `task_info`
  ADD PRIMARY KEY (`task_id`);

--
-- Indexes for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance_info`
--
ALTER TABLE `attendance_info`
  MODIFY `aten_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `task_info`
--
ALTER TABLE `task_info`
  MODIFY `task_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `user_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

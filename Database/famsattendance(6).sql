-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 10, 2025 at 05:12 AM
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
-- Database: `famsattendance`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `ID` int(11) NOT NULL,
  `USERNAME` varchar(50) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`ID`, `USERNAME`, `PASSWORD`) VALUES
(1, 'admin', '$2y$10$ZlfrUEK9PJE9EKtE9/6CouvKM8uI.vBTjW8xLPyUjE3qnZecew/AK');

-- --------------------------------------------------------

--
-- Table structure for table `emp_info`
--

CREATE TABLE `emp_info` (
  `ID` varchar(20) NOT NULL,
  `Name` varchar(50) NOT NULL,
  `DEPT` varchar(255) NOT NULL,
  `STATUS` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emp_info`
--

INSERT INTO `emp_info` (`ID`, `Name`, `DEPT`, `STATUS`) VALUES
('1', 'JM', 'Test', ''),
('3', 'Mj', 'Administrator', ''),
('4', 'Corpuz', 'Faculty_Member', ''),
('4444', 'Mark', 'Other_Personnel', '');

-- --------------------------------------------------------

--
-- Table structure for table `emp_rec`
--

CREATE TABLE `emp_rec` (
  `ID` int(11) NOT NULL,
  `EMP_ID` varchar(20) DEFAULT NULL,
  `AM_IN` time DEFAULT NULL,
  `AM_OUT` time DEFAULT NULL,
  `PM_IN` time DEFAULT NULL,
  `PM_OUT` time DEFAULT NULL,
  `DATE` date NOT NULL,
  `LATE` int(255) DEFAULT NULL,
  `UNDERTIME` int(255) DEFAULT NULL,
  `NOTE` varchar(255) DEFAULT NULL,
  `OB` int(255) DEFAULT NULL,
  `SL` int(255) DEFAULT NULL,
  `HOLIDAY` int(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emp_rec`
--

INSERT INTO `emp_rec` (`ID`, `EMP_ID`, `AM_IN`, `AM_OUT`, `PM_IN`, `PM_OUT`, `DATE`, `LATE`, `UNDERTIME`, `NOTE`, `OB`, `SL`, `HOLIDAY`) VALUES
(110, '1', NULL, NULL, NULL, NULL, '2025-05-01', 0, 660, NULL, NULL, NULL, 1),
(111, '1', NULL, NULL, NULL, NULL, '2025-05-02', 0, 660, NULL, NULL, NULL, 0),
(112, '1', NULL, NULL, NULL, NULL, '2025-05-03', 0, 660, NULL, NULL, NULL, 0),
(113, '1', NULL, NULL, NULL, NULL, '2025-05-04', 0, 660, NULL, NULL, NULL, 0),
(114, '1', NULL, NULL, NULL, NULL, '2025-05-05', 0, 660, NULL, NULL, NULL, 0),
(115, '1', NULL, NULL, NULL, NULL, '2025-05-06', 0, 660, NULL, NULL, NULL, 0),
(116, '1', NULL, NULL, NULL, NULL, '2025-05-07', 0, 660, NULL, NULL, NULL, 0),
(117, '1', NULL, NULL, NULL, NULL, '2025-05-08', 0, 660, NULL, NULL, NULL, 0),
(118, '1', NULL, NULL, NULL, NULL, '2025-05-09', 0, 660, NULL, NULL, NULL, 0),
(119, '1', NULL, NULL, NULL, NULL, '2025-05-10', 0, 660, NULL, NULL, NULL, 0),
(120, '3', '09:53:00', '11:48:00', '12:54:00', '13:19:00', '2025-05-01', 173, 346, NULL, 0, NULL, 1),
(121, '3', NULL, NULL, NULL, NULL, '2025-05-02', 0, 480, NULL, NULL, NULL, 0),
(122, '3', NULL, NULL, NULL, NULL, '2025-05-03', 0, 480, NULL, NULL, NULL, 0),
(123, '3', NULL, NULL, NULL, NULL, '2025-05-04', 0, 480, NULL, NULL, NULL, 0),
(124, '3', '00:29:00', NULL, NULL, NULL, '2025-05-05', 0, 480, NULL, NULL, NULL, 0),
(125, '3', '09:14:00', NULL, NULL, NULL, '2025-05-06', 134, 480, NULL, NULL, NULL, 0),
(126, '3', NULL, NULL, NULL, NULL, '2025-05-07', 0, 480, NULL, NULL, NULL, 0),
(127, '3', '07:58:00', '08:57:00', '13:00:00', '17:00:00', '2025-05-08', 58, 241, NULL, 0, NULL, 0),
(128, '3', '07:00:00', '12:00:00', '13:10:00', '17:00:00', '2025-05-09', 10, 10, 'asdasd', 1, 0, 0),
(129, '4444', NULL, '12:05:00', '12:06:00', '13:19:00', '2025-05-01', 0, 641, NULL, NULL, NULL, 1),
(130, '4444', NULL, NULL, NULL, NULL, '2025-05-02', 0, 660, NULL, NULL, NULL, 0),
(131, '4444', NULL, NULL, NULL, NULL, '2025-05-03', 0, 660, NULL, NULL, NULL, 0),
(132, '4444', NULL, NULL, NULL, NULL, '2025-05-04', 0, 660, NULL, NULL, NULL, 0),
(133, '4444', NULL, NULL, NULL, NULL, '2025-05-05', 0, 660, NULL, NULL, NULL, 0),
(134, '4444', NULL, NULL, NULL, NULL, '2025-05-06', 0, 660, NULL, NULL, NULL, 0),
(135, '4444', NULL, NULL, NULL, NULL, '2025-05-07', 0, 660, NULL, NULL, NULL, 0),
(136, '4444', '05:00:00', '12:00:00', '13:00:00', NULL, '2025-05-08', 0, 300, NULL, NULL, NULL, 0),
(137, '4444', '06:00:00', '12:10:00', NULL, NULL, '2025-05-09', 0, 300, NULL, NULL, NULL, 0),
(138, '4444', '06:05:00', '12:00:00', '12:00:00', '18:00:00', '2025-05-10', 5, 5, NULL, 0, NULL, 0),
(139, '4', NULL, NULL, NULL, NULL, '2025-05-01', 0, 480, NULL, NULL, NULL, 1),
(140, '4', NULL, NULL, NULL, NULL, '2025-05-02', 0, 480, NULL, NULL, NULL, 0),
(141, '4', NULL, NULL, NULL, NULL, '2025-05-03', 0, 480, NULL, NULL, NULL, 0),
(142, '4', NULL, NULL, NULL, NULL, '2025-05-04', 0, 480, NULL, NULL, NULL, 0),
(143, '4', '10:30:00', '10:30:00', NULL, NULL, '2025-05-05', 150, 480, NULL, NULL, NULL, 0),
(144, '4', NULL, NULL, NULL, NULL, '2025-05-06', 0, 480, NULL, NULL, NULL, 0),
(145, '4', NULL, NULL, NULL, NULL, '2025-05-07', 0, 480, NULL, NULL, NULL, 0),
(146, '4', NULL, NULL, NULL, NULL, '2025-05-08', 0, 480, NULL, NULL, 0, 0),
(147, '4', NULL, NULL, NULL, NULL, '2025-05-09', 0, 480, NULL, 0, NULL, 0),
(148, '4', NULL, NULL, NULL, NULL, '2025-05-10', 0, 480, NULL, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `holidays`
--

CREATE TABLE `holidays` (
  `ID` int(11) NOT NULL,
  `DATE` date NOT NULL,
  `DESCRIPTION` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `holidays`
--

INSERT INTO `holidays` (`ID`, `DATE`, `DESCRIPTION`) VALUES
(1, '2025-05-01', 'Basta');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `emp_info`
--
ALTER TABLE `emp_info`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `emp_rec`
--
ALTER TABLE `emp_rec`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `emp_rec_ibfk_1` (`EMP_ID`);

--
-- Indexes for table `holidays`
--
ALTER TABLE `holidays`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `DATE` (`DATE`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `emp_rec`
--
ALTER TABLE `emp_rec`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=149;

--
-- AUTO_INCREMENT for table `holidays`
--
ALTER TABLE `holidays`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `emp_rec`
--
ALTER TABLE `emp_rec`
  ADD CONSTRAINT `emp_rec_ibfk_1` FOREIGN KEY (`EMP_ID`) REFERENCES `emp_info` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 08, 2025 at 02:04 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

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
('00', 'mnfv, jhf mhf', 'other_personel', 'Active'),
('1', 'JM', 'Administrator', 'Active'),
('2', 'Mara', 'Administrator', 'Active'),
('3', 'Mj', 'Other_Personnel', 'Inactive'),
('4444', 'Mark', 'Administrator', 'Active'),
('821', '85', 'Administrator', 'Active');

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
  `OB` int(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emp_rec`
--

INSERT INTO `emp_rec` (`ID`, `EMP_ID`, `AM_IN`, `AM_OUT`, `PM_IN`, `PM_OUT`, `DATE`, `LATE`, `UNDERTIME`, `NOTE`, `OB`) VALUES
(1, '1', NULL, NULL, NULL, NULL, '2025-05-01', 0, 540, NULL, NULL),
(2, '1', NULL, NULL, NULL, NULL, '2025-05-02', 0, 540, NULL, NULL),
(3, '1', NULL, NULL, NULL, NULL, '2025-05-03', 0, 540, NULL, NULL),
(4, '1', NULL, NULL, NULL, NULL, '2025-05-04', 0, 540, NULL, NULL),
(5, '1', NULL, NULL, NULL, NULL, '2025-05-05', 0, 540, NULL, 1),
(6, '821', NULL, NULL, NULL, NULL, '2025-05-01', 0, 540, NULL, NULL),
(7, '821', NULL, NULL, NULL, NULL, '2025-05-02', 0, 540, NULL, NULL),
(8, '821', NULL, NULL, NULL, NULL, '2025-05-03', 0, 540, NULL, NULL),
(9, '821', NULL, NULL, NULL, NULL, '2025-05-04', 0, 540, NULL, NULL),
(10, '821', NULL, NULL, NULL, NULL, '2025-05-05', 0, 540, NULL, NULL),
(11, '00', NULL, NULL, NULL, NULL, '2025-05-01', 0, 540, NULL, NULL),
(12, '00', NULL, NULL, NULL, NULL, '2025-05-02', 0, 540, NULL, NULL),
(13, '00', NULL, NULL, NULL, NULL, '2025-05-03', 0, 540, NULL, NULL),
(14, '00', NULL, NULL, NULL, NULL, '2025-05-04', 0, 540, NULL, NULL),
(15, '00', NULL, NULL, NULL, NULL, '2025-05-05', 0, 540, NULL, NULL),
(16, '2', NULL, NULL, NULL, NULL, '2025-05-01', 0, 540, NULL, NULL),
(17, '2', NULL, NULL, NULL, NULL, '2025-05-02', 0, 540, NULL, NULL),
(18, '2', NULL, NULL, NULL, NULL, '2025-05-03', 0, 540, NULL, NULL),
(19, '2', NULL, NULL, NULL, NULL, '2025-05-04', 0, 540, NULL, NULL),
(20, '2', '00:28:00', NULL, NULL, NULL, '2025-05-05', 0, 540, NULL, NULL),
(21, '3', '09:53:00', '13:19:00', NULL, NULL, '2025-05-01', 53, 334, NULL, NULL),
(22, '3', NULL, NULL, NULL, NULL, '2025-05-02', 0, 540, NULL, NULL),
(23, '3', NULL, NULL, NULL, NULL, '2025-05-03', 0, 540, NULL, 1),
(24, '3', NULL, NULL, NULL, NULL, '2025-05-04', 0, 540, NULL, 0),
(25, '3', '08:00:00', NULL, NULL, '16:00:00', '2025-05-05', 0, 540, NULL, NULL),
(26, '4444', '12:05:00', '13:19:00', NULL, NULL, '2025-05-01', 185, 466, NULL, NULL),
(27, '4444', NULL, NULL, NULL, NULL, '2025-05-02', 0, 540, NULL, NULL),
(28, '4444', NULL, NULL, NULL, NULL, '2025-05-03', 0, 540, NULL, NULL),
(29, '4444', NULL, NULL, NULL, NULL, '2025-05-04', 0, 540, NULL, NULL),
(30, '4444', NULL, NULL, NULL, NULL, '2025-05-05', 0, 540, NULL, 1);

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
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

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

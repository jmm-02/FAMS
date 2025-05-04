-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 04, 2025 at 04:00 PM
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
('1', 'JM', 'Company', ''),
('4444', 'Markyy', 'Company', '');

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
  `NOTE` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emp_rec`
--

INSERT INTO `emp_rec` (`ID`, `EMP_ID`, `AM_IN`, `AM_OUT`, `PM_IN`, `PM_OUT`, `DATE`, `NOTE`) VALUES
(951, '1', '18:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-01', NULL),
(952, '1', '19:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-02', NULL),
(953, '1', '20:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-03', NULL),
(954, '1', '21:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-04', NULL),
(955, '1', '22:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-05', NULL),
(956, '1', '23:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-06', NULL),
(957, '1', '00:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-07', NULL),
(958, '1', '01:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-08', NULL),
(959, '1', '02:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-09', NULL),
(960, '1', '03:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-10', NULL),
(961, '1', '04:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-11', NULL),
(962, '1', '05:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-12', NULL),
(963, '1', '06:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-13', NULL),
(964, '1', '07:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-14', NULL),
(965, '1', '08:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-15', NULL),
(966, '1', '09:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-16', NULL),
(967, '1', '10:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-17', NULL),
(968, '1', '11:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-18', NULL),
(969, '1', '12:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-19', NULL),
(970, '1', '13:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-20', NULL),
(971, '1', '14:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-21', NULL),
(972, '1', '15:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-22', NULL),
(973, '1', '16:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-23', NULL),
(974, '1', '17:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-24', NULL),
(975, '1', '18:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-25', NULL),
(976, '1', '19:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-26', NULL),
(977, '1', '20:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-27', NULL),
(978, '1', '21:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-28', NULL),
(979, '1', '22:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-29', NULL),
(980, '1', '23:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-30', NULL),
(981, '4444', '23:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-30', NULL),
(982, '1', '23:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-05-02', NULL);

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
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=983;

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

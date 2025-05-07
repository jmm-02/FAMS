-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 07, 2025 at 04:29 PM
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
('00', 'mnfv, jhf mhf', 'Administrator', 'Active'),
('1', 'JM', 'Administrator', 'Active'),
('2', 'Mara', 'Administrator', 'Active'),
('3', 'Mj', 'Test', 'Inactive'),
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
  `NOTE` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

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

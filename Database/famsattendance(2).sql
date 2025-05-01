-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 01, 2025 at 03:40 AM
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
(1, 'admin', '$2a$05$Ut9rp1AtP5e.qb/wplcSnebY1PpffCFbYufDN3MGPJ1iKGx46CDDS');

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
('1', 'JM', 'Company', '');

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
  `DATE` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `emp_rec`
--

INSERT INTO `emp_rec` (`ID`, `EMP_ID`, `AM_IN`, `AM_OUT`, `PM_IN`, `PM_OUT`, `DATE`) VALUES
(764, '1', '18:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-01'),
(765, '1', '19:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-02'),
(766, '1', '20:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-03'),
(767, '1', '21:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-04'),
(768, '1', '22:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-05'),
(769, '1', '23:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-06'),
(770, '1', '00:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-07'),
(771, '1', '01:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-08'),
(772, '1', '02:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-09'),
(773, '1', '03:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-10'),
(774, '1', '04:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-11'),
(775, '1', '05:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-12'),
(776, '1', '06:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-13'),
(777, '1', '07:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-14'),
(778, '1', '08:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-15'),
(779, '1', '09:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-16'),
(780, '1', '10:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-17'),
(781, '1', '11:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-18'),
(782, '1', '12:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-19'),
(783, '1', '13:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-20'),
(784, '1', '14:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-21'),
(785, '1', '15:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-22'),
(786, '1', '16:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-23'),
(787, '1', '17:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-24'),
(788, '1', '18:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-25'),
(789, '1', '19:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-26'),
(790, '1', '20:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-27'),
(791, '1', '21:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-28'),
(792, '1', '22:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-29'),
(793, '1', '23:31:00', '18:31:00', '18:31:00', '18:31:00', '2025-04-30');

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
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=794;

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

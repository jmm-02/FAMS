-- Table structure for table `holidays`
CREATE TABLE `holidays` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `DATE` date NOT NULL,
  `DESCRIPTION` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `DATE` (`DATE`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add column to emp_rec table if not exists
ALTER TABLE `emp_rec`
ADD COLUMN IF NOT EXISTS `HOLIDAY` int(1) DEFAULT 0 AFTER `SL`; 
-- phpMyAdmin SQL Dump
-- version 4.4.10
-- http://www.phpmyadmin.net
--
-- Host: 10.168.1.93
-- Generation Time: Mar 07, 2016 at 01:06 PM
-- Server version: 5.6.21
-- PHP Version: 5.3.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `letsnurt_wayapp`
--

-- --------------------------------------------------------

--
-- Table structure for table `beacon`
--

CREATE TABLE IF NOT EXISTS `beacon` (
  `ID` int(11) NOT NULL,
  `Gate_ID` varchar(50) NOT NULL,
  `Beacon_ID` varchar(50) NOT NULL,
  `Beacon_Minor` varchar(5) NOT NULL,
  `Beacon_Major` varchar(4) NOT NULL,
  `Garade_ID` varchar(32) NOT NULL,
  `Park_ID` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `beacon`
--

INSERT INTO `beacon` (`ID`, `Gate_ID`, `Beacon_ID`, `Beacon_Minor`, `Beacon_Major`, `Garade_ID`, `Park_ID`) VALUES
(1, '10', '00 01 01 00 11 00 01 10 10 00 00 10 11 11 01 00', '0001', '10', '5', 1004),
(2, '11', '00 01 01 00 11 00 01 10 10 00 00 10 11 11 01 00', '0002', '11', '5', 1004),
(3, '12', '00 01 01 00 11 00 01 10 10 00 00 10 11 11 01 00', '0002', '12', '5', 1004),
(4, '13', '00 01 01 00 11 00 01 10 10 00 00 10 11 11 01 00', '0001', '13', '6', 362),
(5, '14', '00 01 01 00 11 00 01 10 10 00 00 10 11 11 01 00', '0002', '14', '6', 362),
(6, '15', '00 01 01 00 11 00 01 10 10 00 00 10 11 11 01 00', '0002', '15', '6', 362),
(7, '10', '00 01 01 00 11 00 01 10 10 00 00 10 11 11 01 00', '0001', '10', '5', 172),
(8, '11', '00 01 01 00 11 00 01 10 10 00 00 10 11 11 01 00', '0002', '11', '5', 172),
(9, '12', '00 01 01 00 11 00 01 10 10 00 00 10 11 11 01 00', '0002', '12', '5', 172);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `beacon`
--
ALTER TABLE `beacon`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `beacon`
--
ALTER TABLE `beacon`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=10;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

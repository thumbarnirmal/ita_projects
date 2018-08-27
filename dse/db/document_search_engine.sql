-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 22, 2016 at 06:31 AM
-- Server version: 10.1.9-MariaDB
-- PHP Version: 5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `document_search_engine`
--

-- --------------------------------------------------------

--
-- Table structure for table `document_table`
--

CREATE TABLE `document_table` (
  `document_id` int(11) NOT NULL,
  `document_name` varchar(100) NOT NULL,
  `document_url` varchar(200) NOT NULL,
  `document_thumbnail_url` varchar(200) NOT NULL,
  `document_author` varchar(100) DEFAULT NULL,
  `document_tag` varchar(200) DEFAULT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `document_table`
--

INSERT INTO `document_table` (`document_id`, `document_name`, `document_url`, `document_thumbnail_url`, `document_author`, `document_tag`, `create_time`, `update_time`) VALUES
(17, 'hadoop', '57191d171c335_17document.pdf', '57191a90186d7_17_document_thumbnail.png', 'hortonworks', 'Software Tools,DBMS', '2016-04-21 18:23:11', '2016-04-21 18:33:59'),
(20, 'fbhackercup', '5719a59655945_20document.pdf', '5719a59675c38_20_document_thumbnail.jpg', 'facebook', 'Information Security', '2016-04-22 04:16:22', '2016-04-22 04:16:22'),
(21, 'srs', '5719a7dc74fff_21document.pdf', '5719a7a810d73_21_document_thumbnail.png', 'nirmal', 'Software Tools', '2016-04-22 04:25:11', '2016-04-22 04:26:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `document_table`
--
ALTER TABLE `document_table`
  ADD PRIMARY KEY (`document_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `document_table`
--
ALTER TABLE `document_table`
  MODIFY `document_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

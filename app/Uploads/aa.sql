-- phpMyAdmin SQL Dump
-- version 4.1.12
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Mar 20, 2015 at 10:53 AM
-- Server version: 5.5.36
-- PHP Version: 5.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `test`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `test_multi_sets`()
    DETERMINISTIC
begin
        select user() as first_col;
        select user() as first_col, now() as second_col;
        select user() as first_col, now() as second_col, now() as third_col;
        end$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `think_data`
--

CREATE TABLE IF NOT EXISTS `think_data` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `data` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `think_data`
--

INSERT INTO `think_data` (`id`, `data`) VALUES
(1, 'thinkphp'),
(2, 'php'),
(3, 'framework');

-- --------------------------------------------------------

--
-- Table structure for table `think_form`
--

CREATE TABLE IF NOT EXISTS `think_form` (
  `id` smallint(4) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` varchar(255) NOT NULL,
  `create_time` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `think_form`
--

INSERT INTO `think_form` (`id`, `title`, `content`, `create_time`) VALUES
(1, '1112', 'asdfadf', 1426730225),
(2, '222', '22222', 1426730232),
(5, '5566', '55', 1426732654),
(4, '4445', '4445', 1426732530);

-- --------------------------------------------------------

--
-- Table structure for table `think_goods`
--

CREATE TABLE IF NOT EXISTS `think_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sku` varchar(100) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `size` varchar(255) DEFAULT NULL,
  `stock_total` int(5) NOT NULL DEFAULT '0',
  `stock_used` int(5) NOT NULL DEFAULT '0',
  `add_time` int(12) DEFAULT NULL,
  `memo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `think_goods`
--

INSERT INTO `think_goods` (`id`, `sku`, `title`, `color`, `size`, `stock_total`, `stock_used`, `add_time`, `memo`) VALUES
(1, '001', 'sku1', 'red', 'M', 100, 0, 1426838558, ''),
(2, '002', 'sku2', '蓝色', '3', 20, 5, 1426838591, ''),
(3, '003', 'sku3', '红色', 'S', 50, 3, 1426840018, '11');

-- --------------------------------------------------------

--
-- Table structure for table `think_retailer`
--

CREATE TABLE IF NOT EXISTS `think_retailer` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `retailer_name` varchar(255) NOT NULL,
  `add_time` int(12) NOT NULL,
  `level` char(1) NOT NULL,
  `memo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `think_retailer`
--

INSERT INTO `think_retailer` (`id`, `retailer_name`, `add_time`, `level`, `memo`) VALUES
(1, '分销商1', 0, '3', '分销商1测试2'),
(2, '分销商2', 1426834240, '2', '分销商2测试'),
(3, '分销商11', 1426836462, '1', '1111111'),
(4, '分销商12', 1426836474, '1', '222'),
(5, '分销商13', 1426836487, '1', '333');

-- --------------------------------------------------------

--
-- Table structure for table `think_retailer2goods`
--

CREATE TABLE IF NOT EXISTS `think_retailer2goods` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `retailer_id` int(5) NOT NULL,
  `goods_id` int(12) NOT NULL,
  `amount` int(5) NOT NULL,
  `is_delete` char(1) NOT NULL DEFAULT '0',
  `add_time` int(12) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `think_retailer2goods`
--

INSERT INTO `think_retailer2goods` (`id`, `retailer_id`, `goods_id`, `amount`, `is_delete`, `add_time`) VALUES
(1, 1, 0, 10, '0', 0),
(2, 2, 0, 2, '0', 0),
(3, 1, 1, 6, '0', 0);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

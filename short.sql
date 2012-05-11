-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 11, 2012 at 07:58 AM
-- Server version: 5.5.24-log
-- PHP Version: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `short`
--

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE IF NOT EXISTS `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country` varchar(2) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `short_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `short_id` (`short_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=64 ;

-- --------------------------------------------------------

--
-- Table structure for table `short`
--

CREATE TABLE IF NOT EXISTS `short` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(10) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `url_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `url_id` (`url_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;

-- --------------------------------------------------------

--
-- Table structure for table `url`
--

CREATE TABLE IF NOT EXISTS `url` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(2000) NOT NULL DEFAULT '|',
  `host_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `host_id` (`host_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Table structure for table `url_host`
--

CREATE TABLE IF NOT EXISTS `url_host` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `host` varchar(256) NOT NULL DEFAULT '|',
  `port` int(5) NOT NULL DEFAULT '0',
  `scheme_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `scheme_id` (`scheme_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Table structure for table `url_scheme`
--

CREATE TABLE IF NOT EXISTS `url_scheme` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `scheme` varchar(256) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `log`
--
ALTER TABLE `log`
  ADD CONSTRAINT `log_ibfk_1` FOREIGN KEY (`short_id`) REFERENCES `short` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `short`
--
ALTER TABLE `short`
  ADD CONSTRAINT `short_ibfk_1` FOREIGN KEY (`url_id`) REFERENCES `url` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `url`
--
ALTER TABLE `url`
  ADD CONSTRAINT `url_ibfk_1` FOREIGN KEY (`host_id`) REFERENCES `url_host` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `url_host`
--
ALTER TABLE `url_host`
  ADD CONSTRAINT `url_host_ibfk_1` FOREIGN KEY (`scheme_id`) REFERENCES `url_scheme` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

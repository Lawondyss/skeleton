-- Adminer 4.1.0 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

CREATE DATABASE `skeleton` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `skeleton`;

CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL,
  `token` char(14) DEFAULT NULL,
  `confirm` char(14) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_email` (`email`),
  UNIQUE KEY `unique_token` (`token`),
  UNIQUE KEY `unique_confirm` (`confirm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- 2014-09-10 18:27:42

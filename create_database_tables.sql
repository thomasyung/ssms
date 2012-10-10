CREATE DATABASE `ssms` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `ssms`;

CREATE TABLE IF NOT EXISTS `messages` (
  `id` char(13) NOT NULL,
  `message` mediumtext NOT NULL,
  `viewed` tinyint(1) NOT NULL,
  `timestamp` text,
  `ipaddress` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
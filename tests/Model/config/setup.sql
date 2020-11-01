-- Adminer 4.7.7 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(60) COLLATE utf8mb4_czech_ci NOT NULL,
  `password` varchar(60) COLLATE utf8mb4_czech_ci NOT NULL,
  `type` varchar(20) COLLATE utf8mb4_czech_ci NOT NULL,
  `profile` text CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci NOT NULL,
  `metadata` text CHARACTER SET utf8mb4 COLLATE utf8mb4_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_czech_ci;

INSERT INTO `users` (`id`, `username`, `password`, `type`, `profile`, `metadata`) VALUES
(1,	'john.doe',	'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3',	'manager',	'{\r\n  \"array\": [\r\n    1,\r\n    2,\r\n    3\r\n  ],\r\n  \"boolean\": true,\r\n  \"color\": \"gold\",\r\n  \"null\": null,\r\n  \"number\": 123,\r\n  \"object\": {\r\n    \"a\": \"b\",\r\n    \"c\": \"d\"\r\n  },\r\n  \"string\": \"Hello World\"\r\n}',	'a:3:{s:7:\"created\";s:16:\"2020-10-28 12:04\";s:7:\"updated\";s:16:\"2020-11-01 11:21\";s:5:\"photo\";N;}');

-- 2020-11-01 10:57:00
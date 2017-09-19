-- Adminer 4.3.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `a_groups`;
CREATE TABLE `a_groups` (
  `group_id` varchar(255) NOT NULL,
  `group_name` varchar(255) NOT NULL,
  `group_username` varchar(255) DEFAULT NULL,
  `msg_count` bigint(20) NOT NULL,
  `max_warn` int(11) NOT NULL DEFAULT '3',
  `welcome_message` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `a_users`;
CREATE TABLE `a_users` (
  `userid` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `msg_count` bigint(20) NOT NULL,
  `private` enum('true','false') NOT NULL,
  `notification` enum('true','false') NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `groups_admin`;
CREATE TABLE `groups_admin` (
  `group_id` varchar(255) NOT NULL,
  `userid` varchar(255) NOT NULL,
  `status` enum('creator','admin') NOT NULL,
  `privileges` text NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  KEY `group_id` (`group_id`),
  KEY `userid` (`userid`),
  CONSTRAINT `groups_admin_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `a_groups` (`group_id`),
  CONSTRAINT `groups_admin_ibfk_2` FOREIGN KEY (`userid`) REFERENCES `a_users` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `group_messages`;
CREATE TABLE `group_messages` (
  `group_id` varchar(255) NOT NULL,
  `userid` varchar(255) NOT NULL,
  `message_uniq` varchar(255) NOT NULL,
  `message_id` bigint(20) NOT NULL,
  `type` enum('text','photo','sticker','video','unknown') NOT NULL DEFAULT 'unknown',
  `reply_to_message_id` bigint(20) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`message_uniq`),
  KEY `group_id` (`group_id`),
  KEY `userid` (`userid`),
  CONSTRAINT `group_messages_ibfk_2` FOREIGN KEY (`group_id`) REFERENCES `a_groups` (`group_id`),
  CONSTRAINT `group_messages_ibfk_3` FOREIGN KEY (`userid`) REFERENCES `a_users` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `group_messages_data`;
CREATE TABLE `group_messages_data` (
  `message_uniq` varchar(255) NOT NULL,
  `text` text,
  `file_id` varchar(255) DEFAULT NULL,
  KEY `message_uniq` (`message_uniq`),
  CONSTRAINT `group_messages_data_ibfk_1` FOREIGN KEY (`message_uniq`) REFERENCES `group_messages` (`message_uniq`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `private_messages`;
CREATE TABLE `private_messages` (
  `userid` varchar(255) NOT NULL,
  `message_uniq` varchar(255) NOT NULL,
  `message_id` bigint(20) NOT NULL,
  `type` enum('text','photo','sticker','video','unknown') NOT NULL DEFAULT 'unknown',
  `reply_to_message_id` bigint(20) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`message_uniq`),
  KEY `userid` (`userid`),
  KEY `message_id` (`message_id`),
  CONSTRAINT `private_messages_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `a_users` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `private_messages_data`;
CREATE TABLE `private_messages_data` (
  `message_uniq` varchar(255) NOT NULL,
  `text` text,
  `file_id` varchar(255) DEFAULT NULL,
  KEY `message_uniq` (`message_uniq`),
  CONSTRAINT `private_messages_data_ibfk_1` FOREIGN KEY (`message_uniq`) REFERENCES `private_messages` (`message_uniq`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `private_outbox`;
CREATE TABLE `private_outbox` (
  `userid` varchar(255) NOT NULL,
  `message_id` bigint(20) NOT NULL,
  `type` enum('text','photo','sticker','video','unknown') NOT NULL DEFAULT 'unknown',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  KEY `userid` (`userid`),
  CONSTRAINT `private_outbox_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `a_users` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `save_content`;
CREATE TABLE `save_content` (
  `content_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userid` varchar(255) NOT NULL,
  `chat_id` varchar(255) NOT NULL,
  `tag` varchar(255) NOT NULL,
  `type` enum('text','photo','sticker','video','unknown') NOT NULL,
  `chat_type` enum('private','group') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`content_id`),
  KEY `userid` (`userid`),
  CONSTRAINT `save_content_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `a_users` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `save_content_data`;
CREATE TABLE `save_content_data` (
  `content_id` bigint(20) NOT NULL,
  `text` text,
  `file_id` varchar(255) DEFAULT NULL,
  KEY `content_id` (`content_id`),
  CONSTRAINT `save_content_data_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `save_content` (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `user_warn`;
CREATE TABLE `user_warn` (
  `group_id` varchar(255) NOT NULL,
  `userid` varchar(255) NOT NULL,
  `reason` text,
  `warn_count` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  KEY `group_id` (`group_id`),
  KEY `userid` (`userid`),
  CONSTRAINT `user_warn_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `a_groups` (`group_id`),
  CONSTRAINT `user_warn_ibfk_2` FOREIGN KEY (`userid`) REFERENCES `a_users` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


-- 2017-09-19 17:33:22

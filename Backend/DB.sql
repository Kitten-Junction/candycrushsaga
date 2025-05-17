-- --------------------------------------------------------
-- Host:                         X
-- Server version:               8.0.42-0ubuntu0.22.04.1 - (Ubuntu)
-- Server OS:                    Linux
-- HeidiSQL Version:             12.10.0.7000
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for candycrush
CREATE DATABASE IF NOT EXISTS `candycrush` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `candycrush`;

-- Dumping structure for table candycrush.messages
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `type` varchar(50) NOT NULL,
  `data` text NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `processed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `type` (`type`),
  KEY `processed` (`processed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table candycrush.sugartrack
CREATE TABLE IF NOT EXISTS `sugartrack` (
  `id` int NOT NULL,
  `balance` int DEFAULT NULL,
  `cooldown` bigint DEFAULT '0',
  `cooldown_end` bigint DEFAULT '0',
  `sugartrack_nodes` json DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table candycrush.transactions
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `transaction_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned NOT NULL,
  `recipient_user_id` int unsigned DEFAULT NULL,
  `product_id` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` int unsigned NOT NULL,
  `status` enum('pending','completed','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `error_message` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `metadata` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_transaction_id` (`transaction_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_recipient_user_id` (`recipient_user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=1907 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data exporting was unselected.

-- Dumping structure for table candycrush.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT COMMENT 'The in-game user ID',
  `fbuid` int DEFAULT NULL COMMENT 'External SA ID',
  `lives` int DEFAULT '5' COMMENT 'The user total lives',
  `maxLives` int DEFAULT '5' COMMENT 'Maximum lives the user can regenerate',
  `gold` int DEFAULT '0' COMMENT 'User gold amount',
  `timeToNextRegeneration` datetime DEFAULT NULL COMMENT 'Time until next life regeneration',
  `CandyProperties` json NOT NULL COMMENT 'In-game properties (aka tutorial data)',
  `last_spin_time` datetime DEFAULT NULL COMMENT 'Booster wheel last spin time',
  `soundFx` tinyint(1) DEFAULT '1' COMMENT 'Sound FX toggled?',
  `soundMusic` tinyint(1) DEFAULT '1' COMMENT 'Sound music toggled?',
  `immortal` tinyint(1) DEFAULT '0' COMMENT 'Is user immortal?',
  `currency` varchar(3) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'USD' COMMENT 'User currency, not important',
  `kingSessionKey` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Kingdom session key',
  `oauth_token` varchar(855) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Facebook OAuth token',
  `deviceId` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Device identification (used for mobile connected)',
  `country` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'User country, not important',
  `language` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'User language, not important',
  `createdAt` datetime NOT NULL COMMENT 'User account creation date',
  `lastLogin` datetime NOT NULL,
  `signInCount` int DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `selectedAvatar` int DEFAULT '1',
  `email` varchar(255) DEFAULT NULL,
  `facebookSessionKey` varchar(255) DEFAULT NULL,
  `deliveredSB` int DEFAULT '0',
  `wheel_spin_streak` int DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `fbuid` (`fbuid`),
  KEY `deviceId` (`deviceId`),
  KEY `kingSessionKey` (`kingSessionKey`)
) ENGINE=InnoDB AUTO_INCREMENT=319 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

-- Dumping structure for table candycrush.user_items
CREATE TABLE IF NOT EXISTS `user_items` (
  `uid` int NOT NULL,
  `items` longtext,
  `vanity_items` json DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Data exporting was unselected.

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

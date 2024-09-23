-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.0.30 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table font-group.fonts
CREATE TABLE IF NOT EXISTS `fonts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `font_name` varchar(255) NOT NULL,
  `font_path` varchar(255) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table font-group.fonts: ~11 rows (approximately)
INSERT INTO `fonts` (`id`, `font_name`, `font_path`, `status`) VALUES
	(2, 'kalpurush.ttf', 'E:\\laragon\\www\\Zepto_font_group_system/fonts/kalpurush.ttf', 0),
	(5, 'Li Abu J M Akkas ANSI V1 Italic.ttf', 'E:\\laragon\\www\\Zepto_font_group_system/fonts/Li Abu J M Akkas ANSI V1 Italic.ttf', 0),
	(6, 'MouldyCheeseRegular-WyMWG.ttf', 'E:\\laragon\\www\\Zepto_font_group_system/fonts/MouldyCheeseRegular-WyMWG.ttf', 0),
	(7, 'WeddingdayPersonalUseRegular-1Gvo0.ttf', 'E:\\laragon\\www\\Zepto_font_group_system/fonts/WeddingdayPersonalUseRegular-1Gvo0.ttf', 0),
	(8, 'kalpurush.ttf', 'E:\\laragon\\www\\Zepto_font_group_system/fonts/kalpurush.ttf', 0),
	(9, 'kalpurush.ttf', 'E:\\laragon\\www\\Zepto_font_group_system/fonts/kalpurush.ttf', 0),
	(10, 'kalpurush.ttf', 'E:\\laragon\\www\\Zepto_font_group_system/fonts/kalpurush.ttf', 0),
	(11, 'kalpurush.ttf', 'E:\\laragon\\www\\Zepto_font_group_system/fonts/kalpurush.ttf', 1),
	(12, 'kalpurush.ttf', 'E:\\laragon\\www\\Zepto_font_group_system/fonts/kalpurush.ttf', 0),
	(13, 'kalpurush.ttf', 'E:\\laragon\\www\\Zepto_font_group_system/fonts/kalpurush.ttf', 0),
	(14, 'SofadiOne-Regular', 'E:\\laragon\\www\\Zepto_font_group_system/fonts/SofadiOne-Regular.ttf', 1),
	(15, 'Montserrat-VariableFont_wght', 'E:\\laragon\\www\\Zepto_font_group_system/fonts/Montserrat-VariableFont_wght.ttf', 1);

-- Dumping structure for table font-group.font_groups
CREATE TABLE IF NOT EXISTS `font_groups` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `total_fonts` int NOT NULL,
  `status` tinyint NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table font-group.font_groups: ~9 rows (approximately)
INSERT INTO `font_groups` (`id`, `name`, `total_fonts`, `status`, `created_at`, `updated_at`) VALUES
	(1, 'Brenden Gillespie', 2, 1, '2024-09-23 06:22:53', '2024-09-23 06:22:53'),
	(2, 'Test', 2, 1, '2024-09-23 06:32:14', '2024-09-23 06:32:14'),
	(3, 'Test', 2, 0, '2024-09-23 06:33:09', '2024-09-23 10:57:24'),
	(4, 'Test', 2, 0, '2024-09-23 06:34:01', '2024-09-23 10:57:59'),
	(5, 'Group 1', 2, 0, '2024-09-23 09:52:51', '2024-09-23 10:57:14'),
	(6, 'Group 1', 2, 0, '2024-09-23 09:52:58', '2024-09-23 10:56:19'),
	(7, 'Group 1', 2, 0, '2024-09-23 11:01:01', '2024-09-23 11:01:34'),
	(8, 'Group 2', 2, 0, '2024-09-23 11:03:15', '2024-09-23 11:21:11'),
	(9, 'Hop Kent', 2, 1, '2024-09-23 11:25:17', '2024-09-23 11:25:17');

-- Dumping structure for table font-group.font_group_fonts
CREATE TABLE IF NOT EXISTS `font_group_fonts` (
  `group_id` int NOT NULL,
  `font_id` int NOT NULL,
  PRIMARY KEY (`group_id`,`font_id`),
  KEY `font_id` (`font_id`),
  CONSTRAINT `font_group_fonts_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `font_groups` (`id`),
  CONSTRAINT `font_group_fonts_ibfk_2` FOREIGN KEY (`font_id`) REFERENCES `fonts` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Dumping data for table font-group.font_group_fonts: ~13 rows (approximately)
INSERT INTO `font_group_fonts` (`group_id`, `font_id`) VALUES
	(4, 11),
	(7, 11),
	(8, 11),
	(9, 11),
	(3, 13),
	(4, 13),
	(5, 13),
	(6, 13),
	(5, 14),
	(6, 14),
	(7, 14),
	(8, 14),
	(9, 14);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;

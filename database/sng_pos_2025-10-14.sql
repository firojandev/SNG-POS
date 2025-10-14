# ************************************************************
# Sequel Ace SQL dump
# Version 20095
#
# https://sequel-ace.com/
# https://github.com/Sequel-Ace/Sequel-Ace
#
# Host: 127.0.0.1 (MySQL 5.7.39)
# Database: sng_pos
# Generation Time: 2025-10-14 16:40:05 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE='NO_AUTO_VALUE_ON_ZERO', SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table cache
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cache`;

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table cache_locks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cache_locks`;

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table categories
# ------------------------------------------------------------

DROP TABLE IF EXISTS `categories`;

CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;

INSERT INTO `categories` (`id`, `store_id`, `name`, `created_at`, `updated_at`, `deleted_at`)
VALUES
	(1,'1','Laptop','2025-09-27 08:09:35','2025-09-27 08:09:35',NULL),
	(5,'1','Desktop','2025-09-27 08:58:25','2025-09-27 08:58:49',NULL),
	(6,'1','Keyboard','2025-09-27 08:58:57','2025-09-27 08:58:57',NULL),
	(7,'1','Test','2025-10-11 15:07:47','2025-10-11 15:07:47',NULL);

/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table currencies
# ------------------------------------------------------------

DROP TABLE IF EXISTS `currencies`;

CREATE TABLE `currencies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `symbol` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `currencies_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `currencies` WRITE;
/*!40000 ALTER TABLE `currencies` DISABLE KEYS */;

INSERT INTO `currencies` (`id`, `store_id`, `name`, `symbol`, `created_at`, `updated_at`)
VALUES
	(1,NULL,'USD','$','2025-10-05 04:58:29','2025-10-05 04:58:29'),
	(2,NULL,'EUR','€','2025-10-05 04:58:29','2025-10-05 04:58:29'),
	(3,NULL,'BDT','৳','2025-10-05 04:58:29','2025-10-05 04:58:29'),
	(4,NULL,'GBP','£','2025-10-05 04:58:59','2025-10-05 04:58:59'),
	(5,NULL,'JPY','¥','2025-10-05 04:58:59','2025-10-05 04:58:59');

/*!40000 ALTER TABLE `currencies` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table customers
# ------------------------------------------------------------

DROP TABLE IF EXISTS `customers`;

CREATE TABLE `customers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;

INSERT INTO `customers` (`id`, `uuid`, `store_id`, `name`, `phone`, `email`, `address`, `photo`, `is_active`, `created_at`, `updated_at`, `deleted_at`)
VALUES
	(1,NULL,NULL,'Rakib Uddin','01917948316','rakib@gmail.com','Jhenaidah Sadar','customers/zK1qr5N6ezeHT9U31ldN6bJ3OOxv8akS1b4yQn9l.jpg',1,'2025-10-11 04:55:33','2025-10-11 04:57:05',NULL),
	(2,NULL,NULL,'Josim Uddin','01917381122','josim@gmail.com','Jhenaidah Sadar','customers/4wAD3F9QLWVzVab7OHYZU1oxV8GURlkqBPjdPRB9.png',1,'2025-10-11 04:56:00','2025-10-11 04:56:52',NULL),
	(3,NULL,NULL,'dd','dddd344444','ddd@ffff.com','ddd',NULL,1,'2025-10-11 04:57:22','2025-10-11 04:57:35','2025-10-11 04:57:35');

/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table expense_categories
# ------------------------------------------------------------

DROP TABLE IF EXISTS `expense_categories`;

CREATE TABLE `expense_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `expense_categories` WRITE;
/*!40000 ALTER TABLE `expense_categories` DISABLE KEYS */;

INSERT INTO `expense_categories` (`id`, `store_id`, `name`, `created_at`, `updated_at`, `deleted_at`)
VALUES
	(2,'1','House Rant','2025-10-12 04:45:37','2025-10-12 04:45:37',NULL),
	(3,'1','Salary','2025-10-12 04:46:38','2025-10-12 04:46:38',NULL);

/*!40000 ALTER TABLE `expense_categories` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table expenses
# ------------------------------------------------------------

DROP TABLE IF EXISTS `expenses`;

CREATE TABLE `expenses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expense_category_id` bigint(20) unsigned NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `date` date NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expenses_expense_category_id_foreign` (`expense_category_id`),
  CONSTRAINT `expenses_expense_category_id_foreign` FOREIGN KEY (`expense_category_id`) REFERENCES `expense_categories` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;

INSERT INTO `expenses` (`id`, `store_id`, `expense_category_id`, `amount`, `date`, `note`, `created_at`, `updated_at`, `deleted_at`)
VALUES
	(1,'1',2,1000.00,'2025-10-14','Test','2025-10-14 10:08:35','2025-10-14 10:08:35',NULL),
	(3,'1',3,10000.00,'2025-10-15','Note here','2025-10-14 10:25:11','2025-10-14 10:25:11',NULL);

/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table failed_jobs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `failed_jobs`;

CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table job_batches
# ------------------------------------------------------------

DROP TABLE IF EXISTS `job_batches`;

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table jobs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `jobs`;

CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table migrations
# ------------------------------------------------------------

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;

INSERT INTO `migrations` (`id`, `migration`, `batch`)
VALUES
	(1,'0001_01_01_000000_create_users_table',1),
	(2,'0001_01_01_000001_create_cache_table',1),
	(3,'0001_01_01_000002_create_jobs_table',1),
	(4,'2025_09_27_080249_create_categories_table',2),
	(5,'2025_09_27_153420_create_units_table',3),
	(6,'2025_09_28_062450_create_taxes_table',4),
	(7,'2025_10_02_064518_add_store_id_to_users_table',5),
	(8,'2025_10_02_064522_create_products_table',5),
	(9,'2025_10_02_093129_add_soft_deletes_to_products_table',6),
	(10,'2025_10_05_045502_create_options_table',7),
	(11,'2025_10_05_045507_create_currencies_table',7),
	(12,'2025_10_06_045141_add_profile_fields_to_users_table',8),
	(13,'2025_10_07_052151_create_stores_table',9),
	(14,'2025_10_09_000000_add_designation_to_users_table',10),
	(15,'2025_10_09_091923_add_deleted_at_to_users_table',11),
	(16,'2025_10_09_100000_create_suppliers_table',12),
	(17,'2025_10_11_044153_create_customers_table',13),
	(18,'2025_10_12_043547_create_expense_categories_table',14),
	(19,'2025_10_14_095608_create_expenses_table',15),
	(20,'2025_10_14_000000_add_store_id_to_core_tables',16),
	(21,'2025_10_14_000001_add_uuid_to_core_models',16);

/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table options
# ------------------------------------------------------------

DROP TABLE IF EXISTS `options`;

CREATE TABLE `options` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `options_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `options` WRITE;
/*!40000 ALTER TABLE `options` DISABLE KEYS */;

INSERT INTO `options` (`id`, `key`, `value`, `created_at`, `updated_at`)
VALUES
	(1,'app_currency','€','2025-10-05 04:58:29','2025-10-14 10:16:31'),
	(2,'app_name','SNG POS','2025-10-05 04:58:59','2025-10-05 04:58:59'),
	(3,'app_address','Dhaka Bangladesh','2025-10-05 04:58:59','2025-10-05 05:35:31'),
	(4,'app_phone','01917948316','2025-10-05 04:58:59','2025-10-05 05:34:51'),
	(5,'date_format','d/m/Y','2025-10-05 04:58:59','2025-10-05 05:35:31'),
	(6,'app_logo','settings/mAKwS1YCZVDVVbMdysX4KoLvoK255AWoZzNpN82M.jpg','2025-10-05 04:58:59','2025-10-05 06:17:36'),
	(7,'app_favicon','settings/pUslLE1eChwBFy0PnthZoIv214zesKxWjVYr7oo3.png','2025-10-05 04:58:59','2025-10-05 05:35:16');

/*!40000 ALTER TABLE `options` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table password_reset_tokens
# ------------------------------------------------------------

DROP TABLE IF EXISTS `password_reset_tokens`;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table products
# ------------------------------------------------------------

DROP TABLE IF EXISTS `products`;

CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sku` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `purchase_price` decimal(10,2) NOT NULL,
  `sell_price` decimal(10,2) NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `stock_quantity` int(11) NOT NULL DEFAULT '0',
  `store_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `unit_id` bigint(20) unsigned DEFAULT NULL,
  `tax_id` bigint(20) unsigned DEFAULT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_sku_unique` (`sku`),
  KEY `products_category_id_foreign` (`category_id`),
  KEY `products_unit_id_foreign` (`unit_id`),
  KEY `products_tax_id_foreign` (`tax_id`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_tax_id_foreign` FOREIGN KEY (`tax_id`) REFERENCES `taxes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;

INSERT INTO `products` (`id`, `uuid`, `name`, `sku`, `purchase_price`, `sell_price`, `description`, `stock_quantity`, `store_id`, `category_id`, `unit_id`, `tax_id`, `image`, `is_active`, `created_at`, `updated_at`, `deleted_at`)
VALUES
	(1,NULL,'Sample Product 1','P000001',100.00,150.00,'Sample product description',50,'1',1,3,2,'products/1759388667_mtFfqw0YbY.png',1,'2025-10-02 06:58:44','2025-10-02 07:04:27',NULL),
	(2,NULL,'Sample Product 2','P000002',200.00,300.00,'Another sample product',25,'1',1,3,2,NULL,1,'2025-10-02 06:58:44','2025-10-03 06:09:02','2025-10-03 06:09:02'),
	(3,NULL,'Sample Product 3','P000003',50.00,75.00,'Food item sample',100,'1',NULL,2,NULL,NULL,1,'2025-10-02 06:58:44','2025-10-02 09:42:31',NULL),
	(4,NULL,'Product 1','PRO249072',100.00,150.00,NULL,10,'1',6,1,2,NULL,1,'2025-10-02 08:21:10','2025-10-02 08:21:32',NULL),
	(5,NULL,'Product 2','PRO843747',200.00,250.00,'Test',10,'1',1,1,2,'products/1759393876_m6FLxjWehg.png',1,'2025-10-02 08:31:16','2025-10-02 08:31:16',NULL),
	(6,NULL,'Sample Product 1','P0000045',100.00,150.00,'Sample product description',50,'1',NULL,NULL,NULL,NULL,1,'2025-10-02 09:45:37','2025-10-02 09:45:37',NULL),
	(7,NULL,'Sample Product 2','P0000040',200.00,300.00,'Another sample product',25,'1',NULL,NULL,NULL,NULL,1,'2025-10-02 09:45:37','2025-10-02 09:45:37',NULL),
	(8,NULL,'Sample Product 3','P0000033',50.00,75.00,'Food item sample',100,'1',NULL,2,NULL,NULL,1,'2025-10-02 09:45:37','2025-10-03 06:08:32',NULL),
	(9,NULL,'Test','TES989540',20.00,50.00,'Test',40,'1',1,2,2,NULL,1,'2025-10-03 06:13:31','2025-10-03 06:38:04','2025-10-03 06:38:04'),
	(10,NULL,'Mac 20','MAC495562',10000.00,12000.00,'Test ddd',10,'1',1,1,2,'products/1759473556_bg2Om1etgJ.png',1,'2025-10-03 06:39:16','2025-10-03 06:39:29',NULL),
	(11,NULL,'ddddd','DDD990780',22.00,33.00,'eee',10,'1',5,1,2,NULL,1,'2025-10-03 06:46:44','2025-10-03 06:46:49','2025-10-03 06:46:49');

/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table sessions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sessions`;

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table stores
# ------------------------------------------------------------

DROP TABLE IF EXISTS `stores`;

CREATE TABLE `stores` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `details` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stores_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `stores` WRITE;
/*!40000 ALTER TABLE `stores` DISABLE KEYS */;

INSERT INTO `stores` (`id`, `name`, `contact_person`, `phone_number`, `address`, `email`, `details`, `is_active`, `created_at`, `updated_at`, `deleted_at`)
VALUES
	(1,'Devintime101','Rakib Uddin','029844744444','Dhaka Bangladesh','devintime@gmail.com','Good for selling',1,'2025-10-07 05:29:02','2025-10-07 05:31:16',NULL),
	(2,'dsd','dsdssd','094874774444444','dddd dsddddddd ddddd','ddd@ffff.com','ddd',0,'2025-10-07 05:30:13','2025-10-07 05:31:04','2025-10-07 05:31:04'),
	(3,'MBPOS','Ali Reza','2094884474444','Dhaka Bangladesh','reza@gmail.com','iPhone sell Center',1,'2025-10-07 05:45:55','2025-10-07 05:45:55',NULL);

/*!40000 ALTER TABLE `stores` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table suppliers
# ------------------------------------------------------------

DROP TABLE IF EXISTS `suppliers`;

CREATE TABLE `suppliers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `photo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `about` text COLLATE utf8mb4_unicode_ci,
  `balance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;

INSERT INTO `suppliers` (`id`, `uuid`, `store_id`, `name`, `contact_person`, `phone`, `email`, `address`, `photo`, `about`, `balance`, `is_active`, `created_at`, `updated_at`, `deleted_at`)
VALUES
	(1,NULL,NULL,'Alif Conputers','Jakir Hossen','1093333','alif@gmail.com','Address here','suppliers/rH7arxp04VTCFrEHZaXokPAUCu6iKY6pciKcU15L.png','Test ddd',0.00,1,'2025-10-09 09:59:56','2025-10-09 10:02:50',NULL),
	(2,NULL,NULL,'Test','wwwdeee','1235555555',NULL,'eeee',NULL,'eeeee',0.00,1,'2025-10-09 10:03:31','2025-10-09 10:03:31',NULL);

/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table taxes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `taxes`;

CREATE TABLE `taxes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` decimal(5,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `taxes` WRITE;
/*!40000 ALTER TABLE `taxes` DISABLE KEYS */;

INSERT INTO `taxes` (`id`, `store_id`, `name`, `value`, `created_at`, `updated_at`, `deleted_at`)
VALUES
	(1,'1','10 % Vat',10.00,'2025-09-28 06:31:26','2025-09-28 06:31:26',NULL),
	(2,'1','No Tax',0.00,'2025-09-28 06:31:40','2025-09-28 06:31:40',NULL);

/*!40000 ALTER TABLE `taxes` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table units
# ------------------------------------------------------------

DROP TABLE IF EXISTS `units`;

CREATE TABLE `units` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `units` WRITE;
/*!40000 ALTER TABLE `units` DISABLE KEYS */;

INSERT INTO `units` (`id`, `store_id`, `name`, `created_at`, `updated_at`, `deleted_at`)
VALUES
	(1,'1','Pic','2025-09-27 15:43:35','2025-09-27 15:43:35',NULL),
	(2,'1','KG','2025-09-27 15:43:41','2025-09-27 15:43:41',NULL),
	(3,'1','Pices','2025-09-27 15:43:57','2025-09-27 15:43:57',NULL);

/*!40000 ALTER TABLE `units` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `designation` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`id`, `store_id`, `name`, `email`, `phone`, `designation`, `address`, `avatar`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `deleted_at`)
VALUES
	(1,'1','Rakib Uddin','admin@gmail.com','(019) 434-44444',NULL,'Dhaka Bangladesh','avatars/PGh892ub3rBiQVt1ifT1ky8l8WR8ZSMQo2zrxCLa.jpg',NULL,'$2y$12$n8y6ORPtd2AE.t1eerz8aO3DkRWYfTNMQbWTYX6ihC3IvUQ./d0.u',NULL,'2025-09-23 15:47:30','2025-10-06 05:25:16',NULL),
	(2,'3','Josim Uddin','josim@gmail.com','45778888888','Cashier','Dhaka Bangladesh','avatars/QBo2sNcBiwaD0piVBlsQ5piSzGBTW8KJavbSPMl3.png',NULL,'$2y$12$RMjDucFJzCJoFjOwyMDn/uktg0IYeQv4YB1Zi7nDIbGRLnzP1l/e2',NULL,'2025-10-07 06:47:40','2025-10-09 09:16:14',NULL),
	(4,'1','TR','tr@gg.com','1234555','Manager','Test','avatars/doYdWvqxl8RRv2bqYKLWvztnGmoqY2R5GGOsx9Is.png',NULL,'$2y$12$0g1NtsWC95dzMHWlEkKgdOBROO0Xj3LkGNcnmBo.yU6em/bOGltn6',NULL,'2025-10-09 09:34:20','2025-10-09 09:34:46','2025-10-09 09:34:46');

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

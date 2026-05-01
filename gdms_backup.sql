-- MySQL dump 10.13  Distrib 9.6.0, for macos26.2 (arm64)
--
-- Host: 127.0.0.1    Database: gdms
-- ------------------------------------------------------
-- Server version	9.6.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- GTID state at the beginning of the backup 
--

SET @@GLOBAL.GTID_PURGED=/*!80000 '+'*/ 'c4ed345e-1d05-11f1-b0ec-6d813db0bc9b:1-62906';

--
-- Table structure for table `approvals`
--

DROP TABLE IF EXISTS `approvals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `approvals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `approvable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `approvable_id` bigint unsigned NOT NULL,
  `action` enum('approved','queried') COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approvals_approvable_type_approvable_id_index` (`approvable_type`,`approvable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `approvals`
--

LOCK TABLES `approvals` WRITE;
/*!40000 ALTER TABLE `approvals` DISABLE KEYS */;
INSERT INTO `approvals` VALUES (1,'App\\Models\\CashSubmission',1,'approved',NULL,'2026-04-17 07:54:38','2026-04-17 07:54:38'),(2,'App\\Models\\CashSubmission',2,'approved',NULL,'2026-04-17 07:55:12','2026-04-17 07:55:12'),(3,'App\\Models\\Sale',1,'approved',NULL,'2026-04-17 07:55:33','2026-04-17 07:55:33'),(4,'App\\Models\\Sale',2,'approved',NULL,'2026-04-17 08:29:08','2026-04-17 08:29:08'),(5,'App\\Models\\Sale',3,'approved',NULL,'2026-04-29 19:46:09','2026-04-29 19:46:09'),(6,'App\\Models\\CashSubmission',3,'approved',NULL,'2026-04-29 19:46:40','2026-04-29 19:46:40');
/*!40000 ALTER TABLE `approvals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asset_categories`
--

DROP TABLE IF EXISTS `asset_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asset_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_depreciable` tinyint(1) NOT NULL DEFAULT '0',
  `default_depreciation_rate` decimal(5,2) DEFAULT NULL,
  `useful_life_years` int DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_categories`
--

LOCK TABLES `asset_categories` WRITE;
/*!40000 ALTER TABLE `asset_categories` DISABLE KEYS */;
INSERT INTO `asset_categories` VALUES (1,'Office Equipment','Computers, printers, etc.',0,25.00,4,1,'2026-04-16 12:55:20','2026-04-16 12:55:20'),(2,'Furniture','Desks, chairs, etc.',0,10.00,10,1,'2026-04-16 12:55:20','2026-04-16 12:55:20'),(3,'Vehicles','Company vehicles',0,20.00,5,1,'2026-04-16 12:55:20','2026-04-16 12:55:20'),(4,'Plant & Machinery','Production equipment',0,15.00,7,1,'2026-04-16 12:55:20','2026-04-16 12:55:20'),(5,'Building','Buildings and structures',0,5.00,20,1,'2026-04-16 12:55:20','2026-04-16 12:55:20');
/*!40000 ALTER TABLE `asset_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `asset_disposals`
--

DROP TABLE IF EXISTS `asset_disposals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `asset_disposals` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` bigint unsigned NOT NULL,
  `disposal_date` date NOT NULL,
  `method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `book_value_at_disposal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `sale_proceeds` decimal(12,2) NOT NULL DEFAULT '0.00',
  `gain_loss` decimal(12,2) NOT NULL DEFAULT '0.00',
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `asset_disposals_asset_id_foreign` (`asset_id`),
  CONSTRAINT `asset_disposals_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `asset_disposals`
--

LOCK TABLES `asset_disposals` WRITE;
/*!40000 ALTER TABLE `asset_disposals` DISABLE KEYS */;
/*!40000 ALTER TABLE `asset_disposals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assets`
--

DROP TABLE IF EXISTS `assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `assets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `asset_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `asset_category_id` bigint unsigned NOT NULL,
  `serial_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plate_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `accumulated_depreciation` decimal(12,2) NOT NULL DEFAULT '0.00',
  `current_book_value` decimal(12,2) NOT NULL DEFAULT '0.00',
  `depreciation_rate` decimal(5,2) DEFAULT NULL,
  `assigned_to_outlet` bigint unsigned DEFAULT NULL,
  `assigned_to_employee` bigint unsigned DEFAULT NULL,
  `status` enum('active','disposed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `assets_asset_number_unique` (`asset_number`),
  KEY `assets_asset_category_id_foreign` (`asset_category_id`),
  KEY `assets_assigned_to_outlet_foreign` (`assigned_to_outlet`),
  KEY `assets_assigned_to_employee_foreign` (`assigned_to_employee`),
  CONSTRAINT `assets_asset_category_id_foreign` FOREIGN KEY (`asset_category_id`) REFERENCES `asset_categories` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `assets_assigned_to_employee_foreign` FOREIGN KEY (`assigned_to_employee`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `assets_assigned_to_outlet_foreign` FOREIGN KEY (`assigned_to_outlet`) REFERENCES `outlets` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assets`
--

LOCK TABLES `assets` WRITE;
/*!40000 ALTER TABLE `assets` DISABLE KEYS */;
INSERT INTO `assets` VALUES (1,'AST-2026-0001','TOYOTA CENTRE',3,NULL,'T 768 BEC','2024-02-01',50000000.00,2221922.05,47778077.95,2.00,1,NULL,'active','2026-05-01 05:09:18','2026-05-01 08:32:57');
/*!40000 ALTER TABLE `assets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cash_submissions`
--

DROP TABLE IF EXISTS `cash_submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cash_submissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sale_id` bigint unsigned NOT NULL,
  `expected_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `submitted_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `variance` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` enum('pending','approved','queried','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `receipt_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cash_submissions_reference_unique` (`reference`),
  KEY `cash_submissions_sale_id_foreign` (`sale_id`),
  CONSTRAINT `cash_submissions_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cash_submissions`
--

LOCK TABLES `cash_submissions` WRITE;
/*!40000 ALTER TABLE `cash_submissions` DISABLE KEYS */;
INSERT INTO `cash_submissions` VALUES (1,'CS-20260417-0001',1,50000.00,50000.00,0.00,'approved',NULL,NULL,'2026-04-17 07:54:20','2026-04-17 07:54:38'),(2,'CS-20260417-0002',1,50000.00,50000.00,0.00,'approved',NULL,NULL,'2026-04-17 07:55:05','2026-04-17 07:55:12'),(3,'CS-20260429-0003',3,25000.00,25000.00,0.00,'approved',NULL,NULL,'2026-04-29 19:45:51','2026-04-29 19:46:40');
/*!40000 ALTER TABLE `cash_submissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('walk_in','regular','wholesale') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'walk_in',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cylinder_types`
--

DROP TABLE IF EXISTS `cylinder_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cylinder_types` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size_kg` int NOT NULL,
  `full_sale_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `full_sale_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `refill_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `refill_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cylinder_types`
--

LOCK TABLES `cylinder_types` WRITE;
/*!40000 ALTER TABLE `cylinder_types` DISABLE KEYS */;
INSERT INTO `cylinder_types` VALUES (1,'6kg',6,75500.00,150000.00,125000.00,25000.00,1,'2026-04-16 12:55:20','2026-04-16 22:03:04'),(2,'13kg',13,45000.00,150000.00,15000.00,25000.00,1,'2026-04-16 12:55:20','2026-04-17 08:26:31'),(3,'50kg',50,150000.00,150000.00,30000.00,25000.00,1,'2026-04-16 12:55:20','2026-04-17 08:26:31');
/*!40000 ALTER TABLE `cylinder_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `depreciation_logs`
--

DROP TABLE IF EXISTS `depreciation_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `depreciation_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` bigint unsigned NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `book_value_before` decimal(12,2) NOT NULL DEFAULT '0.00',
  `depreciation_rate` decimal(5,2) NOT NULL DEFAULT '0.00',
  `depreciation_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `book_value_after` decimal(12,2) NOT NULL DEFAULT '0.00',
  `run_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `depreciation_logs_asset_id_foreign` (`asset_id`),
  CONSTRAINT `depreciation_logs_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `depreciation_logs`
--

LOCK TABLES `depreciation_logs` WRITE;
/*!40000 ALTER TABLE `depreciation_logs` DISABLE KEYS */;
INSERT INTO `depreciation_logs` VALUES (28,1,'2024-03-01','2024-03-31',50000000.00,2.00,84107.13,49915892.87,'System','2026-05-01 08:32:57','2026-05-01 08:32:57'),(29,1,'2024-04-01','2024-04-30',49915892.87,2.00,83965.65,49831927.22,'System','2026-05-01 08:32:57','2026-05-01 08:32:57'),(30,1,'2024-05-01','2024-05-31',49831927.22,2.00,83824.41,49748102.82,'System','2026-05-01 08:32:57','2026-05-01 08:32:57'),(31,1,'2024-06-01','2024-06-30',49748102.82,2.00,83683.40,49664419.42,'System','2026-05-01 08:32:57','2026-05-01 08:32:57'),(32,1,'2024-07-01','2024-07-31',49664419.42,2.00,83542.63,49580876.79,'System','2026-05-01 08:32:57','2026-05-01 08:32:57'),(33,1,'2024-08-01','2024-08-31',49580876.79,2.00,83402.10,49497474.68,'System','2026-05-01 08:32:57','2026-05-01 08:32:57'),(34,1,'2024-09-01','2024-09-30',49497474.68,2.00,83261.81,49414212.87,'System','2026-05-01 08:32:57','2026-05-01 08:32:57'),(35,1,'2024-10-01','2024-10-31',49414212.87,2.00,83121.75,49331091.12,'System','2026-05-01 08:32:57','2026-05-01 08:32:57'),(36,1,'2024-11-01','2024-11-30',49331091.12,2.00,82981.93,49248109.20,'System','2026-05-01 08:32:57','2026-05-01 08:32:57'),(37,1,'2024-12-01','2024-12-31',49248109.20,2.00,82842.34,49165266.86,'System','2026-05-01 08:32:57','2026-05-01 08:32:57'),(38,1,'2025-01-01','2025-01-31',49165266.86,2.00,82702.99,49082563.87,'System','2026-05-01 08:32:57','2026-05-01 08:32:57'),(39,1,'2025-02-01','2025-02-28',49082563.87,2.00,82563.87,49000000.00,'System','2026-05-01 08:32:57','2026-05-01 08:32:57'),(40,1,'2025-03-01','2025-03-31',49000000.00,2.00,82424.99,48917575.01,'System','2026-05-01 08:32:57','2026-05-01 08:32:57'),(41,1,'2025-04-01','2025-04-30',48917575.01,2.00,82286.33,48835288.68,'System','2026-05-01 08:32:57','2026-05-01 08:32:57'),(42,1,'2025-05-01','2025-05-31',48835288.68,2.00,82147.92,48753140.76,'System','2026-05-01 08:32:57','2026-05-01 08:32:57'),(43,1,'2025-06-01','2025-06-30',48753140.76,2.00,82009.73,48671131.03,'System','2026-05-01 08:32:57','2026-05-01 08:32:57'),(44,1,'2025-07-01','2025-07-31',48671131.03,2.00,81871.78,48589259.25,'System','2026-05-01 08:32:57','2026-05-01 08:32:57'),(45,1,'2025-08-01','2025-08-31',48589259.25,2.00,81734.06,48507525.19,'System','2026-05-01 08:32:57','2026-05-01 08:32:57'),(46,1,'2025-09-01','2025-09-30',48507525.19,2.00,81596.57,48425928.62,'System','2026-05-01 08:32:57','2026-05-01 08:32:57'),(47,1,'2025-10-01','2025-10-31',48425928.62,2.00,81459.32,48344469.30,'System','2026-05-01 08:32:57','2026-05-01 08:32:57'),(48,1,'2025-11-01','2025-11-30',48344469.30,2.00,81322.29,48263147.01,'System','2026-05-01 08:32:57','2026-05-01 08:32:57'),(49,1,'2025-12-01','2025-12-31',48263147.01,2.00,81185.49,48181961.52,'System','2026-05-01 08:32:57','2026-05-01 08:32:57'),(50,1,'2026-01-01','2026-01-31',48181961.52,2.00,81048.93,48100912.59,'System','2026-05-01 08:32:57','2026-05-01 08:32:57'),(51,1,'2026-02-01','2026-02-28',48100912.59,2.00,80912.59,48020000.00,'System','2026-05-01 08:32:57','2026-05-01 08:32:57'),(52,1,'2026-03-01','2026-03-31',48020000.00,2.00,80776.49,47939223.51,'System','2026-05-01 08:32:57','2026-05-01 08:32:57'),(53,1,'2026-04-01','2026-04-30',47939223.51,2.00,80640.61,47858582.91,'System','2026-05-01 08:32:57','2026-05-01 08:32:57'),(54,1,'2026-05-01','2026-05-31',47858582.91,2.00,80504.96,47778077.95,'System','2026-05-01 08:32:57','2026-05-01 08:32:57');
/*!40000 ALTER TABLE `depreciation_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `employee_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `outlet_id` bigint unsigned DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `basic_salary` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('active','inactive','terminated') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `employees_employee_number_unique` (`employee_number`),
  KEY `employees_outlet_id_foreign` (`outlet_id`),
  CONSTRAINT `employees_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,'EMP-001','EMMANUEL','LOTA',NULL,'0699130318',NULL,NULL,NULL,NULL,200000.00,'active','2026-05-01 09:55:21','2026-05-01 09:55:21');
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empty_return_items`
--

DROP TABLE IF EXISTS `empty_return_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empty_return_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `empty_return_id` bigint unsigned NOT NULL,
  `cylinder_type_id` bigint unsigned NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `empty_return_items_empty_return_id_foreign` (`empty_return_id`),
  KEY `empty_return_items_cylinder_type_id_foreign` (`cylinder_type_id`),
  CONSTRAINT `empty_return_items_cylinder_type_id_foreign` FOREIGN KEY (`cylinder_type_id`) REFERENCES `cylinder_types` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `empty_return_items_empty_return_id_foreign` FOREIGN KEY (`empty_return_id`) REFERENCES `empty_returns` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empty_return_items`
--

LOCK TABLES `empty_return_items` WRITE;
/*!40000 ALTER TABLE `empty_return_items` DISABLE KEYS */;
INSERT INTO `empty_return_items` VALUES (1,1,1,2,'2026-04-29 20:01:32','2026-04-29 20:01:32'),(2,2,1,2,'2026-04-30 20:03:55','2026-04-30 20:03:55'),(3,2,2,3,'2026-04-30 20:03:55','2026-04-30 20:03:55'),(4,2,3,2,'2026-04-30 20:03:55','2026-04-30 20:03:55'),(5,3,1,4,'2026-05-01 04:41:43','2026-05-01 04:41:43'),(6,3,2,2,'2026-05-01 04:41:43','2026-05-01 04:41:43');
/*!40000 ALTER TABLE `empty_return_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `empty_returns`
--

DROP TABLE IF EXISTS `empty_returns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empty_returns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `return_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `outlet_id` bigint unsigned NOT NULL,
  `status` enum('pending','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `empty_returns_return_number_unique` (`return_number`),
  KEY `empty_returns_outlet_id_foreign` (`outlet_id`),
  CONSTRAINT `empty_returns_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empty_returns`
--

LOCK TABLES `empty_returns` WRITE;
/*!40000 ALTER TABLE `empty_returns` DISABLE KEYS */;
INSERT INTO `empty_returns` VALUES (1,'ER-2026-0001',2,'completed',NULL,'2026-04-29 20:01:32','2026-04-29 20:01:32'),(2,'RET-20260430-0002',2,'completed',NULL,'2026-04-30 20:03:55','2026-04-30 20:03:55'),(3,'RET-20260501-0003',1,'completed',NULL,'2026-05-01 04:41:43','2026-05-01 04:41:43');
/*!40000 ALTER TABLE `empty_returns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expense_categories`
--

DROP TABLE IF EXISTS `expense_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expense_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expense_categories`
--

LOCK TABLES `expense_categories` WRITE;
/*!40000 ALTER TABLE `expense_categories` DISABLE KEYS */;
INSERT INTO `expense_categories` VALUES (1,'Rent','Office/shop rent',1,NULL,NULL),(2,'Rent','Office/shop rent',1,'2026-04-16 13:12:15','2026-04-16 13:12:15'),(3,'Utilities','Electricity, water, internet',1,'2026-04-16 13:12:15','2026-04-16 13:12:15'),(4,'Office Supplies','Stationery, consumables',1,'2026-04-16 13:12:15','2026-04-16 13:12:15'),(5,'Vehicle Repairs','Vehicle maintenance and repairs',1,'2026-04-16 13:12:15','2026-04-16 13:12:15'),(6,'Marketing','Advertising and promotions',1,'2026-04-16 13:12:15','2026-04-16 13:12:15'),(7,'Miscellaneous','Other expenses',1,'2026-04-16 13:12:15','2026-04-16 13:12:15');
/*!40000 ALTER TABLE `expense_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `expenses`
--

DROP TABLE IF EXISTS `expenses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `expenses` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `expense_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expense_category_id` bigint unsigned NOT NULL,
  `expense_date` date NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `expenses_expense_number_unique` (`expense_number`),
  KEY `expenses_expense_category_id_foreign` (`expense_category_id`),
  CONSTRAINT `expenses_expense_category_id_foreign` FOREIGN KEY (`expense_category_id`) REFERENCES `expense_categories` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `expenses`
--

LOCK TABLES `expenses` WRITE;
/*!40000 ALTER TABLE `expenses` DISABLE KEYS */;
INSERT INTO `expenses` VALUES (1,'EXP-2026-0001',5,'2026-05-01','repair for toyota centre',350000.00,NULL,'2026-05-01 06:37:04','2026-05-01 06:37:04');
/*!40000 ALTER TABLE `expenses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fuel_assets`
--

DROP TABLE IF EXISTS `fuel_assets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fuel_assets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('car','forklift','other') COLLATE utf8mb4_unicode_ci NOT NULL,
  `plate_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fuel_assets`
--

LOCK TABLES `fuel_assets` WRITE;
/*!40000 ALTER TABLE `fuel_assets` DISABLE KEYS */;
INSERT INTO `fuel_assets` VALUES (1,'TOYOTA CENTRE','car','T 768 BEC',1,'2026-05-01 04:59:58','2026-05-01 04:59:58');
/*!40000 ALTER TABLE `fuel_assets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fuel_issues`
--

DROP TABLE IF EXISTS `fuel_issues`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fuel_issues` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `asset_id` bigint unsigned NOT NULL,
  `fuel_type` enum('diesel','petrol') COLLATE utf8mb4_unicode_ci NOT NULL,
  `litres` decimal(10,2) NOT NULL DEFAULT '0.00',
  `odometer_km` int DEFAULT NULL,
  `issued_by` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fuel_issues_asset_id_foreign` (`asset_id`),
  CONSTRAINT `fuel_issues_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `fuel_assets` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fuel_issues`
--

LOCK TABLES `fuel_issues` WRITE;
/*!40000 ALTER TABLE `fuel_issues` DISABLE KEYS */;
INSERT INTO `fuel_issues` VALUES (1,'2026-05-01',1,'diesel',5000.00,NULL,NULL,'2026-05-01 06:38:40','2026-05-01 06:38:40'),(2,'2026-05-01',1,'diesel',100.00,NULL,NULL,'2026-05-01 06:39:24','2026-05-01 06:39:24');
/*!40000 ALTER TABLE `fuel_issues` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fuel_purchases`
--

DROP TABLE IF EXISTS `fuel_purchases`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fuel_purchases` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL,
  `fuel_type` enum('diesel','petrol') COLLATE utf8mb4_unicode_ci NOT NULL,
  `litres` decimal(10,2) NOT NULL DEFAULT '0.00',
  `unit_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `supplier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receipt_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fuel_purchases`
--

LOCK TABLES `fuel_purchases` WRITE;
/*!40000 ALTER TABLE `fuel_purchases` DISABLE KEYS */;
INSERT INTO `fuel_purchases` VALUES (1,'2026-04-30','diesel',5000.00,3000.00,15000000.00,'oryx',NULL,'2026-04-30 17:56:10','2026-04-30 17:56:10'),(2,'2026-04-30','diesel',5000.00,3000.00,15000000.00,'oryx',NULL,'2026-04-30 17:56:57','2026-04-30 17:56:57');
/*!40000 ALTER TABLE `fuel_purchases` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fuel_stock`
--

DROP TABLE IF EXISTS `fuel_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fuel_stock` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `fuel_type` enum('diesel','petrol') COLLATE utf8mb4_unicode_ci NOT NULL,
  `litres` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `fuel_stock_fuel_type_unique` (`fuel_type`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fuel_stock`
--

LOCK TABLES `fuel_stock` WRITE;
/*!40000 ALTER TABLE `fuel_stock` DISABLE KEYS */;
INSERT INTO `fuel_stock` VALUES (1,'diesel',4900.00,'2026-04-16 12:55:20','2026-05-01 06:39:24'),(2,'petrol',0.00,'2026-04-16 12:55:20','2026-04-16 12:55:20');
/*!40000 ALTER TABLE `fuel_stock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `goods_received`
--

DROP TABLE IF EXISTS `goods_received`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `goods_received` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `grn_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `purchase_order_id` bigint unsigned DEFAULT NULL,
  `status` enum('pending','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `total_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `goods_received_grn_number_unique` (`grn_number`),
  KEY `goods_received_supplier_id_foreign` (`supplier_id`),
  KEY `goods_received_purchase_order_id_foreign` (`purchase_order_id`),
  CONSTRAINT `goods_received_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `goods_received_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `goods_received`
--

LOCK TABLES `goods_received` WRITE;
/*!40000 ALTER TABLE `goods_received` DISABLE KEYS */;
INSERT INTO `goods_received` VALUES (1,'GRN-2026-0001',1,NULL,'completed',0.00,NULL,'2026-04-30 17:47:20','2026-04-30 17:47:20'),(2,'GRN-2026-0002',1,1,'completed',604000.00,NULL,'2026-04-30 17:54:54','2026-04-30 17:54:54'),(3,'GRN-20260501-0003',1,NULL,'completed',1450000.00,NULL,'2026-05-01 04:30:00','2026-05-01 04:30:00');
/*!40000 ALTER TABLE `goods_received` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `goods_received_items`
--

DROP TABLE IF EXISTS `goods_received_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `goods_received_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `goods_received_id` bigint unsigned NOT NULL,
  `cylinder_type_id` bigint unsigned NOT NULL,
  `purchase_type` enum('full','refill') COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `unit_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `line_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `goods_received_items_goods_received_id_foreign` (`goods_received_id`),
  KEY `goods_received_items_cylinder_type_id_foreign` (`cylinder_type_id`),
  CONSTRAINT `goods_received_items_cylinder_type_id_foreign` FOREIGN KEY (`cylinder_type_id`) REFERENCES `cylinder_types` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `goods_received_items_goods_received_id_foreign` FOREIGN KEY (`goods_received_id`) REFERENCES `goods_received` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `goods_received_items`
--

LOCK TABLES `goods_received_items` WRITE;
/*!40000 ALTER TABLE `goods_received_items` DISABLE KEYS */;
INSERT INTO `goods_received_items` VALUES (1,1,1,'full',3,0.00,0.00,'2026-04-30 17:47:20','2026-04-30 17:47:20'),(2,2,1,'full',8,75500.00,604000.00,'2026-04-30 17:54:54','2026-04-30 17:54:54'),(3,3,1,'refill',8,125000.00,1000000.00,'2026-05-01 04:30:00','2026-05-01 04:30:00'),(4,3,2,'refill',12,15000.00,180000.00,'2026-05-01 04:30:00','2026-05-01 04:30:00'),(5,3,3,'refill',9,30000.00,270000.00,'2026-05-01 04:30:00','2026-05-01 04:30:00');
/*!40000 ALTER TABLE `goods_received_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `maintenance_logs`
--

DROP TABLE IF EXISTS `maintenance_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `maintenance_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `asset_id` bigint unsigned NOT NULL,
  `service_date` date NOT NULL,
  `maintenance_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `service_provider` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `next_service_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `maintenance_logs_asset_id_foreign` (`asset_id`),
  CONSTRAINT `maintenance_logs_asset_id_foreign` FOREIGN KEY (`asset_id`) REFERENCES `assets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `maintenance_logs`
--

LOCK TABLES `maintenance_logs` WRITE;
/*!40000 ALTER TABLE `maintenance_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `maintenance_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (31,'0001_01_01_000000_create_users_table',2),(32,'0001_01_01_000001_create_cache_table',2),(33,'0001_01_01_000002_create_jobs_table',2),(34,'2024_01_01_000001_create_cylinder_types_table',2),(35,'2024_01_01_000002_create_price_history_table',2),(36,'2024_01_01_000003_create_outlets_table',2),(37,'2024_01_01_000004_create_suppliers_table',2),(38,'2024_01_01_000005_create_settings_table',2),(39,'2024_01_01_000006_create_purchase_orders_table',2),(40,'2024_01_01_000007_create_purchase_order_items_table',2),(41,'2024_01_01_000008_create_goods_received_table',2),(42,'2024_01_01_000009_create_goods_received_items_table',2),(43,'2024_01_01_000010_create_stock_main_table',2),(44,'2024_01_01_000011_create_stock_main_ledger_table',2),(45,'2024_01_01_000012_create_opening_stock_table',2),(46,'2024_01_01_000013_create_opening_stock_items_table',2),(47,'2024_01_01_000014_create_stock_outlet_table',2),(48,'2024_01_01_000015_create_stock_transfers_table',2),(49,'2024_01_01_000016_create_stock_transfer_items_table',2),(50,'2024_01_01_000017_create_empty_returns_table',2),(51,'2024_01_01_000018_create_empty_return_items_table',2),(52,'2024_01_01_000019_create_stock_adjustments_table',2),(53,'2024_01_01_000020_create_sales_table',2),(54,'2024_01_01_000021_create_sale_items_table',2),(55,'2024_01_01_000022_create_cash_submissions_table',2),(56,'2024_01_01_000023_create_approvals_table',2),(57,'2024_01_01_000024_create_fuel_purchases_table',2),(58,'2024_01_01_000026a_create_fuel_assets_table',3),(59,'2024_01_01_000027_create_fuel_issues_table',3),(60,'2024_01_01_000028_create_asset_categories_table',3),(61,'2024_01_01_000030_create_fuel_stock_table',4),(62,'2024_01_01_000035_create_company_assets_table',4),(63,'2024_01_01_000036_create_depreciation_logs_table',4),(64,'2024_01_01_000037_create_maintenance_logs_table',4),(65,'2024_01_01_000038_create_asset_disposals_table',4),(66,'2024_01_01_000039_create_employees_table',5),(67,'2024_01_01_000040_create_payroll_periods_table',5),(68,'2024_01_01_000041_create_payroll_items_table',5),(69,'2024_01_01_000042_create_customers_table',6),(70,'2024_01_01_000043_add_customer_id_to_sales_table',6),(71,'2024_01_01_000044_create_expense_categories_table',6),(72,'2024_01_01_000045_create_expenses_table',6),(73,'2024_01_01_000013_add_outlet_id_to_opening_stock_table',7),(74,'2024_01_01_000046_add_fields_to_asset_categories_table',7),(75,'2026_04_29_230457_add_outlet_id_to_stock_adjustments_table',8),(76,'2026_04_29_231259_add_outlet_id_to_stock_main_ledger_table',9),(77,'2026_04_30_214737_add_cash_fields_to_sales_table',10),(78,'2026_05_01_075329_add_asset_id_to_outlets',11),(79,'2026_05_01_075919_add_outlet_id_to_assets',11),(80,'2026_05_01_113510_remove_under_maintenance_from_assets_status',11),(81,'2026_05_01_121500_add_username_to_users_table',12);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `opening_stock`
--

DROP TABLE IF EXISTS `opening_stock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `opening_stock` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `reference` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `outlet_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `opening_stock_reference_unique` (`reference`),
  KEY `opening_stock_outlet_id_foreign` (`outlet_id`),
  CONSTRAINT `opening_stock_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `opening_stock`
--

LOCK TABLES `opening_stock` WRITE;
/*!40000 ALTER TABLE `opening_stock` DISABLE KEYS */;
INSERT INTO `opening_stock` VALUES (1,'OP-20260416-0001',NULL,'2026-04-16 19:34:16','2026-04-16 19:34:16',NULL),(2,'OP-20260416-0002',NULL,'2026-04-16 19:43:16','2026-04-16 19:43:16',1),(3,'OP-20260416-0003',NULL,'2026-04-16 19:43:42','2026-04-16 19:43:42',2);
/*!40000 ALTER TABLE `opening_stock` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `opening_stock_items`
--

DROP TABLE IF EXISTS `opening_stock_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `opening_stock_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `opening_stock_id` bigint unsigned NOT NULL,
  `cylinder_type_id` bigint unsigned NOT NULL,
  `full_qty` int NOT NULL DEFAULT '0',
  `empty_qty` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `opening_stock_items_opening_stock_id_foreign` (`opening_stock_id`),
  KEY `opening_stock_items_cylinder_type_id_foreign` (`cylinder_type_id`),
  CONSTRAINT `opening_stock_items_cylinder_type_id_foreign` FOREIGN KEY (`cylinder_type_id`) REFERENCES `cylinder_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `opening_stock_items_opening_stock_id_foreign` FOREIGN KEY (`opening_stock_id`) REFERENCES `opening_stock` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `opening_stock_items`
--

LOCK TABLES `opening_stock_items` WRITE;
/*!40000 ALTER TABLE `opening_stock_items` DISABLE KEYS */;
INSERT INTO `opening_stock_items` VALUES (1,1,1,6,4,'2026-04-16 19:34:16','2026-04-16 19:34:16'),(2,1,2,4,6,'2026-04-16 19:34:16','2026-04-16 19:34:16'),(3,1,3,3,7,'2026-04-16 19:34:16','2026-04-16 19:34:16'),(4,2,1,2,4,'2026-04-16 19:43:16','2026-04-16 19:43:16'),(5,2,2,1,2,'2026-04-16 19:43:16','2026-04-16 19:43:16'),(6,2,3,4,0,'2026-04-16 19:43:16','2026-04-16 19:43:16'),(7,3,1,2,4,'2026-04-16 19:43:42','2026-04-16 19:43:42'),(8,3,2,3,3,'2026-04-16 19:43:42','2026-04-16 19:43:42'),(9,3,3,4,0,'2026-04-16 19:43:42','2026-04-16 19:43:42');
/*!40000 ALTER TABLE `opening_stock_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `outlets`
--

DROP TABLE IF EXISTS `outlets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `outlets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('car','physical') COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `plate_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `outlets`
--

LOCK TABLES `outlets` WRITE;
/*!40000 ALTER TABLE `outlets` DISABLE KEYS */;
INSERT INTO `outlets` VALUES (1,'TOYOTA CENTRE','car','MAIN OFFICE','T 768 BEC',1,'2026-04-16 19:16:37','2026-04-16 19:16:37'),(2,'KIJENGE SHOP','physical','KIJENGE, ARUSHA',NULL,1,'2026-04-16 19:17:40','2026-04-16 19:17:40');
/*!40000 ALTER TABLE `outlets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_items`
--

DROP TABLE IF EXISTS `payroll_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payroll_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `payroll_period_id` bigint unsigned NOT NULL,
  `employee_id` bigint unsigned NOT NULL,
  `basic_salary` decimal(10,2) NOT NULL DEFAULT '0.00',
  `allowances` decimal(10,2) NOT NULL DEFAULT '0.00',
  `allowance_note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deductions` decimal(10,2) NOT NULL DEFAULT '0.00',
  `deduction_note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `net_pay` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payroll_items_payroll_period_id_employee_id_unique` (`payroll_period_id`,`employee_id`),
  KEY `payroll_items_employee_id_foreign` (`employee_id`),
  CONSTRAINT `payroll_items_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payroll_items_payroll_period_id_foreign` FOREIGN KEY (`payroll_period_id`) REFERENCES `payroll_periods` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_items`
--

LOCK TABLES `payroll_items` WRITE;
/*!40000 ALTER TABLE `payroll_items` DISABLE KEYS */;
INSERT INTO `payroll_items` VALUES (1,1,1,200000.00,50000.00,NULL,0.00,NULL,250000.00,'2026-05-01 09:55:57','2026-05-01 09:56:42');
/*!40000 ALTER TABLE `payroll_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payroll_periods`
--

DROP TABLE IF EXISTS `payroll_periods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `payroll_periods` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `period_month` int NOT NULL,
  `period_year` int NOT NULL,
  `total_gross` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_deductions` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_net` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` enum('draft','approved','paid') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `approved_by` bigint unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `payroll_periods_period_month_period_year_unique` (`period_month`,`period_year`),
  KEY `payroll_periods_approved_by_foreign` (`approved_by`),
  CONSTRAINT `payroll_periods_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payroll_periods`
--

LOCK TABLES `payroll_periods` WRITE;
/*!40000 ALTER TABLE `payroll_periods` DISABLE KEYS */;
INSERT INTO `payroll_periods` VALUES (1,4,2026,250000.00,0.00,250000.00,'approved',1,'2026-05-01 10:13:38','2026-05-01 09:55:57','2026-05-01 10:13:38');
/*!40000 ALTER TABLE `payroll_periods` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `price_history`
--

DROP TABLE IF EXISTS `price_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `price_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cylinder_type_id` bigint unsigned NOT NULL,
  `full_sale_cost` decimal(12,2) NOT NULL,
  `full_sale_price` decimal(12,2) NOT NULL,
  `refill_cost` decimal(12,2) NOT NULL,
  `refill_price` decimal(12,2) NOT NULL,
  `effective_from` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `price_history_cylinder_type_id_foreign` (`cylinder_type_id`),
  CONSTRAINT `price_history_cylinder_type_id_foreign` FOREIGN KEY (`cylinder_type_id`) REFERENCES `cylinder_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `price_history`
--

LOCK TABLES `price_history` WRITE;
/*!40000 ALTER TABLE `price_history` DISABLE KEYS */;
INSERT INTO `price_history` VALUES (1,1,75000.00,150000.00,125000.00,25000.00,'2026-04-16 22:03:04','2026-04-16 22:03:04','2026-04-16 22:03:04');
/*!40000 ALTER TABLE `price_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_order_items`
--

DROP TABLE IF EXISTS `purchase_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_order_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `purchase_order_id` bigint unsigned NOT NULL,
  `cylinder_type_id` bigint unsigned NOT NULL,
  `purchase_type` enum('full','refill') COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `unit_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `line_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_order_items_purchase_order_id_foreign` (`purchase_order_id`),
  KEY `purchase_order_items_cylinder_type_id_foreign` (`cylinder_type_id`),
  CONSTRAINT `purchase_order_items_cylinder_type_id_foreign` FOREIGN KEY (`cylinder_type_id`) REFERENCES `cylinder_types` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `purchase_order_items_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_order_items`
--

LOCK TABLES `purchase_order_items` WRITE;
/*!40000 ALTER TABLE `purchase_order_items` DISABLE KEYS */;
INSERT INTO `purchase_order_items` VALUES (1,1,1,'full',8,75500.00,604000.00,'2026-04-30 17:46:40','2026-04-30 17:46:40');
/*!40000 ALTER TABLE `purchase_order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_orders`
--

DROP TABLE IF EXISTS `purchase_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_orders` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `po_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint unsigned NOT NULL,
  `status` enum('pending','received','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `total_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_orders_po_number_unique` (`po_number`),
  KEY `purchase_orders_supplier_id_foreign` (`supplier_id`),
  CONSTRAINT `purchase_orders_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_orders`
--

LOCK TABLES `purchase_orders` WRITE;
/*!40000 ALTER TABLE `purchase_orders` DISABLE KEYS */;
INSERT INTO `purchase_orders` VALUES (1,'PO-2026-0001',1,'received',604000.00,NULL,'2026-04-30 17:46:40','2026-04-30 17:54:54');
/*!40000 ALTER TABLE `purchase_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sale_items`
--

DROP TABLE IF EXISTS `sale_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sale_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint unsigned NOT NULL,
  `cylinder_type_id` bigint unsigned NOT NULL,
  `sale_type` enum('full','refill') COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `unit_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `unit_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `gross_profit` decimal(12,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_items_sale_id_foreign` (`sale_id`),
  KEY `sale_items_cylinder_type_id_foreign` (`cylinder_type_id`),
  CONSTRAINT `sale_items_cylinder_type_id_foreign` FOREIGN KEY (`cylinder_type_id`) REFERENCES `cylinder_types` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sale_items`
--

LOCK TABLES `sale_items` WRITE;
/*!40000 ALTER TABLE `sale_items` DISABLE KEYS */;
INSERT INTO `sale_items` VALUES (1,1,3,'refill',2,25000.00,30000.00,50000.00,60000.00,-10000.00,'2026-04-17 07:44:35','2026-04-17 08:26:40'),(2,2,2,'full',1,150000.00,45000.00,150000.00,45000.00,105000.00,'2026-04-17 08:29:03','2026-04-17 08:29:03'),(3,3,2,'refill',1,25000.00,15000.00,25000.00,15000.00,10000.00,'2026-04-29 19:45:41','2026-04-29 19:45:41'),(4,4,1,'full',2,150000.00,75500.00,300000.00,151000.00,149000.00,'2026-04-30 19:00:19','2026-04-30 19:00:19'),(5,5,2,'full',2,150000.00,45000.00,300000.00,90000.00,210000.00,'2026-04-30 20:07:31','2026-04-30 20:07:31'),(6,5,2,'refill',1,25000.00,15000.00,25000.00,15000.00,10000.00,'2026-04-30 20:07:31','2026-04-30 20:07:31'),(7,6,2,'full',2,150000.00,45000.00,300000.00,90000.00,210000.00,'2026-04-30 20:14:53','2026-04-30 20:14:53'),(8,7,1,'full',2,150000.00,75500.00,300000.00,151000.00,149000.00,'2026-05-01 11:08:05','2026-05-01 11:08:05');
/*!40000 ALTER TABLE `sale_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sales`
--

DROP TABLE IF EXISTS `sales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `sale_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `outlet_id` bigint unsigned NOT NULL,
  `sale_date` date NOT NULL,
  `status` enum('pending','approved','queried','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `total_price` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_cost` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total_gross_profit` decimal(12,2) NOT NULL DEFAULT '0.00',
  `cash_submitted` decimal(12,2) DEFAULT NULL,
  `cash_submitted_date` date DEFAULT NULL,
  `cash_submitted_by` bigint unsigned DEFAULT NULL,
  `cash_variance` decimal(12,2) DEFAULT NULL,
  `cash_receipt_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `customer_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sales_sale_number_unique` (`sale_number`),
  KEY `sales_outlet_id_foreign` (`outlet_id`),
  KEY `sales_customer_id_foreign` (`customer_id`),
  CONSTRAINT `sales_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sales`
--

LOCK TABLES `sales` WRITE;
/*!40000 ALTER TABLE `sales` DISABLE KEYS */;
INSERT INTO `sales` VALUES (1,'SL-2026-0001',2,'2026-04-17','approved',50000.00,0.00,50000.00,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-17 07:44:35','2026-04-17 07:55:33',NULL),(2,'SL-2026-0002',1,'2026-04-17','approved',150000.00,45000.00,105000.00,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-17 08:29:03','2026-04-17 08:29:08',NULL),(3,'SL-2026-0003',2,'2026-04-29','approved',25000.00,15000.00,10000.00,NULL,NULL,NULL,NULL,NULL,NULL,'2026-04-29 19:45:41','2026-04-29 19:46:09',NULL),(4,'SL-2026-0004',2,'2026-04-30','approved',300000.00,151000.00,149000.00,300000.00,'2026-04-30',NULL,0.00,NULL,NULL,'2026-04-30 19:00:19','2026-04-30 19:04:50',NULL),(5,'SL-2026-0005',2,'2026-04-30','approved',325000.00,105000.00,220000.00,325000.00,'2026-04-30',NULL,0.00,NULL,NULL,'2026-04-30 20:07:31','2026-04-30 20:07:49',NULL),(6,'SL-2026-0006',1,'2026-04-30','approved',300000.00,90000.00,210000.00,300000.00,'2026-05-01',NULL,0.00,NULL,NULL,'2026-04-30 20:14:53','2026-05-01 04:30:34',NULL),(7,'SL-2026-0007',2,'2026-05-01','approved',300000.00,151000.00,149000.00,300000.00,'2026-05-01',NULL,0.00,NULL,NULL,'2026-05-01 11:08:05','2026-05-01 11:08:08',NULL);
/*!40000 ALTER TABLE `sales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('40t0kExWp73S9KcsQHsoAe5e5rSNPHYWK2P6dZ33',NULL,'127.0.0.1','curl/8.7.1','YTo0OntzOjY6Il90b2tlbiI7czo0MDoibG9HTjFxekl5VFdzRThVbG1tVmR4MVJYdkJaOUdJbmhRemtOZWxhZCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozNzoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL3JlcG9ydHMvcGF5cm9sbCI7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjM3OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvcmVwb3J0cy9wYXlyb2xsIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1777631856),('tyyWgUtY1rafunjKhfCv8bQBIIaeVdGIfNXSiS5i',NULL,'127.0.0.1','Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoidkZoamZQQUZNUG5PUFF5eGl0YjdYN3l0QmF5dWZYTmVXU3MwMldjRSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fX0=',1777634593);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `settings_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'business_name','GDMS','2026-05-01 08:36:21','2026-05-01 08:36:21'),(2,'address','Arusha, Tanzania','2026-05-01 08:36:21','2026-05-01 09:21:31'),(3,'currency','TZS','2026-05-01 08:36:21','2026-05-01 08:36:21'),(4,'financial_year_start','01','2026-05-01 08:36:21','2026-05-01 08:36:21'),(5,'password_reset_email','iamjoshualotha@gmail.com','2026-05-01 09:21:31','2026-05-01 09:21:31');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_adjustments`
--

DROP TABLE IF EXISTS `stock_adjustments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_adjustments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `adjustment_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cylinder_type_id` bigint unsigned NOT NULL,
  `type` enum('gain','loss','correction') COLLATE utf8mb4_unicode_ci NOT NULL,
  `full_qty_change` int NOT NULL DEFAULT '0',
  `empty_qty_change` int NOT NULL DEFAULT '0',
  `reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `outlet_id` bigint unsigned DEFAULT NULL,
  `is_main` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_adjustments_adjustment_number_unique` (`adjustment_number`),
  KEY `stock_adjustments_cylinder_type_id_foreign` (`cylinder_type_id`),
  KEY `stock_adjustments_outlet_id_foreign` (`outlet_id`),
  CONSTRAINT `stock_adjustments_cylinder_type_id_foreign` FOREIGN KEY (`cylinder_type_id`) REFERENCES `cylinder_types` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `stock_adjustments_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_adjustments`
--

LOCK TABLES `stock_adjustments` WRITE;
/*!40000 ALTER TABLE `stock_adjustments` DISABLE KEYS */;
INSERT INTO `stock_adjustments` VALUES (1,'ADJ-2026-0001',2,'loss',0,-1,'wrong added','2026-04-29 20:20:30','2026-04-29 20:20:30',NULL,1);
/*!40000 ALTER TABLE `stock_adjustments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_main`
--

DROP TABLE IF EXISTS `stock_main`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_main` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cylinder_type_id` bigint unsigned NOT NULL,
  `full_qty` int NOT NULL DEFAULT '0',
  `empty_qty` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_main_cylinder_type_id_unique` (`cylinder_type_id`),
  CONSTRAINT `stock_main_cylinder_type_id_foreign` FOREIGN KEY (`cylinder_type_id`) REFERENCES `cylinder_types` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_main`
--

LOCK TABLES `stock_main` WRITE;
/*!40000 ALTER TABLE `stock_main` DISABLE KEYS */;
INSERT INTO `stock_main` VALUES (1,1,23,4,'2026-04-16 19:34:16','2026-05-01 04:41:43'),(2,2,10,2,'2026-04-16 19:34:16','2026-05-01 04:41:43'),(3,3,11,0,'2026-04-16 19:34:16','2026-05-01 04:30:00');
/*!40000 ALTER TABLE `stock_main` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_main_ledger`
--

DROP TABLE IF EXISTS `stock_main_ledger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_main_ledger` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `cylinder_type_id` bigint unsigned NOT NULL,
  `full_qty_change` int NOT NULL DEFAULT '0',
  `empty_qty_change` int NOT NULL DEFAULT '0',
  `full_qty_after` int NOT NULL DEFAULT '0',
  `empty_qty_after` int NOT NULL DEFAULT '0',
  `transaction_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` bigint unsigned DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `outlet_id` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_main_ledger_cylinder_type_id_foreign` (`cylinder_type_id`),
  KEY `stock_main_ledger_outlet_id_foreign` (`outlet_id`),
  CONSTRAINT `stock_main_ledger_cylinder_type_id_foreign` FOREIGN KEY (`cylinder_type_id`) REFERENCES `cylinder_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_main_ledger_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_main_ledger`
--

LOCK TABLES `stock_main_ledger` WRITE;
/*!40000 ALTER TABLE `stock_main_ledger` DISABLE KEYS */;
INSERT INTO `stock_main_ledger` VALUES (1,1,6,4,6,4,'opening','OpeningStock',1,'Opening stock entry','2026-04-16 19:34:16','2026-04-16 19:34:16',NULL),(2,2,4,6,4,6,'opening','OpeningStock',1,'Opening stock entry','2026-04-16 19:34:16','2026-04-16 19:34:16',NULL),(3,3,3,7,3,7,'opening','OpeningStock',1,'Opening stock entry','2026-04-16 19:34:16','2026-04-16 19:34:16',NULL),(4,2,0,1,4,7,'sale','sale',3,'Sale SL-2026-0003 - refill','2026-04-29 19:45:41','2026-04-29 19:45:41',NULL),(5,1,0,2,6,6,'empty_return_in','EmptyReturn',1,'Empty return from outlet ID: 2','2026-04-29 20:01:32','2026-04-29 20:01:32',NULL),(6,2,0,1,3,8,'adjustment','StockAdjustment',1,'loss: wrong added','2026-04-29 20:20:30','2026-04-29 20:20:30',NULL),(7,3,-1,0,2,7,'transfer_out','StockTransfer',1,'Transfer to outlet ID: 2','2026-04-30 17:43:46','2026-04-30 17:43:46',NULL),(8,1,3,0,9,6,'grn_full','GoodsReceived',1,'GRN: GRN-2026-0001 - Full cylinders received','2026-04-30 17:47:20','2026-04-30 17:47:20',NULL),(9,1,8,0,17,6,'grn_full','GoodsReceived',2,'GRN: GRN-2026-0002 - Full cylinders received','2026-04-30 17:54:54','2026-04-30 17:54:54',NULL),(10,1,-2,0,15,6,'sale','sale',4,'Sale SL-2026-0004 - full','2026-04-30 19:00:19','2026-04-30 19:00:19',NULL),(11,2,-2,0,1,11,'sale','sale',5,'Sale SL-2026-0005 - full','2026-04-30 20:07:31','2026-04-30 20:07:31',NULL),(12,2,0,1,1,12,'sale','sale',5,'Sale SL-2026-0005 - refill','2026-04-30 20:07:31','2026-04-30 20:07:31',NULL),(13,2,-2,0,-2,12,'sale','sale',6,'Sale SL-2026-0006 - full','2026-04-30 20:14:53','2026-04-30 20:14:53',NULL),(14,2,-2,0,-4,2,'sale','sale',6,'Sale SL-2026-0006 - full (Outlet)','2026-04-30 20:14:53','2026-04-30 20:14:53',NULL),(15,1,8,-8,23,0,'grn_refill','GoodsReceived',3,'GRN GRN-20260501-0003 - Refill from jumla supliers','2026-05-01 04:30:00','2026-05-01 04:30:00',NULL),(16,2,12,-12,12,0,'grn_refill','GoodsReceived',3,'GRN GRN-20260501-0003 - Refill from jumla supliers','2026-05-01 04:30:00','2026-05-01 04:30:00',NULL),(17,3,9,-9,11,0,'grn_refill','GoodsReceived',3,'GRN GRN-20260501-0003 - Refill from jumla supliers','2026-05-01 04:30:00','2026-05-01 04:30:00',NULL),(18,2,-2,0,8,0,'transfer_out','transfer',2,'Transfer to TOYOTA CENTRE','2026-05-01 04:40:31','2026-05-01 04:40:31',NULL),(19,1,4,-4,27,0,'empty_return_in','empty_return',3,'Empty return from TOYOTA CENTRE','2026-05-01 04:41:43','2026-05-01 04:41:43',NULL),(20,2,2,-2,12,0,'empty_return_in','empty_return',3,'Empty return from TOYOTA CENTRE','2026-05-01 04:41:43','2026-05-01 04:41:43',NULL),(21,1,-2,0,2,0,'sale','sale',7,'Sale SL-2026-0007 - full (Outlet)','2026-05-01 11:08:05','2026-05-01 11:08:05',NULL);
/*!40000 ALTER TABLE `stock_main_ledger` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_outlet`
--

DROP TABLE IF EXISTS `stock_outlet`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_outlet` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `outlet_id` bigint unsigned NOT NULL,
  `cylinder_type_id` bigint unsigned NOT NULL,
  `full_qty` int NOT NULL DEFAULT '0',
  `empty_qty` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_outlet_outlet_id_cylinder_type_id_unique` (`outlet_id`,`cylinder_type_id`),
  KEY `stock_outlet_cylinder_type_id_foreign` (`cylinder_type_id`),
  CONSTRAINT `stock_outlet_cylinder_type_id_foreign` FOREIGN KEY (`cylinder_type_id`) REFERENCES `cylinder_types` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_outlet_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_outlet`
--

LOCK TABLES `stock_outlet` WRITE;
/*!40000 ALTER TABLE `stock_outlet` DISABLE KEYS */;
INSERT INTO `stock_outlet` VALUES (1,1,1,6,0,'2026-04-16 19:43:16','2026-05-01 04:41:43'),(2,1,2,4,0,'2026-04-16 19:43:16','2026-05-01 04:41:43'),(3,1,3,4,0,'2026-04-16 19:43:16','2026-04-16 19:43:16'),(4,2,1,2,0,'2026-04-16 19:43:42','2026-05-01 11:08:05'),(5,2,2,6,0,'2026-04-16 19:43:42','2026-04-30 20:03:55'),(6,2,3,5,0,'2026-04-16 19:43:42','2026-04-30 20:03:55');
/*!40000 ALTER TABLE `stock_outlet` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_transfer_items`
--

DROP TABLE IF EXISTS `stock_transfer_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_transfer_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `stock_transfer_id` bigint unsigned NOT NULL,
  `cylinder_type_id` bigint unsigned NOT NULL,
  `quantity` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_transfer_items_stock_transfer_id_foreign` (`stock_transfer_id`),
  KEY `stock_transfer_items_cylinder_type_id_foreign` (`cylinder_type_id`),
  CONSTRAINT `stock_transfer_items_cylinder_type_id_foreign` FOREIGN KEY (`cylinder_type_id`) REFERENCES `cylinder_types` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `stock_transfer_items_stock_transfer_id_foreign` FOREIGN KEY (`stock_transfer_id`) REFERENCES `stock_transfers` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_transfer_items`
--

LOCK TABLES `stock_transfer_items` WRITE;
/*!40000 ALTER TABLE `stock_transfer_items` DISABLE KEYS */;
INSERT INTO `stock_transfer_items` VALUES (1,1,3,1,'2026-04-30 17:43:46','2026-04-30 17:43:46'),(2,2,2,2,'2026-05-01 04:40:31','2026-05-01 04:40:31');
/*!40000 ALTER TABLE `stock_transfer_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_transfers`
--

DROP TABLE IF EXISTS `stock_transfers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `stock_transfers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `transfer_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `outlet_id` bigint unsigned NOT NULL,
  `status` enum('pending','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_transfers_transfer_number_unique` (`transfer_number`),
  KEY `stock_transfers_outlet_id_foreign` (`outlet_id`),
  CONSTRAINT `stock_transfers_outlet_id_foreign` FOREIGN KEY (`outlet_id`) REFERENCES `outlets` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_transfers`
--

LOCK TABLES `stock_transfers` WRITE;
/*!40000 ALTER TABLE `stock_transfers` DISABLE KEYS */;
INSERT INTO `stock_transfers` VALUES (1,'ST-2026-0001',2,'completed',NULL,'2026-04-30 17:43:46','2026-04-30 17:43:46'),(2,'TRF-20260501-0002',1,'completed',NULL,'2026-05-01 04:40:31','2026-05-01 04:40:31');
/*!40000 ALTER TABLE `stock_transfers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,'jumla supliers','Joshua Lotha','0784319441',NULL,'Arusha, Tanzania',1,'2026-04-30 17:46:16','2026-04-30 17:46:16');
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','admin','admin@gdms.com',NULL,'$2y$12$/lVZi1geGStLF7Q2DvSasOpqoCC6tOoF1.byX7lAbCGz.Kn6wzKRq',NULL,'2026-04-16 12:55:20','2026-05-01 09:24:40');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
SET @@SESSION.SQL_LOG_BIN = @MYSQLDUMP_TEMP_LOG_BIN;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-05-01 15:37:54

-- MySQL dump 10.13  Distrib 8.0.38, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: webphp
-- ------------------------------------------------------
-- Server version	8.0.39

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

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
-- Table structure for table `cart_items`
--

DROP TABLE IF EXISTS `cart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cart_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `store_id` bigint unsigned NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `cart_items_user_id_foreign` (`user_id`),
  KEY `cart_items_product_id_foreign` (`product_id`),
  KEY `cart_items_store_id_foreign` (`store_id`),
  CONSTRAINT `cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_items_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `store` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_items_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart_items`
--

LOCK TABLES `cart_items` WRITE;
/*!40000 ALTER TABLE `cart_items` DISABLE KEYS */;
INSERT INTO `cart_items` VALUES (10,1,3,1,1,'2025-04-13 13:37:48','2025-04-13 13:37:48');
/*!40000 ALTER TABLE `cart_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `categoryName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Quần',NULL,NULL),(2,'Áo',NULL,NULL),(3,'Giày',NULL,NULL),(4,'Dép',NULL,NULL);
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `follower`
--

DROP TABLE IF EXISTS `follower`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `follower` (
  `store_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`store_id`,`user_id`),
  KEY `follower_user_id_foreign` (`user_id`),
  CONSTRAINT `follower_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `store` (`id`) ON DELETE CASCADE,
  CONSTRAINT `follower_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `follower`
--

LOCK TABLES `follower` WRITE;
/*!40000 ALTER TABLE `follower` DISABLE KEYS */;
INSERT INTO `follower` VALUES (1,6,'2025-04-13 16:30:06','2025-04-13 16:30:06');
/*!40000 ALTER TABLE `follower` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `images_detail`
--

DROP TABLE IF EXISTS `images_detail`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `images_detail` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `imageUrl` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `images_detail_product_id_foreign` (`product_id`),
  CONSTRAINT `images_detail_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `images_detail`
--

LOCK TABLES `images_detail` WRITE;
/*!40000 ALTER TABLE `images_detail` DISABLE KEYS */;
INSERT INTO `images_detail` VALUES (5,'https://res.cloudinary.com/di0mcnrby/image/upload/v1744450144/uploads/oqoy7kfey3wen9uz8m1w.jpg',2),(6,'https://res.cloudinary.com/di0mcnrby/image/upload/v1744450145/uploads/wxxr4ik0v98sllsvorky.jpg',2),(7,'https://res.cloudinary.com/di0mcnrby/image/upload/v1744450864/uploads/jphotvyqnql7fwfsts6s.jpg',3),(8,'https://res.cloudinary.com/di0mcnrby/image/upload/v1744450867/uploads/lpyjfuetfhkkghkfbanf.jpg',3),(9,'https://res.cloudinary.com/di0mcnrby/image/upload/v1744552184/uploads/u2ne0m7qagvaawz9tlqi.png',4),(10,'https://res.cloudinary.com/di0mcnrby/image/upload/v1744552186/uploads/ki2dtilhctdgmaoln1sy.png',4);
/*!40000 ALTER TABLE `images_detail` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `message`
--

DROP TABLE IF EXISTS `message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `message` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `store_id` bigint unsigned NOT NULL,
  `content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isRead` tinyint(1) NOT NULL,
  `senderType` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `message_user_id_foreign` (`user_id`),
  KEY `message_store_id_foreign` (`store_id`),
  CONSTRAINT `message_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `store` (`id`) ON DELETE CASCADE,
  CONSTRAINT `message_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `message`
--

LOCK TABLES `message` WRITE;
/*!40000 ALTER TABLE `message` DISABLE KEYS */;
/*!40000 ALTER TABLE `message` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_role',1),(2,'0001_01_01_000000_create_users_table',1),(3,'2023_01_01_000000_create_sessions_table',1),(4,'2025_02_27_053343_create_table__category',1),(5,'2025_02_27_053602_create_store',1),(6,'2025_02_27_053750_create_table_product',1),(7,'2025_02_27_061340_create_follower',1),(8,'2025_02_27_061528_create_store_notification',1),(9,'2025_02_27_062111_create_images_detail',1),(10,'2025_02_27_062253_creat_order',1),(11,'2025_02_27_062622_creat_order_detail',1),(12,'2025_02_27_063159_creat_user_notification',1),(13,'2025_02_27_070628_create_message',1),(14,'2025_03_03_091429_create_personal_access_tokens_table',1),(15,'2025_03_12_025053_create_cart',1),(16,'2025_03_21_191858_rename_thumnail_to_thumbnail_in_product_table',1),(17,'2025_04_07_152851_create_cache_table',2),(18,'2025_04_12_072943_add_status_to_stores_table',2),(19,'2025_04_12_073255_add_status_to_stores_table',2),(20,'2025_04_13_110800_add_total_amount_to_order_table',3),(21,'2025_04_13_112151_change_shipping_address_to_text_in_order_table',4),(22,'2025_04_13_112425_remove_totalprice_from_order_table',5),(23,'2025_04_13_190522_modify_address_columns_in_order_table',6);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `store_id` bigint unsigned NOT NULL,
  `total_amount` decimal(15,2) NOT NULL,
  `shipping_first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_street` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_state` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_zipcode` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `shipping_country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phoneNumber` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `paymentMethod` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `shipping_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_user_id_foreign` (`user_id`),
  KEY `order_store_id_foreign` (`store_id`),
  CONSTRAINT `order_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `store` (`id`),
  CONSTRAINT `order_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order`
--

LOCK TABLES `order` WRITE;
/*!40000 ALTER TABLE `order` DISABLE KEYS */;
INSERT INTO `order` VALUES (14,1,1,80000.00,'Nguyễn','Tuấn','sheamus201515@gmail.com','05 Hem 2 Truong Quyen Tay Ninh','Tay Ninh','Tây Ninh Province','840000','Vietnam','+84967793967',NULL,'COD','0','Waiting for Pickup','2025-04-13 12:10:12','2025-04-13 12:10:12'),(16,1,1,270000.00,'Nguyễn','Tuấn','sheamus201515@gmail.com','05 Hem 2 Truong Quyen Tay Ninh','Tay Ninh','Tây Ninh Province','840000','Vietnam','+84967793967',NULL,'BANKING','0','Pending Payment','2025-04-13 12:14:45','2025-04-13 12:14:45'),(17,1,1,90000.00,'Nguyễn','Tuấn','sheamus201515@gmail.com','05 Hem 2 Truong Quyen Tay Ninh','Tay Ninh','Tây Ninh Province','840000','Vietnam','+84967793967',NULL,'BANKING','1','Paid','2025-04-13 12:16:53','2025-04-13 12:17:22');
/*!40000 ALTER TABLE `order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_details`
--

DROP TABLE IF EXISTS `order_details`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `order_details` (
  `quantity` int unsigned NOT NULL,
  `product_id` bigint unsigned NOT NULL,
  `order_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`product_id`,`order_id`),
  KEY `order_details_order_id_foreign` (`order_id`),
  CONSTRAINT `order_details_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_details`
--

LOCK TABLES `order_details` WRITE;
/*!40000 ALTER TABLE `order_details` DISABLE KEYS */;
INSERT INTO `order_details` VALUES (1,2,14),(8,3,16),(2,3,17);
/*!40000 ALTER TABLE `order_details` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `personal_access_tokens`
--

DROP TABLE IF EXISTS `personal_access_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `personal_access_tokens`
--

LOCK TABLES `personal_access_tokens` WRITE;
/*!40000 ALTER TABLE `personal_access_tokens` DISABLE KEYS */;
INSERT INTO `personal_access_tokens` VALUES (1,'App\\Models\\User',1,'api-token','32ddf92b97156cde10c7376c806f245aa407562a8b87344b256e6e693305e6a4','[\"*\"]','2025-04-12 00:23:58',NULL,'2025-04-12 00:23:50','2025-04-12 00:23:58'),(2,'App\\Models\\User',1,'api-token','f66e906fd85dc71d22866759661e167803e6e16d7b0fdd68204a082033f85ae5','[\"*\"]','2025-04-12 01:28:11',NULL,'2025-04-12 01:04:10','2025-04-12 01:28:11'),(3,'App\\Models\\User',1,'api-token','585de90403338b64f90ec329524bce16976c385af338a5425c9a6fd23fad21b5','[\"*\"]','2025-04-12 04:02:50',NULL,'2025-04-12 01:28:14','2025-04-12 04:02:50'),(4,'App\\Models\\User',1,'api-token','88fa8c6cc67559d78d03feab3611b79ca942d4771f8aaa271e840bf6b6b62238','[\"*\"]','2025-04-13 04:02:00',NULL,'2025-04-12 04:02:54','2025-04-13 04:02:00'),(5,'App\\Models\\User',1,'api-token','caf042327b1d2fac943eb9f50bdd9761ea61ea83663a853288dc5d7f5db163cd','[\"*\"]','2025-04-13 04:09:59',NULL,'2025-04-13 04:02:03','2025-04-13 04:09:59'),(6,'App\\Models\\User',1,'api-token','4533e7153fef4de06d9d6e22898aa1d9929c2757b784a4a7915c6509dc030d8a','[\"*\"]','2025-04-13 04:10:17',NULL,'2025-04-13 04:10:05','2025-04-13 04:10:17'),(7,'App\\Models\\User',1,'api-token','1a656031bef3eb4409e8e0fc6d10b710a1fbd0680120f232836100c7a21fd7b5','[\"*\"]','2025-04-13 04:18:27',NULL,'2025-04-13 04:13:37','2025-04-13 04:18:27'),(8,'App\\Models\\User',1,'api-token','0818f6d554022136677d99e82a248d5152a03f23ba8996d80453ea770519f7bf','[\"*\"]','2025-04-13 04:22:36',NULL,'2025-04-13 04:18:30','2025-04-13 04:22:36'),(9,'App\\Models\\User',1,'api-token','18fcc16f6d2223458a88cdcf628b9f85a25d68bf32a4df9f93d08a9ac172c4ca','[\"*\"]','2025-04-13 12:14:45',NULL,'2025-04-13 04:22:43','2025-04-13 12:14:45'),(10,'App\\Models\\User',1,'api-token','cf0139837f0014eb0ad411b37eda1d4643822ea77c1e3aef59c8c73dd70e3163','[\"*\"]',NULL,NULL,'2025-04-13 12:14:53','2025-04-13 12:14:53'),(11,'App\\Models\\User',1,'api-token','36871c91428098483466ca29873d99b8cc38268ddaee6a45d18b7d1b2f0bd411','[\"*\"]',NULL,NULL,'2025-04-13 12:14:55','2025-04-13 12:14:55'),(12,'App\\Models\\User',1,'api-token','9cace84dfe126df9ce7390a44dd7122866dc1a5d5f07c1d17a6a391b2fe91341','[\"*\"]',NULL,NULL,'2025-04-13 12:15:09','2025-04-13 12:15:09'),(13,'App\\Models\\User',1,'api-token','bd862d88878efc93086ae25ff308321c34127cdc8d97c2a1db186c498c23a7f9','[\"*\"]',NULL,NULL,'2025-04-13 12:15:17','2025-04-13 12:15:17'),(14,'App\\Models\\User',1,'api-token','42a505da9f1c30351822b4f7af5049ee200f524dcfbb70cba915e716ff0f9031','[\"*\"]',NULL,NULL,'2025-04-13 12:15:25','2025-04-13 12:15:25'),(15,'App\\Models\\User',1,'api-token','a08538d1a5992ecd14a8167b7615e2251a3ecbfe6e5fd99c1b0a05d5640bb39d','[\"*\"]',NULL,NULL,'2025-04-13 12:15:42','2025-04-13 12:15:42'),(16,'App\\Models\\User',1,'api-token','2ac5f218d1a11398ef6f0c7757fda209d03e3442f6c4455b907e228f9a0bcfb6','[\"*\"]','2025-04-13 12:48:00',NULL,'2025-04-13 12:16:10','2025-04-13 12:48:00'),(17,'App\\Models\\User',1,'api-token','06ae2f377ba47b498c2d18abd6d1da91d790d382e807edc11f1a219174cbf5b7','[\"*\"]','2025-04-13 13:09:20',NULL,'2025-04-13 12:48:07','2025-04-13 13:09:20'),(18,'App\\Models\\User',1,'api-token','e2196a547e3257c0c0fe13593425a5ab8bcd9910e3bde2b2d5915d6db8c0c23c','[\"*\"]','2025-04-13 15:05:37',NULL,'2025-04-13 13:09:22','2025-04-13 15:05:37'),(19,'App\\Models\\User',1,'api-token','998f0294886b999574e9ceddff4c062437d84c377bb4522e6d3de92b89873aef','[\"*\"]','2025-04-13 15:06:35',NULL,'2025-04-13 15:05:51','2025-04-13 15:06:35'),(20,'App\\Models\\User',6,'api-token','5eec70e967c402764f574304981c0ce04abdc5850e0d27ba67a5f03de36642f3','[\"*\"]',NULL,NULL,'2025-04-13 15:09:37','2025-04-13 15:09:37'),(21,'App\\Models\\User',6,'api-token','77bb3e20c62957306754e8f2627cdbf313e4a635a032f0c346f5891c79e8c8d2','[\"*\"]','2025-04-13 16:52:51',NULL,'2025-04-13 15:10:01','2025-04-13 16:52:51'),(22,'App\\Models\\User',1,'api-token','758bcc367e80d06759cdc185574d99a8bd7511f5d2326779640282f42e56dc15','[\"*\"]','2025-04-13 17:05:48',NULL,'2025-04-13 16:52:58','2025-04-13 17:05:48'),(23,'App\\Models\\User',1,'api-token','8542b164d33ffed765d63ff43c9aca36566eeecd7ce2f3ef3b81f85a715fa724','[\"*\"]','2025-04-13 17:12:39',NULL,'2025-04-13 17:07:31','2025-04-13 17:12:39'),(24,'App\\Models\\User',1,'api-token','62531ca5129b4df70c8411ee1aad7e8f55d4b078e5d78d874998e8e53ad3d3c8','[\"*\"]','2025-04-13 17:23:31',NULL,'2025-04-13 17:16:05','2025-04-13 17:23:31');
/*!40000 ALTER TABLE `personal_access_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `product` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `category_id` bigint unsigned DEFAULT NULL,
  `productName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remainQuantity` int unsigned NOT NULL,
  `price` double NOT NULL,
  `store_id` bigint unsigned NOT NULL,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isValidated` tinyint(1) NOT NULL DEFAULT '0',
  `soldQuantity` int unsigned NOT NULL,
  `productDetail` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_category_id_foreign` (`category_id`),
  KEY `product_store_id_foreign` (`store_id`),
  CONSTRAINT `product_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `product_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `store` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product`
--

LOCK TABLES `product` WRITE;
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
INSERT INTO `product` VALUES (2,1,'QUần đùi',1075,50000,1,'https://res.cloudinary.com/di0mcnrby/image/upload/v1744450141/uploads/h1hbryfqxdiuurqi2fxq.jpg',0,15,'sadasdasdsad','2025-04-12 02:29:02','2025-04-13 12:10:12'),(3,3,'Áo Ba Lỗ 1111',9998,30000,1,'https://res.cloudinary.com/di0mcnrby/image/upload/v1744450862/uploads/fblqfihokfhxjrznwsiq.jpg',0,21,'asdasd','2025-04-12 02:41:03','2025-04-13 13:52:38'),(4,1,'QUần jeanssad',100,50000,1,'https://res.cloudinary.com/di0mcnrby/image/upload/v1744552182/uploads/iirje5agdhcwtey6l7vs.png',0,0,'asdasd','2025-04-13 13:49:42','2025-04-13 13:49:42');
/*!40000 ALTER TABLE `product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `roleName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'ADMIN'),(2,'USER');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store`
--

DROP TABLE IF EXISTS `store`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `storeName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ownId` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `store_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store`
--

LOCK TABLES `store` WRITE;
/*!40000 ALTER TABLE `store` DISABLE KEYS */;
INSERT INTO `store` VALUES (1,'Shop Quần Áo','sadas1','https://res.cloudinary.com/di0mcnrby/image/upload/v1744450902/stores/1744450901_489791393_1233990762063087_8718577406130654860_n.jpg','1','approved','2025-04-12 01:30:24','2025-04-13 15:06:16');
/*!40000 ALTER TABLE `store` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `store_notification`
--

DROP TABLE IF EXISTS `store_notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `store_notification` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `store_id` bigint unsigned NOT NULL,
  `message` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isRead` tinyint(1) NOT NULL DEFAULT '0',
  `user_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `store_notification_store_id_foreign` (`store_id`),
  CONSTRAINT `store_notification_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `store` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `store_notification`
--

LOCK TABLES `store_notification` WRITE;
/*!40000 ALTER TABLE `store_notification` DISABLE KEYS */;
/*!40000 ALTER TABLE `store_notification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_notification`
--

DROP TABLE IF EXISTS `user_notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_notification` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `message` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `isRead` tinyint(1) NOT NULL DEFAULT '0',
  `store_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_notification_user_id_foreign` (`user_id`),
  CONSTRAINT `user_notification_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `store` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_notification`
--

LOCK TABLES `user_notification` WRITE;
/*!40000 ALTER TABLE `user_notification` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_notification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `firstName` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lastName` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role_id` bigint unsigned DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_role_id_foreign` (`role_id`),
  CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,NULL,NULL,'sheamus201515@gmail.com',NULL,1,'$2y$12$N3h63opicCww.HvdmsH.re9qsIQYjutzIbwNDGuiJI18P82EK8mrK','2025-04-12 00:22:42','2025-04-12 00:22:42'),(2,NULL,NULL,'beatbbbb@gmail.com',NULL,2,'$2y$12$..rdKKPOCXfy/XABIZ9yT.ibgzaelTp3hGL9wVtPmVJvEeNWVwGs6','2025-04-13 15:06:51','2025-04-13 15:06:51'),(3,NULL,NULL,'user@gmail.com',NULL,2,'$2y$12$kIjLj4yxbMt1UbE3Up5RWOCOTC1dY3H7u./2TzAybkCvmDXn7nkAe','2025-04-13 15:08:05','2025-04-13 15:08:05'),(4,NULL,NULL,'user1@gmail.com',NULL,2,'$2y$12$vx7Glmhhp/CNxHCUIpJiSu5FCS3mKMkNjbTZp74x95kaUmyKKm7mq','2025-04-13 15:08:24','2025-04-13 15:08:24'),(5,NULL,NULL,'user2@gmail.com',NULL,2,'$2y$12$riSkRikeDvO6mz8Hge9MV.bXK.NJ1MCw31ZmL6TZaLiXnffrgntEa','2025-04-13 15:09:04','2025-04-13 15:09:04'),(6,'Nguyễn','Tuấn','user3@gmail.com',NULL,2,'$2y$12$tJRP39VRwBcSkybZiCV3p./Eh666VReFJ0Fs1MBV8LL/nsivL4yG2','2025-04-13 15:09:29','2025-04-13 16:49:17');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-04-14 20:11:20

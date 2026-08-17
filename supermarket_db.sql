mysqldump: [Warning] Using a password on the command line interface can be insecure.
-- MySQL dump 10.13  Distrib 5.7.42, for Linux (x86_64)
--
-- Host: 35.199.1.205    Database: supermarket_db
-- ------------------------------------------------------
-- Server version	5.6.51-google-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
SET @MYSQLDUMP_TEMP_LOG_BIN = @@SESSION.SQL_LOG_BIN;
SET @@SESSION.SQL_LOG_BIN= 0;

--
-- Table structure for table `audit_log`
--

DROP TABLE IF EXISTS `audit_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `audit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `entity_type` varchar(100) NOT NULL,
  `entity_id` int(11) DEFAULT NULL,
  `old_values` text,
  `new_values` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_entity` (`entity_type`,`entity_id`),
  KEY `idx_action` (`action`),
  KEY `idx_created` (`created_at`),
  CONSTRAINT `audit_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audit_log`
--

LOCK TABLES `audit_log` WRITE;
/*!40000 ALTER TABLE `audit_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `audit_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cart` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `session_id` varchar(191) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_user_product` (`user_id`,`product_id`),
  KEY `product_id` (`product_id`),
  KEY `idx_session` (`session_id`),
  CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart`
--

LOCK TABLES `cart` WRITE;
/*!40000 ALTER TABLE `cart` DISABLE KEYS */;
INSERT INTO `cart` VALUES (16,1,NULL,18,2,'2026-03-26 16:47:37','2026-03-26 16:47:57'),(17,1,NULL,11,2,'2026-03-26 16:47:37','2026-03-26 16:47:57'),(18,1,NULL,13,2,'2026-03-26 16:47:38','2026-03-26 16:47:58'),(19,1,NULL,15,2,'2026-03-26 16:47:39','2026-03-26 16:47:58');
/*!40000 ALTER TABLE `cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `sort_order` int(11) DEFAULT '0',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_slug` (`slug`),
  KEY `idx_status` (`status`),
  KEY `idx_parent` (`parent_id`),
  CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Bebidas','bebidas','Jugos, refrescos, agua y bebidas alcohólicas',NULL,NULL,1,'active','2026-03-26 12:20:10','2026-03-26 12:20:10'),(2,'Lácteos','lacteos','Leche, queso, yogurt y derivados',NULL,NULL,2,'active','2026-03-26 12:20:10','2026-03-26 12:20:10'),(3,'Carnes','carnes','Res, cerdo, pollo y embutidos',NULL,NULL,3,'active','2026-03-26 12:20:10','2026-03-26 12:20:10'),(4,'Frutas y Verduras','frutas-y-verduras','Frutas frescas y vegetales',NULL,NULL,4,'active','2026-03-26 12:20:10','2026-03-26 12:20:10'),(5,'Panadería','panaderia','Pan, repostería y productos horneados',NULL,NULL,5,'active','2026-03-26 12:20:10','2026-03-26 12:20:10'),(6,'Limpieza','limpieza','Productos de limpieza del hogar',NULL,NULL,6,'active','2026-03-26 12:20:10','2026-03-26 12:20:10'),(7,'Cuidado Personal','cuidado-personal','Higiene y cuidado personal',NULL,NULL,7,'active','2026-03-26 12:20:10','2026-03-26 12:20:10'),(8,'Snacks','snacks','Galletas, chips y dulces',NULL,NULL,8,'active','2026-03-26 12:20:10','2026-03-26 12:20:10'),(9,'Congelados','congelados','Productos congelados y helados',NULL,NULL,9,'active','2026-03-26 12:20:10','2026-03-26 12:20:10'),(10,'Abarrotes','abarrotes','Arroz, pasta, enlatados y granos',NULL,NULL,10,'active','2026-03-26 12:20:10','2026-03-26 12:20:10');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_addresses`
--

DROP TABLE IF EXISTS `customer_addresses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer_addresses` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `label` varchar(50) DEFAULT 'Home',
  `address_line1` varchar(255) NOT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) DEFAULT NULL,
  `postal_code` varchar(20) DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `customer_addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_addresses`
--

LOCK TABLES `customer_addresses` WRITE;
/*!40000 ALTER TABLE `customer_addresses` DISABLE KEYS */;
/*!40000 ALTER TABLE `customer_addresses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_movements`
--

DROP TABLE IF EXISTS `inventory_movements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_movements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `type` enum('entry','exit','adjustment','sale','return') NOT NULL,
  `quantity` int(11) NOT NULL,
  `previous_stock` int(11) NOT NULL,
  `new_stock` int(11) NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `notes` text,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `created_by` (`created_by`),
  KEY `idx_product` (`product_id`),
  KEY `idx_type` (`type`),
  KEY `idx_created` (`created_at`),
  CONSTRAINT `inventory_movements_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `inventory_movements_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_movements`
--

LOCK TABLES `inventory_movements` WRITE;
/*!40000 ALTER TABLE `inventory_movements` DISABLE KEYS */;
INSERT INTO `inventory_movements` VALUES (1,18,'sale',-1,24,23,'order',1,'Order #ORD-20260326-C152',1,'2026-03-26 15:49:06'),(2,11,'sale',-4,76,72,'order',2,'Order #ORD-20260326-C815',1,'2026-03-26 15:53:55'),(3,18,'sale',-3,20,17,'order',2,'Order #ORD-20260326-C815',1,'2026-03-26 15:53:55'),(4,13,'sale',-3,37,34,'order',2,'Order #ORD-20260326-C815',1,'2026-03-26 15:53:55');
/*!40000 ALTER TABLE `inventory_movements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL DEFAULT 'info',
  `title` varchar(255) NOT NULL,
  `message` text,
  `data` text,
  `link` varchar(500) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_is_read` (`is_read`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (2,'new_order','Nuevo Pedido #ORD-20260326-5BAB','Admin System ha realizado un pedido por RD$ 112.10','{\"order_id\":3,\"order_number\":\"ORD-20260326-5BAB\",\"total\":\"112.10\"}','admin/orders/3',1,'2026-03-26 20:01:43'),(3,'new_order','Nuevo Pedido #ORD-20260326-32CB','Admin System ha realizado un pedido por RD$ 62.54','{\"order_id\":4,\"order_number\":\"ORD-20260326-32CB\",\"total\":\"62.54\"}','admin/orders/4',1,'2026-03-26 20:19:48');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_items`
--

DROP TABLE IF EXISTS `order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `product_sku` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `tax_rate` decimal(5,2) DEFAULT '0.00',
  `subtotal` decimal(12,2) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_items`
--

LOCK TABLES `order_items` WRITE;
/*!40000 ALTER TABLE `order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `orders`
--

DROP TABLE IF EXISTS `orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(50) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `customer_name` varchar(200) DEFAULT NULL,
  `customer_email` varchar(191) DEFAULT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `order_type` enum('online','in_store') DEFAULT 'online',
  `status` enum('pending','confirmed','processing','ready','delivered','completed','cancelled','refunded') DEFAULT 'pending',
  `payment_status` enum('pending','paid','partial','refunded') DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `tax_amount` decimal(12,2) DEFAULT '0.00',
  `discount_amount` decimal(12,2) DEFAULT '0.00',
  `total_amount` decimal(12,2) NOT NULL,
  `shipping_address` text,
  `notes` text,
  `created_by` int(11) DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `created_by` (`created_by`),
  KEY `idx_user` (`user_id`),
  KEY `idx_status` (`status`),
  KEY `idx_payment_status` (`payment_status`),
  KEY `idx_created` (`created_at`),
  CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `orders`
--

LOCK TABLES `orders` WRITE;
/*!40000 ALTER TABLE `orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `payments`
--

DROP TABLE IF EXISTS `payments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `payment_method` enum('cash','card_pos','stripe','bank_transfer') NOT NULL,
  `status` enum('pending','completed','failed','refunded') DEFAULT 'pending',
  `amount` decimal(12,2) NOT NULL,
  `reference` varchar(191) DEFAULT NULL,
  `stripe_payment_intent_id` varchar(191) DEFAULT NULL,
  `notes` text,
  `processed_by` int(11) DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `processed_by` (`processed_by`),
  KEY `idx_order` (`order_id`),
  KEY `idx_method` (`payment_method`),
  KEY `idx_status` (`status`),
  KEY `idx_stripe` (`stripe_payment_intent_id`),
  CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `payments`
--

LOCK TABLES `payments` WRITE;
/*!40000 ALTER TABLE `payments` DISABLE KEYS */;
/*!40000 ALTER TABLE `payments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_images`
--

DROP TABLE IF EXISTS `product_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_images`
--

LOCK TABLES `product_images` WRITE;
/*!40000 ALTER TABLE `product_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `product_images` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `products`
--

DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sku` varchar(50) NOT NULL,
  `barcode` varchar(50) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(191) NOT NULL,
  `description` text,
  `category_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `cost_price` decimal(12,2) DEFAULT '0.00',
  `sale_price` decimal(12,2) NOT NULL,
  `compare_price` decimal(12,2) DEFAULT NULL,
  `stock` int(11) DEFAULT '0',
  `min_stock` int(11) DEFAULT '5',
  `max_stock` int(11) DEFAULT '1000',
  `unit` varchar(20) DEFAULT 'unit',
  `image` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `tax_rate` decimal(5,2) DEFAULT '18.00',
  `weight` decimal(8,3) DEFAULT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sku` (`sku`),
  UNIQUE KEY `slug` (`slug`),
  KEY `idx_category` (`category_id`),
  KEY `idx_supplier` (`supplier_id`),
  KEY `idx_barcode` (`barcode`),
  KEY `idx_featured` (`is_featured`),
  KEY `idx_active` (`is_active`),
  FULLTEXT KEY `ft_search` (`name`,`description`,`brand`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `products`
--

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'BEB-001','7501055300112','Coca-Cola 2L','coca-cola-2l','Refresco Coca-Cola botella de 2 litros',1,NULL,45.00,75.00,85.00,120,10,1000,'unit','prod_69c58d337b86f3.33165664.jpeg',1,1,18.00,NULL,'Coca-Cola','2026-03-26 12:28:11','2026-03-26 15:46:59'),(2,'BEB-002','7501055300129','Agua Crystal 500ml','agua-crystal-500ml','Agua purificada Crystal botella 500ml',1,NULL,8.00,20.00,NULL,200,20,1000,'unit','agua-crystal.jpg',0,1,18.00,NULL,'Crystal','2026-03-26 12:28:11','2026-03-26 14:59:35'),(3,'BEB-003','7501055300136','Jugo de Naranja Country Club 1L','jugo-naranja-country-club-1l','Jugo de naranja natural Country Club 1 litro',1,NULL,35.00,65.00,75.00,80,10,1000,'unit','jugo-naranja.jpg',1,1,18.00,NULL,'Country Club','2026-03-26 12:28:11','2026-03-26 14:59:35'),(4,'BEB-004','7501055300143','Cerveza Presidente Lata','cerveza-presidente-lata','Cerveza Presidente lata 355ml',1,NULL,30.00,55.00,NULL,150,15,1000,'unit','cerveza-presidente.jpg',0,1,18.00,NULL,'Presidente','2026-03-26 12:28:11','2026-03-26 14:59:35'),(5,'LAC-001','7501055300150','Leche Milex Entera 1L','leche-milex-entera-1l','Leche entera Milex cartón 1 litro',2,NULL,40.00,72.00,80.00,90,10,1000,'unit','leche-entera.jpg',1,1,18.00,NULL,'Milex','2026-03-26 12:28:11','2026-03-26 14:59:36'),(6,'LAC-002','7501055300167','Queso de Freír Sosúa 350g','queso-freir-sosua-350g','Queso cheddar Sosúa rebanado 200 gramos',2,NULL,55.00,120.00,140.00,45,5,1000,'unit','queso-freir.jpg',1,1,18.00,NULL,'Sosúa','2026-03-26 12:28:11','2026-03-26 15:17:21'),(7,'LAC-003','7501055300174','Café Santo Domingo Molido 250g','cafe-santo-domingo-250g','Yogurt Yoplait sabor fresa vaso 150g',10,NULL,18.00,95.00,110.00,60,10,1000,'unit','cafe-santo-domingo.jpg',1,1,18.00,NULL,'Yoplait','2026-03-26 12:28:11','2026-03-26 15:17:21'),(8,'CAR-001','7501055300181','Pechuga de Pollo Fresca (lb)','pechuga-pollo-fresca-lb','Pechuga de pollo fresca por libra',3,NULL,55.00,89.00,99.00,50,5,1000,'lb','pechuga-pollo.jpg',1,1,18.00,NULL,'Granja','2026-03-26 12:28:11','2026-03-26 14:59:36'),(9,'CAR-002','7501055300198','Salami Induveca 450g','salami-induveca-450g','Salami de res Induveca 450g',3,NULL,65.00,89.00,105.00,35,5,1000,'unit','salami-induveca.jpg',1,1,18.00,NULL,'Induveca','2026-03-26 12:28:11','2026-03-26 15:17:21'),(10,'FRU-001','7501055300204','Plátano Verde (unidad)','platano-verde-unidad','Plátano maduro dominicano por unidad',4,NULL,5.00,10.00,NULL,200,20,1000,'unit','platano-verde.jpg',0,1,18.00,NULL,'Local','2026-03-26 12:28:11','2026-03-26 15:17:21'),(11,'FRU-002','7501055300211','Aguacate (unidad)','aguacate-unidad','Aguacate dominicano por unidad',4,NULL,15.00,35.00,45.00,72,10,1000,'unit','aguacate.jpg',1,1,18.00,NULL,'Local','2026-03-26 12:28:11','2026-03-26 15:53:55'),(12,'PAN-001','7501055300228','Pan Sobao Dominicano','pan-sobao-dominicano','Pan de agua fresco horneado del día',5,NULL,3.00,25.00,NULL,150,20,1000,'unit','pan-sobao.jpg',0,1,18.00,NULL,'Panadería','2026-03-26 12:28:11','2026-03-26 15:17:21'),(13,'PAN-002','7501055300235','Bizcocho Dominicano Doña María','bizcocho-dominicano-dona-maria','Paquete de 6 bizcochos dominicanos',5,NULL,25.00,35.00,45.00,34,5,1000,'unit','bizcocho-dominicano.jpg',0,1,18.00,NULL,'Panadería','2026-03-26 12:28:11','2026-03-26 15:53:55'),(14,'LIM-001','7501055300242','Jabón de Cuaba La Corona','jabon-cuaba-la-corona','Detergente en polvo Ace bolsa 1 kilogramo',6,NULL,45.00,35.00,NULL,60,5,1000,'unit','jabon-cuaba.jpg',0,1,18.00,NULL,'Ace','2026-03-26 12:28:11','2026-03-26 15:17:21'),(15,'LIM-002','7501055300259','Sazón Ranchero 12 sobres','sazon-ranchero-12','Cloro líquido Bravo 1 galón',10,NULL,30.00,25.00,30.00,40,5,1000,'unit','sazon-ranchero.jpg',0,1,18.00,NULL,'Bravo','2026-03-26 12:28:11','2026-03-26 15:17:21'),(16,'SNK-001','7501055300266','Malta Morena Lata','malta-morena-lata','Doritos sabor nacho bolsa 150 gramos',1,NULL,25.00,30.00,35.00,70,10,1000,'unit','malta-morena.jpg',1,1,18.00,NULL,'Doritos','2026-03-26 12:28:11','2026-03-26 15:17:21'),(17,'SNK-002','7501055300273','Casabe Artesanal','casabe-artesanal','Galletas Oreo paquete familiar 432g',10,NULL,40.00,40.00,NULL,55,5,1000,'unit','casabe.jpg',0,1,18.00,NULL,'Oreo','2026-03-26 12:28:11','2026-03-26 15:17:21'),(18,'CON-001','7501055300280','Yuca Fresca (lb)','yuca-fresca-lb','Pizza congelada de pepperoni lista para hornear',4,NULL,70.00,18.00,25.00,17,5,1000,'unit','prod_69c590854a3449.93077181.webp',1,1,18.00,NULL,'DiGiorno','2026-03-26 12:28:11','2026-03-26 16:01:09'),(19,'ABA-001','7501055300297','Arroz Selecto La Garza 5lb','arroz-selecto-la-garza-5lb','Arroz blanco selecto La Garza bolsa 5 libras',10,NULL,80.00,145.00,160.00,100,10,1000,'unit','arroz.jpg',1,1,18.00,NULL,'La Garza','2026-03-26 12:28:11','2026-03-26 14:59:36');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_order_items`
--

DROP TABLE IF EXISTS `purchase_order_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchase_order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity_ordered` int(11) NOT NULL,
  `quantity_received` int(11) DEFAULT '0',
  `unit_cost` decimal(12,2) NOT NULL,
  `total_cost` decimal(12,2) NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `purchase_order_id` (`purchase_order_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `purchase_order_items_ibfk_1` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_order_items`
--

LOCK TABLES `purchase_order_items` WRITE;
/*!40000 ALTER TABLE `purchase_order_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_order_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_orders`
--

DROP TABLE IF EXISTS `purchase_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchase_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_number` varchar(50) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `status` enum('draft','ordered','partial','received','cancelled') DEFAULT 'draft',
  `subtotal` decimal(12,2) DEFAULT '0.00',
  `tax_amount` decimal(12,2) DEFAULT '0.00',
  `total` decimal(12,2) DEFAULT '0.00',
  `notes` text,
  `expected_date` date DEFAULT NULL,
  `received_date` date DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_number` (`order_number`),
  KEY `supplier_id` (`supplier_id`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `purchase_orders_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`),
  CONSTRAINT `purchase_orders_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_orders`
--

LOCK TABLES `purchase_orders` WRITE;
/*!40000 ALTER TABLE `purchase_orders` DISABLE KEYS */;
/*!40000 ALTER TABLE `purchase_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(191) NOT NULL,
  `setting_value` text,
  `setting_type` enum('string','number','boolean','json') DEFAULT 'string',
  `description` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `settings`
--

LOCK TABLES `settings` WRITE;
/*!40000 ALTER TABLE `settings` DISABLE KEYS */;
INSERT INTO `settings` VALUES (1,'store_name','SuperMarket','string','Store display name','2026-03-26 12:20:10','2026-03-26 12:20:10'),(2,'store_phone','809-555-0001','string','Store phone number','2026-03-26 12:20:10','2026-03-26 12:20:10'),(3,'store_email','info@supermarket.com','string','Store email','2026-03-26 12:20:10','2026-03-26 12:20:10'),(4,'store_address','Santo Domingo, República Dominicana','string','Store address','2026-03-26 12:20:10','2026-03-26 12:20:10'),(5,'currency','RD$','string','Currency symbol','2026-03-26 12:20:10','2026-03-26 12:20:10'),(6,'tax_rate','18.00','number','Default tax rate (ITBIS)','2026-03-26 12:20:10','2026-03-26 12:20:10'),(7,'low_stock_threshold','10','number','Low stock alert threshold','2026-03-26 12:20:10','2026-03-26 12:20:10'),(8,'allow_guest_checkout','true','boolean','Allow guest checkout','2026-03-26 12:20:10','2026-03-26 12:20:10'),(9,'stripe_enabled','false','boolean','Enable Stripe payments','2026-03-26 12:20:10','2026-03-26 12:20:10');
/*!40000 ALTER TABLE `settings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `stock_alerts`
--

DROP TABLE IF EXISTS `stock_alerts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stock_alerts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `alert_type` enum('low_stock','out_of_stock') NOT NULL,
  `current_stock` int(11) NOT NULL,
  `min_stock` int(11) NOT NULL,
  `is_resolved` tinyint(1) DEFAULT '0',
  `resolved_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_product` (`product_id`),
  KEY `idx_resolved` (`is_resolved`),
  CONSTRAINT `stock_alerts_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `stock_alerts`
--

LOCK TABLES `stock_alerts` WRITE;
/*!40000 ALTER TABLE `stock_alerts` DISABLE KEYS */;
/*!40000 ALTER TABLE `stock_alerts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `contact_person` varchar(200) DEFAULT NULL,
  `email` varchar(191) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `rnc` varchar(20) DEFAULT NULL,
  `address` text,
  `city` varchar(100) DEFAULT NULL,
  `notes` text,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(191) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('admin','manager','cashier','customer') DEFAULT 'customer',
  `status` enum('active','inactive','locked') DEFAULT 'active',
  `login_attempts` int(11) DEFAULT '0',
  `locked_until` datetime DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `email_verified_at` datetime DEFAULT NULL,
  `reset_token` varchar(191) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_role` (`role`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','System','admin@supermarket.com','$2y$10$2rdbbY2/Nna7kkBiqIpxKeeE471leyYe5HzR5jHTw6XeJBrHnxMCO','','admin','active',0,NULL,'2026-03-26 16:34:29',NULL,NULL,NULL,NULL,1,'2026-03-26 12:20:10','2026-03-26 16:34:29'),(2,'Prueba','prueba1','dmfrias.ms@gmail.com','$2y$10$kI0ig9BWylYXqn6abfwKAeeSVaTEaY5Bi5LEXzqCzsu/A5HpELIoi','8492245643','customer','active',0,NULL,'2026-03-26 13:41:14',NULL,NULL,NULL,NULL,1,'2026-03-26 12:22:59','2026-03-26 13:41:14');
Warning: A partial dump from a server that has GTIDs will by default include the GTIDs of all transactions, even those that changed suppressed parts of the database. If you don't want to restore GTIDs, pass --set-gtid-purged=OFF. To make a complete dump, pass --all-databases --triggers --routines --events. 
Warning: A dump from a server that has GTIDs enabled will by default include the GTIDs of all transactions, even those that were executed during its extraction and might not be represented in the dumped data. This might result in an inconsistent data dump. 
In order to ensure a consistent backup of the database, pass --single-transaction or --lock-all-tables or --master-data. 
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- GTID state at the end of the backup 
--

SET @@GLOBAL.GTID_PURGED='8c3c60b3-c6ff-11ea-b9d2-42010a960031:1-1319027298';
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-26 20:52:55

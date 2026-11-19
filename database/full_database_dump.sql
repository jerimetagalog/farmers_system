-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: farmers_system_db
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_logs`
--

DROP TABLE IF EXISTS `activity_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event_type` varchar(50) NOT NULL,
  `username_typed` varchar(100) DEFAULT NULL,
  `user_id` varchar(9) DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_logs_event_type` (`event_type`),
  KEY `idx_logs_ip` (`ip_address`),
  KEY `idx_logs_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_logs`
--

LOCK TABLES `activity_logs` WRITE;
/*!40000 ALTER TABLE `activity_logs` DISABLE KEYS */;
INSERT INTO `activity_logs` VALUES (1,'REGISTERED','admin_officer','2020-1050','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64)','Initial agricultural administrator profile created.','2026-11-19 08:00:00'),(2,'ACCOUNT_VERIFIED','admin_officer','2020-1050','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64)','Administrator verified successfully.','2026-11-19 08:00:00'),(3,'REGISTERED','farmer_juan','2020-1051','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64)','New farmer account registered.','2026-11-19 08:15:00'),(4,'ACCOUNT_VERIFIED','farmer_juan','2020-1051','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64)','Farmer OTP verified successfully.','2026-11-19 08:15:00'),(5,'REGISTERED','farmer_maria','2020-1052','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64)','New farmer account registered.','2026-11-19 08:30:00'),(6,'ACCOUNT_VERIFIED','farmer_maria','2020-1052','127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64)','Farmer OTP verified successfully.','2026-11-19 08:30:00');
/*!40000 ALTER TABLE `activity_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assistance_requests`
--

DROP TABLE IF EXISTS `assistance_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `assistance_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `farmer_id` varchar(9) NOT NULL,
  `program_category` varchar(50) NOT NULL,
  `preferred_date` date NOT NULL,
  `farm_location` varchar(100) NOT NULL,
  `request_details` text NOT NULL,
  `status` enum('Pending','Approved','Dispatched','Rejected') NOT NULL DEFAULT 'Pending',
  `officer_notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_requests_farmer` (`farmer_id`),
  KEY `idx_requests_status` (`status`),
  KEY `idx_requests_date` (`preferred_date`),
  CONSTRAINT `fk_requests_farmer` FOREIGN KEY (`farmer_id`) REFERENCES `users` (`id_number`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assistance_requests`
--

LOCK TABLES `assistance_requests` WRITE;
/*!40000 ALTER TABLE `assistance_requests` DISABLE KEYS */;
INSERT INTO `assistance_requests` VALUES (1,'2020-1051','Fertilizer & Seed Subsidy','2026-11-25','Purok 2 Green Valley, Los Angeles, Butuan City','Requesting 4 bags of Certified Inbred Rice Seeds and Urea fertilizer subsidy for upcoming wet cropping season.','Approved','Allocated 4 bags NSIC Rc222 and 2 bags fertilizer voucher under Municipal Agri Office.','2026-11-19 08:45:00','2026-11-19 09:10:00'),(2,'2020-1051','Soil & Agronomy Advisory','2026-12-02','Purok 2 Green Valley, Los Angeles, Butuan City','Soil pH and nutrient deficiency test needed due to yellowing of lower rice leaves.','Pending',NULL,'2026-11-19 09:20:00','2026-11-19 09:20:00'),(3,'2020-1052','Crop Insurance & Protection','2026-11-28','Purok 5 Riverside, Baan, Butuan City','Enrollment for Philippine Crop Insurance Corporation (PCIC) coverage for 1.5 hectares hybrid corn farm.','Pending',NULL,'2026-11-19 09:35:00','2026-11-19 09:35:00');
/*!40000 ALTER TABLE `assistance_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `authentication_answers`
--

DROP TABLE IF EXISTS `authentication_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `authentication_answers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(9) NOT NULL,
  `question_key` varchar(10) NOT NULL,
  `answer_hash` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_user_question` (`user_id`,`question_key`),
  CONSTRAINT `fk_auth_answers_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_number`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `authentication_answers`
--

LOCK TABLES `authentication_answers` WRITE;
/*!40000 ALTER TABLE `authentication_answers` DISABLE KEYS */;
INSERT INTO `authentication_answers` VALUES (1,'2020-1050','Q1','$2y$10$O5ic/B/dFAk5WG2FmkWoDegybWrhpwRyjCBSVRZi.FcmL.wQGxgd6','2026-11-19 08:00:00'),(2,'2020-1050','Q2','$2y$10$dP2izUWO02tbC67689WyOOutm071GiphMTaV5njCwAenxHDBqJO7i','2026-11-19 08:00:00'),(3,'2020-1050','Q5','$2y$10$2.LtI8szf.vPcbHD1TT9mOD6Nh3HFKBn6YRX530TmP5SHNtbE5X46','2026-11-19 08:00:00'),(4,'2020-1051','Q1','$2y$10$O5ic/B/dFAk5WG2FmkWoDegybWrhpwRyjCBSVRZi.FcmL.wQGxgd6','2026-11-19 08:15:00'),(5,'2020-1051','Q2','$2y$10$dP2izUWO02tbC67689WyOOutm071GiphMTaV5njCwAenxHDBqJO7i','2026-11-19 08:15:00'),(6,'2020-1051','Q5','$2y$10$2.LtI8szf.vPcbHD1TT9mOD6Nh3HFKBn6YRX530TmP5SHNtbE5X46','2026-11-19 08:15:00'),(7,'2020-1052','Q1','$2y$10$O5ic/B/dFAk5WG2FmkWoDegybWrhpwRyjCBSVRZi.FcmL.wQGxgd6','2026-11-19 08:30:00'),(8,'2020-1052','Q2','$2y$10$dP2izUWO02tbC67689WyOOutm071GiphMTaV5njCwAenxHDBqJO7i','2026-11-19 08:30:00'),(9,'2020-1052','Q5','$2y$10$2.LtI8szf.vPcbHD1TT9mOD6Nh3HFKBn6YRX530TmP5SHNtbE5X46','2026-11-19 08:30:00');
/*!40000 ALTER TABLE `authentication_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ip_blocks`
--

DROP TABLE IF EXISTS `ip_blocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ip_blocks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `failure_count` int(11) NOT NULL DEFAULT 1,
  `first_failure_at` datetime NOT NULL,
  `blocked_until` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_ip_blocks_ip` (`ip_address`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ip_blocks`
--

LOCK TABLES `ip_blocks` WRITE;
/*!40000 ALTER TABLE `ip_blocks` DISABLE KEYS */;
/*!40000 ALTER TABLE `ip_blocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `otps`
--

DROP TABLE IF EXISTS `otps`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `otps` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(9) NOT NULL,
  `email` varchar(100) NOT NULL,
  `otp_hash` varchar(255) NOT NULL,
  `purpose` enum('register','login','reset') NOT NULL,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  `expires_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_otps_lookup` (`user_id`,`purpose`,`is_used`,`expires_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `otps`
--

LOCK TABLES `otps` WRITE;
/*!40000 ALTER TABLE `otps` DISABLE KEYS */;
/*!40000 ALTER TABLE `otps` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_history`
--

DROP TABLE IF EXISTS `password_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` varchar(9) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_pwd_history_user` (`user_id`,`created_at`),
  CONSTRAINT `fk_pwd_history_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_number`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_history`
--

LOCK TABLES `password_history` WRITE;
/*!40000 ALTER TABLE `password_history` DISABLE KEYS */;
INSERT INTO `password_history` VALUES (1,'2020-1050','$2y$10$wYd22L6DS5tBxeI4sqZ2OuUv.LsfW1HEyqSRUrH.oXUEtvvxb.CgO','2026-11-19 08:00:00'),(2,'2020-1051','$2y$10$sXx4NxMfGnkGLKoOsjJpneD2GmHeCRHh6j2/LoDdHv6lRqU/Ojlai','2026-11-19 08:15:00'),(3,'2020-1052','$2y$10$sXx4NxMfGnkGLKoOsjJpneD2GmHeCRHh6j2/LoDdHv6lRqU/Ojlai','2026-11-19 08:30:00');
/*!40000 ALTER TABLE `password_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `security_flags`
--

DROP TABLE IF EXISTS `security_flags`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `security_flags` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pattern_type` varchar(50) NOT NULL,
  `severity` enum('LOW','MED','HIGH') NOT NULL DEFAULT 'MED',
  `target` varchar(100) NOT NULL,
  `details` text NOT NULL,
  `flagged_date` date NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_flag_unique_daily` (`pattern_type`,`target`,`flagged_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `security_flags`
--

LOCK TABLES `security_flags` WRITE;
/*!40000 ALTER TABLE `security_flags` DISABLE KEYS */;
/*!40000 ALTER TABLE `security_flags` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id_number` varchar(9) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `family_name` varchar(50) NOT NULL,
  `name_extension` varchar(10) DEFAULT NULL,
  `birthdate` date NOT NULL,
  `age` int(11) NOT NULL,
  `purok_street` varchar(50) NOT NULL,
  `barangay` varchar(50) NOT NULL,
  `municipality_city` varchar(50) NOT NULL,
  `province` varchar(50) NOT NULL,
  `country` varchar(50) NOT NULL,
  `zip_code` varchar(4) NOT NULL,
  `email` varchar(100) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `contact_number_encrypted` text NOT NULL,
  `farmer_type` varchar(35) NOT NULL,
  `primary_crop` varchar(50) NOT NULL,
  `emergency_contact_encrypted` text DEFAULT NULL,
  `role` enum('farmer','admin') NOT NULL DEFAULT 'farmer',
  `is_verified` tinyint(1) NOT NULL DEFAULT 0,
  `failed_logins` int(11) NOT NULL DEFAULT 0,
  `lockout_tier` int(11) NOT NULL DEFAULT 0,
  `locked_until` datetime DEFAULT NULL,
  `privacy_consent_at` datetime NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id_number`),
  UNIQUE KEY `idx_users_email` (`email`),
  UNIQUE KEY `idx_users_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES ('2020-1050','Jerime','V','Tagalog',NULL,'1998-05-12',28,'Purok 4','Ampayon','Butuan City','Agusan del Norte','Philippines','8600','Jerime.Tagalog@csucc.edu.ph','admin_officer','$2y$10$wYd22L6DS5tBxeI4sqZ2OuUv.LsfW1HEyqSRUrH.oXUEtvvxb.CgO','AzNsJtCdz5HdrvIREXXmbuORCjpAE1Pmz+hQqJZJ0FE=','Commercial Farmer','Rice',NULL,'admin',1,0,0,NULL,'2026-11-19 08:00:00','2026-11-19 08:00:00','2026-11-19 08:00:00'),('2020-1051','Juan','Dela','Cruz',NULL,'1985-08-20',41,'Purok 2 Green Valley','Los Angeles','Butuan City','Agusan del Norte','Philippines','8600','Jerime.Tagalog+juan@csucc.edu.ph','farmer_juan','$2y$10$sXx4NxMfGnkGLKoOsjJpneD2GmHeCRHh6j2/LoDdHv6lRqU/Ojlai','0M902eJrITdk69lgg0rue4IRwkTgNvvimqHDNjGYWd8=','Smallholder Farmer','Rice',NULL,'farmer',1,0,0,NULL,'2026-11-19 08:15:00','2026-11-19 08:15:00','2026-11-19 08:15:00'),('2020-1052','Maria','Santos','Reyes',NULL,'1990-11-14',36,'Purok 5 Riverside','Baan','Butuan City','Agusan del Norte','Philippines','8600','Jerime.Tagalog+maria@csucc.edu.ph','farmer_maria','$2y$10$sXx4NxMfGnkGLKoOsjJpneD2GmHeCRHh6j2/LoDdHv6lRqU/Ojlai','lDAPKstN5QZRcNti8qJeSTNekmHHYy1ziIb6pAnGYvs=','Tenant Farmer','Corn',NULL,'farmer',1,0,0,NULL,'2026-11-19 08:30:00','2026-11-19 08:30:00','2026-11-19 08:30:00');
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

-- Dump completed on 2026-11-19 11:05:52

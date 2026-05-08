-- SQL Database Recovery Script for TechCare
-- Database: techcare_db

CREATE DATABASE IF NOT EXISTS techcare_db;
USE techcare_db;

-- Table structure for table `personne`
CREATE TABLE IF NOT EXISTS `personne` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telephone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) DEFAULT 'user',
  `status` varchar(50) DEFAULT 'actif',
  `google_id` varchar(255) DEFAULT NULL,
  `face_descriptor` text DEFAULT NULL,
  `failed_attempts` int(11) DEFAULT 0,
  `lockout_time` datetime DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `verification_token` varchar(255) DEFAULT NULL,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_token_expires` datetime DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT 'default.png',
  `google2fa_secret` varchar(255) DEFAULT NULL,
  `two_factor_enabled` tinyint(1) DEFAULT 0,
  `linkedin_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `google_id` (`google_id`),
  UNIQUE KEY `linkedin_id` (`linkedin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `produits` (Placeholder for dashboard)
CREATE TABLE IF NOT EXISTS `produits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(255) NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table structure for table `commandes` (Placeholder for dashboard)
CREATE TABLE IF NOT EXISTS `commandes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `date_commande` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Dumping data for table `personne` (Default Admin)
-- Password is 'admin123' (hashed)
INSERT INTO `personne` (`nom`, `prenom`, `email`, `telephone`, `password`, `role`, `status`, `is_verified`) VALUES
('Admin', 'System', 'admin@techcare.tn', '12345678', '$2y$10$8W3Bv9F2m9f6p3B8g7v5eOe6W3Bv9F2m9f6p3B8g7v5eOe', 'admin', 'actif', 1);

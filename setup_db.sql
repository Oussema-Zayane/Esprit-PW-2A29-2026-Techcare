-- ═══════════════════════════════════════════════════════════════════
--  TechCare – Script de création de la table PRODUIT
--  Exécutez ce script dans phpMyAdmin ou MySQL CLI
-- ═══════════════════════════════════════════════════════════════════

-- ═ Créer la base de données (si elle n'existe pas) ═
-- CREATE DATABASE IF NOT EXISTS techcare;
-- USE techcare;

-- ═ Créer la table PRODUIT ═
CREATE TABLE IF NOT EXISTS produit (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(255) NOT NULL COMMENT 'Nom du produit',
  description TEXT COMMENT 'Description détaillée',
  prix DECIMAL(10, 2) NOT NULL COMMENT 'Prix en DT',
  stock INT DEFAULT 0 COMMENT 'Quantité en stock',
  categorie VARCHAR(100) COMMENT 'Catégorie (Smartphone, Laptop, etc)',
  marque VARCHAR(100) COMMENT 'Marque du produit',
  image VARCHAR(500) COMMENT 'URL de l\'image',
  etat ENUM('neuf', 'occasion') DEFAULT 'neuf' COMMENT 'État du produit',
  date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de création',
  date_modif TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Date de modification',

  -- Index pour optimiser les requêtes
  INDEX idx_categorie (categorie),
  INDEX idx_marque (marque),
  INDEX idx_prix (prix),
  INDEX idx_date_ajout (date_ajout)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ═ Données de test (optionnel) ═
INSERT INTO produit (nom, description, prix, stock, categorie, marque, image, etat) VALUES
('iPhone 15 Pro', 'Dernier smartphone Apple avec écran OLED', 1499.99, 10, 'Smartphone', 'Apple', 'https://via.placeholder.com/300?text=iPhone15', 'neuf'),
('Samsung Galaxy S24', 'Téléphone flagship Samsung', 1299.99, 15, 'Smartphone', 'Samsung', 'https://via.placeholder.com/300?text=S24', 'neuf'),
('MacBook Pro 14"', 'Ordinateur portable puissant pour le travail', 2499.99, 5, 'Laptop', 'Apple', 'https://via.placeholder.com/300?text=MacBook', 'neuf'),
('Dell XPS 15', 'Laptop performant Windows', 1799.99, 8, 'Laptop', 'Dell', 'https://via.placeholder.com/300?text=XPS', 'neuf'),
('iPad Air', 'Tablette polyvalente', 899.99, 12, 'Tablette', 'Apple', 'https://via.placeholder.com/300?text=iPad', 'neuf'),
('AirPods Pro', 'Écouteurs sans fil avec réduction de bruit', 399.99, 20, 'Accessoires', 'Apple', 'https://via.placeholder.com/300?text=AirPods', 'neuf'),
('Samsung Watch 6', 'Montre connectée', 499.99, 7, 'Montre', 'Samsung', 'https://via.placeholder.com/300?text=Watch', 'neuf'),
('iPhone 13 occasion', 'iPhone 13 en bon état', 699.99, 3, 'Smartphone', 'Apple', 'https://via.placeholder.com/300?text=iPhone13', 'occasion');

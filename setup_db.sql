-- ═══════════════════════════════════════════════════════════════════
--  TechCare – Script de création de la base de données avec JOINTURE
--  Exécutez ce script dans phpMyAdmin ou MySQL CLI
-- ═══════════════════════════════════════════════════════════════════

-- ═ Créer la base de données (si elle n'existe pas) ═
CREATE DATABASE IF NOT EXISTS techcare;
USE techcare;

-- ═ SUPPRIMER les anciennes tables si elles existent ═
DROP TABLE IF EXISTS produit;
DROP TABLE IF EXISTS categorie;

-- ═ Créer la table CATEGORIE ═
CREATE TABLE IF NOT EXISTS categorie (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(100) NOT NULL UNIQUE COMMENT 'Nom de la catégorie',
  description TEXT COMMENT 'Description de la catégorie',
  image VARCHAR(500) COMMENT 'Image de la catégorie',
  date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de création',
  date_modif TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Date de modification',
  
  INDEX idx_nom (nom)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ═ Créer la table PRODUIT ═
CREATE TABLE IF NOT EXISTS produit (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nom VARCHAR(255) NOT NULL COMMENT 'Nom du produit',
  description TEXT COMMENT 'Description détaillée',
  prix DECIMAL(10, 2) NOT NULL COMMENT 'Prix en DT',
  stock INT DEFAULT 0 COMMENT 'Quantité en stock',
  categorie_id INT COMMENT 'Clé étrangère vers la table categorie',
  marque VARCHAR(100) COMMENT 'Marque du produit',
  image VARCHAR(500) COMMENT 'URL de l\'image',
  etat ENUM('neuf', 'occasion') DEFAULT 'neuf' COMMENT 'État du produit',
  views INT DEFAULT 0 COMMENT 'Nombre de vues du produit',
  ventes INT DEFAULT 0 COMMENT 'Nombre de ventes',
  date_ajout TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Date de création',
  date_modif TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Date de modification',

  -- Index pour optimiser les requêtes
  INDEX idx_categorie_id (categorie_id),
  INDEX idx_marque (marque),
  INDEX idx_prix (prix),
  INDEX idx_ventes (ventes),
  INDEX idx_views (views),
  INDEX idx_date_ajout (date_ajout),
  
  -- Contrainte de clé étrangère
  CONSTRAINT fk_categorie FOREIGN KEY (categorie_id) REFERENCES categorie(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ═ Données de test pour catégories ═
INSERT INTO categorie (nom, description, image) VALUES
('Smartphone', 'Téléphones mobiles et smartphones', 'https://via.placeholder.com/300?text=Smartphone'),
('Laptop', 'Ordinateurs portables et ultrabooks', 'https://via.placeholder.com/300?text=Laptop'),
('Tablette', 'Tablettes tactiles et iPad', 'https://via.placeholder.com/300?text=Tablette'),
('Accessoires', 'Accessoires technologiques', 'https://via.placeholder.com/300?text=Accessoires'),
('Montre', 'Montres connectées et intelligentes', 'https://via.placeholder.com/300?text=Montre');

-- ═ Données de test pour produits ═
INSERT INTO produit (nom, description, prix, stock, categorie_id, marque, image, etat) VALUES
('iPhone 15 Pro', 'Dernier smartphone Apple avec écran OLED', 1499.99, 10, 1, 'Apple', 'https://via.placeholder.com/300?text=iPhone15', 'neuf'),
('Samsung Galaxy S24', 'Téléphone flagship Samsung', 1299.99, 15, 1, 'Samsung', 'https://via.placeholder.com/300?text=S24', 'neuf'),
('MacBook Pro 14"', 'Ordinateur portable puissant pour le travail', 2499.99, 5, 2, 'Apple', 'https://via.placeholder.com/300?text=MacBook', 'neuf'),
('Dell XPS 15', 'Laptop performant Windows', 1799.99, 8, 2, 'Dell', 'https://via.placeholder.com/300?text=XPS', 'neuf'),
('iPad Air', 'Tablette polyvalente', 899.99, 12, 3, 'Apple', 'https://via.placeholder.com/300?text=iPad', 'neuf'),
('AirPods Pro', 'Écouteurs sans fil avec réduction de bruit', 399.99, 20, 4, 'Apple', 'https://via.placeholder.com/300?text=AirPods', 'neuf'),
('Samsung Watch 6', 'Montre connectée', 499.99, 7, 5, 'Samsung', 'https://via.placeholder.com/300?text=Watch', 'neuf'),
('iPhone 13 occasion', 'iPhone 13 en bon état', 699.99, 3, 1, 'Apple', 'https://via.placeholder.com/300?text=iPhone13', 'occasion');

-- ═ Test de la JOINTURE ═
-- Afficher tous les produits avec leurs catégories
SELECT p.id, p.nom as produit, c.nom as categorie, p.prix, p.stock
FROM produit p
LEFT JOIN categorie c ON p.categorie_id = c.id
ORDER BY c.nom, p.nom;

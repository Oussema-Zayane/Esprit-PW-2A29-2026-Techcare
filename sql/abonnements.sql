CREATE DATABASE IF NOT EXISTS techcare;
USE techcare;

-- Supprimer la table existante si elle existe
DROP TABLE IF EXISTS abonnements;

-- Créer la table avec la bonne structure
CREATE TABLE abonnements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_client VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    type_abonnement ENUM('mensuel', 'annuel', 'ponctuel') NOT NULL DEFAULT 'mensuel',
    prix DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    statut ENUM('actif', 'suspendu', 'expiré', 'annulé') DEFAULT 'actif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_statut (statut),
    INDEX idx_date_fin (date_fin)
);

-- Insérer des données de test
INSERT INTO abonnements (nom_client, email, type_abonnement, prix, date_debut, date_fin, statut) VALUES
('Ahmed Ben Ali', 'ahmed@techcare.com', 'mensuel', 29.90, '2026-01-01', '2026-12-31', 'actif'),
('Sara Mansouri', 'sara@techcare.com', 'annuel', 299.00, '2026-01-15', '2027-01-15', 'actif'),
('Karim Jbeli', 'karim@techcare.com', 'ponctuel', 49.90, '2026-02-01', '2026-02-15', 'expiré');
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

-- Créer la table type_abonnement pour la jointure
CREATE TABLE type_abonnement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom_type VARCHAR(50) NOT NULL UNIQUE,
    prix_base DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    duree_jours INT NOT NULL DEFAULT 30,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nom_type (nom_type)
);

-- Insérer des types d'abonnement par défaut
INSERT INTO type_abonnement (nom_type, prix_base, duree_jours, description) VALUES
('mensuel', 29.90, 30, 'Abonnement mensuel avec support technique'),
('annuel', 299.00, 365, 'Abonnement annuel avec remise et support premium'),
('ponctuel', 49.90, 15, 'Service ponctuel pour intervention spécifique');

-- Créer la table offre pour les options supplémentaires
CREATE TABLE offre (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_abonnement INT NOT NULL,
    description TEXT NOT NULL,
    prix_supplementaire DECIMAL(10,2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_abonnement) REFERENCES abonnements(id) ON DELETE CASCADE,
    INDEX idx_id_abonnement (id_abonnement)
);

-- Insérer des options d'offre de test
INSERT INTO offre (id_abonnement, description, prix_supplementaire) VALUES
(1, 'Support 24/7', 10.00),
(2, 'Formation utilisateur', 50.00);

-- Créer la table sms_logs pour le suivi des SMS
CREATE TABLE IF NOT EXISTS sms_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    telephone VARCHAR(20) NOT NULL,
    message TEXT NOT NULL,
    type_sms ENUM('bienvenue', 'rappel_j7', 'rappel_j3', 'manuel') NOT NULL DEFAULT 'manuel',
    statut ENUM('envoye', 'echoue', 'simule') NOT NULL DEFAULT 'simule',
    date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_telephone (telephone),
    INDEX idx_statut (statut),
    INDEX idx_type_sms (type_sms),
    INDEX idx_date_envoi (date_envoi)
);

-- Insérer des données de test pour sms_logs
INSERT INTO sms_logs (telephone, message, type_sms, statut, date_envoi) VALUES
('+21612345678', 'Bienvenue chez TechCare! Votre abonnement est actif.', 'bienvenue', 'envoye', '2026-04-20 10:00:00'),
('+21623456789', 'Rappel: Votre abonnement expire dans 7 jours.', 'rappel_j7', 'envoye', '2026-04-19 09:00:00'),
('+21634567890', 'Rappel: Votre abonnement expire dans 3 jours.', 'rappel_j3', 'simule', '2026-04-23 14:30:00'),
('+21645678901', 'Message manuel de test', 'manuel', 'echoue', '2026-04-18 16:00:00');
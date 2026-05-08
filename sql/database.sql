-- Supprimer les anciennes tables
DROP TABLE IF EXISTS fidelite;
DROP TABLE IF EXISTS utilisateur;
DROP TABLE IF EXISTS badge;
DROP TABLE IF EXISTS abonnement;
DROP TABLE IF EXISTS offre;

-- Table Utilisateur (avec nbPoints)
CREATE TABLE utilisateur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL,
    motDePasse VARCHAR(255) NOT NULL,
    nbPoints INT DEFAULT 0
);

-- Table Fidelite (entité principale pour CRUD)
CREATE TABLE fidelite (
    idPoints INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(100) NOT NULL,
    description TEXT,
    total INT DEFAULT 0,
    dateGain DATE
);

-- Table Badge
CREATE TABLE badge (
    idBadge INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    seuilPoints INT NOT NULL
);

-- Table Abonnement
CREATE TABLE abonnement (
    idAbonnement INT AUTO_INCREMENT PRIMARY KEY,
    dateDebut DATE,
    dateFin DATE,
    statut VARCHAR(50),
    prix FLOAT
);

-- Table Offre
CREATE TABLE offre (
    idOffre INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    duree INT,
    prix FLOAT
);

-- Insertion des données de test
INSERT INTO utilisateur (id, nom, email, motDePasse, nbPoints) VALUES
(1, 'Ayari Firas', 'ayari.firas@esprit.tn', 'password', 1250),
(2, 'EcoWarrior', 'eco@test.com', 'password', 3200),
(3, 'GreenMaster', 'green@test.com', 'password', 2450);

INSERT INTO fidelite (titre, description, total, dateGain) VALUES
('Bonus de bienvenue', 'Points offerts à l\'inscription', 500, '2025-04-01'),
('Fidélité mensuelle', 'Points gagnés pour votre fidélité', 200, '2025-04-15'),
('Anniversaire', 'Points spéciaux pour votre anniversaire', 300, '2025-04-20');

INSERT INTO badge (nom, seuilPoints) VALUES
('Éco-Poussin', 100),
('Éco-Gardien', 500),
('Éco-Guerrier', 1000),
('Éco-Maître', 2500),
('Éco-Légende', 5000);
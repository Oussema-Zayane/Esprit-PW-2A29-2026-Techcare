-- =====================================================
-- CRÉATION DE LA BASE DE DONNÉES TECHCARE
-- =====================================================

CREATE DATABASE IF NOT EXISTS techcare_db;
USE techcare_db;

-- Table des utilisateurs et points
CREATE TABLE user_points (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    points INT DEFAULT 0,
    level INT DEFAULT 1,
    total_actions INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des badges
CREATE TABLE badges (
    badge_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    points_required INT DEFAULT 0
);

-- Table des badges utilisateurs
CREATE TABLE user_badges (
    user_id INT,
    badge_id INT,
    earned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, badge_id)
);

-- Table des récompenses
CREATE TABLE rewards (
    reward_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    points_cost INT DEFAULT 0,
    stock INT DEFAULT 999,
    is_active INT DEFAULT 1
);

-- Table des récompenses échangées
CREATE TABLE user_rewards (
    user_id INT,
    reward_id INT,
    redeemed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, reward_id, redeemed_at)
);

-- Table historique des points
CREATE TABLE point_history (
    history_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    points_change INT,
    reason VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- INSERTION DES DONNÉES DE TEST
-- =====================================================

INSERT INTO user_points (user_id, username, points, level, total_actions) VALUES
(1, 'Ayari Firas', 1250, 2, 15),
(2, 'EcoWarrior', 3200, 4, 42),
(3, 'GreenMaster', 2450, 3, 28),
(4, 'GreenHero', 890, 1, 8),
(5, 'EcoCitizen', 450, 1, 5);

INSERT INTO badges (badge_id, name, description, icon, points_required) VALUES
(1, 'Éco-Poussin', 'Première action écologique', '🐣', 100),
(2, 'Éco-Gardien', '5 actions écologiques', '🦊', 500),
(3, 'Éco-Guerrier', '15 actions écologiques', '🐺', 1000),
(4, 'Éco-Maître', '30 actions écologiques', '🦅', 2500),
(5, 'Éco-Légende', '50 actions écologiques', '🐉', 5000);

INSERT INTO user_badges (user_id, badge_id) VALUES
(1, 1), (1, 2), (1, 3),
(2, 1), (2, 2), (2, 3), (2, 4),
(3, 1), (3, 2), (3, 3),
(4, 1), (4, 2),
(5, 1);

INSERT INTO rewards (reward_id, name, description, points_cost, stock) VALUES
(1, 'Réduction 10%', '10% de réduction sur votre prochaine réparation', 500, 100),
(2, 'Support Prioritaire', 'Accès au support prioritaire pendant 1 mois', 1000, 50),
(3, 'Goodies TechCare', 'Kit de goodies exclusif', 2000, 30),
(4, 'Formation Gratuite', 'Formation en ligne offerte', 3000, 20),
(5, 'Réparation Gratuite', 'Une réparation offerte', 5000, 10);

INSERT INTO point_history (user_id, points_change, reason, created_at) VALUES
(1, 50, 'Réparer un appareil', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(1, 100, 'Recycler un produit', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(1, 25, 'Donner un avis', DATE_SUB(NOW(), INTERVAL 4 DAY)),
(2, 100, 'Recycler un produit', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(3, 50, 'Réparer un appareil', DATE_SUB(NOW(), INTERVAL 2 DAY));
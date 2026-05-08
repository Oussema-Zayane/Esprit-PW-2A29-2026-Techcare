-- ═══════════════════════════════════════════════════════════════════
--  TechCare – Script de mise à jour pour les colonnes views et ventes
--  Exécutez ce script pour ajouter les nouvelles fonctionnalités
-- ═══════════════════════════════════════════════════════════════════

USE techcare;

-- ═ Ajouter les colonnes views et ventes si elles n'existent pas ═
ALTER TABLE produit ADD COLUMN IF NOT EXISTS views INT DEFAULT 0 COMMENT 'Nombre de vues du produit';
ALTER TABLE produit ADD COLUMN IF NOT EXISTS ventes INT DEFAULT 0 COMMENT 'Nombre de ventes';

-- ═ Créer les index pour optimiser les requêtes ═
ALTER TABLE produit ADD INDEX IF NOT EXISTS idx_ventes (ventes);
ALTER TABLE produit ADD INDEX IF NOT EXISTS idx_views (views);

-- ═ Initialiser avec des données de test si vides ═
-- Les colonnes ont des valeurs par défaut de 0, donc rien à faire ici

-- Vérification : afficher la structure mise à jour
DESC produit;

-- Afficher les premiers produits avec leurs statistiques
SELECT id, nom, prix, stock, views, ventes, (ventes * 2 + views) as popularite_score
FROM produit
ORDER BY popularite_score DESC
LIMIT 10;

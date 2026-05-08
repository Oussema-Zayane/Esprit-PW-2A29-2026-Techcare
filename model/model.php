<?php
// ╔══════════════════════════════════════════════╗
// ║  MODEL – Connexion MySQL & CRUD table produit ║
// ╚══════════════════════════════════════════════╝

require_once __DIR__ . '/../config/config.php';

class Model {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = new PDO(DB_DSN, DB_USER, DB_PASS, DB_OPTIONS);
    }

    // ── READ : Récupérer tous les produits ──────────────
    public function getProducts(): array {
        $stmt = $this->pdo->query("SELECT * FROM produit ORDER BY date_ajout DESC");
        return $stmt->fetchAll();
    }

    // ── READ : Récupérer tous les produits avec catégories (JOINTURE) ─
    public function getProductsWithCategories(): array {
        $stmt = $this->pdo->query(
            "SELECT p.*, c.id as categorie_id, c.nom as categorie_nom, c.description as categorie_description, c.image as categorie_image
             FROM produit p
             LEFT JOIN categorie c ON p.categorie_id = c.id
             ORDER BY p.date_ajout DESC"
        );
        return $stmt->fetchAll();
    }

    // ── READ : Récupérer un produit par ID ──────────────
    public function getProductById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM produit WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    // ── READ : Récupérer un produit par ID avec sa catégorie (JOINTURE) ─
    public function getProductByIdWithCategorie(int $id): ?array {
        $stmt = $this->pdo->prepare(
            "SELECT p.*, c.id as categorie_id, c.nom as categorie_nom, c.description as categorie_description, c.image as categorie_image
             FROM produit p
             LEFT JOIN categorie c ON p.categorie_id = c.id
             WHERE p.id = ?"
        );
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    // ── CREATE : Ajouter un produit ─────────────────────
    public function addProduct(array $data): array {
        $sql = "INSERT INTO produit (nom, description, prix, stock, categorie_id, marque, image, etat)
                VALUES (:nom, :description, :prix, :stock, :categorie_id, :marque, :image, :etat)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nom'           => trim($data['nom']),
            ':description'   => trim($data['description'] ?? ''),
            ':prix'          => (float)$data['prix'],
            ':stock'         => !empty($data['stock']) ? (int)$data['stock'] : 10,
            ':categorie_id'  => isset($data['categorie_id']) ? (int)$data['categorie_id'] : null,
            ':marque'        => trim($data['marque'] ?? ''),
            ':image'         => trim($data['image'] ?? ''),
            ':etat'          => in_array($data['etat'] ?? '', ['neuf', 'occasion']) ? $data['etat'] : 'neuf',
        ]);

        $newId = (int)$this->pdo->lastInsertId();
        return $this->getProductById($newId);
    }

    // ── UPDATE : Modifier un produit ────────────────────
    public function updateProduct(int $id, array $data): ?array {
        $sql = "UPDATE produit
                SET nom = :nom, description = :description, prix = :prix,
                    stock = :stock, categorie_id = :categorie_id, marque = :marque,
                    image = :image, etat = :etat
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nom'           => trim($data['nom']),
            ':description'   => trim($data['description'] ?? ''),
            ':prix'          => (float)$data['prix'],
            ':stock'         => !empty($data['stock']) ? (int)$data['stock'] : 10,
            ':categorie_id'  => isset($data['categorie_id']) ? (int)$data['categorie_id'] : null,
            ':marque'        => trim($data['marque'] ?? ''),
            ':image'         => trim($data['image'] ?? ''),
            ':etat'          => in_array($data['etat'] ?? '', ['neuf', 'occasion']) ? $data['etat'] : 'neuf',
            ':id'            => $id,
        ]);

        return $this->getProductById($id);
    }

    // ── DELETE : Supprimer un produit ───────────────────
    public function deleteProduct(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM produit WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    // ── STOCK : Diminuer le stock (achat) ───────────────
    public function decreaseStock(int $id, int $quantity = 1): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE produit 
             SET stock = GREATEST(0, stock - ?) 
             WHERE id = ? AND stock > 0"
        );
        return $stmt->execute([$quantity, $id]) && $stmt->rowCount() > 0;
    }

    // ── VENTES : Incrémenter les ventes ─────────────────
    public function incrementVentes(int $id, int $quantity = 1): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE produit 
             SET ventes = ventes + ? 
             WHERE id = ?"
        );
        return $stmt->execute([$quantity, $id]) && $stmt->rowCount() > 0;
    }

    // ── VIEWS : Incrémenter les vues ───────────────────
    public function incrementViews(int $id): bool {
        $stmt = $this->pdo->prepare(
            "UPDATE produit 
             SET views = views + 1 
             WHERE id = ?"
        );
        return $stmt->execute([$id]) && $stmt->rowCount() > 0;
    }

    // ── POPULAIRES : Récupérer les produits populaires ──
    public function getPopularProducts(int $limit = 5): array {
        $stmt = $this->pdo->query(
            "SELECT p.*, c.id as categorie_id, c.nom as categorie_nom, c.description as categorie_description, c.image as categorie_image
             FROM produit p
             LEFT JOIN categorie c ON p.categorie_id = c.id
             ORDER BY (p.ventes * 2 + p.views) DESC
             LIMIT {$limit}"
        );
        return $stmt->fetchAll();
    }

    // ── GET PRODUITS TRIÉS : Récupérer avec critères ────
    public function getProductsSorted(string $sortBy = 'recent'): array {
        $query = "SELECT p.*, c.id as categorie_id, c.nom as categorie_nom, c.description as categorie_description, c.image as categorie_image
                  FROM produit p
                  LEFT JOIN categorie c ON p.categorie_id = c.id";
        
        switch ($sortBy) {
            case 'price-asc':
                $query .= " ORDER BY p.prix ASC";
                break;
            case 'price-desc':
                $query .= " ORDER BY p.prix DESC";
                break;
            case 'name-asc':
                $query .= " ORDER BY p.nom ASC";
                break;
            case 'name-desc':
                $query .= " ORDER BY p.nom DESC";
                break;
            case 'popular':
                $query .= " ORDER BY (p.ventes * 2 + p.views) DESC";
                break;
            case 'stock':
                $query .= " ORDER BY p.stock DESC";
                break;
            default: // 'recent'
                $query .= " ORDER BY p.date_ajout DESC";
        }

        $stmt = $this->pdo->query($query);
        return $stmt->fetchAll();
    }
}

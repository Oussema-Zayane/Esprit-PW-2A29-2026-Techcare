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
}

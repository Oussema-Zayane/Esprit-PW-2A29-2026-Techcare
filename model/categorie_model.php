<?php
// ╔══════════════════════════════════════════════╗
// ║  CATEGORIE MODEL – CRUD table catégorie     ║
// ╚══════════════════════════════════════════════╝

require_once __DIR__ . '/../config/config.php';

class CategorieModel {
    private PDO $pdo;

    public function __construct() {
        $this->pdo = new PDO(DB_DSN, DB_USER, DB_PASS, DB_OPTIONS);
    }

    // ── READ : Récupérer toutes les catégories ─────────
    public function getCategories(): array {
        $stmt = $this->pdo->query("SELECT * FROM categorie ORDER BY nom ASC");
        return $stmt->fetchAll();
    }

    // ── READ : Récupérer une catégorie par ID ──────────
    public function getCategorieById(int $id): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM categorie WHERE id = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    // ── CREATE : Ajouter une catégorie ──────────────────
    public function addCategorie(array $data): array {
        $sql = "INSERT INTO categorie (nom, description)
                VALUES (:nom, :description)";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nom'         => trim($data['nom']),
            ':description' => trim($data['description'] ?? ''),
        ]);

        $newId = (int)$this->pdo->lastInsertId();
        return $this->getCategorieById($newId);
    }

    // ── UPDATE : Modifier une catégorie ────────────────
    public function updateCategorie(int $id, array $data): ?array {
        $sql = "UPDATE categorie
                SET nom = :nom, description = :description
                WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':nom'         => trim($data['nom']),
            ':description' => trim($data['description'] ?? ''),
            ':id'          => $id,
        ]);

        return $this->getCategorieById($id);
    }

    // ── DELETE : Supprimer une catégorie ────────────────
    public function deleteCategorie(int $id): bool {
        $stmt = $this->pdo->prepare("DELETE FROM categorie WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }
}

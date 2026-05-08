<?php
require_once __DIR__ . '/../config.php';

class AuditLog
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->createTableIfNotExists();
    }

    private function createTableIfNotExists()
    {
        $sql = "CREATE TABLE IF NOT EXISTS audit_log (
            idAudit INT AUTO_INCREMENT PRIMARY KEY,
            adminName VARCHAR(100) NOT NULL DEFAULT 'Admin',
            actionType VARCHAR(80) NOT NULL,
            entityType VARCHAR(80) NOT NULL,
            entityId INT NULL,
            details TEXT NULL,
            createdAt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        $this->pdo->exec($sql);
    }

    public function add($adminName, $actionType, $entityType, $entityId = null, $details = '')
    {
        $sql = "INSERT INTO audit_log (adminName, actionType, entityType, entityId, details)
                VALUES (:adminName, :actionType, :entityType, :entityId, :details)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'adminName' => $adminName ?: 'Admin',
            'actionType' => $actionType,
            'entityType' => $entityType,
            'entityId' => $entityId,
            'details' => $details
        ]);
    }

    public function getAll($limit = 100)
    {
        $limit = max(1, min((int)$limit, 500));
        $sql = "SELECT * FROM audit_log ORDER BY createdAt DESC, idAudit DESC LIMIT " . $limit;
        return $this->pdo->query($sql)->fetchAll();
    }

    public function getStats()
    {
        return $this->pdo->query("SELECT actionType, COUNT(*) AS total FROM audit_log GROUP BY actionType ORDER BY total DESC")->fetchAll();
    }
}
?>

<?php
require_once __DIR__ . '/../config.php';

class Solution
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll()
    {
        $sql = "SELECT s.*, d.typeObjet, d.marque, d.categorieProbleme, d.photo
                FROM solution s
                INNER JOIN diagnostic d ON s.idDiagnostic = d.idDiagnostic
                ORDER BY s.idSolution DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function getStats()
    {
        $stats = [];

        $stats['total'] = (int) $this->pdo->query("SELECT COUNT(*) FROM solution")->fetchColumn();

        $sqlPriorite = "SELECT niveauPriorite, COUNT(*) AS total
                        FROM solution
                        GROUP BY niveauPriorite
                        ORDER BY total DESC";
        $stats['parPriorite'] = $this->pdo->query($sqlPriorite)->fetchAll();

        $sqlEfficacite = "SELECT efficaciteEstimee, COUNT(*) AS total
                          FROM solution
                          GROUP BY efficaciteEstimee
                          ORDER BY total DESC";
        $stats['parEfficacite'] = $this->pdo->query($sqlEfficacite)->fetchAll();

        return $stats;
    }

    public function filter($keyword = '', $niveauPriorite = '', $efficaciteEstimee = '', $typeSolution = '')
    {
        $sql = "SELECT s.*, d.typeObjet, d.marque, d.categorieProbleme, d.photo
                FROM solution s
                INNER JOIN diagnostic d ON s.idDiagnostic = d.idDiagnostic
                WHERE 1=1";
        $params = [];

        if (!empty($keyword)) {
            $sql .= " AND (s.contenu LIKE :keyword OR s.justificationTechnique LIKE :keyword OR s.suggestionIA LIKE :keyword OR d.typeObjet LIKE :keyword OR d.marque LIKE :keyword OR d.categorieProbleme LIKE :keyword)";
            $params['keyword'] = '%' . $keyword . '%';
        }

        if (!empty($niveauPriorite)) {
            $sql .= " AND s.niveauPriorite = :niveauPriorite";
            $params['niveauPriorite'] = $niveauPriorite;
        }

        if (!empty($efficaciteEstimee)) {
            $sql .= " AND s.efficaciteEstimee = :efficaciteEstimee";
            $params['efficaciteEstimee'] = $efficaciteEstimee;
        }

        if (!empty($typeSolution)) {
            $sql .= " AND s.typeSolution = :typeSolution";
            $params['typeSolution'] = $typeSolution;
        }

        $sql .= " ORDER BY s.idSolution DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }



    public function getPerformanceStats()
    {
        $sql = "SELECT s.idSolution, s.contenu, s.typeSolution, s.niveauPriorite, s.efficaciteEstimee,
                       d.typeObjet, d.marque, d.categorieProbleme,
                       CASE
                           WHEN s.efficaciteEstimee = 'élevée' THEN 90
                           WHEN s.efficaciteEstimee = 'moyenne' THEN 60
                           WHEN s.efficaciteEstimee = 'faible' THEN 30
                           ELSE 50
                       END AS scorePerformance
                FROM solution s
                INNER JOIN diagnostic d ON s.idDiagnostic = d.idDiagnostic
                ORDER BY scorePerformance DESC, s.idSolution DESC
                LIMIT 10";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM solution WHERE idSolution = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getByDiagnostic($idDiagnostic)
    {
        $sql = "SELECT s.*, d.typeObjet, d.marque, d.categorieProbleme, d.photo
                FROM solution s
                INNER JOIN diagnostic d ON s.idDiagnostic = d.idDiagnostic
                WHERE s.idDiagnostic = ?
                ORDER BY s.idSolution DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$idDiagnostic]);
        return $stmt->fetchAll();
    }

    public function create($data)
    {
        $sql = "INSERT INTO solution (
                    contenu,
                    niveauPriorite,
                    efficaciteEstimee,
                    typeSolution,
                    justificationTechnique,
                    suggestionIA,
                    idDiagnostic
                ) VALUES (
                    :contenu,
                    :niveauPriorite,
                    :efficaciteEstimee,
                    :typeSolution,
                    :justificationTechnique,
                    :suggestionIA,
                    :idDiagnostic
                )";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'contenu' => $data['contenu'],
            'niveauPriorite' => $data['niveauPriorite'],
            'efficaciteEstimee' => $data['efficaciteEstimee'],
            'typeSolution' => $data['typeSolution'],
            'justificationTechnique' => $data['justificationTechnique'],
            'suggestionIA' => $data['suggestionIA'],
            'idDiagnostic' => $data['idDiagnostic']
        ]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE solution SET
                    contenu = :contenu,
                    niveauPriorite = :niveauPriorite,
                    efficaciteEstimee = :efficaciteEstimee,
                    typeSolution = :typeSolution,
                    justificationTechnique = :justificationTechnique,
                    suggestionIA = :suggestionIA,
                    idDiagnostic = :idDiagnostic
                WHERE idSolution = :idSolution";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'contenu' => $data['contenu'],
            'niveauPriorite' => $data['niveauPriorite'],
            'efficaciteEstimee' => $data['efficaciteEstimee'],
            'typeSolution' => $data['typeSolution'],
            'justificationTechnique' => $data['justificationTechnique'],
            'suggestionIA' => $data['suggestionIA'],
            'idDiagnostic' => $data['idDiagnostic'],
            'idSolution' => $id
        ]);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM solution WHERE idSolution = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>

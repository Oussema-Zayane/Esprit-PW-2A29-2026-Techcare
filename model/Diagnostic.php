<?php
require_once __DIR__ . '/../config.php';

class Diagnostic
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll()
    {
        $sql = "SELECT * FROM diagnostic ORDER BY idDiagnostic DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function getStats()
    {
        $stats = [];

        $stats['total'] = (int) $this->pdo->query("SELECT COUNT(*) FROM diagnostic")->fetchColumn();

        $sqlCategorie = "SELECT categorieProbleme, COUNT(*) AS total
                         FROM diagnostic
                         GROUP BY categorieProbleme
                         ORDER BY total DESC";
        $stats['parCategorie'] = $this->pdo->query($sqlCategorie)->fetchAll();

        $sqlUrgence = "SELECT urgence, COUNT(*) AS total
                       FROM diagnostic
                       GROUP BY urgence
                       ORDER BY total DESC";
        $stats['parUrgence'] = $this->pdo->query($sqlUrgence)->fetchAll();

        $sqlTypeObjet = "SELECT typeObjet, COUNT(*) AS total
                         FROM diagnostic
                         GROUP BY typeObjet
                         ORDER BY total DESC";
        $stats["parTypeObjet"] = $this->pdo->query($sqlTypeObjet)->fetchAll();

        return $stats;
    }

    public function filter($keyword = '', $categorieProbleme = '', $urgence = '', $ordreAlpha = '')
    {
        $sql = "SELECT * FROM diagnostic WHERE 1=1";
        $params = [];

        if (!empty($keyword)) {
            $sql .= " AND (typeObjet LIKE :keyword OR marque LIKE :keyword OR modele LIKE :keyword OR description LIKE :keyword)";
            $params['keyword'] = '%' . $keyword . '%';
        }

        if (!empty($categorieProbleme)) {
            $sql .= " AND categorieProbleme = :categorieProbleme";
            $params['categorieProbleme'] = $categorieProbleme;
        }

        if (!empty($urgence)) {
            $sql .= " AND urgence = :urgence";
            $params['urgence'] = $urgence;
        }

        if ($ordreAlpha === 'asc') {
            $sql .= " ORDER BY typeObjet ASC, marque ASC, modele ASC";
        } elseif ($ordreAlpha === 'desc') {
            $sql .= " ORDER BY typeObjet DESC, marque DESC, modele DESC";
        } else {
            $sql .= " ORDER BY idDiagnostic DESC";
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getSortedByMarque($ordre = "asc")
    {
        $direction = ($ordre === "desc") ? "DESC" : "ASC";
        $sql = "SELECT * FROM diagnostic ORDER BY marque " . $direction . ", typeObjet ASC, modele ASC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM diagnostic WHERE idDiagnostic = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $sql = "INSERT INTO diagnostic (
            typeObjet,
            marque,
            modele,
            dateAchat,
            statutObjet,
            categorieProbleme,
            description,
            urgence,
            suggestionIA,
            photo
        ) VALUES (
            :typeObjet,
            :marque,
            :modele,
            :dateAchat,
            :statutObjet,
            :categorieProbleme,
            :description,
            :urgence,
            :suggestionIA,
            :photo
        )";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'typeObjet' => $data['typeObjet'],
            'marque' => $data['marque'],
            'modele' => $data['modele'],
            'dateAchat' => $data['dateAchat'],
            'statutObjet' => $data['statutObjet'],
            'categorieProbleme' => $data['categorieProbleme'],
            'description' => $data['description'],
            'urgence' => $data['urgence'],
            'suggestionIA' => $data['suggestionIA'],
            'photo' => $data['photo']
        ]);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE diagnostic SET
            typeObjet = :typeObjet,
            marque = :marque,
            modele = :modele,
            dateAchat = :dateAchat,
            statutObjet = :statutObjet,
            categorieProbleme = :categorieProbleme,
            description = :description,
            urgence = :urgence,
            suggestionIA = :suggestionIA,
            photo = :photo
            WHERE idDiagnostic = :idDiagnostic";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'typeObjet' => $data['typeObjet'],
            'marque' => $data['marque'],
            'modele' => $data['modele'],
            'dateAchat' => $data['dateAchat'],
            'statutObjet' => $data['statutObjet'],
            'categorieProbleme' => $data['categorieProbleme'],
            'description' => $data['description'],
            'urgence' => $data['urgence'],
            'suggestionIA' => $data['suggestionIA'],
            'photo' => $data['photo'],
            'idDiagnostic' => $id
        ]);
    }


    public function getDashboardStats()
    {
        $stats = [];
        $stats['totalDiagnostics'] = (int) $this->pdo->query("SELECT COUNT(*) FROM diagnostic")->fetchColumn();
        $stats['totalUrgents'] = (int) $this->pdo->query("SELECT COUNT(*) FROM diagnostic WHERE urgence IN ('eleve','élevé','urgent')")->fetchColumn();
        $stats['totalSolutions'] = (int) $this->pdo->query("SELECT COUNT(*) FROM solution")->fetchColumn();
        $stats['diagnosticsSansSolution'] = (int) $this->pdo->query("SELECT COUNT(*) FROM diagnostic d WHERE NOT EXISTS (SELECT 1 FROM solution s WHERE s.idDiagnostic = d.idDiagnostic)")->fetchColumn();

        $rowType = $this->pdo->query("SELECT typeObjet, COUNT(*) AS total FROM diagnostic GROUP BY typeObjet ORDER BY total DESC LIMIT 1")->fetch();
        $rowCategorie = $this->pdo->query("SELECT categorieProbleme, COUNT(*) AS total FROM diagnostic GROUP BY categorieProbleme ORDER BY total DESC LIMIT 1")->fetch();

        $stats['typeFrequent'] = $rowType['typeObjet'] ?? 'Aucun';
        $stats['categorieFrequente'] = $rowCategorie['categorieProbleme'] ?? 'Aucune';
        return $stats;
    }



    public function getSmartDashboardData()
    {
        $data = $this->getDashboardStats();
        $total = max(1, (int)$data['totalDiagnostics']);
        $data['tauxUrgence'] = round(((int)$data['totalUrgents'] * 100) / $total, 2);

        $data['topTypes'] = $this->pdo->query("SELECT typeObjet AS label, COUNT(*) AS total FROM diagnostic GROUP BY typeObjet ORDER BY total DESC LIMIT 5")->fetchAll();
        $data['topCategories'] = $this->pdo->query("SELECT categorieProbleme AS label, COUNT(*) AS total FROM diagnostic GROUP BY categorieProbleme ORDER BY total DESC LIMIT 5")->fetchAll();

        $today = (int)$this->pdo->query("SELECT COUNT(*) FROM diagnostic WHERE DATE(dateAchat) = CURDATE()")->fetchColumn();
        $yesterday = (int)$this->pdo->query("SELECT COUNT(*) FROM diagnostic WHERE DATE(dateAchat) = DATE_SUB(CURDATE(), INTERVAL 1 DAY)")->fetchColumn();
        $data['diagnosticsAujourdhui'] = $today;
        $data['diagnosticsHier'] = $yesterday;
        $data['evolution'] = $today - $yesterday;

        $topType = $data['topTypes'][0]['label'] ?? 'Aucun appareil';
        $topCategorie = $data['topCategories'][0]['label'] ?? 'aucune catégorie';
        $niveau = $data['tauxUrgence'] >= 50 ? 'élevé' : ($data['tauxUrgence'] >= 25 ? 'moyen' : 'faible');
        $data['prediction'] = "Les " . strtolower($topType) . " ont un risque " . $niveau . " de panne " . strtolower($topCategorie) . ".";
        $data['decision'] = "Préparer plus de solutions pour : " . $topCategorie . " sur " . $topType . ".";

        return $data;
    }

    public function getUrgentDiagnostics()
    {
        $sql = "SELECT * FROM diagnostic
                WHERE urgence IN ('eleve','élevé','urgent')
                ORDER BY dateAchat DESC, idDiagnostic DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function getDiagnosticSolutionRelations()
    {
        $sql = "SELECT d.idDiagnostic, d.typeObjet, d.marque, d.modele, d.categorieProbleme, d.urgence,
                       COUNT(s.idSolution) AS totalSolutions
                FROM diagnostic d
                LEFT JOIN solution s ON s.idDiagnostic = d.idDiagnostic
                GROUP BY d.idDiagnostic, d.typeObjet, d.marque, d.modele, d.categorieProbleme, d.urgence
                ORDER BY d.idDiagnostic DESC";
        return $this->pdo->query($sql)->fetchAll();
    }

    public function getAdminAnalysis()
    {
        $analysis = [];
        $analysis['parType'] = $this->pdo->query("SELECT typeObjet AS label, COUNT(*) AS total FROM diagnostic GROUP BY typeObjet ORDER BY total DESC")->fetchAll();
        $analysis['parCategorie'] = $this->pdo->query("SELECT categorieProbleme AS label, COUNT(*) AS total FROM diagnostic GROUP BY categorieProbleme ORDER BY total DESC")->fetchAll();
        $analysis['parUrgence'] = $this->pdo->query("SELECT urgence AS label, COUNT(*) AS total FROM diagnostic GROUP BY urgence ORDER BY total DESC")->fetchAll();
        $analysis['solutionsParDiagnostic'] = $this->pdo->query("SELECT d.typeObjet, d.marque, d.modele, COUNT(s.idSolution) AS totalSolutions FROM diagnostic d LEFT JOIN solution s ON s.idDiagnostic = d.idDiagnostic GROUP BY d.idDiagnostic, d.typeObjet, d.marque, d.modele ORDER BY totalSolutions DESC")->fetchAll();
        return $analysis;
    }

    public function cleanDiagnosticsWithoutSolution()
    {
        $sql = "DELETE FROM diagnostic
                WHERE idDiagnostic NOT IN (SELECT DISTINCT idDiagnostic FROM solution WHERE idDiagnostic IS NOT NULL)";
        return (int) $this->pdo->exec($sql);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM diagnostic WHERE idDiagnostic = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>

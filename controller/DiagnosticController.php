<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/Diagnostic.php';
require_once __DIR__ . '/../model/AuditLog.php';

class DiagnosticController
{
    private $model;
    private $auditModel;

    public function __construct()
    {
        $pdo = config::getConnexion();
        $this->model = new Diagnostic($pdo);
        $this->auditModel = new AuditLog($pdo);
    }

    public function getAllDiagnostics()
    {
        return $this->model->getAll();
    }

    public function getDiagnosticStats()
    {
        return $this->model->getStats();
    }

    public function filterDiagnostics($keyword = '', $categorieProbleme = '', $urgence = '', $ordreAlpha = '')
    {
        return $this->model->filter($keyword, $categorieProbleme, $urgence, $ordreAlpha);
    }

    public function getDiagnosticsSortedByMarque($ordre = "asc")
    {
        return $this->model->getSortedByMarque($ordre);
    }

    public function getDiagnosticById($id)
    {
        return $this->model->getById($id);
    }

    public function createDiagnostic($data, $files)
    {
        $data['suggestionIA'] = $this->generateSuggestionIA($data);
        $errors = $this->validateDiagnostic($data, $files);

        if (!empty($errors)) {
            return $errors;
        }

        $data['photo'] = $this->uploadPhoto($files['photo']);
        $this->model->create($data);
        $this->auditModel->add($this->getAdminName(), 'Création diagnostic', 'Diagnostic', null, 'Création d’un diagnostic : ' . ($data['typeObjet'] ?? '') . ' - ' . ($data['marque'] ?? ''));
        return [];
    }

    public function updateDiagnostic($id, $data, $files = [])
    {
        $data['suggestionIA'] = $this->generateSuggestionIA($data);
        $errors = $this->validateDiagnostic($data, $files, false);

        if (!empty($errors)) {
            return $errors;
        }

        $old = $this->model->getById($id);

        if (isset($files['photo']) && $files['photo']['error'] === 0) {
            $data['photo'] = $this->uploadPhoto($files['photo']);
        } else {
            $data['photo'] = $old['photo'];
        }

        $this->model->update($id, $data);
        $this->auditModel->add($this->getAdminName(), 'Modification diagnostic', 'Diagnostic', $id, 'Modification du diagnostic #' . $id . ' : ' . ($data['typeObjet'] ?? '') . ' - ' . ($data['marque'] ?? ''));
        return [];
    }

    public function deleteDiagnostic($id)
    {
        $old = $this->model->getById($id);
        $deleted = $this->model->delete($id);
        if ($deleted) {
            $details = $old ? ('Suppression du diagnostic #' . $id . ' : ' . $old['typeObjet'] . ' - ' . $old['marque']) : ('Suppression du diagnostic #' . $id);
            $this->auditModel->add($this->getAdminName(), 'Suppression diagnostic', 'Diagnostic', $id, $details);
        }
        return $deleted;
    }

    public function generateSuggestionIA($data)
    {
        if (!empty($data['suggestionIA'])) {
            return trim($data['suggestionIA']);
        }

        $categorie = $data['categorieProbleme'] ?? '';
        $urgence = strtolower($data['urgence'] ?? '');

        switch ($categorie) {
            case 'Écran cassé':
                $suggestion = "Suggestion IA : vérifier l'état de l'écran et prévoir un remplacement si l'affichage ou le tactile est endommagé.";
                break;
            case 'Batterie':
                $suggestion = "Suggestion IA : contrôler l'état de la batterie, le chargeur et envisager un remplacement si l'autonomie est faible.";
                break;
            case 'Problème de charge':
                $suggestion = "Suggestion IA : nettoyer le port de charge, tester un autre câble et vérifier le connecteur.";
                break;
            case 'Appareil lent':
                $suggestion = "Suggestion IA : libérer l'espace de stockage, fermer les applications inutiles et effectuer une mise à jour système.";
                break;
            case 'Virus':
                $suggestion = "Suggestion IA : lancer une analyse antivirus complète, supprimer les applications suspectes et changer les mots de passe.";
                break;
            default:
                $suggestion = "Suggestion IA : effectuer un diagnostic général et vérifier les composants principaux de l'appareil.";
                break;
        }

        if ($urgence === 'élevé' || $urgence === 'eleve' || $urgence === 'urgent') {
            $suggestion .= " Priorité élevée : intervention rapide recommandée.";
        }

        return $suggestion;
    }



    public function calculateDiagnosticScore($diagnostic)
    {
        $score = 0;

        $urgence = strtolower(trim($diagnostic['urgence'] ?? ''));
        $typeObjet = strtolower(trim($diagnostic['typeObjet'] ?? ''));
        $categorie = strtolower(trim($diagnostic['categorieProbleme'] ?? ''));

        // 1) Score selon urgence
        if (in_array($urgence, ['eleve', 'élevé', 'urgent'])) {
            $score += 50;
        } elseif (in_array($urgence, ['moyen', 'moyenne'])) {
            $score += 30;
        } elseif ($urgence === 'faible') {
            $score += 10;
        }

        // 2) Score selon catégorie du problème
        if (in_array($categorie, ['écran cassé', 'ecran casse', 'problème de charge', 'probleme de charge', 'virus'])) {
            $score += 25;
        } elseif (in_array($categorie, ['batterie', 'appareil lent'])) {
            $score += 18;
        } else {
            $score += 10;
        }

        // 3) Score selon type d'objet
        if (in_array($typeObjet, ['téléphone', 'telephone', 'pc', 'ordinateur bureau'])) {
            $score += 15;
        } elseif (in_array($typeObjet, ['machine à laver', 'machine a laver', 'tablette', 'ipad'])) {
            $score += 12;
        } else {
            $score += 8;
        }

        return min($score, 100);
    }

    public function getDiagnosticRiskLevel($score)
    {
        if ($score >= 75) {
            return 'Critique';
        }

        if ($score >= 45) {
            return 'Important';
        }

        return 'Normal';
    }

    public function getScoreRecommendation($diagnostic, $score)
    {
        $categorie = $diagnostic['categorieProbleme'] ?? 'Autre';
        $urgence = strtolower(trim($diagnostic['urgence'] ?? ''));

        if ($score >= 75) {
            return "Intervention rapide recommandée : le diagnostic combine une urgence élevée avec un problème sensible ($categorie).";
        }

        if ($urgence === 'moyen') {
            return "Diagnostic à traiter bientôt : surveiller l'évolution du problème et préparer une solution adaptée.";
        }

        return "Diagnostic stable : suivre le problème et appliquer une solution simple si nécessaire.";
    }

    public function getDiagnosticsWithIntelligentScore()
    {
        $diagnostics = $this->model->getAll();

        foreach ($diagnostics as &$diagnostic) {
            $score = $this->calculateDiagnosticScore($diagnostic);
            $diagnostic['scoreIntelligent'] = $score;
            $diagnostic['niveauRisque'] = $this->getDiagnosticRiskLevel($score);
            $diagnostic['recommandationScore'] = $this->getScoreRecommendation($diagnostic, $score);
        }

        usort($diagnostics, function ($a, $b) {
            return $b['scoreIntelligent'] <=> $a['scoreIntelligent'];
        });

        return $diagnostics;
    }

    public function getAutoAmeliorationData()
    {
        $diagnostics = $this->model->getAll();
        $total = count($diagnostics);

        $parType = [];
        $parCategorie = [];
        $parUrgence = [];

        foreach ($diagnostics as $diagnostic) {
            $type = trim($diagnostic['typeObjet'] ?? 'Autre') ?: 'Autre';
            $categorie = trim($diagnostic['categorieProbleme'] ?? 'Autre') ?: 'Autre';
            $urgence = trim($diagnostic['urgence'] ?? 'Non définie') ?: 'Non définie';

            $parType[$type] = ($parType[$type] ?? 0) + 1;
            $parCategorie[$categorie] = ($parCategorie[$categorie] ?? 0) + 1;
            $parUrgence[$urgence] = ($parUrgence[$urgence] ?? 0) + 1;
        }

        arsort($parType);
        arsort($parCategorie);
        arsort($parUrgence);

        $typeFrequent = !empty($parType) ? array_key_first($parType) : 'Aucun';
        $categorieFrequente = !empty($parCategorie) ? array_key_first($parCategorie) : 'Aucune';
        $urgenceFrequente = !empty($parUrgence) ? array_key_first($parUrgence) : 'Aucune';

        $recommendations = [];

        if ($total > 0) {
            $recommendations[] = "Le type d'objet le plus fréquent est : " . $typeFrequent . ". Il faut préparer des solutions adaptées à ce type.";
            $recommendations[] = "Le problème le plus fréquent est : " . $categorieFrequente . ". Il faut créer plus de solutions pour cette catégorie.";
            $recommendations[] = "Le niveau d'urgence le plus fréquent est : " . $urgenceFrequente . ". Cela aide à organiser les priorités de traitement.";
        } else {
            $recommendations[] = "Aucune donnée disponible pour générer une auto-amélioration.";
        }

        return [
            'total' => $total,
            'parType' => $parType,
            'parCategorie' => $parCategorie,
            'parUrgence' => $parUrgence,
            'typeFrequent' => $typeFrequent,
            'categorieFrequente' => $categorieFrequente,
            'urgenceFrequente' => $urgenceFrequente,
            'recommendations' => $recommendations
        ];
    }


    public function getAdminDashboardStats()
    {
        return $this->model->getDashboardStats();
    }

    public function getSmartDashboardData()
    {
        return $this->model->getSmartDashboardData();
    }

    public function getAdminAuditLogs()
    {
        return $this->auditModel->getAll();
    }

    public function getAdminAuditStats()
    {
        return $this->auditModel->getStats();
    }

    private function getAdminName()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['admin_name'] ?? 'Admin';
    }

    public function getUrgentDiagnostics()
    {
        return $this->model->getUrgentDiagnostics();
    }

    public function getDiagnosticSolutionRelations()
    {
        return $this->model->getDiagnosticSolutionRelations();
    }

    public function getAdminAnalysis()
    {
        return $this->model->getAdminAnalysis();
    }

    public function cleanDiagnosticsWithoutSolution()
    {
        return $this->model->cleanDiagnosticsWithoutSolution();
    }

    private function validateDiagnostic($data, $files, $photoRequired = true)
    {
        $errors = [];

        $fields = [
            'typeObjet',
            'marque',
            'modele',
            'dateAchat',
            'statutObjet',
            'categorieProbleme',
            'description',
            'urgence'
        ];

        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $errors[$field] = "Le champ $field est obligatoire.";
            }
        }

        if (isset($data['marque']) && strlen(trim($data['marque'])) < 2) {
            $errors['marque'] = "La marque doit contenir au moins 2 lettres.";
        }

        $typeObjets = ['Téléphone', 'PC', 'Ordinateur bureau', 'Machine à laver', 'Tablette', 'iPad', 'Smart watch', 'Imprimante', 'Autre'];

        if (isset($data['typeObjet']) && trim($data['typeObjet']) !== '' && !in_array($data['typeObjet'], $typeObjets)) {
            $errors['typeObjet'] = "Type d'objet invalide.";
        }

        $categories = [
            'Écran cassé',
            'Batterie',
            'Problème de charge',
            'Appareil lent',
            'Virus',
            'Autre'
        ];

        if (isset($data['categorieProbleme']) && !in_array($data['categorieProbleme'], $categories)) {
            $errors['categorieProbleme'] = "Catégorie invalide.";
        }

        if (!empty($data['dateAchat'])) {
            $today = new DateTime();
            $purchaseDate = DateTime::createFromFormat('Y-m-d', $data['dateAchat']);

            if (!$purchaseDate) {
                $errors['dateAchat'] = "Date d'achat invalide.";
            } else {
                $minDate = (clone $today)->modify('-7 years');

                if ($purchaseDate > $today) {
                    $errors['dateAchat'] = "La date d'achat ne peut pas être dans le futur.";
                } elseif ($purchaseDate < $minDate) {
                    $errors['dateAchat'] = "La date d'achat ne peut pas dépasser 7 ans.";
                }
            }
        }

        if ($photoRequired) {
            if (!isset($files['photo']) || $files['photo']['error'] !== 0) {
                $errors['photo'] = "La photo est obligatoire.";
            }
        }

        if (isset($files['photo']) && $files['photo']['error'] === 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($files['photo']['name'], PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed)) {
                $errors['photo'] = "Format image invalide.";
            }

            if ($files['photo']['size'] > 2 * 1024 * 1024) {
                $errors['photo'] = "La photo ne doit pas dépasser 2MB.";
            }
        }

        return $errors;
    }

    private function uploadPhoto($file)
    {
        $uploadDir = __DIR__ . '/../uploads/diagnostic/';

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $fileName = time() . '_' . uniqid() . '.' . $ext;
        $targetPath = $uploadDir . $fileName;

        move_uploaded_file($file['tmp_name'], $targetPath);

        return $fileName;
    }
}
?>

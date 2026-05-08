<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../model/Solution.php';
require_once __DIR__ . '/../model/Diagnostic.php';
require_once __DIR__ . '/../model/AuditLog.php';

class SolutionController
{
    private $solutionModel;
    private $diagnosticModel;
    private $auditModel;

    public function __construct()
    {
        $pdo = config::getConnexion();
        $this->solutionModel = new Solution($pdo);
        $this->diagnosticModel = new Diagnostic($pdo);
        $this->auditModel = new AuditLog($pdo);
    }

    public function getAllSolutions()
    {
        return $this->solutionModel->getAll();
    }

    public function getSolutionStats()
    {
        return $this->solutionModel->getStats();
    }

    public function filterSolutions($keyword = '', $niveauPriorite = '', $efficaciteEstimee = '', $typeSolution = '')
    {
        return $this->solutionModel->filter($keyword, $niveauPriorite, $efficaciteEstimee, $typeSolution);
    }

    public function getSolutionById($id)
    {
        return $this->solutionModel->getById($id);
    }

    public function getSolutionsByDiagnostic($idDiagnostic)
    {
        return $this->solutionModel->getByDiagnostic($idDiagnostic);
    }

    public function getAllDiagnostics()
    {
        return $this->diagnosticModel->getAll();
    }

    public function createSolution($data)
    {
        $data['suggestionIA'] = $this->generateSolutionSuggestion($data);
        $errors = $this->validateSolution($data);

        if (!empty($errors)) {
            return $errors;
        }

        $this->solutionModel->create($data);
        $this->auditModel->add($this->getAdminName(), 'Création solution', 'Solution', null, 'Création d’une solution pour le diagnostic #' . ($data['idDiagnostic'] ?? ''));
        return [];
    }

    public function updateSolution($id, $data)
    {
        $data['suggestionIA'] = $this->generateSolutionSuggestion($data);
        $errors = $this->validateSolution($data);

        if (!empty($errors)) {
            return $errors;
        }

        $this->solutionModel->update($id, $data);
        $this->auditModel->add($this->getAdminName(), 'Modification solution', 'Solution', $id, 'Modification de la solution #' . $id . ' liée au diagnostic #' . ($data['idDiagnostic'] ?? ''));
        return [];
    }

    public function deleteSolution($id)
    {
        $old = $this->solutionModel->getById($id);
        $deleted = $this->solutionModel->delete($id);
        if ($deleted) {
            $details = $old ? ('Suppression de la solution #' . $id . ' liée au diagnostic #' . $old['idDiagnostic']) : ('Suppression de la solution #' . $id);
            $this->auditModel->add($this->getAdminName(), 'Suppression solution', 'Solution', $id, $details);
        }
        return $deleted;
    }

    public function getSolutionPerformance()
    {
        return $this->solutionModel->getPerformanceStats();
    }

    private function getAdminName()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['admin_name'] ?? 'Admin';
    }

    public function generateSolutionSuggestion($data)
    {
        if (!empty($data['suggestionIA'])) {
            return trim($data['suggestionIA']);
        }

        $type = $data['typeSolution'] ?? '';
        $priorite = $data['niveauPriorite'] ?? '';
        $efficacite = $data['efficaciteEstimee'] ?? '';

        $suggestion = "Suggestion IA : appliquer cette solution en suivant les étapes techniques et vérifier le résultat après intervention.";

        if ($type === 'logiciel') {
            $suggestion = "Suggestion IA : commencer par une vérification logicielle, mise à jour système et nettoyage des fichiers inutiles.";
        } elseif ($type === 'matériel') {
            $suggestion = "Suggestion IA : contrôler le composant matériel concerné et remplacer la pièce si le défaut est confirmé.";
        } elseif ($type === 'maintenance') {
            $suggestion = "Suggestion IA : effectuer une maintenance complète, nettoyage, test et optimisation de l'appareil.";
        } elseif ($type === 'conseil') {
            $suggestion = "Suggestion IA : appliquer les conseils proposés et surveiller l'évolution du problème.";
        }

        if ($priorite === 'élevé') {
            $suggestion .= " Priorité élevée : traiter cette solution rapidement.";
        }

        if ($efficacite === 'élevée') {
            $suggestion .= " Efficacité estimée élevée : solution fortement recommandée.";
        }

        return $suggestion;
    }

    private function validateSolution($data)
    {
        $errors = [];

        $fields = [
            'contenu',
            'niveauPriorite',
            'efficaciteEstimee',
            'typeSolution',
            'justificationTechnique',
            'idDiagnostic'
        ];

        foreach ($fields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $errors[$field] = "Le champ $field est obligatoire.";
            }
        }

        $priorites = ['faible', 'moyen', 'élevé'];
        if (isset($data['niveauPriorite']) && !in_array($data['niveauPriorite'], $priorites)) {
            $errors['niveauPriorite'] = "Niveau de priorité invalide.";
        }

        $efficacites = ['faible', 'moyenne', 'élevée'];
        if (isset($data['efficaciteEstimee']) && !in_array($data['efficaciteEstimee'], $efficacites)) {
            $errors['efficaciteEstimee'] = "Efficacité estimée invalide.";
        }

        $types = ['matériel', 'logiciel', 'maintenance', 'conseil'];
        if (isset($data['typeSolution']) && !in_array($data['typeSolution'], $types)) {
            $errors['typeSolution'] = "Type de solution invalide.";
        }

        if (isset($data['justificationTechnique']) && strlen(trim($data['justificationTechnique'])) < 5) {
            $errors['justificationTechnique'] = "La justification technique doit contenir au moins 5 caractères.";
        }

        return $errors;
    }
}
?>

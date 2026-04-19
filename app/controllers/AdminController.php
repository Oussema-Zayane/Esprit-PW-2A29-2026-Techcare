<?php
require_once __DIR__ . '/../models/Abonnement.php';
require_once __DIR__ . '/../models/Type.php';

class AdminController {
    private $model;

    public function __construct() {
        $this->model = new Abonnement();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Dashboard - Page d'accueil admin
     */
    public function dashboard() {
        $abonnements = $this->model->getAll();
        
        $totalAbonnements = count($abonnements);
        $actifs = count(array_filter($abonnements, fn($a) => $a['statut'] == 'actif'));
        $expires = count(array_filter($abonnements, fn($a) => $a['statut'] == 'expiré'));
        $revenus = array_sum(array_column($abonnements, 'prix'));
        $derniersAbonnements = array_slice($abonnements, 0, 5);
        
        include __DIR__ . '/../views/back/dashboard.php';
    }

    /**
     * Liste des abonnements
     */
    public function liste() {
        $abonnements = $this->model->getAll();
        error_log("[TECHCARE] Back Office liste - Nombre d'abonnements: " . count($abonnements));
        include __DIR__ . '/../views/back/abonnements/liste.php';
    }

    /**
     * Ajouter un abonnement
     */
    public function ajouter() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom_client' => $_POST['nom_client'],
                'email' => $_POST['email'] ?? '',
                'type_abonnement' => $_POST['type_abonnement'],
                'prix' => $_POST['prix'],
                'date_debut' => $_POST['date_debut'],
                'date_fin' => $_POST['date_fin']
            ];
            $result = $this->model->create($data);
            
            if ($result) {
                header('Location: index.php?action=liste&success=ajoute');
            } else {
                header('Location: index.php?action=ajouter&error=1');
            }
            exit();
        }
        include __DIR__ . '/../views/back/abonnements/ajouter.php';
    }

    /**
     * Modifier un abonnement
     */
    public function modifier() {
        $id = $_GET['id'] ?? 0;
        $abonnement = $this->model->getById($id);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom_client' => $_POST['nom_client'],
                'email' => $_POST['email'] ?? '',
                'type_abonnement' => $_POST['type_abonnement'],
                'prix' => $_POST['prix'],
                'date_debut' => $_POST['date_debut'],
                'date_fin' => $_POST['date_fin'],
                'statut' => $_POST['statut']
            ];
            $this->model->update($id, $data);
            header('Location: index.php?action=liste&success=modifie');
            exit();
        }
        
        include __DIR__ . '/../views/back/abonnements/modifier.php';
    }

    /**
     * Supprimer un abonnement
     */
    public function supprimer() {
        $id = $_GET['id'] ?? 0;
        $this->model->delete($id);
        header('Location: index.php?action=liste&success=supprime');
        exit();
    }

    /**
     * Détail d'un abonnement
     */
    public function detail() {
        $id = $_GET['id'] ?? 0;
        $abonnement = $this->model->getById($id);
        include __DIR__ . '/../views/back/abonnements/detail.php';
    }

    /**
     * Statistiques
     */
    public function statistiques() {
        $abonnements = $this->model->getAll();
        include __DIR__ . '/../views/back/statistiques.php';
    }

    /**
 * Page de recherche avancée - Version corrigée avec debug
 */
public function recherche() {
    // Récupérer tous les types pour le select
    $typeModel = new Type();
    $types = $typeModel->getAll();
    
    // Récupérer les paramètres GET
    $type_abonnement = isset($_GET['type_abonnement']) && $_GET['type_abonnement'] !== '' ? $_GET['type_abonnement'] : null;
    $nom_client = isset($_GET['nom_client']) && $_GET['nom_client'] !== '' ? $_GET['nom_client'] : null;
    $email = isset($_GET['email']) && $_GET['email'] !== '' ? $_GET['email'] : null;
    $date_debut = isset($_GET['date_debut']) && $_GET['date_debut'] !== '' ? $_GET['date_debut'] : null;
    $prix = isset($_GET['prix']) && $_GET['prix'] !== '' ? floatval($_GET['prix']) : null;
    $date_fin = isset($_GET['date_fin']) && $_GET['date_fin'] !== '' ? $_GET['date_fin'] : null;
    
    // Debug: afficher les paramètres reçus
    error_log("[RECHERCHE] type: $type_abonnement, nom: $nom_client, email: $email");
    
    // Vérifier si des filtres sont actifs
    $hasFilters = ($type_abonnement !== null) ||
                  ($nom_client !== null) ||
                  ($email !== null) ||
                  ($date_debut !== null) ||
                  ($prix !== null) ||
                  ($date_fin !== null);
    
    if ($hasFilters) {
        // Appliquer les filtres
        $abonnements = $this->model->rechercheParCritere(
            $type_abonnement, $nom_client, $email, $date_debut, $prix, $date_fin
        );
        error_log("[RECHERCHE] Nombre de résultats: " . count($abonnements));
    } else {
        // Afficher tous les abonnements par défaut
        $abonnements = $this->model->getAll();
        error_log("[RECHERCHE] Aucun filtre, affichage de tous: " . count($abonnements));
    }
    
    // Inclure la vue
    include __DIR__ . '/../views/back/recherche_abonnements.php';
}
    public function apiRecherche() {
        header('Content-Type: application/json');
        
        $type_abonnement = $_GET['type_abonnement'] ?? null;
        $statut = $_GET['statut'] ?? null;
        $nom_client = $_GET['nom_client'] ?? null;
        $email = $_GET['email'] ?? null;
        $date_debut_min = $_GET['date_debut_min'] ?? null;
        $date_debut_max = $_GET['date_debut_max'] ?? null;
        $prix_min = $_GET['prix_min'] ?? null;
        $prix_max = $_GET['prix_max'] ?? null;
        
        $resultats = $this->model->rechercheAvancee(
            $type_abonnement, $statut, $nom_client, $email, 
            $date_debut_min, $date_debut_max, $prix_min, $prix_max
        );
        
        echo json_encode($resultats);
        exit();
    }
}
?>
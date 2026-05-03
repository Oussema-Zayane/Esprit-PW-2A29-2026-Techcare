<?php
require_once __DIR__ . '/../models/Abonnement.php';
require_once __DIR__ . '/../models/Type.php';
require_once __DIR__ . '/../models/Sms.php';
require_once __DIR__ . '/../../config/database.php';

class AdminController {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    // ========== LOGIQUE DE DONNÉES (Abonnements) ==========

    public function getAllAbonnements() {
        $sql = "SELECT a.*, 
                       t.nom_type, 
                       t.prix_base,
                       t.duree_jours
                FROM abonnements a
                LEFT JOIN type_abonnement t ON a.type_abonnement = t.nom_type
                ORDER BY a.date_debut DESC";
        $result = $this->conn->query($sql);
        return ($result && $result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getAbonnementById($id) {
        $stmt = $this->conn->prepare("
            SELECT a.*, t.nom_type, t.prix_base, t.duree_jours
            FROM abonnements a
            LEFT JOIN type_abonnement t ON a.type_abonnement = t.nom_type
            WHERE a.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function createAbonnement($data) {
        $stmt = $this->conn->prepare("
            INSERT INTO abonnements (nom_client, email, telephone, type_abonnement, prix, date_debut, date_fin, statut)
            VALUES (?, ?, ?, ?, ?, ?, ?, 'actif')
        ");
        $email = $data['email'] ?? null;
        $telephone = $data['telephone'] ?? null;
        $stmt->bind_param("ssssdss", $data['nom_client'], $email, $telephone, $data['type_abonnement'], $data['prix'], $data['date_debut'], $data['date_fin']);
        return $stmt->execute() ? $this->conn->insert_id : false;
    }

    public function updateAbonnement($id, $data) {
        $stmt = $this->conn->prepare("
            UPDATE abonnements 
            SET nom_client=?, email=?, telephone=?, type_abonnement=?, prix=?, date_debut=?, date_fin=?, statut=?
            WHERE id=?
        ");
        $email = $data['email'] ?? null;
        $telephone = $data['telephone'] ?? null;
        $stmt->bind_param("ssssdsssi", $data['nom_client'], $email, $telephone, $data['type_abonnement'], $data['prix'], $data['date_debut'], $data['date_fin'], $data['statut'], $id);
        return $stmt->execute();
    }

    public function deleteAbonnement($id) {
        $stmt = $this->conn->prepare("DELETE FROM abonnements WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function rechercheParCritere($type_abonnement = null, $nom_client = null, $email = null, $date_debut = null, $prix = null, $date_fin = null) {
        $sql = "SELECT a.*, t.nom_type, t.prix_base, t.duree_jours 
                FROM abonnements a 
                LEFT JOIN type_abonnement t ON a.type_abonnement = t.nom_type 
                WHERE 1=1";
        $params = []; $types = "";
        if ($type_abonnement) { $sql .= " AND a.type_abonnement = ?"; $params[] = $type_abonnement; $types .= "s"; }
        if ($nom_client) { $sql .= " AND a.nom_client LIKE ?"; $params[] = "%$nom_client%"; $types .= "s"; }
        if ($email) { $sql .= " AND a.email LIKE ?"; $params[] = "%$email%"; $types .= "s"; }
        if ($date_debut) { $sql .= " AND a.date_debut = ?"; $params[] = $date_debut; $types .= "s"; }
        if ($prix) { $sql .= " AND a.prix = ?"; $params[] = (float)$prix; $types .= "d"; }
        if ($date_fin) { $sql .= " AND a.date_fin = ?"; $params[] = $date_fin; $types .= "s"; }
        $sql .= " ORDER BY a.date_debut DESC";
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function rechercheAvancee($type_abonnement = null, $statut = null, $nom_client = null, $email = null, $date_debut_min = null, $date_debut_max = null, $prix_min = null, $prix_max = null) {
        $sql = "SELECT a.*, t.nom_type, t.prix_base, t.duree_jours FROM abonnements a LEFT JOIN type_abonnement t ON a.type_abonnement = t.nom_type WHERE 1=1";
        $params = []; $types = "";
        if ($type_abonnement) { $sql .= " AND a.type_abonnement = ?"; $params[] = $type_abonnement; $types .= "s"; }
        if ($statut) { $sql .= " AND a.statut = ?"; $params[] = $statut; $types .= "s"; }
        if ($nom_client) { $sql .= " AND a.nom_client LIKE ?"; $params[] = "%$nom_client%"; $types .= "s"; }
        if ($email) { $sql .= " AND a.email LIKE ?"; $params[] = "%$email%"; $types .= "s"; }
        if ($date_debut_min) { $sql .= " AND a.date_debut >= ?"; $params[] = $date_debut_min; $types .= "s"; }
        if ($date_debut_max) { $sql .= " AND a.date_debut <= ?"; $params[] = $date_debut_max; $types .= "s"; }
        if ($prix_min) { $sql .= " AND a.prix >= ?"; $params[] = (float)$prix_min; $types .= "d"; }
        if ($prix_max) { $sql .= " AND a.prix <= ?"; $params[] = (float)$prix_max; $types .= "d"; }
        $sql .= " ORDER BY a.date_debut DESC";
        $stmt = $this->conn->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function getAbonnementOptions($abonnement_id) {
        $stmt = $this->conn->prepare("SELECT * FROM offre WHERE id_abonnement = ?");
        $stmt->bind_param("i", $abonnement_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // ========== LOGIQUE DE DONNÉES (Types) ==========

    public function getAllTypes() {
        $result = $this->conn->query("SELECT * FROM type_abonnement ORDER BY id_type ASC");
        return ($result && $result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    // ========== LOGIQUE DE DONNÉES (SMS) ==========

    public function getSmsStats() {
        $result = $this->conn->query("
            SELECT
                COUNT(*) AS total,
                SUM(statut = 'envoye') AS envoyes,
                SUM(statut = 'echoue') AS echoues,
                SUM(statut = 'simule') AS simules,
                SUM(type_sms = 'bienvenue') AS bienvenues,
                SUM(type_sms IN ('rappel_j7','rappel_j3')) AS rappels,
                SUM(type_sms = 'manuel') AS manuels
            FROM sms_logs
        ");
        return $result ? $result->fetch_assoc() : ['total' => 0, 'envoyes' => 0, 'echoues' => 0, 'simules' => 0, 'bienvenues' => 0, 'rappels' => 0, 'manuels' => 0];
    }

    public function getSmsLogs($page = 1, $perPage = 20, $filters = []) {
        $offset = ($page - 1) * $perPage;
        $where = "WHERE 1=1"; $params = []; $types = "";
        if (!empty($filters['type_sms'])) { $where .= " AND type_sms = ?"; $params[] = $filters['type_sms']; $types .= "s"; }
        if (!empty($filters['statut'])) { $where .= " AND statut = ?"; $params[] = $filters['statut']; $types .= "s"; }
        if (!empty($filters['search'])) { $where .= " AND (nom_client LIKE ? OR telephone LIKE ?)"; $params[] = "%{$filters['search']}%"; $params[] = "%{$filters['search']}%"; $types .= "ss"; }
        $params[] = $perPage; $params[] = $offset; $types .= "ii";
        $stmt = $this->conn->prepare("SELECT * FROM sms_logs $where ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function countSmsLogs($filters = []) {
        $where = "WHERE 1=1"; $params = []; $types = "";
        if (!empty($filters['type_sms'])) { $where .= " AND type_sms = ?"; $params[] = $filters['type_sms']; $types .= "s"; }
        if (!empty($filters['statut'])) { $where .= " AND statut = ?"; $params[] = $filters['statut']; $types .= "s"; }
        if (!empty($filters['search'])) { $where .= " AND (nom_client LIKE ? OR telephone LIKE ?)"; $params[] = "%{$filters['search']}%"; $params[] = "%{$filters['search']}%"; $types .= "ss"; }
        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM sms_logs $where");
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    }

    public function deleteSmsLog($id) {
        $stmt = $this->conn->prepare("DELETE FROM sms_logs WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    // ========== VUES (HTML) ==========

    public function dashboard() {
        $abonnements = $this->getAllAbonnements();
        $totalAbonnements = count($abonnements);
        $actifs = count(array_filter($abonnements, fn($a) => $a['statut'] == 'actif'));
        $expires = count(array_filter($abonnements, fn($a) => $a['statut'] == 'expiré'));
        $revenus = array_sum(array_column($abonnements, 'prix'));
        $derniersAbonnements = array_slice($abonnements, 0, 5);
        $smsStats = $this->getSmsStats();
        include __DIR__ . '/../views/back/dashboard.php';
    }

    public function liste() {
        $abonnements = $this->getAllAbonnements();
        include __DIR__ . '/../views/back/abonnements/liste.php';
    }

    public function ajouter() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom_client' => $_POST['nom_client'],
                'email' => $_POST['email'] ?? '',
                'telephone' => $_POST['telephone'] ?? '',
                'type_abonnement' => $_POST['type_abonnement'],
                'prix' => $_POST['prix'],
                'date_debut' => $_POST['date_debut'],
                'date_fin' => $_POST['date_fin'],
            ];
            $result = $this->createAbonnement($data);
            if ($result) {
                // SMS log simulation could be added here if needed
                header('Location: index.php?action=liste&success=ajoute');
            } else {
                header('Location: index.php?action=ajouter&error=1');
            }
            exit();
        }
        include __DIR__ . '/../views/back/abonnements/ajouter.php';
    }

    public function modifier() {
        $id = $_GET['id'] ?? 0;
        $abonnement = $this->getAbonnementById($id);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nom_client' => $_POST['nom_client'],
                'email' => $_POST['email'] ?? '',
                'telephone' => $_POST['telephone'] ?? '',
                'type_abonnement' => $_POST['type_abonnement'],
                'prix' => $_POST['prix'],
                'date_debut' => $_POST['date_debut'],
                'date_fin' => $_POST['date_fin'],
                'statut' => $_POST['statut'],
            ];
            $this->updateAbonnement($id, $data);
            header('Location: index.php?action=liste&success=modifie');
            exit();
        }
        include __DIR__ . '/../views/back/abonnements/modifier.php';
    }

    public function supprimer() {
        $id = $_GET['id'] ?? 0;
        $this->deleteAbonnement($id);
        header('Location: index.php?action=liste&success=supprime');
        exit();
    }

    public function detail() {
        $id = $_GET['id'] ?? 0;
        $abonnement = $this->getAbonnementById($id);
        include __DIR__ . '/../views/back/abonnements/detail.php';
    }

    public function statistiques() {
        $abonnements = $this->getAllAbonnements();
        include __DIR__ . '/../views/back/statistiques.php';
    }

    public function recherche() {
        $types = $this->getAllTypes();
        $type_abonnement = $_GET['type_abonnement'] ?? null;
        $nom_client = $_GET['nom_client'] ?? null;
        $email = $_GET['email'] ?? null;
        $date_debut = $_GET['date_debut'] ?? null;
        $prix = isset($_GET['prix']) ? floatval($_GET['prix']) : null;
        $date_fin = $_GET['date_fin'] ?? null;
        $hasFilters = ($type_abonnement || $nom_client || $email || $date_debut || $prix || $date_fin);
        $abonnements = $hasFilters ? $this->rechercheParCritere($type_abonnement, $nom_client, $email, $date_debut, $prix, $date_fin) : $this->getAllAbonnements();
        foreach ($abonnements as &$a) { $a['options'] = $this->getAbonnementOptions($a['id']); }
        include __DIR__ . '/../views/back/recherche_abonnements.php';
    }

    public function apiRecherche() {
        header('Content-Type: application/json');
        echo json_encode($this->rechercheAvancee($_GET['type_abonnement'] ?? null, $_GET['statut'] ?? null, $_GET['nom_client'] ?? null, $_GET['email'] ?? null, $_GET['date_debut_min'] ?? null, $_GET['date_debut_max'] ?? null, $_GET['prix_min'] ?? null, $_GET['prix_max'] ?? null));
        exit();
    }

    public function logout() {
        session_destroy();
        header('Location: index.php?page=accueil');
        exit();
    }

    public function smsLogs() {
        $page = max(1, intval($_GET['page'] ?? 1));
        $filtres = ['type_sms' => $_GET['type_sms'] ?? '', 'statut' => $_GET['statut'] ?? '', 'search' => $_GET['search'] ?? ''];
        $logs = $this->getSmsLogs($page, 20, $filtres);
        $total = $this->countSmsLogs($filtres);
        $stats = $this->getSmsStats();
        $pages = max(1, ceil($total / 20));
        $flash = $_SESSION['sms_flash'] ?? null;
        unset($_SESSION['sms_flash']);
        include __DIR__ . '/../views/back/sms_logs.php';
    }

    public function smsDeleteLog() {
        $id = intval($_GET['id'] ?? 0);
        $this->deleteSmsLog($id);
        $_SESSION['sms_flash'] = ['type' => 'success', 'msg' => 'Log SMS supprimé avec succès.'];
        header('Location: index.php?action=smsLogs');
        exit();
    }
}
?>
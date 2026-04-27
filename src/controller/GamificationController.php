<?php
require_once __DIR__ . '/../Model/GamificationModel.php';

class GamificationController {
    private $model;
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->model = new GamificationModel();
        
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 1;
            $_SESSION['username'] = 'Ayari Firas';
            $_SESSION['role'] = 'admin';
        }
    }
    
    // =====================================================
    // PAGES PRINCIPALES
    // =====================================================
    
    // Dashboard - Page d'accueil
    public function dashboard() {
        $user = $this->model->getUser(1);
        $history = $this->model->getHistory(1, 5);
        $userBadges = $this->model->getUserBadges(1);
        $allBadges = $this->model->getAllBadges();
        
        $userBadgeIds = array_column($userBadges, 'badge_id');
        $nextBadge = null;
        foreach ($allBadges as $badge) {
            if (!in_array($badge['badge_id'], $userBadgeIds)) {
                $nextBadge = $badge;
                break;
            }
        }
        
        $progress = 0;
        if ($nextBadge && $user['points'] > 0) {
            $progress = min(100, ($user['points'] / $nextBadge['points_required']) * 100);
        }
        
        $pageTitle = "Dashboard";
        $activeTab = "dashboard";
        $points = $user['points'];
        $level = $user['level'];
        $badgesCount = count($userBadges);
        $actionsCount = $user['total_actions'];
        
        ob_start();
        require_once __DIR__ . '/../View/frontoffice/dashboard.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../View/layout/app.php';
    }
    
    // Mes Points - Historique
    public function points() {
        $user = $this->model->getUser(1);
        $history = $this->model->getHistory(1, 50);
        
        $pageTitle = "Mes Points";
        $activeTab = "points";
        ob_start();
        require_once __DIR__ . '/../View/frontoffice/point.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../View/layout/app.php';
    }
    
    // Badges
    public function badges() {
        $user = $this->model->getUser(1);
        $allBadges = $this->model->getAllBadges();
        $userBadges = $this->model->getUserBadges(1);
        $userBadgeIds = array_column($userBadges, 'badge_id');
        
        $pageTitle = "Badges";
        $activeTab = "badges";
        ob_start();
        require_once __DIR__ . '/../View/frontoffice/badges.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../View/layout/app.php';
    }
    
    // Classement
    public function leaderboard() {
        $users = $this->model->getAllUsers();
        
        $pageTitle = "Classement";
        $activeTab = "leaderboard";
        ob_start();
        require_once __DIR__ . '/../View/frontoffice/leaderboard.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../View/layout/app.php';
    }
    
    // Récompenses
    public function rewards() {
        $user = $this->model->getUser(1);
        $rewards = $this->model->getRewards();
        $userRewards = $this->model->getUserRewards(1);
        
        $pageTitle = "Récompenses";
        $activeTab = "rewards";
        ob_start();
        require_once __DIR__ . '/../View/frontoffice/rewards.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../View/layout/app.php';
    }
    
    // =====================================================
    // ACTIONS (POST)
    // =====================================================
    
    // Ajouter des points
    public function addPoints() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $points = intval($_POST['points'] ?? 0);
            $action = $_POST['action'] ?? 'Action';
            
            if ($points > 0) {
                $this->model->addPoints(1, $points, $action);
                $_SESSION['success'] = "+$points points gagnés !";
            }
        }
        header('Location: index.php?controller=gamification&action=dashboard');
        exit;
    }
    
    // Échanger une récompense
    public function redeemReward() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rewardId = intval($_POST['reward_id'] ?? 0);
            if ($this->model->redeemReward(1, $rewardId)) {
                $_SESSION['success'] = "Récompense obtenue avec succès !";
            } else {
                $_SESSION['error'] = "Points insuffisants ou stock épuisé";
            }
        }
        header('Location: index.php?controller=gamification&action=rewards');
        exit;
    }
    
    // Supprimer une entrée de l'historique
    public function deleteHistory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $historyId = intval($_POST['id'] ?? 0);
            if ($this->model->deleteHistoryItem($historyId, 1)) {
                $_SESSION['success'] = "Entrée supprimée !";
            }
        }
        header('Location: index.php?controller=gamification&action=points');
        exit;
    }
    
    // =====================================================
    // API (AJAX)
    // =====================================================
    
    // Récupérer les données utilisateur pour le FrontOffice
    public function getUserData() {
        header('Content-Type: application/json');
        
        $user = $this->model->getUser(1);
        $history = $this->model->getHistory(1, 10);
        $userBadges = $this->model->getUserBadges(1);
        $userRewards = $this->model->getUserRewards(1);
        
        echo json_encode([
            'points' => $user['points'],
            'level' => $user['level'],
            'actions' => $user['total_actions'],
            'badges' => array_column($userBadges, 'badge_id'),
            'userRewards' => $userRewards,
            'history' => array_map(function($h) {
                return [
                    'id' => $h['history_id'],
                    'date' => date('d/m/Y H:i', strtotime($h['created_at'])),
                    'action' => $h['reason'],
                    'points' => $h['points_change']
                ];
            }, $history)
        ]);
        exit;
    }
    
    // Récupérer le classement
    public function getLeaderboard() {
        header('Content-Type: application/json');
        $users = $this->model->getAllUsers();
        echo json_encode($users);
        exit;
    }
    
    // Récupérer tous les badges (API)
    public function getBadgesApi() {
        header('Content-Type: application/json');
        echo json_encode($this->model->getAllBadges());
        exit;
    }
    
    // Récupérer toutes les récompenses (API)
    public function getRewardsApi() {
        header('Content-Type: application/json');
        echo json_encode($this->model->getRewards());
        exit;
    }
    
    // Ajouter des points via API (BackOffice)
    public function addPointsApi() {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = intval($_POST['userId'] ?? 1);
            $points = intval($_POST['points'] ?? 0);
            $reason = $_POST['reason'] ?? 'Ajout admin';
            
            if ($points > 0) {
                $this->model->addPoints($userId, $points, $reason);
                echo json_encode(['success' => true]);
                exit;
            }
        }
        echo json_encode(['success' => false]);
        exit;
    }
}
?>
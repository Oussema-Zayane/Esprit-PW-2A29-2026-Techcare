<?php
require_once __DIR__ . '/../Model/GamificationModel.php';

class GamificationController {
    private $model;
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->model = new GamificationModel();
        if (!isset($_SESSION['username'])) {
            $_SESSION['username'] = 'Ayari Firas';
            $_SESSION['role'] = 'admin';
            $_SESSION['user_id'] = 1;
        }
    }
    
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
        ob_start();
        require_once __DIR__ . '/../View/frontoffice/dashboard.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../View/layout/app.php';
    }
    
    public function addPoints() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $points = intval($_POST['points'] ?? 0);
            $action = $_POST['action'] ?? '';
            if ($points > 0 && $action) {
                $this->model->addPoints(1, $points, $action);
                $_SESSION['success'] = "+$points points gagnés !";
            }
        }
        header('Location: index.php?controller=gamification&action=dashboard');
        exit;
    }
    
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
    
    public function deleteHistory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $historyId = intval($_POST['id'] ?? 0);
            $this->model->deleteHistoryItem($historyId, 1);
            $_SESSION['success'] = "Entrée supprimée !";
        }
        header('Location: index.php?controller=gamification&action=points');
        exit;
    }
    
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
    
    public function leaderboard() {
        $users = $this->model->getAllUsers();
        $userPoints = $this->model->getUser(1)['points'];
        
        $pageTitle = "Classement";
        $activeTab = "leaderboard";
        ob_start();
        require_once __DIR__ . '/../View/frontoffice/leaderboard.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../View/layout/app.php';
    }
    
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
    
    public function redeemReward() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rewardId = intval($_POST['reward_id'] ?? 0);
            if ($this->model->redeemReward(1, $rewardId)) {
                $_SESSION['success'] = "Récompense obtenue avec succès !";
            } else {
                $_SESSION['error'] = "Impossible d'échanger cette récompense.";
            }
        }
        header('Location: index.php?controller=gamification&action=rewards');
        exit;
    }
    
    public function loyalty() {
        $user = $this->model->getUser(1);
        
        $pageTitle = "Fidélité";
        $activeTab = "loyalty";
        ob_start();
        require_once __DIR__ . '/../View/frontoffice/loyalty.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../View/layout/app.php';
    }
    
    // API Methods for AJAX
    public function getBadgesApi() {
        header('Content-Type: application/json');
        echo json_encode($this->model->getAllBadges());
        exit;
    }
    
    public function getRewardsApi() {
        header('Content-Type: application/json');
        echo json_encode($this->model->getRewards());
        exit;
    }
    
    public function addPointsApi() {
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
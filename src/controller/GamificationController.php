<?php
require_once __DIR__ . '/../Model/GamificationModel.php';

class GamificationController {
    private $model;
    private $pdo;
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->model = new GamificationModel();
        $this->pdo = Database::getConnection();
        
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = 1;
            $_SESSION['username'] = 'Ayari Firas';
            $_SESSION['role'] = 'admin';
            $_SESSION['user_points'] = 1250;
        }
    }
    
    // =====================================================
    // PAGES PRINCIPALES (MANQUANTES)
    // =====================================================
    
    public function dashboard() {
        $userId = $_SESSION['user_id'];
        $user = $this->model->getUser($userId);
        $history = $this->model->getHistory($userId, 5);
        $userBadges = $this->model->getUserBadges($userId);
        $allBadges = $this->model->getAllBadges();
        $streak = $this->model->getCurrentStreak($userId);
        $codeInfo = $this->model->getCodeParrainage($userId);
        $statsParrainage = $this->model->getStatsParrainage($userId);
        
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
        
        $_SESSION['user_points'] = $user['points'];
        
        $pageTitle = "Dashboard";
        $activeTab = "dashboard";
        ob_start();
        require_once __DIR__ . '/../View/frontoffice/dashboard.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../View/layout/app.php';
    }
    
    public function points() {
        $userId = $_SESSION['user_id'];
        $user = $this->model->getUser($userId);
        $history = $this->model->getHistory($userId, 50);
        
        $pageTitle = "Mes Points";
        $activeTab = "points";
        ob_start();
        require_once __DIR__ . '/../View/frontoffice/point.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../View/layout/app.php';
    }
    
    public function badges() {
        $userId = $_SESSION['user_id'];
        $user = $this->model->getUser($userId);
        $allBadges = $this->model->getAllBadges();
        $userBadges = $this->model->getUserBadges($userId);
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
        
        $pageTitle = "Classement";
        $activeTab = "leaderboard";
        ob_start();
        require_once __DIR__ . '/../View/frontoffice/leaderboard.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../View/layout/app.php';
    }
    
    public function rewards() {
        $userId = $_SESSION['user_id'];
        $user = $this->model->getUser($userId);
        $rewards = $this->model->getRewards();
        $userRewards = $this->model->getUserRewards($userId);
        
        $pageTitle = "Récompenses";
        $activeTab = "rewards";
        ob_start();
        require_once __DIR__ . '/../View/frontoffice/rewards.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../View/layout/app.php';
    }
    
    public function statistiques() {
        $pageTitle = "Statistiques";
        $activeTab = "statistiques";
        ob_start();
        require_once __DIR__ . '/../View/frontoffice/statistiques.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../View/layout/app.php';
    }
    
    // =====================================================
    // CALENDRIER DE CONNEXION
    // =====================================================
    
    public function enregistrerConnexion() {
        $userId = $_SESSION['user_id'];
        $result = $this->model->enregistrerConnexion($userId);
        
        if ($result && $result['bonus'] > 0) {
            $_SESSION['user_points'] = $this->model->getUser($userId)['points'];
            $_SESSION['success'] = "🎉 Connexion jour " . $result['streak'] . " ! +" . $result['bonus'] . " points bonus !";
        } else {
            $_SESSION['success'] = "✅ Connexion du jour enregistrée !";
        }
        
        header('Location: index.php?controller=gamification&action=dashboard');
        exit;
    }
    
    // =====================================================
    // PARRAINAGE
    // =====================================================
    
    public function parrainage() {
        $userId = $_SESSION['user_id'];
        $codeInfo = $this->model->getCodeParrainage($userId);
        $statsParrainage = $this->model->getStatsParrainage($userId);
        
        $pageTitle = "Parrainage";
        $activeTab = "parrainage";
        ob_start();
        require_once __DIR__ . '/../View/frontoffice/parrainage.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../View/layout/app.php';
    }
    
    public function genererCode() {
        $userId = $_SESSION['user_id'];
        $code = $this->model->genererCodeParrainage($userId);
        $_SESSION['success'] = "Nouveau code généré : $code";
        header('Location: index.php?controller=gamification&action=parrainage');
        exit;
    }
    
    public function utiliserCode() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = trim($_POST['code_parrainage'] ?? '');
            $userId = $_SESSION['user_id'];
            
            $result = $this->model->utiliserCodeParrainage($code, $userId);
            
            if ($result['success']) {
                $_SESSION['success'] = $result['message'];
                $_SESSION['user_points'] = $this->model->getUser($userId)['points'];
            } else {
                $_SESSION['error'] = $result['message'];
            }
        }
        header('Location: index.php?controller=gamification&action=parrainage');
        exit;
    }
    
    // =====================================================
    // EXPORT PDF
    // =====================================================
    
    public function exportPDF() {
        $userId = $_SESSION['user_id'];
        $data = $this->model->getExportData($userId);
        
        header('Content-Type: text/html');
        header('Content-Disposition: attachment; filename="historique_techcare_' . date('Y-m-d') . '.html"');
        
        echo $this->renderExportHTML($data);
        exit;
    }
    
    private function renderExportHTML($data) {
        $html = '<!DOCTYPE html>';
        $html .= '<html><head><meta charset="UTF-8"><title>Mon historique TechCare</title>';
        $html .= '<style>';
        $html .= 'body { font-family: Arial, sans-serif; margin: 40px; }';
        $html .= 'h1 { color: #00A86B; }';
        $html .= 'table { width: 100%; border-collapse: collapse; margin-top: 20px; }';
        $html .= 'th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }';
        $html .= 'th { background-color: #00A86B; color: white; }';
        $html .= '.header { text-align: center; margin-bottom: 30px; }';
        $html .= '</style></head><body>';
        
        $html .= '<div class="header">';
        $html .= '<h1>♻️ TechCare - Mon historique</h1>';
        $html .= '<p>Généré le : ' . $data['date'] . '</p>';
        $html .= '<p>Utilisateur : ' . htmlspecialchars($data['user']['username']) . '</p>';
        $html .= '</div>';
        
        $html .= '<h2>📊 Statistiques</h2>';
        $html .= '<table><table border="1">';
        $html .= ' hilab<th>Total points</th><th>Total actions</th><th>Niveau</th> Loch';
        $html .= '<tr>';
        $html .= '<td>' . number_format($data['total_points']) . '</td>';
        $html .= '<td>' . $data['total_actions'] . '</td>';
        $html .= '<td>Niveau ' . $data['user']['level'] . '</td>';
        $html .= '</tr>';
        $html .= '</table>';
        
        $html .= '<h2>📜 Historique des points</h2>';
        $html .= '<table border="1">';
        $html .= '<tr><th>Date</th><th>Action</th><th>Points</th></tr>';
        foreach ($data['history'] as $h) {
            $html .= '<tr>';
            $html .= '<td>' . date('d/m/Y H:i', strtotime($h['created_at'])) . '</td>';
            $html .= '<td>' . htmlspecialchars($h['reason']) . '</td>';
            $html .= '<td>' . ($h['points_change'] > 0 ? '+' : '') . $h['points_change'] . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';
        
        $html .= '<p style="margin-top: 40px; text-align: center;">Document généré par TechCare - Plateforme Écologique</p>';
        $html .= '</body></html>';
        
        return $html;
    }
    
    // =====================================================
    // ACTIONS (POST)
    // =====================================================
    
    public function addPoints() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $points = intval($_POST['points'] ?? 0);
            $action = $_POST['action'] ?? 'Action';
            
            if ($points > 0) {
                $this->model->addPoints($_SESSION['user_id'], $points, $action);
                $_SESSION['success'] = "+$points points gagnés !";
                $user = $this->model->getUser($_SESSION['user_id']);
                $_SESSION['user_points'] = $user['points'];
            }
        }
        header('Location: index.php?controller=gamification&action=dashboard');
        exit;
    }
    
    public function redeemReward() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $rewardId = intval($_POST['reward_id'] ?? 0);
            if ($this->model->redeemReward($_SESSION['user_id'], $rewardId)) {
                $_SESSION['success'] = "Récompense obtenue avec succès !";
                $user = $this->model->getUser($_SESSION['user_id']);
                $_SESSION['user_points'] = $user['points'];
            } else {
                $_SESSION['error'] = "Points insuffisants ou stock épuisé";
            }
        }
        header('Location: index.php?controller=gamification&action=rewards');
        exit;
    }
    
    public function deleteHistory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $historyId = intval($_POST['id'] ?? 0);
            if ($this->model->deleteHistoryItem($historyId, $_SESSION['user_id'])) {
                $_SESSION['success'] = "Entrée supprimée !";
            }
        }
        header('Location: index.php?controller=gamification&action=points');
        exit;
    }
    
    // =====================================================
    // API (AJAX)
    // =====================================================
    
    public function getUserData() {
        header('Content-Type: application/json');
        $userId = $_SESSION['user_id'];
        $user = $this->model->getUser($userId);
        $history = $this->model->getHistory($userId, 10);
        $userBadges = $this->model->getUserBadges($userId);
        
        echo json_encode([
            'points' => $user['points'],
            'level' => $user['level'],
            'actions' => $user['total_actions'],
            'badges' => array_column($userBadges, 'badge_id'),
            'history' => array_map(function($h) {
                return [
                    'date' => date('d/m/Y H:i', strtotime($h['created_at'])),
                    'action' => $h['reason'],
                    'points' => $h['points_change']
                ];
            }, $history)
        ]);
        exit;
    }
    
    public function getLeaderboard() {
        header('Content-Type: application/json');
        
        $sql = "SELECT u.id as user_id, u.nom as username, u.nbPoints as points,
                       FLOOR(u.nbPoints / 1000) + 1 as level,
                       COUNT(ub.badge_id) as badge_count
                FROM utilisateur u
                LEFT JOIN user_badges ub ON u.id = ub.user_id
                GROUP BY u.id
                ORDER BY u.nbPoints DESC";
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $users = $stmt->fetchAll();
        
        echo json_encode($users);
        exit;
    }
    
    public function getGlobalStats() {
        header('Content-Type: application/json');
        
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM utilisateur");
        $totalUsers = $stmt->fetch()['total'];
        
        $stmt = $this->pdo->query("SELECT SUM(nbPoints) as total FROM utilisateur");
        $totalPoints = $stmt->fetch()['total'] ?? 0;
        
        $avgPoints = $totalUsers > 0 ? round($totalPoints / $totalUsers) : 0;
        
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM user_badges");
        $totalBadgesUnlocked = $stmt->fetch()['total'] ?? 0;
        
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM badge");
        $totalBadges = $stmt->fetch()['total'] ?? 0;
        $lockedBadges = max(0, $totalBadges - $totalBadgesUnlocked);
        
        $stmt = $this->pdo->query("SELECT COUNT(*) as total FROM user_rewards");
        $totalRewardsExchanged = $stmt->fetch()['total'] ?? 0;
        
        $stmt = $this->pdo->query("SELECT nom, nbPoints FROM utilisateur ORDER BY nbPoints DESC LIMIT 1");
        $topUserData = $stmt->fetch();
        $topUser = $topUserData ? $topUserData['nom'] . ' (' . number_format($topUserData['nbPoints']) . ' pts)' : '-';
        
        $stmt = $this->pdo->query("SELECT nom as username, nbPoints as points FROM utilisateur ORDER BY nbPoints DESC LIMIT 5");
        $topUsers = $stmt->fetchAll();
        
        echo json_encode([
            'totalUsers' => $totalUsers,
            'totalPoints' => $totalPoints,
            'avgPoints' => $avgPoints,
            'totalBadgesUnlocked' => $totalBadgesUnlocked,
            'lockedBadges' => $lockedBadges,
            'unlockedBadges' => $totalBadgesUnlocked,
            'totalRewardsExchanged' => $totalRewardsExchanged,
            'topUser' => $topUser,
            'topUsers' => $topUsers
        ]);
        exit;
    }
    
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
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = intval($_POST['userId'] ?? 1);
            $points = intval($_POST['points'] ?? 0);
            if ($points > 0) {
                $this->model->addPoints($userId, $points, 'Ajout admin');
                echo json_encode(['success' => true]);
                exit;
            }
        }
        echo json_encode(['success' => false]);
        exit;
    }
}
?>
<?php
require_once __DIR__ . '/../Model/UserModel.php';
require_once __DIR__ . '/../Model/GamificationModel.php';

class UserController {
    private $userModel;
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->userModel = new UserModel();
        if (!isset($_SESSION['username'])) {
            $_SESSION['username'] = 'Ayari Firas';
            $_SESSION['role'] = 'admin';
            $_SESSION['user_id'] = 1;
        }
    }
    
    public function adminDashboard() {
        $pageTitle = "Administration";
        $activeTab = "admin";
        ob_start();
        require_once __DIR__ . '/../View/backoffice/admin_dashboard.php';
        $content = ob_get_clean();
        require_once __DIR__ . '/../View/layout/app.php';
    }
    
    public function getStats() {
        header('Content-Type: application/json');
        echo json_encode([
            'totalUsers' => $this->userModel->getTotalUsers(),
            'totalPoints' => $this->userModel->getTotalPoints(),
            'totalBadges' => $this->userModel->getTotalBadgesUnlocked(),
            'totalRewards' => $this->userModel->getTotalRewardsExchanged()
        ]);
        exit;
    }
    
    public function getUsers() {
        header('Content-Type: application/json');
        echo json_encode($this->userModel->getAllUsers());
        exit;
    }
    
    public function deleteUser() {
        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) {
            $this->userModel->deleteUser($id);
        }
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
    
    public function addUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $points = intval($_POST['points'] ?? 0);
            if (!empty($username)) {
                $this->userModel->createUser($username, $points);
                $_SESSION['success'] = "Utilisateur ajouté avec succès";
            }
        }
        header('Location: index.php?controller=user&action=admin');
        exit;
    }
    
    public function updateUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = intval($_POST['id'] ?? 0);
            $username = trim($_POST['username'] ?? '');
            $points = intval($_POST['points'] ?? 0);
            if ($id > 0 && !empty($username)) {
                $this->userModel->updateUser($id, $username, $points);
                $_SESSION['success'] = "Utilisateur modifié avec succès";
            }
        }
        header('Location: index.php?controller=user&action=admin');
        exit;
    }
}
?>
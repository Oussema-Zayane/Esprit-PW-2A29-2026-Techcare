<?php
require_once __DIR__ . '/../Model/UserModel.php';

class UserController {
    private $model;
    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->model = new UserModel();
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
            'totalUsers' => $this->model->getTotalUsers(),
            'totalPoints' => $this->model->getTotalPoints(),
            'totalBadges' => $this->model->getTotalBadges(),
            'totalActions' => $this->model->getTotalActions()
        ]);
        exit;
    }
    
    public function getUsers() {
        header('Content-Type: application/json');
        echo json_encode($this->model->getAllUsers());
        exit;
    }
    
    public function deleteUser() {
        $id = intval($_GET['id'] ?? 0);
        if ($id > 0) $this->model->deleteUser($id);
        echo json_encode(['success' => true]);
        exit;
    }
    
    public function addUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $points = intval($_POST['points'] ?? 0);
            if (!empty($username)) {
                $this->model->createUser($username, $points);
                $_SESSION['success'] = "Utilisateur ajouté !";
            }
        }
        header('Location: index.php?controller=user&action=admin');
        exit;
    }
}
?>
<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/api_keys.php';

class BadgeModel {
    private $pdo;
    
    public function __construct() {
        $this->pdo = Database::getConnection();
    }
    
    // =====================================================
    // CRUD de base
    // =====================================================
    
    public function getAll() {
        $sql = "SELECT * FROM badge ORDER BY seuilPoints ASC";
        return $this->pdo->query($sql)->fetchAll();
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM badge WHERE badge_id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }
    
    public function create($nom, $description, $icon, $seuilPoints, $image_ia = null) {
        $sql = "INSERT INTO badge (nom, description, icon, seuilPoints, image_ia) 
                VALUES (:nom, :description, :icon, :seuilPoints, :image_ia)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'nom' => $nom,
            'description' => $description,
            'icon' => $icon,
            'seuilPoints' => $seuilPoints,
            'image_ia' => $image_ia
        ]);
    }
    
    public function update($id, $nom, $description, $icon, $seuilPoints, $image_ia = null) {
        if ($image_ia) {
            $sql = "UPDATE badge SET nom = :nom, description = :description, 
                    icon = :icon, seuilPoints = :seuilPoints, image_ia = :image_ia 
                    WHERE badge_id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'nom' => $nom,
                'description' => $description,
                'icon' => $icon,
                'seuilPoints' => $seuilPoints,
                'image_ia' => $image_ia,
                'id' => $id
            ]);
        } else {
            $sql = "UPDATE badge SET nom = :nom, description = :description, 
                    icon = :icon, seuilPoints = :seuilPoints 
                    WHERE badge_id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                'nom' => $nom,
                'description' => $description,
                'icon' => $icon,
                'seuilPoints' => $seuilPoints,
                'id' => $id
            ]);
        }
    }
    
    public function delete($id) {
        $sql = "DELETE FROM badge WHERE badge_id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
    
    // =====================================================
    // GÉNÉRATION D'IMAGE PAR IA (Hugging Face)
    // =====================================================
    
    public function generateImageWithAI($description, $badgeName) {
        // Construire un prompt détaillé pour l'IA
        $prompt = $this->buildPrompt($description, $badgeName);
        
        // Appeler l'API Hugging Face
        $imageData = $this->callHuggingFaceAPI($prompt);
        
        if ($imageData) {
            // Sauvegarder l'image localement
            $filename = $this->saveImage($imageData, $badgeName);
            return $filename;
        }
        
        return null;
    }
    
    private function buildPrompt($description, $badgeName) {
        // Prompt en anglais pour de meilleurs résultats
        $prompt = "A beautiful game badge design for an eco-friendly achievement called '" . $badgeName . "'. ";
        $prompt .= "Description: " . $description . ". ";
        $prompt .= "Style: flat vector illustration, shiny gold border, green color theme, ";
        $prompt .= "medal shape, suitable for a mobile app badge, clean background, high quality, 512x512 pixels.";
        
        return $prompt;
    }
    
    private function callHuggingFaceAPI($prompt) {
        $apiKey = HUGGING_FACE_API_KEY;
        $model = HUGGING_FACE_MODEL;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api-inference.huggingface.co/models/" . $model);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'inputs' => $prompt,
            'parameters' => [
                'negative_prompt' => 'text, watermark, signature, low quality, blurry, distorted, ugly',
                'num_inference_steps' => 25,
                'guidance_scale' => 7.5
            ]
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer " . $apiKey,
            "Content-Type: application/json"
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60); // 60 secondes max
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            error_log("Curl error: " . $error);
            return null;
        }
        
        if ($httpCode == 200) {
            return $response;
        } elseif ($httpCode == 503) {
            // Le modèle se charge, attendre et réessayer
            sleep(5);
            return $this->callHuggingFaceAPI($prompt);
        } else {
            error_log("API error: HTTP $httpCode - " . substr($response, 0, 200));
            return null;
        }
    }
    
    private function saveImage($imageData, $badgeName) {
        // Créer le dossier s'il n'existe pas
        $uploadDir = __DIR__ . '/../../assets/images/badges/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        // Générer un nom de fichier unique
        $safeName = preg_replace('/[^a-zA-Z0-9]/', '_', $badgeName);
        $filename = 'badge_' . $safeName . '_' . uniqid() . '.png';
        $filepath = $uploadDir . $filename;
        
        // Sauvegarder l'image
        file_put_contents($filepath, $imageData);
        
        return 'assets/images/badges/' . $filename;
    }
    
    // =====================================================
    // JOINTURE Fidélité + Badges
    // =====================================================
    
    public function getFidelitesWithBadges() {
        $sql = "SELECT f.*, u.nom as user_nom, u.email as user_email,
                       GROUP_CONCAT(DISTINCT b.nom SEPARATOR ', ') as badges,
                       COUNT(DISTINCT b.badge_id) as nb_badges
                FROM fidelite f
                LEFT JOIN utilisateur u ON f.user_id = u.id
                LEFT JOIN user_badges ub ON u.id = ub.user_id
                LEFT JOIN badge b ON ub.badge_id = b.badge_id
                GROUP BY f.idPoints
                ORDER BY f.dateGain DESC";
        return $this->pdo->query($sql)->fetchAll();
    }
}
?>
<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/sms.php';

class Sms {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    /**
     * Envoi de SMS en mode Sandbox (simulation uniquement)
     */
    public function envoyer($tel, $nom, $msg, $type, $abo_id = null): array {
        $tel = $this->normaliserTel($tel);

        // Validation
        if (!$this->validerTel($tel)) {
            $result = ['success' => false, 'error' => "Numéro invalide : $tel"];
            $this->log($abo_id, $tel, $nom, $msg, $type, 'echoue', json_encode($result));
            return $result;
        }

        // Mode SANDBOX : Simulation uniquement
        if (SMS_MODE === 'sandbox') {
            $sid = 'SANDBOX_' . strtoupper(uniqid());
            $result = [
                'success' => true,
                'sandbox' => true,
                'sid'     => $sid,
                'to'      => $tel,
                'message' => $msg,
            ];
            $this->log($abo_id, $tel, $nom, $msg, $type, 'simule', json_encode($result), $sid);
            
            // Debug dans les logs Apache
            error_log("[SANDBOX SMS] $type → $tel : $msg");
            
            return $result;
        }

        // Mode réel (à activer plus tard avec Twilio)
        return $this->envoyerReel($tel, $msg, $type);
    }

    /**
     * Envoi réel via httpsms (Android Gateway)
     */
    private function envoyerReel($tel, $msg, $type) {
        $apiKey = HTTPSMS_API_KEY;
        $from   = HTTPSMS_PHONE_NUMBER;

        if (str_contains($apiKey, '...')) {
            return ['success' => false, 'error' => 'API Key non complétée dans config/sms.php'];
        }

        $url = "https://api.httpsms.com/v1/messages/send";
        
        $data = [
            'content' => $msg,
            'from'    => $from,
            'to'      => $tel
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'x-api-key: ' . $apiKey,
            'Content-Type: application/json'
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            $this->log(null, $tel, 'Client', $msg, $type, 'envoye', $response);
            return ['success' => true, 'response' => json_decode($response, true)];
        } else {
            $this->log(null, $tel, 'Client', $msg, $type, 'echoue', $response);
            return ['success' => false, 'error' => "Erreur API httpsms (Code $httpCode) : $response"];
        }
    }

    /**
     * Notifier l'administrateur d'une nouvelle souscription
     */
    public function notifierAdmin(array $abo): array {
        $msg = "📢 Nouvelle souscription !\n" .
               "Client : {$abo['nom_client']}\n" .
               "Offre : {$abo['type_abonnement']}\n" .
               "Prix : {$abo['prix']} DT";
        
        return $this->envoyer(
            SMS_ADMIN_NUMBER,
            'Admin',
            $msg,
            'admin_notif'
        );
    }

    /**
     * SMS de bienvenue
     */
    public function envoyerBienvenue(array $abo): array {
        return $this->envoyer(
            $abo['telephone'],
            $abo['nom_client'],
            $this->formaterTemplate(SMS_TPL_BIENVENUE, $abo),
            'bienvenue',
            $abo['id'] ?? null
        );
    }

    /**
     * SMS rappel J-7
     */
    public function envoyerRappelJ7(array $abo): array {
        return $this->envoyer(
            $abo['telephone'],
            $abo['nom_client'],
            $this->formaterTemplate(SMS_TPL_RAPPEL_J7, $abo),
            'rappel_j7',
            $abo['id'] ?? null
        );
    }

    /**
     * SMS rappel J-3
     */
    public function envoyerRappelJ3(array $abo): array {
        return $this->envoyer(
            $abo['telephone'],
            $abo['nom_client'],
            $this->formaterTemplate(SMS_TPL_RAPPEL_J3, $abo),
            'rappel_j3',
            $abo['id'] ?? null
        );
    }

    /**
     * SMS expiration
     */
    public function envoyerExpiration(array $abo): array {
        return $this->envoyer(
            $abo['telephone'],
            $abo['nom_client'],
            $this->formaterTemplate(SMS_TPL_EXPIRATION, $abo),
            'expiration',
            $abo['id'] ?? null
        );
    }

    /**
     * Envoi manuel depuis Back Office
     */
    public function envoyerManuel(string $tel, string $nom, string $msg, ?int $abo_id = null): array {
        return $this->envoyer($tel, $nom, $msg, 'manuel', $abo_id);
    }

    /**
     * Formater un template avec les données
     */
    private function formaterTemplate(string $tpl, array $data): string {
        return str_replace(
            ['{nom}', '{type}', '{date_fin}', '{prix}'],
            [
                $data['nom_client'] ?? '',
                ucfirst($data['type_abonnement'] ?? ''),
                isset($data['date_fin']) ? date('d/m/Y', strtotime($data['date_fin'])) : '—',
                ($data['prix'] ?? '0') . ' DT',
            ],
            $tpl
        );
    }

    /**
     * Normaliser le numéro de téléphone
     */
    private function normaliserTel(string $tel): string {
        $tel = preg_replace('/[\s\-\.\(\)]/', '', $tel);
        $tel = preg_replace('/^00/', '+', $tel);
        
        // Numéro tunisien court → +216XXXXXXXX
        if (preg_match('/^[2-9]\d{7}$/', $tel)) {
            $tel = '+216' . $tel;
        }
        return $tel;
    }

    /**
     * Valider le numéro de téléphone
     */
    private function validerTel(string $tel): bool {
        return (bool) preg_match('/^\+\d{8,15}$/', $tel);
    }

    /**
     * Journaliser dans la base de données
     */
    public function log($abo_id, $tel, $nom, $msg, $type, $statut, $api_response = '', $sid = null): bool {
        if (!SMS_LOG_ENABLED) return true;

        $stmt = $this->conn->prepare("
            INSERT INTO sms_logs
                (abonnement_id, telephone, nom_client, message, type_sms, statut, api_response, message_sid)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        if (!$stmt) return false;
        
        $stmt->bind_param("isssssss",
            $abo_id, $tel, $nom, $msg, $type, $statut, $api_response, $sid
        );
        return $stmt->execute();
    }

    /**
     * Récupérer les logs
     */
    public function getLogs($page = 1, $perPage = 20, $filters = []) {
        $offset = ($page - 1) * $perPage;
        $where = "WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($filters['type_sms'])) {
            $where .= " AND type_sms = ?";
            $params[] = $filters['type_sms'];
            $types .= "s";
        }
        if (!empty($filters['statut'])) {
            $where .= " AND statut = ?";
            $params[] = $filters['statut'];
            $types .= "s";
        }
        if (!empty($filters['search'])) {
            $where .= " AND (nom_client LIKE ? OR telephone LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
            $types .= "ss";
        }

        $params[] = $perPage;
        $params[] = $offset;
        $types .= "ii";

        $stmt = $this->conn->prepare("
            SELECT * FROM sms_logs
            $where
            ORDER BY created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Compter les logs
     */
    public function countLogs($filters = []) {
        $where = "WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($filters['type_sms'])) {
            $where .= " AND type_sms = ?";
            $params[] = $filters['type_sms'];
            $types .= "s";
        }
        if (!empty($filters['statut'])) {
            $where .= " AND statut = ?";
            $params[] = $filters['statut'];
            $types .= "s";
        }
        if (!empty($filters['search'])) {
            $where .= " AND (nom_client LIKE ? OR telephone LIKE ?)";
            $params[] = "%{$filters['search']}%";
            $params[] = "%{$filters['search']}%";
            $types .= "ss";
        }

        $stmt = $this->conn->prepare("SELECT COUNT(*) as total FROM sms_logs $where");
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc()['total'] ?? 0;
    }

    /**
     * Statistiques
     */
    public function getStats() {
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
        return $result ? $result->fetch_assoc() : [
            'total' => 0, 'envoyes' => 0, 'echoues' => 0,
            'simules' => 0, 'bienvenues' => 0, 'rappels' => 0, 'manuels' => 0
        ];
    }

    /**
     * Supprimer un log
     */
    public function deleteLog($id) {
        $stmt = $this->conn->prepare("DELETE FROM sms_logs WHERE id = ?");
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Récupérer les abonnements qui expirent bientôt
     */
    public function getAbonnementsExpirantDans($jours) {
        $date = date('Y-m-d', strtotime("+$jours days"));
        $stmt = $this->conn->prepare("
            SELECT * FROM abonnements
            WHERE date_fin = ? AND statut = 'actif'
            AND telephone IS NOT NULL AND telephone != ''
        ");
        $stmt->bind_param("s", $date);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Lancer les rappels automatiques J-7 et J-3
     */
    public function lancerRappelsAutomatiques(): array {
        $rapport = ['j7' => [], 'j3' => [], 'envoyes' => 0, 'erreurs' => 0];

        // Rappels J-7
        $abosJ7 = $this->getAbonnementsExpirantDans(7);
        foreach ($abosJ7 as $abo) {
            $res = $this->envoyerRappelJ7($abo);
            if ($res['success']) {
                $rapport['envoyes']++;
                $rapport['j7'][] = $abo['id'];
            } else {
                $rapport['erreurs']++;
            }
        }

        // Rappels J-3
        $abosJ3 = $this->getAbonnementsExpirantDans(3);
        foreach ($abosJ3 as $abo) {
            $res = $this->envoyerRappelJ3($abo);
            if ($res['success']) {
                $rapport['envoyes']++;
                $rapport['j3'][] = $abo['id'];
            } else {
                $rapport['erreurs']++;
            }
        }

        return $rapport;
    }
}
?>
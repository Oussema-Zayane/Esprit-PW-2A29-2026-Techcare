<?php

class Sms {
    private $id;
    private $telephone;
    private $nom_client;
    private $message;
    private $statut;
    private $type_sms;
    private $created_at;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->telephone = $data['telephone'] ?? null;
            $this->nom_client = $data['nom_client'] ?? null;
            $this->message = $data['message'] ?? null;
            $this->statut = $data['statut'] ?? null;
            $this->type_sms = $data['type_sms'] ?? null;
            $this->created_at = $data['created_at'] ?? null;
        }
    }

    public function __destruct() {
        // Destructeur
    }

    // Getters
    public function getId() { return $this->id; }
    public function getTelephone() { return $this->telephone; }
    public function getNomClient() { return $this->nom_client; }
    public function getMessage() { return $this->message; }
    public function getStatut() { return $this->statut; }
    public function getTypeSms() { return $this->type_sms; }
    public function getCreatedAt() { return $this->created_at; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setTelephone($telephone) { $this->telephone = $telephone; }
    public function setNomClient($nom_client) { $this->nom_client = $nom_client; }
    public function setMessage($message) { $this->message = $message; }
    public function setStatut($statut) { $this->statut = $statut; }
    public function setTypeSms($type_sms) { $this->type_sms = $type_sms; }
    public function setCreatedAt($created_at) { $this->created_at = $created_at; }
}
?>
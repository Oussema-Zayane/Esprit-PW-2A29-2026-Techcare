<?php

class Abonnement {
    private $id;
    private $nom_client;
    private $email;
    private $telephone;
    private $type_abonnement;
    private $prix;
    private $date_debut;
    private $date_fin;
    private $statut;
    private $user_id;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id = $data['id'] ?? null;
            $this->nom_client = $data['nom_client'] ?? null;
            $this->email = $data['email'] ?? null;
            $this->telephone = $data['telephone'] ?? null;
            $this->type_abonnement = $data['type_abonnement'] ?? null;
            $this->prix = $data['prix'] ?? null;
            $this->date_debut = $data['date_debut'] ?? null;
            $this->date_fin = $data['date_fin'] ?? null;
            $this->statut = $data['statut'] ?? null;
            $this->user_id = $data['user_id'] ?? null;
        }
    }

    public function __destruct() {
        // Destructeur
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNomClient() { return $this->nom_client; }
    public function getEmail() { return $this->email; }
    public function getTelephone() { return $this->telephone; }
    public function getTypeAbonnement() { return $this->type_abonnement; }
    public function getPrix() { return $this->prix; }
    public function getDateDebut() { return $this->date_debut; }
    public function getDateFin() { return $this->date_fin; }
    public function getStatut() { return $this->statut; }
    public function getUserId() { return $this->user_id; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setNomClient($nom_client) { $this->nom_client = $nom_client; }
    public function setEmail($email) { $this->email = $email; }
    public function setTelephone($telephone) { $this->telephone = $telephone; }
    public function setTypeAbonnement($type_abonnement) { $this->type_abonnement = $type_abonnement; }
    public function setPrix($prix) { $this->prix = $prix; }
    public function setDateDebut($date_debut) { $this->date_debut = $date_debut; }
    public function setDateFin($date_fin) { $this->date_fin = $date_fin; }
    public function setStatut($statut) { $this->statut = $statut; }
    public function setUserId($user_id) { $this->user_id = $user_id; }
}
?>
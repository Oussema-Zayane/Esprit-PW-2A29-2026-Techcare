<?php

class Type {
    private $id_type;
    private $nom_type;
    private $prix_base;
    private $duree_jours;
    private $description;
    private $icone;

    public function __construct($data = []) {
        if (!empty($data)) {
            $this->id_type = $data['id_type'] ?? null;
            $this->nom_type = $data['nom_type'] ?? null;
            $this->prix_base = $data['prix_base'] ?? null;
            $this->duree_jours = $data['duree_jours'] ?? null;
            $this->description = $data['description'] ?? null;
            $this->icone = $data['icone'] ?? null;
        }
    }

    public function __destruct() {
        // Destructeur
    }

    // Getters
    public function getIdType() { return $this->id_type; }
    public function getNomType() { return $this->nom_type; }
    public function getPrixBase() { return $this->prix_base; }
    public function getDureeJours() { return $this->duree_jours; }
    public function getDescription() { return $this->description; }
    public function getIcone() { return $this->icone; }

    // Setters
    public function setIdType($id_type) { $this->id_type = $id_type; }
    public function setNomType($nom_type) { $this->nom_type = $nom_type; }
    public function setPrixBase($prix_base) { $this->prix_base = $prix_base; }
    public function setDureeJours($duree_jours) { $this->duree_jours = $duree_jours; }
    public function setDescription($description) { $this->description = $description; }
    public function setIcone($icone) { $this->icone = $icone; }
}
?>
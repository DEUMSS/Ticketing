<?php

namespace ticketing\model;

class ticket{

    protected int $TI_id;
    protected int $TI_idClient;
    protected int $TI_idUtilisateur;
    protected int $TI_idTypeDemande;
    protected int $TI_idPriorite;
    protected string $TI_sujet;
    protected string $TI_message;
    protected bool $TI_etat;

    public function getId(){
        return $this->TI_id;
    }
    public function getIdClident(){
        return $this->TI_idClient;
    }
    public function getIdUtilisateur(){
        return $this->TI_idUtilisateur;
    }
    public function getIdTypeDemande(){
        return $this->TI_idTypeDemande;
    }
    public function getIdPriorite(){
        return $this->TI_idPriorite;
    }
    public function getSujet(){
        return $this->TI_sujet;
    }
    public function getMessage(){
        return $this->TI_message;
    }
    public function getEtat(){
        return $this->TI_etat;
    }

    public function setId( int $id ){
        $this->TI_id = $id;
    }
    public function setIdClident( int $idClient ){
        $this->TI_idClient = $idClient;
    }
    public function setIdUtilisateur( int $idUtilisateur ){
        $this->TI_idUtilisateur = $idUtilisateur;
    }
    public function setIdTypeDemande( int $idTypeDemande ){
        $this->TI_idTypeDemande = $idTypeDemande;
    }
    public function setIdPriorite( int $idPriorite ){
        $this->TI_idPriorite = $idPriorite;
    }
    public function setSujet( string $sujet ){
        $this->TI_sujet = $sujet;
    }
    public function setMessage( string $message ){
        $this->TI_message = $message;
    }
    public function setEtat( bool $etat ){
        $this->TI_etat = $etat;
    }
}
?>
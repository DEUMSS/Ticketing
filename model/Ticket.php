<?php

namespace Ticketing\model;

class Ticket{

    protected $TI_id;
    protected $TI_idClient;
    protected $TI_idUtilisateur;
    protected $TI_idTypeDemande;
    protected $TI_idPriorite;
    protected $TI_sujet;
    protected $TI_message;
    protected $TI_etat;

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
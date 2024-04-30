<?php

namespace ticketing\model;

class TraitementManager extends manager{

    public function __construct()
    {
        parent::__construct();
    }

    public function getMessageByIdTicket( int $idTicket ){
        $sql = "SELECT * FROM traitement WHERE TM_idTicket=:id";
        $req=$this->manager->db->prepare($sql);
        $req->execute([
            ':id' => $idTicket
        ]);
        $data = $req->fetch(\PDO::FETCH_ASSOC);
        if( $data){
            $traitement = new Traitement( $data );
            return $traitement;    
        }else{
            return false;
        }
    }

    public function createTraitement(array $data){
        $sql = "INSERT INTO traitement (TM_idTicket, TM_message, TM_dateMessage) VALUES (:idTicket, :message, :dateMessage)";
        $req = $this->manager->db->prepare($sql);
        $state = $req->execute([
            ':idTicket' => $data['idTicket'],
            ':message' => $data['message'],
            ':dateMessage' => $data['dateMAJ']
        ]);
        return $state;
    }
}
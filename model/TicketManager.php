<?php

namespace ticketing\model;

class TicketManager extends Manager
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getTypeDemande(){
        $sql = "SELECT * FROM typedemande";
        $allTypeDemande = $this->manager->db->query( $sql );
        $listTypeDemande = $allTypeDemande->fetchAll();
        return $listTypeDemande;
    }

    public function getPriorite(){
        $sql = "SELECT * FROM priorite";
        $allPriorite = $this->manager->db->query( $sql );
        $listPriorite = $allPriorite->fetchAll();
        return $listPriorite;
    }

    public function createTicket( Ticket $newTicket){
        $sql = "INSERT INTO ticket (TI_idClient, TI_idTypeDemande, TI_idPriorite, TI_sujet, TI_message, TI_actif, TI_dateCrea, TI_dateMAJ) VALUES(:idClient, :idTypeDemande, :idPriorite, :sujet, :message, :actif, :dateCrea, :dateMAJ)";
        $req = $this->manager->db->prepare( $sql );
        $state = $req->execute([
            ':idClient'         => $newTicket->getTI_idClient(),
            ':idTypeDemande'    => $newTicket->getTI_idTypeDemande(),
            ':idPriorite'       => $newTicket->getTI_idPriorite(),
            ':sujet'            => $newTicket->getTI_sujet(),
            ':message'          => $newTicket->getTI_messagee(),
            ':actif'            => true,
            ':dateCrea'         => date('Y-m-d H:i:s'),
            ':dateMAJ'          => date('Y-m-d H:i:s')
        ]);
        if( $state ) {
            $newTicket->setTI_id( $this->manager->db->lastInsertId());
        }
        return $state;
    }
}

?>
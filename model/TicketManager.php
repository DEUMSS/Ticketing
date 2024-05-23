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
        return mb_convert_encoding($listTypeDemande, 'UTF-8', 'ISO-8859-1');
    }

    public function getPriorite(){
        $sql = "SELECT * FROM priorite";
        $allPriorite = $this->manager->db->query( $sql );
        $listPriorite = $allPriorite->fetchAll();
        return mb_convert_encoding($listPriorite, 'UTF-8', 'ISO-8859-1');
    }

    public function createTicket( Ticket $newTicket){
        $sql = "INSERT INTO ticket (TI_idClient, TI_idTypeDemande, TI_idPriorite, TI_sujet, TI_message, TI_actif, TI_dateCrea, TI_dateMAJ) VALUES(:idClient, :idTypeDemande, :idPriorite, :sujet, :message, :actif, :dateCrea, :dateMAJ)";
        $req = $this->manager->db->prepare( $sql );
        $state = $req->execute([
            ':idClient'         => $newTicket->getTI_idClient(),
            ':idTypeDemande'    => $newTicket->getTI_idTypeDemande(),
            ':idPriorite'       => $newTicket->getTI_idPriorite(),
            ':sujet'            => $newTicket->getTI_sujet(),
            ':message'          => $newTicket->getTI_message(),
            ':actif'            => true,
            ':dateCrea'         => date('Y-m-d H:i:s'),
            ':dateMAJ'          => date('Y-m-d H:i:s')
        ]);
        if( $state ) {
            $newTicket->setTI_id( $this->manager->db->lastInsertId());
        }
        return $state;
    }

    public function listTicket( array $params ){
        $order = !empty( $params['order'] ) ? $params['order'] : 'ASC';
        $sort = !empty( $params['sort'] ) ? $params['sort'] : 'TI_dateCrea';
        $limit = !empty( $params['limit'] ) ? $params['limit'] : 10;
        $offset = !empty( $params['offset'] ) ? $params['offset'] : 0;
        $strLike = false;
        if( !empty( $params['search'] ) && !empty( $params['searchable'] ) ) {
            foreach( $params['searchable'] as $searchItem ) {
                if ($searchItem == "typeDemande"){
                    $searchItem = "typedemande.TD_typeDemande";
                }
                if ($searchItem == "priorite"){
                    $searchItem = "priorite.PR_priorite";   
                }
                $search = $params['search'];
                $strLike .= $searchItem . " LIKE '%$search%' OR ";
            }
            $strLike = trim( $strLike, ' OR ' );
        }
        $sql = "SELECT ticket.*, typedemande.TD_id AS Type, priorite.PR_id AS Priority FROM ticket 
                INNER JOIN typedemande ON ticket.TI_idTypeDemande = typedemande.TD_id 
                INNER JOIN priorite ON ticket.TI_idPriorite = priorite.PR_id";
        $reqWhereUse = false;
        if (isset($_SESSION['idClient'])){
            $sql .= " WHERE TI_idClient = " . $_SESSION['idClient'];
            $reqWhereUse = true;
        }
        if( $strLike ) {
            if ($reqWhereUse){
                $sql .= " AND (" . $strLike . ")";
            } else {
                $sql .= " WHERE " . $strLike;
                $reqWhereUse = true;
            }
        }
        $sqlFerme = false;
        if(isset($_SESSION['roleUser'])){
            if ($params['ferme']){
                $sqlFerme = "ticket.TI_actif = 0";
            } elseif ($params['ouvert']){
                $sqlFerme = "ticket.TI_actif = 1";
            }
        }
        if ($sqlFerme){
            if ($reqWhereUse){
                $sql .= " AND " . $sqlFerme;
            } else {
                $sql .= " WHERE " . $sqlFerme;
                $reqWhereUse = true;
            }
        }        
        $sql .= " ORDER BY $sort $order";
        $sql .= " LIMIT $offset, $limit";
        $response = $this->manager->db->query( $sql );
        $dataList =  mb_convert_encoding($response->fetchAll( \PDO::FETCH_ASSOC ), 'UTF-8', 'ISO-8859-1');
        $listTickets = [];
        foreach ( $dataList as $data ) {
            $listTickets[] = new Ticket( $data );
        }
        return $listTickets;
    }

    public function getTypeDemandeById(int $idTypeDemande ){
        $sql = "SELECT TD_typeDemande FROM typedemande WHERE TD_id=:id";
        $req = $this->manager->db->prepare( $sql );
        $req->execute([
            ':id'    => $idTypeDemande,
        ]);
        $typeDemande = $req->fetch();
        return mb_convert_encoding($typeDemande['TD_typeDemande'], 'UTF-8', 'ISO-8859-1');
    }

    public function getPrioriteById(int $idPriorite){
        $sql = "SELECT PR_priorite FROM priorite WHERE PR_id=:id";
        $req=$this->manager->db->prepare($sql);
        $req->execute([
            ':id' => $idPriorite
        ]);
        $priorite = $req->fetch();
        return mb_convert_encoding($priorite['PR_priorite'], 'UTF-8', 'ISO-8859-1');
    }

    public function getTicketById( int $idTicket ){
        $sql = "SELECT * FROM ticket WHERE TI_id=:id";
        $req = $this->manager->db->prepare($sql);
        $req->execute([
            ':id' => $idTicket
        ]);
        $dataTicket = mb_convert_encoding($req->fetch(\PDO::FETCH_ASSOC), 'UTF-8', 'ISO-8859-1');
        $ticket = new Ticket( $dataTicket );
        return $ticket;
    }

    public function updateDateMAJTicket( array $data ){
        $sql = "UPDATE ticket SET TI_dateMAJ = :dateMAJ WHERE TI_id=:id";
        $req = $this->manager->db->prepare($sql);
        $state = $req->execute([
            ':dateMAJ' => $data['dateMAJ'],
            ':id'      => $_SESSION['idTicket']
        ]);
        return $state;
    }

    public function fermetureTicket( int $idTicket ){
        $sql = "UPDATE ticket SET TI_actif = 0 WHERE TI_id = :id";
        $req = $this->manager->db->prepare($sql);
        $state = $req->execute([
            ':id' => $idTicket
        ]);
        return $state;
    }
    public function ouvertureTicket( int $idTicket ){
        $sql = "UPDATE ticket SET TI_actif = 1 WHERE TI_id = :id";
        $req = $this->manager->db->prepare($sql);
        $state = $req->execute([
            ':id' => $idTicket
        ]);
        return $state;
    }
}

?>
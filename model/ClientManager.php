<?php

namespace ticketing\model;


class ClientManager extends Manager
{

    public function __construct()
    {
        parent::__construct();
    }


    public function countAll()
    {
        $sql = "SELECT count(*) FROM users";
        $response = $this->manager->db->query( $sql );
        $nbUsers = $response->fetch();
        return $nbUsers[0];
    }


    
    public function getAllClient( array $params )
    {
        $order = !empty( $params['order'] ) ? $params['order'] : 'ASC';
        $sort = !empty( $params['sort'] ) ? $params['sort'] : 'id';
        $limit = !empty( $params['limit'] ) ? $params['limit'] : 10;
        $offset = !empty( $params['offset'] ) ? $params['offset'] : 0;
        $strLike = false;
        if( !empty( $params['search'] ) && !empty( $params['searchable'] ) ) {
            foreach( $params['searchable'] as $searchItem ) {
                $search = $params['search'];
                $strLike .= $searchItem . " LIKE '%$search%' OR ";
            }
            $strLike = trim( $strLike, ' OR ' );
        }
        $sql = "SELECT * FROM users";
        if( $strLike ) {
            $sql .= " WHERE $strLike";
        }
        $sql .= " ORDER BY $sort $order";
        $sql .= " LIMIT $offset, $limit";
        $response = $this->manager->db->query( $sql );
		$dataList = mb_convert_encoding($response->fetchAll( \PDO::FETCH_ASSOC ), 'UTF-8', 'ISO-8859-1');
        $listUsers = [];
		foreach ( $dataList as $data ) {
			$listUsers[] = new Client( $data );
		}
        return $listUsers;
    }

    public function getClient( string $login )
    {
        $sql = "SELECT * FROM client WHERE CI_login=:login";
        $req = $this->manager->db->prepare( $sql );
        $req->execute([':login'=>$login] );
        $data = mb_convert_encoding($req->fetch(\PDO::FETCH_ASSOC), 'UTF-8', 'ISO-8859-1');
        $connectedClient = new Client( $data );
        return $connectedClient;
    }

    public function getClientById( int $idClient )
    {
        $sql = "SELECT * FROM client WHERE CI_id=:id";
        $req = $this->manager->db->prepare( $sql );
        $req->execute([':id'=>$idClient] );
        $data = mb_convert_encoding($req->fetch(\PDO::FETCH_ASSOC), 'UTF-8', 'ISO-8859-1');
        $connectedClient = new Client( $data );
        return $connectedClient;
    }

    public function updateClient( Client $client ) : ?bool 
    {
        $sql = "UPDATE client SET CI_nom=:nom, CI_prenom=:prenom, CI_entreprise=:entreprise, CI_dateCrea=:dateCrea, CI_actif=:actif WHERE CI_id=:id";
        $req = $this->manager->db->prepare( $sql );
        $state = $req->execute([
            ':id'           => $client->getCI_id(),
            ':nom'          => $client->getCI_nom(),
            ':prenom'       => $client->getCI_prenom(),
            ':entreprise'   => $client->getCI_entreprise(),
            ':dateCrea'     => $client->getCI_dateCreaSQL(),
            ':actif'        => $client->getCI_actif()
        ]);
        return $state;
    }

    public function isLoginUsed( String $login ){
        $sql = "SELECT * FROM client WHERE CI_login=:login";
        $req = $this->manager->db->prepare( $sql );
        $req->execute([
            ':login'    => $login,
        ]);
        $loginUsed = $req->fetch();
        return $loginUsed;
    }

    public function addClient( Client $newClient )
    {
        $sql = "INSERT INTO client(CI_login, CI_password, CI_nom, CI_prenom, CI_entreprise, CI_dateCrea, CI_actif) VALUES (:login, :password, :nom, :prenom, :entreprise, :dateCrea, :actif)";
        $req = $this->manager->db->prepare( $sql );
        $state = $req->execute([
            ':login'        => $newClient->getCI_login(),
            ':password'     => $newClient->getCI_password(),
            ':nom'          => $newClient->getCI_nom(),
            ':prenom'       => $newClient->getCI_prenom(),
            ':entreprise'   => $newClient->getCI_entreprise(),
            ':dateCrea'     => $newClient->getCI_dateCreaSQL(),
            ':actif'        => true
        ]);
        if( $state ) {
            $newClient->setCI_id( $this->manager->db->lastInsertId());
        }
        return $state;
    }

    public function listClient( array $params ){
        $order = !empty( $params['order'] ) ? $params['order'] : 'ASC';
        $sort = !empty( $params['sort'] ) ? $params['sort'] : 'CI_dateCrea';
        $limit = !empty( $params['limit'] ) ? $params['limit'] : 10;
        $offset = !empty( $params['offset'] ) ? $params['offset'] : 0;
        $strLike = false;
        if( !empty( $params['search'] ) && !empty( $params['searchable'] ) ) {
            foreach( $params['searchable'] as $searchItem ) {
                $search = $params['search'];
                $strLike .= $searchItem . " LIKE '%$search%' OR ";
            }
            $strLike = trim( $strLike, ' OR ' );
        }
        $sql = "SELECT * FROM client"; 
        $reqWhereUse = false;
        if( $strLike ) {
            if ($reqWhereUse){
                $sql .= " AND (" . $strLike . ")";
            } else {
                $sql .= " WHERE " . $strLike;
                $reqWhereUse = true;
            }
        }
        $sqlActif = false;
        if ($params['inactif']){
            $sqlActif = "CI_actif = 0";
        } elseif ($params['actif']){
            $sqlActif = "CI_actif = 1";
        }
        if ($sqlActif){
            if ($reqWhereUse){
                $sql .= " AND " . $sqlActif;
            } else {
                $sql .= " WHERE " . $sqlActif;
                $reqWhereUse = true;
            }
        }        
        $sql .= " ORDER BY $sort $order";
        $sql .= " LIMIT $offset, $limit";
        $response = $this->manager->db->query( $sql );
        $dataList =  mb_convert_encoding($response->fetchAll( \PDO::FETCH_ASSOC ), 'UTF-8', 'ISO-8859-1');
        $listClient = [];
        foreach ( $dataList as $data ) {
            $listClient[] = new Client( $data );
        }
        return $listClient;
    }

    public function desactiveClient( int $idClient ){
        $sql = "UPDATE client SET CI_actif = 0 WHERE CI_id = :id";
        $req = $this->manager->db->prepare($sql);
        $state = $req->execute([
            ':id' => $idClient
        ]);
        return $state;
    }
    
    public function activeClient( int $idClient ){
        $sql = "UPDATE client SET CI_actif = 1 WHERE CI_id = :id";
        $req = $this->manager->db->prepare($sql);
        $state = $req->execute([
            ':id' => $idClient
        ]);
        return $state;
    }

    public function desactiveClientByLogin( string $loginClient ){
        $sql = "UPDATE client SET CI_actif = 0 WHERE CI_login = :login";
        $req = $this->manager->db->prepare($sql);
        $state = $req->execute([
            ':login' => $loginClient
        ]);
        return $state;
    }

}


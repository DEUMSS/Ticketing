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
		$dataList = $response->fetchAll( \PDO::FETCH_ASSOC );
        $listUsers = [];
		foreach ( $dataList as $data ) {
			$listUsers[] = new Client( $data );
		}
        return $listUsers;
    }


    /**
     * Return user object
     * 
     * @param int $id
     * @return false|Users
     */
    public function getClient( string $login )
    {
        $sql = "SELECT * FROM client WHERE CI_login=:login";
        $req = $this->manager->db->prepare( $sql );
        $req->execute([':login'=>$login] );
        $data = $req->fetch(\PDO::FETCH_ASSOC);
        $connectedClient = new Client( $data );
        return $connectedClient;
    }

    public function getClientById( int $idClient )
    {
        $sql = "SELECT * FROM client WHERE CI_id=:id";
        $req = $this->manager->db->prepare( $sql );
        $req->execute([':id'=>$idClient] );
        $data = $req->fetch(\PDO::FETCH_ASSOC);
        $connectedClient = new Client( $data );
        return $connectedClient;
    }


    /**
     * Undocumented function
     *
     * @param integer $id
     * @return integer
     */
	public function deleteClient( int $id ): int
	{
		$sql = "DELETE FROM client WHERE id=:id";
		$req = $this->manager->db->prepare( $sql );
		$state = $req->execute([
			':id'  => $id
		]);
		return $state;
	}


    public function setClient( int $id, int $isActive ): bool
    {
        $sql = 'UPDATE client SET CI_actif=:isActive WHERE CI_id=:id';
        $req = $this->manager->db->prepare( $sql );
		$state = $req->execute([
            ':isActive'     => $isActive,
			':id'           => $id
		]);
        return $state;
    }

    /*
    public function isUserAdmin(): bool
    {
        $sql = 'SELECT CI_id FROM client WHERE isAdmin=1';
        $req = $this->manager->db->query( $sql );
		if($id = current($req->fetch())){
            $sql = 'UPDATE users SET isAdmin=0 WHERE id=:id';
            $req = $this->manager->db->prepare( $sql );
            return $req->execute([
                ':id'       => $id
            ]);
        } else return true;
    }
    */
    /*
    public function setUserAdmin( int $id, int $isAdmin): bool
    {
        if(!$this->isUserAdmin()){
            return false;
        }
        $sql = 'UPDATE users SET isAdmin=:isAdmin WHERE id=:id';
        $req = $this->manager->db->prepare( $sql );
		$state = $req->execute([
            ':isAdmin'  => $isAdmin,
			':id'       => $id
		]);
        return $state;
    }
    */

    public function updateClient( Client $client ) : ?bool 
    {
        $sql = "UPDATE client SET CI_nom=:nom, CI_prenom=:prenom, CI_entreprise=:entreprise, CI_dateCrea=:dateCrea, CI_actif=:actif WHERE CI_id=:id";
        $req = $this->manager->db->prepare( $sql );
        $state = $req->execute([
            ':id'           => $client->getCI_id(),
            ':nom'          => $client->getCI_nom(),
            ':prenom'       => $client->getCI_prenom(),
            ':entreprise'   => $client->getCI_entreprise(),
            ':dateCrea'     => $client->getCI_dateCrea(),
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
            ':dateCrea'     => $newClient->getCI_dateCrea()->format('Y-m-d'),
            ':actif'        => true
        ]);
        if( $state ) {
            $newClient->setCI_id( $this->manager->db->lastInsertId());
        }
        return $state;
    }
}


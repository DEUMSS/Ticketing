<?php

namespace ticketing\model;

class UserManager extends Manager
{
    public function __construct()
    {
        parent::__construct();
    }

    public function isAccountUsed( String $login ){
        $sql = "SELECT * FROM utilisateur WHERE UT_login=:login AND UT_actif=1";
        $req = $this->manager->db->prepare( $sql );
        $req->execute([
            ':login'    => $login,
        ]);
        $accountUsed = $req->fetch();
        return $accountUsed;
    }

    public function createUser( $newUser ){
        $sql = "INSERT INTO utilisateur(UT_login, UT_password, UT_nom, UT_prenom, UT_role, UT_dateCrea) VALUES (:login, :password, :nom, :prenom, :role, :dateCrea)";
        $req = $this->manager->db->prepare( $sql );
        $state = $req->execute([
            ':login'        => $newUser->getUT_login(),
            ':password'     => $newUser->getUT_password(),
            ':nom'          => $newUser->getUT_nom(),
            ':prenom'       => $newUser->getUT_prenom(),
            ':dateCrea'     => $newUser->getUT_dateCreaSQL(),
            ':role'         => 'PERSONNEL',
       ]);
        if( $state ) {
            $newUser->setUT_id( $this->manager->db->lastInsertId());
        }
        return $state;
    }

    public function getUser( string $login )
    {
        $sql = "SELECT * FROM utilisateur WHERE UT_login=:login";
        $req = $this->manager->db->prepare( $sql );
        $req->execute([':login'=>$login] );
        $data = $req->fetch(\PDO::FETCH_ASSOC);
        $connectedUser = new User( $data );
        return $connectedUser;
    }

    public function listUser( array $params ){
        $order = !empty( $params['order'] ) ? $params['order'] : 'ASC';
        $sort = !empty( $params['sort'] ) ? $params['sort'] : 'UT_dateCrea';
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
        $sql = "SELECT * FROM utilisateur"; 
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
            $sqlActif = "UT_actif = 0";
        } elseif ($params['actif']){
            $sqlActif = "UT_actif = 1";
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
        $dataList = $response->fetchAll( \PDO::FETCH_ASSOC );
        $listUser = [];
        foreach ( $dataList as $data ) {
            $listUser[] = new User( $data );
        }
        return $listUser;
    }

    public function getUserById( int $idUser ){
        $sql = "SELECT * FROM utilisateur WHERE UT_id = :id";
        $req = $this->manager->db->prepare($sql);
        $req->execute([
            ':id' => $idUser
        ]);
        $data = $req->fetch(\PDO::FETCH_ASSOC);
        $user = new User( $data );
        return $user;
    }

    public function updateRole( array $data ){
        $sql = "UPDATE utilisateur SET UT_role = :role WHERE UT_id=:id";
        $req = $this->manager->db->prepare($sql);
        $state = $req->execute([
            ':role' => $data['role'],
            ':id'   => $data['idUser']
        ]);
        return $state;
    }

    public function desactiveUser( int $idUser ){
        $sql = "UPDATE utilisateur SET UT_actif = 0 WHERE UT_id = :id";
        $req = $this->manager->db->prepare($sql);
        $state = $req->execute([
            ':id' => $idUser
        ]);
        return $state;
    }

    public function activeUser( int $idUser ){
        $sql = "UPDATE utilisateur SET UT_actif = 1 WHERE UT_id = :id";
        $req = $this->manager->db->prepare($sql);
        $state = $req->execute([
            ':id' => $idUser
        ]);
        return $state;
    }

    public function listDemandeUser( array $params ){
        $order = !empty( $params['order'] ) ? $params['order'] : 'ASC';
        $sort = !empty( $params['sort'] ) ? $params['sort'] : 'UT_dateCrea';
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
        $sql = "SELECT * FROM utilisateur INNER JOIN client ON (utilisateur.UT_login = client.CI_login) WHERE client.CI_actif = 1"; 
        if( $strLike ) {
            $sql .= " AND (" . $strLike . ")";
        }
        $sqlActif = false;
        if ($sqlActif){
            $sql .= " AND " . $sqlActif;
        }        
        $sql .= " ORDER BY $sort $order";
        $sql .= " LIMIT $offset, $limit";
        $response = $this->manager->db->query( $sql );
        $dataList = $response->fetchAll( \PDO::FETCH_ASSOC );
        $listUser = [];
        foreach ( $dataList as $data ) {
            $listUser[] = new User( $data );
        }
        return $listUser;
    }
}

?>
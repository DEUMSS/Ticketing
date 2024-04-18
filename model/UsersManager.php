<?php
/**
 * Entity manager Users Table
 */

namespace ticketing\model;

/**
 *
 * This class is used to manage users
 *
 * @author Fred Fraticelli
 *
 */
class UsersManager extends Manager
{
    
    private $_user;

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


    
    public function getAllUsers( array $params )
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
    public function getUser( int $id )
    {
        $sql = "SELECT * FROM users WHERE id=:id";
        $req = $this->manager->db->prepare( $sql );
        if( $res = $req->execute([':id'=>$id] ) ) {
            $user = $req->fetch(\PDO::FETCH_ASSOC);
            $this->_user = new Client( $user );
            return $this->_user;
        } else {
            return false;
        }
    }



    /**
     * Undocumented function
     *
     * @param integer $id
     * @return integer
     */
	public function deleteUser( int $id ): int
	{
		$sql = "DELETE FROM users WHERE id=:id";
		$req = $this->manager->db->prepare( $sql );
		$state = $req->execute([
			':id'  => $id
		]);
		return $state;
	}



    /**
     * Undocumented function
     *
     * @param integer $id
     * @param integer $isActive
     * @return boolean
     */
    public function setUser( int $id, int $isActive ): bool
    {
        $sql = 'UPDATE users SET isActive=:isActive WHERE id=:id';
        $req = $this->manager->db->prepare( $sql );
		$state = $req->execute([
            ':isActive'     => $isActive,
			':id'           => $id
		]);
        return $state;
    }

    public function isUserAdmin(): bool
    {
        $sql = 'SELECT id FROM users WHERE isAdmin=1';
        $req = $this->manager->db->query( $sql );
		if($id = current($req->fetch())){
            $sql = 'UPDATE users SET isAdmin=0 WHERE id=:id';
            $req = $this->manager->db->prepare( $sql );
            return $req->execute([
                ':id'       => $id
            ]);
        } else return true;
    }

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

    public function updateUser( Client $user ) : ?bool 
    {
        $sql = "UPDATE users SET name=:name, surname=:surname WHERE id=:id";
        $req = $this->manager->db->prepare( $sql );
        $state = $req->execute([
            ':id'       => $user->getId(),
            ':name'     => $user->getNom(),
            ':surname'  => $user->getPrenom()
        ]);
        return $state;
    }



    public function addUser( Client $newUser )
    {
        $sql = "INSERT INTO users(login, password) VALUES (:login, :password)";
        $req = $this->manager->db->prepare( $sql );
        $state = $req->execute([
            ':login'    => $newUser->getLogin(),
            ':password' => $newUser->getPassword()
        ]);
        if( $state ) {
            $newUser->setId( $this->manager->db->lastInsertId() );
        }
        return $state;
    }


}


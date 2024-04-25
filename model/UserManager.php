<?php

namespace Ticketing\model;

class UserManager extends Manager
{
    public function __construct()
    {
        parent::__construct();
    }

    public function isAccountUsed( String $login ){
        $sql = "SELECT * FROM utilisateur WHERE UT_login=:login";
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
            ':dateCrea'     => $newUser->getUT_dateCrea()->format('Y-m-d'),
            ':role'         => 'UTILISATEUR',
       ]);
        if( $state ) {
            $newUser->setUT_id( $this->manager->db->lastInsertId());
        }
        return $state;
    }
}

?>
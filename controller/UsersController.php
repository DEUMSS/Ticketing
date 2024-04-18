<?php

namespace ticketing\controller;

use ticketing\model\UsersManager;
use ticketing\model\Users;
use ticketing\classes\JWT;

/**
 *
 * This class is used to manage users
 *
 * @author Fred Fraticelli
 *
 * @internal
 */
class UsersController extends Controller
{
    protected $usersManager;


    public function __construct( array $params=[] )
    {
        $this->usersManager = new UsersManager();
        parent::__construct( $params );
    }



    public function defaultAction()
    {
        $this->listusersAction();
    }


    
    public function userAction()
    {
        $data = [];
        if( isset( $this->vars['id'] ) ) {
            $user = $this->usersManager->getUser( $this->vars['id'] );
            $data = [
                'user'  => $user
            ];
        }
        $this->render( 'users/user', $data );
    }


    public function updateuserAction() {

    }


	public function deleteuserAction()
	{
		if( isset( $this->vars['id'] ) ) {
			if( $this->usersManager->deleteUser( $this->vars['id'] ) ) {
				return $this->redirectToRoute( 'users/listusers', ['message'=>'Utilisateur effacé'] );
			}
		}
	}


    public function createuserAction() {

    }
    

    public function listusersAction()
    {
        $nbUsers = $this->usersManager->countAll() ?? 0;
        $listUsers = $this->usersManager->getAllUsers([]);
        $data = [
            "nbUsers"       => $nbUsers,
            "listUsers"     => $listUsers,
            "titre"		    => "Page utilisateur.",
			"vars"			=> $this->vars
        ];
        $this->render( 'users/listusers', $data );
    }
}







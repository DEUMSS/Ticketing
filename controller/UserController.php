<?php

namespace Ticketing\controller;

use ticketing\model\UserManager;
use ticketing\model\User;
use Ticketing\model\ClientManager;
use Ticketing\model\Client;


class UserController extends Controller
{
    protected $userManager;
    protected $clientManager;

    public function __construct( array $params=[] )
    {
        $this->userManager = new UserManager();
        parent::__construct( $params );
    }

    public function defaultAction()
    {
        $this->registeruserAction();
    }

    public function registeruserAction(){
        $data=[];
        $this->render('client/registeruser');
    }

    public function requestuserAction(){
        $this->clientManager = new ClientManager();
        $clientConnected = $this->clientManager->getClientById($_SESSION['idClient']);
        $params=[
            "UT_login"      => $clientConnected->getCI_login(),
            "UT_password"   => $clientConnected->getCI_password(),
            "UT_prenom"     => $clientConnected->getCI_prenom(),
            "UT_nom"        => $clientConnected->getCI_nom(),
            "UT_dateCrea"   => date('Y-m-d'),
        ];
        $newUser = new User( $params );
        $accountUsed = $this->userManager->isAccountUsed( $newUser->getUT_login() );
        if( !empty($accountUsed) ){
            $data=[
                'resultat' => false,
                'message'  => 'Vous avez déjà fait une demande pour devenir membre'
            ];
            $this->render( 'client/registeruser', $data );
            exit;
        }
        $state = $this->userManager->createUser( $newUser );
        if( $state ){
            $data = [
                'resultat' => true,
                'message' => 'Votre demande pour devnir membre à bien était enregistré'
            ];
            $this->render('Client/registeruser', $data);
        }else{
            $data = [
                'resultat'  => false,
                'message'   => 'Une erreur c\'est produite lors de la création de votre compte'
            ];
            $this->render( 'Client/registeruser', $data );
        }
    }
}
<?php

namespace ticketing\controller;

use ticketing\model\UserManager;
use ticketing\model\User;
use ticketing\model\ClientManager;
use ticketing\model\Client;


class UserController extends Controller
{
    protected $userManager;
    protected $clientManager;

    public function __construct( array $params=[] )
    {
        $this->userManager = new UserManager();
        $this->clientManager = new ClientManager();
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

    public function listuserAction(){
        $data = [];
        $this->render('user/listuser', $data);
    }

    public function listuserBSAction(){
        if(isset($_SESSION['idUser'])){
            $searchParams = [
                'search'		=> $this->vars['search'],
                'sort'			=> $this->vars['sort'],
                'order'			=> $this->vars['order'],
                'offset'		=> $this->vars['offset'],
                'limit'			=> $this->vars['limit'],
                'searchable'	=> $this->vars['searchable'],
                'inactif'       => $this->vars['inactif'] == 1,
                'actif'         => $this->vars['actif'] == 1
            ];
            
            $listUser = $this->userManager->listUser( $searchParams );
            $searchParams['offset'] = "";
            $searchParams['limit'] = "";
            $nbUser = count($this->userManager->listUser($searchParams));
    
            $dataBs = [];
            foreach( $listUser as $user ) {
                $dataBs[] = [
                    'UT_id'             => $user->getUT_id(),
                    'UT_prenom'         => $user->getUT_prenom(),
                    'UT_nom'            => $user->getUT_nom(),
                    'UT_login'          => $user->getUT_login(),
                    'UT_dateCrea'       => $user->getUT_dateCrea(),
                    'UT_actif'          => $user->getUT_actif() ? "Ouvert" : "Fermé",
                    'UT_role'           => $user->getUT_role()
                ];
            }
    
            $data = [
                "rows"      => $dataBs,
                "total"     => $nbUser
            ];
            $jsData = json_encode( $data );
            echo $jsData;
        }else{
            $data=[];
            $this->render('user/listuser', $data );
        }
    }
}
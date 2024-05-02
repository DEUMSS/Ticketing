<?php

namespace ticketing\controller;

use ticketing\model\UserManager;
use ticketing\model\User;
use ticketing\model\ClientManager;

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
                    'UT_actif'          => $user->getUT_actif() ? "Actif" : "Inactif",
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

    public function updateuserAction( array $data=[] ){
        if(isset($this->vars['id']) || isset($_SESSION['idUserMaj']) ){
            if(isset( $this->vars['id'])){
                $_SESSION['idUserMaj'] = $this->vars['id'];
            }
            $user = $this->userManager->getUserById(  $_SESSION['idUserMaj'] );
            if(isset($user)){
                $data['user'] = $user;
                $this->render('user/updateuser', $data );
            }else{
                $data = [
                'resultat'  => 'alert-danger',
                'message'   => 'Une erreur est survenue lors de la récupération des infomations du compte'
            ];
            $this->render('user/listuser', $data);    
            }
        }else{
            $data = [
            'resultat'  => 'alert-danger',
            'message'   => 'Une erreur est survenue lors de la récupération de l\'id du compte'
        ];
        $this->render('user/listuser', $data);
        }
    }

    public function updateroleAction(){
        if( $_POST['role'] == 'ADMIN' || $_POST['role'] == 'PERSONNEL'){
            if(isset($_SESSION['idUserMaj'])){
                $data = [
                    'idUser' => $_SESSION['idUserMaj'],
                    'role'   =>$_POST['role']
                ];
                $state = $this->userManager->updateRole( $data );
                if($state){
                    $data = [
                        'resultat' => 'alert-success',
                        'message'  => 'Le role du compte à bien était modifié'
                    ];
                    $this->render('user/listuser', $data);
                }else{
                    $data['resultat'] = 'alert-danger';
                    $data['message']  = 'Une erreur c\'est produite lors de la modification du role';
                    $this->render('user/updateuser', $data);
                }
            }    
        }else{
            $data['resultat'] = 'alert-danger';
            $data['message']  = 'Le rôle saisie est incorrect, veuillez réessayer';
            $this->updateuserAction( $data );
        }
    }

    public function desactiveuserAction(){
        $state = $this->userManager->desactiveUser( $this->vars['id'] );
        if( !$state ){
            $data = [
                'resultat' => 'alert-danger',
                'message' => 'Une erreur c\'est produite lors de la cloturation du compte'
            ];
            $this->render('user/updateuser', $data);
        }else{
            $data = [
                'resultat' => 'alert-success',
                'message' => 'Le compte a bien était rendu inactif'
            ];
            $this->render('user/listuser', $data);
        }
    }

    public function activeuserAction(){
        $state = $this->userManager->activeUser( $this->vars['id'] );
        if( !$state ){
            $data = [
                'resultat' => 'alert-danger',
                'message' => 'Une erreur c\'est produite lors de la cloturation du compte'
            ];
            $this->render('user/updateuser', $data);
        }else{
            $data = [
                'resultat' => 'alert-success',
                'message' => 'Le compte a bien était rendu inactif'
            ];
            $this->render('user/listuser', $data);
        }
    }

    public function demandeuserAction(){
        $data = [];
        $this->render('user/demandeuser', $data);
    }

    public function demandeuserBSAction(){
        if(isset($_SESSION['idUser'])){
            $searchParams = [
                'search'		=> $this->vars['search'],
                'sort'			=> $this->vars['sort'],
                'order'			=> $this->vars['order'],
                'offset'		=> $this->vars['offset'],
                'limit'			=> $this->vars['limit'],
                'searchable'	=> $this->vars['searchable'],
            ];
            
            $listDemandeUser = $this->userManager->listDemandeUser( $searchParams );
            $searchParams['offset'] = "";
            $searchParams['limit'] = "";
            $nbDemandeUser = count($this->userManager->listDemandeUser($searchParams));
    
            $dataBs = [];
            foreach( $listDemandeUser as $user ) {
                $dataBs[] = [
                    'UT_id'             => $user->getUT_id(),
                    'UT_prenom'         => $user->getUT_prenom(),
                    'UT_nom'            => $user->getUT_nom(),
                    'UT_login'          => $user->getUT_login(),
                    'UT_dateCrea'       => $user->getUT_dateCrea(),
                    'UT_actif'          => $user->getUT_actif() ? "Actif" : "Inactif",
                    'UT_role'           => $user->getUT_role()
                ];
            }
    
            $data = [
                "rows"      => $dataBs,
                "total"     => $nbDemandeUser
            ];
            $jsData = json_encode( $data );
            echo $jsData;
        }else{
            $data=[];
            $this->render('user/demandeuser', $data );
        }
    }

    public function validdemandeuserAction(){
        if(isset($this->vars['id']) && isset($this->vars['login'])){
            $state = $this->userManager->activeUser($this->vars['id']);
            if($state){
                $state = $this->clientManager->desactiveClientByLogin($this->vars['login']);
                if($state){
                    $data = [
                        'resultat' => 'alert-success',
                        'message'  => 'La demande a bien était accepté'
                    ];
                    $this->render('user/demandeuser', $data);
                }else{
                    $data = [
                        'resultat' => 'alert-danger',
                        'message'  => 'Une erreur est survenue lors de la désactivation du compte client'
                    ];
                    $this->render('user/demandeuser', $data);
                }
            }else{
                $data = [
                    'resultat' => 'alert-danger',
                    'message'  => 'Une erreur est survenue lors de l\'activation du compte utilisateur'
                ];
                $this->render('user/demandeuser', $data);
            }
        }
    }

}
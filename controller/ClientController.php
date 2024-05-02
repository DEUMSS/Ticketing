<?php

namespace ticketing\controller;

use ticketing\model\ClientManager;
use ticketing\model\Client;
use ticketing\model\UserManager;
use ticketing\model\User;

class ClientController extends Controller
{
    protected $clientManager;
    protected $userManager;


    public function __construct( array $params=[] )
    {
        $this->clientManager = new ClientManager();
        $this->userManager = new UserManager();
        parent::__construct( $params );
    }

    public function defaultAction()
    {
        $this->connectionAction();
    }

    public function updateclientAction(){
        if(isset($this->vars['id'])){
            $client = $this->clientManager->getClientById( $this->vars['id']);
            if(isset($client)){
                $isClientUser = $this->userManager->isAccountUsed( $client->getCI_login() );
                if(empty($isClientUser)){
                    $data['clientIsUser'] = false;
                }else{
                    $data['clientIsUser'] = true;
                }
                $data['client'] = $client;
                $this->render('client/updateclient', $data );
            }else{
                $data = [
                'resultat'  => 'alert-danger',
                'message'   => 'Une erreur est survenue lors de la récupération des infomations du compte'
            ];
            $this->render('client/listclient', $data);    
            }
        }else{
            $data = [
            'resultat'  => 'alert-danger',
            'message'   => 'Une erreur est survenue lors de la récupération de l\'id du compte'
        ];
        $this->render('client/listclient', $data);
        }
    }

    public function connectionAction(){
        $data=[];
        $this->render('client/Connectclient', $data);
    }

    public function connectclientAction(){
        $login = filter_var($_POST['login']);
        $password = filter_var($_POST['password']);
        $this->userManager = new UserManager();
        $userAccountData = $this->userManager->isAccountUsed( $login );
        if( !empty($userAccountData) ){
            $userAccount = new User( $userAccountData );
            $isAccountActiv =  $userAccount->getUT_actif();
            if( $isAccountActiv == false ){
                $loginUsed = $this->clientManager->isLoginUsed( $login );
                if ( empty($loginUsed) ){
                    $data = [
                        'message' => 'Votre login est incorrect, veuillez réessayer'
                    ];
                    $this->render('client/Connectclient', $data);
                    exit;
                }
                $connectedClient = $this->clientManager->getClient( $login );
                $isClientActiv = $connectedClient->getCI_actif();
                if( $isClientActiv ){
                    if(sodium_crypto_pwhash_str_verify( $connectedClient->getCI_password(), $password ) ) {
                        $_SESSION['idClient'] = $connectedClient->getCI_id();
                        header('Location:' . $this->pathRoot . 'Ticket/list');
                    } else {
                        $data = [
                            'message' => 'Votre mot de passe est incorrect, veuillez réessayer'
                        ];
                        $this->render('client/Connectclient', $data);
                    }
                }else{
                    $data = [
                        'message' => 'Le compte auquel vous essayez de vous connecter n\'est plus actif'
                    ];
                    $this->render('client/Connectclient', $data);
                    exit;
                }
            }else{
                $connectedUser = $this->userManager->getUser( $login );
                $roleUser = $connectedUser->getUT_role();
                if($roleUser == "ADMIN"){
                    $_SESSION['roleUser'] = $roleUser;
                }
                if(sodium_crypto_pwhash_str_verify( $connectedUser->getUT_password(), $password ) ) {
                    $_SESSION['idUser'] = $connectedUser->getUT_id();
                    header('Location:' . $this->pathRoot . 'Ticket/list');
                } else {
                    $data = [
                        'message' => 'Votre mot de passe est incorrect, veuillez réessayer'
                    ];
                    $this->render('client/Connectclient', $data);
                }
            }
        }else{
            $loginUsed = $this->clientManager->isLoginUsed( $login );
            if ( empty($loginUsed) ){
                $data = [
                    'message' => 'Votre login est incorrect, veuillez réessayer'
                ];
                $this->render('client/Connectclient', $data);
                exit;
            }
            $connectedClient = $this->clientManager->getClient( $login );
            $isClientActiv = $connectedClient->getCI_actif();
            if( $isClientActiv ){
                if(sodium_crypto_pwhash_str_verify( $connectedClient->getCI_password(), $password ) ) {
                    $_SESSION['idClient'] = $connectedClient->getCI_id();
                    header('Location:' . $this->pathRoot . 'Ticket/list');
                } else {
                    $data = [
                        'message' => 'Votre mot de passe est incorrect, veuillez réessayer'
                    ];
                    $this->render('client/Connectclient', $data);
                }
            }else{
                $data = [
                    'message' => 'Le compte auquel vous essayez de vous connecter n\'est plus actif'
                ];
                $this->render('client/Connectclient', $data);
            }
        }
    }


    public function createclientAction() {
        $data=[];
        $this->render('client/Createclient', $data);
    }

    public function validclientAction(){
        if(strlen($_POST['password'])< 8){
            $data=[
                'message' => 'Votre mot de passe doit contenir 8 caractères au minimum'
            ];
        $this->render( 'client/Createclient', $data);
        exit;
        }elseif(!preg_match('/[A-Z]/', $_POST['password'])){
            $data=[
                'message' => 'Votre mot de passe doit contenir au moins une majuscule'
            ];
        $this->render( 'client/Createclient', $data);
        exit;
        }elseif(!preg_match('/\d/', $_POST['password'])){
            $data=[
                'message' => 'Votre mot de passe doit contenir au moins un chiffre'
            ];
        $this->render( 'client/Createclient', $data);
        exit;
        }elseif(!preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $_POST['password'])){
            $data=[
                'message' => 'Votre mot de passe doit contenir au moins un caractère spécial'
            ];
        $this->render( 'client/Createclient', $data);
        exit;
        }
        $passHash = sodium_crypto_pwhash_str(
            $_POST['password'],
            SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE,
            SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE
        );
        $data = [
            'CI_prenom'        => $_POST['prenom'],
            'CI_nom'           => $_POST['nom'],
            'CI_entreprise'    => $_POST['entreprise'],
            'CI_login'         => $_POST['login'],
            'CI_password'      => $passHash,
            'CI_dateCrea'      => date("Y-m-d")
        ];
        $newClient = new Client( $data );
        $loginUsed = $this->clientManager->isLoginUsed($newClient->getCI_login());
        if( !empty($loginUsed) ){
            $data=[
                'message'  => 'Le pseudo que vous avez choisi est déjà utilisé, veuillez en saisir un autre'
            ];
            $this->render( 'client/Createclient', $data );
            exit;
        }
        $state = $this->clientManager->addClient($newClient);
        if( $state ){
            $_SESSION['idClient'] = $newClient->getCI_Id();
            header('Location:' . $this->pathRoot);
        }else{
            $data = [
                'message'   => 'Une erreur c\'est produite lors de la création de votre compte'
            ];
            $this->render( 'client/Createclient', $data );
        }
    }
    

    public function listclientAction(){
        $data = [];
        $this->render('client/listclient', $data);
    }

    public function listclientBSAction (){
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
            
            $listClient = $this->clientManager->listClient( $searchParams );
            $searchParams['offset'] = "";
            $searchParams['limit'] = "";
            $nbClient = count($this->clientManager->listClient($searchParams));
    
            $dataBs = [];
            foreach( $listClient as $client ) {
                $dataBs[] = [
                    'CI_id'             => $client->getCI_id(),
                    'CI_prenom'         => $client->getCI_prenom(),
                    'CI_nom'            => $client->getCI_nom(),
                    'CI_login'          => $client->getCI_login(),
                    'CI_dateCrea'        => $client->getCI_dateCrea(),
                    'CI_actif'          => $client->getCI_actif() ? "Ouvert" : "Fermé"
                ];
            }
    
            $data = [
                "rows"      => $dataBs,
                "total"     => $nbClient
            ];
            $jsData = json_encode( $data );
            echo $jsData;
        }else{
            $data=[];
            $this->render('ticket/listclient', $data );
        }
    }

    public function logoutAction(){
        session_destroy();
        header('Location:' . $this->pathRoot);
        exit;
    }

    public function desactiveclientAction(){
        $state = $this->clientManager->desactiveClient( $this->vars['id'] );
        if( !$state ){
            $data = [
                'resultat' => 'alert-danger',
                'message' => 'Une erreur c\'est produite lors de la cloturation du compte'
            ];
            $this->render('client/updateclient', $data);
        }else{
            $data = [
                'resultat' => 'alert-success',
                'message' => 'Le compte a bien était rendu inactif'
            ];
            $this->render('client/listclient', $data);
        }
    }

    public function activeclientAction(){
        $state = $this->clientManager->activeClient( $this->vars['id'] );
        if( !$state ){
            $data = [
                'resultat' => 'alert-danger',
                'message' => 'Une erreur c\'est produite lors de la réouverture du compte'
            ];
            $this->render('client/updateclient', $data);
        }else{
            $data = [
                'resultat' => 'alert-success',
                'message' => 'Le compte a bien était réouvert'
            ];
            $this->render('client/listclient', $data);
        }
    }

}







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
        parent::__construct( $params );
    }



    public function defaultAction()
    {
        $this->connectionAction();
    }


    
    public function clientAction()
    {    }


    public function updateclientAction() {
        
    }


	public function deleteclientAction()
	{
		if( isset( $this->vars['id'] ) ) {
			if( $this->clientManager->deleteClient( $this->vars['id'] ) ) {
				return $this->redirectToRoute( 'client/listclient', ['message'=>'Utilisateur effacé'] );
			}
		}
	}

    public function connectionAction(){
        $data=[];
        $this->render('client/Connectclient', $data);
    }

    public function connectclientAction(){
        $this->userManager = new UserManager();
        $userAccountData = $this->userManager->isAccountUsed( $_POST['login'] );
        $userAccount = new User( $userAccountData );
        if( !empty($userAccountData) ){
           $isAccountActiv =  $userAccount->getUT_actif();
            if( $isAccountActiv == false ){
                $loginUsed = $this->clientManager->isLoginUsed( $_POST['login'] );
                if ( empty($loginUsed) ){
                    $data = [
                        'message' => 'Votre login est incorrect, veuillez réessayer'
                    ];
                    $this->render('client/Connectclient', $data);
                    exit;
                }
                $connectedClient = $this->clientManager->getClient( $_POST['login'] );
                $isClientActiv = $connectedClient->getCI_actif();
                if( $isClientActiv ){
                    if(sodium_crypto_pwhash_str_verify( $connectedClient->getCI_password(), $_POST['password'] ) ) {
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
                $connectedUser = $this->userManager->getUser( $_POST['login'] );
                if(sodium_crypto_pwhash_str_verify( $connectedUser->getUT_password(), $_POST['password'] ) ) {
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
            $loginUsed = $this->clientManager->isLoginUsed( $_POST['login'] );
            if ( empty($loginUsed) ){
                $data = [
                    'message' => 'Votre login est incorrect, veuillez réessayer'
                ];
                $this->render('client/Connectclient', $data);
                exit;
            }
            $connectedClient = $this->clientManager->getClient( $_POST['login'] );
            $isClientActiv = $connectedClient->getCI_actif();
            if( $isClientActiv ){
                if(sodium_crypto_pwhash_str_verify( $connectedClient->getCI_password(), $_POST['password'] ) ) {
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
                'resultat'  => false,
                'message'   => 'Une erreur c\'est produite lors de la création de votre compte'
            ];
            $this->render( 'Index', $data );
        }
    }
    

    public function listclientAction()
    {
        $nbUsers = $this->clientManager->countAll() ?? 0;
        $listUsers = $this->clientManager->getAllClient([]);
        $data = [
            "nbUsers"       => $nbUsers,
            "listUsers"     => $listUsers,
            "titre"		    => "Page utilisateur.",
			"vars"			=> $this->vars
        ];
        $this->render( 'client/listclient', $data );
    }

    public function logoutAction(){
        session_destroy();
        header('Location:' . $this->pathRoot);
        exit;
    }
}







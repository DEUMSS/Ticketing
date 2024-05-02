<?php

namespace ticketing\controller;
;
use ticketing\model\TicketManager;
use ticketing\model\Ticket;
use ticketing\model\TraitementManager;

class TicketController extends Controller
{
    protected $ticketManager;
    protected $traitementManager;

    public function __construct( array $params=[] )
    {
        $this->ticketManager = new TicketManager();
        $this->traitementManager = new TraitementManager;
        parent::__construct( $params );
    }

    public function defaultAction()
    {
        $this->listticketAction();
    }

    public function listAction(){
        $data=[];
        $this->render('ticket/Listticket', $data);
    }

    public function listticketAction(){
        $data=[];
        $this->render('ticket/listticket');
    }

    public function listticketBSAction (){
        if(isset($_SESSION['idUser']) || isset($_SESSION['idClient'])){
            $searchParams = [
                'search'		=> $this->vars['search'],
                'sort'			=> $this->vars['sort'],
                'order'			=> $this->vars['order'],
                'offset'		=> $this->vars['offset'],
                'limit'			=> $this->vars['limit'],
                'searchable'	=> $this->vars['searchable'],
            ];

            if(isset($_SESSION['roleUser'])){
                $searchParams['ferme'] = $this->vars['ferme'] == 1;
                $searchParams['ouvert'] = $this->vars['ouvert'] == 1;
            }
            
            $listTicket = $this->ticketManager->listTicket( $searchParams );
            $searchParams['offset'] = "";
            $searchParams['limit'] = "";
            $nbTickets = count($this->ticketManager->listTicket($searchParams));
    
            $dataBs = [];
            foreach( $listTicket as $ticket ) {
                $dataBs[] = [
                    'TI_id'             => $ticket->getTI_id(),
                    'TI_idTypeDemande'  => mb_convert_encoding($this->ticketManager->getTypeDemandeById($ticket->getTI_idTypeDemande()), 'UTF-8'),
                    'TI_idPriorite'     => $this->ticketManager->getPrioriteById($ticket->getTI_idPriorite()),
                    'TI_sujet'          => $ticket->getTI_sujet(),
                    'TI_dateCrea'       => $ticket->getTI_dateCrea(),
                    'TI_dateMAJ'        => $ticket->getTI_dateMAJ(),
                    'TI_actif'          => $ticket->getTI_actif() ? "Ouvert" : "Fermé",
                ];
            }
    
            $data = [
                "rows"      => $dataBs,
                "total"     => $nbTickets
            ];
            $jsData = json_encode( $data );
            echo $jsData;
        }else{
            $data=[];
            $this->render('ticket/listticket', $data );
        }
    }
    
    public function createticketAction(){
        $data = [
            'TI_idClient'       => $_SESSION['idClient'],
            'TI_idTypeDemande'  => $_POST['typeDemande'],
            'TI_idPriorite'     => $_POST['priorite'],
            'TI_sujet'          => $_POST['sujet'],
            'TI_message'        => $_POST['message']
        ];
        $newTicket = new Ticket($data);
        $state = $this->ticketManager->createTicket( $newTicket );
        if( $state ){
            $data = [
                'resultat'  => 'alert-success',
                'message'   => 'Votre ticket à bien était créé !'
            ];
            $this->render('ticket/listticket', $data);
        }else{
            $data = [
                'resultat'  => 'alert-danger',
                'message'   => 'Une erreur c\'est produite lors de la création de votre ticket'
            ];
            $this->render('ticket/createticket', $data);
        }
    }
    

    public function listparamAction(){
        $data['listTypeDemande'] = $this->ticketManager->getTypeDemande();
        $data['listPriorite'] = $this->ticketManager->getPriorite();
        $this->render('ticket/createticket', $data);
    }

    public function updateticketAction(){
        if(isset($this->vars['id'])){
            $_SESSION['idTicket'] = $this->vars['id'];
            $ticket = $this->ticketManager->getTicketById( $this->vars['id']);
            if(isset($ticket)){
                $traitements = $this->traitementManager->getMessageByIdTicket( $ticket->getTI_id());
                $typeDemande = $this->ticketManager->getTypeDemandeById($ticket->getTI_idTypeDemande());
                $priorite = $this->ticketManager->getPrioriteById($ticket->getTI_idPriorite());
                $data['ticket'] = $ticket;
                $data['typeDemande'] = $typeDemande;
                $data['priorite'] = $priorite;
                $data['traitements'] = $traitements;
                $this->render('ticket/updateticket', $data );
            }else{
                $data = [
                'resultat'  => 'alert-danger',
                'message'   => 'Une erreur est survenue lors de la récupération de votre ticket'
            ];
            $this->render('ticket/listticket', $data);    
            }
        }else{
            $data = [
            'resultat'  => 'alert-danger',
            'message'   => 'Une erreur est survenue lors de la récupération de l\'id du ticket'
        ];
        $this->render('ticket/listticket', $data);
        }
    }

    public function sendmessageAction(){
        $data = [
            'idTicket' => $_SESSION['idTicket'],
            'message' => $_POST['newMessage'],
            'dateMAJ' => date('Y-m-d H:i:s')
        ];
        $state1 = $this->traitementManager->createTraitement($data);
        $state2 = $this->ticketManager->updateDateMAJTicket($data);
        if($state1 && $state2){
            $data = [
                'resultat' => 'alert-success',
                'message' => 'Votre réponse à était enregistrée'
            ];
            $this->render('ticket/listticket', $data);
        }else{
            $data = [
                'resultat' => 'alert-danger',
                'message' => 'Une erreur c\'est produite lors de l\'enregistrement de votre réponse'
            ];
            $this->render('ticket/listticket', $data);
        }
    }

    public function fermetureticketAction(){
        $state = $this->ticketManager->fermetureTicket( $this->vars['id'] );
        if( !$state ){
            $data = [
                'resultat' => 'alert-danger',
                'message' => 'Une erreur c\'est produite lors de la fermeture de votre ticket'
            ];
            $this->render('ticket/Updateticket', $data);
        }else{
            $data = [
                'resultat' => 'alert-success',
                'message' => 'Votre ticket a bien était fermé'
            ];
            $this->render('ticket/listticket', $data);
        }
    }

    public function ouvertureticketAction(){
        $state = $this->ticketManager->ouvertureTicket( $this->vars['id'] );
        if( !$state ){
            $data = [
                'resultat' => 'alert-danger',
                'message' => 'Une erreur c\'est produite lors de l\'ouverture de votre ticket'
            ];
            $this->render('ticket/Updateticket', $data);
        }else{
            $data = [
                'resultat' => 'alert-success',
                'message' => 'Votre ticket a bien était ouvert'
            ];
            $this->render('ticket/listticket', $data);
        }
    }
}

?>
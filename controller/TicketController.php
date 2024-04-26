<?php

namespace ticketing\controller;
;
use ticketing\model\TicketManager;
use ticketing\model\Ticket;

class TicketController extends Controller
{
    protected $ticketManager;

    public function __construct( array $params=[] )
    {
        $this->ticketManager = new TicketManager();
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
                'resultat'  => true,
                'message'   => 'Votre ticket à bien était créé !'
            ];
            header('Location:' . $this->pathRoot);
        }else{
            $data = [
                'resultat'  => false,
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


}

?>
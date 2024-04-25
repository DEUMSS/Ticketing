<?php

namespace Ticketing\controller;

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
}

?>
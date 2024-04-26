<?php

namespace ticketing\controller;

/**
 * Default base class for compiled templates.
 *
 * @author Fred Fraticelli
 *
 */
class IndexController extends Controller
{

    public function __construct( array $params=[] )
    {
        parent::__construct( $params );
    }


    /**
     *  Default action, called if no action is detected
     */
    public function defaultAction()
    {
        $data=[];
        $this->render( 'index', $data );
    }



    /**
     *  Destroy session vars & redirect to home
     */
    public function logoutAction()
    {
        session_destroy();
        header('Location: .');
        exit();
    }

}







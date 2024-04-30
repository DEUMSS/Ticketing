<?php

namespace ticketing\model;

use \DateTimeImmutable;
use \ReflectionMethod;

class Traitement{
    protected $TM_id;
    protected $TM_idTicket;
    protected $TM_message;

    public function __construct( array $data )
    {
        $this->hydrate( $data );
    }

    public function hydrate( array $data )
    {
        foreach ( $data as $key=>$value ) {
            $method = 'set' . ucfirst($key);
            if( method_exists( $this, $method ) ) {
                $reflectionMethod = new ReflectionMethod($this, $method);
                $parameters = $reflectionMethod->getParameters();
                if (!empty($parameters)) {
                    $parameterType = $parameters[0]->getType();
                    if ($parameterType && $parameterType->getName() === 'DateTimeImmutable') {
                        $value = new DateTimeImmutable($value);
                    }
                }
                $this->$method($value);
            }
        }
    }

    public function setTM_id( $id ){
        $this->TM_id = $id;
    }

    public function setTM_idTicket( $idTicket ){
        $this->TM_idTicket = $idTicket;
    }
    
    public function setTM_message( $message ){
        $this->TM_message = $message;
    }

    public function getTM_id(){
        return $this->TM_id;
    }

    public function getTM_idTicket(){
        return $this->TM_idTicket;
    }

    public function getTM_messagee(){
        return $this->TM_message;
    }
}
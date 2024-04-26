<?php

namespace ticketing\model;

use \DateTimeImmutable;
use \ReflectionMethod;

class Ticket{

    protected $TI_id;
    protected $TI_idClient;
    protected $TI_idTypeDemande;
    protected $TI_idPriorite;
    protected $TI_sujet;
    protected $TI_message;
    protected $TI_actif;
    protected $TI_dateCrea;
    protected $TI_dateMAJ;

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


    public function getTI_id(){
        return $this->TI_id;
    }
    public function getTI_idClient(){
        return $this->TI_idClient;
    }
    public function getTI_idTypeDemande(){
        return $this->TI_idTypeDemande;
    }
    public function getTI_idPriorite(){
        return $this->TI_idPriorite;
    }
    public function getTI_sujet(){
        return $this->TI_sujet;
    }
    public function getTI_messagee(){
        return $this->TI_message;
    }
    public function getTI_actif(){
        return $this->TI_actif;
    }
    public function getTI_dateCrea(): ?\DateTimeImmutable
    {
        return $this->TI_dateCrea;
    }
    public function getTI_dateMAJ(): ?\DateTimeImmutable
    {
        return $this->TI_dateMAJ;
    }

    public function setTI_id( int $id ){
        $this->TI_id = $id;
    }
    public function setTI_idClient( int $idClient ){
        $this->TI_idClient = $idClient;
    }
    public function setTI_idTypeDemande( int $idTypeDemande ){
        $this->TI_idTypeDemande = $idTypeDemande;
    }
    public function setTI_idPriorite( int $idPriorite ){
        $this->TI_idPriorite = $idPriorite;
    }
    public function setTI_sujet( string $sujet ){
        $this->TI_sujet = $sujet;
    }
    public function setTI_message( string $message ){
        $this->TI_message = $message;
    }
    public function setTI_actif( bool $etat ){
        $this->TI_actif = $etat;
    }
    public function setTI_dateCrea(?\DateTimeImmutable $dateTime){
        $this->TI_dateCrea = $dateTime;
    }
    public function setTI_dateMAJ(?\DateTimeImmutable $dateMAJ){
        $this->TI_dateMAJ = $dateMAJ;
    }
}
?>
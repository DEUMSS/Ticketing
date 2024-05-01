<?php
/**
 * Entity  Users Table
 */

namespace ticketing\model;

use \DateTimeImmutable;
use \ReflectionMethod;

class Client
{
    protected $CI_id;
    protected $CI_login;
    protected $CI_password;
    protected $CI_actif = false;
    protected $CI_nom;
    protected $CI_prenom;
    protected $CI_entreprise;
	protected $CI_dateCrea = null;


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


    // Setters
    public function setCI_id( $id )
    {
        $this->CI_id = $id;
    }
    public function setCI_login( $login )
    {
        $this->CI_login = $login;
    }
    public function setCI_password( $mdp )
    {
        $this->CI_password = $mdp;
    }
    public function setCI_actif( $isactive )
    {
        $this->CI_actif = $isactive;
    }
    public function setCI_nom( $nom )
    {
        $this->CI_nom = $nom;
    }
    public function setCI_prenom( $prenom )
    {
        $this->CI_prenom = $prenom;
    }
	public function setCI_dateCrea( ?\DateTimeImmutable $dateCrea )
	{
		$this->CI_dateCrea = $dateCrea;
	}
    public function setCI_entreprise( $entreprise ){
        $this->CI_entreprise = $entreprise;
    }


    // Getters
    public function getCI_id()
    {
        return $this->CI_id;
    }
    public function getCI_login()
    {
        return $this->CI_login;
    }
    public function getCI_password()
    {
        return $this->CI_password;
    }
    public function getCI_actif()
    {
        return $this->CI_actif;
    }
    public function getCI_nom()
    {
        return $this->CI_nom;
    }
    public function getCI_prenom()
    {
        return $this->CI_prenom;
    }
    public function getCI_dateCrea()
	{
		return $this->CI_dateCrea->format('d/m/Y');
	}
    public function getCI_entreprise(){
        return $this->CI_entreprise;
    }

}
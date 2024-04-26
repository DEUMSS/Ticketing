<?php

namespace ticketing\model;

use \DateTimeImmutable;
use \ReflectionMethod;

class User
{
    protected $UT_id;
    protected $UT_login;
    protected $UT_password;
    protected $UT_nom;
    protected $UT_prenom;
    protected $UT_role;
    protected $UT_actif = false;
	protected $UT_dateCrea = null;


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
    public function setUT_id( $id )
    {
        $this->UT_id = $id;
    }
    public function setUT_login( $login )
    {
        $this->UT_login = $login;
    }
    public function setUT_password( $mdp )
    {
        $this->UT_password = $mdp;
    }
    public function setUT_actif( $isactive )
    {
        $this->UT_actif = $isactive;
    }
    public function setUT_nom( $nom )
    {
        $this->UT_nom = $nom;
    }
    public function setUT_prenom( $prenom )
    {
        $this->UT_prenom = $prenom;
    }
	public function setUT_dateCrea( ?\DateTimeImmutable $dateCrea )
	{
		$this->UT_dateCrea = $dateCrea;
	}
    public function setUT_role( $role ){
        $this->UT_role = $role;
    }



    // Getters
    public function getUT_id()
    {
        return $this->UT_id;
    }
    public function getUT_login()
    {
        return $this->UT_login;
    }
    public function getUT_password()
    {
        return $this->UT_password;
    }
    public function getUT_actif()
    {
        return $this->UT_actif;
    }
    public function getUT_nom()
    {
        return $this->UT_nom;
    }
    public function getUT_prenom()
    {
        return $this->UT_prenom;
    }
    public function getUT_dateCrea(): ?\DateTimeImmutable
	{
		return $this->UT_dateCrea;
	}
    public function getUT_role(){
        return $this->UT_role;
    }

}
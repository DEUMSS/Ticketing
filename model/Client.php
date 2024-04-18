<?php
/**
 * Entity  Users Table
 */

namespace ticketing\model;

use \DateTimeImmutable;
use \ReflectionMethod;

class Client
{
    protected int $CI_id;
    protected string $CI_login;
    protected string $CI_mdp;
    protected bool $CI_actif = false;
    protected string $CI_nom;
    protected string $CI_prenom;
    protected string $CI_entreprise;
	protected ?\DateTimeImmutable $CI_dateCrea = null;


    public function __construct( array $data )
    {
        $this->hydrate( $data );
    }


    /**
     * Fill each property with the values present in $data
     *
     * @param array $data
     */
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
    public function setId( $id )
    {
        $this->CI_id = $id;
    }
    public function setLogin( $login )
    {
        $this->CI_login = $login;
    }
    public function setPassword( $mdp )
    {
        $this->CI_mdp = $mdp;
    }
    public function setActif( $isactive )
    {
        $this->CI_actif = $isactive;
    }
    public function setNom( $nom )
    {
        $this->CI_nom = $nom;
    }
    public function setPrenom( $prenom )
    {
        $this->CI_prenom = $prenom;
    }
	public function setDateCrea( ?\DateTimeImmutable $dateCrea )
	{
		$this->CI_dateCrea = $dateCrea;
	}
    public function setEntreprise( $entreprise ){
        $this->CI_entreprise = $entreprise;
    }


    // Getters
    public function getId()
    {
        return $this->CI_id;
    }
    public function getLogin()
    {
        return $this->CI_login;
    }
    public function getPassword()
    {
        return $this->CI_mdp;
    }
    public function getActif()
    {
        return $this->CI_actif;
    }
    public function getNom()
    {
        return $this->CI_nom;
    }
    public function getPrenom()
    {
        return $this->CI_prenom;
    }
    public function getDateCrea(): ?\DateTimeImmutable
	{
		return $this->CI_dateCrea;
	}
    public function getEntreprise(){
        return $this->CI_entreprise;
    }

}
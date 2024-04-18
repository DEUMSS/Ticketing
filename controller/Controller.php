<?php

namespace ticketing\controller;

use Twig\Loader\FilesystemLoader;
use Twig\Environment;
use Twig\Extension\DebugExtension;

abstract class Controller
{
    protected $twig;
    protected $pathView = 'view';
    protected $pathRoot;

    protected $vars = [];
    protected $action = false;
    protected $isFetched = false;



    public function __construct( array $params=[])
    {
        $this->setParams( $params );

        $this->pathRoot = str_replace( $_SERVER['REDIRECT_QUERY_STRING'], '', $_SERVER['REDIRECT_URL'] );

        $this->pathView = dirname( __DIR__ ) . DIRECTORY_SEPARATOR . $this->pathView;

        // Twig initialization
        $loader = new FilesystemLoader( $this->pathView );

        $this->twig = new Environment($loader, [
            'debug' => true
        ]);
        $this->twig->addGlobal('pathRoot', $this->pathRoot );
        $this->twig->addExtension(new DebugExtension());

        // Call action
        if( $this->action ) {
            $action = $this->action . 'Action';
            $this->$action();
        } else {
            $this->defaultAction();
        }
    }



    abstract public function defaultAction();


    /**
     *  Get request parameters in $this->vars property
     *
     * @param array $params
     */
    protected function setParams( array $params=[] )
    {
        if( isset( $params['action'] ) ) {
            $this->action = $params['action'];
        }
        if( isset( $params['vars'] ) && is_array( $params['vars'] ) ) {
            $nb = count($params['vars']);
            if ($nb > 1) {
                $i = 0;
                while ($i < $nb) {
                    $this->vars[$params['vars'][$i]] = isset( $params['vars'][$i+1] ) ? $params['vars'][$i+1] : '';
                    $i += 2;
                }
            }
            if( !empty( $params['redirect'] ) ) {
                $params['request'] = unserialize( base64_decode( $params['redirect'] ) );
            }
            if( !empty( $params['request'] ) ) {
                foreach ( $params['request'] as $k=>$v ) {
                    $this->vars[$k] = $v;
                }
            }
            $this->isFetched = $params['isFetched'];
        }
    }

    

    protected function redirectToRoute( string $route, array $data=[] )
    {
        $dataSerialize = base64_encode( serialize( $data ) );
		$_SESSION['redirect'] = $dataSerialize;
        header( 'Location: http://localhost:84' . $this->pathRoot . $route );
		return true;
    }



    protected function render( $view, $data=[] )
    {
        extract( $data );
        $tmp = explode('/', $view);
        $view = array_pop( $tmp );
        $fileNameView = ucfirst( $view ) . 'View.twig';
        $fileNameView = implode('/', $tmp ) . '/' . $fileNameView;

        $filePath = $this->pathView . '/' . $fileNameView;
        if( file_exists( $filePath ) ) {
            echo $this->twig->render( $fileNameView, $data );
        } else {
            die( "Twig view file not exist in view folder." );
        }
    }

}
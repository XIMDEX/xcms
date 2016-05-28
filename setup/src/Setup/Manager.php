<?php

namespace Ximdex\Setup;


use Twig_Environment;
use Twig_Loader_Filesystem;
use Ximdex\Setup\Step\Base;

class Manager
{
    private $basePath = null ;
    /**
     * @var null|Twig_Environment
     */
    private $template = null ;
    /**
     * @var array Ximdex\Setup\Step\Base
     */
    private $steps = [] ;
    private $currentStep = 0 ;
    private $installRoot = "";

    public function __construct( $basePath )
    {
        $this->basePath = $basePath;

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $loader = new Twig_Loader_Filesystem($this->basePath . '/data/templates/');
        $this->template = new Twig_Environment($loader, ['debug' => true]);

        // check step
        if ( !isset( $_SESSION['currentStep'])) {
            $_SESSION['currentStep'] = 0;
        }
        $this->currentStep = (int) isset( $_GET['step']) ? $_GET['step'] : 0 ;


        $this->installRoot = dirname( $basePath );




    }
    public function getFullPath( $path  ) {
        return $this->basePath . $path ;
    }
    public function getRootPath( $path  ) {
        return $this->installRoot . $path ;
    }
    public function getInstallRoot( ) {
        return $this->installRoot ;
    }
    public function addStep( Base $step ) {
        $this->steps[] = $step ;
    }
    public function run() {
        $this->getCurrentStep()->run( $this->currentStep );
    }

    /**
     * @return Base
     */
    public function getCurrentStep( ) {


        return $this->steps[ $this->currentStep ] ;
    }

    /**
     * @return array|null
     */
    public function getNextStepLink( ) {
        $result = null;
        $next = $this->currentStep + 1 ;
        if ( isset( $this->steps[ $next ])) {
            $result = [
                'href' => "?step={$next}",
                'label' => $this->steps[$next]->getLabel(),
                ];
        }
        return $result ;
    }
    /**
     * @return array|null
     */
    public function getCurrentStepLink( ) {
        $result = null;

            $result = [
                'href' => "?step={$this->currentStep}",
                'label' => $this->steps[$this->currentStep]->getLabel(),
            ];
        return $result ;
    }
    /**
     * @return array
     */
    public function getCommonVars() {
        $vars = [];
        $vars['steps'] = [];
        foreach(  $this->steps as  $key => $step ){
            /**
             * @var $step Base
             */
            $vars['steps'][] = [
                'label' => $step->getLabel() ,
                'key' => $key,
                'active' => ($key === $this->currentStep ) ,
            ] ;
        }
        $vars['currentStep'] = $this->currentStep ;
        $vars['nextStepLink'] = $this->getNextStepLink();
        $vars['currentStepLink'] = $this->getCurrentStepLink();


        return $vars ;

    }

    /**
     * @param $template
     * @param $vars array
     * @return string
     */
    public function render( $template, $vars ) {
        $vieVars = array_merge( $this->getCommonVars(), $vars );
        return  $this->template->render($template , $vieVars );
    }
}
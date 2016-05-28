<?php


namespace Ximdex\Setup\Step ;


use Ximdex\Setup\Manager;

class Base
{
    protected $manager = null ;
    protected $label = "";
    protected $template = "";
    protected $title = "Ximdex Setup";
    protected $vars = [] ;
    public $errors = [] ;

    public function __construct( Manager $manager )
    {
        $this->manager = $manager ;
    }
    public function checkErrors() {

    }


    public function getErrors( ) {
        return $this->errors;
    }


    public function run(  $step ) {
        // set the step
        $_SESSION['currentStep'] = $step ;

        $this->checkErrors();
        $this->vars['errors'] = $this->errors ;


        echo  $this->manager->render( $this->template ,  $this->vars );
    }
    public function getLabel( ) {
        return $this->label ;
    }
    public function addError( $title , $message , $step ) {
        $this->errors[] = [
            'step' => $step,
            'title' => $title ,
            'message'=> $message,

        ];

    }
}
<?php
/**
 * Created by PhpStorm.
 * User: drzippie
 * Date: 28/05/16
 * Time: 11:19
 */

namespace Ximdex\Setup\Step;


use PDO;
use PDOException;
use Ximdex\Setup\Manager;

class Settings extends Base
{
    public function __construct(Manager $manager)
    {
        parent::__construct($manager);

        $this->label = "Settings";
        $this->template = "settings.twig";
        $this->title = "Administration settings";
        $this->vars['title'] = $this->title;

        $this->vars['form'] = $this->getForm();
    }

    private function getForm() {
        $result = [
            'pass' => (isset( $_POST['password'])) ? $_POST['password'] : '' ,
            'pass2' => (isset( $_POST['repeatpassword'])) ? $_POST['repeatpassword'] : '' ,

            'submitted' => !empty( $_POST ),

        ] ;
        return $result ;


    }

    public function checkErrors()
    {


        parent::checkErrors();

        if ( $this->vars['form']['submitted'] === true  ) {

           if (empty( $this->vars['form']['pass']) || $this->vars['form']['pass'] !== $this->vars['form']['pass2']) {
                   $this->addError(
                       sprintf("You must provide a valid password"),
                       sprintf("You must provide a valid password"),
                       "Settings"
                   );


           } else {
               $this->saveSettings();
           }


        }


    }

    /**
     * Methods to check
     */
    private function saveSettings( )
    {
        $form =  $_SESSION['db'] ;
        $valid = true ;
        try {
            $pdconnstring = "mysql:host={$form['dbhost']}:{$form['dbport']};dbname={$form['dbname']}" ;
            $db =  new PDO( $pdconnstring,$form['dbuser'], $form['dbpass']);
            # save session data ;
            $db->exec("UPDATE Users SET Pass=MD5('{$this->vars['form']['pass']}') where IdUser = '301'");
            file_put_contents($this->manager->getRootPath('/conf/_STATUSFILE'), "INSTALLED");



        } catch (PDOException $e) {
            $valid = false ;
        }




        if ( $valid === false  ) {
            $this->addError(
                sprintf("Unable to connect to database"),
                sprintf("Unable to connect to database. Please check settings and try again"),
                "DB"
            );

        }



    }


}
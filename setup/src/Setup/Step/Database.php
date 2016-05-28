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

class Database extends Base
{
    public function __construct(Manager $manager)
    {
        parent::__construct($manager);

        $this->label = "Database";
        $this->template = "database.twig";
        $this->title = "Database setup";
        $this->vars['title'] = $this->title;

        $this->vars['form'] = $this->getForm();
    }

    private function getForm() {
        $result = [
            'dbhost' => (isset( $_POST['dbhost'])) ? $_POST['dbhost'] : 'localhost' ,
            'dbport' => (isset( $_POST['dbport'])) ? $_POST['dbport'] : '3306' ,
            'dbuser' => (isset( $_POST['dbuser'])) ? $_POST['dbuser'] : 'ximdex' ,
            'dbpass' => (isset( $_POST['dbpass'])) ? $_POST['dbpass'] : '' ,
            'dbname' => (isset( $_POST['dbname'])) ? $_POST['dbname'] : 'ximdex' ,
            'submitted' => !empty( $_POST ),

        ] ;
        return $result ;


    }

    public function checkErrors()
    {


        parent::checkErrors();

        if ( $this->vars['form']['submitted'] === true ) {

           $this->checkDBConnection();
        }


    }

    /**
     * Methods to check
     */
    private function checkDBConnection( )
    {
        $form = $this->vars['form'];
        $valid = true ;

        try {
            $pdconnstring = "mysql:host={$form['dbhost']}:{$form['dbport']};dbname={$form['dbname']}" ;
            $db =  new PDO( $pdconnstring,$form['dbuser'], $form['dbpass']);
            # save session data ;
            $_SESSION['db'] = $form ;

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
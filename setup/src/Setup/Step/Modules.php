<?php
/**
 * Created by PhpStorm.
 * User: drzippie
 * Date: 28/05/16
 * Time: 11:19
 */

namespace Ximdex\Setup\Step;


use Ximdex\Setup\Manager;

class Modules extends Base
{
    private $modules = [
        'ximIO',
        'ximSYNC',
        'ximTAGS',
        'ximTOUR',
        'ximNEWS',
        'ximPUBLISHtools',
      //   'Xowl'

    ];

    public function __construct( Manager $manager )
    {
       parent::__construct( $manager);
        $this->label = "Modules";
        $this->template = "modules.twig" ;
        $this->title = "Install Modules" ;
        $this->vars['title'] = $this->title ;

    }

    public function checkErrors()
    {


        parent::checkErrors();
        // create modules file
        $modConfStr =  $this->manager->render( 'files/install-modules.php.twig', [ 'modules' => $this->modules ]) ;
        file_put_contents( $this->manager->getRootPath( '/conf/install-modules.php'), $modConfStr ) ;


        include_once $this->manager->getRootPath( '/bootstrap/start.php') ;
        include_once $this->manager->getRootPath( '/inc/install/managers/InstallModulesManager.class.php') ;



        foreach( $this->modules as $module ) {

            $this->installModule( $module );
        }


    }
    private function installModule( $moduleName ) {
        $mm = new \InstallModulesManager();
        $installed = $mm->installModule($moduleName);
        $isInstalled = ($installed == "Already installed" || $installed == "Installed");
        if(!$isInstalled){
            $this->addError(
                sprintf("Unable to install module %s", $moduleName),
                sprintf("Unable to install module %s", $moduleName),
                "DB"
            );
        }
        $result = $mm->enableModule($moduleName);

    }



}
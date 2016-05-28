<?php
/**
 * Created by PhpStorm.
 * User: drzippie
 * Date: 28/05/16
 * Time: 11:19
 */

namespace Ximdex\Setup\Step;


use Ximdex\Setup\Manager;

class System extends Base
{
    public function __construct(Manager $manager)
    {
        parent::__construct($manager);

        $this->label = "System Check";
        $this->template = "system.twig";
        $this->title = "System Requirements";
        $this->vars['title'] = $this->title;
    }

    public function checkErrors()
    {
        parent::checkErrors();

        // file_permisions
        $this->checkPHPVersion( '5.6') ;
        $this->checkRequiredPHPExtensions();
        $this->CheckPermissions() ;
        $this->checkRequiredPackages();

    }

    /**
     * Methods to check
     */
    private function checkPHPVersion( $min  )
    {
        if (phpversion() <= $min) {
            $this->addError(
                sprintf("PHP %s or greater is required", $min),
                sprintf("PHP %s or greater is required. Please upgrade the PHP version", $min),
                "PHP Version"
            );

        }
    }
    private function checkRequiredPHPExtensions(  )
    {
            $current = array_merge( get_loaded_extensions());
            $required = [
                'xsl',
                'curl',
                'gd',
                'mcrypt',
                'PDO',
                'pdo_mysql'
            ];

            foreach( $required as $item ) {

                if (!in_array( $item, $current )) {
                    $this->addError(
                        sprintf("PHP %s extension is required", $item),
                        sprintf("PHP %s extension is required", $item),
                        "PHP Ext"
                    );
                }

            }




    }
    private  function checkRequiredPackages()
    {
        $res = null ;
        @exec('openssl version', $res);
        if (empty($res) ) {
            $tool = 'OpenSSL' ;
            $this->addError(
                sprintf("Tool %s is required", $tool ),
                sprintf("%s package is needed. Please install the package on your system.", $tool  ),
                "Tools"
            );
          }
    }

    private function CheckPermissions()
    {
        $installRoot = $this->manager->getInstallRoot();
        $dirsToCheck = [
            '/conf/',
            '/data',
            '/data/cache',
            '/data/cache/pipelines',
            '/data/files',
            '/data/nodes',
            '/data/sync',
            '/data/sync/serverframes',
            '/data/tmp',
            '/data/tmp/templates_c',
            '/data/tmp/js',
            '/logs',
        ];
        foreach ($dirsToCheck as $dir) {
            $fullDir = $installRoot . $dir;
            if  (!is_dir( $fullDir )) {
                $isValid =  mkdir( $fullDir, true );
                if ( !$isValid) {
                    $this->addError(
                        sprintf("Unable to write on dir: %s", $dir),
                        sprintf("Check permission on directory %s, Read and Write are required to install Ximdex", $dir ),
                        "Permissions"
                    );
                    continue;
                }
            }
            $result = @file_put_contents( $fullDir . '.empty', "empty");
            if ( $result !== 5 ) {// lenght of string
                $this->addError(
                    sprintf("Unable to write on dir: %s", $dir),
                    sprintf("Check permission on directory %s, Read and Write are required to install Ximdex", $dir ),
                    "Permissions"
                );
                continue;
            }
            unlink( $fullDir . '.empty' ) ;


        }


    }


}
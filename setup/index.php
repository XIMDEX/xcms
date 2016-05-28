<?php

include_once "src/bootstrap.php";


$manager = new \Ximdex\Setup\Manager( __DIR__ ) ;
/**
 * Steps
 */

$manager->addStep( new \Ximdex\Setup\Step\Wellcome( $manager ));
$manager->addStep( new \Ximdex\Setup\Step\System( $manager ));
$manager->addStep( new \Ximdex\Setup\Step\Database( $manager ));
$manager->addStep( new \Ximdex\Setup\Step\CreateDB( $manager ));
$manager->addStep( new \Ximdex\Setup\Step\Modules( $manager ));
$manager->addStep( new \Ximdex\Setup\Step\Settings( $manager ));




$manager->run();



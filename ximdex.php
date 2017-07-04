#!/usr/bin/php
<?php

use Symfony\Component\Console\Application;

include_once "bootstrap/start.php";

$application = new Application();

// global Ximdex commands
$application->add(new \Ximdex\Commands\ModulesListCommand());

// Custom module commands
foreach (ModulesManager::getEnabledModules() as $module) {
    $name = $module["name"];
    $mManager->instanceModule($name)->addCommands( $application );
}

$application->run();

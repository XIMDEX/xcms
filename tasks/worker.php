#!/usr/bin/php
<?php

include_once "../bootstrap.php";


$worker = new \Ximdex\Tasks\Worker();


foreach (ModulesManager::getEnabledModules() as $module) {
    $name = $module["name"];
    $mManager->instanceModule($name)->addTasks($worker);
}

 
$worker->run(0);



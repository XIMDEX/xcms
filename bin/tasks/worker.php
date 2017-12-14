#!/usr/bin/php
<?php

require_once dirname(__DIR__, 2) . '/bootstrap.php';


$worker = new \Ximdex\Tasks\Worker();


foreach (ModulesManager::getEnabledModules() as $module) {
    $name = $module["name"];
    $mManager->instanceModule($name)->addTasks($worker);
}

 
$worker->run(0);



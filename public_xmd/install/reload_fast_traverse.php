<?php

// Run in Ximdex root folder

include_once 'bootstrap.php';

require_once APP_ROOT_PATH . '/install/managers/FastTraverseManager.class.php';

$ftManager = new FastTraverseManager();
$ftManager->buildFastTraverse();
echo "\nBuild success";

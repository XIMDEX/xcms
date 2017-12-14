<?php
require_once dirname(__DIR__, 2) . '/bootstrap.php';


ModulesManager::file('/src/SolrSearchManager.php', 'XSearch');

$sm = new SolrSearchManager();

if(count($argv) == 3){
    echo "You have to pass a parameter\n";
    die();
}

$resultset = $sm->search($argv[1]);
print_r($resultset);
/*foreach($resultset as $doc){
    print_r($doc);
}*/
//echo $resultset->getNumFound() . ' docs founded.';